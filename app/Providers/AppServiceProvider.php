<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Request as ResourceRequest;
use App\Models\RequestItem;
use App\Models\User;
use App\Models\Department;
use App\Observers\AuditLogObserver;

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
        User::observe(AuditLogObserver::class);
        ResourceRequest::observe(AuditLogObserver::class);
        RequestItem::observe(AuditLogObserver::class);
        Department::observe(AuditLogObserver::class);
    }
}
