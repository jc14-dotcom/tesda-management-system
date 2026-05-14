<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Alcatt Portal — Create Account</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-grayTheme-light text-grayTheme-dark antialiased">
        <main class="flex min-h-screen">
            <!-- Left: Brand Panel -->
            <div class="relative hidden overflow-hidden lg:flex lg:w-[42%] lg:flex-col bg-primary">
                <!-- Radial glow accents -->
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,_rgba(244,180,0,0.13),_transparent_50%)]"></div>
                <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-white/5"></div>
                <div class="absolute -bottom-32 -right-24 h-96 w-96 rounded-full bg-white/5"></div>
                <!-- Dot grid -->
                <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.10)_1px,transparent_1px)] bg-[size:28px_28px]"></div>

                <div class="relative z-10 flex flex-1 flex-col px-10 py-10">
                    <!-- Logo -->
                    <a href="/" class="inline-flex items-center gap-3">
                        <img src="{{ asset('assets/alcatt-logo.png') }}" class="h-10 w-10 object-contain" alt="Alcatt Portal" />
                        <span class="text-xl font-extrabold tracking-tight text-white">Alcatt Portal</span>
                    </a>

                    <!-- Center content -->
                    <div class="flex flex-1 flex-col justify-center">
                        <h2 class="text-3xl font-black leading-snug tracking-tight text-white">
                            Join the portal.<br>
                            <span class="text-accent">Get started today.</span>
                        </h2>
                        <p class="mt-4 max-w-xs text-sm leading-7 text-white/65">
                            Create your account and gain instant access to certificate tracking, document management, and profile administration.
                        </p>

                        <div class="mt-10 space-y-5">
                            <div class="flex items-start gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-accent">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">Quick Setup</p>
                                    <p class="text-xs text-white/60">Up and running in under a minute</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">Secure by Default</p>
                                    <p class="text-xs text-white/60">SSL encryption &amp; role-based access control</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">Everything Organized</p>
                                    <p class="text-xs text-white/60">Certificates, documents &amp; profiles in one dashboard</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-white/40">© {{ date('Y') }} Alcatt Portal. All rights reserved.</p>
                </div>
            </div>

            <!-- Right: Form Panel -->
            <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 lg:px-12 bg-[radial-gradient(circle_at_top_right,_rgba(43,45,126,0.06),_transparent_40%),linear-gradient(135deg,_#f8f9ff_0%,_#f3f4f6_100%)]">
                <!-- Mobile logo -->
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <img src="{{ asset('assets/alcatt-logo.png') }}" class="h-10 w-10 object-contain" alt="Alcatt Portal" />
                    <span class="text-xl font-extrabold text-grayTheme-dark">Alcatt Portal</span>
                </div>

                <div class="w-full max-w-md">
                    <div class="rounded-[20px] border border-grayTheme-border bg-white p-8 shadow-modal sm:p-10">
                        <!-- Heading -->
                        <div class="mb-7">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-soft">
                                <svg class="h-6 w-6 text-[#B67C00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-grayTheme-dark">Create your account</h1>
                            <p class="mt-1 text-sm text-grayTheme-medium">Join Alcatt Portal to get started</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}" class="space-y-4">
                            @csrf

                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Full name')" />
                                <div class="relative mt-1.5">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                        <svg class="h-4 w-4 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <x-text-input id="name" class="block w-full pl-10" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
                                </div>
                                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="email" :value="__('Email address')" />
                                <div class="relative mt-1.5">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                        <svg class="h-4 w-4 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <x-text-input id="email" class="block w-full pl-10" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                            </div>

                            <!-- Password -->
                            <div>
                                <x-input-label for="password" :value="__('Password')" />
                                <div class="relative mt-1.5">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                        <svg class="h-4 w-4 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <x-text-input id="password" class="block w-full pl-10 pr-10" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                                    <button type="button"
                                        onclick="(function(b){var i=document.getElementById('password');i.type=i.type==='password'?'text':'password';b.querySelector('.eye-off').classList.toggle('hidden');b.querySelector('.eye-on').classList.toggle('hidden');})(this)"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-grayTheme-medium hover:text-primary transition"
                                        tabindex="-1"
                                        aria-label="Toggle password visibility">
                                        <svg class="eye-off h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                        <svg class="eye-on hidden h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm password')" />
                                <div class="relative mt-1.5">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                        <svg class="h-4 w-4 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <x-text-input id="password_confirmation" class="block w-full pl-10 pr-10" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                                    <button type="button"
                                        onclick="(function(b){var i=document.getElementById('password_confirmation');i.type=i.type==='password'?'text':'password';b.querySelector('.eye-off').classList.toggle('hidden');b.querySelector('.eye-on').classList.toggle('hidden');})(this)"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-grayTheme-medium hover:text-primary transition"
                                        tabindex="-1"
                                        aria-label="Toggle confirm password visibility">
                                        <svg class="eye-off h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                        <svg class="eye-on hidden h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                            </div>

                            <!-- Submit -->
                            <div class="pt-1">
                                <x-primary-button class="w-full justify-center gap-2 py-3 text-sm font-bold tracking-wide">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    {{ __('Create account') }}
                                </x-primary-button>
                            </div>

                            <p class="pt-1 text-center text-sm text-grayTheme-medium">
                                {{ __('Already have an account?') }}
                                <a class="font-semibold text-primary transition hover:text-primary-hover" href="{{ route('login') }}">{{ __('Sign in here') }}</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
