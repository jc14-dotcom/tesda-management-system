<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Alcatt Portal — Data Privacy Act &amp; Terms of Use</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-screen overflow-hidden bg-grayTheme-light text-grayTheme-dark antialiased">
        <main class="flex h-full">

            {{-- Left: Brand Panel --}}
            <div class="relative hidden overflow-hidden lg:flex lg:w-[36%] lg:flex-col bg-primary">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_80%,_rgba(244,180,0,0.13),_transparent_50%)]"></div>
                <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/5"></div>
                <div class="absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-white/5"></div>
                <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.10)_1px,transparent_1px)] bg-[size:28px_28px]"></div>

                <div class="relative z-10 flex h-full flex-col px-10 py-10">
                    <a href="/" class="inline-flex items-center gap-3">
                        <img src="{{ asset('assets/alcatt-logo.png') }}" class="h-10 w-10 object-contain" alt="Alcatt Portal" />
                        <span class="text-xl font-extrabold tracking-tight text-white">Alcatt Portal</span>
                    </a>

                    <div class="flex flex-1 flex-col justify-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm">
                            <svg class="h-8 w-8 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        </div>

                        <h2 class="mt-6 text-3xl font-black leading-snug tracking-tight text-white">
                            Your privacy<br>
                            <span class="text-accent">matters to us.</span>
                        </h2>
                        <p class="mt-4 max-w-xs text-sm leading-7 text-white/65">
                            We are committed to protecting your personal information in accordance with Republic Act No. 10173, the Data Privacy Act of 2012.
                        </p>

                        <div class="mt-8 space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-accent">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">Secure by Design</p>
                                    <p class="text-xs text-white/60">Encrypted storage &amp; access controls</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">Your Rights Protected</p>
                                    <p class="text-xs text-white/60">Full data subject rights under RA 10173</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">Transparent Processing</p>
                                    <p class="text-xs text-white/60">Clear purpose &amp; lawful basis for all data use</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-white/40">© {{ date('Y') }} Alcatt Portal. All rights reserved.</p>
                </div>
            </div>

            {{-- Right: Content Panel --}}
            <div class="flex flex-1 h-full flex-col items-center justify-center overflow-hidden px-6 py-6 lg:px-10 bg-[radial-gradient(circle_at_top_right,_rgba(43,45,126,0.06),_transparent_40%),linear-gradient(135deg,_#f8f9ff_0%,_#f3f4f6_100%)]"
                 x-data="{
                     scrolled: false,
                     agreed: false,
                     checkScroll(el) {
                         if (el.scrollHeight - el.scrollTop <= el.clientHeight + 80) {
                             this.scrolled = true;
                         }
                     }
                 }">

                {{-- Mobile logo --}}
                <div class="mb-4 flex w-full max-w-2xl items-center gap-3 lg:hidden">
                    <img src="{{ asset('assets/alcatt-logo.png') }}" class="h-8 w-8 object-contain" alt="Alcatt Portal" />
                    <span class="text-lg font-extrabold text-grayTheme-dark">Alcatt Portal</span>
                </div>

                <div class="flex w-full max-w-2xl flex-col overflow-hidden" style="max-height: calc(100vh - 5rem)">
                    {{-- Card header --}}
                    <div class="shrink-0 rounded-t-[20px] border border-b-0 border-grayTheme-border bg-white px-7 py-5 sm:px-8">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft">
                                <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-xl font-extrabold tracking-tight text-grayTheme-dark">Data Privacy Act &amp; Terms of Use</h1>
                                <p class="mt-1 text-sm text-grayTheme-medium">
                                    Please read this document carefully before continuing. Scroll to the bottom to enable the agreement checkbox.
                                </p>
                            </div>
                        </div>

                        {{-- Scroll hint --}}
                        <div x-show="!scrolled" class="mt-4 flex items-center gap-2 rounded-lg border border-accent/30 bg-accent-soft px-3 py-2 text-xs font-medium text-amber-700">
                            <svg class="h-4 w-4 shrink-0 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                            Scroll down to read the full document and enable the agreement option.
                        </div>
                        <div x-show="scrolled" x-cloak class="mt-4 flex items-center gap-2 rounded-lg border border-success/30 bg-success-soft px-3 py-2 text-xs font-medium text-success">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            You have read the document. Please confirm your agreement below.
                        </div>
                    </div>

                    {{-- Scrollable content --}}
                    <div
                        class="min-h-0 flex-1 overflow-y-auto border border-b-0 border-grayTheme-border bg-white px-7 py-5 sm:px-8"
                        @scroll="checkScroll($event.target)"
                    >
                        @include('partials.dpa-content')
                    </div>

                    {{-- Agreement form --}}
                    <div class="shrink-0 rounded-b-[20px] border border-grayTheme-border bg-white px-7 py-5 shadow-modal sm:px-8">
                        @if ($errors->any())
                            <div class="mb-4 flex items-center gap-3 rounded-lg border border-danger/30 bg-danger-soft px-4 py-3 text-sm font-medium text-danger">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                </svg>
                                {{ $errors->first('agree') ?? 'You must agree to continue.' }}
                            </div>
                        @endif

                        {{-- Logout form (separate, outside the agree form) --}}
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>

                        <form id="dpa-agree-form" method="POST" action="{{ route('dpa.accept.store') }}">
                            @csrf

                            {{-- Checkbox --}}
                            <label
                                class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition"
                                :class="scrolled ? 'border-primary/30 bg-primary-soft/30 hover:bg-primary-soft/50' : 'cursor-not-allowed border-grayTheme-border bg-grayTheme-light opacity-60'"
                            >
                                <input
                                    type="checkbox"
                                    name="agree"
                                    value="1"
                                    x-model="agreed"
                                    :disabled="!scrolled"
                                    class="mt-0.5 h-4 w-4 shrink-0 rounded border-grayTheme-border text-primary focus:ring-primary disabled:cursor-not-allowed"
                                />
                                <span class="text-sm leading-relaxed text-grayTheme-dark">
                                    I have read, understood, and agree to the <strong>Data Privacy Act of 2012 (RA 10173)</strong> Privacy Notice and the <strong>Alcatt Portal Terms of Use</strong>. I consent to the collection and processing of my personal information as described above.
                                </span>
                            </label>

                            {{-- Buttons --}}
                            <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <button
                                    type="button"
                                    onclick="document.getElementById('logout-form').submit()"
                                    class="inline-flex items-center gap-2 rounded-button border border-grayTheme-border bg-white px-4 py-2.5 text-sm font-semibold text-grayTheme-dark shadow-sm transition hover:bg-grayTheme-light"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Decline &amp; Log Out
                                </button>

                                <button
                                    type="submit"
                                    class="btn-primary gap-2 transition"
                                    :class="(!scrolled || !agreed) ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''"
                                    :disabled="!scrolled || !agreed"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                    I Agree &amp; Continue
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Note --}}
                <p class="mt-3 shrink-0 text-center text-xs text-grayTheme-medium">
                    You must agree to continue using Alcatt Portal. For questions, contact your system administrator.
                </p>
            </div>
        </main>
    </body>
</html>
