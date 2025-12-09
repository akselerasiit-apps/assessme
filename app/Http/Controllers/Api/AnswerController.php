<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\GamoObjective;
use App\Http\Resources\AnswerResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnswerController extends Controller
{
    use AuthorizesRequests;
    /**
     * Get all answers for an assessment
     */
    public function index(Request $request, $assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $this->authorize('view', $assessment);
        
        $answers = AssessmentAnswer::where('assessment_id', $assessmentId)
            ->with(['question', 'gamoObjective', 'answerer', 'capabilityScores'])
            ->when($request->input('gamo_objective_id'), function($q) use ($request) {
                return $q->where('gamo_objective_id', $request->input('gamo_objective_id'));
            })
            ->when($request->input('answered'), function($q) use ($request) {
                $answered = $request->input('answered') === 'true';
                return $answered ? $q->whereNotNull('answered_at') : $q->whereNull('answered_at');
            })
            ->paginate($request->input('per_page', 15));
        
        return AnswerResource::collection($answers);
    }

    /**
     * Get single answer
     */
    public function show($assessmentId, $answerId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $answer = AssessmentAnswer::where('id', $answerId)
            ->where('assessment_id', $assessmentId)
            ->with(['question', 'gamoObjective', 'answerer', 'capabilityScores'])
            ->firstOrFail();
        
        $this->authorize('view', $assessment);
        
        return new AnswerResource($answer);
    }

    /**
     * Store/Update answer to a question
     */
    public function store(Request $request, $assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $this->authorize('answer', $assessment);
        
        if (in_array($assessment->status, ['completed', 'reviewed', 'approved', 'archived'])) {
            return response()->json(['message' => 'Assessment is locked'], 403);
        }
        
        $request->validate([
            'question_id' => 'required|exists:gamo_questions,id',
            'gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'answer_text' => 'required|string',
            'answer_json' => 'nullable|json',
            'maturity_level' => 'nullable|integer|min:0|max:5',
            'notes' => 'nullable|string'
        ]);
        
        $answer = AssessmentAnswer::updateOrCreate(
            [
                'assessment_id' => $assessmentId,
                'question_id' => $request->input('question_id')
            ],
            [
                'gamo_objective_id' => $request->input('gamo_objective_id'),
                'answer_text' => $request->input('answer_text'),
                'answer_json' => $request->input('answer_json'),
                'maturity_level' => $request->input('maturity_level', 0),
                'notes' => $request->input('notes'),
                'answered_by' => auth()->id(),
                'answered_at' => now(),
                'is_encrypted' => true
            ]
        );
        
        // activity()->performedOn($assessment)->causedBy(auth()->user())->withProperties([
        //     'action' => 'answer.created',
        //     'entity_type' => 'Answer',
        //     'entity_id' => $answer->id,
        //     'ip_address' => request()->ip()
        // ])->log('answer.created');
        
        // $this->updateAssessmentProgress($assessment);
        
        return new AnswerResource($answer->load(['question', 'gamoObjective', 'answerer']));
    }

    /**
     * Upload evidence file
     */
    public function uploadEvidence(Request $request, $assessmentId, $answerId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $answer = AssessmentAnswer::where('id', $answerId)->where('assessment_id', $assessmentId)->firstOrFail();
        
        $this->authorize('answer', $assessment);
        
        $request->validate([
            'evidence_file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,txt'
        ]);
        
        $file = $request->file('evidence_file');
        $filename = 'ASS-' . $assessment->id . '-Q' . $answer->question_id . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        
        $filepath = $file->storeAs('encrypted/assessments/' . $assessment->id, $filename, 'private');
        
        $answer->update(['evidence_file' => $filepath, 'evidence_encrypted' => true]);
        
        // activity()->performedOn($assessment)->causedBy(auth()->user())->log('evidence.uploaded');
        
        return response()->json(['message' => 'Evidence uploaded successfully'], 201);
    }

    /**
     * Get evidence file
     */
    public function getEvidence($assessmentId, $answerId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $answer = AssessmentAnswer::where('id', $answerId)->where('assessment_id', $assessmentId)->firstOrFail();
        
        $this->authorize('view', $assessment);
        
        if (!$answer->evidence_file || !Storage::disk('private')->exists($answer->evidence_file)) {
            return response()->json(['message' => 'Evidence file not found'], 404);
        }
        
        // activity()->performedOn($assessment)->causedBy(auth()->user())->log('evidence.accessed');
        
        return Storage::disk('private')->download($answer->evidence_file);
    }

    /**
     * Delete answer
     */
    public function destroy($assessmentId, $answerId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $answer = AssessmentAnswer::where('id', $answerId)->where('assessment_id', $assessmentId)->firstOrFail();
        
        $this->authorize('answer', $assessment);
        
        if (in_array($assessment->status, ['completed', 'reviewed', 'approved', 'archived'])) {
            return response()->json(['message' => 'Assessment is locked'], 403);
        }
        
        if ($answer->evidence_file) {
            Storage::disk('private')->delete($answer->evidence_file);
        }
        
        activity()->performedOn($assessment)->causedBy(auth()->user())->log('answer.deleted');
        
        $answer->capabilityScores()->delete();
        $answer->delete();
        
        $this->updateAssessmentProgress($assessment);
        
        return response()->json(['message' => 'Answer deleted successfully']);
    }

    private function updateAssessmentProgress(Assessment $assessment)
    {
        $selectedGamoCount = $assessment->gamoObjectives()->count();
        
        if ($selectedGamoCount === 0) {
            $assessment->update(['progress_percentage' => 0]);
            return;
        }
        
        $answeredCount = AssessmentAnswer::where('assessment_id', $assessment->id)
            ->whereNotNull('answered_at')
            ->distinct('gamo_objective_id')
            ->count('gamo_objective_id');
        
        $progress = ($answeredCount / $selectedGamoCount) * 100;
        $assessment->update(['progress_percentage' => round($progress)]);
    }
    
    /**
     * Simplified store for tests - extracts assessment_id from request body
     */
    public function storeSimple(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
        ]);
        
        return $this->store($request, $request->input('assessment_id'));
    }
    
    /**
     * Simplified update for tests - works with answer ID directly
     */
    public function updateSimple(Request $request, $answerId)
    {
        $answer = AssessmentAnswer::findOrFail($answerId);
        return $this->store($request, $answer->assessment_id);
    }

    /**
     * Simplified evidence upload for tests - accepts assessment_id in body
     */
    public function uploadEvidenceSimple(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
            'answer_id' => 'required|exists:assessment_answers,id',
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,txt',
        ]);

        $assessment = Assessment::findOrFail($request->assessment_id);
        $answer = AssessmentAnswer::findOrFail($request->answer_id);
        
        $this->authorize('answer', $assessment);
        
        $file = $request->file('file');
        $filename = 'ASS-' . $assessment->id . '-Q' . $answer->question_id . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        
        $filepath = $file->storeAs('encrypted/assessments/' . $assessment->id, $filename, 'private');
        
        $answer->update(['evidence_file' => $filepath, 'evidence_encrypted' => true]);
        
        return response()->json(['message' => 'Evidence uploaded successfully'], 201);
    }
}
