<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Certificates"
                subtitle="Track, verify, and manage certificate records across all users."
                eyebrow="Administration"
            />

            <div class="flex justify-end">
                <a href="{{ route('admin.export.certificates') }}" class="btn-secondary text-xs gap-1 inline-flex items-center">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export CSV
                </a>
            </div>

            @if (session('status') === 'cert-updated')
                <div class="rounded-lg bg-success-soft px-4 py-3 text-sm font-semibold text-success">Certificate verification updated.</div>
            @endif

            {{-- Filters --}}
            <div class="surface p-6">
                <form method="get" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-48">
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="search">Search</label>
                        <input id="search" type="text" name="search" value="{{ $search }}"
                            class="mt-1 form-input w-full" placeholder="Certificate name, number…" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="status">Status</label>
                        <select id="status" name="status" class="mt-1 form-input">
                            <option value="all" @selected($status === 'all')>All Statuses</option>
                            <option value="valid" @selected($status === 'valid')>Valid</option>
                            <option value="expiring" @selected($status === 'expiring')>Expiring</option>
                            <option value="expired" @selected($status === 'expired')>Expired</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="type">Type</label>
                        <select id="type" name="type" class="mt-1 form-input">
                            <option value="all" @selected($type === 'all')>All Types</option>
                            @foreach ($typeLabels as $key => $label)
                                <option value="{{ $key }}" @selected($type === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="verify_status">Verification</label>
                        <select id="verify_status" name="verify_status" class="mt-1 form-input">
                            <option value="all" @selected($verifyStatus === 'all')>All</option>
                            <option value="pending" @selected($verifyStatus === 'pending')>Pending</option>
                            <option value="verified" @selected($verifyStatus === 'verified')>Verified</option>
                            <option value="rejected" @selected($verifyStatus === 'rejected')>Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="window">Expiry Window</label>
                        <select id="window" name="window" class="mt-1 form-input">
                            <option value="0" @selected($window === 0)>All Dates</option>
                            <option value="30" @selected($window === 30)>Next 30 Days</option>
                            <option value="60" @selected($window === 60)>Next 60 Days</option>
                            <option value="90" @selected($window === 90)>Next 90 Days</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Apply</button>
                    @if ($search || $status !== 'all' || $type !== 'all' || $verifyStatus !== 'all' || $window > 0)
                        <a href="{{ route('admin.certificates.index') }}" class="btn-secondary">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Results --}}
            <div class="surface">
                <div class="flex items-center justify-between gap-4 border-b px-6 py-4">
                    <span class="text-sm font-semibold text-grayTheme-dark">
                        {{ number_format($certificates->total()) }} {{ Str::plural('certificate', $certificates->total()) }} found
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-grayTheme-medium">
                            <tr>
                                <th class="px-6 py-3">User</th>
                                <th class="px-6 py-3">Certificate</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Expires</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Verification</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($certificates as $cert)
                                <tr>
                                    <td class="px-6 py-3">
                                        <a href="{{ route('admin.users.show', $cert->user) }}" class="font-medium text-primary hover:text-primary-hover">
                                            {{ $cert->user->name ?? '—' }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="font-medium text-grayTheme-dark">{{ $cert->certificate_name }}</div>
                                        @if ($cert->certificate_number)
                                            <div class="text-xs text-grayTheme-medium">No. {{ $cert->certificate_number }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-grayTheme-medium">{{ $cert->certificate_type_label }}</td>
                                    <td class="px-6 py-3 text-grayTheme-medium">
                                        {{ $cert->expiration_date ? $cert->expiration_date->format('M d, Y') : '—' }}
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $statusTone = match ($cert->status) {
                                                'valid'    => 'bg-success-soft text-success',
                                                'expiring' => 'bg-warning-soft text-warning',
                                                'expired'  => 'bg-danger-soft text-danger',
                                                default    => 'bg-grayTheme-light text-grayTheme-medium',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusTone }}">
                                            {{ ucfirst($cert->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $vStatus = $cert->verification_status ?? 'pending';
                                            $verifyTone = match ($vStatus) {
                                                'verified' => 'bg-success-soft text-success',
                                                'rejected' => 'bg-danger-soft text-danger',
                                                default    => 'bg-warning-soft text-warning',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $verifyTone }}">
                                            {{ ucfirst($vStatus) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-3">
                                            @if ($vStatus !== 'verified')
                                                <form method="POST" action="{{ route('admin.certificates.verify', $cert) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="action" value="verify">
                                                    <button type="submit" class="text-xs font-semibold text-success hover:underline">Verify</button>
                                                </form>
                                            @endif
                                            @if ($vStatus !== 'rejected')
                                                <form method="POST" action="{{ route('admin.certificates.verify', $cert) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="text-xs font-semibold text-danger hover:underline">Reject</button>
                                                </form>
                                            @endif
                                            @if ($vStatus !== 'pending')
                                                <form method="POST" action="{{ route('admin.certificates.verify', $cert) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="action" value="reset">
                                                    <button type="submit" class="text-xs font-semibold text-grayTheme-medium hover:underline">Reset</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-grayTheme-medium">No certificates found matching your filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($certificates->hasPages())
                    <div class="border-t px-6 py-4">
                        {{ $certificates->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
