<x-app-layout>
    <div class="py-12" x-data="{
        confirmOpen: false,
        confirmTitle: '',
        confirmMessage: '',
        askDelete(title, message) {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.confirmOpen = true;
        }
    }">
        <div class="page-container space-y-6">
            <x-page-header
                title="User Details"
                subtitle="View the selected user account and documents."
                eyebrow="Administration"
            >
                <x-slot:actions>
                    <a class="rounded-full border border-white/30 px-3 py-1 text-sm font-semibold text-white/90 hover:text-white" href="{{ route('admin.users.index') }}">
                        Back to users
                    </a>
                </x-slot:actions>
            </x-page-header>

            <div class="surface p-6">
                {{-- Section header --}}
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft">
                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-grayTheme-dark">Account</h3>
                        <p class="mt-0.5 text-sm text-grayTheme-medium">Account identity and access information.</p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-6 sm:flex-row sm:items-start">
                    {{-- Profile photo / initial avatar --}}
                    <div class="flex shrink-0 flex-col items-center gap-2">
                        @if ($user->profile?->profile_photo_url)
                            <img
                                src="{{ $user->profile->profile_photo_url }}"
                                alt="{{ $user->name }}"
                                class="h-28 w-28 rounded-full object-cover shadow-md ring-2 ring-grayTheme-border"
                            />
                        @else
                            <div class="flex h-28 w-28 items-center justify-center rounded-full bg-primary-soft text-3xl font-bold text-primary shadow-md ring-2 ring-grayTheme-border">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        @php $accountStatus = $user->profile?->status ?? 'active'; @endphp
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold
                            {{ $accountStatus === 'active' ? 'bg-success-soft text-success' : ($accountStatus === 'pending' ? 'bg-accent-soft text-amber-700' : 'bg-danger-soft text-danger') }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $accountStatus === 'active' ? 'bg-success' : ($accountStatus === 'pending' ? 'bg-accent' : 'bg-danger') }}"></span>
                            {{ $accountStatus === 'pending' ? 'Pending Approval' : ucfirst($accountStatus) }}
                        </span>
                    </div>

                    {{-- Account fields --}}
                    <div class="grid flex-1 gap-3 sm:grid-cols-2">
                        <div class="flex items-start gap-3 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3">
                            <span class="mt-0.5 shrink-0 text-grayTheme-medium">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </span>
                            <div class="min-w-0">
                                <div class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Display Name</div>
                                <div class="mt-0.5 truncate font-medium text-grayTheme-dark">{{ $user->name }}</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3">
                            <span class="mt-0.5 shrink-0 text-grayTheme-medium">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </span>
                            <div class="min-w-0">
                                <div class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Email</div>
                                <div class="mt-0.5 truncate font-medium text-grayTheme-dark">{{ $user->email }}</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3">
                            <span class="mt-0.5 shrink-0 text-grayTheme-medium">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                            </span>
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Role</div>
                                <div class="mt-1">
                                    @if ($user->hasRole('admin'))
                                        <span class="inline-flex items-center gap-1 rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-semibold text-primary">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-grayTheme-hover px-2.5 py-0.5 text-xs font-semibold text-grayTheme-dark">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            User
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @php
                            $fullName = collect([$user->profile?->first_name, $user->profile?->middle_name, $user->profile?->last_name])
                                ->filter()->implode(' ');
                            if ($user->profile?->suffix) $fullName .= ', ' . ucfirst($user->profile->suffix);
                        @endphp
                        @if ($fullName)
                        <div class="flex items-start gap-3 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3">
                            <span class="mt-0.5 shrink-0 text-grayTheme-medium">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2" /></svg>
                            </span>
                            <div class="min-w-0">
                                <div class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Full Name</div>
                                <div class="mt-0.5 font-medium text-grayTheme-dark">{{ $fullName }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Manage Account --}}
            <div class="surface p-6">
                {{-- Section header --}}
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-accent-soft">
                        <svg class="h-5 w-5 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-grayTheme-dark">Manage Account</h3>
                        <p class="mt-0.5 text-sm text-grayTheme-medium">Edit account credentials and access level.</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mt-4 rounded-lg border border-danger/30 bg-danger-soft px-4 py-3 text-sm text-danger">
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

                <div x-data="{
                        submitting: false,
                        dirty: false,
                        orig: {
                            name:   {{ Js::from(old('name',  $user->name)) }},
                            email:  {{ Js::from(old('email', $user->email)) }},
                            role:   {{ Js::from($user->hasRole('admin') ? 'admin' : 'user') }},
                            status: {{ Js::from($user->profile?->status ?? 'active') }},
                        },
                        check(field, val) { this.dirty = Object.keys(this.orig).some(k => document.getElementById('mgmt_' + k)?.value !== this.orig[k]); }
                    }"
                    @input="dirty = ['mgmt_name','mgmt_email','mgmt_role','mgmt_status'].some(id => { const el = document.getElementById(id); return el && el.value !== orig[id === 'mgmt_name' ? 'name' : id === 'mgmt_email' ? 'email' : id === 'mgmt_role' ? 'role' : 'status']; })"
                    @change="$el.dispatchEvent(new Event('input'))"
                >
                <form id="mgmt-update" method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 grid gap-5 md:grid-cols-2"
                    @submit="submitting = true"
                >
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="mgmt_name">Display Name<span class="ml-0.5 text-red-500" aria-hidden="true">*</span></label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </span>
                            <input id="mgmt_name" type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="form-input w-full pl-9" required maxlength="255" placeholder="Enter display name" />
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="mgmt_email">Email Address<span class="ml-0.5 text-red-500" aria-hidden="true">*</span></label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </span>
                            <input id="mgmt_email" type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="form-input w-full pl-9" required maxlength="255" placeholder="user@example.com"
                                oninput="this.value = this.value.toLowerCase()" />
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="mgmt_role">Role</label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                            </span>
                            <select id="mgmt_role" name="role" class="form-input w-full pl-9">
                                <option value="user" @selected(! $user->hasRole('admin'))>User</option>
                                <option value="admin" @selected($user->hasRole('admin'))>Admin</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="mgmt_status">Account Status</label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </span>
                            <select id="mgmt_status" name="status" class="form-input w-full pl-9">
                                <option value="active" @selected(($user->profile?->status ?? 'active') === 'active')>Active</option>
                                <option value="inactive" @selected(($user->profile?->status ?? 'active') === 'inactive')>Inactive</option>
                                <option value="pending" @selected(($user->profile?->status) === 'pending') disabled>Pending Approval (use Approve button)</option>
                            </select>
                        </div>
                    </div>

                </form>

                {{-- Footer buttons — outside the update form to prevent nesting --}}
                <div class="mt-2 flex items-center justify-end gap-4 border-t border-grayTheme-border pt-5">
                        @if(($user->profile?->status) === 'pending')
                            <form method="POST" action="{{ route('admin.users.approve', $user) }}" class="mr-auto">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-2 rounded-button bg-success px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-success/90">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Approve Account
                                </button>
                            </form>
                        @endif
                        <button type="submit" form="mgmt-update" class="btn-primary gap-2" x-bind:disabled="submitting || !dirty">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save Changes
                        </button>
                </div>
                </div>{{-- end x-data wrapper --}}

                {{-- Danger Zone --}}
                @if(auth()->id() !== $user->id)
                <div class="mt-8 border-t border-red-200 pt-6">
                    <h4 class="text-sm font-bold text-danger">Danger Zone</h4>
                    <p class="mt-1 text-sm text-grayTheme-medium">Permanently deletes this user account, their profile, all certificates, and uploaded files. This cannot be undone.</p>
                    <form id="delete-user-form" method="POST" action="{{ route('admin.users.destroy', $user) }}" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button
                        type="button"
                        class="btn-danger mt-4 gap-2"
                        @click="askDelete('Delete Account', 'Permanently delete {{ addslashes($user->name) }}\'s account, profile, all certificates and files? This cannot be undone.')"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete Account
                    </button>
                </div>
                @else
                <div class="mt-8 border-t border-grayTheme-border pt-6">
                    <h4 class="text-sm font-bold text-grayTheme-medium">Danger Zone</h4>
                    <p class="mt-1 text-sm text-grayTheme-medium">You cannot delete your own account.</p>
                </div>
                @endif
            </div>

            <div class="surface p-6">
                {{-- Section header --}}
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft">
                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-grayTheme-dark">Profile Details</h3>
                        <p class="mt-0.5 text-sm text-grayTheme-medium">Personal and employment information on record.</p>
                    </div>
                </div>

                @if ($user->profile)
                    {{-- Personal Details subsection --}}
                    <div class="mt-6 space-y-4">
                        <div class="flex items-center gap-2 border-b border-grayTheme-border pb-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-primary-soft">
                                <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <h4 class="text-sm font-bold text-grayTheme-dark">Personal Details</h4>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            @php
                                $fullName = collect([$user->profile->first_name, $user->profile->middle_name, $user->profile->last_name])->filter()->implode(' ');
                                if ($user->profile->suffix) $fullName .= ', ' . ucfirst($user->profile->suffix);
                            @endphp
                            <div class="flex items-start gap-3 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3 sm:col-span-2">
                                <span class="mt-0.5 shrink-0 text-grayTheme-medium"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg></span>
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Full Name</div>
                                    <div class="mt-0.5 font-medium text-grayTheme-dark">{{ $fullName ?: '—' }}</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3">
                                <span class="mt-0.5 shrink-0 text-grayTheme-medium"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></span>
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Date of Birth</div>
                                    <div class="mt-0.5 font-medium text-grayTheme-dark">{{ $user->profile->date_of_birth?->format('F j, Y') ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3">
                                <span class="mt-0.5 shrink-0 text-grayTheme-medium"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg></span>
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Sex</div>
                                    <div class="mt-0.5 font-medium text-grayTheme-dark">{{ $user->profile->gender ? ucfirst($user->profile->gender) : '—' }}</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3">
                                <span class="mt-0.5 shrink-0 text-grayTheme-medium"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg></span>
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Contact Number</div>
                                    <div class="mt-0.5 font-medium text-grayTheme-dark">{{ $user->profile->contact_number ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3 sm:col-span-2">
                                <span class="mt-0.5 shrink-0 text-grayTheme-medium"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg></span>
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Address</div>
                                    <div class="mt-0.5 font-medium text-grayTheme-dark">{{ $user->profile->address ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TESDA Role & Qualification Details subsection (hidden for admin users) --}}
                    @unless($user->hasRole('admin'))
                    <div class="mt-6 space-y-4">
                        <div class="flex items-center gap-2 border-b border-grayTheme-border pb-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-accent-soft">
                                <svg class="h-4 w-4 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <h4 class="text-sm font-bold text-grayTheme-dark">TESDA Role &amp; Qualification Details</h4>
                        </div>

                        @php
                            $positionRoles = $user->profile->position_roles ?? [];
                            $trainerTitles = array_values(array_filter($user->profile->trainer_qualification_titles ?? []));
                            $assessorTitles = array_values(array_filter($user->profile->assessor_qualification_titles ?? []));
                        @endphp

                        <div class="grid gap-3 sm:grid-cols-2">
                            {{-- Position / Job Role --}}
                            <div class="flex items-start gap-3 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3 sm:col-span-2">
                                <span class="mt-0.5 shrink-0 text-grayTheme-medium"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg></span>
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Position / Job Role</div>
                                    <div class="mt-0.5 font-medium text-grayTheme-dark">{{ $user->profile->position_title ?? '—' }}</div>
                                </div>
                            </div>

                            {{-- Trainer Qualification Title(s) --}}
                            @if(in_array('trainer', $positionRoles))
                            <div class="flex items-start gap-3 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3">
                                <span class="mt-0.5 shrink-0 text-grayTheme-medium"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg></span>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Trainer Qualification Title(s)</div>
                                    @if(count($trainerTitles))
                                        <ul class="mt-1 space-y-0.5">
                                            @foreach($trainerTitles as $title)
                                                <li class="font-medium text-grayTheme-dark">{{ $title }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="mt-0.5 font-medium text-grayTheme-dark">—</div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Assessor Qualification Title(s) --}}
                            @if(in_array('assessor', $positionRoles))
                            <div class="flex items-start gap-3 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3">
                                <span class="mt-0.5 shrink-0 text-grayTheme-medium"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg></span>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Assessor Qualification Title(s)</div>
                                    @if(count($assessorTitles))
                                        <ul class="mt-1 space-y-0.5">
                                            @foreach($assessorTitles as $title)
                                                <li class="font-medium text-grayTheme-dark">{{ $title }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="mt-0.5 font-medium text-grayTheme-dark">—</div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Remarks --}}
                            @if ($user->profile->remarks)
                            <div class="flex items-start gap-3 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3 sm:col-span-2">
                                <span class="mt-0.5 shrink-0 text-grayTheme-medium"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></span>
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Remarks</div>
                                    <div class="mt-0.5 font-medium text-grayTheme-dark">{{ $user->profile->remarks }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endunless
                @else
                    <div class="mt-6 flex flex-col items-center gap-3 rounded-xl border border-dashed border-grayTheme-border bg-grayTheme-light py-10 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-sm">
                            <svg class="h-6 w-6 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </div>
                        <p class="text-sm text-grayTheme-medium">No profile details on record yet.</p>
                    </div>
                @endif
            </div>

            @unless($user->hasRole('admin'))
            <div id="certificates-section" class="surface p-6">
                <h3 class="text-lg font-semibold text-grayTheme-dark">Certificates</h3>
                <form method="get" class="mt-4 flex flex-wrap items-end gap-3 text-sm">
                    @if ($docType !== 'all')
                        <input type="hidden" name="doc_type" value="{{ $docType }}" />
                    @endif
                    <div>
                        <label class="text-xs font-semibold uppercase text-grayTheme-medium" for="cert_status">Status</label>
                        <select id="cert_status" name="cert_status" class="mt-1 form-input">
                            <option value="all" @selected($certStatus === 'all')>All</option>
                            <option value="valid" @selected($certStatus === 'valid')>Valid</option>
                            <option value="expiring" @selected($certStatus === 'expiring')>Expiring</option>
                            <option value="expired" @selected($certStatus === 'expired')>Expired</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-grayTheme-medium" for="cert_window">Expiration Window</label>
                        <select id="cert_window" name="cert_window" class="mt-1 form-input">
                            <option value="0" @selected($certWindow === 0)>All dates</option>
                            <option value="30" @selected($certWindow === 30)>Next 30 days</option>
                            <option value="60" @selected($certWindow === 60)>Next 60 days</option>
                            <option value="90" @selected($certWindow === 90)>Next 90 days</option>
                        </select>
                    </div>
                    <button class="btn-primary" type="submit">Apply</button>
                </form>

                <div
                    class="mt-4"
                    x-data="loadMoreList({ nextUrl: @js($certificates->nextPageUrl()), partialParam: 'certificates_partial' })"
                    x-init="items = @js($certificates->map(fn($cert) => [
                        'id'                 => $cert->id,
                        'showUrl'            => route('admin.certificates.show', $cert) . '?back=' . urlencode(route('admin.users.show', $user)),
                        'name'               => $cert->certificate_name,
                        'type'               => $cert->certificate_type_label,
                        'qualification'      => $cert->qualification_title ?? '—',
                        'number'             => $cert->certificate_number ?? '—',
                        'expirationDate'     => $cert->expiration_date?->format('Y-m-d') ?? '—',
                        'status'             => ucfirst($cert->status),
                        'documents'          => $cert->documents->map(fn($d) => [
                            'name'        => $d->document_name ?? $d->original_name,
                            'downloadUrl' => route('documents.download', $d),
                        ])->values()->all(),
                    ]))"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-primary">
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">TESDA Classification</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Qualification</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Number</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Expires</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Documents</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-grayTheme-border">
                                <template x-if="items.length === 0">
                                    <tr>
                                        <td colspan="8" class="px-4 py-10 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-grayTheme-light">
                                                    <svg class="h-5 w-5 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                </div>
                                                <p class="text-sm text-grayTheme-medium">No certificates found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="cert in items" :key="cert.id">
                                    <tr class="cursor-pointer transition hover:bg-grayTheme-light/60" @click="window.location = cert.showUrl">
                                        <td class="px-4 py-3 font-medium text-grayTheme-dark" x-text="cert.name"></td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center rounded-full bg-grayTheme-hover px-2.5 py-0.5 text-xs font-semibold text-grayTheme-dark" x-text="cert.type"></span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-grayTheme-medium" x-text="cert.qualification"></td>
                                        <td class="px-4 py-3 font-mono text-xs text-grayTheme-medium" x-text="cert.number"></td>
                                        <td class="px-4 py-3 text-sm text-grayTheme-medium" x-text="cert.expirationDate"></td>
                                        <td class="px-4 py-3">
                                            <span
                                                x-text="cert.status"
                                                :class="{
                                                    'bg-success-soft text-success': cert.status === 'Valid',
                                                    'bg-warning-soft text-warning': cert.status === 'Expiring',
                                                    'bg-danger-soft text-danger':   cert.status === 'Expired',
                                                    'bg-grayTheme-hover text-grayTheme-medium': !['Valid','Expiring','Expired'].includes(cert.status),
                                                }"
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                            ></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <template x-if="cert.documents.length === 0">
                                                <span class="text-xs text-grayTheme-medium">—</span>
                                            </template>
                                            <template x-for="doc in cert.documents" :key="doc.downloadUrl">
                                                <div>
                                                    <a class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline" :href="doc.downloadUrl" @click.stop>
                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        <span x-text="doc.name"></span>
                                                    </a>
                                                </div>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        {{ $certificates->links() }}
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 btn-secondary"
                            x-show="nextUrl"
                            x-on:click="loadMore"
                            :disabled="loading"
                        >
                            <svg x-show="!loading" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            <svg x-show="loading" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span x-show="!loading">Load more</span>
                            <span x-show="loading">Loading&hellip;</span>
                        </button>
                    </div>
                </div>
            </div>

            <div id="documents-section" class="surface p-6">
                <h3 class="text-lg font-semibold text-grayTheme-dark">Other Documents</h3>
                <form method="get" class="mt-4 flex flex-wrap items-end gap-3 text-sm">
                    @if ($certStatus !== 'all')
                        <input type="hidden" name="cert_status" value="{{ $certStatus }}" />
                    @endif
                    @if ($certWindow > 0)
                        <input type="hidden" name="cert_window" value="{{ $certWindow }}" />
                    @endif
                    <div>
                        <label class="text-xs font-semibold uppercase text-grayTheme-medium" for="doc_type">Document Type</label>
                        <select id="doc_type" name="doc_type" class="mt-1 form-input">
                            <option value="all" @selected($docType === 'all')>All</option>
                            <option value="cv" @selected($docType === 'cv')>CV</option>
                            <option value="other" @selected($docType === 'other')>Other</option>
                        </select>
                    </div>
                    <button class="btn-primary" type="submit">Apply</button>
                </form>

                <div
                    class="mt-4"
                    x-data="loadMoreList({ nextUrl: @js($documents->nextPageUrl()), partialParam: 'documents_partial' })"
                    x-init="items = @js($documents->map(fn($doc) => [
                        'id'          => $doc->id,
                        'showUrl'     => route('admin.documents.show', $doc) . '?back=' . urlencode(route('admin.users.show', $user)),
                        'type'        => strtoupper($doc->type),
                        'name'        => $doc->document_name ?? $doc->original_name,
                        'downloadUrl' => route('documents.download', $doc),
                    ]))"
                >
                    <template x-if="items.length === 0">
                        <div class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-grayTheme-border bg-grayTheme-light py-10 text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-sm">
                                <svg class="h-6 w-6 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-sm text-grayTheme-medium">No documents uploaded.</p>
                        </div>
                    </template>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <template x-for="doc in items" :key="doc.id">
                            <div class="flex cursor-pointer items-center gap-3 rounded-xl border border-grayTheme-border bg-white p-3 shadow-sm transition hover:shadow-md" @click="window.location = doc.showUrl">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                    :class="{
                                        'bg-primary-soft': doc.type === 'CV',
                                        'bg-accent-soft': doc.type === 'TRAINING',
                                        'bg-grayTheme-light': !['CV','TRAINING'].includes(doc.type)
                                    }"
                                >
                                    <svg x-show="doc.type === 'CV'" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <svg x-show="doc.type === 'TRAINING'" class="h-5 w-5 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    <svg x-show="!['CV','TRAINING'].includes(doc.type)" class="h-5 w-5 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide"
                                        :class="{
                                            'text-primary': doc.type === 'CV',
                                            'text-accent-active': doc.type === 'TRAINING',
                                            'text-grayTheme-medium': !['CV','TRAINING'].includes(doc.type)
                                        }"
                                        x-text="doc.type"></p>
                                    <p class="truncate text-sm font-semibold text-grayTheme-dark" x-text="doc.name"></p>
                                </div>
                                <a
                                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-primary transition hover:bg-primary-soft focus:outline-none"
                                    :href="doc.downloadUrl"
                                    @click.stop
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download
                                </a>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        {{ $documents->links() }}
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 btn-secondary"
                            x-show="nextUrl"
                            x-on:click="loadMore"
                            :disabled="loading"
                        >
                            <svg x-show="!loading" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            <svg x-show="loading" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span x-show="!loading">Load more</span>
                            <span x-show="loading">Loading&hellip;</span>
                        </button>
                    </div>
                </div>
            </div>
            @endunless

        </div>

        {{-- Confirmation Modal --}}
        <div x-cloak x-show="confirmOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4"
            @keydown.escape.window="confirmOpen = false" @click.self="confirmOpen = false">
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-danger-soft">
                        <svg class="h-5 w-5 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-grayTheme-dark" x-text="confirmTitle"></h3>
                        <p class="mt-1 text-sm text-grayTheme-medium" x-text="confirmMessage"></p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn-secondary" @click="confirmOpen = false">Cancel</button>
                    <button type="button" class="btn-danger gap-2" @click="document.getElementById('delete-user-form').submit()">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Bridge session flash messages to toast notifications --}}
    @if (session('status') === 'user-updated')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Account Updated',message:'User account details have been saved successfully.'}}));</script>
    @elseif (session('status') === 'user-created')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Account Created',message:'New user account has been created successfully.'}}));</script>
    @elseif (session('status') === 'password-reset')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Password Reset',message:'The password has been reset successfully.'}}));</script>
    @endif
    <script data-turbo-eval="true">
        (function () {
            var params = new URLSearchParams(window.location.search);
            var sectionId = null;
            if (params.has('cert_status') || params.has('cert_window')) {
                sectionId = 'certificates-section';
            } else if (params.has('doc_type')) {
                sectionId = 'documents-section';
            }
            if (sectionId) {
                // Two rAFs: first runs after Turbo's synchronous scroll-to-top,
                // second ensures the browser has painted the new layout.
                requestAnimationFrame(function () {
                    requestAnimationFrame(function () {
                        var el = document.getElementById(sectionId);
                        if (el) el.scrollIntoView({ block: 'start' });
                    });
                });
            }
        })();
    </script>
</x-app-layout>
