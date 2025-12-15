<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActionPlanWebController extends Controller
{
    /**
     * Display action plan dashboard for an assessment.
     */
    public function index(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        // Get all recommendations grouped by status
        $recommendations = Recommendation::where('assessment_id', $assessment->id)
            ->with(['gamoObjective', 'responsiblePerson'])
            ->orderBy('priority', 'desc')
            ->orderBy('target_date', 'asc')
            ->get();

        // Group by status
        $grouped = [
            'open' => $recommendations->where('status', 'open'),
            'in_progress' => $recommendations->where('status', 'in_progress'),
            'completed' => $recommendations->where('status', 'completed'),
            'closed' => $recommendations->where('status', 'closed'),
        ];

        // Overall statistics
        $stats = [
            'total' => $recommendations->count(),
            'completion_rate' => $recommendations->count() > 0 
                ? round(($recommendations->whereIn('status', ['completed', 'closed'])->count() / $recommendations->count()) * 100, 1)
                : 0,
            'overdue' => $recommendations->where('status', '!=', 'completed')
                ->where('status', '!=', 'closed')
                ->filter(function ($rec) {
                    return $rec->target_date && $rec->target_date < now();
                })->count(),
            'avg_progress' => round($recommendations->avg('progress_percentage'), 1),
        ];

        // Priority breakdown
        $priority_stats = [
            'critical' => $recommendations->where('priority', 'critical')->count(),
            'high' => $recommendations->where('priority', 'high')->count(),
            'medium' => $recommendations->where('priority', 'medium')->count(),
            'low' => $recommendations->where('priority', 'low')->count(),
        ];

        return view('action-plans.index', compact('assessment', 'grouped', 'stats', 'priority_stats'));
    }

    /**
     * Display timeline/roadmap view.
     */
    public function timeline(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        // Get recommendations ordered by target date
        $recommendations = Recommendation::where('assessment_id', $assessment->id)
            ->with(['gamoObjective', 'responsiblePerson'])
            ->whereNotNull('target_date')
            ->orderBy('target_date', 'asc')
            ->get();

        // Group by month/quarter
        $timeline = $recommendations->groupBy(function ($rec) {
            return $rec->target_date->format('Y-m');
        });

        // Get date range
        $startDate = $recommendations->min('target_date');
        $endDate = $recommendations->max('target_date');

        return view('action-plans.timeline', compact('assessment', 'timeline', 'startDate', 'endDate'));
    }

    /**
     * Display progress tracking view.
     */
    public function progress(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        // Get recommendations with progress
        $recommendations = Recommendation::where('assessment_id', $assessment->id)
            ->with(['gamoObjective', 'responsiblePerson'])
            ->orderBy('progress_percentage', 'asc')
            ->orderBy('priority', 'desc')
            ->get();

        // Group by responsible person
        $byOwner = $recommendations->groupBy('responsible_person_id');

        // Calculate owner statistics
        $ownerStats = [];
        foreach ($byOwner as $userId => $recs) {
            if ($userId) {
                $user = User::find($userId);
                $ownerStats[] = [
                    'user' => $user,
                    'total' => $recs->count(),
                    'completed' => $recs->whereIn('status', ['completed', 'closed'])->count(),
                    'in_progress' => $recs->where('status', 'in_progress')->count(),
                    'open' => $recs->where('status', 'open')->count(),
                    'avg_progress' => round($recs->avg('progress_percentage'), 1),
                ];
            }
        }

        // Overall progress by GAMO
        $byGamo = $recommendations->groupBy('gamo_objective_id')->map(function ($recs) {
            return [
                'gamo' => $recs->first()->gamoObjective,
                'total' => $recs->count(),
                'avg_progress' => round($recs->avg('progress_percentage'), 1),
                'completed' => $recs->whereIn('status', ['completed', 'closed'])->count(),
            ];
        });

        return view('action-plans.progress', compact('assessment', 'recommendations', 'ownerStats', 'byGamo'));
    }

    /**
     * Show form to assign recommendations.
     */
    public function assign(Request $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'recommendations' => 'required|array',
                'recommendations.*' => 'exists:recommendations,id',
                'responsible_person_id' => 'required|exists:users,id',
                'target_date' => 'nullable|date|after:today',
            ]);

            DB::beginTransaction();
            try {
                $updated = 0;
                foreach ($validated['recommendations'] as $recId) {
                    $recommendation = Recommendation::find($recId);
                    if ($recommendation && $recommendation->assessment_id == $assessment->id) {
                        $recommendation->update([
                            'responsible_person_id' => $validated['responsible_person_id'],
                            'target_date' => $validated['target_date'] ?? $recommendation->target_date,
                        ]);
                        $updated++;
                    }
                }

                DB::commit();

                activity()
                    ->performedOn($assessment)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'assigned_to' => $validated['responsible_person_id'],
                        'count' => $updated,
                    ])
                    ->log("Bulk assigned {$updated} recommendations");

                return redirect()
                    ->route('assessments.action-plans.index', $assessment)
                    ->with('success', "{$updated} recommendations assigned successfully.");
            } catch (\Exception $e) {
                DB::rollBack();

                return back()
                    ->with('error', 'Failed to assign recommendations: ' . $e->getMessage())
                    ->withInput();
            }
        }

        // GET request - show form
        $recommendations = Recommendation::where('assessment_id', $assessment->id)
            ->whereNull('responsible_person_id')
            ->orWhere('responsible_person_id', 0)
            ->with('gamoObjective')
            ->orderBy('priority', 'desc')
            ->get();

        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Manager', 'Assessor']);
        })->orderBy('name')->get();

        return view('action-plans.assign', compact('assessment', 'recommendations', 'users'));
    }

    /**
     * Update progress for a recommendation.
     */
    public function updateProgress(Request $request, Assessment $assessment, Recommendation $recommendation)
    {
        $this->authorize('update', $assessment);

        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'status' => 'nullable|in:open,in_progress,completed,closed',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldProgress = $recommendation->progress_percentage;
        $oldStatus = $recommendation->status;

        // Auto-update status based on progress
        if ($validated['progress_percentage'] == 100 && !isset($validated['status'])) {
            $validated['status'] = 'completed';
        } elseif ($validated['progress_percentage'] > 0 && $validated['progress_percentage'] < 100 && !isset($validated['status'])) {
            $validated['status'] = 'in_progress';
        }

        $recommendation->update($validated);

        activity()
            ->performedOn($recommendation)
            ->causedBy(Auth::user())
            ->withProperties([
                'old_progress' => $oldProgress,
                'new_progress' => $validated['progress_percentage'],
                'old_status' => $oldStatus,
                'new_status' => $recommendation->status,
                'notes' => $validated['notes'] ?? null,
            ])
            ->log('Updated progress for recommendation: ' . $recommendation->title);

        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully.',
            'data' => [
                'progress' => $recommendation->progress_percentage,
                'status' => $recommendation->status,
            ],
        ]);
    }
}
