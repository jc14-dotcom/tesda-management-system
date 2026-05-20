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
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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

        // Auto-embed the Alcatt logo as a CID inline attachment for every
        // outgoing email. Gmail blocks data: URIs inside <img src>, and the
        // app runs on http://localhost so external URLs can't be fetched —
        // CID attachments are the only universally supported delivery path.
        Event::listen(MessageSending::class, function (MessageSending $event) {
            $logoPath = public_path('assets/alcatt-logo-email.png');
            if (file_exists($logoPath)) {
                $event->message->embedFromPath($logoPath, 'alcatt-logo', 'image/png');
            }
        });

        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        // Password reset emails — max 3 requests per 5 minutes per IP
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinutes(5, 3)->by($request->ip());
        });

        // Registration — max 5 per hour per IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });

        // Password change / confirm-password — max 5 per minute per user
        RateLimiter::for('password-mutation', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        // Sensitive profile mutations (delete account, remove photo, update details)
        RateLimiter::for('profile-mutations', function (Request $request) {
            return Limit::perHour(15)->by($request->user()?->id ?: $request->ip());
        });

        // Admin CSV/Excel exports — DB-heavy, max 10 per hour per admin
        RateLimiter::for('admin-exports', function (Request $request) {
            return Limit::perHour(10)->by($request->user()?->id ?: $request->ip());
        });

        // Admin backup operations — very resource-heavy, max 5 per hour per admin
        RateLimiter::for('admin-backups', function (Request $request) {
            return Limit::perHour(5)->by($request->user()?->id ?: $request->ip());
        });

        // Admin-triggered password resets — max 10 per hour per admin
        RateLimiter::for('admin-password-reset', function (Request $request) {
            return Limit::perHour(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
