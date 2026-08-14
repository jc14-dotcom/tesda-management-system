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
                        Enter the 6-digit verification code sent to your email address.
                    </p>
                </div>

                @if (session('status') == 'otp-sent')
                    <div class="mb-5 rounded-xl bg-success-soft px-4 py-3 text-center text-sm font-semibold text-success">
                        A new 6-digit verification code has been sent to your email address.
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.otp.verify') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="otp" :value="__('Verification code')" />
                        <div class="relative mt-1.5">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <svg class="h-4 w-4 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <x-text-input id="otp" class="block w-full pl-10" type="text" name="otp" required autofocus maxlength="6" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" placeholder="123456" />
                        </div>
                        <x-input-error :messages="$errors->get('otp')" class="mt-1.5" />
                    </div>

                    <div>
                        <button type="submit" class="btn-primary w-full justify-center gap-2 py-3 text-sm font-bold tracking-wide">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Verify
                        </button>
                    </div>
                </form>

                <div class="mt-4 space-y-4 text-center text-sm text-grayTheme-medium">
                    <p>The code expires in {{ config('auth.verification_otp.expire', 10) }} minutes.</p>

                    <form method="POST" action="{{ route('verification.send') }}" class="flex items-center justify-center gap-1.5">
                        @csrf
                        <span>Didn't receive it?</span>
                        <button type="submit" class="font-semibold text-primary hover:underline">Send a new code</button>
                    </form>

                    <div class="text-center">
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="font-semibold text-primary hover:underline">Log out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
