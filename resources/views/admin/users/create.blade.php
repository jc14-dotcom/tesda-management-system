<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Create Account"
                subtitle="Add a new user or admin account to the system."
                eyebrow="Administration"
            >
                <x-slot:actions>
                    <a class="rounded-full border border-white/30 px-3 py-1 text-sm font-semibold text-white/90 hover:text-white" href="{{ route('admin.users.index') }}">
                        Back to users
                    </a>
                </x-slot:actions>
            </x-page-header>

            <div class="surface p-6">
                @if ($errors->any())
                    <div class="mb-5 rounded-lg bg-danger-soft px-4 py-3 text-sm text-danger">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}" class="grid gap-5 md:grid-cols-2">
                    @csrf

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="name">Full Name</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            class="mt-1 form-input w-full"
                            required
                            maxlength="255"
                            autofocus
                        />
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="email">Email Address</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="mt-1 form-input w-full"
                            required
                            maxlength="255"
                        />
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="role">Role</label>
                        <select id="role" name="role" class="mt-1 form-input w-full">
                            <option value="user" @selected(old('role', 'user') === 'user')>User</option>
                            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                        </select>
                    </div>

                    {{-- Spacer to push password fields to their own row --}}
                    <div class="hidden md:block"></div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="password">Password</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="mt-1 form-input w-full"
                            required
                            minlength="8"
                            autocomplete="new-password"
                        />
                        <p class="mt-1 text-xs text-grayTheme-medium">Minimum 8 characters.</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="password_confirmation">Confirm Password</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="mt-1 form-input w-full"
                            required
                            minlength="8"
                            autocomplete="new-password"
                        />
                    </div>

                    <div class="flex items-center justify-end gap-3 md:col-span-2">
                        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
