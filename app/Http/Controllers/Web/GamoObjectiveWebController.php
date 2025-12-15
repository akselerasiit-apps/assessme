<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GamoObjective;
use Illuminate\Http\Request;

class GamoObjectiveWebController extends Controller
{
    /**
     * Display a listing of GAMO objectives
     */
    public function index(Request $request)
    {
        $query = GamoObjective::query();
        
        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $gamoObjectives = $query->orderBy('category')
                               ->orderBy('objective_order')
                               ->paginate(15);
        
        $categories = ['EDM', 'APO', 'BAI', 'DSS', 'MEA'];
        
        return view('master-data.gamo-objectives.index', compact('gamoObjectives', 'categories'));
    }
    
    /**
     * Show the form for creating a new GAMO objective
     */
    public function create()
    {
        $categories = ['EDM', 'APO', 'BAI', 'DSS', 'MEA'];
        
        return view('master-data.gamo-objectives.create', compact('categories'));
    }
    
    /**
     * Store a newly created GAMO objective
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:gamo_objectives,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:EDM,APO,BAI,DSS,MEA',
            'objective_order' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);
        
        GamoObjective::create($validated);
        
        return redirect()->route('master-data.gamo-objectives.index')
            ->with('success', 'GAMO Objective created successfully!');
    }
    
    /**
     * Show the form for editing the specified GAMO objective
     */
    public function edit(GamoObjective $gamoObjective)
    {
        $categories = ['EDM', 'APO', 'BAI', 'DSS', 'MEA'];
        
        return view('master-data.gamo-objectives.edit', compact('gamoObjective', 'categories'));
    }
    
    /**
     * Update the specified GAMO objective
     */
    public function update(Request $request, GamoObjective $gamoObjective)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:gamo_objectives,code,' . $gamoObjective->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:EDM,APO,BAI,DSS,MEA',
            'objective_order' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);
        
        $gamoObjective->update($validated);
        
        return redirect()->route('master-data.gamo-objectives.index')
            ->with('success', 'GAMO Objective updated successfully!');
    }
    
    /**
     * Remove the specified GAMO objective
     */
    public function destroy(GamoObjective $gamoObjective)
    {
        // Check if GAMO objective is used in assessments
        if ($gamoObjective->assessments()->count() > 0) {
            return redirect()->route('master-data.gamo-objectives.index')
                ->with('error', 'Cannot delete GAMO Objective that is used in assessments!');
        }
        
        // Check if it has questions
        if ($gamoObjective->questions()->count() > 0) {
            return redirect()->route('master-data.gamo-objectives.index')
                ->with('error', 'Cannot delete GAMO Objective that has questions!');
        }
        
        $gamoObjective->delete();
        
        return redirect()->route('master-data.gamo-objectives.index')
            ->with('success', 'GAMO Objective deleted successfully!');
    }
    
    /**
     * Toggle active status
     */
    public function toggleActive(GamoObjective $gamoObjective)
    {
        $gamoObjective->update([
            'is_active' => !$gamoObjective->is_active
        ]);
        
        $status = $gamoObjective->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('master-data.gamo-objectives.index')
            ->with('success', "GAMO Objective {$status} successfully!");
    }
}
