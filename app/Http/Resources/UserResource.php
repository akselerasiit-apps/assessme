<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'department' => $this->department,
            'position' => $this->position,
            'status' => $this->status,
            
            // Role information (Spatie Laravel Permission)
            'roles' => $this->whenLoaded('roles', function() {
                return $this->roles->map(function($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => ucwords(str_replace('_', ' ', $role->name)),
                    ];
                });
            }),
            
            // Permissions information
            'permissions' => $this->whenLoaded('permissions', function() {
                return $this->permissions->pluck('name');
            }),
            
            // All permissions (direct + via roles)
            'all_permissions' => $this->when($request->user()?->can('user.view_permissions'), function() {
                return $this->getAllPermissions()->pluck('name');
            }),
            
            // Last login information
            'last_login' => $this->last_login?->format('Y-m-d H:i:s'),
            'last_login_human' => $this->last_login?->diffForHumans(),
            
            // 2FA status
            'two_factor_enabled' => !empty($this->two_factor_secret),
            
            // Profile completeness
            'profile_complete' => $this->phone && $this->department && $this->position,
            
            // Activity summary when loaded
            'assessments_created_count' => $this->whenLoaded('assessmentsCreated', function() {
                return $this->assessmentsCreated->count();
            }),
            'assessments_reviewed_count' => $this->whenLoaded('assessmentsReviewed', function() {
                return $this->assessmentsReviewed->count();
            }),
            'answers_submitted_count' => $this->whenLoaded('answers', function() {
                return $this->answers->count();
            }),
            
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
