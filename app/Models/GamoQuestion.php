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
        'gamo_objective_id',
        'question_code',
        'question_text',
        'question_text_id',
        'question_type',
        'capability_level',
        'question_order',
        'is_active',
    ];

    protected $casts = [
        'capability_level' => 'integer',
        'question_order' => 'integer',
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
     * Scope by capability level
     */
    public function scopeByCapabilityLevel($query, int $level)
    {
        return $query->where('capability_level', $level);
    }

    /**
     * Get localized question text
     */
    public function getLocalizedQuestion(string $locale = 'en'): string
    {
        return $locale === 'id' ? $this->question_text_id : $this->question_text;
    }
}
