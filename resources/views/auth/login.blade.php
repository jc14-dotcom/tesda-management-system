<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Alcatt Portal — Sign In</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{--
            bfcache guard for the login page.
            When the user fills in credentials, logs in, then later logs out and presses
            back, the browser may restore this exact form state from bfcache — including
            the previously typed username and (in some browsers) password placeholder.

            pagehide pre-hides the body before the snapshot so no credentials flash.
            pageshow (persisted) replaces the history entry with a fresh server request,
            which: (1) clears all form fields, (2) issues a new CSRF token, (3) redirects
            to the dashboard automatically if the user is still authenticated.
        --}}
        <script>
        (function(){
            window.addEventListener('pagehide',function(){
                document.body.style.display='none';
            });
            window.addEventListener('pageshow',function(e){
                if(e.persisted){
                    window.location.replace(window.location.href);
                }
            });
        }());
        </script>
    </head>
    <body class="min-h-screen bg-grayTheme-light text-grayTheme-dark antialiased">
        <main class="flex min-h-screen">
            <!-- Left: Brand Panel -->
            <div class="relative hidden overflow-hidden lg:flex lg:w-[42%] lg:flex-col bg-primary">
                <!-- Radial glow accents -->
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_80%,_rgba(244,180,0,0.13),_transparent_50%)]"></div>
                <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/5"></div>
                <div class="absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-white/5"></div>
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
                            Your gateway to<br>
                            <span class="text-accent">secure access.</span>
                        </h2>
                        <p class="mt-4 max-w-xs text-sm leading-7 text-white/65">
                            Manage certificates, documents, and employee profiles from one unified, secure platform.
                        </p>

                        <div class="mt-10 space-y-5">
                            <div class="flex items-start gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-accent">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">Certificate Management</p>
                                    <p class="text-xs text-white/60">Track expiry dates &amp; automate notifications</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">Document Archive</p>
                                    <p class="text-xs text-white/60">Secure storage with instant search &amp; retrieval</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">Profile Administration</p>
                                    <p class="text-xs text-white/60">Role-based access control &amp; team management</p>
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
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft">
                                <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </div>
                            <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-grayTheme-dark">Welcome back</h1>
                            <p class="mt-1 text-sm text-grayTheme-medium">Sign in to your Alcatt Portal account</p>
                        </div>

                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            <!-- Email -->
                            <div>
                                <x-input-label for="email" :value="__('Email address')" />
                                <div class="relative mt-1.5">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                        <svg class="h-4 w-4 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <x-text-input id="email" class="block w-full pl-10" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
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
                                    <x-text-input id="password" class="block w-full pl-10 pr-10" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
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

                            <!-- Remember + Forgot -->
                            <div class="flex items-center justify-between gap-4">
                                <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2">
                                    <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-grayTheme-border bg-white text-primary focus:ring-primary/30" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <span class="text-sm text-grayTheme-medium">{{ __('Remember me') }}</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="text-sm font-semibold text-primary transition hover:text-primary-hover" href="{{ route('password.request') }}">
                                        {{ __('Forgot password?') }}
                                    </a>
                                @endif
                            </div>

                            <!-- Submit -->
                            <x-primary-button class="w-full justify-center gap-2 py-3 text-sm font-bold tracking-wide">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                {{ __('Sign in to your account') }}
                            </x-primary-button>

                            @if (Route::has('register'))
                                <p class="pt-1 text-center text-sm text-grayTheme-medium">
                                    {{ __("Don't have an account?") }}
                                    <a class="font-semibold text-primary transition hover:text-primary-hover" href="{{ route('register') }}">{{ __('Create one here') }}</a>
                                </p>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>

                        <div class="mt-16 space-y-10">
                            <article class="max-w-lg">
                                <div class="flex items-start gap-4">
                                    <div class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-white shadow-card">✓</div>
                                    <div>
                                        <h2 class="text-2xl font-extrabold tracking-tight text-grayTheme-dark">Get started quickly</h2>
                                        <p class="mt-2 text-base leading-7 text-grayTheme-medium">Integrate with developer-friendly APIs or choose low-code.</p>
                                    </div>
                                </div>
                            </article>

                            <article class="max-w-lg">
                                <div class="flex items-start gap-4">
                                    <div class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-white shadow-card">✓</div>
                                    <div>
                                        <h2 class="text-2xl font-extrabold tracking-tight text-grayTheme-dark">Support any business model</h2>
                                        <p class="mt-2 text-base leading-7 text-grayTheme-medium">Host code that you don't want to share with the world in private.</p>
                                    </div>
                                </div>
                            </article>

                            <article class="max-w-lg">
                                <div class="flex items-start gap-4">
                                    <div class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-white shadow-card">✓</div>
                                    <div>
                                        <h2 class="text-2xl font-extrabold tracking-tight text-grayTheme-dark">Join millions of businesses</h2>
                                        <p class="mt-2 text-base leading-7 text-grayTheme-medium">Flowbite is trusted by ambitious startups and enterprises of every size.</p>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </section>

                    <section class="w-full">
                        <div class="mx-auto w-full max-w-xl rounded-card border border-grayTheme-border bg-white p-8 shadow-modal sm:p-10">
                            <h1 class="text-3xl font-extrabold tracking-tight text-grayTheme-dark">Welcome back</h1>

                            <div class="mt-6 space-y-4">
                                <x-auth-session-status :status="session('status')" />

                                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                                    @csrf

                                    <div>
                                        <x-input-label for="email" :value="__('Email')" class="text-grayTheme-dark" />
                                        <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter your email" />
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="password" :value="__('Password')" class="text-grayTheme-dark" />
                                        <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    </div>

                                    <div class="flex items-center justify-between gap-4">
                                        <label for="remember_me" class="inline-flex items-center gap-3">
                                            <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-grayTheme-border bg-white text-primary focus:ring-primary/30" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <span class="text-sm text-grayTheme-medium">{{ __('Remember me') }}</span>
                                        </label>

                                        @if (Route::has('password.request'))
                                            <a class="text-sm font-semibold text-primary transition hover:text-primary-hover" href="{{ route('password.request') }}">
                                                {{ __('Forgot password?') }}
                                            </a>
                                        @endif
                                    </div>

                                    <x-primary-button class="w-full justify-center text-base tracking-wide">
                                        {{ __('Sign in to your account') }}
                                    </x-primary-button>

                                    @if (Route::has('register'))
                                        <p class="pt-2 text-sm text-grayTheme-medium">
                                            {{ __('Don\'t have an account yet?') }}
                                            <a class="font-semibold text-primary transition hover:text-primary-hover" href="{{ route('register') }}">{{ __('Sign up here') }}</a>
                                        </p>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </body>
</html>
