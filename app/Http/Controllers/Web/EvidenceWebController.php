<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\EvidenceVersion;
use App\Models\EvidenceAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EvidenceWebController extends Controller
{
    use AuthorizesRequests;
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
     * Store uploaded evidence with versioning
     */
    public function store(Request $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        $validated = $request->validate([
            'answer_id' => 'required|exists:assessment_answers,id',
            'evidence' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip',
            'notes' => 'nullable|string|max:1000',
            'tags' => 'nullable|string|max:255',
        ]);

        $answer = AssessmentAnswer::findOrFail($validated['answer_id']);

        // Verify answer belongs to this assessment
        if ($answer->assessment_id !== $assessment->id) {
            abort(403, 'Unauthorized');
        }

        DB::beginTransaction();
        try {
            // Store new evidence
            $file = $request->file('evidence');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('evidence/' . $assessment->id, $filename, 'private');

            // Calculate file hash for integrity
            $fileHash = hash_file('sha256', $file->getRealPath());

            // Determine version number
            $versionNumber = 1;
            if ($answer->evidence_file) {
                $versionNumber = ($answer->current_version ?? 1) + 1;
            }

            // Create version record
            $version = \App\Models\EvidenceVersion::create([
                'assessment_answer_id' => $answer->id,
                'version_number' => $versionNumber,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'file_hash' => $fileHash,
                'is_encrypted' => true,
                'version_notes' => $request->notes,
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now(),
            ]);

            // Update answer
            $answer->update([
                'evidence_file' => $path,
                'evidence_encrypted' => true,
                'current_version' => $versionNumber,
                'current_version_id' => $version->id,
                'notes' => $request->notes ?? $answer->notes,
                'tags' => $request->tags ?? $answer->tags,
                'evidence_updated_at' => now(),
            ]);

            // Log access
            \App\Models\EvidenceAccessLog::logAccess($answer->id, $version->id, 'upload');

            // Log activity
            activity()
                ->performedOn($answer)
                ->causedBy(auth()->user())
                ->log('Uploaded evidence file (v' . $versionNumber . '): ' . $filename);

            DB::commit();

            return redirect()
                ->route('assessments.evidence.index', $assessment)
                ->with('success', 'Evidence uploaded successfully (Version ' . $versionNumber . ')');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Evidence upload failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to upload evidence. Please try again.')
                ->withInput();
        }
    }

    /**
     * Download evidence file with access logging
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

        // Log access
        EvidenceAccessLog::logAccess($answer->id, $answer->current_version_id, 'download');

        // Log activity
        activity()
            ->performedOn($answer)
            ->causedBy(auth()->user())
            ->log('Downloaded evidence file');

        return response()->download(
            Storage::disk('private')->path($answer->evidence_file),
            basename($answer->evidence_file)
        );
    }

    /**
     * Show evidence preview
     */
    public function preview(Assessment $assessment, AssessmentAnswer $answer)
    {
        $this->authorize('view', $assessment);

        // Verify answer belongs to this assessment
        if ($answer->assessment_id !== $assessment->id) {
            abort(403, 'Unauthorized');
        }

        // Get all versions
        $versions = EvidenceVersion::where('assessment_answer_id', $answer->id)
            ->with('uploadedBy')
            ->orderBy('version_number', 'desc')
            ->get();

        // Log access
        EvidenceAccessLog::logAccess($answer->id, $answer->current_version_id, 'view');

        return view('evidence.preview', compact('assessment', 'answer', 'versions'));
    }

    /**
     * Upload new version of evidence
     */
    public function uploadVersion(Request $request, Assessment $assessment, AssessmentAnswer $answer)
    {
        $this->authorize('update', $assessment);

        // Verify answer belongs to this assessment
        if ($answer->assessment_id !== $assessment->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'evidence' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('evidence');
            $filename = time() . '_v' . (($answer->current_version ?? 1) + 1) . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('evidence/' . $assessment->id, $filename, 'private');

            // Calculate file hash
            $fileHash = hash_file('sha256', $file->getRealPath());

            // Create new version
            $versionNumber = ($answer->current_version ?? 1) + 1;
            $version = EvidenceVersion::create([
                'assessment_answer_id' => $answer->id,
                'version_number' => $versionNumber,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientOriginalExtension(),
                'file_size' => $file->getSize(),
                'file_hash' => $fileHash,
                'is_encrypted' => true,
                'version_notes' => $request->notes,
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now(),
            ]);

            // Update answer to use new version
            $answer->update([
                'evidence_file' => $path,
                'current_version' => $versionNumber,
                'current_version_id' => $version->id,
                'evidence_updated_at' => now(),
            ]);

            // Log access
            EvidenceAccessLog::logAccess($answer->id, $version->id, 'upload');

            DB::commit();

            return redirect()
                ->route('assessments.evidence.preview', [$assessment, $answer])
                ->with('success', 'New version uploaded successfully (Version ' . $versionNumber . ')');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Version upload failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to upload new version. Please try again.');
        }
    }

    /**
     * Download specific version
     */
    public function downloadVersion(Assessment $assessment, AssessmentAnswer $answer, EvidenceVersion $version)
    {
        $this->authorize('view', $assessment);

        // Verify relationships
        if ($answer->assessment_id !== $assessment->id || $version->assessment_answer_id !== $answer->id) {
            abort(403, 'Unauthorized');
        }

        if (!Storage::disk('private')->exists($version->file_path)) {
            abort(404, 'File not found');
        }

        // Log access
        EvidenceAccessLog::logAccess($answer->id, $version->id, 'download');

        return response()->download(
            Storage::disk('private')->path($version->file_path),
            $version->file_name
        );
    }

    /**
     * Restore previous version
     */
    public function restoreVersion(Request $request, Assessment $assessment, AssessmentAnswer $answer, EvidenceVersion $version)
    {
        $this->authorize('update', $assessment);

        // Verify relationships
        if ($answer->assessment_id !== $assessment->id || $version->assessment_answer_id !== $answer->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();
        try {
            // Update answer to use this version
            $answer->update([
                'evidence_file' => $version->file_path,
                'current_version_id' => $version->id,
                'evidence_updated_at' => now(),
            ]);

            // Log access
            EvidenceAccessLog::logAccess($answer->id, $version->id, 'restore');

            // Log activity
            activity()
                ->performedOn($answer)
                ->causedBy(auth()->user())
                ->log('Restored evidence to version ' . $version->version_number);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Version restored successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Version restore failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore version'
            ], 500);
        }
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
