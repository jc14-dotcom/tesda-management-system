<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnforceSingleSession
{
    /**
     * If the authenticated user's stored session ID no longer matches the
     * current session, another device has signed in — log this one out.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check()) {
            $user = $request->user();

            if (
                $user->current_session_id !== null &&
                $user->current_session_id !== $request->session()->getId()
            ) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('status', 'Your session was ended because this account was signed in from another location.');
            }
        }

        return $next($request);
    }
}
