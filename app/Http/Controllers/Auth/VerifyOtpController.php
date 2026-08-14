<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyOtpController extends Controller
{
    /**
     * Handle the OTP verification request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        if ($user->otp_expires_at?->isPast()) {
            return back()->withInput()
                ->withErrors(['otp' => 'This verification code has expired. Please request a new code.']);
        }

        if (! $user->hasValidOtp($request->otp)) {
            return back()->withInput()
                ->withErrors(['otp' => 'The verification code is invalid. Please try again.']);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $user->clearOtp();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
