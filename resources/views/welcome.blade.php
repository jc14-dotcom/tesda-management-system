<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Alcatt Portal</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-grayTheme-light text-grayTheme-dark antialiased">
        <main class="relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(43,45,126,0.10),_transparent_40%),radial-gradient(circle_at_bottom_right,_rgba(244,180,0,0.07),_transparent_40%),linear-gradient(135deg,_#f8f9ff_0%,_#f2f4f8_50%,_#f8fafc_100%)]">
            <!-- Dot grid -->
            <div class="pointer-events-none absolute inset-0 z-0 bg-[linear-gradient(rgba(107,114,128,0.07)_1px,transparent_1px),linear-gradient(90deg,rgba(107,114,128,0.07)_1px,transparent_1px)] bg-[size:72px_72px] opacity-40"></div>

            <div class="relative z-10 mx-auto flex min-h-screen w-full max-w-7xl flex-col px-6 py-8 lg:px-10">
                <!-- Header -->
                <header class="flex items-center justify-between gap-4">
                    <a href="/" class="inline-flex items-center gap-3 group">
                        <img src="{{ asset('assets/alcatt-logo.png') }}" class="h-11 w-11 object-contain drop-shadow-[0_0_18px_rgba(43,45,126,0.35)] transition group-hover:drop-shadow-[0_0_26px_rgba(43,45,126,0.55)]" alt="Alcatt Portal" />
                        <span class="text-2xl font-extrabold tracking-tight text-grayTheme-dark">Alcatt Portal</span>
                    </a>

                    <nav class="flex items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="rounded-button border border-grayTheme-border bg-white px-5 py-2.5 text-sm font-semibold text-grayTheme-medium shadow-card transition hover:border-primary hover:text-primary">Dashboard</a>
                        @endauth
                    </nav>
                </header>

                <!-- Hero grid -->
                <div class="grid flex-1 items-center gap-12 py-12 lg:grid-cols-[1fr_0.82fr] lg:gap-16">

                    <!-- Left: Hero content -->
                    <section class="max-w-xl">
                        <span class="inline-flex items-center gap-2 rounded-full border border-accent/30 bg-accent-soft px-4 py-1.5 text-sm font-semibold text-[#B67C00]">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Secure Certificate &amp; Document Management
                        </span>

                        <h1 class="mt-6 text-4xl font-black leading-tight tracking-tight text-grayTheme-dark sm:text-5xl">
                            ALCATT registration
                            <span class="text-accent">eSystem</span>
                        </h1>

                        <p class="mt-5 max-w-lg text-lg leading-8 text-grayTheme-medium">
                            A unified portal for organizations to track certificate expiry, archive employee documents, and administer profiles — securely and at scale.
                        </p>

                        <!-- Feature cards -->
                        <div class="mt-10 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-card border border-grayTheme-border bg-white p-4 shadow-card">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent-soft">
                                    <svg class="h-5 w-5 text-[#B67C00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                </div>
                                <p class="mt-3 text-sm font-bold text-grayTheme-dark">Certificates</p>
                                <p class="mt-1 text-xs text-grayTheme-medium">Automated expiry alerts &amp; renewal tracking.</p>
                            </div>

                            <div class="rounded-card border border-grayTheme-border bg-white p-4 shadow-card">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-soft">
                                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="mt-3 text-sm font-bold text-grayTheme-dark">Documents</p>
                                <p class="mt-1 text-xs text-grayTheme-medium">Secure archive with instant search &amp; retrieval.</p>
                            </div>

                            <div class="rounded-card border border-grayTheme-border bg-white p-4 shadow-card">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent-soft">
                                    <svg class="h-5 w-5 text-[#B67C00]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <p class="mt-3 text-sm font-bold text-grayTheme-dark">Team & Admin</p>
                                <p class="mt-1 text-xs text-grayTheme-medium">Role-based access for teams and administrators.</p>
                            </div>
                        </div>
                    </section>

                    <!-- Right: CTA Card -->
                    <section class="w-full">
                        <div class="mx-auto w-full max-w-md overflow-hidden rounded-[20px] border border-grayTheme-border bg-white shadow-modal">
                            <!-- Card header -->
                            <div class="bg-primary px-8 py-8">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('assets/alcatt-logo.png') }}" class="h-11 w-11 object-contain" alt="Alcatt Portal" />
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-widest text-accent">Welcome to</p>
                                        <span class="text-xl font-extrabold tracking-tight text-white">Alcatt Portal</span>
                                    </div>
                                </div>
                                <p class="mt-4 text-sm leading-6 text-white/70">Your secure portal for certificates, documents, and profile administration.</p>

                                <!-- Stats row -->
                                <div class="mt-6 grid grid-cols-3 gap-4 border-t border-white/15 pt-5">
                                    <div class="text-center">
                                        <p class="text-xl font-black text-accent">100%</p>
                                        <p class="mt-0.5 text-[11px] text-white/60">Secure</p>
                                    </div>
                                    <div class="text-center border-x border-white/15">
                                        <p class="text-xl font-black text-accent">24/7</p>
                                        <p class="mt-0.5 text-[11px] text-white/60">Access</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xl font-black text-accent">All</p>
                                        <p class="mt-0.5 text-[11px] text-white/60">In One Place</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Card body -->
                            <div class="space-y-3 px-8 py-7">
                                <a href="{{ route('login') }}" class="flex w-full items-center justify-center gap-2 rounded-button bg-primary py-3 text-sm font-bold text-white shadow-card transition hover:bg-primary-hover">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                    Sign in to your account
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="flex w-full items-center justify-center gap-2 rounded-button border-2 border-grayTheme-border bg-white py-3 text-sm font-bold text-grayTheme-dark shadow-sm transition hover:border-primary hover:text-primary">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                        Create an account
                                    </a>
                                @endif

                                <p class="pt-1 text-center text-xs text-grayTheme-medium">
                                    Protected by SSL encryption &amp; role-based access control
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </body>
</html>
