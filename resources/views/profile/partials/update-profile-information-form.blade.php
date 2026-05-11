<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid gap-6 md:grid-cols-[220px_1fr] md:items-start">
            <div class="space-y-4">
                <input id="profile_photo" name="profile_photo" type="file" class="sr-only" accept="image/*" />

                <label for="profile_photo" class="block cursor-pointer select-none text-center">
                    <div class="mx-auto h-40 w-40 overflow-hidden rounded-full border border-grayTheme-border bg-gray-100 shadow-sm transition hover:shadow-md">
                        @if ($profile?->profile_photo_url)
                            <img src="{{ $profile->profile_photo_url }}" alt="Profile photo" class="h-full w-full object-cover" />
                        @else
                            <div class="flex h-full w-full items-center justify-center text-sm font-semibold uppercase tracking-wide text-gray-400">No Photo</div>
                        @endif
                    </div>

                    <span class="mt-3 block text-sm font-semibold text-primary hover:text-primary-hover">Click the circle to choose a photo</span>
                    <span class="mt-1 block text-xs text-gray-500">Accepted formats: JPG, PNG, WebP, GIF, and other image files.</span>
                </label>

                <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
            </div>

            <div class="space-y-6">
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div>
                            <p class="text-sm mt-2 text-slate-700">
                                {{ __('Your email address is unverified.') }}

                                <button form="send-verification" class="underline text-sm text-slate-600 hover:text-slate-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary/30">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 font-medium text-sm text-green-600">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
