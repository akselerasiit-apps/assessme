<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'is_active',
        'avatar_path',
        'phone',
        'bio',
        'timezone',
        'language',
        'preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'preferences' => 'array',
        ];
    }

    /**
     * Get activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get assessments created by this user
     */
    public function createdAssessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'created_by');
    }

    /**
     * Get the company that owns the user
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get answers submitted by this user
     */
    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class, 'answered_by');
    }

    /**
     * Get banding requests by this user
     */
    public function bandingRequests(): HasMany
    {
        return $this->hasMany(AssessmentBanding::class, 'requested_by');
    }

    /**
     * Get banding reviews by this user
     */
    public function bandingReviews(): HasMany
    {
        return $this->hasMany(AssessmentBanding::class, 'reviewed_by');
    }

    /**
     * Get login attempts for this user
     */
    public function loginAttempts(): HasMany
    {
        return $this->hasMany(LoginAttempt::class);
    }

    /**
     * Get audit logs for this user
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get user tokens
     */
    public function userTokens(): HasMany
    {
        return $this->hasMany(UserToken::class);
    }

    /**
     * Get notifications for this user
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
