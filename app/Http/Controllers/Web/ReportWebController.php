<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\GamoObjective;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportWebController extends Controller
{
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
}
