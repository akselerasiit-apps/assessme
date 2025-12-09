<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GamoObjective extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'name_id',
        'description',
        'description_id',
        'category',
        'objective_order',
        'is_active',
    ];

    protected $casts = [
        'objective_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get all questions for this GAMO
     */
    public function questions(): HasMany
    {
        return $this->hasMany(GamoQuestion::class, 'gamo_objective_id');
    }

    /**
     * Get capability definitions for this GAMO
     */
    public function capabilityDefinitions(): HasMany
    {
        return $this->hasMany(GamoCapabilityDefinition::class, 'gamo_objective_id');
    }

    /**
     * Get assessments that selected this GAMO
     */
    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(Assessment::class, 'assessment_gamo_selections')
            ->withPivot('is_selected', 'selection_reason', 'selected_at')
            ->withTimestamps();
    }

    /**
     * Get GAMO scores across assessments
     */
    public function scores(): HasMany
    {
        return $this->hasMany(GamoScore::class, 'gamo_objective_id');
    }

    /**
     * Get target levels across assessments
     */
    public function targetLevels(): HasMany
    {
        return $this->hasMany(AssessmentGamoTargetLevel::class, 'gamo_objective_id');
    }

    /**
     * Get bandings for this GAMO
     */
    public function bandings(): HasMany
    {
        return $this->hasMany(AssessmentBanding::class, 'gamo_objective_id');
    }

    /**
     * Scope by category (EDM, APO, BAI, DSS, MEA)
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope active GAMO objectives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get localized name
     */
    public function getLocalizedName(string $locale = 'en'): string
    {
        return $locale === 'id' ? $this->name_id : $this->name;
    }

    /**
     * Get localized description
     */
    public function getLocalizedDescription(string $locale = 'en'): ?string
    {
        return $locale === 'id' ? $this->description_id : $this->description;
    }
}
