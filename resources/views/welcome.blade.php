<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-grayTheme-light text-grayTheme-dark antialiased">
        <main class="relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(43,45,126,0.12),_transparent_34%),radial-gradient(circle_at_80%_20%,_rgba(43,45,126,0.08),_transparent_28%),linear-gradient(135deg,_#f8f9ff_0%,_#f2f4f8_45%,_#f8fafc_100%)]">
            <div class="pointer-events-none absolute inset-0 z-0 bg-[linear-gradient(rgba(107,114,128,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(107,114,128,0.08)_1px,transparent_1px)] bg-[size:72px_72px] opacity-40"></div>

            <div class="relative z-10 mx-auto flex min-h-screen w-full max-w-7xl flex-col justify-between px-6 py-8 lg:px-10">
                <header class="flex items-center justify-between gap-4">
                    <a href="/" class="inline-flex items-center gap-3 text-grayTheme-dark">
                        <x-application-logo class="h-11 w-11 fill-current text-primary drop-shadow-[0_0_18px_rgba(43,45,126,0.35)]" />
                        <span class="text-3xl font-extrabold tracking-tight">Flowbite</span>
                    </a>

                    <nav class="flex items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="rounded-button border border-grayTheme-border bg-white px-5 py-2.5 text-sm font-semibold text-grayTheme-medium shadow-card transition hover:border-primary hover:text-primary">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-button border border-grayTheme-border bg-white px-5 py-2.5 text-sm font-semibold text-grayTheme-medium shadow-card transition hover:border-primary hover:text-primary">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-button bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-card transition hover:bg-primary-hover">Register</a>
                            @endif
                        @endauth
                    </nav>
                </header>

                <div class="grid flex-1 items-center gap-12 py-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-16">
                    <section class="max-w-xl">
                        <p class="inline-flex rounded-full border border-primary/30 bg-primary-soft px-4 py-1 text-sm font-semibold text-primary shadow-card">Simple, secure access for your team</p>
                        <h1 class="mt-6 text-4xl font-black tracking-tight text-grayTheme-dark sm:text-5xl">Manage accounts with a clean light interface.</h1>
                        <p class="mt-5 max-w-lg text-lg leading-8 text-grayTheme-medium">A focused welcome page and auth flow designed to match your login and register screens, with a lighter look throughout.</p>

                        <div class="mt-10 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-card border border-grayTheme-border bg-white p-5 shadow-card">
                                <p class="text-sm font-semibold text-grayTheme-medium">Fast setup</p>
                                <p class="mt-2 text-base font-semibold text-grayTheme-dark">Get started with the same streamlined layout everywhere.</p>
                            </div>
                            <div class="rounded-card border border-grayTheme-border bg-white p-5 shadow-card">
                                <p class="text-sm font-semibold text-grayTheme-medium">Light mode</p>
                                <p class="mt-2 text-base font-semibold text-grayTheme-dark">Consistent pale surfaces, crisp borders, and blue accents.</p>
                            </div>
                        </div>
                    </section>

                    <section class="w-full">
                        <div class="mx-auto w-full max-w-xl rounded-card border border-grayTheme-border bg-white p-8 shadow-modal sm:p-10">
                            <h2 class="text-3xl font-extrabold tracking-tight text-grayTheme-dark">Welcome page</h2>
                            <p class="mt-3 text-base leading-7 text-grayTheme-medium">Use this landing page as the entry point to your app. It now matches the same light design system as the login and register screens.</p>

                            <div class="mt-8 space-y-4">
                                <a href="{{ route('login') }}" class="flex items-center justify-center rounded-button bg-primary px-5 py-3 text-sm font-semibold text-white shadow-card transition hover:bg-primary-hover">Go to login</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="flex items-center justify-center rounded-button border border-grayTheme-border bg-white px-5 py-3 text-sm font-semibold text-grayTheme-medium shadow-card transition hover:border-primary hover:text-primary">Create an account</a>
                                @endif
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </body>
</html>
