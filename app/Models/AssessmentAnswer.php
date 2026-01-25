<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentAnswer extends Model
{
    use HasFactory;
    protected $fillable = [
        'assessment_id',
        'question_id',
        'gamo_objective_id',
        'answer_text',
        'translated_text',
        'answer_json',
        'maturity_level',
        'level',
        'capability_score',
        'capability_rating',
        'is_encrypted',
        'evidence_file',
        'evidence_encrypted',
        'notes',
        'answered_by',
        'answered_at',
    ];

    protected $casts = [
        'answer_json' => 'array',
        'maturity_level' => 'integer',
        'level' => 'integer',
        'capability_score' => 'decimal:2',
        'is_encrypted' => 'boolean',
        'evidence_encrypted' => 'boolean',
        'answered_at' => 'datetime',
    ];

    /**
     * Get the assessment this answer belongs to
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the question this answer is for
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(GamoQuestion::class, 'question_id');
    }

    /**
     * Get the GAMO objective this answer is for
     */
    public function gamoObjective(): BelongsTo
    {
        return $this->belongsTo(GamoObjective::class, 'gamo_objective_id');
    }

    /**
     * Get the user who answered
     */
    public function answerer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'answered_by');
    }

    /**
     * Get capability scores for this answer
     */
    public function capabilityScores(): HasMany
    {
        return $this->hasMany(AssessmentAnswerCapabilityScore::class, 'assessment_answer_id');
    }

    /**
     * Check if answer is complete
     */
    public function isComplete(): bool
    {
        return !is_null($this->answered_at) && (!is_null($this->answer_text) || !is_null($this->answer_json));
    }
}
