<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GamoQuestion extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'gamo_objective_id',
        'question_text',
        'guidance',
        'evidence_requirement',
        'question_type',
        'maturity_level',
        'required',
        'question_order',
        'is_active',
    ];

    protected $casts = [
        'maturity_level' => 'integer',
        'question_order' => 'integer',
        'required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the GAMO objective this question belongs to
     */
    public function gamoObjective(): BelongsTo
    {
        return $this->belongsTo(GamoObjective::class, 'gamo_objective_id');
    }

    /**
     * Get all answers for this question
     */
    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class, 'question_id');
    }

    /**
     * Scope active questions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by maturity level
     */
    public function scopeByMaturityLevel($query, int $level)
    {
        return $query->where('maturity_level', $level);
    }

    /**
     * Get localized question text
     */
    public function getLocalizedQuestion(string $locale = 'en'): string
    {
        return $locale === 'id' ? $this->question_text_id : $this->question_text;
    }
}
