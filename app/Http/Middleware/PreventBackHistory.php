<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Add no-cache headers to authenticated responses so the browser cannot serve
     * stale pages from disk/HTTP cache after logout.
     *
     * We also set a JS-readable (non-httpOnly) session cookie — `auth_presence` —
     * that client-side bfcache guards can read instantly on pageshow to determine
     * whether the session is still alive, without making a server round-trip.
     * This cookie carries NO authentication authority; all real auth is still
     * enforced by Laravel's session (httpOnly) and auth middleware server-side.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check()) {
            // HTTP/disk cache prevention (also disables bfcache in Chrome 96+ / Firefox 96+).
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');

            // JS-readable presence cookie — expires when the browser session ends.
            // httpOnly=false is intentional: JS must read it in pageshow to detect logout.
            $response->headers->setCookie(new Cookie(
                'auth_presence', '1', 0, '/',
                null,
                config('session.secure', false),
                false,  // not httpOnly
                false,
                Cookie::SAMESITE_LAX
            ));
        } else {
            // Erase the presence cookie so any bfcache restore of an authenticated
            // page can detect the session is gone without hitting the server.
            $response->headers->setCookie(new Cookie(
                'auth_presence', '', 1, '/',
                null,
                config('session.secure', false),
                false,
                false,
                Cookie::SAMESITE_LAX
            ));
        }

        return $response;
    }
}
