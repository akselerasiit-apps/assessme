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
        // Get statistics
        $stats = [
            'total_assessments' => Assessment::count(),
            'draft' => Assessment::where('status', 'draft')->count(),
            'in_progress' => Assessment::where('status', 'in_progress')->count(),
            'under_review' => Assessment::where('status', 'under_review')->count(),
            'completed' => Assessment::where('status', 'completed')->count(),
            'approved' => Assessment::where('status', 'approved')->count(),
            'average_maturity' => Assessment::whereNotNull('maturity_level')->avg('maturity_level') ?? 0,
        ];
        
        // Get maturity distribution
        $maturityDistribution = [];
        for ($i = 0; $i <= 5; $i++) {
            $maturityDistribution[$i] = Assessment::whereBetween('maturity_level', [$i, $i + 0.99])->count();
        }
        
        // Get recent assessments
        $recentAssessments = Assessment::with(['company', 'creator'])
            ->latest()
            ->limit(10)
            ->get();
        
        return view('dashboard.index', compact('stats', 'maturityDistribution', 'recentAssessments'));
    }
    
    public function profile()
    {
        return view('dashboard.profile');
    }
    
    public function settings()
    {
        return view('dashboard.settings');
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
        $companies = \App\Models\Company::where('is_active', true)->get();
        
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
