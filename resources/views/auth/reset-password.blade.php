<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Alcatt Portal &mdash; Reset Password</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen antialiased bg-[radial-gradient(circle_at_top_right,_rgba(43,45,126,0.07),_transparent_40%),linear-gradient(135deg,_#f8f9ff_0%,_#f3f4f6_100%)]">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12">

            <!-- Back navigation -->
            <div class="mb-5 w-full max-w-md">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-button border border-grayTheme-border bg-white px-4 py-2 text-sm font-semibold text-grayTheme-medium shadow-card transition hover:border-primary hover:text-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to sign in
                </a>
            </div>

            <!-- Card -->
            <div class="w-full max-w-md rounded-[20px] border border-grayTheme-border bg-white p-8 shadow-modal sm:p-10"
                 x-data="{
                     showPwd: false,
                     showConfirmPwd: false,
                     pwd: '',
                     pwdConfirm: '',
                     get hasMinLength() { return this.pwd.length >= 8; },
                     get hasUpper()     { return /[A-Z]/.test(this.pwd); },
                     get hasLower()     { return /[a-z]/.test(this.pwd); },
                     get hasNumber()    { return /[0-9]/.test(this.pwd); },
                     get hasSpecial()   { return /[^A-Za-z0-9]/.test(this.pwd); },
                     get strength() {
                         let s = 0;
                         if (this.hasMinLength) s++;
                         if (this.hasUpper)     s++;
                         if (this.hasLower)     s++;
                         if (this.hasNumber)    s++;
                         if (this.hasSpecial)   s++;
                         return s;
                     },
                     get strengthLabel()      { if (!this.pwd.length) return ''; return ['','Very Weak','Weak','Fair','Strong','Very Strong'][this.strength] ?? 'Very Strong'; },
                     get strengthColor()      { return this.strength <= 1 ? 'bg-danger' : this.strength === 2 ? 'bg-warning' : this.strength === 3 ? 'bg-accent' : 'bg-success'; },
                     get strengthLabelColor() { return this.strength <= 1 ? 'text-danger' : this.strength === 2 ? 'text-warning' : this.strength === 3 ? 'text-accent' : 'text-success'; },
                     get strengthWidthPct()   { return (this.strength / 5 * 100) + '%'; },
                     get requirements() {
                         return [
                             { met: this.hasMinLength, label: 'At least 8 characters' },
                             { met: this.hasUpper,     label: 'Uppercase letter (A–Z)' },
                             { met: this.hasLower,     label: 'Lowercase letter (a–z)' },
                             { met: this.hasNumber,    label: 'Number (0–9)' },
                             { met: this.hasSpecial,   label: 'Special character (!@#…)' },
                         ];
                     },
                     get pwdMatch()    { return this.pwd.length > 0 && this.pwdConfirm.length > 0 && this.pwd === this.pwdConfirm; },
                     get pwdMismatch() { return this.pwdConfirm.length > 0 && this.pwd !== this.pwdConfirm; },
                     get canSubmit()   { return this.strength >= 4 && this.pwdMatch; },
                 }">

                <!-- Header -->
                <div class="mb-8 flex flex-col items-center text-center">
                    <img src="{{ asset('assets/alcatt-logo.png') }}" alt="Alcatt Portal" class="h-14 w-14 object-contain" />

                    <div class="mt-5 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>

                    <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-grayTheme-dark">Set a new password</h1>
                    <p class="mt-2 max-w-xs text-sm leading-6 text-grayTheme-medium">
                        Choose a strong password for your Alcatt Portal account.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    {{-- Rate limit banner --}}
                    @if (session('rate_limit_error'))
                        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-100">
                                    <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-red-800">Too Many Requests</p>
                                    <p class="mt-0.5 text-sm text-red-700">{{ session('rate_limit_error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Email address')" :required="true" />
                        <div class="relative mt-1.5">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <svg class="h-4 w-4 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <x-text-input id="email" class="block w-full pl-10" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="you@example.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <!-- New Password -->
                    <div>
                        <x-input-label for="password" :value="__('New password')" :required="true" />
                        <div class="relative mt-1.5">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <svg class="h-4 w-4 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <x-text-input id="password" class="block w-full pl-10 pr-10" x-bind:type="showPwd ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="••••••••" x-model="pwd" />
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-grayTheme-medium hover:text-grayTheme-dark" @click="showPwd = !showPwd" tabindex="-1">
                                <svg x-show="!showPwd" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPwd" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />

                        {{-- Strength bar --}}
                        <div x-show="pwd.length > 0" x-cloak class="mt-3 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-grayTheme-border">
                                    <div class="h-full rounded-full transition-all duration-300"
                                         x-bind:class="strengthColor"
                                         x-bind:style="'width:' + strengthWidthPct"></div>
                                </div>
                                <span class="text-xs font-semibold" x-bind:class="strengthLabelColor" x-text="strengthLabel"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                                <template x-for="req in requirements" :key="req.label">
                                    <div class="flex items-center gap-1.5">
                                        <svg x-show="req.met" class="h-3.5 w-3.5 shrink-0 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <svg x-show="!req.met" class="h-3.5 w-3.5 shrink-0 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="9"/>
                                        </svg>
                                        <span class="text-xs" x-bind:class="req.met ? 'text-success font-medium' : 'text-grayTheme-medium'" x-text="req.label"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm new password')" :required="true" />
                        <div class="relative mt-1.5">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <svg class="h-4 w-4 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <x-text-input id="password_confirmation" class="block w-full pl-10 pr-10" x-bind:type="showConfirmPwd ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" x-model="pwdConfirm" />
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-grayTheme-medium hover:text-grayTheme-dark" @click="showConfirmPwd = !showConfirmPwd" tabindex="-1">
                                <svg x-show="!showConfirmPwd" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showConfirmPwd" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        <p x-show="pwdMatch" x-cloak class="mt-1.5 flex items-center gap-1 text-xs font-medium text-success">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Passwords match
                        </p>
                        <p x-show="pwdMismatch" x-cloak class="mt-1.5 flex items-center gap-1 text-xs font-medium text-danger">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Passwords do not match
                        </p>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                    </div>

                    <x-primary-button class="w-full justify-center gap-2 py-3 text-sm font-bold tracking-wide disabled:opacity-50 disabled:cursor-not-allowed" x-bind:disabled="!canSubmit">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('Reset Password') }}
                    </x-primary-button>
                </form>
            </div>
        </div>
    </body>
</html>
