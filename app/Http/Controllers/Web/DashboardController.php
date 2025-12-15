<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment;
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
    
    public function users()
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        return view('admin.users');
    }
    
    public function roles()
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        return view('admin.roles');
    }
    
    public function auditLogs()
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        return view('admin.audit-logs');
    }
    
    public function adminSettings()
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        return view('admin.settings');
    }
}
