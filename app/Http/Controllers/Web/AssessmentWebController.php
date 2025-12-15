<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Company;
use App\Models\DesignFactor;
use App\Models\GamoObjective;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentWebController extends Controller
{
    /**
     * Display a listing of assessments
     */
    public function index(Request $request)
    {
        $query = Assessment::with(['company', 'createdBy'])
            ->when($request->status, function($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->search, function($q) use ($request) {
                return $q->where(function($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->search . '%')
                          ->orWhere('code', 'like', '%' . $request->search . '%')
                          ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->company_id, function($q) use ($request) {
                return $q->where('company_id', $request->company_id);
            });

        // Apply authorization filters
        if (!auth()->user()->hasRole('Super Admin')) {
            if (auth()->user()->hasRole('Admin')) {
                // Admin can see all assessments
            } elseif (auth()->user()->hasRole('Manager')) {
                // Manager can see own company assessments
                $query->where('company_id', auth()->user()->company_id);
            } else {
                // Assessor/Viewer can see own or participated assessments
                $query->where(function($q) {
                    $q->where('created_by', auth()->id())
                      ->orWhereHas('answers', function($query) {
                          $query->where('answered_by', auth()->id());
                      });
                });
            }
        }

        $assessments = $query->latest()->paginate(15);
        $companies = Company::where('is_active', true)->get();
        
        $statusCounts = [
            'all' => Assessment::count(),
            'draft' => Assessment::where('status', 'draft')->count(),
            'in_progress' => Assessment::where('status', 'in_progress')->count(),
            'completed' => Assessment::where('status', 'completed')->count(),
            'reviewed' => Assessment::where('status', 'reviewed')->count(),
            'approved' => Assessment::where('status', 'approved')->count(),
        ];

        return view('assessments.index', compact('assessments', 'companies', 'statusCounts'));
    }

    /**
     * Show the form for creating a new assessment
     */
    public function create()
    {
        $companies = Company::where('is_active', true)->get();
        $designFactors = DesignFactor::where('is_active', true)->orderBy('factor_order')->get();
        $gamoObjectives = GamoObjective::where('is_active', true)->orderBy('objective_order')->get();
        
        return view('assessments.create', compact('companies', 'designFactors', 'gamoObjectives'));
    }

    /**
     * Store a newly created assessment
     */
    public function store(Request $request)
    {
        // Will implement after create view is ready
    }

    /**
     * Display the specified assessment
     */
    public function show(Assessment $assessment)
    {
        $this->authorize('view', $assessment);
        
        $assessment->load([
            'company',
            'createdBy',
            'reviewedBy',
            'approvedBy',
            'designFactors',
            'gamoObjectives',
            'answers.question',
            'gamoScores.gamoObjective'
        ]);
        
        return view('assessments.show', compact('assessment'));
    }

    /**
     * Show the form for editing the specified assessment
     */
    public function edit(Assessment $assessment)
    {
        $this->authorize('update', $assessment);
        
        $companies = Company::where('is_active', true)->get();
        
        return view('assessments.edit', compact('assessment', 'companies'));
    }

    /**
     * Update the specified assessment
     */
    public function update(Request $request, Assessment $assessment)
    {
        // Will implement after edit view is ready
    }

    /**
     * Remove the specified assessment
     */
    public function destroy(Assessment $assessment)
    {
        $this->authorize('delete', $assessment);
        
        DB::beginTransaction();
        try {
            $assessment->delete();
            DB::commit();
            
            return redirect()->route('assessments.index')
                ->with('success', 'Assessment deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete assessment: ' . $e->getMessage());
        }
    }

    /**
     * Show my assessments
     */
    public function myAssessments()
    {
        $assessments = Assessment::with(['company', 'createdBy'])
            ->where(function($q) {
                $q->where('created_by', auth()->id())
                  ->orWhereHas('answers', function($query) {
                      $query->where('answered_by', auth()->id());
                  });
            })
            ->latest()
            ->paginate(15);
        
        return view('assessments.my', compact('assessments'));
    }

    /**
     * Show answer questions interface
     */
    public function answer(Assessment $assessment)
    {
        $this->authorize('answer', $assessment);
        
        // Will implement Q&A interface
        return view('assessments.answer', compact('assessment'));
    }

    /**
     * Submit answer for a question
     */
    public function submitAnswer(Request $request, Assessment $assessment)
    {
        // Will implement after answer view is ready
    }
}

