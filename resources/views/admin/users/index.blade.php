<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Users"
                subtitle="Manage account access and user details."
                eyebrow="Administration"
            />

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.users.create') }}" class="btn-primary inline-flex items-center gap-1.5 text-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Create Account
                </a>
                <a href="{{ route('admin.export.users') }}" class="btn-secondary text-xs gap-1 inline-flex items-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export CSV
                </a>
            </div>

            @if (session('status') === 'user-deleted')
                <div class="rounded-lg bg-danger-soft px-4 py-3 text-sm font-semibold text-danger">User account deleted.</div>
            @endif

            {{-- Search / Filter --}}
            <div class="surface p-6">
                <form method="get" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-48">
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="search">Search</label>
                        <input id="search" type="text" name="search" value="{{ $search }}"
                            class="mt-1 form-input w-full" placeholder="Name or email…" />
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
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Apply</button>
                    @if ($search || $role !== 'all' || $status !== 'all')
                        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Reset</a>
                    @endif
                </form>
            </div>

            <div class="surface">
                <div class="p-6 text-grayTheme-dark">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-grayTheme-medium">
                                <tr>
                                    <th class="py-2">Name</th>
                                    <th class="py-2">Email</th>
                                    <th class="py-2">Role</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2">Certificates</th>
                                    <th class="py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse ($users as $user)
                                    <tr>
                                        <td class="py-2 font-medium text-grayTheme-dark">{{ $user->name }}</td>
                                        <td class="py-2">{{ $user->email }}</td>
                                        <td class="py-2">
                                            {{ $user->hasRole('admin') ? 'Admin' : 'User' }}
                                        </td>
                                        <td class="py-2">
                                            @php $s = $user->profile?->status ?? 'active'; @endphp
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                                {{ $s === 'active' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                                {{ ucfirst($s) }}
                                            </span>
                                        </td>
                                        <td class="py-2">{{ $user->certificates_count }}</td>
                                        <td class="py-2 text-right">
                                            <a class="text-primary hover:text-primary-hover" href="{{ route('admin.users.show', $user) }}">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-grayTheme-medium">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
