<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), false)) {
            RateLimiter::hit($this->throttleKey());

            $remaining = RateLimiter::remaining($this->throttleKey(), 5);

            // All attempts exhausted — lock out immediately and show countdown
            if ($remaining === 0) {
                $seconds = RateLimiter::availableIn($this->throttleKey());
                $minutes = (int) ceil($seconds / 60);
                session()->flash('lockout_seconds', $seconds);
                throw ValidationException::withMessages([
                    'login_lockout' => "Too many failed login attempts. Please wait {$minutes} " . ($minutes === 1 ? 'minute' : 'minutes') . ' and try again.',
                ]);
            }

            // Build messages: always show invalid-credentials; add warning when ≤ 3 remain
            $messages = ['email' => trans('auth.failed')];
            if ($remaining <= 3) {
                $messages['login_warning'] = $remaining === 1
                    ? 'Warning: This is your last attempt before your account is temporarily locked.'
                    : "Warning: {$remaining} attempts remaining before your account is temporarily locked.";
            }

            throw ValidationException::withMessages($messages);
        }

        RateLimiter::clear($this->throttleKey());

        // Block pending and inactive accounts after credentials are verified
        $profileStatus = Auth::user()->profile?->status ?? 'active';

        if ($profileStatus === 'pending') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your account is pending approval by an administrator. You will receive an email once your account is approved.',
            ]);
        }

        if ($profileStatus === 'inactive') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your account has been deactivated. Please contact the system administrator.',
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $minutes = (int) ceil($seconds / 60);
        session()->flash('lockout_seconds', $seconds);

        throw ValidationException::withMessages([
            'login_lockout' => "Too many failed login attempts. Please wait {$minutes} " . ($minutes === 1 ? 'minute' : 'minutes') . ' and try again.',
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
