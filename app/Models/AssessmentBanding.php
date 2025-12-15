<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentBanding extends Model
{
    protected $fillable = [
        'assessment_id',
        'gamo_objective_id',
        'banding_round',
        'initiated_by',
        'banding_reason',
        'banding_description',
        'old_maturity_level',
        'new_maturity_level',
        'old_evidence_count',
        'new_evidence_count',
        'additional_evidence_files',
        'revised_answers',
        'status',
        'approved_by',
        'approval_notes',
    ];

    protected $casts = [
        'banding_round' => 'integer',
        'old_maturity_level' => 'decimal:2',
        'new_maturity_level' => 'decimal:2',
        'old_evidence_count' => 'integer',
        'new_evidence_count' => 'integer',
    ];

    /**
     * Get the assessment this banding belongs to
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the GAMO objective this banding is for
     */
    public function gamoObjective(): BelongsTo
    {
        return $this->belongsTo(GamoObjective::class, 'gamo_objective_id');
    }

    /**
     * Get the user who initiated banding
     */
    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    /**
     * Alias for initiatedBy
     */
    public function requester(): BelongsTo
    {
        return $this->initiatedBy();
    }

    /**
     * Get the user who approved/rejected banding
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Alias for approvedBy
     */
    public function reviewer(): BelongsTo
    {
        return $this->approvedBy();
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope submitted bandings (pending approval)
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope draft bandings
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Check if banding was approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if banding was rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if banding is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Get maturity improvement
     */
    public function getMaturityImprovement(): ?float
    {
        if ($this->old_maturity_level && $this->new_maturity_level) {
            return round($this->new_maturity_level - $this->old_maturity_level, 2);
        }
        return null;
    }

    /**
     * Get evidence improvement
     */
    public function getEvidenceImprovement(): int
    {
        return ($this->new_evidence_count ?? 0) - ($this->old_evidence_count ?? 0);
    }
}

