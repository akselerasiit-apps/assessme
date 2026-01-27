<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'entity_type',
        'entity_id',
        'status_code',
        'old_values',
        'new_values',
        'sensitive_data_accessed',
        'ip_address',
        'user_agent',
        'session_id',
        'is_encrypted',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'sensitive_data_accessed' => 'boolean',
        'is_encrypted' => 'boolean',
    ];

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable model
     */
    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Scope by action type
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope by auditable type
     */
    public function scopeByAuditableType($query, string $type)
    {
        return $query->where('auditable_type', $type);
    }
}
