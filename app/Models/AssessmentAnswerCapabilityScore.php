<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAnswerCapabilityScore extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'assessment_answer_id',
        'level',
        'compliance_score',
        'compliance_percentage',
        'achievement_status',
        'evidence_provided',
        'evidence_count',
        'assessment_notes',
    ];

    protected $casts = [
        'level' => 'integer',
        'compliance_score' => 'decimal:2',
        'compliance_percentage' => 'integer',
        'evidence_provided' => 'boolean',
        'evidence_count' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the assessment answer this score belongs to
     */
    public function assessmentAnswer(): BelongsTo
    {
        return $this->belongsTo(AssessmentAnswer::class, 'assessment_answer_id');
    }

    /**
     * Scope by achievement status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('achievement_status', $status);
    }

    /**
     * Check if fully achieved
     */
    public function isFullyAchieved(): bool
    {
        return $this->achievement_status === 'FULLY_ACHIEVED';
    }
}
