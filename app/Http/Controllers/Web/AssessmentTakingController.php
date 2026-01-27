<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAuditLog;
use App\Models\AssessmentEvidence;
use App\Models\AssessmentGamoSelection;
use App\Models\AssessmentNote;
use App\Models\AssessmentOfi;
use App\Models\GamoObjective;
use App\Models\GamoQuestion;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AssessmentTakingController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Show new answer assessment interface
     */
    public function answerNew(Assessment $assessment)
    {
        // Check if user can take this assessment
        $this->authorize('take-assessment', $assessment);
        
        // Get selected GAMO objectives for this assessment with pivot data
        $gamoObjectives = $assessment->gamoSelections()
            ->where('is_selected', true)
            ->with('gamoObjective')
            ->get()
            ->map(function($selection) {
                $gamo = $selection->gamoObjective;
                if ($gamo) {
                    // Attach pivot data to gamoObjective
                    $gamo->pivot = (object)[
                        'target_maturity_level' => $selection->target_maturity_level
                    ];
                }
                return $gamo;
            })
            ->filter();

        if ($gamoObjectives->isEmpty()) {
            return back()->with('error', 'No GAMO objectives selected for this assessment');
        }

        return view('assessments.answer-new', compact('assessment', 'gamoObjectives'));
    }
    
    /**
     * Show assessment questionnaire - paginated view
     * Updated to support COBIT 2019 Activities with bilingual format
     */
    public function take(Assessment $assessment, Request $request)
    {
        // Check if user can take this assessment
        $this->authorize('take-assessment', $assessment);
        
        // Get language preference from session or default to English
        $language = session('assessment_language', 'en');
        
        // Get selected GAMO objectives for this assessment
        $selectedGamoIds = $assessment->gamoSelections()
            ->pluck('gamo_objective_id')
            ->toArray();

        if (empty($selectedGamoIds)) {
            return back()->with('error', 'No GAMO objectives selected for this assessment');
        }

        // Get all activities (questions) for selected GAMO objectives
        // Ordered by GAMO code, then maturity level for progressive assessment
        $questionsQuery = GamoQuestion::with(['gamoObjective:id,code,name,category'])
            ->whereIn('gamo_objective_id', $selectedGamoIds)
            ->where('is_active', true)
            ->join('gamo_objectives', 'gamo_questions.gamo_objective_id', '=', 'gamo_objectives.id')
            ->select('gamo_questions.*')
            ->orderBy('gamo_objectives.code')
            ->orderBy('gamo_questions.maturity_level')
            ->orderBy('gamo_questions.question_order');

        $totalQuestions = $questionsQuery->count();
        
        // Paginate activities - 1 per page for focused assessment
        $questions = GamoQuestion::with(['gamoObjective:id,code,name,category'])
            ->whereIn('gamo_objective_id', $selectedGamoIds)
            ->where('is_active', true)
            ->join('gamo_objectives', 'gamo_questions.gamo_objective_id', '=', 'gamo_objectives.id')
            ->select('gamo_questions.*')
            ->orderBy('gamo_objectives.code')
            ->orderBy('gamo_questions.maturity_level')
            ->orderBy('gamo_questions.question_order')
            ->paginate(1);

        // Get answered questions for this assessment
        $answeredQuestions = AssessmentAnswer::where('assessment_id', $assessment->id)
            ->pluck('question_id')
            ->toArray();

        $answeredCount = count($answeredQuestions);
        $unansweredCount = $totalQuestions - $answeredCount;

        // Get current question/activity details
        $currentQuestion = $questions->first();
        $currentAnswer = null;

        if ($currentQuestion) {
            // Parse bilingual text (format: "English text | Indonesian text")
            $questionParts = explode(' | ', $currentQuestion->question_text);
            $currentQuestion->question_en = trim($questionParts[0] ?? $currentQuestion->question_text);
            $currentQuestion->question_id = trim($questionParts[1] ?? $questionParts[0]);
            
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
            'selectedGamoIds',
            'language'
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
        // GAMO-based progress calculation (matching show.blade.php)
        $totalGamoCount = $assessment->gamoObjectives()->count();
        
        // Count unique GAMO IDs that have been answered
        $answeredGamoCount = AssessmentAnswer::where('assessment_id', $assessment->id)
            ->whereNotNull('answered_at')
            ->distinct()
            ->count('gamo_objective_id');

        $progressPercentage = $totalGamoCount > 0 
            ? round(($answeredGamoCount / $totalGamoCount) * 100) 
            : 0;

        // Auto-update status based on progress (case-insensitive check)
        $newStatus = $assessment->status;
        if ($answeredGamoCount > 0 && strtolower($assessment->status) === 'draft') {
            $newStatus = 'in_progress';
        }

        $assessment->update([
            'progress_percentage' => $progressPercentage,
            'status' => $newStatus
        ]);
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

    /**
     * Get activities grouped by level for a GAMO
     */
    public function getActivitiesByLevel(Assessment $assessment, GamoObjective $gamo)
    {
        $this->authorize('view', $assessment);

        $activities = GamoQuestion::where('gamo_objective_id', $gamo->id)
            ->where('is_active', true)
            ->where('maturity_level', '>=', 2) // COBIT 2019: Level 2-5 only
            ->orderBy('maturity_level')
            ->orderBy('question_order')
            ->get()
            ->map(function($question) use ($assessment) {
                // Get answer for this activity
                $answer = AssessmentAnswer::where('assessment_id', $assessment->id)
                    ->where('question_id', $question->id)
                    ->first();

                // Count evidence for this activity
                $evidenceCount = AssessmentEvidence::where('assessment_id', $assessment->id)
                    ->where('activity_id', $question->id)
                    ->count();

                // Split English and Indonesian text (separated by |)
                $texts = explode(' | ', $question->question_text);
                $textEn = $texts[0] ?? $question->question_text;
                $textId = $texts[1] ?? $textEn;

                return [
                    'id' => $question->id,
                    'code' => $question->code,
                    'name' => $textEn,
                    'translated_text' => $textId,
                    'level' => $question->maturity_level,
                    'weight' => 1, // Default weight, bisa ditambahkan kolom jika perlu
                    'guidance' => $question->guidance,
                    'document_requirements' => $question->document_requirements,
                    'evidence_count' => $evidenceCount,
                    'answer' => $answer ? [
                        'capability_rating' => $answer->capability_rating,
                        'capability_score' => $answer->capability_score,
                        'notes' => $answer->notes,
                    ] : null,
                ];
            })
            ->groupBy('level');

        return response()->json([
            'success' => true,
            'activities' => $activities,
        ]);
    }

    /**
     * Get activity detail for modal
     */
    public function getActivityDetail(Assessment $assessment, GamoQuestion $activity)
    {
        $this->authorize('view', $assessment);

        // Get answer for this activity
        $answer = AssessmentAnswer::where('assessment_id', $assessment->id)
            ->where('question_id', $activity->id)
            ->first();

        // Count evidence for this activity
        $evidenceCount = AssessmentEvidence::where('assessment_id', $assessment->id)
            ->where('activity_id', $activity->id)
            ->count();

        // Split English and Indonesian text (separated by |)
        $texts = explode(' | ', $activity->question_text);
        $textEn = $texts[0] ?? $activity->question_text;
        $textId = $texts[1] ?? $textEn;

        return response()->json([
            'id' => $activity->id,
            'code' => $activity->code,
            'name' => $textEn,
            'translated_text' => $textId,
            'level' => $activity->maturity_level,
            'weight' => 1, // Default weight
            'evidence_count' => $evidenceCount,
            'answer' => $answer ? [
                'capability_rating' => $answer->capability_rating,
                'capability_score' => $answer->capability_score,
                'notes' => $answer->notes,
            ] : null,
        ]);
    }

    /**
     * Save activity answer with capability rating
     */
    public function saveActivityAnswer(Request $request, Assessment $assessment, GamoQuestion $activity)
    {
        $this->authorize('take-assessment', $assessment);

        $validated = $request->validate([
            'capability_rating' => 'required|in:N/A,N,P,L,F',
            'notes' => 'nullable|string',
        ]);

        // Map rating to numeric score
        $ratingScores = [
            'N/A' => 0,
            'N' => 0.15,
            'P' => 0.33,
            'L' => 0.67,
            'F' => 1.0,
        ];

        $answer = AssessmentAnswer::updateOrCreate(
            [
                'assessment_id' => $assessment->id,
                'question_id' => $activity->id,
            ],
            [
                'gamo_objective_id' => $activity->gamo_objective_id,
                'level' => $activity->maturity_level,
                'capability_rating' => $validated['capability_rating'],
                'capability_score' => $ratingScores[$validated['capability_rating']],
                'notes' => $validated['notes'] ?? null,
                'answered_by' => auth()->id(),
                'answered_at' => now(),
            ]
        );

        // Log the change
        AssessmentAuditLog::logChange(
            $assessment->id,
            $activity->gamo_objective_id,
            $activity->maturity_level,
            'update_rating',
            "Mengubah penilaian aktivitas menjadi {$validated['capability_rating']}",
            null,
            ['rating' => $validated['capability_rating'], 'score' => $ratingScores[$validated['capability_rating']]]
        );

        // Update assessment progress
        $this->updateAssessmentProgress($assessment);

        // Auto-calculate GAMO score for this objective
        $this->updateGamoScore($assessment, $activity->gamo_objective_id);

        return response()->json([
            'success' => true,
            'answer' => $answer,
            'message' => 'Assessment saved successfully',
        ]);
    }

    /**
     * Get history log for GAMO
     */
    public function getHistoryLog(Assessment $assessment, GamoObjective $gamo, Request $request)
    {
        $this->authorize('view', $assessment);

        $action = $request->input('action');
        $date = $request->input('date');

        $query = AssessmentAuditLog::where('assessment_id', $assessment->id)
            ->where('gamo_objective_id', $gamo->id)
            ->with('user');

        if ($action) {
            $query->where('action', $action);
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        // Format logs for frontend
        $history = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'action' => $log->action,
                'action_description' => $log->description,
                'user_name' => $log->user->name ?? 'System',
                'created_at' => $log->created_at->toISOString(),
                'changes' => json_encode([
                    'old_value' => is_array($log->old_value) ? json_encode($log->old_value) : $log->old_value,
                    'new_value' => is_array($log->new_value) ? json_encode($log->new_value) : $log->new_value,
                ]),
            ];
        });

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }

    /**
     * Get average score for GAMO
     */
    public function getAverageScore(Assessment $assessment, GamoObjective $gamo)
    {
        $this->authorize('view', $assessment);

        // Get compliance per level and rating distribution
        $levels = [];
        $ratingDistribution = [
            'F' => 0,
            'L' => 0,
            'P' => 0,
            'N' => 0,
            'N/A' => 0
        ];
        
        for ($level = 2; $level <= 5; $level++) {
            $activities = GamoQuestion::where('gamo_objective_id', $gamo->id)
                ->where('maturity_level', $level)
                ->where('is_active', true)
                ->get();

            $totalActivities = $activities->count();
            
            if ($totalActivities === 0) {
                $levels[$level] = [
                    'total_activities' => 0,
                    'assessed' => 0,
                    'compliance' => 0
                ];
                continue;
            }

            $answers = AssessmentAnswer::where('assessment_id', $assessment->id)
                ->whereIn('question_id', $activities->pluck('id'))
                ->get();

            $assessed = $answers->whereNotNull('capability_score')->count();

            // Calculate compliance for this level
            $totalWeight = $activities->sum('weight');
            $weightedScore = 0;

            foreach ($answers as $answer) {
                $activity = $activities->firstWhere('id', $answer->question_id);
                if ($activity && $answer->capability_score !== null) {
                    $weightedScore += $activity->weight * $answer->capability_score;
                    
                    // Count rating distribution
                    $rating = $answer->rating ?? 'N/A';
                    if (isset($ratingDistribution[$rating])) {
                        $ratingDistribution[$rating]++;
                    }
                }
            }

            $compliance = $totalWeight > 0 ? $weightedScore / $totalWeight : 0;
            
            $levels[$level] = [
                'total_activities' => $totalActivities,
                'assessed' => $assessed,
                'compliance' => round($compliance, 4)
            ];
        }

        return response()->json([
            'success' => true,
            'levels' => $levels,
            'rating_distribution' => $ratingDistribution
        ]);
    }

    /**
     * Get notes list for GAMO and level
     */
    public function getNotesList(Assessment $assessment, GamoObjective $gamo, Request $request)
    {
        $this->authorize('view', $assessment);

        $level = $request->input('level');
        $search = $request->input('search');

        // Build query for activities
        $query = GamoQuestion::where('gamo_objective_id', $gamo->id)
            ->where('is_active', true)
            ->where('maturity_level', '>=', 2); // COBIT 2019: Level 2-5 only

        // Apply level filter if provided
        if ($level) {
            $query->where('maturity_level', $level);
        }

        // Get activities with their answers that have notes
        $activities = $query->with(['answers' => function($q) use ($assessment) {
            $q->where('assessment_id', $assessment->id)
              ->whereNotNull('notes')
              ->where('notes', '!=', '');
        }])->get();

        // Transform to notes format
        $notes = [];
        $stats = [
            'total' => 0,
            'with_rating' => 0,
            'without_rating' => 0
        ];

        foreach ($activities as $activity) {
            foreach ($activity->answers as $answer) {
                // Apply search filter
                if ($search && stripos($answer->notes, $search) === false) {
                    continue;
                }

                // Get user name
                $userName = 'Unknown';
                if ($answer->answered_by) {
                    $user = \App\Models\User::find($answer->answered_by);
                    $userName = $user ? $user->name : 'Unknown';
                }

                $texts = explode(' | ', $activity->question_text);
                $notes[] = [
                    'activity_id' => $activity->id,
                    'activity_code' => $activity->code,
                    'activity_name' => $texts[0] ?? $activity->question_text,
                    'level' => $activity->maturity_level,
                    'notes' => $answer->notes,
                    'rating' => $answer->capability_rating,
                    'user_name' => $userName,
                    'created_at' => $answer->created_at,
                    'updated_at' => $answer->updated_at,
                ];

                $stats['total']++;
                if ($answer->capability_rating && $answer->capability_rating !== 'N/A') {
                    $stats['with_rating']++;
                } else {
                    $stats['without_rating']++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'notes' => $notes,
            'statistics' => $stats
        ]);
    }

    /**
     * Get evidence list for activity
     */
    public function getEvidenceList(Assessment $assessment, GamoQuestion $activity)
    {
        $this->authorize('view', $assessment);

        $evidence = AssessmentEvidence::where('assessment_id', $assessment->id)
            ->where('activity_id', $activity->id)
            ->with(['uploader:id,name', 'activity:id,code,question_text'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($ev) {
                return [
                    'id' => $ev->id,
                    'evidence_name' => $ev->evidence_name,
                    'evidence_description' => $ev->evidence_description,
                    'file_name' => $ev->file_name,
                    'file_path' => $ev->file_path,
                    'url' => $ev->url,
                    'activity_code' => $ev->activity->code ?? '-',
                    'uploaded_by' => $ev->uploader->name ?? '-',
                    'uploaded_at' => $ev->created_at->format('d M Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'evidence' => $evidence,
        ]);
    }

    /**
     * Download evidence file
     */
    public function downloadEvidence(Assessment $assessment, $evidenceId)
    {
        $this->authorize('view', $assessment);

        $evidence = AssessmentEvidence::where('assessment_id', $assessment->id)
            ->findOrFail($evidenceId);

        if (!$evidence->file_path || !Storage::disk('private')->exists($evidence->file_path)) {
            abort(404, 'File not found');
        }

        $fileName = basename($evidence->file_path);
        
        return Storage::disk('private')->download($evidence->file_path, $fileName);
    }

    /**
     * Upload evidence for activity
     */
    public function uploadEvidence(Request $request, Assessment $assessment, GamoQuestion $activity)
    {
        // Check if user can upload evidence (Asesi can, Viewer cannot)
        $this->authorize('uploadEvidence', $assessment);

        $validated = $request->validate([
            'evidence_name' => 'required|string|max:255',
            'evidence_description' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB
            'url' => 'nullable|url',
        ]);

        // At least one of file or URL must be provided
        if (!$request->hasFile('file') && empty($validated['url'])) {
            return response()->json([
                'success' => false,
                'message' => 'Either file or URL must be provided',
            ], 422);
        }

        $filePath = null;
        $fileType = null;
        $fileSize = null;
        $originalName = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $filePath = $file->store("assessments/{$assessment->id}/evidence", 'private');
            $fileType = $file->getClientOriginalExtension();
            $fileSize = $file->getSize();
        }

        $evidence = AssessmentEvidence::create([
            'assessment_id' => $assessment->id,
            'activity_id' => $activity->id,
            'evidence_name' => $validated['evidence_name'],
            'evidence_description' => $validated['evidence_description'] ?? null,
            'file_path' => $filePath,
            'url' => $validated['url'] ?? null,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'uploaded_by' => auth()->id(),
        ]);

        // Log the upload
        AssessmentAuditLog::logChange(
            $assessment->id,
            $activity->gamo_objective_id,
            $activity->maturity_level,
            'upload_evidence',
            "Mengunggah evidence: {$validated['evidence_name']}",
            null,
            ['evidence_id' => $evidence->id]
        );

        return response()->json([
            'success' => true,
            'evidence' => $evidence,
            'message' => 'Evidence uploaded successfully',
        ]);
    }

    /**
     * Toggle language preference for assessment taking
     */
    public function toggleLanguage(Request $request)
    {
        $language = $request->input('language', 'en');
        
        // Validate language
        if (!in_array($language, ['en', 'id'])) {
            $language = 'en';
        }
        
        session(['assessment_language' => $language]);
        
        return response()->json([
            'success' => true,
            'language' => $language,
            'message' => $language === 'en' ? 'Language changed to English' : 'Bahasa diubah ke Indonesia'
        ]);
    }

    /**
     * Get summary data for GAMO
     */
    public function getSummary(Assessment $assessment, GamoObjective $gamo)
    {
        $this->authorize('view', $assessment);

        $summary = [
            'levels' => [],
            'totals' => [
                'activities' => 0,
                'assessed' => 0,
                'not_assessed' => 0,
                'na' => 0, 'n' => 0, 'p' => 0, 'l' => 0, 'f' => 0,
                'compliance' => 0
            ]
        ];

        for ($level = 2; $level <= 5; $level++) {
            $activities = GamoQuestion::where('gamo_objective_id', $gamo->id)
                ->where('maturity_level', $level)
                ->where('is_active', true)
                ->get();

            $total = $activities->count();
            $answers = AssessmentAnswer::where('assessment_id', $assessment->id)
                ->whereIn('question_id', $activities->pluck('id'))
                ->get();

            $assessed = $answers->whereNotNull('capability_rating')->count();
            $notAssessed = $total - $assessed;

            $ratingCounts = [
                'na' => $answers->where('capability_rating', 'N/A')->count(),
                'n' => $answers->where('capability_rating', 'N')->count(),
                'p' => $answers->where('capability_rating', 'P')->count(),
                'l' => $answers->where('capability_rating', 'L')->count(),
                'f' => $answers->where('capability_rating', 'F')->count(),
            ];

            // Calculate compliance for this level
            $totalWeight = $total; // Simple: 1 weight per activity
            $weightedScore = $answers->sum('capability_score');
            $compliance = $totalWeight > 0 ? ($weightedScore / $totalWeight) * 100 : 0;

            $levelData = [
                'level' => $level,
                'total' => $total,
                'assessed' => $assessed,
                'not_assessed' => $notAssessed,
                'compliance' => round($compliance, 2),
            ] + $ratingCounts;

            $summary['levels'][$level] = $levelData;

            // Update totals
            $summary['totals']['activities'] += $total;
            $summary['totals']['assessed'] += $assessed;
            $summary['totals']['not_assessed'] += $notAssessed;
            foreach ($ratingCounts as $key => $value) {
                $summary['totals'][$key] += $value;
            }
        }

        // Calculate overall compliance
        if ($summary['totals']['activities'] > 0) {
            $totalAnswers = AssessmentAnswer::where('assessment_id', $assessment->id)
                ->whereIn('question_id', function($query) use ($gamo) {
                    $query->select('id')
                        ->from('gamo_questions')
                        ->where('gamo_objective_id', $gamo->id)
                        ->where('is_active', true);
                })
                ->sum('capability_score');
            
            $summary['totals']['compliance'] = round(($totalAnswers / $summary['totals']['activities']) * 100, 2);
        }

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'levels' => $summary['levels'],
            'totals' => $summary['totals'],
        ]);
    }

    /**
     * Get summary for ALL GAMO objectives in assessment
     */
    public function getSummaryAllGamos(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $gamos = $assessment->gamoObjectives()
            ->withPivot('target_maturity_level')
            ->with(['questions' => function($query) {
                $query->where('is_active', true)
                      ->where('maturity_level', '>=', 2); // COBIT 2019: Level 2-5 only
            }])->get();

        $summaryData = [];
        $grandTotals = [
            'activities' => 0,
            'assessed' => 0,
        ];

        foreach ($gamos as $gamo) {
            $activities = $gamo->questions;
            $total = $activities->count();

            $answers = AssessmentAnswer::where('assessment_id', $assessment->id)
                ->whereIn('question_id', $activities->pluck('id'))
                ->whereNotNull('capability_rating')
                ->get();

            $assessedCount = $answers->count();
            $avgScore = $answers->avg('capability_score') ?? 0;

            $summaryData[] = [
                'code' => $gamo->code,
                'name' => $gamo->name,
                'total_activities' => $total,
                'assessed_count' => $assessedCount,
                'avg_score' => round($avgScore, 2),
                'target_level' => $gamo->pivot->target_maturity_level ?? 3,
            ];

            $grandTotals['activities'] += $total;
            $grandTotals['assessed'] += $assessedCount;
        }

        return response()->json([
            'success' => true,
            'gamos' => $summaryData,
            'totals' => $grandTotals,
        ]);
    }

    /**
     * Get evidence repository for entire assessment
     */
    public function getEvidenceRepository(Assessment $assessment, Request $request)
    {
        $this->authorize('view', $assessment);

        $query = AssessmentEvidence::where('assessment_id', $assessment->id)
            ->with(['activity.gamoObjective', 'uploader:id,name']);

        // Filter by GAMO
        if ($request->has('gamo_id') && $request->gamo_id) {
            $query->whereHas('activity', function($q) use ($request) {
                $q->where('gamo_objective_id', $request->gamo_id);
            });
        }

        // Filter by level
        if ($request->has('level') && $request->level) {
            $query->whereHas('activity', function($q) use ($request) {
                $q->where('maturity_level', $request->level);
            });
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            if ($request->type === 'file') {
                $query->whereNotNull('file_path');
            } else {
                $query->whereNotNull('url');
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('evidence_description', 'like', "%{$search}%")
                  ->orWhere('evidence_name', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        $evidence = $query->orderBy('created_at', 'desc')->get()->map(function($ev) use ($assessment) {
            return [
                'id' => $ev->id,
                'assessment_id' => $assessment->id,
                'activity_code' => $ev->activity->code ?? 'N/A',
                'activity_name' => $ev->activity->question_text ?? 'Unknown',
                'gamo_code' => $ev->activity->gamoObjective->code ?? 'N/A',
                'evidence_name' => $ev->evidence_name,
                'evidence_description' => $ev->evidence_description,
                'file_name' => $ev->file_name,
                'file_path' => $ev->file_path,
                'url' => $ev->url,
                'uploaded_by' => $ev->uploader->name ?? '-',
                'uploaded_at' => $ev->created_at->format('d M Y H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'evidence' => $evidence,
        ]);
    }

    /**
     * Get PBC (Prepared By Client) documents by level
     */
    public function getPBCByLevel(Assessment $assessment, GamoObjective $gamo, Request $request)
    {
        $this->authorize('view', $assessment);

        $level = $request->input('level', 2);

        $activities = GamoQuestion::where('gamo_objective_id', $gamo->id)
            ->where('maturity_level', $level)
            ->where('is_active', true)
            ->orderBy('question_order')
            ->get()
            ->map(function($question) use ($assessment) {
                // Count evidence for this activity
                $evidenceCount = AssessmentEvidence::where('assessment_id', $assessment->id)
                    ->where('activity_id', $question->id)
                    ->count();

                // Get answer status
                $answer = AssessmentAnswer::where('assessment_id', $assessment->id)
                    ->where('question_id', $question->id)
                    ->first();

                // Split English and Indonesian text
                $texts = explode(' | ', $question->question_text);
                $textEn = $texts[0] ?? $question->question_text;
                $textId = $texts[1] ?? $textEn;

                // Determine PBC status based on evidence and rating
                $status = 'pending'; // belum ada evidence dan belum rated
                if ($evidenceCount > 0 && $answer && $answer->capability_rating) {
                    $status = 'complete'; // ada evidence dan sudah rated
                } elseif ($evidenceCount > 0) {
                    $status = 'partial'; // ada evidence tapi belum rated
                } elseif ($answer && $answer->capability_rating) {
                    $status = 'rated'; // sudah rated tapi belum ada evidence
                }

                return [
                    'id' => $question->id,
                    'code' => $question->code,
                    'name' => $textEn,
                    'translated_text' => $textId,
                    'level' => $question->maturity_level,
                    'evidence_count' => $evidenceCount,
                    'status' => $status,
                    'rating' => $answer?->capability_rating,
                    'notes' => $answer?->notes,
                ];
            });

        return response()->json([
            'success' => true,
            'level' => $level,
            'activities' => $activities,
        ]);
    }

    /**
     * Get OFI data for a GAMO objective
     */
    public function getOFIData(Assessment $assessment, GamoObjective $gamo)
    {
        $this->authorize('take-assessment', $assessment);

        // Get current and target levels
        $selection = AssessmentGamoSelection::where('assessment_id', $assessment->id)
            ->where('gamo_objective_id', $gamo->id)
            ->first();

        if (!$selection) {
            return response()->json(['error' => 'GAMO not selected'], 404);
        }

        $targetLevel = $selection->target_maturity_level;
        
        // Calculate current level based on answers
        $currentLevel = $this->calculateCurrentLevel($assessment, $gamo);

        // Get auto-generated OFIs
        $autoOFIs = AssessmentOfi::where('assessment_id', $assessment->id)
            ->where('gamo_objective_id', $gamo->id)
            ->where('type', 'auto')
            ->with('gamoObjective')
            ->get();

        // Get manual OFIs
        $manualOFIs = AssessmentOfi::where('assessment_id', $assessment->id)
            ->where('gamo_objective_id', $gamo->id)
            ->where('type', 'manual')
            ->with('gamoObjective')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'gamo' => [
                'id' => $gamo->id,
                'code' => $gamo->code,
                'name' => $gamo->name,
            ],
            'current_level' => $currentLevel,
            'target_level' => $targetLevel,
            'gap' => $targetLevel - $currentLevel,
            'auto_ofis' => $autoOFIs,
            'manual_ofis' => $manualOFIs,
        ]);
    }

    /**
     * Generate auto OFI recommendations
     */
    public function generateAutoOFI(Assessment $assessment, GamoObjective $gamo)
    {
        $this->authorize('take-assessment', $assessment);

        // Get current and target levels
        $selection = AssessmentGamoSelection::where('assessment_id', $assessment->id)
            ->where('gamo_objective_id', $gamo->id)
            ->first();

        if (!$selection) {
            return response()->json(['error' => 'GAMO not selected'], 404);
        }

        $targetLevel = $selection->target_maturity_level;
        $currentLevel = $this->calculateCurrentLevel($assessment, $gamo);

        // Delete existing auto OFIs
        AssessmentOfi::where('assessment_id', $assessment->id)
            ->where('gamo_objective_id', $gamo->id)
            ->where('type', 'auto')
            ->delete();

        $recommendations = [];

        // Generate recommendations for gap levels
        if ($currentLevel < $targetLevel) {
            // Get activities for levels between current+1 and target
            for ($level = $currentLevel + 1; $level <= $targetLevel; $level++) {
                $activities = GamoQuestion::where('gamo_objective_id', $gamo->id)
                    ->where('maturity_level', $level)
                    ->get();

                foreach ($activities as $activity) {
                    // Get answer for this activity
                    $answer = AssessmentAnswer::where('assessment_id', $assessment->id)
                        ->where('question_id', $activity->id)
                        ->first();

                    $compliance = $answer ? $answer->compliance_percentage : 0;

                    // Recommend if not fully achieved (< 85%)
                    if ($compliance < 85) {
                        $parts = explode(' | ', $activity->question_text);
                        $activityNameId = $parts[1] ?? $parts[0];

                        $recommendations[] = [
                            'level' => $level,
                            'activity_code' => $activity->code,
                            'activity_name' => $activityNameId,
                            'current_compliance' => $compliance,
                        ];
                    }
                }
            }

            // Create summary OFI record
            if (!empty($recommendations)) {
                $description = "<p><strong>Kesenjangan Kapabilitas: Level {$currentLevel} → Level {$targetLevel}</strong></p>";
                $description .= "<p>Untuk mencapai target level, disarankan untuk meningkatkan aktivitas berikut:</p>";
                $description .= "<ul>";
                
                foreach ($recommendations as $rec) {
                    $description .= "<li>";
                    $description .= "<strong>[{$rec['activity_code']}]</strong> {$rec['activity_name']} ";
                    $description .= "(Level {$rec['level']}, Kepatuhan saat ini: {$rec['current_compliance']}%)";
                    $description .= "</li>";
                }
                
                $description .= "</ul>";

                $gapScore = $targetLevel - $currentLevel;
                
                AssessmentOfi::create([
                    'assessment_id' => $assessment->id,
                    'gamo_objective_id' => $gamo->id,
                    'title' => "Rekomendasi Peningkatan Level {$currentLevel} ke Level {$targetLevel}",
                    'description' => $description,
                    'type' => 'auto',
                    'priority' => $gapScore >= 2 ? 'high' : 'medium',
                    'status' => 'open',
                    'category' => 'Process',
                    'current_level' => $currentLevel,
                    'target_level' => $targetLevel,
                    'gap_score' => $gapScore,
                    'created_by' => Auth::id(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Auto OFI generated successfully',
            'recommendations_count' => count($recommendations),
        ]);
    }

    /**
     * Store new manual OFI
     */
    public function storeOFI(Request $request, Assessment $assessment, GamoObjective $gamo)
    {
        $this->authorize('take-assessment', $assessment);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:open,in_progress,resolved,closed',
            'category' => 'nullable|string|max:100',
            'target_date' => 'nullable|date',
        ]);

        $ofi = AssessmentOfi::create([
            'assessment_id' => $assessment->id,
            'gamo_objective_id' => $gamo->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => 'manual',
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'category' => $validated['category'] ?? null,
            'target_date' => $validated['target_date'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OFI created successfully',
            'ofi' => $ofi,
        ]);
    }

    /**
     * Update existing OFI
     */
    public function updateOFI(Request $request, Assessment $assessment, AssessmentOfi $ofi)
    {
        $this->authorize('take-assessment', $assessment);

        // Only allow updating manual OFIs
        if ($ofi->type === 'auto') {
            return response()->json(['error' => 'Cannot edit auto-generated OFI'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:open,in_progress,resolved,closed',
            'category' => 'nullable|string|max:100',
            'target_date' => 'nullable|date',
        ]);

        $ofi->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'category' => $validated['category'] ?? null,
            'target_date' => $validated['target_date'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OFI updated successfully',
            'ofi' => $ofi,
        ]);
    }

    /**
     * Delete OFI
     */
    public function deleteOFI(Assessment $assessment, AssessmentOfi $ofi)
    {
        $this->authorize('take-assessment', $assessment);

        // Only allow deleting manual OFIs
        if ($ofi->type === 'auto') {
            return response()->json(['error' => 'Cannot delete auto-generated OFI'], 403);
        }

        $ofi->delete();

        return response()->json([
            'success' => true,
            'message' => 'OFI deleted successfully',
        ]);
    }

    /**
     * Calculate current capability level for a GAMO
     */
    private function calculateCurrentLevel(Assessment $assessment, GamoObjective $gamo): int
    {
        // Get all answered questions for this GAMO, ordered by level
        $answers = AssessmentAnswer::where('assessment_id', $assessment->id)
            ->where('gamo_objective_id', $gamo->id)
            ->whereNotNull('answered_at')
            ->with('question')
            ->get();

        if ($answers->isEmpty()) {
            return 0; // No answers yet
        }

        // Check each level from 2 to 5
        for ($level = 5; $level >= 2; $level--) {
            $levelAnswers = $answers->filter(function($answer) use ($level) {
                return $answer->question->maturity_level == $level;
            });

            if ($levelAnswers->isEmpty()) {
                continue; // No questions answered at this level
            }

            // Calculate average compliance for this level
            $avgCompliance = $levelAnswers->avg('compliance_percentage');

            // Consider level achieved if average compliance >= 85%
            if ($avgCompliance >= 85) {
                return $level;
            }
        }

        // Return 0 (not level 1) for COBIT 2019 - Level 1 doesn't exist
        return 0;
    }

    /**
     * Auto-update GAMO score after answer is saved
     */
    private function updateGamoScore(Assessment $assessment, int $gamoObjectiveId): void
    {
        // Get all answers for this GAMO
        $answers = AssessmentAnswer::where('assessment_id', $assessment->id)
            ->where('gamo_objective_id', $gamoObjectiveId)
            ->whereNotNull('answered_at')
            ->with('question')
            ->get();

        if ($answers->isEmpty()) {
            return; // No answers yet, skip
        }

        // Calculate capability level based on COBIT 2019 rules (85% compliance threshold)
        $achievedLevel = 0;
        
        // Check each level sequentially from 2 to 5
        for ($level = 2; $level <= 5; $level--) {
            $levelAnswers = $answers->filter(function($answer) use ($level) {
                return $answer->question && $answer->question->maturity_level == $level;
            });

            // Skip if no activities at this level
            if ($levelAnswers->isEmpty()) {
                continue;
            }

            // Calculate weighted average score for this level
            $totalWeight = 0;
            $weightedScore = 0;

            foreach ($levelAnswers as $answer) {
                $weight = 1; // Default weight
                $totalWeight += $weight;
                $weightedScore += $weight * ($answer->capability_score ?? 0);
            }

            // Calculate compliance percentage
            $compliance = $totalWeight > 0 ? (($weightedScore / $totalWeight) * 100) : 0;

            // Level is achieved if compliance >= 85%
            if ($compliance >= 85) {
                $achievedLevel = $level;
            } else {
                // Stop checking higher levels if current level not achieved
                break;
            }
        }

        // Calculate average maturity and capability scores
        $avgMaturity = $answers->avg(function($answer) {
            // Convert capability_score to maturity level equivalent
            $score = $answer->capability_score ?? 0;
            $level = $answer->question->maturity_level ?? 0;
            return $score * $level;
        });
        
        $avgCapability = $answers->avg('capability_score') ?? 0;

        // Calculate completion percentage
        $totalQuestions = \App\Models\GamoQuestion::where('gamo_objective_id', $gamoObjectiveId)
            ->where('is_active', true)
            ->count();
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

        // Get target level from assessment
        $targetLevel = $assessment->gamoSelections()
            ->where('gamo_objective_id', $gamoObjectiveId)
            ->value('target_maturity_level') ?? 3;

        // Create or update GAMO score
        \App\Models\GamoScore::updateOrCreate(
            [
                'assessment_id' => $assessment->id,
                'gamo_objective_id' => $gamoObjectiveId,
            ],
            [
                'current_maturity_level' => round($avgMaturity, 2),
                'target_maturity_level' => $targetLevel,
                'capability_score' => round($avgCapability, 2),
                'capability_level' => $achievedLevel, // This is the actual achieved level based on 85% rule
                'percentage_complete' => $completionPercentage,
                'status' => $status,
            ]
        );

        // Update overall assessment maturity
        $overallMaturity = \App\Models\GamoScore::where('assessment_id', $assessment->id)
            ->avg('capability_level');
        
        $assessment->update([
            'overall_maturity_level' => round($overallMaturity ?? 0, 2),
        ]);
    }
}

