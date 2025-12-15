<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EvidenceWebController extends Controller
{
    /**
     * Display evidence list for an assessment
     */
    public function index(Assessment $assessment)
    {
        // Check authorization
        $this->authorize('view', $assessment);

        $answers = AssessmentAnswer::where('assessment_id', $assessment->id)
            ->whereNotNull('evidence_file')
            ->with(['question.gamoObjective', 'answeredBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_evidence' => $answers->total(),
            'total_size' => $this->calculateTotalSize($answers->items()),
            'latest_upload' => $answers->first()?->updated_at,
        ];

        return view('evidence.index', compact('assessment', 'answers', 'stats'));
    }

    /**
     * Show upload form for assessment
     */
    public function create(Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        // Get all answers for this assessment with their questions
        $answers = $assessment->answers()
            ->with('question.gamoObjective')
            ->get();

        return view('evidence.create', compact('assessment', 'answers'));
    }

    /**
     * Store uploaded evidence
     */
    public function store(Request $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        $validated = $request->validate([
            'answer_id' => 'required|exists:assessment_answers,id',
            'evidence' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip',
        ]);

        $answer = AssessmentAnswer::findOrFail($validated['answer_id']);

        // Verify answer belongs to this assessment
        if ($answer->assessment_id !== $assessment->id) {
            abort(403, 'Unauthorized');
        }

        // Delete old evidence if exists
        if ($answer->evidence_file) {
            Storage::disk('private')->delete($answer->evidence_file);
        }

        // Store new evidence
        $file = $request->file('evidence');
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('evidence/' . $assessment->id, $filename, 'private');

        // Update answer
        $answer->update([
            'evidence_file' => $path,
            'evidence_encrypted' => true,
        ]);

        // Log activity
        activity()
            ->performedOn($answer)
            ->causedBy(auth()->user())
            ->log('Uploaded evidence file: ' . $filename);

        return redirect()
            ->route('evidence.index', $assessment)
            ->with('success', 'Evidence uploaded successfully');
    }

    /**
     * Download evidence file
     */
    public function download(Assessment $assessment, AssessmentAnswer $answer)
    {
        $this->authorize('view', $assessment);

        // Verify answer belongs to this assessment
        if ($answer->assessment_id !== $assessment->id) {
            abort(403, 'Unauthorized');
        }

        if (!$answer->evidence_file || !Storage::disk('private')->exists($answer->evidence_file)) {
            abort(404, 'Evidence file not found');
        }

        // Log download
        activity()
            ->performedOn($answer)
            ->causedBy(auth()->user())
            ->log('Downloaded evidence file');

        return Storage::disk('private')->download(
            $answer->evidence_file,
            basename($answer->evidence_file)
        );
    }

    /**
     * Delete evidence file
     */
    public function destroy(Assessment $assessment, AssessmentAnswer $answer)
    {
        $this->authorize('update', $assessment);

        // Verify answer belongs to this assessment
        if ($answer->assessment_id !== $assessment->id) {
            abort(403, 'Unauthorized');
        }

        if ($answer->evidence_file) {
            Storage::disk('private')->delete($answer->evidence_file);
            
            $filename = basename($answer->evidence_file);
            
            $answer->update([
                'evidence_file' => null,
                'evidence_encrypted' => false,
            ]);

            // Log deletion
            activity()
                ->performedOn($answer)
                ->causedBy(auth()->user())
                ->log('Deleted evidence file: ' . $filename);
        }

        return redirect()
            ->route('evidence.index', $assessment)
            ->with('success', 'Evidence deleted successfully');
    }

    /**
     * Calculate total size of evidence files
     */
    private function calculateTotalSize($answers): string
    {
        $totalBytes = 0;
        foreach ($answers as $answer) {
            if ($answer->evidence_file && Storage::disk('private')->exists($answer->evidence_file)) {
                $totalBytes += Storage::disk('private')->size($answer->evidence_file);
            }
        }

        if ($totalBytes >= 1048576) {
            return round($totalBytes / 1048576, 2) . ' MB';
        } elseif ($totalBytes >= 1024) {
            return round($totalBytes / 1024, 2) . ' KB';
        }
        return $totalBytes . ' B';
    }
}
