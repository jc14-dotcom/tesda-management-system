<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (
            $user &&
            ! $user->hasRole('admin') &&
            $user->dpa_agreed_at !== null &&
            ! $request->routeIs('profile.complete', 'profile.complete.store', 'logout', 'notifications.poll', 'notifications.panel', 'dashboard.live', 'verification.notice', 'verification.verify', 'verification.send') &&
            ! $this->isProfileComplete($user)
        ) {
            return redirect()->route('profile.complete');
        }

        return $next($request);
    }

    private function isProfileComplete($user): bool
    {
        $profile = $user->profile;

        return $profile &&
            filled($profile->first_name) &&
            filled($profile->middle_name) &&
            filled($profile->last_name) &&
            filled($profile->date_of_birth) &&
            filled($profile->gender) &&
            filled($profile->contact_number) &&
            ! empty($profile->position_roles);
    }
}
