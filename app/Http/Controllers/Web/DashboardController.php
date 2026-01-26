<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isGlobalAdmin = $user->hasAnyRole(['Super Admin', 'Admin']);
        $isViewer = $user->hasAnyRole(['Viewer', 'Asesi']);
        
        // Build base query based on role
        $baseQuery = Assessment::query();
        
        // Role-based filtering
        if ($user->hasAnyRole(['Viewer', 'Asesi', 'Manager'])) {
            // Viewer, Asesi and Manager: only see their company's assessments
            $baseQuery->where('company_id', $user->company_id);
        } elseif ($user->hasRole('Assessor')) {
            // Assessor: see their own assessments
            $baseQuery->where('created_by', $user->id);
        }
        // Super Admin and Admin: see all assessments (no filter)
        
        // Get comprehensive statistics
        $stats = [
            'total_assessments' => (clone $baseQuery)->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'archived' => (clone $baseQuery)->where('status', 'archived')->count(),
            'completion_rate' => $this->getCompletionRate($baseQuery),
            'average_maturity' => (clone $baseQuery)->whereNotNull('overall_maturity_level')->avg('overall_maturity_level') ?? 0,
        ];
        
        // Get assessment status counts for charts
        $statusCounts = [
            'draft' => $stats['draft'],
            'in_progress' => $stats['in_progress'],
            'completed' => $stats['completed'],
            'approved' => $stats['approved'],
        ];
        
        // Get maturity distribution (COBIT 2019: Level 2-5 only)
        $maturityDistribution = [];
        for ($i = 2; $i <= 5; $i++) {
            $maturityDistribution[$i] = (clone $baseQuery)->whereBetween('overall_maturity_level', [$i, $i + 0.99])->count();
        }
        
        // Get GAMO category distribution
        $gamoDistribution = $this->getGamoDistribution($baseQuery);
        
        // Get recent assessments with relations
        $recentAssessments = (clone $baseQuery)->with(['company', 'creator'])
            ->latest()
            ->limit(8)
            ->get();
        
        // Get assessments by company (only for global admins)
        $assessmentsByCompany = $isGlobalAdmin ? $this->getAssessmentsByCompany() : collect();
        
        // Get completion trend (last 7 days)
        $completionTrend = $this->getCompletionTrend($baseQuery);
        
        return view('dashboard.index', compact(
            'stats',
            'statusCounts',
            'maturityDistribution',
            'gamoDistribution',
            'recentAssessments',
            'assessmentsByCompany',
            'completionTrend',
            'isGlobalAdmin',
            'isViewer'
        ));
    }
    
    /**
     * Get overall completion rate
     */
    private function getCompletionRate($baseQuery): float
    {
        $assessmentIds = $baseQuery->pluck('id');
        
        if ($assessmentIds->isEmpty()) {
            return 0;
        }
        
        $totalQuestions = DB::table('assessment_answers')
            ->whereIn('assessment_id', $assessmentIds)
            ->count();
            
        $answeredQuestions = DB::table('assessment_answers')
            ->whereIn('assessment_id', $assessmentIds)
            ->whereNotNull('answered_at')
            ->count();
        
        return $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 1) : 0;
    }
    
    /**
     * Get GAMO distribution
     */
    private function getGamoDistribution($baseQuery): array
    {
        $assessmentIds = $baseQuery->pluck('id');
        
        if ($assessmentIds->isEmpty()) {
            return [
                'EDM' => 0,
                'APO' => 0,
                'BAI' => 0,
                'DSS' => 0,
                'MEA' => 0,
            ];
        }
        
        return [
            'EDM' => DB::table('assessment_gamo_selections')
                ->whereIn('assessment_id', $assessmentIds)
                ->join('gamo_objectives', 'assessment_gamo_selections.gamo_objective_id', '=', 'gamo_objectives.id')
                ->where('gamo_objectives.category', 'EDM')
                ->count(),
            'APO' => DB::table('assessment_gamo_selections')
                ->whereIn('assessment_id', $assessmentIds)
                ->join('gamo_objectives', 'assessment_gamo_selections.gamo_objective_id', '=', 'gamo_objectives.id')
                ->where('gamo_objectives.category', 'APO')
                ->count(),
            'BAI' => DB::table('assessment_gamo_selections')
                ->whereIn('assessment_id', $assessmentIds)
                ->join('gamo_objectives', 'assessment_gamo_selections.gamo_objective_id', '=', 'gamo_objectives.id')
                ->where('gamo_objectives.category', 'BAI')
                ->count(),
            'DSS' => DB::table('assessment_gamo_selections')
                ->whereIn('assessment_id', $assessmentIds)
                ->join('gamo_objectives', 'assessment_gamo_selections.gamo_objective_id', '=', 'gamo_objectives.id')
                ->where('gamo_objectives.category', 'DSS')
                ->count(),
            'MEA' => DB::table('assessment_gamo_selections')
                ->whereIn('assessment_id', $assessmentIds)
                ->join('gamo_objectives', 'assessment_gamo_selections.gamo_objective_id', '=', 'gamo_objectives.id')
                ->where('gamo_objectives.category', 'MEA')
                ->count(),
        ];
    }
    
    /**
     * Get assessments by company
     */
    private function getAssessmentsByCompany(): \Illuminate\Support\Collection
    {
        return Assessment::with('company')
            ->select('company_id', DB::raw('COUNT(*) as total'))
            ->groupBy('company_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }
    
    /**
     * Get completion trend for last 7 days
     */
    private function getCompletionTrend($baseQuery): array
    {
        $assessmentIds = $baseQuery->pluck('id');
        $trend = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Assessment::whereIn('id', $assessmentIds)
                ->whereDate('updated_at', $date->format('Y-m-d'))
                ->where('status', '=', 'completed')
                ->count();
            $trend[$date->format('M d')] = $count;
        }
        return $trend;
    }

    /**
     * Show Assessment Progress Dashboard
     */
    public function progressDashboard(Request $request)
    {
        // Get filter parameters
        $statusFilter = $request->get('status', null);
        $companyFilter = $request->get('company', null);
        $dateFrom = $request->get('date_from', null);
        $dateTo = $request->get('date_to', null);

        // Build base query
        $query = Assessment::with(['company', 'creator', 'team']);

        // Apply filters
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($companyFilter) {
            $query->where('company_id', $companyFilter);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Get filtered assessments
        $assessments = $query->latest()->paginate(15);

        // Get aggregated progress metrics
        $progressStats = [
            'total' => Assessment::count(),
            'by_status' => [
                'draft' => Assessment::where('status', 'draft')->count(),
                'in_progress' => Assessment::where('status', 'in_progress')->count(),
                'reviewed' => Assessment::where('status', 'reviewed')->count(),
                'completed' => Assessment::where('status', 'completed')->count(),
                'approved' => Assessment::where('status', 'approved')->count(),
            ],
            'avg_progress' => round(Assessment::avg('progress_percentage') ?? 0, 1),
            'avg_questions_answered' => round(DB::table('assessment_answers')->whereNotNull('answered_at')->count() / (Assessment::count() ?: 1), 0),
        ];

        // Get progress distribution for chart
        $progressBuckets = [
            '0-20%' => Assessment::whereBetween('progress_percentage', [0, 20])->count(),
            '21-40%' => Assessment::whereBetween('progress_percentage', [21, 40])->count(),
            '41-60%' => Assessment::whereBetween('progress_percentage', [41, 60])->count(),
            '61-80%' => Assessment::whereBetween('progress_percentage', [61, 80])->count(),
            '81-100%' => Assessment::whereBetween('progress_percentage', [81, 100])->count(),
        ];

        // Get team-wise metrics
        $teamMetrics = User::select('id', 'name', DB::raw('COUNT(DISTINCT assessments.id) as total_assignments'))
            ->leftJoin('assessments', 'users.id', '=', 'assessments.assigned_to')
            ->groupBy('id', 'name')
            ->having('total_assignments', '>', 0)
            ->limit(10)
            ->get();

        // Get top companies by progress
        $companiesProgress = Company::select('companies.id', 'companies.name', 
                DB::raw('COUNT(DISTINCT assessments.id) as total_count'),
                DB::raw('AVG(assessments.progress_percentage) as avg_progress'))
            ->leftJoin('assessments', 'companies.id', '=', 'assessments.company_id')
            ->groupBy('companies.id', 'companies.name')
            ->orderByDesc('avg_progress')
            ->limit(8)
            ->get();

        // Get available filters
        $companies = Company::orderBy('name')->get();
        $statuses = ['draft', 'in_progress', 'reviewed', 'completed', 'approved'];

        return view('dashboard.assessment-progress', compact(
            'assessments',
            'progressStats',
            'progressBuckets',
            'teamMetrics',
            'companiesProgress',
            'companies',
            'statuses',
            'statusFilter',
            'companyFilter',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Show Performance Dashboard with Maturity Heatmap
     */
    public function performanceDashboard(Request $request)
    {
        // Get filter parameters
        $companyFilter = $request->get('company', null);
        $periodFilter = $request->get('period', 'all'); // all, draft, completed, approved

        // Build base query
        $query = Assessment::with(['company', 'gamoSelections.gamoObjective']);

        // Apply status filter based on period
        if ($periodFilter !== 'all') {
            $query->where('status', $periodFilter);
        }

        // Apply company filter
        if ($companyFilter) {
            $query->where('company_id', $companyFilter);
        }

        $assessments = $query->get();

        // Build GAMO Maturity Heatmap
        $gamoObjectives = \App\Models\GamoObjective::with('category')->orderBy('category')->orderBy('code')->get();
        $companies = Company::orderBy('name')->get();

        // Initialize heatmap data structure
        $heatmapData = [];
        foreach ($companies as $company) {
            $heatmapData[$company->id] = [];
            foreach ($gamoObjectives as $gamo) {
                $heatmapData[$company->id][$gamo->id] = null;
            }
        }

        // Populate heatmap with actual maturity levels
        foreach ($assessments as $assessment) {
            $company = $assessment->company;
            if ($company) {
                foreach ($assessment->gamoSelections as $selection) {
                    if ($selection->gamoObjective) {
                        $gamo = $selection->gamoObjective;
                        $maturity = $selection->maturity_level ?? 0;
                        if (!isset($heatmapData[$company->id][$gamo->id]) || $maturity > $heatmapData[$company->id][$gamo->id]) {
                            $heatmapData[$company->id][$gamo->id] = $maturity;
                        }
                    }
                }
            }
        }

        // Calculate overall maturity by GAMO category
        $categoryMaturity = [];
        foreach (GamoObjective::groupBy('category')->pluck('category') as $category) {
            $categoryMaturity[$category] = [
                'total' => GamoObjective::where('category', $category)->count(),
                'count' => 0,
                'sum' => 0,
            ];
        }

        foreach ($assessments as $assessment) {
            foreach ($assessment->gamoSelections as $selection) {
                if ($selection->gamoObjective && $selection->maturity_level) {
                    $category = $selection->gamoObjective->category;
                    if (isset($categoryMaturity[$category])) {
                        $categoryMaturity[$category]['count']++;
                        $categoryMaturity[$category]['sum'] += $selection->maturity_level;
                    }
                }
            }
        }

        // Calculate averages
        foreach ($categoryMaturity as &$cat) {
            $cat['average'] = $cat['count'] > 0 ? round($cat['sum'] / $cat['count'], 2) : 0;
        }

        // Get company capability levels
        $companyCapability = [];
        foreach ($companies as $company) {
            $companyAssessments = $assessments->where('company_id', $company->id);
            $totalMaturity = 0;
            $totalItems = 0;

            foreach ($companyAssessments as $assessment) {
                foreach ($assessment->gamoSelections as $selection) {
                    if ($selection->maturity_level) {
                        $totalMaturity += $selection->maturity_level;
                        $totalItems++;
                    }
                }
            }

            $companyCapability[$company->id] = [
                'name' => $company->name,
                'count' => $companyAssessments->count(),
                'avg_maturity' => $totalItems > 0 ? round($totalMaturity / $totalItems, 2) : 0,
            ];
        }

        // Sort companies by average maturity
        usort($companyCapability, function($a, $b) {
            return $b['avg_maturity'] <=> $a['avg_maturity'];
        });

        // Get maturity trend (last 30 days by completion)
        $maturityTrend = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $assessments_on_date = Assessment::whereDate('completed_at', $date->format('Y-m-d'))
                ->whereNotNull('overall_maturity_level')
                ->avg('overall_maturity_level') ?? 0;
            $maturityTrend[$date->format('M d')] = round($assessments_on_date, 2);
        }

        // Get available companies for filter
        $allCompanies = Company::orderBy('name')->get();
        $periods = ['all' => 'All', 'draft' => 'Draft', 'completed' => 'Completed', 'approved' => 'Approved'];

        return view('dashboard.performance', compact(
            'heatmapData',
            'gamoObjectives',
            'companies',
            'categoryMaturity',
            'companyCapability',
            'maturityTrend',
            'allCompanies',
            'periods',
            'companyFilter',
            'periodFilter',
            'assessments'
        ));
    }

    public function profile()
    {
        // Redirect to new ProfileController
        return redirect()->route('profile.index');
    }
    
    public function settings()
    {
        // Redirect to new ProfileController settings
        return redirect()->route('profile.settings');
    }
    
    public function users(Request $request)
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        
        $query = User::with(['roles', 'company'])
            ->when($request->search, function($q) use ($request) {
                return $q->where(function($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->role, function($q) use ($request) {
                return $q->whereHas('roles', function($query) use ($request) {
                    $query->where('name', $request->role);
                });
            })
            ->when($request->status, function($q) use ($request) {
                return $q->where('is_active', $request->status === 'active' ? 1 : 0);
            });
        
        $users = $query->latest()->paginate(15);
        $roles = \Spatie\Permission\Models\Role::all();
        $companies = \App\Models\Company::all();
        
        return view('admin.users', compact('users', 'roles', 'companies'));
    }
    
    public function roles(Request $request)
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        
        $query = \Spatie\Permission\Models\Role::withCount('users')
            ->when($request->search, function($q) use ($request) {
                return $q->where('name', 'like', '%' . $request->search . '%');
            });
        
        $roles = $query->get();
        $permissions = \Spatie\Permission\Models\Permission::all()->groupBy(function($permission) {
            return explode('.', $permission->name)[0] ?? 'other';
        });
        
        return view('admin.roles', compact('roles', 'permissions'));
    }
    
    public function auditLogs(Request $request)
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        
        $query = \App\Models\AuditLog::with(['user'])
            ->when($request->search, function($q) use ($request) {
                return $q->where(function($query) use ($request) {
                    $query->where('action', 'like', '%' . $request->search . '%')
                          ->orWhere('module', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->user_id, function($q) use ($request) {
                return $q->where('user_id', $request->user_id);
            })
            ->when($request->module, function($q) use ($request) {
                return $q->where('module', $request->module);
            })
            ->when($request->date_from, function($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->date_to);
            });
        
        $logs = $query->latest()->paginate(50);
        $modules = \App\Models\AuditLog::select('module')->distinct()->pluck('module');
        $users = User::select('id', 'name')->get();
        
        return view('admin.audit-logs', compact('logs', 'modules', 'users'));
    }
    
    public function adminSettings()
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        
        // Get system settings (you can create a settings table or use config)
        $settings = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'mail_from' => config('mail.from.address'),
        ];
        
        return view('admin.settings', compact('settings'));
    }
}
