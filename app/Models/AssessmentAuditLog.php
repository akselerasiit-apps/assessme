<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'gamo_objective_id',
        'level',
        'user_id',
        'action',
        'description',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'level' => 'integer',
    ];

    /**
     * Get the assessment this log belongs to
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the GAMO objective
     */
    public function gamoObjective(): BelongsTo
    {
        return $this->belongsTo(GamoObjective::class);
    }

    /**
     * Get the user who made the change
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new audit log entry
     */
    public static function logChange(
        int $assessmentId,
        int $gamoObjectiveId,
        ?int $level,
        string $action,
        string $description,
        $oldValue = null,
        $newValue = null
    ): self {
        return self::create([
            'assessment_id' => $assessmentId,
            'gamo_objective_id' => $gamoObjectiveId,
            'level' => $level,
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
