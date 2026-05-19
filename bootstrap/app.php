<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->append(\App\Http\Middleware\RequestPerformanceLogger::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\PreventBackHistory::class);
        $middleware->appendToGroup('web', \Illuminate\Session\Middleware\AuthenticateSession::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\EnforceSingleSession::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureDpaAgreed::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Redirect authenticated users away from restricted pages instead of showing a raw 403
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, \Illuminate\Http\Request $request) {
            if (auth()->check()) {
                return redirect()->route('dashboard')
                    ->with('forbidden', "You don't have permission to access that page.");
            }
        });

        // Redirect back with a friendly message when a rate limit is hit on a web route
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return null; // fall through to default JSON 429 handling
            }
            $retryAfter = (int) ($e->getHeaders()['Retry-After'] ?? 60);
            $minutes    = (int) ceil($retryAfter / 60);
            $waitText   = $retryAfter >= 60
                ? ($minutes === 1 ? '1 minute' : "{$minutes} minutes")
                : ($retryAfter === 1 ? '1 second' : "{$retryAfter} seconds");

            return redirect()->back()
                ->withInput()
                ->with('rate_limit_error', "Too many requests. Please wait {$waitText} and try again.")
                ->with('rate_limit_seconds', $retryAfter);
        });
    })->create();
