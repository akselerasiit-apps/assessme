<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssessmentResource;
use App\Models\Assessment;
use App\Models\AuditLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssessmentController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of assessments
     * GET /api/assessments
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Assessment::with(['company', 'creator', 'designFactors', 'gamoObjectives']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('assessment_period_start', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('assessment_period_end', '<=', $request->end_date);
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('assessment_code', 'like', '%' . $request->search . '%');
            });
        }

        $assessments = $query->latest()
            ->paginate($request->input('per_page', 15));

        return AssessmentResource::collection($assessments);
    }

    /**
     * Store a newly created assessment
     * POST /api/assessments
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Assessment::class);

        $validated = $request->validate([
            'code' => 'nullable|string|max:50|unique:assessments,code',
            'company_id' => 'required|exists:companies,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assessment_type' => 'nullable|in:initial,periodic,specific',
            'scope_type' => 'nullable|in:full,tailored',
            'assessment_period_start' => 'required|date',
            'assessment_period_end' => 'required|date|after:assessment_period_start',
        ]);

        DB::beginTransaction();
        try {
            // Generate assessment code if not provided
            $code = $validated['code'] ?? ('ASS-' . date('Ymd') . '-' . strtoupper(Str::random(6)));

            $assessment = Assessment::create([
                'code' => $code,
                'company_id' => $validated['company_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'assessment_type' => $validated['assessment_type'] ?? 'initial',
                'scope_type' => $validated['scope_type'] ?? 'tailored',
                'assessment_period_start' => $validated['assessment_period_start'],
                'assessment_period_end' => $validated['assessment_period_end'],
                'status' => 'draft',
                'created_by' => $request->user()->id,
                'progress_percentage' => 0,
            ]);

            // Log audit
            $this->logAudit($request, 'CREATE', $assessment);

            DB::commit();

            return response()->json([
                'message' => 'Assessment created successfully',
                'data' => new AssessmentResource($assessment->load(['company', 'creator'])),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create assessment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified assessment
     * GET /api/assessments/{id}
     */
    public function show(string $id): JsonResponse
    {
        $assessment = Assessment::with([
            'company',
            'creator',
            'reviewer',
            'approver',
            'designFactors',
            'gamoObjectives',
            'gamoScores',
            'gamoTargetLevels',
        ])->findOrFail($id);

        return response()->json([
            'data' => new AssessmentResource($assessment),
        ]);
    }

    /**
     * Update the specified assessment
     * PUT /api/assessments/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $assessment = Assessment::findOrFail($id);

        $this->authorize('update', $assessment);

        $validated = $request->validate([
            'code' => 'sometimes|string|max:50|unique:assessments,code,' . $id,
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'company_id' => 'sometimes|exists:companies,id',
            'assessment_type' => 'sometimes|in:initial,periodic,specific',
            'scope_type' => 'sometimes|in:full,tailored',
            'assessment_period_start' => 'sometimes|date',
            'assessment_period_end' => 'sometimes|date|after:assessment_period_start',
            'status' => 'sometimes|in:draft,in_progress,completed,reviewed,approved',
        ]);

        DB::beginTransaction();
        try {
            $oldValues = $assessment->toArray();

            $assessment->update($validated);

            // Log audit
            $this->logAudit($request, 'UPDATE', $assessment, $oldValues);

            DB::commit();

            return response()->json([
                'message' => 'Assessment updated successfully',
                'data' => new AssessmentResource($assessment->fresh()->load(['company', 'creator'])),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update assessment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified assessment
     * DELETE /api/assessments/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        $assessment = Assessment::findOrFail($id);

        $this->authorize('delete', $assessment);

        DB::beginTransaction();
        try {
            // Log audit before deletion
            $this->logAudit(request(), 'DELETE', $assessment);

            $assessment->delete();

            DB::commit();

            return response()->json([
                'message' => 'Assessment deleted successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete assessment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Select design factors for assessment
     * POST /api/assessments/{id}/design-factors
     */
    public function selectDesignFactors(Request $request, string $id): JsonResponse
    {
        $assessment = Assessment::findOrFail($id);

        $validated = $request->validate([
            'design_factors' => 'required|array',
            'design_factors.*.design_factor_id' => 'required|exists:design_factors,id',
            'design_factors.*.selected_value' => 'required|string|max:500',
            'design_factors.*.description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Sync design factors
            $syncData = [];
            foreach ($validated['design_factors'] as $df) {
                $syncData[$df['design_factor_id']] = [
                    'selected_value' => $df['selected_value'],
                    'description' => $df['description'] ?? null,
                ];
            }

            $assessment->designFactors()->sync($syncData);

            // Log audit
            $this->logAudit($request, 'UPDATE_DESIGN_FACTORS', $assessment);

            DB::commit();

            return response()->json([
                'message' => 'Design factors selected successfully',
                'data' => new AssessmentResource($assessment->fresh()->load('designFactors')),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to select design factors',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Select GAMO objectives for assessment
     * POST /api/assessments/{id}/gamo-selections
     */
    public function selectGamoObjectives(Request $request, string $id): JsonResponse
    {
        $assessment = Assessment::findOrFail($id);

        $validated = $request->validate([
            'gamo_objectives' => 'required|array',
            'gamo_objectives.*.gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'gamo_objectives.*.is_selected' => 'required|boolean',
            'gamo_objectives.*.selection_reason' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Sync GAMO objectives
            $syncData = [];
            foreach ($validated['gamo_objectives'] as $gamo) {
                $syncData[$gamo['gamo_objective_id']] = [
                    'is_selected' => $gamo['is_selected'],
                    'selection_reason' => $gamo['selection_reason'] ?? null,
                    'selected_at' => now(),
                ];
            }

            $assessment->gamoObjectives()->sync($syncData);

            // Create GAMO scores for selected objectives
            foreach ($validated['gamo_objectives'] as $gamo) {
                if ($gamo['is_selected']) {
                    $assessment->gamoScores()->updateOrCreate(
                        ['gamo_objective_id' => $gamo['gamo_objective_id']],
                        [
                            'current_maturity_level' => 0,
                            'target_maturity_level' => 3, // Default target
                            'status' => 'not_started',
                            'percentage_complete' => 0,
                        ]
                    );
                }
            }

            // Log audit
            $this->logAudit($request, 'UPDATE_GAMO_SELECTIONS', $assessment);

            DB::commit();

            return response()->json([
                'message' => 'GAMO objectives selected successfully',
                'data' => new AssessmentResource($assessment->fresh()->load(['gamoObjectives', 'gamoScores'])),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to select GAMO objectives',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit assessment for review
     * POST /api/assessments/{id}/submit
     */
    public function submit(Request $request, string $id): JsonResponse
    {
        $assessment = Assessment::findOrFail($id);

        if ($assessment->status !== 'IN_PROGRESS') {
            return response()->json([
                'message' => 'Only in-progress assessments can be submitted',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $assessment->update([
                'status' => 'UNDER_REVIEW',
                'is_locked' => true,
            ]);

            // Log audit
            $this->logAudit($request, 'SUBMIT', $assessment);

            DB::commit();

            return response()->json([
                'message' => 'Assessment submitted for review successfully',
                'data' => new AssessmentResource($assessment->fresh()),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to submit assessment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update assessment status
     * PATCH /api/assessments/{id}/status
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        try {
            $assessment = Assessment::findOrFail($id);
            
            $this->authorize('update', $assessment);
            
            $validated = $request->validate([
                'status' => ['required', 'in:draft,in_progress,completed,reviewed,approved'],
            ]);
            
            $oldStatus = $assessment->status;
            $assessment->status = $validated['status'];
            
            // Auto-set completion/approval dates
            if ($validated['status'] === 'completed' && !$assessment->completed_at) {
                $assessment->completed_at = now();
            }
            if ($validated['status'] === 'approved' && !$assessment->approved_at) {
                $assessment->approved_at = now();
                $assessment->approved_by = $request->user()->id;
            }
            
            $assessment->save();
            
            $this->logAudit($request, 'status_updated', $assessment, ['status' => $oldStatus]);
            
            return response()->json([
                'message' => 'Assessment status updated successfully',
                'data' => new AssessmentResource($assessment),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update assessment status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Log audit trail
     */
    private function logAudit(Request $request, string $action, Assessment $assessment, ?array $oldValues = null): void
    {
        AuditLog::create([
            'user_id' => $request->user()?->id,
            'action' => $action,
            'entity_type' => Assessment::class,
            'entity_id' => $assessment->id,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => json_encode($assessment->toArray()),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
