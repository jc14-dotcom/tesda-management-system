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

    <form method="post" action="{{ route('account.profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6" x-data='profileInfoForm({ initialName: @json($user->name), initialEmail: @json($user->email) })' @profile-photo-changed="photoChanged = Boolean($event.detail?.dirty)" @submit.prevent="submitForm($event)">
        @csrf
        @method('patch')

        <div class="grid gap-6 md:grid-cols-[220px_1fr] md:items-start">
            <div class="space-y-4" x-data='profilePhotoPreview({ initialUrl: @json($profile?->profile_photo_url) })' @click.outside="menuOpen = false" @profile-saved.window="if ($event.detail?.profile_photo_url) { originalUrl = $event.detail.profile_photo_url; previewUrl = $event.detail.profile_photo_url }">
                <input id="profile_photo" name="profile_photo" type="file" class="sr-only" accept="image/*" x-ref="profilePhotoInput" @change="selectFile($event)" />

                <div class="group relative mx-auto h-40 w-40">
                    <button
                        type="button"
                        class="block h-40 w-40 overflow-hidden rounded-full border border-grayTheme-border bg-gray-100 shadow-sm transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary/30"
                        @click="toggleMenu()"
                        aria-label="Profile picture actions"
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
                            class="flex h-full w-full items-center justify-center text-sm font-semibold uppercase tracking-wide text-gray-400"
                            @if ($profile?->profile_photo_url) style="display: none;" @endif
                        >No Photo</div>
                    </button>

                    <div
                        class="absolute inset-0 flex items-end justify-center rounded-full bg-grayTheme-dark/55 p-2 opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100"
                        x-bind:class="menuOpen ? 'opacity-100' : ''"
                    >
                        <div class="flex flex-col items-center gap-2">
                            <button type="button" class="rounded-full bg-white/95 px-3 py-1.5 text-xs font-semibold leading-none text-primary shadow-sm transition hover:bg-white" @click.stop="triggerUpload()">
                                Upload new photo
                            </button>
                            <button type="button" class="rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold leading-none text-white ring-1 ring-white/30 transition hover:bg-white/25" x-show="previewUrl" @click.stop="confirmRemovePhoto()">
                                Remove photo
                            </button>
                        </div>
                    </div>
                </div>

                <p class="text-center text-sm font-semibold text-primary">Click or hover the circle to manage your photo</p>
                <p class="text-center text-xs text-gray-500">Accepted formats: JPG, PNG, WebP, GIF, and other image files.</p>

                <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
            </div>

            <div class="space-y-6">
                <div>
                    <x-input-label for="name" :value="__('Name')" />
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
                    <x-input-label for="email" :value="__('Email')" />
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
            <x-primary-button x-bind:disabled="loading || !isDirty() || hasErrors()" x-bind:class="(loading || !isDirty() || hasErrors()) ? 'opacity-60 cursor-not-allowed' : ''">
                {{ __('Save') }}
            </x-primary-button>

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

    <form id="profile-photo-remove-form" method="post" action="{{ route('account.profile.photo.remove') }}" class="hidden">
        @csrf
        @method('patch')
    </form>
</section>
