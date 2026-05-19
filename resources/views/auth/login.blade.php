<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Alcatt Portal — Sign In</title>

        <script>
        (function(){
            if(window.__toastQueueInstalled) return;
            window.__toastQueueInstalled = true;
            window.__pendingToasts = window.__pendingToasts || [];
            window.__toastReady = false;
            window.addEventListener('show-toast', function(e){
                if(!window.__toastReady){
                    window.__pendingToasts.push(e.detail || {});
                }
            });
        }());
        </script>

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
    <body class="h-dvh overflow-hidden bg-grayTheme-light text-grayTheme-dark antialiased">
        <main class="flex h-dvh overflow-hidden">
            <!-- Left: Brand Panel -->
            <div class="relative hidden overflow-hidden lg:flex lg:w-[42%] lg:flex-col bg-primary">
                <!-- Radial glow accents -->
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_80%,_rgba(244,180,0,0.13),_transparent_50%)]"></div>
                <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/5"></div>
                <div class="absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-white/5"></div>
                <!-- Dot grid -->
                <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.10)_1px,transparent_1px)] bg-[size:28px_28px]"></div>

                <div class="relative z-10 flex h-full flex-col px-10 py-10">
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

                        <div class="mt-8 space-y-4">
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
            <div class="flex flex-1 flex-col items-center justify-center overflow-y-auto px-6 py-8 lg:px-12 bg-[radial-gradient(circle_at_top_right,_rgba(43,45,126,0.06),_transparent_40%),linear-gradient(135deg,_#f8f9ff_0%,_#f3f4f6_100%)]">
                <!-- Mobile logo -->
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <img src="{{ asset('assets/alcatt-logo.png') }}" class="h-10 w-10 object-contain" alt="Alcatt Portal" />
                    <span class="text-xl font-extrabold text-grayTheme-dark">Alcatt Portal</span>
                </div>

                <div class="w-full max-w-md">
                    <div class="rounded-[20px] border border-grayTheme-border bg-white p-7 shadow-modal sm:p-8"
                         x-data="{
                             locked: {{ $errors->has('login_lockout') ? 'true' : 'false' }},
                             seconds: {{ session('lockout_seconds', 0) }},
                             dpaOpen: false,
                             get timerLabel() {
                                 const m = Math.floor(this.seconds / 60);
                                 const s = this.seconds % 60;
                                 return m > 0 ? m + ':' + String(s).padStart(2, '0') : this.seconds + 's';
                             },
                             init() {
                                 if (!this.locked || this.seconds <= 0) return;
                                 const t = setInterval(() => {
                                     if (this.seconds > 0) { this.seconds--; }
                                     else { this.locked = false; clearInterval(t); }
                                 }, 1000);
                             }
                         }">
                        <!-- Heading -->
                        <div class="mb-6">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft">
                                <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </div>
                            <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-grayTheme-dark">Welcome back</h1>
                            <p class="mt-1 text-sm text-grayTheme-medium">Sign in to your Alcatt Portal account</p>
                        </div>

                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        {{-- Pending approval banner --}}
                        @if(session('account_pending'))
                            <div class="mb-5 rounded-xl border border-accent/30 bg-accent-soft px-4 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-accent/20">
                                        <svg class="h-4 w-4 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-amber-800">Account Pending Approval</p>
                                        <p class="mt-0.5 text-sm text-amber-700">Your account has been created and is awaiting administrator approval. You will receive an email at your registered address once your account is approved.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Lockout banner with live countdown --}}
                        @if ($errors->has('login_lockout'))
                            <div x-show="locked" class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-100">
                                        <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-red-800">Account Temporarily Locked</p>
                                        <p class="mt-0.5 text-sm text-red-700">{{ $errors->first('login_lockout') }}</p>
                                        <p class="mt-2 text-xs font-medium text-red-600">Try again in <span class="font-mono font-bold" x-text="timerLabel"></span></p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Warning: approaching lockout --}}
                        @if ($errors->has('login_warning'))
                            <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100">
                                        <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-amber-800">{{ $errors->first('login_warning') }}</p>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="space-y-4">
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

                            <!-- Forgot password -->
                            <div class="flex items-center justify-end">
                                @if (Route::has('password.request'))
                                    <a class="text-sm font-semibold text-primary transition hover:text-primary-hover" href="{{ route('password.request') }}">
                                        {{ __('Forgot password?') }}
                                    </a>
                                @endif
                            </div>

                            <!-- Submit -->
                            <x-primary-button class="w-full justify-center gap-2 py-3 text-sm font-bold tracking-wide"
                                x-bind:disabled="locked"
                                x-bind:class="locked ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''">
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

                            {{-- DPA link --}}
                            <div class="border-t border-grayTheme-border pt-3 text-center">
                                <button
                                    type="button"
                                    @click="dpaOpen = true"
                                    class="inline-flex items-center gap-1.5 text-xs text-grayTheme-medium transition hover:text-primary"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                    </svg>
                                    Data Privacy Act &amp; Terms of Use
                                </button>
                            </div>
                        </form>

                        {{-- DPA Modal --}}
                        <div
                            x-show="dpaOpen"
                            x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center p-4"
                            @keydown.escape.window="dpaOpen = false"
                        >
                            {{-- Backdrop --}}
                            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="dpaOpen = false"></div>

                            {{-- Panel --}}
                            <div class="relative z-10 flex w-full max-w-2xl flex-col rounded-2xl border border-grayTheme-border bg-white shadow-modal">
                                {{-- Modal header --}}
                                <div class="flex shrink-0 items-center justify-between border-b border-grayTheme-border px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-soft">
                                            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-base font-bold text-grayTheme-dark">Data Privacy Act &amp; Terms of Use</h2>
                                            <p class="text-xs text-grayTheme-medium">Republic Act No. 10173 &bull; Alcatt Portal</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="dpaOpen = false" class="rounded-lg p-1.5 text-grayTheme-medium transition hover:bg-grayTheme-light hover:text-grayTheme-dark">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Scrollable content --}}
                                <div class="flex-1 overflow-y-auto px-6 py-5" style="max-height: 65vh">
                                    @include('partials.dpa-content')
                                </div>

                                {{-- Modal footer --}}
                                <div class="shrink-0 border-t border-grayTheme-border px-6 py-4">
                                    <button type="button" @click="dpaOpen = false" class="btn-primary w-full justify-center gap-2">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none" aria-live="polite" aria-atomic="false"></div>

        @if(session('restore_success'))
        <script data-turbo-eval="true">
            window.Turbo?.cache?.clear?.();
            window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Database Restored',message:{{ Js::from(session('restore_success')) }}}}));
        </script>
        @endif
    </body>
</html>
