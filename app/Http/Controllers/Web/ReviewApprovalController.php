<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewApprovalController extends Controller
{
    /**
     * Display assessments pending review (Admin/Manager dashboard)
     * Shows assessments with status = 'completed'
     */
    public function pendingReview(Request $request)
    {
        $this->authorize('viewAny', Assessment::class);

        $query = Assessment::with(['company', 'createdBy', 'reviewedBy'])
            ->where('status', 'completed');

        // Filter by company (for Managers)
        if (auth()->user()->hasRole('Manager')) {
            $query->where('company_id', auth()->user()->company_id);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // Company filter (for Admin)
        if ($request->filled('company_id') && auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            $query->where('company_id', $request->company_id);
        }

        $pendingAssessments = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('review-approval.pending', compact('pendingAssessments'));
    }

    /**
     * Display assessments pending approval (Super Admin dashboard)
     * Shows assessments with status = 'reviewed'
     */
    public function pendingApproval(Request $request)
    {
        $this->authorize('viewAny', Assessment::class);

        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Only Super Admin can approve assessments');
        }

        $query = Assessment::with(['company', 'createdBy', 'reviewedBy'])
            ->where('status', 'reviewed');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $pendingAssessments = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('review-approval.pending-approval', compact('pendingAssessments'));
    }

    /**
     * Show review form for an assessment
     */
    public function showReviewForm(Assessment $assessment)
    {
        $this->authorize('review', $assessment);

        // Load necessary relationships
        $assessment->load([
            'company',
            'createdBy',
            'gamoScores.gamoObjective',
            'answers.question.gamoObjective'
        ]);

        // Calculate summary statistics
        $totalQuestions = $assessment->answers()->count();
        $answeredQuestions = $assessment->answers()->whereNotNull('answer_text')->count();
        $evidenceCount = $assessment->answers()->whereNotNull('evidence_file')->count();
        $avgMaturity = $assessment->answers()->whereNotNull('maturity_level')->avg('maturity_level');

        $statistics = [
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'completion_rate' => $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 1) : 0,
            'evidence_count' => $evidenceCount,
            'avg_maturity' => round($avgMaturity, 2),
        ];

        return view('review-approval.review', compact('assessment', 'statistics'));
    }

    /**
     * Submit review (approve or send back for revision)
     */
    public function submitReview(Request $request, Assessment $assessment)
    {
        $this->authorize('review', $assessment);

        $request->validate([
            'action' => 'required|in:approve,revise',
            'review_notes' => 'required|string|min:10',
        ]);

        try {
            DB::beginTransaction();

            if ($request->action === 'approve') {
                // Mark as reviewed
                $assessment->update([
                    'status' => 'reviewed',
                    'reviewed_by' => auth()->id(),
                ]);

                activity()
                    ->performedOn($assessment)
                    ->withProperties([
                        'action' => 'reviewed',
                        'notes' => $request->review_notes,
                    ])
                    ->log('Assessment reviewed and approved for final approval');

                DB::commit();

                return redirect()
                    ->route('review-approval.pending-review')
                    ->with('success', 'Assessment reviewed successfully. Waiting for Super Admin approval.');
            } else {
                // Send back for revision
                $assessment->update([
                    'status' => 'in_progress',
                ]);

                activity()
                    ->performedOn($assessment)
                    ->withProperties([
                        'action' => 'revision_requested',
                        'notes' => $request->review_notes,
                    ])
                    ->log('Assessment sent back for revision');

                DB::commit();

                return redirect()
                    ->route('review-approval.pending-review')
                    ->with('success', 'Assessment sent back for revision.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit review: ' . $e->getMessage());
        }
    }

    /**
     * Show approval form for an assessment
     */
    public function showApprovalForm(Assessment $assessment)
    {
        $this->authorize('approve', $assessment);

        // Load necessary relationships
        $assessment->load([
            'company',
            'createdBy',
            'reviewedBy',
            'gamoScores.gamoObjective',
        ]);

        return view('review-approval.approve', compact('assessment'));
    }

    /**
     * Submit final approval (Super Admin only)
     */
    public function submitApproval(Request $request, Assessment $assessment)
    {
        $this->authorize('approve', $assessment);

        $request->validate([
            'action' => 'required|in:approve,reject',
            'approval_notes' => 'required|string|min:10',
        ]);

        try {
            DB::beginTransaction();

            if ($request->action === 'approve') {
                // Final approval
                $assessment->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                ]);

                activity()
                    ->performedOn($assessment)
                    ->withProperties([
                        'action' => 'approved',
                        'notes' => $request->approval_notes,
                    ])
                    ->log('Assessment finally approved by Super Admin');

                DB::commit();

                return redirect()
                    ->route('review-approval.pending-approval')
                    ->with('success', 'Assessment approved successfully.');
            } else {
                // Reject and send back for revision
                $assessment->update([
                    'status' => 'in_progress',
                    'reviewed_by' => null, // Clear review
                ]);

                activity()
                    ->performedOn($assessment)
                    ->withProperties([
                        'action' => 'rejected',
                        'notes' => $request->approval_notes,
                    ])
                    ->log('Assessment rejected by Super Admin');

                DB::commit();

                return redirect()
                    ->route('review-approval.pending-approval')
                    ->with('success', 'Assessment rejected and sent back for revision.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit approval: ' . $e->getMessage());
        }
    }

    /**
     * Display review/approval history for an assessment
     */
    public function history(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $assessment->load(['company', 'createdBy', 'reviewedBy', 'approvedBy']);

        // Get activity logs related to review/approval
        $activities = activity()
            ->forSubject($assessment)
            ->whereIn('description', [
                'Assessment reviewed and approved for final approval',
                'Assessment sent back for revision',
                'Assessment finally approved by Super Admin',
                'Assessment rejected by Super Admin',
            ])
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('review-approval.history', compact('assessment', 'activities'));
    }
}
