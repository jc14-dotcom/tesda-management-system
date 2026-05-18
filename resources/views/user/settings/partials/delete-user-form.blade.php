<section
    class="space-y-0"
    x-data="{ confirmOpen: false }"
>
    {{-- Danger zone header --}}
    <div class="flex items-center gap-3 border-b border-danger/20 bg-danger-soft/40 px-4 py-4 sm:px-8">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-danger-soft">
            <svg class="h-5 w-5 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-danger">{{ __('Danger Zone') }}</h2>
            <p class="text-sm text-danger/70">{{ __('These actions are permanent and cannot be undone.') }}</p>
        </div>
    </div>

    <div class="px-4 py-6 sm:px-8">
        <div class="max-w-xl">
            <h3 class="text-base font-semibold text-grayTheme-dark">{{ __('Delete Account') }}</h3>
            <p class="mt-1 text-sm text-grayTheme-medium">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please download any data you wish to retain before proceeding.') }}
            </p>

            <button
                type="button"
                class="mt-4 btn-danger gap-2"
                @click="confirmOpen = true"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                {{ __('Delete Account') }}
            </button>
        </div>
    </div>

    {{-- Confirmation Modal --}}
    <div
        x-cloak
        x-show="confirmOpen"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4"
        @keydown.escape.window="confirmOpen = false"
        @click.self="confirmOpen = false"
    >
        <div
            class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-danger-soft">
                    <svg class="h-5 w-5 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h3 class="text-base font-bold text-grayTheme-dark">{{ __('Delete your account?') }}</h3>
                    <p class="mt-1 text-sm text-grayTheme-medium">{{ __('This will permanently delete all your data. Enter your password to confirm.') }}</p>
                </div>
            </div>

            <form method="post" action="{{ route('account.profile.destroy') }}" class="mt-5 space-y-4" id="delete-account-form">
                @csrf
                @method('delete')

                <div>
                    <x-input-label for="delete_account_password" :value="__('Your Password')" />
                    <x-text-input
                        id="delete_account_password"
                        name="password"
                        type="password"
                        class="mt-1 block w-full"
                        placeholder="{{ __('Enter your current password') }}"
                        autocomplete="current-password"
                    />
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" class="btn-secondary" @click="confirmOpen = false">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn-danger gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</section>
