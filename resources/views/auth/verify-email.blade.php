<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Alcatt Portal &mdash; Verify Email</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen antialiased bg-[radial-gradient(circle_at_top_right,_rgba(43,45,126,0.07),_transparent_40%),linear-gradient(135deg,_#f8f9ff_0%,_#f3f4f6_100%)]">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12">

            <!-- Card -->
            <div class="w-full max-w-md rounded-[20px] border border-grayTheme-border bg-white p-8 shadow-modal sm:p-10">

                <!-- Header -->
                <div class="mb-8 flex flex-col items-center text-center">
                    <img src="{{ asset('assets/alcatt-logo.png') }}" alt="Alcatt Portal" class="h-14 w-14 object-contain" />

                    <div class="mt-5 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>

                    <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-grayTheme-dark">Verify your email</h1>
                    <p class="mt-2 max-w-xs text-sm leading-6 text-grayTheme-medium">
                        Thanks for registering! Please verify your email address by clicking the link we sent you.
                    </p>
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-5 rounded-xl bg-success-soft px-4 py-3 text-center text-sm font-semibold text-success">
                        A new verification link has been sent to your email address.
                    </div>
                @endif

                <div class="space-y-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <x-primary-button class="w-full justify-center gap-2 py-3 text-sm font-bold tracking-wide">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ __('Resend Verification Email') }}
                        </x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-button border border-grayTheme-border bg-white py-2.5 text-sm font-semibold text-grayTheme-medium shadow-card transition hover:border-danger hover:text-danger">
                            {{ __('Sign Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
