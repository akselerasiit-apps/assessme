<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'company_id',
        'title',
        'description',
        'assessment_type',
        'scope_type',
        'assessment_period_start',
        'assessment_period_end',
        'status',
        'overall_maturity_level',
        'progress_percentage',
        'is_encrypted',
        'created_by',
        'reviewed_by',
        'approved_by',
    ];

    protected $casts = [
        'assessment_period_start' => 'date',
        'assessment_period_end' => 'date',
        'overall_maturity_level' => 'decimal:2',
        'progress_percentage' => 'integer',
        'is_encrypted' => 'boolean',
    ];

    /**
     * Get the company this assessment belongs to
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created this assessment
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Alias for createdBy (backward compatibility)
     */
    public function creator(): BelongsTo
    {
        return $this->createdBy();
    }

    /**
     * Get the user who reviewed this assessment
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Alias for reviewedBy (backward compatibility)
     */
    public function reviewer(): BelongsTo
    {
        return $this->reviewedBy();
    }

    /**
     * Get the user who approved this assessment
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get selected design factors
     */
    public function designFactors(): BelongsToMany
    {
        return $this->belongsToMany(DesignFactor::class, 'assessment_design_factors')
            ->withPivot('selected_value', 'description');
    }

    /**
     * Get selected GAMO objectives
     */
    public function gamoObjectives(): BelongsToMany
    {
        return $this->belongsToMany(GamoObjective::class, 'assessment_gamo_selections')
            ->withPivot('is_selected', 'selection_reason', 'selected_at');
    }

    /**
     * Get all answers for this assessment
     */
    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    /**
     * Get GAMO scores for this assessment
     */
    public function gamoScores(): HasMany
    {
        return $this->hasMany(GamoScore::class);
    }

    /**
     * Get GAMO target levels for this assessment
     */
    public function gamoTargetLevels(): HasMany
    {
        return $this->hasMany(AssessmentGamoTargetLevel::class);
    }

    /**
     * Get bandings for this assessment
     */
    public function bandings(): HasMany
    {
        return $this->hasMany(AssessmentBanding::class);
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope active (not locked) assessments
     */
    public function scopeActive($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Check if assessment is editable
     */
    public function isEditable(): bool
    {
        return !$this->is_locked && in_array($this->status, ['DRAFT', 'IN_PROGRESS']);
    }

    /**
     * Calculate overall completion percentage
     */
    public function calculateCompletionPercentage(): int
    {
        $totalQuestions = $this->answers()->count();
        $answeredQuestions = $this->answers()->whereNotNull('answered_at')->count();
        
        return $totalQuestions > 0 ? (int) (($answeredQuestions / $totalQuestions) * 100) : 0;
    }
}
