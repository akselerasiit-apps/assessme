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
        'original_score',
        'banded_score',
        'banding_reason',
        'evidence_submitted',
        'requested_by',
        'reviewed_by',
        'status',
        'reviewer_notes',
        'requested_at',
        'reviewed_at',
    ];

    protected $casts = [
        'banding_round' => 'integer',
        'original_score' => 'decimal:2',
        'banded_score' => 'decimal:2',
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
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
     * Get the user who requested banding
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who reviewed banding
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pending bandings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    /**
     * Check if banding was approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    /**
     * Get score improvement
     */
    public function getScoreImprovement(): float
    {
        return round($this->banded_score - $this->original_score, 2);
    }
}
