<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ModelAuditObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->logModelEvent($model, 'created', null, $model->getAttributes());
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $this->logModelEvent($model, 'updated', $model->getOriginal(), $model->getChanges());
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->logModelEvent($model, 'deleted', $model->getAttributes(), null);
    }

    /**
     * Log model event to audit log
     */
    protected function logModelEvent(Model $model, string $action, ?array $oldValues, ?array $newValues): void
    {
        // Skip logging for AuditLog model itself to prevent infinite loop
        if ($model instanceof AuditLog) {
            return;
        }

        // Skip if no authenticated user (seeder, migration, etc)
        if (!Auth::check()) {
            return;
        }

        try {
            $moduleName = class_basename($model);
            
            // Filter sensitive data
            $sensitiveFields = ['password', 'remember_token', 'api_token'];
            if ($oldValues) {
                $oldValues = array_diff_key($oldValues, array_flip($sensitiveFields));
            }
            if ($newValues) {
                $newValues = array_diff_key($newValues, array_flip($sensitiveFields));
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'module' => $moduleName,
                'entity_type' => get_class($model),
                'entity_id' => $model->getKey(),
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'sensitive_data_accessed' => !empty(array_intersect_key(
                    $model->getAttributes(),
                    array_flip($sensitiveFields)
                )),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'session_id' => session()->getId(),
            ]);
        } catch (\Exception $e) {
            // Silent fail - don't break app if logging fails
            \Log::error('Model audit log failed: ' . $e->getMessage());
        }
    }
}
