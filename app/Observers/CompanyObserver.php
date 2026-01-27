<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class CompanyObserver
{
    public function created(Company $company): void
    {
        $this->log('created', $company);
    }

    public function updated(Company $company): void
    {
        if ($company->wasChanged()) {
            $this->log('updated', $company, $company->getOriginal(), $company->getChanges());
        }
    }

    public function deleted(Company $company): void
    {
        $this->log('deleted', $company);
    }

    private function log(string $action, Company $company, array $oldValues = null, array $newValues = null): void
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'module' => 'Company',
                'entity_type' => Company::class,
                'entity_id' => $company->id,
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
