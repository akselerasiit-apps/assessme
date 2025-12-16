<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\AssessmentWebController;
use App\Http\Controllers\Web\ReportWebController;
use App\Http\Controllers\Web\QuestionWebController;
use App\Http\Controllers\Web\ReviewApprovalController;
use App\Http\Controllers\Web\BandingController;
use App\Http\Controllers\Web\RecommendationWebController;
use App\Http\Controllers\Web\ActionPlanWebController;
use App\Http\Controllers\Web\CapabilityAssessmentController;
use App\Http\Controllers\Web\ProfileController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
    
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile & Settings
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
        Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::get('/activity', [ProfileController::class, 'activity'])->name('activity');
        Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
        Route::put('/update-settings', [ProfileController::class, 'updateSettings'])->name('update-settings');
    });
    
    // Legacy routes - redirect to new profile routes
    Route::get('/profile-old', [DashboardController::class, 'profile']);
    Route::get('/settings-old', [DashboardController::class, 'settings']);
    
    // Assessments
    Route::prefix('assessments')->name('assessments.')->group(function () {
        Route::get('/', [AssessmentWebController::class, 'index'])->name('index');
        Route::get('/create', [AssessmentWebController::class, 'create'])->name('create');
        Route::post('/', [AssessmentWebController::class, 'store'])->name('store');
        Route::get('/my', [AssessmentWebController::class, 'myAssessments'])->name('my');
        Route::get('/{assessment}', [AssessmentWebController::class, 'show'])->name('show');
        Route::get('/{assessment}/edit', [AssessmentWebController::class, 'edit'])->name('edit');
        Route::put('/{assessment}', [AssessmentWebController::class, 'update'])->name('update');
        Route::delete('/{assessment}', [AssessmentWebController::class, 'destroy'])->name('destroy');
        
        // Progress tracking
        Route::get('/{assessment}/progress', [\App\Http\Controllers\Web\AssessmentProgressController::class, 'show'])->name('progress');
        
        // Answer questions
        Route::get('/{assessment}/answer', [AssessmentWebController::class, 'answer'])->name('answer');
        Route::post('/{assessment}/submit-answer', [AssessmentWebController::class, 'submitAnswer'])->name('submit-answer');
        
        // Evidence Management (nested under assessments)
        Route::prefix('/{assessment}/evidence')->name('evidence.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\EvidenceWebController::class, 'index'])->name('index');
            Route::get('/upload', [\App\Http\Controllers\Web\EvidenceWebController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Web\EvidenceWebController::class, 'store'])->name('store');
            Route::get('/{answer}/download', [\App\Http\Controllers\Web\EvidenceWebController::class, 'download'])->name('download');
            Route::delete('/{answer}', [\App\Http\Controllers\Web\EvidenceWebController::class, 'destroy'])->name('destroy');
        });
        
        // Scoring & Maturity (nested under assessments)
        Route::prefix('/{assessment}/scoring')->name('scoring.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\ScoringWebController::class, 'index'])->name('index');
            Route::get('/{score}', [\App\Http\Controllers\Web\ScoringWebController::class, 'show'])->name('show');
            Route::post('/calculate', [\App\Http\Controllers\Web\ScoringWebController::class, 'calculate'])->name('calculate');
        });
        
        // Banding/Appeal Process (nested under assessments)
        Route::prefix('/{assessment}/banding')->name('banding.')->group(function () {
            Route::get('/', [BandingController::class, 'index'])->name('index');
            Route::get('/create', [BandingController::class, 'create'])->name('create');
            Route::post('/', [BandingController::class, 'store'])->name('store');
            Route::get('/{banding}', [BandingController::class, 'show'])->name('show');
            Route::post('/{banding}/submit', [BandingController::class, 'submit'])->name('submit');
            Route::post('/{banding}/process', [BandingController::class, 'processApproval'])
                ->name('process-approval')
                ->middleware('role:Super Admin|Admin');
            Route::delete('/{banding}', [BandingController::class, 'destroy'])->name('destroy');
        });
        
        // Recommendations (nested under assessments)
        Route::prefix('/{assessment}/recommendations')->name('recommendations.')->group(function () {
            Route::get('/', [RecommendationWebController::class, 'index'])->name('index');
            Route::get('/create', [RecommendationWebController::class, 'create'])->name('create');
            Route::post('/', [RecommendationWebController::class, 'store'])->name('store');
            Route::get('/generate', [RecommendationWebController::class, 'generate'])->name('generate');
            Route::get('/{recommendation}', [RecommendationWebController::class, 'show'])->name('show');
            Route::get('/{recommendation}/edit', [RecommendationWebController::class, 'edit'])->name('edit');
            Route::put('/{recommendation}', [RecommendationWebController::class, 'update'])->name('update');
            Route::delete('/{recommendation}', [RecommendationWebController::class, 'destroy'])->name('destroy');
        });
        
        // Action Plans (nested under assessments)
        Route::prefix('/{assessment}/action-plans')->name('action-plans.')->group(function () {
            Route::get('/', [ActionPlanWebController::class, 'index'])->name('index');
            Route::get('/timeline', [ActionPlanWebController::class, 'timeline'])->name('timeline');
            Route::get('/progress', [ActionPlanWebController::class, 'progress'])->name('progress');
            Route::match(['get', 'post'], '/assign', [ActionPlanWebController::class, 'assign'])->name('assign');
            Route::post('/{recommendation}/update-progress', [ActionPlanWebController::class, 'updateProgress'])->name('update-progress');
        });
        
        // Team Management (nested under assessments)
        Route::prefix('/{assessment}/team')->name('team.')->group(function () {
            Route::get('/', [AssessmentWebController::class, 'teamIndex'])->name('index');
            Route::post('/', [AssessmentWebController::class, 'teamStore'])->name('store');
            Route::delete('/{member}', [AssessmentWebController::class, 'teamDestroy'])->name('destroy');
        });
        
        // Schedule Management (nested under assessments)
        Route::prefix('/{assessment}/schedule')->name('schedule.')->group(function () {
            Route::get('/', [AssessmentWebController::class, 'scheduleShow'])->name('show');
            Route::put('/', [AssessmentWebController::class, 'scheduleUpdate'])->name('update');
        });
        
        // Capability Assessment (nested under assessments)
        Route::prefix('/{assessment}/capability')->name('capability.')->group(function () {
            Route::get('/', [CapabilityAssessmentController::class, 'index'])->name('index');
            Route::get('/{gamo}', [CapabilityAssessmentController::class, 'assessment'])->name('assessment');
            Route::post('/update-score', [CapabilityAssessmentController::class, 'updateCapabilityScore'])->name('update-score');
            Route::get('/{gamo}/level/{level}/summary', [CapabilityAssessmentController::class, 'levelSummary'])->name('level-summary');
        });
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportWebController::class, 'index'])->name('index');
        Route::get('/{assessment}/maturity', [ReportWebController::class, 'maturity'])->name('maturity');
        Route::get('/{assessment}/gap-analysis', [ReportWebController::class, 'gapAnalysis'])->name('gap-analysis');
        Route::get('/{assessment}/summary', [ReportWebController::class, 'summary'])->name('summary');
    });
    
    // Review & Approval Routes
    Route::prefix('review-approval')->name('review-approval.')->group(function () {
        // Pending Review (Admin/Manager)
        Route::get('/pending-review', [ReviewApprovalController::class, 'pendingReview'])
            ->name('pending-review')
            ->middleware('role:Admin|Manager');
        
        // Pending Approval (Super Admin only)
        Route::get('/pending-approval', [ReviewApprovalController::class, 'pendingApproval'])
            ->name('pending-approval')
            ->middleware('role:Super Admin');
        
        // Review Form & Submit
        Route::get('/{assessment}/review', [ReviewApprovalController::class, 'showReviewForm'])
            ->name('review')
            ->middleware('role:Admin|Manager');
        Route::post('/{assessment}/review', [ReviewApprovalController::class, 'submitReview'])
            ->name('submit-review')
            ->middleware('role:Admin|Manager');
        
        // Approval Form & Submit (Super Admin only)
        Route::get('/{assessment}/approve', [ReviewApprovalController::class, 'showApprovalForm'])
            ->name('approve')
            ->middleware('role:Super Admin');
        Route::post('/{assessment}/approve', [ReviewApprovalController::class, 'submitApproval'])
            ->name('submit-approval')
            ->middleware('role:Super Admin');
        
        // History
        Route::get('/{assessment}/history', [ReviewApprovalController::class, 'history'])
            ->name('history');
    });
    
    // Banding/Appeal Approval Routes (Admin/Super Admin)
    Route::prefix('banding')->name('banding.')->group(function () {
        // Global pending approvals dashboard
        Route::get('/pending-approval', [BandingController::class, 'pendingApproval'])
            ->name('pending-approval')
            ->middleware('role:Super Admin|Admin');
    });
    
    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [DashboardController::class, 'users'])->name('users');
        Route::post('/users', [\App\Http\Controllers\Web\UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [\App\Http\Controllers\Web\UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Web\UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/toggle', [\App\Http\Controllers\Web\UserManagementController::class, 'toggleStatus'])->name('users.toggle');
        
        Route::get('/roles', [DashboardController::class, 'roles'])->name('roles');
        Route::post('/roles', [\App\Http\Controllers\Web\RoleManagementController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [\App\Http\Controllers\Web\RoleManagementController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [\App\Http\Controllers\Web\RoleManagementController::class, 'destroy'])->name('roles.destroy');
        
        Route::get('/audit-logs', [DashboardController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/settings', [DashboardController::class, 'adminSettings'])->name('settings');
    });
    
    // Master Data Routes (Super Admin only)
    Route::prefix('master-data')->name('master-data.')->middleware('role:Super Admin')->group(function () {
        // Companies Management
        Route::prefix('companies')->name('companies.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\CompanyWebController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Web\CompanyWebController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Web\CompanyWebController::class, 'store'])->name('store');
            Route::get('/{company}/edit', [\App\Http\Controllers\Web\CompanyWebController::class, 'edit'])->name('edit');
            Route::put('/{company}', [\App\Http\Controllers\Web\CompanyWebController::class, 'update'])->name('update');
            Route::delete('/{company}', [\App\Http\Controllers\Web\CompanyWebController::class, 'destroy'])->name('destroy');
        });
        
        // Design Factors Management
        Route::prefix('design-factors')->name('design-factors.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\DesignFactorWebController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Web\DesignFactorWebController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Web\DesignFactorWebController::class, 'store'])->name('store');
            Route::get('/{designFactor}/edit', [\App\Http\Controllers\Web\DesignFactorWebController::class, 'edit'])->name('edit');
            Route::put('/{designFactor}', [\App\Http\Controllers\Web\DesignFactorWebController::class, 'update'])->name('update');
            Route::delete('/{designFactor}', [\App\Http\Controllers\Web\DesignFactorWebController::class, 'destroy'])->name('destroy');
            Route::patch('/{designFactor}/toggle', [\App\Http\Controllers\Web\DesignFactorWebController::class, 'toggleActive'])->name('toggle');
        });
        
        // GAMO Objectives Management
        Route::prefix('gamo-objectives')->name('gamo-objectives.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\GamoObjectiveWebController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Web\GamoObjectiveWebController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Web\GamoObjectiveWebController::class, 'store'])->name('store');
            Route::get('/{gamoObjective}/edit', [\App\Http\Controllers\Web\GamoObjectiveWebController::class, 'edit'])->name('edit');
            Route::put('/{gamoObjective}', [\App\Http\Controllers\Web\GamoObjectiveWebController::class, 'update'])->name('update');
            Route::delete('/{gamoObjective}', [\App\Http\Controllers\Web\GamoObjectiveWebController::class, 'destroy'])->name('destroy');
            Route::patch('/{gamoObjective}/toggle', [\App\Http\Controllers\Web\GamoObjectiveWebController::class, 'toggleActive'])->name('toggle');
        });
        
        // Question Management
        Route::prefix('questions')->name('questions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\QuestionWebController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Web\QuestionWebController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Web\QuestionWebController::class, 'store'])->name('store');
            Route::get('/{question}', [\App\Http\Controllers\Web\QuestionWebController::class, 'show'])->name('show');
            Route::get('/{question}/edit', [\App\Http\Controllers\Web\QuestionWebController::class, 'edit'])->name('edit');
            Route::put('/{question}', [\App\Http\Controllers\Web\QuestionWebController::class, 'update'])->name('update');
            Route::delete('/{question}', [\App\Http\Controllers\Web\QuestionWebController::class, 'destroy'])->name('destroy');
            Route::patch('/{question}/toggle-active', [\App\Http\Controllers\Web\QuestionWebController::class, 'toggleActive'])->name('toggle-active');
        });
    });
});
