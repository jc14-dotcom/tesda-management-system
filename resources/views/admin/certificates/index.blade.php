<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Certificates"
                subtitle="Track, verify, and manage certificate records across all users."
                eyebrow="Administration"
            />

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Total Certificates</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                </div>
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Pending Verification</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($stats['pending']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-warning-soft">
                        <svg class="h-6 w-6 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Expiring Soon</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($stats['expiring']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-danger-soft">
                        <svg class="h-6 w-6 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
            </div>

            {{-- Flash messages handled by toast notifications --}}

            {{-- Filters --}}
            <div class="surface p-6">
                <form method="get" x-data="liveSearch()">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-48">
                            <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="search">Search</label>
                            <input id="search" type="text" name="search" value="{{ $search }}"
                                class="mt-1 form-input w-full" placeholder="Certificate name, number…"
                                @input.debounce.400ms="search($el.closest('form'))" />
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
                    </div>
                    @php $hasFilters = $search || $status !== 'all' || $type !== 'all' || $verifyStatus !== 'all' || $window > 0; @endphp
                    <div class="mt-4 flex items-center justify-end gap-2">
                        <a href="{{ route('admin.certificates.index') }}" class="btn-secondary inline-flex items-center gap-1.5 {{ !$hasFilters ? 'pointer-events-none opacity-40' : '' }}">
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

            {{-- Results --}}
            <div id="live-search-results" class="surface">
                <div class="flex items-center justify-between gap-4 border-b px-6 py-4">
                    <span class="text-sm font-semibold text-grayTheme-dark">
                        {{ number_format($certificates->total()) }} {{ Str::plural('certificate', $certificates->total()) }} found
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-primary">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Certificate</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Expires</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Verification</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-grayTheme-border">
                            @forelse ($certificates as $cert)
                                <tr class="cursor-pointer transition hover:bg-primary-soft/60" onclick="window.location='{{ route('admin.certificates.show', $cert) }}'">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.users.show', $cert->user) }}" class="inline-flex items-center gap-2 font-medium text-primary hover:underline">
                                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary-soft text-xs font-bold text-primary">{{ strtoupper(substr($cert->user->name ?? '?', 0, 1)) }}</div>
                                            {{ $cert->user->name ?? '—' }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-grayTheme-dark">{{ $cert->certificate_name }}</div>
                                        @if ($cert->certificate_number)
                                            <div class="font-mono text-xs text-grayTheme-medium">No. {{ $cert->certificate_number }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full bg-grayTheme-hover px-2.5 py-0.5 text-xs font-semibold text-grayTheme-dark">{{ $cert->certificate_type_label }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-grayTheme-medium">
                                        {{ $cert->expiration_date ? $cert->expiration_date->format('M d, Y') : '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statusTone = match ($cert->status) {
                                                'valid'    => 'bg-success-soft text-success',
                                                'expiring' => 'bg-warning-soft text-warning',
                                                'expired'  => 'bg-danger-soft text-danger',
                                                default    => 'bg-grayTheme-light text-grayTheme-medium',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusTone }}">
                                            @if ($cert->status === 'valid')
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @elseif ($cert->status === 'expiring')
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @elseif ($cert->status === 'expired')
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            @endif
                                            {{ ucfirst($cert->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $vStatus = $cert->verification_status ?? 'pending';
                                            $verifyTone = match ($vStatus) {
                                                'verified' => 'bg-success-soft text-success',
                                                'rejected' => 'bg-danger-soft text-danger',
                                                default    => 'bg-warning-soft text-warning',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $verifyTone }}">
                                            @if ($vStatus === 'verified')
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @elseif ($vStatus === 'rejected')
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            @else
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                            {{ ucfirst($vStatus) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3" onclick="event.stopPropagation()">
                                        <div class="flex items-center gap-1.5">
                                            @if ($vStatus !== 'verified')
                                                <form method="POST" action="{{ route('admin.certificates.verify', $cert) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="action" value="verify">
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-success px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-success-hover focus:outline-none">
                                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                        Verify
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($vStatus !== 'rejected')
                                                <form method="POST" action="{{ route('admin.certificates.verify', $cert) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-danger px-2.5 py-1.5 text-xs font-semibold text-white transition hover:bg-danger-hover focus:outline-none">
                                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        Reject
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($vStatus !== 'pending')
                                                <form method="POST" action="{{ route('admin.certificates.verify', $cert) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="action" value="reset">
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-grayTheme-hover px-2.5 py-1.5 text-xs font-semibold text-grayTheme-dark transition hover:bg-grayTheme-border focus:outline-none">
                                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                        Reset
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-grayTheme-light">
                                                <svg class="h-6 w-6 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            </div>
                                            <p class="text-sm font-semibold text-grayTheme-dark">No certificates found</p>
                                            <p class="text-xs text-grayTheme-medium">Try adjusting your search or filters.</p>
                                        </div>
                                    </td>
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

    {{-- Bridge session flash messages to toast notifications --}}
    @if (session('status') === 'cert-updated')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Certificate Updated',message:'Verification status has been updated.'}}));</script>
    @endif
</x-app-layout>
