<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\GamoObjective;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssessmentReportExport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReportWebController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display reports dashboard
     */
    public function index(Request $request)
    {
        $query = Assessment::with(['company', 'createdBy'])
            ->whereIn('status', ['completed', 'reviewed', 'approved'])
            ->when($request->search, function($q) use ($request) {
                return $q->where(function($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->search . '%')
                          ->orWhere('code', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->company_id, function($q) use ($request) {
                return $q->where('company_id', $request->company_id);
            });

        $assessments = $query->latest()->paginate(15);

        return view('reports.index', compact('assessments'));
    }

    /**
     * Display maturity level report
     */
    public function maturity(Assessment $assessment)
    {
        $this->authorize('view', $assessment);
        
        if (!in_array($assessment->status, ['completed', 'reviewed', 'approved'])) {
            return redirect()->route('reports.index')
                ->with('error', 'Maturity report is only available for completed assessments.');
        }

        $assessment->load([
            'company',
            'createdBy',
            'gamoObjectives',
            'gamoScores.gamoObjective'
        ]);

        // Calculate maturity scores by category
        $maturityByCategory = DB::table('gamo_scores')
            ->join('gamo_objectives', 'gamo_scores.gamo_objective_id', '=', 'gamo_objectives.id')
            ->where('gamo_scores.assessment_id', $assessment->id)
            ->select(
                'gamo_objectives.category',
                DB::raw('AVG(gamo_scores.current_maturity_level) as avg_maturity'),
                DB::raw('AVG(gamo_scores.target_maturity_level) as avg_target'),
                DB::raw('COUNT(*) as objective_count')
            )
            ->groupBy('gamo_objectives.category')
            ->get();

        // Prepare radar chart data
        $categories = ['EDM', 'APO', 'BAI', 'DSS', 'MEA'];
        $currentScores = [];
        $targetScores = [];
        
        foreach ($categories as $category) {
            $categoryData = $maturityByCategory->firstWhere('category', $category);
            $currentScores[] = $categoryData ? round($categoryData->avg_maturity, 2) : 0;
            $targetScores[] = $categoryData ? round($categoryData->avg_target, 2) : 3;
        }

        // Overall statistics
        $overallMaturity = $maturityByCategory->avg('avg_maturity');
        $overallTarget = $maturityByCategory->avg('avg_target');
        $gapPercentage = $overallTarget > 0 ? round((($overallTarget - $overallMaturity) / $overallTarget) * 100, 1) : 0;

        return view('reports.maturity', compact(
            'assessment',
            'maturityByCategory',
            'categories',
            'currentScores',
            'targetScores',
            'overallMaturity',
            'overallTarget',
            'gapPercentage'
        ));
    }

    /**
     * Display gap analysis report
     */
    public function gapAnalysis(Assessment $assessment)
    {
        $this->authorize('view', $assessment);
        
        if (!in_array($assessment->status, ['completed', 'reviewed', 'approved'])) {
            return redirect()->route('reports.index')
                ->with('error', 'Gap analysis is only available for completed assessments.');
        }

        $assessment->load([
            'company',
            'createdBy',
            'gamoObjectives',
            'gamoScores.gamoObjective'
        ]);

        // Get all GAMO scores with gaps
        $gamoGaps = DB::table('gamo_scores')
            ->join('gamo_objectives', 'gamo_scores.gamo_objective_id', '=', 'gamo_objectives.id')
            ->where('gamo_scores.assessment_id', $assessment->id)
            ->select(
                'gamo_objectives.code',
                'gamo_objectives.name',
                'gamo_objectives.category',
                'gamo_scores.current_maturity_level',
                'gamo_scores.target_maturity_level',
                DB::raw('(gamo_scores.target_maturity_level - gamo_scores.current_maturity_level) as gap'),
                'gamo_scores.percentage_complete'
            )
            ->orderByDesc(DB::raw('ABS(gamo_scores.target_maturity_level - gamo_scores.current_maturity_level)'))
            ->get();

        // Priority classification
        $criticalGaps = $gamoGaps->filter(fn($g) => $g->gap >= 2)->count();
        $highGaps = $gamoGaps->filter(fn($g) => $g->gap >= 1 && $g->gap < 2)->count();
        $mediumGaps = $gamoGaps->filter(fn($g) => $g->gap > 0 && $g->gap < 1)->count();
        $onTarget = $gamoGaps->filter(fn($g) => $g->gap <= 0)->count();

        return view('reports.gap-analysis', compact(
            'assessment',
            'gamoGaps',
            'criticalGaps',
            'highGaps',
            'mediumGaps',
            'onTarget'
        ));
    }

    /**
     * Display assessment summary report
     */
    public function summary(Assessment $assessment)
    {
        $this->authorize('view', $assessment);
        
        if (!in_array($assessment->status, ['completed', 'reviewed', 'approved'])) {
            return redirect()->route('reports.index')
                ->with('error', 'Summary report is only available for completed assessments.');
        }

        $assessment->load([
            'company',
            'createdBy',
            'reviewedBy',
            'approvedBy',
            'designFactors',
            'gamoObjectives',
            'answers',
            'gamoScores.gamoObjective'
        ]);

        // Summary statistics
        $totalQuestions = $assessment->answers->count();
        $answeredQuestions = $assessment->answers->whereNotNull('answered_at')->count();
        $completionRate = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 1) : 0;

        // Evidence statistics
        $withEvidence = $assessment->answers->whereNotNull('evidence_file')->count();
        $evidenceRate = $answeredQuestions > 0 ? round(($withEvidence / $answeredQuestions) * 100, 1) : 0;

        // Maturity distribution
        $maturityDistribution = $assessment->gamoScores
            ->groupBy(function($score) {
                return floor($score->current_maturity_level);
            })
            ->map(fn($group) => $group->count());

        // Top performing objectives
        $topPerforming = $assessment->gamoScores()
            ->with('gamoObjective')
            ->orderByDesc('current_maturity_level')
            ->limit(5)
            ->get();

        // Areas needing improvement
        $needsImprovement = $assessment->gamoScores()
            ->with('gamoObjective')
            ->orderBy('current_maturity_level')
            ->limit(5)
            ->get();

        return view('reports.summary', compact(
            'assessment',
            'totalQuestions',
            'answeredQuestions',
            'completionRate',
            'withEvidence',
            'evidenceRate',
            'maturityDistribution',
            'topPerforming',
            'needsImprovement'
        ));
    }

    /**
     * Export Assessment Report to PDF
     */
    public function exportPdf(Assessment $assessment, Request $request)
    {
        $this->authorize('view', $assessment);
        
        if (!in_array($assessment->status, ['completed', 'reviewed', 'approved'])) {
            return redirect()->route('reports.index')
                ->with('error', 'PDF export is only available for completed assessments.');
        }

        $reportType = $request->get('type', 'summary'); // summary, maturity, gap-analysis, executive

        // Load all necessary relationships
        $assessment->load([
            'company',
            'createdBy',
            'reviewedBy',
            'approvedBy',
            'designFactors',
            'gamoObjectives',
            'gamoScores.gamoObjective',
            'answers'
        ]);

        // Prepare data based on report type
        $data = $this->prepareReportData($assessment, $reportType);
        $data['assessment'] = $assessment;
        $data['reportType'] = $reportType;
        $data['generatedAt'] = now()->format('d M Y H:i');

        // Select appropriate PDF template
        $template = match($reportType) {
            'maturity' => 'reports.pdf.maturity',
            'gap-analysis' => 'reports.pdf.gap-analysis',
            'executive' => 'reports.pdf.executive',
            default => 'reports.pdf.summary',
        };

        $pdf = Pdf::loadView($template, $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        $filename = sprintf(
            '%s_%s_Report_%s.pdf',
            $assessment->code,
            ucfirst(str_replace('-', '_', $reportType)),
            now()->format('Ymd')
        );

        return $pdf->download($filename);
    }

    /**
     * Export Assessment Report to Excel
     */
    public function exportExcel(Assessment $assessment)
    {
        $this->authorize('view', $assessment);
        
        if (!in_array($assessment->status, ['completed', 'reviewed', 'approved'])) {
            return redirect()->route('reports.index')
                ->with('error', 'Excel export is only available for completed assessments.');
        }

        $filename = sprintf(
            '%s_Assessment_Report_%s.xlsx',
            $assessment->code,
            now()->format('Ymd')
        );

        return Excel::download(new AssessmentReportExport($assessment), $filename);
    }

    /**
     * Preview report before export
     */
    public function preview(Assessment $assessment, Request $request)
    {
        $this->authorize('view', $assessment);
        
        if (!in_array($assessment->status, ['completed', 'reviewed', 'approved'])) {
            return redirect()->route('reports.index')
                ->with('error', 'Report preview is only available for completed assessments.');
        }

        $reportType = $request->get('type', 'summary');

        $assessment->load([
            'company',
            'createdBy',
            'reviewedBy',
            'approvedBy',
            'designFactors',
            'gamoObjectives',
            'gamoScores.gamoObjective',
            'answers'
        ]);

        $data = $this->prepareReportData($assessment, $reportType);
        $data['assessment'] = $assessment;
        $data['reportType'] = $reportType;

        return view('reports.preview', $data);
    }

    /**
     * Prepare report data based on type
     */
    private function prepareReportData(Assessment $assessment, string $type): array
    {
        $data = [];

        // Common data for all reports
        $totalQuestions = $assessment->answers->count();
        $answeredQuestions = $assessment->answers->whereNotNull('answered_at')->count();
        $completionRate = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 1) : 0;

        $data['totalQuestions'] = $totalQuestions;
        $data['answeredQuestions'] = $answeredQuestions;
        $data['completionRate'] = $completionRate;

        // Evidence statistics
        $withEvidence = $assessment->answers->whereNotNull('evidence_file')->count();
        $data['withEvidence'] = $withEvidence;
        $data['evidenceRate'] = $answeredQuestions > 0 ? round(($withEvidence / $answeredQuestions) * 100, 1) : 0;

        // Maturity by category
        $maturityByCategory = DB::table('gamo_scores')
            ->join('gamo_objectives', 'gamo_scores.gamo_objective_id', '=', 'gamo_objectives.id')
            ->where('gamo_scores.assessment_id', $assessment->id)
            ->select(
                'gamo_objectives.category',
                DB::raw('AVG(gamo_scores.current_maturity_level) as avg_maturity'),
                DB::raw('AVG(gamo_scores.target_maturity_level) as avg_target'),
                DB::raw('COUNT(*) as objective_count')
            )
            ->groupBy('gamo_objectives.category')
            ->get();

        $data['maturityByCategory'] = $maturityByCategory;
        $data['overallMaturity'] = $maturityByCategory->avg('avg_maturity') ?? 0;
        $data['overallTarget'] = $maturityByCategory->avg('avg_target') ?? 3;

        // Gap analysis data
        if ($type === 'gap-analysis' || $type === 'summary') {
            $gamoGaps = DB::table('gamo_scores')
                ->join('gamo_objectives', 'gamo_scores.gamo_objective_id', '=', 'gamo_objectives.id')
                ->where('gamo_scores.assessment_id', $assessment->id)
                ->select(
                    'gamo_objectives.code',
                    'gamo_objectives.name',
                    'gamo_objectives.category',
                    'gamo_scores.current_maturity_level',
                    'gamo_scores.target_maturity_level',
                    DB::raw('(gamo_scores.target_maturity_level - gamo_scores.current_maturity_level) as gap')
                )
                ->orderByDesc(DB::raw('ABS(gamo_scores.target_maturity_level - gamo_scores.current_maturity_level)'))
                ->get();

            $data['gamoGaps'] = $gamoGaps;
            $data['criticalGaps'] = $gamoGaps->filter(fn($g) => $g->gap >= 2)->count();
            $data['highGaps'] = $gamoGaps->filter(fn($g) => $g->gap >= 1 && $g->gap < 2)->count();
            $data['mediumGaps'] = $gamoGaps->filter(fn($g) => $g->gap > 0 && $g->gap < 1)->count();
        }

        // Top performers and areas for improvement
        if ($type === 'summary' || $type === 'executive') {
            $data['topPerforming'] = $assessment->gamoScores()
                ->with('gamoObjective')
                ->orderByDesc('current_maturity_level')
                ->limit(5)
                ->get();

            $data['needsImprovement'] = $assessment->gamoScores()
                ->with('gamoObjective')
                ->orderBy('current_maturity_level')
                ->limit(5)
                ->get();
        }

        return $data;
    }
}
