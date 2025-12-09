<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GamoObjectiveResource;
use App\Models\GamoObjective;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GamoObjectiveController extends Controller
{
    /**
     * Display a listing of GAMO objectives
     * GET /api/gamo-objectives
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = GamoObjective::query();

        // Filter by category (EDM, APO, BAI, DSS, MEA)
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Only active
        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%')
                  ->orWhere('name_id', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $gamos = $query->orderBy('category')
            ->orderBy('objective_order')
            ->paginate($request->input('per_page', 30));

        return GamoObjectiveResource::collection($gamos);
    }

    /**
     * Store a newly created GAMO objective
     * POST /api/gamo-objectives
     */
    public function store(Request $request): JsonResponse
    {
        // Only Super Admin and Admin can create
        if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Only Admin+ can create GAMO objectives',
            ], 403);
        }

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('gamo_objectives', 'code')
            ],
            'name' => 'required|string|max:255',
            'name_id' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'description_id' => 'nullable|string|max:5000',
            'category' => [
                'required',
                Rule::in(['EDM', 'APO', 'BAI', 'DSS', 'MEA'])
            ],
            'objective_order' => 'nullable|integer|min:1|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();
        try {
            $gamo = GamoObjective::create($validated);

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($gamo)
                ->withProperties(['attributes' => $gamo->toArray()])
                ->log('GAMO Objective created');

            DB::commit();

            return response()->json([
                'message' => 'GAMO Objective created successfully',
                'data' => new GamoObjectiveResource($gamo),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create GAMO objective',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified GAMO objective
     * GET /api/gamo-objectives/{id}
     */
    public function show(GamoObjective $gamoObjective): JsonResponse
    {
        return response()->json([
            'data' => new GamoObjectiveResource($gamoObjective),
        ]);
    }

    /**
     * Update the specified GAMO objective
     * PUT /api/gamo-objectives/{id}
     */
    public function update(Request $request, GamoObjective $gamoObjective): JsonResponse
    {
        // Only Super Admin and Admin can update
        if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Only Admin+ can update GAMO objectives',
            ], 403);
        }

        $validated = $request->validate([
            'code' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('gamo_objectives', 'code')->ignore($gamoObjective->id)
            ],
            'name' => 'sometimes|string|max:255',
            'name_id' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'description_id' => 'nullable|string|max:5000',
            'category' => [
                'sometimes',
                Rule::in(['EDM', 'APO', 'BAI', 'DSS', 'MEA'])
            ],
            'objective_order' => 'sometimes|integer|min:1|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldValues = $gamoObjective->toArray();
            
            $gamoObjective->update($validated);

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($gamoObjective)
                ->withProperties([
                    'old' => $oldValues,
                    'attributes' => $gamoObjective->fresh()->toArray()
                ])
                ->log('GAMO Objective updated');

            DB::commit();

            return response()->json([
                'message' => 'GAMO Objective updated successfully',
                'data' => new GamoObjectiveResource($gamoObjective->fresh()),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update GAMO objective',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified GAMO objective
     * DELETE /api/gamo-objectives/{id}
     */
    public function destroy(GamoObjective $gamoObjective): JsonResponse
    {
        // Only Super Admin can delete
        if (!auth()->user()->hasRole('Super Admin')) {
            return response()->json([
                'message' => 'Unauthorized. Only Super Admin can delete GAMO objectives',
            ], 403);
        }

        // Check if used in assessments
        $usageCount = DB::table('assessment_gamo_selections')
            ->where('gamo_objective_id', $gamoObjective->id)
            ->count();

        if ($usageCount > 0) {
            return response()->json([
                'message' => "Cannot delete GAMO objective. Used in {$usageCount} assessment(s)",
            ], 400);
        }

        // Check if has questions
        $questionCount = DB::table('gamo_questions')
            ->where('gamo_objective_id', $gamoObjective->id)
            ->count();

        if ($questionCount > 0) {
            return response()->json([
                'message' => "Cannot delete GAMO objective. Has {$questionCount} question(s)",
            ], 400);
        }

        DB::beginTransaction();
        try {
            $gamoData = $gamoObjective->toArray();
            
            $gamoObjective->delete();

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $gamoData])
                ->log('GAMO Objective deleted');

            DB::commit();

            return response()->json([
                'message' => 'GAMO Objective deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete GAMO objective',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get GAMO objectives by category
     * GET /api/gamo-objectives/category/{category}
     */
    public function byCategory(string $category): AnonymousResourceCollection
    {
        $validCategories = ['EDM', 'APO', 'BAI', 'DSS', 'MEA'];
        
        if (!in_array(strtoupper($category), $validCategories)) {
            abort(400, 'Invalid category. Must be one of: ' . implode(', ', $validCategories));
        }

        $gamos = GamoObjective::where('category', strtoupper($category))
            ->where('is_active', true)
            ->orderBy('objective_order')
            ->get();

        return GamoObjectiveResource::collection($gamos);
    }
}
