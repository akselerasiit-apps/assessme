<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\GamoObjective;
use App\Models\GamoScore;
use App\Models\Answer;
use Illuminate\Http\Request;

class ScoringController extends Controller
{
    /**
     * Get scoring summary for assessment
     */
    public function summary($assessmentId)
    {
        $assessment = Assessment::with(['gamoScores'])->findOrFail($assessmentId);
        $this->authorize('view', $assessment);
        
        $scores = $assessment->gamoScores()->with('gamoObjective')->get();
        $overallMaturity = $scores->avg('current_maturity_level');
        $assessment->update(['overall_maturity_level' => round($overallMaturity, 2)]);
        
        return response()->json([
            'assessment_id' => $assessmentId,
            'overall_current_maturity' => round($overallMaturity, 2),
            'overall_target_maturity' => $scores->avg('target_maturity_level'),
            'overall_gap' => round($scores->avg('target_maturity_level') - $overallMaturity, 2),
            'total_objectives' => $scores->count(),
            'completed_objectives' => $scores->where('status', 'completed')->count(),
            'scores' => $scores->map(fn($s) => [
                'gamo_code' => $s->gamoObjective->code,
                'gamo_name' => $s->gamoObjective->name,
                'current_level' => round($s->current_maturity_level, 2),
                'target_level' => round($s->target_maturity_level, 2),
                'capability_score' => $s->capability_score ? round($s->capability_score, 2) : null,
                'gap' => round($s->target_maturity_level - $s->current_maturity_level, 2),
                'status' => $s->status,
            ])
        ]);
    }

    /**
     * Set target maturity level for GAMO
     */
    public function setTargetMaturity(Request $request, $assessmentId, $gamoId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $gamo = GamoObjective::findOrFail($gamoId);
        
        $this->authorize('answer', $assessment);
        
        $request->validate(['target_maturity_level' => 'required|numeric|min:0|max:5']);
        
        $score = GamoScore::firstOrCreate(
            ['assessment_id' => $assessmentId, 'gamo_objective_id' => $gamoId]
        );
        
        $score->update(['target_maturity_level' => $request->input('target_maturity_level')]);
        activity()->performedOn($assessment)->causedBy(auth()->user())->log('scoring.target_set');
        
        return response()->json([
            'message' => 'Target maturity set successfully',
            'current_level' => round($score->current_maturity_level, 2),
            'target_level' => round($score->target_maturity_level, 2),
            'gap' => round($score->target_maturity_level - $score->current_maturity_level, 2)
        ]);
    }

    /**
     * Calculate capability scores for each level (0-5)
     */
    public function calculateCapabilityScores(Request $request, $assessmentId, $gamoId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $gamo = GamoObjective::findOrFail($gamoId);
        
        $this->authorize('answer', $assessment);
        
        $request->validate([
            'level_scores' => 'required|array|size:6',
            'level_scores.0' => 'numeric|min:0|max:1|required',
            'level_scores.1' => 'numeric|min:0|max:1|required',
            'level_scores.2' => 'numeric|min:0|max:1|required',
            'level_scores.3' => 'numeric|min:0|max:1|required',
            'level_scores.4' => 'numeric|min:0|max:1|required',
            'level_scores.5' => 'numeric|min:0|max:1|required',
        ]);
        
        $score = GamoScore::firstOrCreate(
            ['assessment_id' => $assessmentId, 'gamo_objective_id' => $gamoId]
        );
        
        $levelScores = $request->input('level_scores');
        $capabilityScore = array_sum($levelScores) / 6;
        $capabilityLevel = $this->determineCapabilityLevel($levelScores);
        
        $score->update([
            'current_maturity_level' => $capabilityLevel,
            'capability_score' => $capabilityScore,
            'capability_level' => $capabilityLevel,
            'status' => 'completed',
        ]);
        
        return response()->json([
            'message' => 'Capability scores calculated',
            'gamo_code' => $gamo->code,
            'level_scores' => ['L0' => $levelScores[0], 'L1' => $levelScores[1], 'L2' => $levelScores[2], 'L3' => $levelScores[3], 'L4' => $levelScores[4], 'L5' => $levelScores[5]],
            'current_level' => round($capabilityLevel, 2),
            'capability_score' => round($capabilityScore, 2),
            'target_level' => round($score->target_maturity_level, 2),
            'gap' => round($score->target_maturity_level - $capabilityLevel, 2),
            'priority' => $this->calculatePriority($score->target_maturity_level - $capabilityLevel)
        ]);
    }

    /**
     * Get capability assessment for GAMO
     */
    public function getCapabilityAssessment($assessmentId, $gamoId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $gamo = GamoObjective::findOrFail($gamoId);
        $this->authorize('view', $assessment);
        
        $score = GamoScore::where('assessment_id', $assessmentId)
            ->where('gamo_objective_id', $gamoId)
            ->firstOrFail();
        
        $answers = Answer::where('assessment_id', $assessmentId)
            ->where('gamo_objective_id', $gamoId)
            ->get();
        
        return response()->json([
            'gamo' => ['code' => $gamo->code, 'name' => $gamo->name, 'category' => $gamo->category],
            'scoring' => [
                'current' => round($score->current_maturity_level, 2),
                'target' => round($score->target_maturity_level, 2),
                'gap' => round($score->target_maturity_level - $score->current_maturity_level, 2),
                'priority' => $this->calculatePriority($score->target_maturity_level - $score->current_maturity_level),
            ],
            'stats' => [
                'total_questions' => $answers->count(),
                'answered' => $answers->whereNotNull('answered_at')->count(),
                'evidence' => $answers->whereNotNull('evidence_file')->count(),
            ]
        ]);
    }

    /**
     * Gap analysis by category
     */
    public function gapAnalysis($assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $result = [];
        foreach ($byCategory as $category => $items) {
            $gaps = $items->map(fn($s) => [
                'code' => $s->gamoObjective->code,
                'current' => round($s->current_maturity_level, 2),
                'target' => round($s->target_maturity_level, 2),
                'gap' => round($s->target_maturity_level - $s->current_maturity_level, 2),
                'priority' => $this->calculatePriority($s->target_maturity_level - $s->current_maturity_level)
            ])->sortByDesc('gap')->values();
            
            $result[$category] = [
                'count' => $items->count(),
                'avg_gap' => round($items->avg(fn($s) => $s->target_maturity_level - $s->current_maturity_level), 2),
                'items' => $gaps
            ];
        }
        
        return response()->json([
            'summary' => [
                'total' => $scores->count(),
                'avg_gap' => round($scores->avg(fn($s) => $s->target_maturity_level - $s->current_maturity_level), 2),
                'critical_items' => $scores->filter(fn($s) => ($s->target_maturity_level - $s->current_maturity_level) >= 3)->count()
            ],
            'by_category' => $result
        ]);
    }

    private function determineCapabilityLevel($levelScores)
    {
        for ($level = 5; $level >= 0; $level--) {
            if ($levelScores[$level] > 0.5) return $level;
        }
        return 0;
    }

    private function calculatePriority($gap)
    {
        if ($gap >= 3) return 'CRITICAL';
        if ($gap >= 2) return 'HIGH';
        if ($gap >= 1) return 'MEDIUM';
        return 'LOW';
    }
}
