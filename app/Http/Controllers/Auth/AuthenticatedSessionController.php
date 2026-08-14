<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Single-session enforcement: record the current session ID so that
        // EnforceSingleSession middleware can evict any other active sessions.
        $user->forceFill(['current_session_id' => $request->session()->getId()])->save();

        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->event('login')
            ->log('User logged in');

        if (! $user->hasVerifiedEmail()) {
            if (! $user->hasActiveOtp()) {
                $user->sendEmailVerificationNotification();
            }

            return redirect()->route('verification.notice');
        }

        $redirectRoute = $user->hasRole('admin') ? 'admin.dashboard' : 'dashboard';

        return redirect()->intended(route($redirectRoute, absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        if ($user) {
            // Clear the stored session ID so stale sessions do not get false-positive evictions.
            $user->forceFill(['current_session_id' => null])->save();

            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->event('logout')
                ->log('User logged out');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
