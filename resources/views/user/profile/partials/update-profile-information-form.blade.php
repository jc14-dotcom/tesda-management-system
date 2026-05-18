<section>
    <header class="flex items-center gap-3">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft">
            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h2 class="text-base font-bold text-grayTheme-dark">
                {{ __('Profile Information') }}
            </h2>
            <p class="mt-0.5 text-sm text-grayTheme-medium">
                {{ __("Update your account's profile information and email address.") }}
            </p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('account.profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6" x-data='profileInfoForm({ initialName: @json($user->name), initialEmail: @json($user->email) })' @profile-photo-changed="photoChanged = Boolean($event.detail?.dirty)" @submit.prevent="submitForm($event)">
        @csrf
        @method('patch')

        <div class="grid gap-6 md:grid-cols-[220px_1fr] md:items-start">
            <div class="space-y-4" x-data='profilePhotoPreview({ initialUrl: @json($profile?->profile_photo_url) })' @click.outside="menuOpen = false" @profile-saved.window="if ($event.detail?.profile_photo_url) { originalUrl = $event.detail.profile_photo_url; previewUrl = $event.detail.profile_photo_url }">
                <input id="profile_photo" name="profile_photo" type="file" class="sr-only" accept="image/*" x-ref="profilePhotoInput" @change="selectFile($event)" />

                <div class="group relative mx-auto h-40 w-40">
                    {{-- Circle: click anywhere → open file picker --}}
                    <button
                        type="button"
                        class="block h-40 w-40 cursor-pointer overflow-hidden rounded-full border-2 border-grayTheme-border bg-gray-100 shadow-sm transition duration-200 group-hover:border-primary/40 group-hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary/40 focus:ring-offset-2"
                        @click="triggerUpload()"
                        aria-label="Upload profile photo"
                    >
                        <img
                            x-show="previewUrl"
                            :src="previewUrl"
                            alt="Profile photo"
                            class="h-full w-full object-cover"
                            @if (! $profile?->profile_photo_url) style="display: none;" @endif
                        />
                        <div
                            x-show="!previewUrl"
                            class="flex h-full w-full flex-col items-center justify-center gap-2 text-gray-400"
                            @if ($profile?->profile_photo_url) style="display: none;" @endif
                        >
                            <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <span class="text-xs font-semibold uppercase tracking-wider">No Photo</span>
                        </div>
                    </button>

                    {{-- Hover overlay: camera icon centered, "Remove" pill at bottom --}}
                    <div
                        class="absolute inset-0 flex flex-col items-center justify-center rounded-full bg-black/55 opacity-0 transition-opacity duration-200 group-hover:opacity-100 group-focus-within:opacity-100"
                        @click="triggerUpload()"
                    >
                        <div class="pointer-events-none flex flex-col items-center gap-1.5">
                            <svg class="h-8 w-8 text-white drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                            <span class="text-xs font-bold tracking-wide text-white drop-shadow">Change Photo</span>
                        </div>
                        <button
                            type="button"
                            x-show="previewUrl"
                            class="absolute bottom-5 rounded-full bg-white/20 px-3.5 py-1 text-xs font-semibold text-white ring-1 ring-white/40 backdrop-blur-sm transition hover:bg-danger hover:ring-danger focus:outline-none"
                            @click.stop="confirmRemovePhoto()"
                            style="display: none;"
                        >
                            Remove
                        </button>
                    </div>
                </div>

                <p class="text-center text-xs text-grayTheme-medium">JPG, PNG, WebP, GIF &middot; Max 5 MB</p>

                <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
            </div>

            <div class="space-y-6">
                <div>
                    <x-input-label for="name" :value="__('Nickname')" :required="true" />
                    <x-text-input
                        id="name"
                        name="name"
                        type="text"
                        class="mt-1 block w-full"
                        x-bind:class="showError('name') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        :value="old('name', $user->name)"
                        required
                        autocomplete="name"
                        maxlength="255"
                        x-model.trim="name"
                        @input="updateValidation()"
                        @blur="touched.name = true; updateValidation()"
                        x-bind:aria-invalid="showError('name')"
                        x-bind:aria-describedby="showError('name') ? 'name-error' : null"
                    />
                    <p id="name-error" x-show="showError('name')" class="mt-2 text-sm text-red-600" x-text="errors.name"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" :required="true" />
                    <x-text-input
                        id="email"
                        name="email"
                        type="email"
                        class="mt-1 block w-full"
                        x-bind:class="showError('email') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        :value="old('email', $user->email)"
                        required
                        autocomplete="username"
                        maxlength="255"
                        inputmode="email"
                        x-model.trim="email"
                        @input="updateValidation()"
                        @blur="touched.email = true; updateValidation()"
                        x-bind:aria-invalid="showError('email')"
                        x-bind:aria-describedby="showError('email') ? 'email-error' : null"
                    />
                    <p id="email-error" x-show="showError('email')" class="mt-2 text-sm text-red-600" x-text="errors.email"></p>
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
            <x-primary-button class="gap-2" x-bind:disabled="loading || !isDirty() || hasErrors()" x-bind:class="(loading || !isDirty() || hasErrors()) ? 'opacity-60 cursor-not-allowed' : ''">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Save changes') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-green-600"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>

    <form id="profile-photo-remove-form" method="post" action="{{ route('account.profile.photo.remove') }}" class="hidden">
        @csrf
        @method('patch')
    </form>
</section>
