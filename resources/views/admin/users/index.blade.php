<x-app-layout>
    <div class="py-12"
         x-data="{
             resetOpen: false,
             resetUserName: '',
             resetUrl: '',
             openReset(name, url) {
                 this.resetUserName = name;
                 this.resetUrl = url;
                 this.resetOpen = true;
             }
         }">
        <div class="page-container space-y-6">
            <x-page-header
                title="Users"
                subtitle="Manage account access and user details."
                eyebrow="Administration"
            >
                <x-slot:actions>
                    <a href="{{ route('admin.users.create') }}" class="rounded-full border border-white/30 bg-white/10 px-4 py-1.5 text-sm font-semibold text-white hover:bg-white/20 inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Create Account
                    </a>
                </x-slot:actions>
            </x-page-header>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Total Users</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Active Users</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($stats['active']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-soft">
                        <svg class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Inactive Users</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($stats['inactive']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-danger-soft">
                        <svg class="h-6 w-6 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                </div>
            </div>

            {{-- Flash messages handled by toast notifications --}}

            {{-- Search / Filter --}}
            <div class="surface p-6">
                <form method="get" x-data="liveSearch()">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-48">
                            <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="search">Search</label>
                            <input id="search" type="text" name="search" value="{{ $search }}"
                                class="mt-1 form-input w-full" placeholder="Name or email…"
                                @input.debounce.400ms="search($el.closest('form'))" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="role">Role</label>
                            <select id="role" name="role" class="mt-1 form-input">
                                <option value="all" @selected($role === 'all')>All Roles</option>
                                <option value="admin" @selected($role === 'admin')>Admin</option>
                                <option value="user" @selected($role === 'user')>User</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="status">Status</label>
                            <select id="status" name="status" class="mt-1 form-input">
                                <option value="all" @selected($status === 'all')>All Statuses</option>
                                <option value="active" @selected($status === 'active')>Active</option>
                                <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                                <option value="pending" @selected($status === 'pending')>Pending Approval</option>
                            </select>
                        </div>
                    </div>
                    @php $hasFilters = $search || $role !== 'all' || $status !== 'all'; @endphp
                    <div class="mt-4 flex items-center justify-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn-secondary inline-flex items-center gap-1.5 {{ !$hasFilters ? 'pointer-events-none opacity-40' : '' }}">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reset
                        </a>
                        <button type="submit" class="btn-primary inline-flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                            Apply
                        </button>
                    </div>
                </form>
            </div>

            <div id="live-search-results" class="surface overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-primary">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Certificates</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-grayTheme-border">
                            @forelse ($users as $user)
                                <tr class="cursor-pointer transition hover:bg-grayTheme-light/60" onclick="window.location='{{ route('admin.users.show', $user) }}'">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            @if ($user->profile?->profile_photo_url)
                                                <img
                                                    src="{{ $user->profile->profile_photo_url }}"
                                                    alt="{{ $user->name }}"
                                                    class="h-8 w-8 shrink-0 rounded-full object-cover"
                                                />
                                            @else
                                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-xs font-bold text-primary">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="font-medium text-grayTheme-dark">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-grayTheme-medium">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
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
                                    </td>
                                    <td class="px-4 py-3">
                                        @php $s = $user->profile?->status ?? 'active'; @endphp
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold
                                            {{ $s === 'active' ? 'bg-success-soft text-success' : ($s === 'pending' ? 'bg-accent-soft text-amber-700' : 'bg-danger-soft text-danger') }}">
                                            @if ($s === 'active')
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @elseif ($s === 'pending')
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @else
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            @endif
                                            {{ $s === 'pending' ? 'Pending' : ucfirst($s) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-grayTheme-hover px-2.5 py-0.5 text-xs font-semibold text-grayTheme-dark">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            {{ $user->certificates_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-1" onclick="event.stopPropagation()">
                                            {{-- Activity Log --}}
                                            <a href="{{ route('admin.activity.index', ['causer_id' => $user->id]) }}"
                                               title="View Activity Log"
                                               class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-primary transition hover:bg-primary-soft">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </a>
                                            {{-- Edit --}}
                                            <a href="{{ route('admin.users.show', $user) }}"
                                               title="Edit User"
                                               class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-accent transition hover:bg-accent-soft">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            {{-- Reset Password --}}
                                            <button type="button"
                                                    title="Reset Password"
                                                    @click="openReset('{{ addslashes($user->name) }}', '{{ route('admin.users.reset-password', $user) }}')"
                                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-warning transition hover:bg-warning-soft">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                            </button>
                                            {{-- Toggle Status / Approve (hidden for self) --}}
                                            @if(auth()->id() !== $user->id)
                                                @php $userStatus = $user->profile?->status ?? 'active'; @endphp
                                                @if($userStatus === 'pending')
                                                    <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                title="Approve Account"
                                                                class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-success transition hover:bg-success-soft">
                                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                title="{{ $userStatus === 'active' ? 'Deactivate User' : 'Activate User' }}"
                                                                class="inline-flex h-7 w-7 items-center justify-center rounded-lg transition {{ $userStatus === 'active' ? 'text-danger hover:bg-danger-soft' : 'text-success hover:bg-success-soft' }}">
                                                            @if($userStatus === 'active')
                                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                            @else
                                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            @endif
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <span class="inline-flex h-7 w-7 items-center justify-center" title="Cannot change own status">
                                                    <svg class="h-4 w-4 text-grayTheme-border" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                        <td colspan="6" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-grayTheme-light">
                                                <svg class="h-6 w-6 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                            </div>
                                            <p class="text-sm font-semibold text-grayTheme-dark">No users found</p>
                                            <p class="text-xs text-grayTheme-medium">Try adjusting your search or filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="border-t border-grayTheme-border px-4 py-4">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>

    {{-- Reset Password Modal --}}
    <div x-cloak
         x-show="resetOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4"
         @keydown.escape.window="resetOpen = false">
        <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl"
             @click.outside="resetOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-grayTheme-border px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-warning-soft">
                        <svg class="h-5 w-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-grayTheme-dark">Reset Password</h3>
                        <p class="text-xs text-grayTheme-medium" x-text="'For: ' + resetUserName"></p>
                    </div>
                </div>
                <button type="button" @click="resetOpen = false" class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-grayTheme-medium transition hover:bg-grayTheme-light hover:text-grayTheme-dark">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            {{-- Body --}}
            <form method="POST" :action="resetUrl">
                @csrf
                <div class="space-y-4 px-6 py-5">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="modal_password">
                            New Password <span class="text-danger">*</span>
                        </label>
                        <input id="modal_password" type="password" name="password"
                               class="mt-1 form-input w-full" required minlength="8"
                               autocomplete="new-password" placeholder="Min. 8 characters" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="modal_password_confirmation">
                            Confirm New Password <span class="text-danger">*</span>
                        </label>
                        <input id="modal_password_confirmation" type="password" name="password_confirmation"
                               class="mt-1 form-input w-full" required minlength="8"
                               autocomplete="new-password" placeholder="Repeat new password" />
                    </div>
                    <p class="text-xs text-grayTheme-medium">The user will need to use this password on their next login.</p>
                </div>
                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-grayTheme-border px-6 py-4">
                    <button type="button" @click="resetOpen = false" class="btn-secondary text-sm">Cancel</button>
                    <button type="submit" class="btn-primary gap-2 text-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>

    {{-- Bridge session flash messages to toast notifications --}}
    @if (session('status') === 'user-deleted')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'User Deleted',message:'The user account has been permanently removed.'}}));</script>
    @endif
</x-app-layout>
