<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserToken extends Model
{
    protected $fillable = [
        'user_id',
        'token_name',
        'token_hash',
        'abilities',
        'last_used_at',
        'expires_at',
        'revoked_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /**
     * Get the user this token belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope active tokens
     */
    public function scopeActive($query)
    {
        return $query->whereNull('revoked_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope expired tokens
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Check if token is valid
     */
    public function isValid(): bool
    {
        if ($this->revoked_at) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Revoke token
     */
    public function revoke(): bool
    {
        $this->revoked_at = now();
        return $this->save();
    }
}
