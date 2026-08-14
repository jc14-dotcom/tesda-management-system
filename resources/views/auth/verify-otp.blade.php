<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Alcatt Portal — Verify OTP</title>

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

                    <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-grayTheme-dark">Verify your account</h1>
                    <p class="mt-2 max-w-xs text-sm leading-6 text-grayTheme-medium">
                        Enter the 6-digit code sent to your email address.
                    </p>
                </div>

                @if (session('status'))
                    <div class="mb-5 rounded-xl bg-success-soft px-4 py-3 text-center text-sm font-semibold text-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-danger/30 bg-danger-soft px-4 py-3 text-sm text-danger">
                        <div class="flex items-center gap-2 font-semibold">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /></svg>
                            Please fix the following errors:
                        </div>
                        <ul class="mt-1.5 list-inside list-disc space-y-0.5 pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.otp.verify') }}" class="space-y-6">
                    @csrf

                    <!-- OTP Input -->
                    <div>
                        <x-input-label for="otp" :value="__('Verification code')" />
                        <div class="relative mt-1.5">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <svg class="h-4 w-4 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <x-text-input id="otp" class="block w-full pl-10" type="text" name="otp" required autofocus maxlength="6" placeholder="123457" />
                        </div>
                        <x-input-error :messages="$errors->get('otp')" class="mt-1.5" />
                    </div>

                    <!-- Resend OTP -->
                    @if (session('otp_resend_pending'))
                        <div class="mb-5 rounded-xl bg-warning-soft px-4 py-3 text-center text-sm font-semibold text-amber-700">
                            A new OTP has been sent. Please check your email.
                        </div>
                    @endif

                    <div class="flex items-center justify-end">
                        <button type="submit" class="btn-primary inline-flex items-center gap-2 py-3 text-sm font-bold tracking-wide">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0-10h-5a2 2 0 01-2-2V5a2 2 0 012-2h5a2 2 0 012 2v3.3m0 0l-5 5"/>
                            </svg>
                            Verify
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-sm text-grayTheme-medium">
                            Don't have an account?
                            <a class="font-semibold text-primary transition hover:text-primary-hover" href="{{ route('register') }}">Create one here</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>