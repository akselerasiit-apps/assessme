<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyWebController extends Controller
{
    /**
     * Display a listing of companies
     */
    public function index(Request $request)
    {
        $query = Company::query();
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('industry', 'like', "%{$search}%");
            });
        }
        
        // Filter by industry
        if ($request->has('industry') && $request->industry != '') {
            $query->where('industry', $request->industry);
        }
        
        // Filter by size
        if ($request->has('size') && $request->size != '') {
            $query->where('size', $request->size);
        }
        
        $companies = $query->latest()->paginate(15);
        $industries = Company::distinct()->pluck('industry')->filter();
        
        return view('master-data.companies.index', compact('companies', 'industries'));
    }
    
    /**
     * Show the form for creating a new company
     */
    public function create()
    {
        return view('master-data.companies.create');
    }
    
    /**
     * Store a newly created company
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'industry' => 'nullable|string|max:100',
            'size' => 'required|in:startup,sme,enterprise',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
        ]);
        
        Company::create($validated);
        
        return redirect()->route('master-data.companies.index')
            ->with('success', 'Company created successfully!');
    }
    
    /**
     * Show the form for editing the specified company
     */
    public function edit(Company $company)
    {
        return view('master-data.companies.edit', compact('company'));
    }
    
    /**
     * Update the specified company
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'industry' => 'nullable|string|max:100',
            'size' => 'required|in:startup,sme,enterprise',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
        ]);
        
        $company->update($validated);
        
        return redirect()->route('master-data.companies.index')
            ->with('success', 'Company updated successfully!');
    }
    
    /**
     * Remove the specified company
     */
    public function destroy(Company $company)
    {
        // Check if company has assessments
        if ($company->assessments()->count() > 0) {
            return redirect()->route('master-data.companies.index')
                ->with('error', 'Cannot delete company with existing assessments!');
        }
        
        $company->delete();
        
        return redirect()->route('master-data.companies.index')
            ->with('success', 'Company deleted successfully!');
    }
}
