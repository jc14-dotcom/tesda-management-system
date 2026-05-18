<?php

namespace App\Providers;

use App\Models\Certificate;
use App\Models\Document;
use App\Models\User;
use App\Observers\CertificateObserver;
use App\Observers\DocumentObserver;
use App\Observers\UserObserver;
use App\Policies\CertificatePolicy;
use App\Policies\DocumentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        if (env('VITE_FORCE_BUILD', false)) {
            Vite::useHotFile(storage_path('app/vite-hot-disabled'));
        }

        // Provide $isAdminUser to the sidebar layout via a cached view composer
        // so the Spatie role query only runs once per hour per user instead of
        // on every single request.
        View::composer('layouts.sidebar', \App\View\Composers\SidebarComposer::class);

        // Register model observers for admin notifications
        Certificate::observe(CertificateObserver::class);
        Document::observe(DocumentObserver::class);
        User::observe(UserObserver::class);

        // Register model policies
        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(Certificate::class, CertificatePolicy::class);
    }
}
