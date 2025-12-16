<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DesignFactor;
use Illuminate\Http\Request;

class DesignFactorWebController extends Controller
{
    /**
     * Display a listing of design factors
     */
    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 15);
        $designFactors = DesignFactor::orderBy('factor_order')->paginate($perPage);
        
        return view('master-data.design-factors.index', compact('designFactors'));
    }
    
    /**
     * Show the form for creating a new design factor
     */
    public function create()
    {
        return view('master-data.design-factors.create');
    }
    
    /**
     * Store a newly created design factor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:design_factors,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'factor_order' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);
        
        DesignFactor::create($validated);
        
        return redirect()->route('master-data.design-factors.index')
            ->with('success', 'Design Factor created successfully!');
    }
    
    /**
     * Show the form for editing the specified design factor
     */
    public function edit(DesignFactor $designFactor)
    {
        return view('master-data.design-factors.edit', compact('designFactor'));
    }
    
    /**
     * Update the specified design factor
     */
    public function update(Request $request, DesignFactor $designFactor)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:design_factors,code,' . $designFactor->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'factor_order' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);
        
        $designFactor->update($validated);
        
        return redirect()->route('master-data.design-factors.index')
            ->with('success', 'Design Factor updated successfully!');
    }
    
    /**
     * Remove the specified design factor
     */
    public function destroy(DesignFactor $designFactor)
    {
        // Check if design factor is used in assessments
        if ($designFactor->assessments()->count() > 0) {
            return redirect()->route('master-data.design-factors.index')
                ->with('error', 'Cannot delete Design Factor that is used in assessments!');
        }
        
        $designFactor->delete();
        
        return redirect()->route('master-data.design-factors.index')
            ->with('success', 'Design Factor deleted successfully!');
    }
    
    /**
     * Toggle active status
     */
    public function toggleActive(DesignFactor $designFactor)
    {
        $designFactor->update([
            'is_active' => !$designFactor->is_active
        ]);
        
        $status = $designFactor->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('master-data.design-factors.index')
            ->with('success', "Design Factor {$status} successfully!");
    }
}
