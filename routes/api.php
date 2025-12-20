<?php

use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BandingController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DesignFactorController;
use App\Http\Controllers\Api\GamoObjectiveController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\ScoringController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Web\CapabilityAssessmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'login']); // Alias for tests

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
    
    // Alias routes for tests
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // User Management routes
    Route::apiResource('users', UserController::class);
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);
    Route::post('/users/{id}/assign-role', [UserController::class, 'assignRole']);
    Route::patch('/users/{id}/status', [UserController::class, 'updateStatus']);
    Route::post('/users/bulk-import', [UserController::class, 'bulkImport']);

    // Company routes
    Route::apiResource('companies', CompanyController::class);

    // Design Factor routes
    Route::apiResource('design-factors', DesignFactorController::class);

    // GAMO Objective routes
    Route::get('/gamo-objectives/category/{category}', [GamoObjectiveController::class, 'byCategory']);
    Route::apiResource('gamo-objectives', GamoObjectiveController::class);

    // Question routes
    Route::get('/questions/gamo-objective/{gamoObjectiveId}', [QuestionController::class, 'byGamoObjective']);
    Route::post('/questions/bulk-import', [QuestionController::class, 'bulkImport']);
    Route::patch('/questions/{id}/toggle-active', [QuestionController::class, 'toggleActive']);
    Route::apiResource('questions', QuestionController::class);

    // Assessment routes
    Route::apiResource('assessments', AssessmentController::class, [
        'names' => [
            'index' => 'api.assessments.index',
            'store' => 'api.assessments.store',
            'show' => 'api.assessments.show',
            'update' => 'api.assessments.update',
            'destroy' => 'api.assessments.destroy',
        ]
    ]);
    Route::patch('/assessments/{id}/status', [AssessmentController::class, 'updateStatus']);
    Route::post('/assessments/{id}/design-factors', [AssessmentController::class, 'selectDesignFactors']);
    Route::post('/assessments/{id}/gamo-selections', [AssessmentController::class, 'selectGamoObjectives']);
    Route::post('/assessments/{id}/submit', [AssessmentController::class, 'submit']);

    // Answer routes
    Route::get('/assessments/{assessmentId}/answers', [AnswerController::class, 'index']);
    Route::get('/assessments/{assessmentId}/answers/{answerId}', [AnswerController::class, 'show']);
    Route::post('/assessments/{assessmentId}/answers', [AnswerController::class, 'store']);
    Route::post('/assessments/{assessmentId}/answers/{answerId}/evidence', [AnswerController::class, 'uploadEvidence']);
    Route::get('/assessments/{assessmentId}/answers/{answerId}/evidence', [AnswerController::class, 'getEvidence'])->name('api.assessments.evidence');
    Route::delete('/assessments/{assessmentId}/answers/{answerId}', [AnswerController::class, 'destroy']);
    
    // Answer routes - Simplified aliases for tests
    Route::post('/answers', [AnswerController::class, 'storeSimple']);
    Route::put('/answers/{answerId}', [AnswerController::class, 'updateSimple']);
    Route::post('/evidence/upload', [AnswerController::class, 'uploadEvidenceSimple']);

    // Scoring routes
    Route::get('/assessments/{assessmentId}/scoring/summary', [ScoringController::class, 'summary']);
    Route::post('/assessments/{assessmentId}/scoring/target-maturity/{gamoId}', [ScoringController::class, 'setTargetMaturity']);
    Route::post('/assessments/{assessmentId}/scoring/capability/{gamoId}', [ScoringController::class, 'calculateCapabilityScores']);
    Route::get('/assessments/{assessmentId}/scoring/capability/{gamoId}', [ScoringController::class, 'getCapabilityAssessment']);
    Route::get('/assessments/{assessmentId}/scoring/gap-analysis', [ScoringController::class, 'gapAnalysis']);

    // Banding routes
    Route::apiResource('bandings', BandingController::class);
    Route::post('/assessments/{assessmentId}/bandings', [BandingController::class, 'store']);
    Route::post('/bandings/{id}/approve', [BandingController::class, 'approve']);
    Route::post('/bandings/{id}/reject', [BandingController::class, 'reject']);

    // Report routes
    Route::get('/reports/dashboard-stats', [ReportController::class, 'dashboardStats']);
    Route::get('/reports/assessments/{assessment}/summary-pdf', [ReportController::class, 'assessmentSummaryPdf']);
    Route::get('/reports/assessments/{assessment}/maturity-pdf', [ReportController::class, 'maturityReportPdf']);
    Route::get('/reports/assessments/{assessment}/gap-analysis-pdf', [ReportController::class, 'gapAnalysisPdf']);
    Route::get('/reports/assessments/{assessment}/export-excel', [ReportController::class, 'exportExcel']);

    // Audit Log routes
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
    Route::get('/audit-logs/statistics', [AuditLogController::class, 'statistics']);
    Route::get('/audit-logs/export', [AuditLogController::class, 'export']);
    Route::get('/audit-logs/users/{userId}/activities', [AuditLogController::class, 'userActivities']);
    Route::get('/audit-logs/{id}', [AuditLogController::class, 'show']);
    
    // Capability Assessment - Evidence Details
    Route::get('/assessments/{assessment}/answers/{answer}/evidence', [CapabilityAssessmentController::class, 'getEvidenceDetails']);
});
