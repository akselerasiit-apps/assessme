<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentTeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'user_id',
        'role',
        'responsibilities',
        'can_edit',
        'can_approve',
        'assigned_at',
        'assigned_by',
    ];

    protected $casts = [
        'can_edit' => 'boolean',
        'can_approve' => 'boolean',
        'assigned_at' => 'datetime',
    ];

    /**
     * Get the assessment that this team member belongs to.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the user assigned to the team.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who assigned this team member.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Scope to filter by role.
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Check if member is lead.
     */
    public function isLead(): bool
    {
        return $this->role === 'lead';
    }

    /**
     * Check if member can edit.
     */
    public function canEdit(): bool
    {
        return $this->can_edit;
    }

    /**
     * Check if member can approve.
     */
    public function canApprove(): bool
    {
        return $this->can_approve;
    }
}
