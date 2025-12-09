<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies
     * GET /api/companies
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Company::class);

        $query = Company::query();

        // Filter by size
        if ($request->has('size')) {
            $query->where('size', $request->size);
        }

        // Filter by industry
        if ($request->has('industry')) {
            $query->where('industry', 'like', '%' . $request->industry . '%');
        }

        // Search
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        // Role-based filtering
        if (!auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            $query->where('id', auth()->user()->company_id);
        }

        $companies = $query->withCount(['assessments', 'users'])
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json($companies);
    }

    /**
     * Store a newly created company
     * POST /api/companies
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $this->authorize('create', Company::class);

        DB::beginTransaction();
        try {
            $company = Company::create($request->validated());

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($company)
                ->withProperties(['attributes' => $company->toArray()])
                ->log('Company created');

            DB::commit();

            return response()->json([
                'message' => 'Company created successfully',
                'data' => $company,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create company',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified company
     * GET /api/companies/{id}
     */
    public function show(Company $company): JsonResponse
    {
        $this->authorize('view', $company);

        $company->loadCount(['assessments', 'users']);
        $company->load(['users' => function ($query) {
            $query->select('id', 'name', 'email', 'position', 'company_id')
                ->where('status', 'active')
                ->limit(5);
        }]);

        return response()->json([
            'data' => $company,
        ]);
    }

    /**
     * Update the specified company
     * PUT /api/companies/{id}
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $this->authorize('update', $company);

        DB::beginTransaction();
        try {
            $oldValues = $company->toArray();
            
            $company->update($request->validated());

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->performedOn($company)
                ->withProperties([
                    'old' => $oldValues,
                    'attributes' => $company->fresh()->toArray()
                ])
                ->log('Company updated');

            DB::commit();

            return response()->json([
                'message' => 'Company updated successfully',
                'data' => $company->fresh(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update company',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified company
     * DELETE /api/companies/{id}
     */
    public function destroy(Company $company): JsonResponse
    {
        $this->authorize('delete', $company);

        // Check if company has assessments
        if ($company->assessments()->exists()) {
            return response()->json([
                'message' => 'Cannot delete company with existing assessments',
            ], 400);
        }

        // Check if company has users
        if ($company->users()->exists()) {
            return response()->json([
                'message' => 'Cannot delete company with existing users',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $companyData = $company->toArray();
            
            $company->delete();

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $companyData])
                ->log('Company deleted');

            DB::commit();

            return response()->json([
                'message' => 'Company deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete company',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
