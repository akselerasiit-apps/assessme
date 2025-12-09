<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BandingResource;
use App\Models\Assessment;
use App\Models\AssessmentBanding;
use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class BandingController extends Controller
{
    /**
     * Display a listing of bandings
     * GET /api/bandings
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = AssessmentBanding::with([
            'assessment',
            'gamoObjective',
            'requester',
            'reviewer',
        ]);

        // Filter by assessment
        if ($request->has('assessment_id')) {
            $query->where('assessment_id', $request->assessment_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter pending only
        if ($request->boolean('pending_only')) {
            $query->pending();
        }

        // Filter by GAMO
        if ($request->has('gamo_objective_id')) {
            $query->where('gamo_objective_id', $request->gamo_objective_id);
        }

        $bandings = $query->latest()
            ->paginate($request->input('per_page', 15));

        return BandingResource::collection($bandings);
    }

    /**
     * Store a newly created banding request
     * POST /api/assessments/{assessmentId}/bandings
     */
    public function store(Request $request, string $assessmentId): JsonResponse
    {
        $assessment = Assessment::findOrFail($assessmentId);

        $validated = $request->validate([
            'gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'banding_reason' => 'required|string|max:500',
            'banding_description' => 'nullable|string',
            'evidence_submitted' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Get current score for this GAMO
            $currentScore = $assessment->gamoScores()
                ->where('gamo_objective_id', $validated['gamo_objective_id'])
                ->first();

            // Calculate banding round
            $bandingRound = AssessmentBanding::where('assessment_id', $assessment->id)
                ->where('gamo_objective_id', $validated['gamo_objective_id'])
                ->max('banding_round') + 1;

            $banding = AssessmentBanding::create([
                'assessment_id' => $assessment->id,
                'gamo_objective_id' => $validated['gamo_objective_id'],
                'banding_round' => $bandingRound,
                'original_score' => $currentScore?->current_maturity_level ?? 0,
                'banding_reason' => $validated['banding_reason'],
                'banding_description' => $validated['banding_description'] ?? null,
                'evidence_submitted' => $validated['evidence_submitted'] ?? null,
                'requested_by' => $request->user()->id,
                'status' => 'PENDING',
                'requested_at' => now(),
            ]);

            // Log audit
            $this->logAudit($request, 'CREATE_BANDING', $banding);

            DB::commit();

            return response()->json([
                'message' => 'Banding request submitted successfully',
                'data' => new BandingResource($banding->load(['assessment', 'gamoObjective', 'requester'])),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to submit banding request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified banding
     * GET /api/bandings/{id}
     */
    public function show(string $id): JsonResponse
    {
        $banding = AssessmentBanding::with([
            'assessment',
            'gamoObjective',
            'requester',
            'reviewer',
        ])->findOrFail($id);

        return response()->json([
            'data' => new BandingResource($banding),
        ]);
    }

    /**
     * Update banding request (for banding handler to revise)
     * PUT /api/bandings/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $banding = AssessmentBanding::findOrFail($id);

        if ($banding->status !== 'PENDING') {
            return response()->json([
                'message' => 'Can only update pending banding requests',
            ], 400);
        }

        $validated = $request->validate([
            'banded_score' => 'sometimes|numeric|min:0|max:5',
            'banding_description' => 'nullable|string',
            'evidence_submitted' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $oldValues = $banding->toArray();

            $banding->update($validated);

            // Log audit
            $this->logAudit($request, 'UPDATE_BANDING', $banding, $oldValues);

            DB::commit();

            return response()->json([
                'message' => 'Banding updated successfully',
                'data' => new BandingResource($banding->fresh()->load(['assessment', 'gamoObjective'])),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update banding',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve banding request
     * POST /api/bandings/{id}/approve
     */
    public function approve(Request $request, string $id): JsonResponse
    {
        $banding = AssessmentBanding::findOrFail($id);

        if ($banding->status !== 'PENDING') {
            return response()->json([
                'message' => 'Can only approve pending banding requests',
            ], 400);
        }

        $validated = $request->validate([
            'banded_score' => 'required|numeric|min:0|max:5',
            'reviewer_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $banding->update([
                'banded_score' => $validated['banded_score'],
                'reviewer_notes' => $validated['reviewer_notes'] ?? null,
                'reviewed_by' => $request->user()->id,
                'status' => 'APPROVED',
                'reviewed_at' => now(),
            ]);

            // Update GAMO score with new banded score
            $banding->assessment->gamoScores()
                ->where('gamo_objective_id', $banding->gamo_objective_id)
                ->update([
                    'current_maturity_level' => $validated['banded_score'],
                ]);

            // Log audit
            $this->logAudit($request, 'APPROVE_BANDING', $banding);

            DB::commit();

            return response()->json([
                'message' => 'Banding approved successfully',
                'data' => new BandingResource($banding->fresh()->load(['assessment', 'gamoObjective', 'reviewer'])),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to approve banding',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject banding request
     * POST /api/bandings/{id}/reject
     */
    public function reject(Request $request, string $id): JsonResponse
    {
        $banding = AssessmentBanding::findOrFail($id);

        if ($banding->status !== 'PENDING') {
            return response()->json([
                'message' => 'Can only reject pending banding requests',
            ], 400);
        }

        $validated = $request->validate([
            'reviewer_notes' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $banding->update([
                'reviewer_notes' => $validated['reviewer_notes'],
                'reviewed_by' => $request->user()->id,
                'status' => 'REJECTED',
                'reviewed_at' => now(),
            ]);

            // Log audit
            $this->logAudit($request, 'REJECT_BANDING', $banding);

            DB::commit();

            return response()->json([
                'message' => 'Banding rejected',
                'data' => new BandingResource($banding->fresh()->load(['assessment', 'gamoObjective', 'reviewer'])),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to reject banding',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete banding request (only if pending and own request)
     * DELETE /api/bandings/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $banding = AssessmentBanding::findOrFail($id);

        if ($banding->status !== 'PENDING') {
            return response()->json([
                'message' => 'Can only delete pending banding requests',
            ], 400);
        }

        if ($banding->requested_by !== $request->user()->id) {
            return response()->json([
                'message' => 'You can only delete your own banding requests',
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Log audit
            $this->logAudit($request, 'DELETE_BANDING', $banding);

            $banding->delete();

            DB::commit();

            return response()->json([
                'message' => 'Banding request deleted successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete banding request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Log audit trail
     */
    private function logAudit(Request $request, string $action, AssessmentBanding $banding, ?array $oldValues = null): void
    {
        AuditLog::create([
            'user_id' => $request->user()?->id,
            'action' => $action,
            'entity_type' => AssessmentBanding::class,
            'entity_id' => $banding->id,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => json_encode($banding->toArray()),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
