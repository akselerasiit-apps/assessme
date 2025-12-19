<?php

namespace App\Exports;

use App\Models\Assessment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\DB;

class AssessmentReportExport implements WithMultipleSheets
{
    protected Assessment $assessment;

    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
        $this->assessment->load([
            'company',
            'createdBy',
            'gamoObjectives',
            'gamoScores.gamoObjective',
            'answers.question'
        ]);
    }

    public function sheets(): array
    {
        return [
            new AssessmentSummarySheet($this->assessment),
            new MaturityScoresSheet($this->assessment),
            new GamoBreakdownSheet($this->assessment),
            new AnswersSheet($this->assessment),
        ];
    }
}

class AssessmentSummarySheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected Assessment $assessment;

    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }

    public function collection()
    {
        $totalQuestions = $this->assessment->answers->count();
        $answeredQuestions = $this->assessment->answers->whereNotNull('answered_at')->count();
        $completionRate = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 1) : 0;

        return collect([
            ['Assessment Code', $this->assessment->code],
            ['Title', $this->assessment->title],
            ['Company', $this->assessment->company->name ?? '-'],
            ['Status', ucfirst($this->assessment->status)],
            ['Created By', $this->assessment->createdBy->name ?? '-'],
            ['Created At', $this->assessment->created_at->format('d M Y')],
            [''],
            ['Progress Information'],
            ['Total Questions', $totalQuestions],
            ['Answered Questions', $answeredQuestions],
            ['Completion Rate', $completionRate . '%'],
            ['Overall Maturity', round($this->assessment->overall_maturity_level ?? 0, 2)],
            [''],
            ['Assessment Period'],
            ['Start Date', $this->assessment->assessment_period_start?->format('d M Y') ?? '-'],
            ['End Date', $this->assessment->assessment_period_end?->format('d M Y') ?? '-'],
        ]);
    }

    public function headings(): array
    {
        return ['Field', 'Value'];
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']], 'font' => ['color' => ['rgb' => 'FFFFFF']]],
            'A8' => ['font' => ['bold' => true]],
            'A14' => ['font' => ['bold' => true]],
        ];
    }
}

class MaturityScoresSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithMapping
{
    protected Assessment $assessment;
    protected $maturityData;

    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
        $this->loadMaturityData();
    }

    protected function loadMaturityData()
    {
        $this->maturityData = DB::table('gamo_scores')
            ->join('gamo_objectives', 'gamo_scores.gamo_objective_id', '=', 'gamo_objectives.id')
            ->where('gamo_scores.assessment_id', $this->assessment->id)
            ->select(
                'gamo_objectives.category',
                DB::raw('AVG(gamo_scores.current_maturity_level) as avg_maturity'),
                DB::raw('AVG(gamo_scores.target_maturity_level) as avg_target'),
                DB::raw('AVG(gamo_scores.target_maturity_level - gamo_scores.current_maturity_level) as avg_gap'),
                DB::raw('COUNT(*) as objective_count')
            )
            ->groupBy('gamo_objectives.category')
            ->get();
    }

    public function collection()
    {
        return $this->maturityData;
    }

    public function map($row): array
    {
        return [
            $row->category,
            round($row->avg_maturity, 2),
            round($row->avg_target, 2),
            round($row->avg_gap, 2),
            $row->objective_count,
        ];
    }

    public function headings(): array
    {
        return ['Category', 'Current Maturity', 'Target Maturity', 'Gap', 'Objectives Count'];
    }

    public function title(): string
    {
        return 'Maturity by Category';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']], 'font' => ['color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}

class GamoBreakdownSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithMapping
{
    protected Assessment $assessment;
    protected $gamoScores;

    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
        $this->loadGamoScores();
    }

    protected function loadGamoScores()
    {
        $this->gamoScores = DB::table('gamo_scores')
            ->join('gamo_objectives', 'gamo_scores.gamo_objective_id', '=', 'gamo_objectives.id')
            ->where('gamo_scores.assessment_id', $this->assessment->id)
            ->select(
                'gamo_objectives.category',
                'gamo_objectives.code',
                'gamo_objectives.name',
                'gamo_scores.current_maturity_level',
                'gamo_scores.target_maturity_level',
                DB::raw('(gamo_scores.target_maturity_level - gamo_scores.current_maturity_level) as gap'),
                'gamo_scores.percentage_complete'
            )
            ->orderBy('gamo_objectives.category')
            ->orderBy('gamo_objectives.code')
            ->get();
    }

    public function collection()
    {
        return $this->gamoScores;
    }

    public function map($row): array
    {
        return [
            $row->category,
            $row->code,
            $row->name,
            round($row->current_maturity_level, 2),
            round($row->target_maturity_level, 2),
            round($row->gap, 2),
            round($row->percentage_complete ?? 0, 1) . '%',
        ];
    }

    public function headings(): array
    {
        return ['Category', 'Code', 'Objective Name', 'Current', 'Target', 'Gap', 'Completion %'];
    }

    public function title(): string
    {
        return 'GAMO Objectives';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']], 'font' => ['color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}

class AnswersSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithMapping
{
    protected Assessment $assessment;

    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }

    public function collection()
    {
        return $this->assessment->answers()->with('question')->get();
    }

    public function map($answer): array
    {
        return [
            $answer->question->code ?? '-',
            $answer->question->question_text ?? '-',
            $answer->answer_value ?? '-',
            $answer->notes ?? '-',
            $answer->evidence_file ? 'Yes' : 'No',
            $answer->answered_at ? $answer->answered_at->format('d M Y H:i') : 'Not answered',
        ];
    }

    public function headings(): array
    {
        return ['Question Code', 'Question Text', 'Answer', 'Notes', 'Has Evidence', 'Answered At'];
    }

    public function title(): string
    {
        return 'Answers';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']], 'font' => ['color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}
