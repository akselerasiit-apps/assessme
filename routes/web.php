<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\AssessmentWebController;
use App\Http\Controllers\Web\ReportWebController;

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
Route::middleware(['auth:sanctum'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile & Settings
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    
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
        
        // Answer questions
        Route::get('/{assessment}/answer', [AssessmentWebController::class, 'answer'])->name('answer');
        Route::post('/{assessment}/submit-answer', [AssessmentWebController::class, 'submitAnswer'])->name('submit-answer');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportWebController::class, 'index'])->name('index');
        Route::get('/{assessment}/maturity', [ReportWebController::class, 'maturity'])->name('maturity');
        Route::get('/{assessment}/gap-analysis', [ReportWebController::class, 'gapAnalysis'])->name('gap-analysis');
        Route::get('/{assessment}/summary', [ReportWebController::class, 'summary'])->name('summary');
    });
    
    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [DashboardController::class, 'users'])->name('users');
        Route::get('/roles', [DashboardController::class, 'roles'])->name('roles');
        Route::get('/audit-logs', [DashboardController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/settings', [DashboardController::class, 'adminSettings'])->name('settings');
    });
});
