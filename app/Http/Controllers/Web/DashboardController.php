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
        // Get comprehensive statistics
        $stats = [
            'total_assessments' => Assessment::count(),
            'draft' => Assessment::where('status', 'draft')->count(),
            'in_progress' => Assessment::where('status', 'in_progress')->count(),
            'reviewed' => Assessment::where('status', 'reviewed')->count(),
            'completed' => Assessment::where('status', 'completed')->count(),
            'approved' => Assessment::where('status', 'approved')->count(),
            'archived' => Assessment::where('status', 'archived')->count(),
            'completion_rate' => $this->getCompletionRate(),
            'average_maturity' => Assessment::whereNotNull('overall_maturity_level')->avg('overall_maturity_level') ?? 0,
        ];
        
        // Get assessment status counts for charts
        $statusCounts = [
            'draft' => $stats['draft'],
            'in_progress' => $stats['in_progress'],
            'reviewed' => $stats['reviewed'],
            'completed' => $stats['completed'],
            'approved' => $stats['approved'],
        ];
        
        // Get maturity distribution
        $maturityDistribution = [];
        for ($i = 0; $i <= 5; $i++) {
            $maturityDistribution[$i] = Assessment::whereBetween('overall_maturity_level', [$i, $i + 0.99])->count();
        }
        
        // Get GAMO category distribution
        $gamoDistribution = $this->getGamoDistribution();
        
        // Get recent assessments with relations
        $recentAssessments = Assessment::with(['company', 'creator'])
            ->latest()
            ->limit(8)
            ->get();
        
        // Get assessments by company
        $assessmentsByCompany = $this->getAssessmentsByCompany();
        
        // Get completion trend (last 7 days)
        $completionTrend = $this->getCompletionTrend();
        
        return view('dashboard.index', compact(
            'stats',
            'statusCounts',
            'maturityDistribution',
            'gamoDistribution',
            'recentAssessments',
            'assessmentsByCompany',
            'completionTrend'
        ));
    }
    
    /**
     * Get overall completion rate
     */
    private function getCompletionRate(): float
    {
        $totalQuestions = DB::table('assessment_answers')->count();
        $answeredQuestions = DB::table('assessment_answers')->whereNotNull('answered_at')->count();
        
        return $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 1) : 0;
    }
    
    /**
     * Get GAMO distribution
     */
    private function getGamoDistribution(): array
    {
        return [
            'EDM' => DB::table('assessment_gamo_selections')
                ->join('gamo_objectives', 'assessment_gamo_selections.gamo_objective_id', '=', 'gamo_objectives.id')
                ->where('gamo_objectives.category', 'EDM')
                ->count(),
            'APO' => DB::table('assessment_gamo_selections')
                ->join('gamo_objectives', 'assessment_gamo_selections.gamo_objective_id', '=', 'gamo_objectives.id')
                ->where('gamo_objectives.category', 'APO')
                ->count(),
            'BAI' => DB::table('assessment_gamo_selections')
                ->join('gamo_objectives', 'assessment_gamo_selections.gamo_objective_id', '=', 'gamo_objectives.id')
                ->where('gamo_objectives.category', 'BAI')
                ->count(),
            'DSS' => DB::table('assessment_gamo_selections')
                ->join('gamo_objectives', 'assessment_gamo_selections.gamo_objective_id', '=', 'gamo_objectives.id')
                ->where('gamo_objectives.category', 'DSS')
                ->count(),
            'MEA' => DB::table('assessment_gamo_selections')
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
    private function getCompletionTrend(): array
    {
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Assessment::whereDate('updated_at', $date->format('Y-m-d'))
                ->where('status', '=', 'completed')
                ->count();
            $trend[$date->format('M d')] = $count;
        }
        return $trend;
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
