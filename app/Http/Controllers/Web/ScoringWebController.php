<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\GamoScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScoringWebController extends Controller
{
    /**
     * Show scoring overview for an assessment
     */
    public function index(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        // Get all GAMO scores with objectives
        $scores = GamoScore::where('assessment_id', $assessment->id)
            ->with('gamoObjective')
            ->get();

        // Group by category
        $scoresByCategory = $scores->groupBy(function($score) {
            return $score->gamoObjective->category;
        });

        // Calculate overall statistics
        $stats = [
            'total_objectives' => $scores->count(),
            'avg_maturity' => $scores->avg('current_maturity_level') ?? 0,
            'min_maturity' => $scores->min('current_maturity_level') ?? 0,
            'max_maturity' => $scores->max('current_maturity_level') ?? 0,
            'objectives_on_target' => $scores->filter(fn($s) => $s->isTargetMet())->count(),
            'objectives_below_target' => $scores->filter(fn($s) => !$s->isTargetMet())->count(),
        ];

        return view('scoring.index', compact('assessment', 'scores', 'scoresByCategory', 'stats'));
    }

    /**
     * Show detailed scoring for a specific GAMO objective
     */
    public function show(Assessment $assessment, GamoScore $score)
    {
        $this->authorize('view', $assessment);

        // Verify score belongs to assessment
        if ($score->assessment_id !== $assessment->id) {
            abort(403, 'Unauthorized');
        }

        $score->load(['gamoObjective', 'assessment']);

        // Get all answers for this GAMO
        $answers = $assessment->answers()
            ->where('gamo_objective_id', $score->gamo_objective_id)
            ->with(['question', 'answeredBy'])
            ->get();

        // Calculate answer statistics
        $answerStats = [
            'total_questions' => $answers->count(),
            'avg_maturity' => $answers->avg('maturity_level') ?? 0,
            'with_evidence' => $answers->whereNotNull('evidence_file')->count(),
            'avg_capability' => $answers->avg('capability_score') ?? 0,
        ];

        return view('scoring.show', compact('assessment', 'score', 'answers', 'answerStats'));
    }

    /**
     * Calculate or recalculate scores for an assessment
     */
    public function calculate(Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        DB::beginTransaction();
        try {
            // Get all GAMO objectives for this assessment
            $gamoObjectives = $assessment->gamoObjectives;

            foreach ($gamoObjectives as $gamo) {
                // Get all answers for this GAMO
                $answers = $assessment->answers()
                    ->where('gamo_objective_id', $gamo->id)
                    ->get();

                if ($answers->isEmpty()) {
                    continue;
                }

                // Calculate average maturity level
                $avgMaturity = $answers->avg('maturity_level') ?? 0;
                
                // Calculate average capability score
                $avgCapability = $answers->avg('capability_score') ?? 0;
                
                // Calculate completion percentage
                $totalQuestions = $gamo->questions()->where('is_active', true)->count();
                $answeredQuestions = $answers->count();
                $completionPercentage = $totalQuestions > 0 
                    ? round(($answeredQuestions / $totalQuestions) * 100) 
                    : 0;

                // Determine status
                $status = 'not_started';
                if ($completionPercentage >= 100) {
                    $status = 'completed';
                } elseif ($completionPercentage > 0) {
                    $status = 'in_progress';
                }

                // Create or update GAMO score
                GamoScore::updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'gamo_objective_id' => $gamo->id,
                    ],
                    [
                        'current_maturity_level' => round($avgMaturity, 2),
                        'capability_score' => round($avgCapability, 2),
                        'capability_level' => round($avgMaturity, 2),
                        'percentage_complete' => $completionPercentage,
                        'status' => $status,
                    ]
                );
            }

            // Update overall assessment maturity
            $overallMaturity = GamoScore::where('assessment_id', $assessment->id)
                ->avg('current_maturity_level');
            
            $assessment->update([
                'overall_maturity_level' => round($overallMaturity ?? 0, 2),
            ]);

            // Log activity
            activity()
                ->performedOn($assessment)
                ->causedBy(auth()->user())
                ->log('Calculated scores for assessment');

            DB::commit();

            return redirect()
                ->route('scoring.index', $assessment)
                ->with('success', 'Scores calculated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to calculate scores: ' . $e->getMessage());
        }
    }
}
