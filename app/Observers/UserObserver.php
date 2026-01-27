<?php

namespace App\Observers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public function created(User $user): void
    {
        $this->log('created', $user);
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged()) {
            $changes = $user->getChanges();
            $original = $user->getOriginal();
            
            // Remove sensitive data from logs
            unset($changes['password'], $original['password']);
            
            $this->log('updated', $user, $original, $changes);
        }
    }

    public function deleted(User $user): void
    {
        $this->log('deleted', $user);
    }

    private function log(string $action, User $user, array $oldValues = null, array $newValues = null): void
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'module' => 'User',
                'entity_type' => User::class,
                'entity_id' => $user->id,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Audit log failed: ' . $e->getMessage());
        }
    }
}
