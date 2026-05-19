<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Create Account"
                subtitle="Add a new user or admin account to the system."
                eyebrow="Administration"
            >
                <x-slot:actions>
                    <a class="inline-flex items-center gap-1.5 rounded-full border border-white/30 px-3 py-1 text-sm font-semibold text-white/90 transition hover:text-white" href="{{ route('admin.users.index') }}">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Back to users
                    </a>
                </x-slot:actions>
            </x-page-header>

            <div class="surface p-6"
                 x-data="{
                     password: '',
                     confirm: '',
                     showPass: false,
                     showConfirm: false,
                     get hasMinLength() { return this.password.length >= 8; },
                     get hasUpper()     { return /[A-Z]/.test(this.password); },
                     get hasLower()     { return /[a-z]/.test(this.password); },
                     get hasNumber()    { return /[0-9]/.test(this.password); },
                     get hasSpecial()   { return /[^A-Za-z0-9]/.test(this.password); },
                     get strength() {
                         let s = 0;
                         if (this.hasMinLength) s++;
                         if (this.hasUpper)     s++;
                         if (this.hasLower)     s++;
                         if (this.hasNumber)    s++;
                         if (this.hasSpecial)   s++;
                         return s;
                     },
                     get strengthLabel() {
                         if (!this.password.length) return '';
                         return ['', 'Very Weak', 'Weak', 'Fair', 'Strong', 'Very Strong'][this.strength] ?? 'Very Strong';
                     },
                     get strengthColor() {
                         if (this.strength <= 1) return 'bg-danger';
                         if (this.strength === 2) return 'bg-warning';
                         if (this.strength === 3) return 'bg-accent';
                         return 'bg-success';
                     },
                     get strengthLabelColor() {
                         if (this.strength <= 1) return 'text-danger';
                         if (this.strength === 2) return 'text-warning';
                         if (this.strength === 3) return 'text-accent';
                         return 'text-success';
                     },
                     get strengthWidthPct() { return (this.strength / 5 * 100) + '%'; },
                     get requirements() {
                         return [
                             { met: this.hasMinLength, label: 'At least 8 characters' },
                             { met: this.hasUpper,     label: 'Uppercase letter (A–Z)' },
                             { met: this.hasLower,     label: 'Lowercase letter (a–z)' },
                             { met: this.hasNumber,    label: 'Number (0–9)' },
                             { met: this.hasSpecial,   label: 'Special character (!@#…)' },
                         ];
                     },
                     get passwordsMatch()    { return this.confirm.length > 0 && this.password === this.confirm; },
                     get passwordsMismatch() { return this.confirm.length > 0 && this.password !== this.confirm; }
                 }">

                @if ($errors->any())
                    <div class="mb-5 flex items-start gap-2.5 rounded-xl border border-danger/20 bg-danger-soft px-4 py-3.5 text-sm text-danger">
                        <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="grid gap-5 md:grid-cols-2">

                        {{-- Full Name --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="name">
                                Full Name<span class="ml-0.5 text-danger" aria-hidden="true">*</span>
                            </label>
                            <div class="relative mt-1">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                    </svg>
                                </span>
                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="form-input w-full pl-9"
                                    required
                                    maxlength="255"
                                    autofocus
                                    placeholder="e.g. Juan dela Cruz"
                                />
                            </div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="email">
                                Email Address<span class="ml-0.5 text-danger" aria-hidden="true">*</span>
                            </label>
                            <div class="relative mt-1">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                    </svg>
                                </span>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="form-input w-full pl-9"
                                    required
                                    maxlength="255"
                                    placeholder="user@example.com"
                                    oninput="this.value = this.value.toLowerCase()"
                                />
                            </div>
                        </div>

                        {{-- Role --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="role">
                                Role
                            </label>
                            <div class="relative mt-1">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                    </svg>
                                </span>
                                <select id="role" name="role" class="form-input w-full pl-9 pr-8">
                                    <option value="user"  @selected(old('role', 'user') === 'user')>User</option>
                                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                                </select>
                            </div>
                        </div>

                        {{-- Spacer --}}
                        <div class="hidden md:block"></div>

                        {{-- Password --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="password">
                                Password<span class="ml-0.5 text-danger" aria-hidden="true">*</span>
                            </label>
                            <div class="relative mt-1">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                    </svg>
                                </span>
                                <input
                                    id="password"
                                    :type="showPass ? 'text' : 'password'"
                                    name="password"
                                    class="form-input w-full pl-9 pr-10"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Create a strong password"
                                    x-model="password"
                                />
                                <button
                                    type="button"
                                    @click="showPass = !showPass"
                                    :aria-label="showPass ? 'Hide password' : 'Show password'"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-grayTheme-medium transition hover:text-grayTheme-dark focus:outline-none"
                                >
                                    {{-- Eye-off (password hidden) --}}
                                    <svg x-show="!showPass" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                    </svg>
                                    {{-- Eye-on (password visible) --}}
                                    <svg x-show="showPass" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Live strength meter --}}
                            <div x-show="password.length > 0" x-cloak class="mt-3 space-y-2.5">
                                {{-- Bar + label --}}
                                <div class="flex items-center gap-3">
                                    <div class="relative h-1.5 flex-1 overflow-hidden rounded-full bg-grayTheme-hover">
                                        <div
                                            class="absolute inset-y-0 left-0 rounded-full transition-all duration-300"
                                            :class="strengthColor"
                                            :style="'width:' + strengthWidthPct"
                                        ></div>
                                    </div>
                                    <span class="w-20 text-right text-xs font-semibold transition" :class="strengthLabelColor" x-text="strengthLabel"></span>
                                </div>
                                {{-- Requirements checklist --}}
                                <ul class="grid grid-cols-2 gap-x-4 gap-y-1">
                                    <template x-for="req in requirements" :key="req.label">
                                        <li class="flex items-center gap-1.5 text-xs transition-colors duration-150" :class="req.met ? 'text-success' : 'text-grayTheme-medium'">
                                            {{-- Check circle --}}
                                            <svg x-show="req.met" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{-- Empty circle --}}
                                            <svg x-show="!req.met" class="h-3.5 w-3.5 shrink-0 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <circle cx="12" cy="12" r="9"/>
                                            </svg>
                                            <span x-text="req.label"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="password_confirmation">
                                Confirm Password<span class="ml-0.5 text-danger" aria-hidden="true">*</span>
                            </label>
                            <div class="relative mt-1">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                    </svg>
                                </span>
                                <input
                                    id="password_confirmation"
                                    :type="showConfirm ? 'text' : 'password'"
                                    name="password_confirmation"
                                    :class="passwordsMismatch ? 'border-danger/50 focus:ring-danger/30' : (passwordsMatch ? 'border-success/50 focus:ring-success/30' : '')"
                                    class="form-input w-full pl-9 pr-10"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Re-enter your password"
                                    x-model="confirm"
                                />
                                <button
                                    type="button"
                                    @click="showConfirm = !showConfirm"
                                    :aria-label="showConfirm ? 'Hide password' : 'Show password'"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-grayTheme-medium transition hover:text-grayTheme-dark focus:outline-none"
                                >
                                    <svg x-show="!showConfirm" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                    </svg>
                                    <svg x-show="showConfirm" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </button>
                            </div>
                            {{-- Match indicator --}}
                            <div x-show="confirm.length > 0" x-cloak class="mt-1.5 text-xs">
                                <span x-show="passwordsMatch" class="flex items-center gap-1.5 text-success">
                                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Passwords match
                                </span>
                                <span x-show="passwordsMismatch" class="flex items-center gap-1.5 text-danger">
                                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Passwords do not match
                                </span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 md:col-span-2">
                            <a href="{{ route('admin.users.index') }}" class="btn-secondary inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Cancel
                            </a>
                            <button type="submit" class="btn-primary inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0112 21c-2.278 0-4.413-.74-6-2z"/></svg>
                                Create Account
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
