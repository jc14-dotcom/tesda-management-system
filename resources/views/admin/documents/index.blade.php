<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Documents"
                subtitle="Monitor certificate files and supporting documents across all users."
                eyebrow="Administration"
            />

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Total Documents</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Uploaded This Week</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($stats['thisWeek']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-soft">
                        <svg class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                </div>
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Total Storage Used</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">
                            @php $mb = $stats['totalSize'] / 1048576; @endphp
                            {{ $mb >= 1 ? number_format($mb, 1) . ' MB' : number_format($stats['totalSize'] / 1024, 1) . ' KB' }}
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-soft">
                        <svg class="h-6 w-6 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="surface p-6">
                <form method="get" x-data="liveSearch()">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="flex-1 min-w-48">
                            <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="search">Search</label>
                            <input id="search" type="text" name="search" value="{{ $search }}"
                                class="mt-1 form-input w-full" placeholder="Document name or user…"
                                @input.debounce.400ms="search($el.closest('form'))" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="type">Type</label>
                            <select id="type" name="type" class="mt-1 form-input">
                                <option value="all" @selected($type === 'all')>All Types</option>
                                <option value="cv" @selected($type === 'cv')>CV</option>
                                <option value="certificate" @selected($type === 'certificate')>Training/Workshop/Seminars Certificate</option>
                                <option value="other" @selected($type === 'other')>Other</option>
                            </select>
                        </div>
                    </div>
                    @php $hasFilters = $search || $type !== 'all'; @endphp
                    <div class="mt-4 flex items-center justify-end gap-2">
                        <a href="{{ route('admin.documents.index') }}" class="btn-secondary inline-flex items-center gap-1.5 {{ !$hasFilters ? 'pointer-events-none opacity-40' : '' }}">
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
                <div class="flex items-center gap-4 border-b px-6 py-4">
                    <span class="text-sm font-semibold text-grayTheme-dark">
                        {{ number_format($documents->total()) }} {{ Str::plural('document', $documents->total()) }} found
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-primary">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Document</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Size</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Uploaded</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-grayTheme-border">
                            @forelse ($documents as $doc)
                                <tr class="transition hover:bg-grayTheme-light/60">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.users.show', $doc->user) }}" class="inline-flex items-center gap-2 font-medium text-primary hover:underline">
                                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary-soft text-xs font-bold text-primary">{{ strtoupper(substr($doc->user->name ?? '?', 0, 1)) }}</div>
                                            {{ $doc->user->name ?? '—' }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-grayTheme-dark">{{ $doc->document_name ?: $doc->original_name }}</div>
                                        @if ($doc->document_name && $doc->original_name !== $doc->document_name)
                                            <div class="text-xs text-grayTheme-medium">{{ $doc->original_name }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $docTypeTone = match($doc->type) {
                                                'cv'       => 'bg-primary-soft text-primary',
                                                'training' => 'bg-accent-soft text-accent-active',
                                                default    => 'bg-grayTheme-hover text-grayTheme-dark',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $docTypeTone }}">{{ strtoupper($doc->type) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-grayTheme-medium">
                                        @php
                                            $kb = $doc->size / 1024;
                                            echo $kb >= 1024 ? number_format($kb / 1024, 2) . ' MB' : number_format($kb, 1) . ' KB';
                                        @endphp
                                    </td>
                                    <td class="px-4 py-3 text-sm text-grayTheme-medium">{{ $doc->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('documents.download', $doc) }}" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-primary transition hover:bg-primary-soft focus:outline-none">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Download
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-grayTheme-light">
                                                <svg class="h-6 w-6 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <p class="text-sm font-semibold text-grayTheme-dark">No documents found</p>
                                            <p class="text-xs text-grayTheme-medium">Try adjusting your search or filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($documents->hasPages())
                    <div class="border-t px-6 py-4">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
