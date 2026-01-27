<?php

namespace App\Observers;

use App\Models\Assessment;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AssessmentObserver
{
    public function created(Assessment $assessment): void
    {
        $this->log('created', $assessment);
    }

    public function updated(Assessment $assessment): void
    {
        if ($assessment->wasChanged()) {
            $this->log('updated', $assessment, $assessment->getOriginal(), $assessment->getChanges());
        }
    }

    public function deleted(Assessment $assessment): void
    {
        $this->log('deleted', $assessment);
    }

    private function log(string $action, Assessment $assessment, array $oldValues = null, array $newValues = null): void
    {
        // Skip logging for console/queue/background processes
        if (!app()->runningInConsole() && Auth::check()) {
            try {
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => $action,
                    'module' => 'Assessment',
                    'entity_type' => Assessment::class,
                    'entity_id' => $assessment->id,
                    'old_values' => $oldValues ? json_encode($oldValues) : null,
                    'new_values' => $newValues ? json_encode($newValues) : null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => session()->getId(),
                ]);
            } catch (\Exception $e) {
                // Silent fail
            }
        }
    }
}
