<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Recommendation extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'assessment_id',
        'gamo_objective_id',
        'title',
        'description',
        'priority',
        'estimated_effort',
        'responsible_person_id',
        'target_date',
        'status',
        'progress_percentage',
    ];

    protected $casts = [
        'target_date' => 'date',
        'progress_percentage' => 'integer',
    ];

    /**
     * Get activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'priority', 'status', 'progress_percentage', 'responsible_person_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the assessment that owns the recommendation.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the GAMO objective that owns the recommendation.
     */
    public function gamoObjective(): BelongsTo
    {
        return $this->belongsTo(GamoObjective::class);
    }

    /**
     * Get the responsible person for this recommendation.
     */
    public function responsiblePerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_person_id');
    }

    /**
     * Scope to filter by priority.
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get overdue recommendations.
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotIn('status', ['completed', 'closed'])
            ->whereNotNull('target_date')
            ->where('target_date', '<', now());
    }

    /**
     * Scope to get upcoming recommendations (due in next 7 days).
     */
    public function scopeUpcoming($query)
    {
        return $query->whereNotIn('status', ['completed', 'closed'])
            ->whereNotNull('target_date')
            ->whereBetween('target_date', [now(), now()->addDays(7)]);
    }

    /**
     * Check if recommendation is overdue.
     */
    public function isOverdue(): bool
    {
        if (!$this->target_date) {
            return false;
        }

        return !in_array($this->status, ['completed', 'closed']) && $this->target_date < now();
    }

    /**
     * Check if recommendation is upcoming (due in next 7 days).
     */
    public function isUpcoming(): bool
    {
        if (!$this->target_date) {
            return false;
        }

        $daysUntilDue = now()->diffInDays($this->target_date, false);

        return !in_array($this->status, ['completed', 'closed']) 
            && $daysUntilDue >= 0 
            && $daysUntilDue <= 7;
    }

    /**
     * Get priority badge color.
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'open' => 'secondary',
            'in_progress' => 'primary',
            'completed' => 'success',
            'closed' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Get progress bar color based on status and progress.
     */
    public function getProgressBarColorAttribute(): string
    {
        if ($this->progress_percentage >= 100) {
            return 'success';
        } elseif ($this->progress_percentage >= 75) {
            return 'info';
        } elseif ($this->progress_percentage >= 50) {
            return 'primary';
        } elseif ($this->progress_percentage >= 25) {
            return 'warning';
        }

        return 'danger';
    }

    /**
     * Get days until target date.
     */
    public function getDaysUntilTargetAttribute(): ?int
    {
        if (!$this->target_date) {
            return null;
        }

        return now()->diffInDays($this->target_date, false);
    }

    /**
     * Get formatted target date (e.g., "in 5 days", "overdue by 2 days").
     */
    public function getTargetDateFormattedAttribute(): ?string
    {
        if (!$this->target_date) {
            return null;
        }

        $daysUntil = $this->days_until_target;

        if ($daysUntil < 0) {
            return 'overdue by ' . abs($daysUntil) . ' day' . (abs($daysUntil) != 1 ? 's' : '');
        } elseif ($daysUntil == 0) {
            return 'due today';
        } elseif ($daysUntil == 1) {
            return 'due tomorrow';
        } else {
            return 'in ' . $daysUntil . ' days';
        }
    }
}
