<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestPerformanceLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.perf_log_enabled', false)) {
            return $next($request);
        }

        $start = microtime(true);
        $queryCount = 0;
        $queryTimeMs = 0.0;

        DB::listen(function ($query) use (&$queryCount, &$queryTimeMs) {
            $queryCount++;
            $queryTimeMs += (float) $query->time;
        });

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        $totalMs = (microtime(true) - $start) * 1000;
        $response->headers->set('Server-Timing', sprintf('app;dur=%.1f, db;dur=%.1f', $totalMs, $queryTimeMs));
        $response->headers->set('X-Perf-Queries', (string) $queryCount);
        $response->headers->set('X-Perf-Time', sprintf('%.1f', $totalMs));

        Log::channel('perf')->info('request', [
            'method' => $request->getMethod(),
            'path' => $request->path(),
            'route' => optional($request->route())->getName(),
            'status' => $response->getStatusCode(),
            'duration_ms' => round($totalMs, 1),
            'db_time_ms' => round($queryTimeMs, 1),
            'db_queries' => $queryCount,
        ]);

        return $response;
    }
}
