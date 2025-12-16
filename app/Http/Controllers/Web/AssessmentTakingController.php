<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentGamoSelection;
use App\Models\GamoQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentTakingController extends Controller
{
    /**
     * Show assessment questionnaire - paginated view
     */
    public function take(Assessment $assessment, Request $request)
    {
        // Check if user can take this assessment
        $this->authorize('take-assessment', $assessment);
        
        // Get selected GAMO objectives for this assessment
        $selectedGamos = $assessment->gamoSelections()
            ->with('gamoObjective')
            ->get()
            ->pluck('gamo_objective_id')
            ->toArray();

        if (empty($selectedGamos)) {
            return back()->with('error', 'No GAMO objectives selected for this assessment');
        }

        // Get all questions for selected GAMO objectives, ordered by maturity level
        $questionsQuery = GamoQuestion::with('gamoObjective')
            ->whereIn('gamo_objective_id', $selectedGamos)
            ->where('is_active', true)
            ->orderBy('maturity_level')
            ->orderBy('gamo_objective_id')
            ->orderBy('question_order');

        $totalQuestions = $questionsQuery->count();
        $questions = $questionsQuery->paginate(1); // 1 question per page for better focus

        // Get answered questions for this assessment
        $answeredQuestions = AssessmentAnswer::where('assessment_id', $assessment->id)
            ->pluck('question_id')
            ->toArray();

        $answeredCount = count($answeredQuestions);
        $unansweredCount = $totalQuestions - $answeredCount;

        // Get current question details
        $currentQuestion = $questions->first();
        $currentAnswer = null;

        if ($currentQuestion) {
            $currentAnswer = AssessmentAnswer::where('assessment_id', $assessment->id)
                ->where('question_id', $currentQuestion->id)
                ->first();
        }

        return view('assessments.take', compact(
            'assessment',
            'questions',
            'currentQuestion',
            'currentAnswer',
            'totalQuestions',
            'answeredCount',
            'unansweredCount',
            'answeredQuestions',
            'selectedGamos'
        ));
    }

    /**
     * Store answer and move to next question
     */
    public function saveAnswer(Request $request, Assessment $assessment, GamoQuestion $question)
    {
        // Check if user can answer questions
        $this->authorize('take-assessment', $assessment);

        $validated = $request->validate([
            'answer_text' => 'nullable|string|max:5000',
            'answer_json' => 'nullable|json',
            'maturity_level' => 'nullable|integer|min:0|max:5',
            'notes' => 'nullable|string|max:2000',
            'evidence_file' => 'nullable|file|max:10240',
        ]);

        // Find or create answer
        $answer = AssessmentAnswer::updateOrCreate(
            [
                'assessment_id' => $assessment->id,
                'question_id' => $question->id,
            ],
            [
                'gamo_objective_id' => $question->gamo_objective_id,
                'answer_text' => $validated['answer_text'] ?? null,
                'answer_json' => $validated['answer_json'] ?? null,
                'maturity_level' => $validated['maturity_level'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'answered_by' => Auth::id(),
                'answered_at' => now(),
            ]
        );

        // Handle evidence file upload
        if ($request->hasFile('evidence_file')) {
            $file = $request->file('evidence_file');
            $path = $file->store('evidence/' . $assessment->id, 'private');
            $answer->update(['evidence_file' => $path]);
        }

        // Update assessment progress
        $this->updateAssessmentProgress($assessment);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Answer saved successfully',
                'answer' => $answer
            ]);
        }

        return redirect()
            ->route('assessments.take', $assessment)
            ->with('success', 'Answer saved. Moving to next question...');
    }

    /**
     * Save draft without validation and return to list
     */
    public function saveDraft(Request $request, Assessment $assessment)
    {
        $this->authorize('take-assessment', $assessment);

        $validated = $request->validate([
            'question_id' => 'required|exists:gamo_questions,id',
            'answer_text' => 'nullable|string|max:5000',
            'answer_json' => 'nullable|json',
            'notes' => 'nullable|string|max:2000',
        ]);

        $question = GamoQuestion::findOrFail($validated['question_id']);

        AssessmentAnswer::updateOrCreate(
            [
                'assessment_id' => $assessment->id,
                'question_id' => $question->id,
            ],
            [
                'gamo_objective_id' => $question->gamo_objective_id,
                'answer_text' => $validated['answer_text'] ?? null,
                'answer_json' => $validated['answer_json'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        $this->updateAssessmentProgress($assessment);

        return response()->json(['success' => true, 'message' => 'Draft saved']);
    }

    /**
     * Get assessment answers summary for review
     */
    public function review(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $answers = AssessmentAnswer::where('assessment_id', $assessment->id)
            ->with(['question.gamoObjective', 'answerer'])
            ->get();

        $questionsGroupedByGamo = $answers
            ->groupBy('gamo_objective_id')
            ->map(function ($groupedAnswers) {
                return $groupedAnswers->sortBy('question.maturity_level');
            });

        $statistics = [
            'total_questions' => $answers->count(),
            'answered_questions' => $answers->whereNotNull('answered_at')->count(),
            'unanswered_questions' => $answers->whereNull('answered_at')->count(),
            'questions_with_evidence' => $answers->whereNotNull('evidence_file')->count(),
        ];

        return view('assessments.review', compact(
            'assessment',
            'answers',
            'questionsGroupedByGamo',
            'statistics'
        ));
    }

    /**
     * Show bookmarked questions for an assessment
     */
    public function bookmarked(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $bookmarkedAnswers = AssessmentAnswer::where('assessment_id', $assessment->id)
            ->whereNotNull('notes')
            ->with('question.gamoObjective')
            ->get();

        return view('assessments.bookmarked', compact(
            'assessment',
            'bookmarkedAnswers'
        ));
    }

    /**
     * Update assessment progress percentage
     */
    private function updateAssessmentProgress(Assessment $assessment): void
    {
        $totalQuestions = GamoQuestion::whereIn('gamo_objective_id',
            $assessment->gamoSelections()->pluck('gamo_objective_id')->toArray()
        )->where('is_active', true)->count();

        $answeredQuestions = AssessmentAnswer::where('assessment_id', $assessment->id)
            ->whereNotNull('answered_at')
            ->count();

        $progressPercentage = $totalQuestions > 0 
            ? round(($answeredQuestions / $totalQuestions) * 100) 
            : 0;

        $assessment->update(['progress_percentage' => $progressPercentage]);
    }

    /**
     * Auto-save answer (AJAX) - every 30 seconds
     */
    public function autoSave(Request $request, Assessment $assessment, GamoQuestion $question)
    {
        $this->authorize('take-assessment', $assessment);

        $validated = $request->validate([
            'answer_text' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:2000',
        ]);

        AssessmentAnswer::updateOrCreate(
            [
                'assessment_id' => $assessment->id,
                'question_id' => $question->id,
            ],
            [
                'gamo_objective_id' => $question->gamo_objective_id,
                'answer_text' => $validated['answer_text'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json(['success' => true, 'saved_at' => now()]);
    }
}
