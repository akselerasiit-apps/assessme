<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'status',
        'failure_reason',
        'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope successful attempts
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'SUCCESS');
    }

    /**
     * Scope failed attempts
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'FAILED');
    }

    /**
     * Scope by IP address
     */
    public function scopeByIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }
}
