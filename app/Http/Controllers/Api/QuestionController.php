<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GamoQuestion;
use App\Models\GamoObjective;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions with filtering and search
     */
    public function index(Request $request)
    {
        // UAM: Admin+ can view all, Manager can view, Assessor read-only
        $user = $request->user();
        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Assessor'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = GamoQuestion::with('gamoObjective');

        // Search by code, question text, or guidance
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('question_text', 'like', "%{$search}%")
                  ->orWhere('guidance', 'like', "%{$search}%");
            });
        }

        // Filter by GAMO objective
        if ($request->has('gamo_objective_id')) {
            $query->where('gamo_objective_id', $request->gamo_objective_id);
        }

        // Filter by maturity level
        if ($request->has('maturity_level')) {
            $query->where('maturity_level', $request->maturity_level);
        }

        // Filter by question type
        if ($request->has('question_type')) {
            $query->where('question_type', $request->question_type);
        }

        // Filter by active status
        $query->where('is_active', $request->get('is_active', true));

        // Order by GAMO objective and question order
        $query->orderBy('gamo_objective_id', 'asc')
              ->orderBy('question_order', 'asc');

        $perPage = $request->get('per_page', 50);
        $questions = $query->paginate($perPage);

        return response()->json($questions);
    }

    /**
     * Store a newly created question
     */
    public function store(Request $request)
    {
        // UAM: Only Admin+ can create questions
        $user = $request->user();
        if (!$user->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json(['message' => 'Unauthorized. Only Admin can create questions.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:gamo_questions,code|regex:/^[A-Z0-9_-]+$/',
            'gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'question_text' => 'required|string|min:10',
            'guidance' => 'nullable|string',
            'evidence_requirement' => 'nullable|string',
            'question_type' => 'required|in:text,rating,multiple_choice,yes_no,evidence',
            'maturity_level' => 'required|integer|min:0|max:5',
            'required' => 'nullable|boolean',
            'question_order' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $question = GamoQuestion::create($request->all());

            // Audit log
            activity()
                ->causedBy($user)
                ->performedOn($question)
                ->withProperties(['attributes' => $question->toArray()])
                ->log('Created question: ' . $question->code);

            DB::commit();

            return response()->json([
                'message' => 'Question created successfully',
                'data' => $question->load('gamoObjective')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create question', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified question
     */
    public function show(Request $request, $id)
    {
        // UAM: Admin+ and Manager can view details
        $user = $request->user();
        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Assessor'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $question = GamoQuestion::with('gamoObjective')->find($id);

        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        return response()->json(['data' => $question]);
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, $id)
    {
        // UAM: Only Admin+ can update questions
        $user = $request->user();
        if (!$user->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json(['message' => 'Unauthorized. Only Admin can update questions.'], 403);
        }

        $question = GamoQuestion::find($id);

        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|string|max:50|unique:gamo_questions,code,' . $id . '|regex:/^[A-Z0-9_-]+$/',
            'gamo_objective_id' => 'sometimes|exists:gamo_objectives,id',
            'question_text' => 'sometimes|string|min:10',
            'guidance' => 'nullable|string',
            'evidence_requirement' => 'nullable|string',
            'question_type' => 'sometimes|in:text,rating,multiple_choice,yes_no,evidence',
            'maturity_level' => 'sometimes|integer|min:0|max:5',
            'required' => 'nullable|boolean',
            'question_order' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $oldValues = $question->toArray();
            $question->update($request->all());

            // Audit log
            activity()
                ->causedBy($user)
                ->performedOn($question)
                ->withProperties([
                    'old' => $oldValues,
                    'attributes' => $question->fresh()->toArray()
                ])
                ->log('Updated question: ' . $question->code);

            DB::commit();

            return response()->json([
                'message' => 'Question updated successfully',
                'data' => $question->load('gamoObjective')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update question', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified question
     */
    public function destroy(Request $request, $id)
    {
        // UAM: Only Super Admin can delete questions
        $user = $request->user();
        if (!$user->hasRole('Super Admin')) {
            return response()->json(['message' => 'Unauthorized. Only Super Admin can delete questions.'], 403);
        }

        $question = GamoQuestion::find($id);

        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        // Check if question is used in any assessment answers
        $usedInAnswers = DB::table('assessment_answers')
            ->where('question_id', $id)
            ->exists();

        if ($usedInAnswers) {
            return response()->json([
                'message' => 'Cannot delete question. It is being used in assessment answers.',
                'suggestion' => 'Consider deactivating instead by setting is_active=false'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $questionData = $question->toArray();
            $question->delete();

            // Audit log
            activity()
                ->causedBy($user)
                ->withProperties(['attributes' => $questionData])
                ->log('Deleted question: ' . $questionData['code']);

            DB::commit();

            return response()->json(['message' => 'Question deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to delete question', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get questions by GAMO objective
     */
    public function byGamoObjective(Request $request, $gamoObjectiveId)
    {
        // UAM: All authenticated users can view
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $gamoObjective = GamoObjective::find($gamoObjectiveId);

        if (!$gamoObjective) {
            return response()->json(['message' => 'GAMO Objective not found'], 404);
        }

        $query = GamoQuestion::where('gamo_objective_id', $gamoObjectiveId)
            ->where('is_active', $request->get('is_active', true));

        // Filter by maturity level if specified
        if ($request->has('maturity_level')) {
            $query->where('maturity_level', $request->maturity_level);
        }

        $questions = $query->orderBy('question_order', 'asc')->get();

        return response()->json([
            'gamo_objective' => $gamoObjective,
            'questions' => $questions,
            'total_questions' => $questions->count()
        ]);
    }

    /**
     * Bulk import questions from array
     */
    public function bulkImport(Request $request)
    {
        // UAM: Only Admin+ can bulk import
        $user = $request->user();
        if (!$user->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json(['message' => 'Unauthorized. Only Admin can bulk import questions.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'questions' => 'required|array|min:1',
            'questions.*.code' => 'required|string|max:50|unique:gamo_questions,code|regex:/^[A-Z0-9_-]+$/',
            'questions.*.gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'questions.*.question_text' => 'required|string|min:10',
            'questions.*.guidance' => 'nullable|string',
            'questions.*.evidence_requirement' => 'nullable|string',
            'questions.*.question_type' => 'required|in:text,rating,multiple_choice,yes_no,evidence',
            'questions.*.maturity_level' => 'required|integer|min:0|max:5',
            'questions.*.required' => 'nullable|boolean',
            'questions.*.question_order' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $imported = [];
            $failed = [];

            foreach ($request->questions as $index => $questionData) {
                try {
                    $question = GamoQuestion::create($questionData);
                    $imported[] = $question->code;
                } catch (\Exception $e) {
                    $failed[] = [
                        'index' => $index,
                        'code' => $questionData['code'] ?? 'N/A',
                        'error' => $e->getMessage()
                    ];
                }
            }

            // Audit log
            activity()
                ->causedBy($user)
                ->withProperties([
                    'imported_count' => count($imported),
                    'failed_count' => count($failed),
                    'imported_codes' => $imported
                ])
                ->log('Bulk imported questions');

            DB::commit();

            return response()->json([
                'message' => 'Bulk import completed',
                'imported_count' => count($imported),
                'failed_count' => count($failed),
                'imported' => $imported,
                'failed' => $failed
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Bulk import failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Toggle question active status
     */
    public function toggleActive(Request $request, $id)
    {
        // UAM: Only Admin+ can toggle status
        $user = $request->user();
        if (!$user->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json(['message' => 'Unauthorized. Only Admin can toggle question status.'], 403);
        }

        $question = GamoQuestion::find($id);

        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $question->is_active;
            $question->is_active = !$oldStatus;
            $question->save();

            // Audit log
            activity()
                ->causedBy($user)
                ->performedOn($question)
                ->withProperties([
                    'old_status' => $oldStatus,
                    'new_status' => $question->is_active
                ])
                ->log('Toggled question status: ' . $question->code);

            DB::commit();

            return response()->json([
                'message' => 'Question status updated successfully',
                'data' => $question,
                'is_active' => $question->is_active
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to toggle status', 'error' => $e->getMessage()], 500);
        }
    }
}
