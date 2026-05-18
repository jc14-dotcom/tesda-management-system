<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied — Alcatt Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-grayTheme-light text-grayTheme-dark antialiased">
    <main class="relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(43,45,126,0.10),_transparent_40%),radial-gradient(circle_at_bottom_right,_rgba(244,180,0,0.07),_transparent_40%),linear-gradient(135deg,_#f8f9ff_0%,_#f2f4f8_50%,_#f8fafc_100%)]">
        <div class="pointer-events-none absolute inset-0 z-0 bg-[linear-gradient(rgba(107,114,128,0.07)_1px,transparent_1px),linear-gradient(90deg,rgba(107,114,128,0.07)_1px,transparent_1px)] bg-[size:72px_72px] opacity-40"></div>

        <div class="relative z-10 flex min-h-screen flex-col items-center justify-center px-6 text-center">
            <a href="/" class="mb-10 inline-flex items-center gap-3">
                <img src="{{ asset('assets/alcatt-logo.png') }}" class="h-11 w-11 object-contain drop-shadow-[0_0_18px_rgba(43,45,126,0.35)]" alt="Alcatt Portal" />
                <span class="text-xl font-bold tracking-tight text-primary">Alcatt Portal</span>
            </a>

            <div class="w-full max-w-md rounded-2xl bg-white px-8 py-10 shadow-xl ring-1 ring-grayTheme-border">
                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-danger-soft">
                    <svg class="h-8 w-8 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>

                <p class="text-5xl font-extrabold text-danger">403</p>
                <h1 class="mt-2 text-xl font-bold text-grayTheme-dark">Access Denied</h1>
                <p class="mt-3 text-sm text-grayTheme-medium">You don't have permission to view this page. If you believe this is a mistake, please contact your administrator.</p>

                <div class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Sign In
                        </a>
                    @endauth
                    <a href="javascript:history.back()" class="btn-secondary inline-flex items-center gap-2 px-5 py-2.5 text-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Go Back
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
