<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\GamoObjective;
use App\Models\GamoScore;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportController extends Controller
{
    use AuthorizesRequests;
    /**
     * Generate assessment summary report (PDF)
     * 
     * @param Assessment $assessment
     * @return \Illuminate\Http\Response
     */
    public function assessmentSummaryPdf(Assessment $assessment)
    {
        // Authorization check
        $this->authorize('view', $assessment);

        // Load relationships
        $assessment->load([
            'company',
            'createdBy',
            'designFactors',
            'gamoObjectives',
            'gamoScores.gamoObjective'
        ]);

        // Prepare data
        $data = [
            'assessment' => $assessment,
            'company' => $assessment->company,
            'overallMaturity' => $assessment->overall_maturity_level ?? 0,
            'completedGamos' => $assessment->gamoScores()->where('status', 'completed')->count(),
            'totalGamos' => $assessment->gamoScores()->count(),
            'generatedAt' => now()->format('d F Y H:i'),
            'generatedBy' => Auth::user()->name,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('reports.summary-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10);

        return $pdf->download("Assessment-Summary-{$assessment->code}.pdf");
    }

    /**
     * Generate maturity level report (PDF)
     * 
     * @param Assessment $assessment
     * @return \Illuminate\Http\Response
     */
    public function maturityReportPdf(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $assessment->load(['company', 'gamoScores.gamoObjective']);

        // Group GAMO scores by category
        $gamosByCategory = $assessment->gamoScores->groupBy(function ($score) {
            return $score->gamoObjective->category;
        });

        // Calculate average maturity per category
        $categoryAverages = [];
        foreach ($gamosByCategory as $category => $scores) {
            $categoryAverages[$category] = [
                'average' => $scores->avg('current_maturity_level'),
                'target' => $scores->avg('target_maturity_level'),
                'count' => $scores->count(),
                'completed' => $scores->where('status', 'completed')->count(),
            ];
        }

        $data = [
            'assessment' => $assessment,
            'company' => $assessment->company,
            'gamosByCategory' => $gamosByCategory,
            'categoryAverages' => $categoryAverages,
            'overallMaturity' => $assessment->overall_maturity_level,
            'generatedAt' => now()->format('d F Y H:i'),
            'generatedBy' => Auth::user()->name,
        ];

        $pdf = Pdf::loadView('reports.maturity-pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10);

        return $pdf->download("Maturity-Report-{$assessment->code}.pdf");
    }

    /**
     * Generate gap analysis report (PDF)
     * 
     * @param Assessment $assessment
     * @return \Illuminate\Http\Response
     */
    public function gapAnalysisPdf(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $assessment->load(['company', 'gamoScores.gamoObjective']);

        // Calculate gaps
        $gaps = $assessment->gamoScores->map(function ($score) {
            $gap = $score->target_maturity_level - $score->current_maturity_level;
            return [
                'gamo' => $score->gamoObjective,
                'current' => $score->current_maturity_level,
                'target' => $score->target_maturity_level,
                'gap' => $gap,
                'priority' => $this->calculatePriority($gap),
                'effort' => $this->estimateEffort($gap, $score->current_maturity_level),
            ];
        })->sortByDesc('gap');

        $data = [
            'assessment' => $assessment,
            'company' => $assessment->company,
            'gaps' => $gaps,
            'criticalGaps' => $gaps->where('priority', 'CRITICAL')->count(),
            'highGaps' => $gaps->where('priority', 'HIGH')->count(),
            'mediumGaps' => $gaps->where('priority', 'MEDIUM')->count(),
            'lowGaps' => $gaps->where('priority', 'LOW')->count(),
            'generatedAt' => now()->format('d F Y H:i'),
            'generatedBy' => Auth::user()->name,
        ];

        $pdf = Pdf::loadView('reports.gap-analysis-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10);

        return $pdf->download("Gap-Analysis-{$assessment->code}.pdf");
    }

    /**
     * Export assessment data to Excel
     * 
     * @param Assessment $assessment
     * @return \Illuminate\Http\Response
     */
    public function exportExcel(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $assessment->load([
            'company',
            'gamoScores.gamoObjective',
            'answers.question.gamoObjective'
        ]);

        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Assessment Overview
        $this->createOverviewSheet($spreadsheet, $assessment);
        
        // Sheet 2: GAMO Scores
        $this->createScoresSheet($spreadsheet, $assessment);
        
        // Sheet 3: Gap Analysis
        $this->createGapAnalysisSheet($spreadsheet, $assessment);
        
        // Sheet 4: Detailed Answers
        $this->createAnswersSheet($spreadsheet, $assessment);

        // Save to file
        $writer = new Xlsx($spreadsheet);
        $filename = "Assessment-Export-{$assessment->code}-" . date('Ymd-His') . ".xlsx";
        $tempFile = storage_path("app/temp/{$filename}");

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Get assessment statistics for dashboard
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboardStats(Request $request)
    {
        $user = Auth::user();
        
        // Build query based on role
        $query = Assessment::query();
        
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            // Non-admin users see only their company's assessments
            $query->where('company_id', $user->company_id);
        }

        // Apply filters
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // Calculate statistics
        $stats = [
            'total_assessments' => (clone $query)->count(),
            'draft' => (clone $query)->where('status', 'draft')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'reviewed' => (clone $query)->where('status', 'reviewed')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'average_maturity' => (clone $query)
                ->whereNotNull('overall_maturity_level')
                ->avg('overall_maturity_level'),
            'average_progress' => (clone $query)->avg('progress_percentage'),
        ];

        // Maturity distribution
        $maturityDistribution = (clone $query)
            ->whereNotNull('overall_maturity_level')
            ->selectRaw('
                COUNT(CASE WHEN overall_maturity_level < 1 THEN 1 END) as level_0,
                COUNT(CASE WHEN overall_maturity_level >= 1 AND overall_maturity_level < 2 THEN 1 END) as level_1,
                COUNT(CASE WHEN overall_maturity_level >= 2 AND overall_maturity_level < 3 THEN 1 END) as level_2,
                COUNT(CASE WHEN overall_maturity_level >= 3 AND overall_maturity_level < 4 THEN 1 END) as level_3,
                COUNT(CASE WHEN overall_maturity_level >= 4 AND overall_maturity_level < 5 THEN 1 END) as level_4,
                COUNT(CASE WHEN overall_maturity_level = 5 THEN 1 END) as level_5
            ')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'maturity_distribution' => $maturityDistribution,
            ]
        ]);
    }

    /**
     * Calculate priority based on gap
     * 
     * @param float $gap
     * @return string
     */
    private function calculatePriority($gap)
    {
        if ($gap >= 3) return 'CRITICAL';
        if ($gap >= 2) return 'HIGH';
        if ($gap >= 1) return 'MEDIUM';
        return 'LOW';
    }

    /**
     * Estimate effort based on gap and current level
     * 
     * @param float $gap
     * @param float $currentLevel
     * @return string
     */
    private function estimateEffort($gap, $currentLevel)
    {
        $baseEffort = $gap * 30; // 30 days per level
        $multiplier = (6 - $currentLevel) * 0.2; // Higher levels need more effort
        $totalDays = $baseEffort * (1 + $multiplier);

        if ($totalDays > 180) return 'Sangat Tinggi (>6 bulan)';
        if ($totalDays > 90) return 'Tinggi (3-6 bulan)';
        if ($totalDays > 30) return 'Sedang (1-3 bulan)';
        return 'Rendah (<1 bulan)';
    }

    /**
     * Create overview sheet in Excel
     */
    private function createOverviewSheet($spreadsheet, $assessment)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Overview');

        // Header
        $sheet->setCellValue('A1', 'ASSESSMENT OVERVIEW REPORT');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Assessment info
        $row = 3;
        $sheet->setCellValue("A{$row}", 'Assessment Code:');
        $sheet->setCellValue("B{$row}", $assessment->code);
        $row++;
        
        $sheet->setCellValue("A{$row}", 'Title:');
        $sheet->setCellValue("B{$row}", $assessment->title);
        $row++;
        
        $sheet->setCellValue("A{$row}", 'Company:');
        $sheet->setCellValue("B{$row}", $assessment->company->name);
        $row++;
        
        $sheet->setCellValue("A{$row}", 'Status:');
        $sheet->setCellValue("B{$row}", strtoupper($assessment->status));
        $row++;
        
        $sheet->setCellValue("A{$row}", 'Progress:');
        $sheet->setCellValue("B{$row}", $assessment->progress_percentage . '%');
        $row++;
        
        $sheet->setCellValue("A{$row}", 'Overall Maturity:');
        $sheet->setCellValue("B{$row}", number_format($assessment->overall_maturity_level ?? 0, 2));
        $row++;

        // Auto-size columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
    }

    /**
     * Create scores sheet in Excel
     */
    private function createScoresSheet($spreadsheet, $assessment)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('GAMO Scores');

        // Headers
        $headers = ['No', 'GAMO Code', 'GAMO Objective', 'Category', 'Current Level', 'Target Level', 'Gap', 'Status'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue("{$col}1", $header);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true);
            $sheet->getStyle("{$col}1")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFD9D9D9');
            $col++;
        }

        // Data
        $row = 2;
        $no = 1;
        foreach ($assessment->gamoScores as $score) {
            $gap = $score->target_maturity_level - $score->current_maturity_level;
            
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $score->gamoObjective->code);
            $sheet->setCellValue("C{$row}", $score->gamoObjective->name);
            $sheet->setCellValue("D{$row}", $score->gamoObjective->category);
            $sheet->setCellValue("E{$row}", number_format($score->current_maturity_level, 2));
            $sheet->setCellValue("F{$row}", number_format($score->target_maturity_level, 2));
            $sheet->setCellValue("G{$row}", number_format($gap, 2));
            $sheet->setCellValue("H{$row}", strtoupper($score->status));
            
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create gap analysis sheet in Excel
     */
    private function createGapAnalysisSheet($spreadsheet, $assessment)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Gap Analysis');

        // Headers
        $headers = ['No', 'GAMO Code', 'GAMO Objective', 'Current', 'Target', 'Gap', 'Priority', 'Effort Estimate'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue("{$col}1", $header);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true);
            $sheet->getStyle("{$col}1")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFD9D9D9');
            $col++;
        }

        // Data
        $row = 2;
        $no = 1;
        $scores = $assessment->gamoScores->sortByDesc(function ($score) {
            return $score->target_maturity_level - $score->current_maturity_level;
        });

        foreach ($scores as $score) {
            $gap = $score->target_maturity_level - $score->current_maturity_level;
            $priority = $this->calculatePriority($gap);
            $effort = $this->estimateEffort($gap, $score->current_maturity_level);
            
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $score->gamoObjective->code);
            $sheet->setCellValue("C{$row}", $score->gamoObjective->name);
            $sheet->setCellValue("D{$row}", number_format($score->current_maturity_level, 2));
            $sheet->setCellValue("E{$row}", number_format($score->target_maturity_level, 2));
            $sheet->setCellValue("F{$row}", number_format($gap, 2));
            $sheet->setCellValue("G{$row}", $priority);
            $sheet->setCellValue("H{$row}", $effort);
            
            // Color code priority
            if ($priority === 'CRITICAL') {
                $sheet->getStyle("G{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFF0000');
                $sheet->getStyle("G{$row}")->getFont()->getColor()->setARGB('FFFFFFFF');
            } elseif ($priority === 'HIGH') {
                $sheet->getStyle("G{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFFA500');
            }
            
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create detailed answers sheet in Excel
     */
    private function createAnswersSheet($spreadsheet, $assessment)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Detailed Answers');

        // Headers
        $headers = ['No', 'GAMO Code', 'Question Code', 'Question', 'Answer', 'Maturity Level', 'Evidence'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue("{$col}1", $header);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true);
            $sheet->getStyle("{$col}1")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFD9D9D9');
            $col++;
        }

        // Data
        $row = 2;
        $no = 1;
        foreach ($assessment->answers as $answer) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $answer->question->gamoObjective->code);
            $sheet->setCellValue("C{$row}", $answer->question->code);
            $sheet->setCellValue("D{$row}", $answer->question->question_text);
            $sheet->setCellValue("E{$row}", $answer->answer_text ?? '-');
            $sheet->setCellValue("F{$row}", $answer->maturity_level);
            $sheet->setCellValue("G{$row}", $answer->evidence_file ? 'Yes' : 'No');
            
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Wrap text for question column
        $sheet->getStyle('D2:D' . ($row - 1))->getAlignment()->setWrapText(true);
        $sheet->getStyle('E2:E' . ($row - 1))->getAlignment()->setWrapText(true);
    }
}
