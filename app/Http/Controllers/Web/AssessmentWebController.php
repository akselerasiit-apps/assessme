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
        $companies = Company::all();
        
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
        // Check if user has permission to create assessments (Super Admin, Admin, Manager can create)
        $allowedRoles = ['Super Admin', 'Admin', 'Manager', 'Assessor'];
        if (!auth()->user()->hasAnyRole($allowedRoles)) {
            abort(403, 'You do not have permission to create assessments.');
        }

        $companies = Company::all();
        $designFactors = DesignFactor::where('is_active', true)->orderBy('factor_order')->get();
        $gamoObjectives = GamoObjective::where('is_active', true)->orderBy('objective_order')->get();
        
        return view('assessments.create', compact('companies', 'designFactors', 'gamoObjectives'));
    }

    /**
     * Store a newly created assessment
     */
    public function store(Request $request)
    {
        // Check if user has permission to create assessments (Super Admin, Admin, Manager can create)
        $allowedRoles = ['Super Admin', 'Admin', 'Manager', 'Assessor'];
        if (!auth()->user()->hasAnyRole($allowedRoles)) {
            abort(403, 'You do not have permission to create assessments.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
            'assessment_type' => 'required|in:initial,periodic,specific',
            'scope_type' => 'required|in:full,tailored',
            'assessment_period_start' => 'required|date',
            'assessment_period_end' => 'required|date|after:assessment_period_start',
            'design_factors' => 'required|array|min:1',
            'design_factors.*' => 'exists:design_factors,id',
            'gamo_objectives' => 'required|array|min:1',
            'gamo_objectives.*' => 'exists:gamo_objectives,id',
        ], [
            'design_factors.required' => 'Please select at least one Design Factor.',
            'design_factors.min' => 'Please select at least one Design Factor.',
            'gamo_objectives.required' => 'Please select at least one GAMO Objective.',
            'gamo_objectives.min' => 'Please select at least one GAMO Objective.',
        ]);

        DB::beginTransaction();
        try {
            // Generate unique code
            $lastAssessment = Assessment::latest('id')->first();
            $nextNumber = $lastAssessment ? intval(substr($lastAssessment->code, 4)) + 1 : 1;
            $code = 'ASM-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            // Calculate initial progress percentage (10% for having selections)
            $initialProgress = 10;

            // Create assessment
            $assessment = Assessment::create([
                'code' => $code,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'company_id' => $validated['company_id'],
                'assessment_type' => $validated['assessment_type'],
                'scope_type' => $validated['scope_type'],
                'status' => 'draft',
                'assessment_period_start' => $validated['assessment_period_start'],
                'assessment_period_end' => $validated['assessment_period_end'],
                'created_by' => auth()->id(),
                'progress_percentage' => $initialProgress,
            ]);

            // Attach Design Factors
            if (!empty($validated['design_factors'])) {
                foreach ($validated['design_factors'] as $factorId) {
                    DB::table('assessment_design_factors')->insert([
                        'assessment_id' => $assessment->id,
                        'design_factor_id' => $factorId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Attach GAMO Objectives
            if (!empty($validated['gamo_objectives'])) {
                foreach ($validated['gamo_objectives'] as $gamoId) {
                    DB::table('assessment_gamo_selections')->insert([
                        'assessment_id' => $assessment->id,
                        'gamo_objective_id' => $gamoId,
                        'is_selected' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('assessments.show', $assessment)
                ->with('success', 'Assessment created successfully! You can now start answering questions.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create assessment: ' . $e->getMessage());
        }
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
        
        $companies = Company::all();
        $designFactors = DesignFactor::where('is_active', true)->orderBy('factor_order')->get();
        $gamoObjectives = GamoObjective::where('is_active', true)->orderBy('objective_order')->get();
        
        $assessment->load(['company', 'designFactors', 'gamoObjectives']);
        
        return view('assessments.edit', compact('assessment', 'companies', 'designFactors', 'gamoObjectives'));
    }

    /**
     * Update the specified assessment
     */
    public function update(Request $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
            'assessment_type' => 'required|in:initial,periodic,specific',
            'scope_type' => 'required|in:full,tailored',
            'assessment_period_start' => 'required|date',
            'assessment_period_end' => 'required|date|after:assessment_period_start',
            'design_factors' => 'nullable|array',
            'design_factors.*' => 'exists:design_factors,id',
            'gamo_objectives' => 'nullable|array',
            'gamo_objectives.*' => 'exists:gamo_objectives,id',
        ]);

        DB::beginTransaction();
        try {
            $assessment->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'company_id' => $validated['company_id'],
                'assessment_type' => $validated['assessment_type'],
                'scope_type' => $validated['scope_type'],
                'assessment_period_start' => $validated['assessment_period_start'],
                'assessment_period_end' => $validated['assessment_period_end'],
            ]);

            // Sync Design Factors
            DB::table('assessment_design_factors')
                ->where('assessment_id', $assessment->id)
                ->delete();
            
            if (!empty($validated['design_factors'])) {
                foreach ($validated['design_factors'] as $factorId) {
                    DB::table('assessment_design_factors')->insert([
                        'assessment_id' => $assessment->id,
                        'design_factor_id' => $factorId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Sync GAMO Objectives
            DB::table('assessment_gamo_selections')
                ->where('assessment_id', $assessment->id)
                ->delete();
            
            if (!empty($validated['gamo_objectives'])) {
                foreach ($validated['gamo_objectives'] as $gamoId) {
                    DB::table('assessment_gamo_selections')->insert([
                        'assessment_id' => $assessment->id,
                        'gamo_objective_id' => $gamoId,
                        'is_selected' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('assessments.show', $assessment)
                ->with('success', 'Assessment updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update assessment: ' . $e->getMessage());
        }
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
    public function myAssessments(Request $request)
    {
        $query = Assessment::with(['company', 'createdBy'])
            ->where(function($q) {
                $q->where('created_by', auth()->id())
                  ->orWhereHas('answers', function($query) {
                      $query->where('answered_by', auth()->id());
                  });
            })
            ->when($request->status, function($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->search, function($q) use ($request) {
                return $q->where(function($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->search . '%')
                          ->orWhere('code', 'like', '%' . $request->search . '%');
                });
            });

        $assessments = $query->latest()->paginate(15);
        
        $stats = [
            'total' => Assessment::where('created_by', auth()->id())->count(),
            'in_progress' => Assessment::where('created_by', auth()->id())->where('status', 'in_progress')->count(),
            'completed' => Assessment::where('created_by', auth()->id())->where('status', 'completed')->count(),
            'approved' => Assessment::where('created_by', auth()->id())->where('status', 'approved')->count(),
        ];
        
        return view('assessments.my-assessments', compact('assessments', 'stats'));
    }

    /**
     * Show answer questions interface
     */
    public function answer(Assessment $assessment)
    {
        $this->authorize('update', $assessment);
        
        $assessment->load(['gamoObjectives.questions' => function($query) {
            $query->where('is_active', true)->orderBy('question_order');
        }, 'answers']);
        
        return view('assessments.answer', compact('assessment'));
    }

    /**
     * Submit answer for a question
     */
    public function submitAnswer(Request $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);
        
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:gamo_questions,id',
            'answers.*.gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'answers.*.answer_text' => 'required|string',
            'answers.*.maturity_level' => 'required|integer|min:0|max:5',
            'answers.*.notes' => 'nullable|string',
            'answers.*.evidence' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['answers'] as $questionId => $answerData) {
                $evidencePath = null;
                
                // Handle file upload
                if ($request->hasFile("answers.{$questionId}.evidence")) {
                    $file = $request->file("answers.{$questionId}.evidence");
                    $evidencePath = $file->store('evidence', 'public');
                }

                // Update or create answer
                DB::table('assessment_answers')->updateOrInsert(
                    [
                        'assessment_id' => $assessment->id,
                        'question_id' => $questionId,
                    ],
                    [
                        'gamo_objective_id' => $answerData['gamo_objective_id'],
                        'answer_text' => $answerData['answer_text'],
                        'maturity_level' => $answerData['maturity_level'],
                        'notes' => $answerData['notes'] ?? null,
                        'evidence_file' => $evidencePath ?? DB::raw('evidence_file'),
                        'answered_by' => auth()->id(),
                        'answered_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            // Update assessment status based on action
            if ($request->action === 'submit') {
                $assessment->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
                $message = 'Answers submitted successfully!';
            } else {
                $assessment->update([
                    'status' => 'in_progress',
                ]);
                $message = 'Answers saved as draft!';
            }

            // Calculate progress
            $totalQuestions = DB::table('gamo_questions')
                ->whereIn('gamo_objective_id', $assessment->gamoObjectives->pluck('id'))
                ->where('is_active', true)
                ->count();
            
            $answeredQuestions = DB::table('assessment_answers')
                ->where('assessment_id', $assessment->id)
                ->count();
            
            $progress = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
            
            $assessment->update(['completion_percentage' => $progress]);

            DB::commit();

            return redirect()->route('assessments.show', $assessment)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save answers: ' . $e->getMessage());
        }
    }

    /**
     * Display team management for assessment
     */
    public function teamIndex(Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        $teamMembers = $assessment->teamMembers()
            ->with(['user.roles', 'assignedBy'])
            ->orderBy('assigned_at', 'desc')
            ->get();

        // Get users not already in team
        $assignedUserIds = $teamMembers->pluck('user_id')->toArray();
        $availableUsers = \App\Models\User::whereNotIn('id', $assignedUserIds)
            ->where('id', '!=', $assessment->created_by)
            ->with('roles')
            ->orderBy('name')
            ->get();

        return view('assessments.team', compact('assessment', 'teamMembers', 'availableUsers'));
    }

    /**
     * Add team member to assessment
     */
    public function teamStore(Request $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:lead,assessor,reviewer,observer',
            'responsibilities' => 'nullable|string',
            'can_edit' => 'boolean',
            'can_approve' => 'boolean',
        ]);

        // Check if user already assigned
        $exists = $assessment->teamMembers()
            ->where('user_id', $validated['user_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'User is already assigned to this assessment team.');
        }

        $assessment->teamMembers()->create([
            'user_id' => $validated['user_id'],
            'role' => $validated['role'],
            'responsibilities' => $validated['responsibilities'],
            'can_edit' => $request->has('can_edit'),
            'can_approve' => $request->has('can_approve'),
            'assigned_by' => auth()->id(),
        ]);

        activity()
            ->performedOn($assessment)
            ->withProperties(['user_id' => $validated['user_id'], 'role' => $validated['role']])
            ->log('Team member added');

        return redirect()->back()
            ->with('success', 'Team member added successfully.');
    }

    /**
     * Remove team member from assessment
     */
    public function teamDestroy(Assessment $assessment, $memberId)
    {
        $this->authorize('update', $assessment);

        $member = $assessment->teamMembers()->findOrFail($memberId);
        
        activity()
            ->performedOn($assessment)
            ->withProperties(['user_id' => $member->user_id, 'role' => $member->role])
            ->log('Team member removed');

        $member->delete();

        return redirect()->back()
            ->with('success', 'Team member removed successfully.');
    }

    /**
     * Display schedule management for assessment
     */
    public function scheduleShow(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        return view('assessments.schedule', compact('assessment'));
    }

    /**
     * Update assessment schedule
     */
    public function scheduleUpdate(Request $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        $validated = $request->validate([
            'assessment_period_start' => 'required|date',
            'assessment_period_end' => 'required|date|after:assessment_period_start',
            'schedule_notes' => 'nullable|string',
        ]);

        $assessment->update([
            'assessment_period_start' => $validated['assessment_period_start'],
            'assessment_period_end' => $validated['assessment_period_end'],
        ]);

        activity()
            ->performedOn($assessment)
            ->withProperties($validated)
            ->log('Assessment schedule updated');

        return redirect()->back()
            ->with('success', 'Assessment schedule updated successfully.');
    }
}

