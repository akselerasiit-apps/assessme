<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Recommendation;
use App\Models\GamoObjective;
use App\Models\GamoScore;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecommendationWebController extends Controller
{
    /**
     * Display a listing of recommendations for an assessment.
     */
    public function index(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        $recommendations = Recommendation::where('assessment_id', $assessment->id)
            ->with(['gamoObjective', 'responsiblePerson'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Statistics
        $stats = [
            'total' => $recommendations->total(),
            'open' => Recommendation::where('assessment_id', $assessment->id)
                ->where('status', 'open')->count(),
            'in_progress' => Recommendation::where('assessment_id', $assessment->id)
                ->where('status', 'in_progress')->count(),
            'completed' => Recommendation::where('assessment_id', $assessment->id)
                ->where('status', 'completed')->count(),
            'critical' => Recommendation::where('assessment_id', $assessment->id)
                ->where('priority', 'critical')->count(),
            'high' => Recommendation::where('assessment_id', $assessment->id)
                ->where('priority', 'high')->count(),
        ];

        return view('recommendations.index', compact('assessment', 'recommendations', 'stats'));
    }

    /**
     * Show the form for creating a new recommendation.
     */
    public function create(Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        // Get GAMO objectives for this assessment
        $gamoObjectives = $assessment->gamoObjectives()
            ->with('gamoScores')
            ->get();

        // Get potential owners (users from the same company or assessors)
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Manager', 'Assessor']);
        })->orderBy('name')->get();

        return view('recommendations.create', compact('assessment', 'gamoObjectives', 'users'));
    }

    /**
     * Store a newly created recommendation.
     */
    public function store(Request $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        $validated = $request->validate([
            'gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'priority' => 'required|in:low,medium,high,critical',
            'estimated_effort' => 'nullable|string|max:100',
            'responsible_person_id' => 'nullable|exists:users,id',
            'target_date' => 'nullable|date|after:today',
            'status' => 'nullable|in:open,in_progress,completed,closed',
        ]);

        $validated['assessment_id'] = $assessment->id;
        $validated['status'] = $validated['status'] ?? 'open';
        $validated['progress_percentage'] = 0;

        $recommendation = Recommendation::create($validated);

        activity()
            ->performedOn($recommendation)
            ->causedBy(Auth::user())
            ->withProperties(['assessment_id' => $assessment->id])
            ->log('Created recommendation: ' . $recommendation->title);

        return redirect()
            ->route('assessments.recommendations.index', $assessment)
            ->with('success', 'Recommendation created successfully.');
    }

    /**
     * Display the specified recommendation.
     */
    public function show(Assessment $assessment, Recommendation $recommendation)
    {
        $this->authorize('view', $assessment);

        $recommendation->load(['gamoObjective', 'responsiblePerson']);

        // Get related GAMO score for context
        $gamoScore = GamoScore::where('assessment_id', $assessment->id)
            ->where('gamo_objective_id', $recommendation->gamo_objective_id)
            ->first();

        // Get activity history
        $activities = activity()
            ->performedOn($recommendation)
            ->with('causer')
            ->latest()
            ->get();

        return view('recommendations.show', compact('assessment', 'recommendation', 'gamoScore', 'activities'));
    }

    /**
     * Show the form for editing the specified recommendation.
     */
    public function edit(Assessment $assessment, Recommendation $recommendation)
    {
        $this->authorize('update', $assessment);

        // Get GAMO objectives for this assessment
        $gamoObjectives = $assessment->gamoObjectives()
            ->with('gamoScores')
            ->get();

        // Get potential owners
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Manager', 'Assessor']);
        })->orderBy('name')->get();

        return view('recommendations.edit', compact('assessment', 'recommendation', 'gamoObjectives', 'users'));
    }

    /**
     * Update the specified recommendation.
     */
    public function update(Request $request, Assessment $assessment, Recommendation $recommendation)
    {
        $this->authorize('update', $assessment);

        $validated = $request->validate([
            'gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'priority' => 'required|in:low,medium,high,critical',
            'estimated_effort' => 'nullable|string|max:100',
            'responsible_person_id' => 'nullable|exists:users,id',
            'target_date' => 'nullable|date',
            'status' => 'required|in:open,in_progress,completed,closed',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
        ]);

        $oldValues = $recommendation->toArray();
        $recommendation->update($validated);

        activity()
            ->performedOn($recommendation)
            ->causedBy(Auth::user())
            ->withProperties([
                'old' => $oldValues,
                'new' => $recommendation->fresh()->toArray(),
            ])
            ->log('Updated recommendation: ' . $recommendation->title);

        return redirect()
            ->route('assessments.recommendations.show', [$assessment, $recommendation])
            ->with('success', 'Recommendation updated successfully.');
    }

    /**
     * Remove the specified recommendation.
     */
    public function destroy(Assessment $assessment, Recommendation $recommendation)
    {
        $this->authorize('update', $assessment);

        $title = $recommendation->title;

        activity()
            ->performedOn($recommendation)
            ->causedBy(Auth::user())
            ->withProperties($recommendation->toArray())
            ->log('Deleted recommendation: ' . $title);

        $recommendation->delete();

        return redirect()
            ->route('assessments.recommendations.index', $assessment)
            ->with('success', 'Recommendation deleted successfully.');
    }

    /**
     * Auto-generate recommendations based on gap analysis.
     */
    public function generate(Request $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        // Get GAMO scores with gaps
        $gamoScores = GamoScore::where('assessment_id', $assessment->id)
            ->with('gamoObjective')
            ->get();

        $generated = 0;

        DB::beginTransaction();
        try {
            foreach ($gamoScores as $score) {
                $gap = $score->target_maturity_level - $score->current_maturity_level;

                if ($gap > 0) {
                    // Generate recommendation based on gap size
                    $priority = $this->determinePriority($gap);
                    $effort = $this->estimateEffort($gap);

                    // Check if recommendation already exists
                    $exists = Recommendation::where('assessment_id', $assessment->id)
                        ->where('gamo_objective_id', $score->gamo_objective_id)
                        ->where('title', 'LIKE', 'Improve maturity level for%')
                        ->exists();

                    if (!$exists) {
                        Recommendation::create([
                            'assessment_id' => $assessment->id,
                            'gamo_objective_id' => $score->gamo_objective_id,
                            'title' => "Improve maturity level for {$score->gamoObjective->code}",
                            'description' => $this->generateDescription($score),
                            'priority' => $priority,
                            'estimated_effort' => $effort,
                            'status' => 'open',
                            'progress_percentage' => 0,
                        ]);

                        $generated++;
                    }
                }
            }

            DB::commit();

            activity()
                ->performedOn($assessment)
                ->causedBy(Auth::user())
                ->withProperties(['generated_count' => $generated])
                ->log("Auto-generated {$generated} recommendations");

            return redirect()
                ->route('assessments.recommendations.index', $assessment)
                ->with('success', "{$generated} recommendations generated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('assessments.recommendations.index', $assessment)
                ->with('error', 'Failed to generate recommendations: ' . $e->getMessage());
        }
    }

    /**
     * Determine priority based on gap size.
     */
    private function determinePriority(float $gap): string
    {
        if ($gap >= 3) {
            return 'critical';
        } elseif ($gap >= 2) {
            return 'high';
        } elseif ($gap >= 1) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Estimate effort based on gap size.
     */
    private function estimateEffort(float $gap): string
    {
        if ($gap >= 3) {
            return '6-12 months';
        } elseif ($gap >= 2) {
            return '3-6 months';
        } elseif ($gap >= 1) {
            return '1-3 months';
        }

        return '< 1 month';
    }

    /**
     * Generate description based on GAMO score.
     */
    private function generateDescription(GamoScore $score): string
    {
        $gamo = $score->gamoObjective;
        $gap = round($score->target_maturity_level - $score->current_maturity_level, 2);

        return "Current maturity level for {$gamo->code} - {$gamo->name} is {$score->current_maturity_level}, " .
            "with a target of {$score->target_maturity_level}. " .
            "Gap of {$gap} levels needs to be addressed through process improvements, " .
            "documentation enhancements, and capability building initiatives. " .
            "Focus on establishing defined processes, implementing controls, " .
            "and ensuring consistent execution across the organization.";
    }
}
