<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentBanding;
use App\Models\GamoScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BandingController extends Controller
{
    /**
     * Display all bandings for an assessment
     */
    public function index(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $bandings = $assessment->bandings()
            ->with(['gamoObjective', 'initiatedBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Count by status
        $statistics = [
            'total' => $assessment->bandings()->count(),
            'draft' => $assessment->bandings()->where('status', 'draft')->count(),
            'submitted' => $assessment->bandings()->where('status', 'submitted')->count(),
            'approved' => $assessment->bandings()->where('status', 'approved')->count(),
            'rejected' => $assessment->bandings()->where('status', 'rejected')->count(),
        ];

        return view('banding.index', compact('assessment', 'bandings', 'statistics'));
    }

    /**
     * Show form to create new banding
     */
    public function create(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        // Only allow banding for approved assessments
        if (!in_array($assessment->status, ['reviewed', 'approved'])) {
            return back()->with('error', 'Banding can only be submitted for reviewed or approved assessments.');
        }

        // Get GAMO scores for this assessment
        $gamoScores = $assessment->gamoScores()
            ->with('gamoObjective')
            ->orderBy('current_maturity_level', 'asc')
            ->get();

        // Get existing bandings count per GAMO
        $existingBandings = $assessment->bandings()
            ->select('gamo_objective_id', DB::raw('MAX(banding_round) as max_round'))
            ->groupBy('gamo_objective_id')
            ->pluck('max_round', 'gamo_objective_id');

        return view('banding.create', compact('assessment', 'gamoScores', 'existingBandings'));
    }

    /**
     * Store new banding
     */
    public function store(Request $request, Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $request->validate([
            'gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'banding_reason' => 'required|string|max:255',
            'banding_description' => 'required|string|min:50',
            'new_maturity_level' => 'required|numeric|min:0|max:5',
            'revised_answers' => 'nullable|string',
            'additional_evidence_files' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip',
        ]);

        try {
            DB::beginTransaction();

            // Get current GAMO score
            $gamoScore = GamoScore::where('assessment_id', $assessment->id)
                ->where('gamo_objective_id', $request->gamo_objective_id)
                ->first();

            if (!$gamoScore) {
                return back()->with('error', 'GAMO score not found for selected objective.');
            }

            // Get banding round
            $existingRound = AssessmentBanding::where('assessment_id', $assessment->id)
                ->where('gamo_objective_id', $request->gamo_objective_id)
                ->max('banding_round');

            $bandingRound = ($existingRound ?? 0) + 1;

            // Count evidence
            $evidenceCount = $assessment->answers()
                ->whereHas('question', function ($q) use ($request) {
                    $q->where('gamo_objective_id', $request->gamo_objective_id);
                })
                ->whereNotNull('evidence_file')
                ->count();

            // Handle file upload
            $evidenceFilePath = null;
            if ($request->hasFile('additional_evidence_files')) {
                $file = $request->file('additional_evidence_files');
                $evidenceFilePath = $file->store("evidence/{$assessment->id}/banding", 'private');
            }

            // Create banding
            $banding = AssessmentBanding::create([
                'assessment_id' => $assessment->id,
                'gamo_objective_id' => $request->gamo_objective_id,
                'banding_round' => $bandingRound,
                'initiated_by' => auth()->id(),
                'banding_reason' => $request->banding_reason,
                'banding_description' => $request->banding_description,
                'old_maturity_level' => $gamoScore->current_maturity_level,
                'new_maturity_level' => $request->new_maturity_level,
                'old_evidence_count' => $evidenceCount,
                'new_evidence_count' => $evidenceCount + ($request->hasFile('additional_evidence_files') ? 1 : 0),
                'additional_evidence_files' => $evidenceFilePath,
                'revised_answers' => $request->revised_answers,
                'status' => 'draft',
            ]);

            activity()
                ->performedOn($banding)
                ->withProperties([
                    'assessment_code' => $assessment->code,
                    'gamo_code' => $gamoScore->gamoObjective->gamo_code,
                    'old_maturity' => $gamoScore->current_maturity_level,
                    'new_maturity' => $request->new_maturity_level,
                ])
                ->log('Created banding request (draft)');

            DB::commit();

            return redirect()
                ->route('banding.show', [$assessment, $banding])
                ->with('success', 'Banding created as draft. Please review and submit for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create banding: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display banding detail
     */
    public function show(Assessment $assessment, AssessmentBanding $banding)
    {
        $this->authorize('view', $assessment);

        // Verify banding belongs to assessment
        if ($banding->assessment_id !== $assessment->id) {
            abort(404);
        }

        $banding->load(['gamoObjective', 'initiatedBy', 'approvedBy']);

        return view('banding.show', compact('assessment', 'banding'));
    }

    /**
     * Submit banding for approval
     */
    public function submit(Assessment $assessment, AssessmentBanding $banding)
    {
        $this->authorize('view', $assessment);

        if ($banding->status !== 'draft') {
            return back()->with('error', 'Only draft bandings can be submitted.');
        }

        if ($banding->initiated_by !== auth()->id() && !auth()->user()->hasRole('Super Admin')) {
            return back()->with('error', 'You can only submit your own bandings.');
        }

        try {
            $banding->update(['status' => 'submitted']);

            activity()
                ->performedOn($banding)
                ->withProperties(['assessment_code' => $assessment->code])
                ->log('Submitted banding for approval');

            return redirect()
                ->route('banding.index', $assessment)
                ->with('success', 'Banding submitted for approval.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit banding: ' . $e->getMessage());
        }
    }

    /**
     * Display pending bandings for approval (Super Admin only)
     */
    public function pendingApproval(Request $request)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Only Super Admin can approve bandings.');
        }

        $query = AssessmentBanding::with(['assessment.company', 'gamoObjective', 'initiatedBy'])
            ->where('status', 'submitted');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('assessment', function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // Company filter (for Admin)
        if ($request->filled('company_id') && auth()->user()->hasRole('Admin')) {
            $query->whereHas('assessment', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        $bandings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('banding.pending-approval', compact('bandings'));
    }

    /**
     * Approve or reject banding
     */
    public function processApproval(Request $request, Assessment $assessment, AssessmentBanding $banding)
    {
        if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            abort(403, 'Only Admin and Super Admin can approve bandings.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'approval_notes' => 'required|string|min:10',
        ]);

        try {
            DB::beginTransaction();

            if ($request->action === 'approve') {
                // Update banding status
                $banding->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approval_notes' => $request->approval_notes,
                ]);

                // Update GAMO score with new maturity level
                $gamoScore = GamoScore::where('assessment_id', $assessment->id)
                    ->where('gamo_objective_id', $banding->gamo_objective_id)
                    ->first();

                if ($gamoScore) {
                    $gamoScore->update([
                        'current_maturity_level' => $banding->new_maturity_level,
                    ]);
                }

                activity()
                    ->performedOn($banding)
                    ->withProperties([
                        'assessment_code' => $assessment->code,
                        'notes' => $request->approval_notes,
                    ])
                    ->log('Approved banding request');

                $message = 'Banding approved and maturity level updated.';
            } else {
                // Reject banding
                $banding->update([
                    'status' => 'rejected',
                    'approved_by' => auth()->id(),
                    'approval_notes' => $request->approval_notes,
                ]);

                activity()
                    ->performedOn($banding)
                    ->withProperties([
                        'assessment_code' => $assessment->code,
                        'notes' => $request->approval_notes,
                    ])
                    ->log('Rejected banding request');

                $message = 'Banding rejected.';
            }

            DB::commit();

            return redirect()
                ->route('banding.pending-approval')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process banding: ' . $e->getMessage());
        }
    }

    /**
     * Delete draft banding
     */
    public function destroy(Assessment $assessment, AssessmentBanding $banding)
    {
        $this->authorize('view', $assessment);

        if ($banding->status !== 'draft') {
            return back()->with('error', 'Only draft bandings can be deleted.');
        }

        if ($banding->initiated_by !== auth()->id() && !auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            return back()->with('error', 'You can only delete your own bandings.');
        }

        try {
            // Delete evidence file if exists
            if ($banding->additional_evidence_files && \Storage::disk('private')->exists($banding->additional_evidence_files)) {
                \Storage::disk('private')->delete($banding->additional_evidence_files);
            }

            $banding->delete();

            return redirect()
                ->route('banding.index', $assessment)
                ->with('success', 'Banding deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete banding: ' . $e->getMessage());
        }
    }
}
