<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDpaAgreed
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            auth()->check() &&
            auth()->user()->dpa_agreed_at === null &&
            ! $request->routeIs('dpa.accept', 'dpa.accept.store', 'logout')
        ) {
            return redirect()->route('dpa.accept');
        }

        return $next($request);
    }
}
