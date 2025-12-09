<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignFactorResource;
use App\Models\DesignFactor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DesignFactorController extends Controller
{
    /**
     * Display a listing of design factors
     * GET /api/design-factors
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = DesignFactor::query();

        // Only active by default
        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        // Search by code or name
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                    ->orWhere('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $designFactors = $query->orderBy('factor_order')
            ->get();

        return DesignFactorResource::collection($designFactors);
    }

    /**
     * Store a newly created design factor
     * POST /api/design-factors
     */
    public function store(Request $request): JsonResponse
    {
        // Only Super Admin and Admin can create
        if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Only Admin+ can create design factors',
            ], 403);
        }

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9_]+$/',
                Rule::unique('design_factors', 'code')
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'factor_order' => 'nullable|integer|min:1|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();
        try {
            $designFactor = DesignFactor::create($validated);

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($designFactor)
                ->withProperties(['attributes' => $designFactor->toArray()])
                ->log('Design Factor created');

            DB::commit();

            return response()->json([
                'message' => 'Design Factor created successfully',
                'data' => new DesignFactorResource($designFactor),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create design factor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified design factor
     * GET /api/design-factors/{id}
     */
    public function show(DesignFactor $designFactor): JsonResponse
    {
        return response()->json([
            'data' => new DesignFactorResource($designFactor),
        ]);
    }

    /**
     * Update the specified design factor
     * PUT /api/design-factors/{id}
     */
    public function update(Request $request, DesignFactor $designFactor): JsonResponse
    {
        // Only Super Admin and Admin can update
        if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Only Admin+ can update design factors',
            ], 403);
        }

        $validated = $request->validate([
            'code' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^[A-Z0-9_]+$/',
                Rule::unique('design_factors', 'code')->ignore($designFactor->id)
            ],
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:5000',
            'factor_order' => 'sometimes|integer|min:1|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldValues = $designFactor->toArray();
            
            $designFactor->update($validated);

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($designFactor)
                ->withProperties([
                    'old' => $oldValues,
                    'attributes' => $designFactor->fresh()->toArray()
                ])
                ->log('Design Factor updated');

            DB::commit();

            return response()->json([
                'message' => 'Design Factor updated successfully',
                'data' => new DesignFactorResource($designFactor->fresh()),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update design factor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified design factor
     * DELETE /api/design-factors/{id}
     */
    public function destroy(DesignFactor $designFactor): JsonResponse
    {
        // Only Super Admin can delete
        if (!auth()->user()->hasRole('Super Admin')) {
            return response()->json([
                'message' => 'Unauthorized. Only Super Admin can delete design factors',
            ], 403);
        }

        // Check if used in assessments
        $usageCount = DB::table('assessment_design_factors')
            ->where('design_factor_id', $designFactor->id)
            ->count();

        if ($usageCount > 0) {
            return response()->json([
                'message' => "Cannot delete design factor. Used in {$usageCount} assessment(s)",
            ], 400);
        }

        DB::beginTransaction();
        try {
            $designFactorData = $designFactor->toArray();
            
            $designFactor->delete();

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $designFactorData])
                ->log('Design Factor deleted');

            DB::commit();

            return response()->json([
                'message' => 'Design Factor deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete design factor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
