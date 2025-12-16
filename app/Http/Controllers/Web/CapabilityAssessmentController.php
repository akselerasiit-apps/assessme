<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\GamoObjective;
use App\Models\GamoCapabilityDefinition;
use App\Models\GamoQuestion;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAnswerCapabilityScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapabilityAssessmentController extends Controller
{
    /**
     * Display GAMO selection page
     */
    public function index(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $gamoObjectives = GamoObjective::where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('capability.index', compact('assessment', 'gamoObjectives'));
    }

    /**
     * Display capability assessment interface for selected GAMO
     */
    public function assessment(Assessment $assessment, GamoObjective $gamo)
    {
        $this->authorize('view', $assessment);

        // Get capability definitions (levels 0-5)
        $capabilityLevels = GamoCapabilityDefinition::where('gamo_objective_id', $gamo->id)
            ->orderBy('level')
            ->get();

        // Get questions for this GAMO grouped by level
        $questions = GamoQuestion::where('gamo_objective_id', $gamo->id)
            ->where('is_active', true)
            ->with(['answers' => function($query) use ($assessment) {
                $query->where('assessment_id', $assessment->id)
                    ->with('capabilityScores');
            }])
            ->orderBy('maturity_level')
            ->orderBy('order')
            ->get()
            ->groupBy('maturity_level');

        // Calculate progress per level
        $levelProgress = [];
        for ($level = 0; $level <= 5; $level++) {
            $levelQuestions = $questions->get($level, collect());
            $answeredCount = $levelQuestions->filter(function($q) {
                return $q->answers->isNotEmpty();
            })->count();
            
            $totalQuestions = $levelQuestions->count();
            $levelProgress[$level] = [
                'total' => $totalQuestions,
                'answered' => $answeredCount,
                'percentage' => $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100) : 0,
            ];
        }

        return view('capability.assessment', compact(
            'assessment', 
            'gamo', 
            'capabilityLevels', 
            'questions',
            'levelProgress'
        ));
    }

    /**
     * Update capability score for an answer
     */
    public function updateCapabilityScore(Request $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        $validated = $request->validate([
            'answer_id' => 'required|exists:assessment_answers,id',
            'level' => 'required|integer|between:0,5',
            'achievement_status' => 'required|in:NOT_ACHIEVED,PARTIALLY_ACHIEVED,LARGELY_ACHIEVED,FULLY_ACHIEVED',
            'assessment_notes' => 'nullable|string',
        ]);

        $answer = AssessmentAnswer::findOrFail($validated['answer_id']);
        
        // Check answer belongs to this assessment
        if ($answer->assessment_id !== $assessment->id) {
            return response()->json(['error' => 'Invalid answer'], 403);
        }

        // Calculate compliance percentage based on achievement status
        $compliancePercentage = match($validated['achievement_status']) {
            'NOT_ACHIEVED' => 0,
            'PARTIALLY_ACHIEVED' => 50,
            'LARGELY_ACHIEVED' => 75,
            'FULLY_ACHIEVED' => 100,
        };

        // Update or create capability score
        $capabilityScore = AssessmentAnswerCapabilityScore::updateOrCreate(
            [
                'assessment_answer_id' => $answer->id,
                'level' => $validated['level'],
            ],
            [
                'achievement_status' => $validated['achievement_status'],
                'compliance_percentage' => $compliancePercentage,
                'compliance_score' => ($compliancePercentage / 100) * ($answer->question->weight ?? 1),
                'assessment_notes' => $validated['assessment_notes'],
                'evidence_provided' => $answer->evidence_provided ?? false,
                'evidence_count' => $answer->evidence_count ?? 0,
            ]
        );

        activity()
            ->performedOn($assessment)
            ->withProperties([
                'question_id' => $answer->question_id,
                'level' => $validated['level'],
                'achievement_status' => $validated['achievement_status'],
            ])
            ->log('Capability score updated');

        return response()->json([
            'success' => true,
            'capability_score' => $capabilityScore,
            'message' => 'Capability score updated successfully',
        ]);
    }

    /**
     * Get level summary for a GAMO
     */
    public function levelSummary(Assessment $assessment, GamoObjective $gamo, $level)
    {
        $this->authorize('view', $assessment);

        // Get questions for this level
        $questions = GamoQuestion::where('gamo_objective_id', $gamo->id)
            ->where('maturity_level', $level)
            ->where('is_active', true)
            ->with(['answers' => function($query) use ($assessment) {
                $query->where('assessment_id', $assessment->id)
                    ->with('capabilityScores');
            }])
            ->get();

        $totalQuestions = $questions->count();
        $answeredQuestions = $questions->filter(function($q) {
            return $q->answers->isNotEmpty();
        })->count();

        $fullyAchieved = 0;
        $largelyAchieved = 0;
        $partiallyAchieved = 0;
        $notAchieved = 0;

        foreach ($questions as $question) {
            if ($question->answers->isEmpty()) continue;
            
            $answer = $question->answers->first();
            $capabilityScore = $answer->capabilityScores->where('level', $level)->first();
            
            if ($capabilityScore) {
                switch ($capabilityScore->achievement_status) {
                    case 'FULLY_ACHIEVED':
                        $fullyAchieved++;
                        break;
                    case 'LARGELY_ACHIEVED':
                        $largelyAchieved++;
                        break;
                    case 'PARTIALLY_ACHIEVED':
                        $partiallyAchieved++;
                        break;
                    case 'NOT_ACHIEVED':
                        $notAchieved++;
                        break;
                }
            }
        }

        $overallAchievement = $totalQuestions > 0 
            ? round((($fullyAchieved * 100 + $largelyAchieved * 75 + $partiallyAchieved * 50) / $totalQuestions))
            : 0;

        return response()->json([
            'level' => $level,
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'fully_achieved' => $fullyAchieved,
            'largely_achieved' => $largelyAchieved,
            'partially_achieved' => $partiallyAchieved,
            'not_achieved' => $notAchieved,
            'overall_achievement' => $overallAchievement,
        ]);
    }

    /**
     * Get evidence details for an answer (API endpoint)
     */
    public function getEvidenceDetails(Assessment $assessment, AssessmentAnswer $answer)
    {
        // Check answer belongs to this assessment
        if ($answer->assessment_id !== $assessment->id) {
            return response()->json(['error' => 'Invalid answer'], 403);
        }

        $answer->load([
            'question',
            'answeredBy:id,name,email',
            'capabilityScores' => function($query) {
                $query->orderBy('level');
            }
        ]);

        return response()->json([
            'answer' => [
                'id' => $answer->id,
                'answer_text' => $answer->answer_text,
                'answered_at' => $answer->answered_at,
                'evidence_provided' => $answer->evidence_provided,
                'evidence_file_path' => $answer->evidence_file_path,
                'evidence_url' => $answer->evidence_url,
                'evidence_description' => $answer->evidence_description,
                'evidence_count' => $answer->evidence_count,
            ],
            'question' => [
                'id' => $answer->question->id,
                'code' => $answer->question->code,
                'question_text_en' => $answer->question->question_text_en,
                'question_text_id' => $answer->question->question_text_id,
                'maturity_level' => $answer->question->maturity_level,
            ],
            'answered_by' => $answer->answeredBy ? [
                'id' => $answer->answeredBy->id,
                'name' => $answer->answeredBy->name,
                'email' => $answer->answeredBy->email,
            ] : null,
            'capability_scores' => $answer->capabilityScores->map(function($score) {
                return [
                    'level' => $score->level,
                    'achievement_status' => $score->achievement_status,
                    'compliance_percentage' => $score->compliance_percentage,
                    'compliance_score' => $score->compliance_score,
                    'assessment_notes' => $score->assessment_notes,
                    'evidence_provided' => $score->evidence_provided,
                    'evidence_count' => $score->evidence_count,
                ];
            }),
        ]);
    }
}

