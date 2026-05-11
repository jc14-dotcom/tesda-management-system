<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-grayTheme-light text-grayTheme-dark antialiased">
        <main class="relative min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(43,45,126,0.12),_transparent_34%),radial-gradient(circle_at_80%_20%,_rgba(43,45,126,0.08),_transparent_28%),linear-gradient(135deg,_#f8f9ff_0%,_#f2f4f8_45%,_#f8fafc_100%)]">
            <div class="pointer-events-none absolute inset-0 z-0 bg-[linear-gradient(rgba(107,114,128,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(107,114,128,0.08)_1px,transparent_1px)] bg-[size:72px_72px] opacity-40"></div>

            <div class="relative z-10 mx-auto flex min-h-screen w-full max-w-7xl flex-col justify-between px-6 py-8 lg:px-10">
                <div class="grid flex-1 items-center gap-12 lg:grid-cols-[1.05fr_0.95fr] lg:gap-16">
                    <section class="max-w-xl">
                        <a href="/" class="inline-flex items-center gap-3 text-grayTheme-dark">
                            <x-application-logo class="h-11 w-11 fill-current text-primary drop-shadow-[0_0_18px_rgba(43,45,126,0.35)]" />
                            <span class="text-3xl font-extrabold tracking-tight">Flowbite</span>
                        </a>

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
