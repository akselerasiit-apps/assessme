<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Assessment;
use App\Models\User;
use App\Models\Company;
use App\Observers\AssessmentObserver;
use App\Observers\UserObserver;
use App\Observers\CompanyObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers for audit logging
        Assessment::observe(AssessmentObserver::class);
        User::observe(UserObserver::class);
        Company::observe(CompanyObserver::class);
    }
}
