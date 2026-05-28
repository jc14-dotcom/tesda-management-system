<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="People"
                subtitle="Search staff by their trainer or assessor qualifications."
                eyebrow="Account"
            />

            {{-- Search & Filter Form --}}
            <form method="GET" action="{{ route('account.directory') }}" id="directory-filter-form" x-data="liveSearch()">
                <div class="surface p-4 sm:p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">

                        {{-- Name search --}}
                        <div class="flex-1 min-w-[180px]">
                            <label for="dir-search" class="block text-xs font-semibold uppercase tracking-widest text-grayTheme-medium mb-1.5">Search by name</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-grayTheme-medium">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7 7 0 105.65 5.65a7 7 0 0011 11z" />
                                    </svg>
                                </span>
                                <input
                                    type="text"
                                    id="dir-search"
                                    name="search"
                                    value="{{ $search }}"
                                    placeholder="Type a name..."
                                    autocomplete="off"
                                    @input.debounce.400ms="search($el.closest('form'))"
                                    class="block w-full rounded-button border border-grayTheme-border bg-white py-2 pl-9 pr-3 text-sm text-grayTheme-dark placeholder-grayTheme-medium transition focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                />
                            </div>
                        </div>

                        {{-- Trainer Qualifications dropdown --}}
                        <div x-data="{ open: false }" class="relative min-w-[200px]">
                            <label class="block text-xs font-semibold uppercase tracking-widest text-grayTheme-medium mb-1.5">Trainer Qualifications</label>
                            @if ($trainerQuals->isEmpty())
                                <button type="button" disabled class="inline-flex w-full cursor-not-allowed items-center justify-between gap-2 rounded-button border border-grayTheme-border bg-grayTheme-light px-3 py-2 text-sm text-grayTheme-medium/50">
                                    <span class="truncate">Not configured</span>
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            @else
                                <button
                                    type="button"
                                    @click="open = !open"
                                    :aria-expanded="open"
                                    class="inline-flex w-full items-center justify-between gap-2 rounded-button border border-grayTheme-border bg-white px-3 py-2 text-sm transition focus:outline-none"
                                    :class="open || {{ count($trainerFilter) > 0 ? 'true' : 'false' }} ? 'border-primary text-primary' : 'text-grayTheme-dark hover:border-primary'"
                                >
                                    <span class="flex min-w-0 items-center gap-2">
                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="truncate">
                                            @if (count($trainerFilter) > 0)
                                                <span class="font-semibold">{{ count($trainerFilter) }}</span> selected
                                            @else
                                                All trainers
                                            @endif
                                        </span>
                                    </span>
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" :class="open ? 'rotate-180' : ''" style="transition: transform 0.15s">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div
                                    x-show="open"
                                    @click.outside="open = false"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute left-0 top-full z-30 mt-1 w-80 max-h-60 overflow-y-auto rounded-card border border-grayTheme-border bg-white shadow-modal"
                                >
                                    <ul class="divide-y divide-grayTheme-border py-1">
                                        @foreach ($trainerQuals as $qual)
                                            <li>
                                                <label class="flex cursor-pointer items-start gap-3 px-4 py-2.5 text-sm text-grayTheme-dark hover:bg-primary-soft">
                                                    <input
                                                        type="checkbox"
                                                        name="trainer_qual[]"
                                                        value="{{ $qual }}"
                                                        {{ in_array($qual, $trainerFilter) ? 'checked' : '' }}
                                                        class="mt-0.5 h-4 w-4 shrink-0 rounded border-grayTheme-border text-primary focus:ring-primary"
                                                    />
                                                    <span class="leading-snug">{{ $qual }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        {{-- Assessor Qualifications dropdown --}}
                        <div x-data="{ open: false }" class="relative min-w-[200px]">
                            <label class="block text-xs font-semibold uppercase tracking-widest text-grayTheme-medium mb-1.5">Assessor Qualifications</label>
                            @if ($assessorQuals->isEmpty())
                                <button type="button" disabled class="inline-flex w-full cursor-not-allowed items-center justify-between gap-2 rounded-button border border-grayTheme-border bg-grayTheme-light px-3 py-2 text-sm text-grayTheme-medium/50">
                                    <span class="truncate">Not configured</span>
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            @else
                                <button
                                    type="button"
                                    @click="open = !open"
                                    :aria-expanded="open"
                                    class="inline-flex w-full items-center justify-between gap-2 rounded-button border border-grayTheme-border bg-white px-3 py-2 text-sm transition focus:outline-none"
                                    :class="open || {{ count($assessorFilter) > 0 ? 'true' : 'false' }} ? 'border-primary text-primary' : 'text-grayTheme-dark hover:border-primary'"
                                >
                                    <span class="flex min-w-0 items-center gap-2">
                                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                        <span class="truncate">
                                            @if (count($assessorFilter) > 0)
                                                <span class="font-semibold">{{ count($assessorFilter) }}</span> selected
                                            @else
                                                All assessors
                                            @endif
                                        </span>
                                    </span>
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" :class="open ? 'rotate-180' : ''" style="transition: transform 0.15s">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div
                                    x-show="open"
                                    @click.outside="open = false"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute left-0 top-full z-30 mt-1 w-80 max-h-60 overflow-y-auto rounded-card border border-grayTheme-border bg-white shadow-modal"
                                >
                                    <ul class="divide-y divide-grayTheme-border py-1">
                                        @foreach ($assessorQuals as $qual)
                                            <li>
                                                <label class="flex cursor-pointer items-start gap-3 px-4 py-2.5 text-sm text-grayTheme-dark hover:bg-success-soft">
                                                    <input
                                                        type="checkbox"
                                                        name="assessor_qual[]"
                                                        value="{{ $qual }}"
                                                        {{ in_array($qual, $assessorFilter) ? 'checked' : '' }}
                                                        class="mt-0.5 h-4 w-4 shrink-0 rounded border-grayTheme-border text-primary focus:ring-primary"
                                                    />
                                                    <span class="leading-snug">{{ $qual }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        {{-- Action buttons --}}
                        @php $hasActiveFilters = $search || count($trainerFilter) || count($assessorFilter); @endphp
                        <div class="flex items-end gap-2">
                            <button
                                type="submit"
                                class="inline-flex items-center gap-1.5 rounded-button bg-primary px-4 py-2 text-sm font-semibold text-white shadow-card transition hover:bg-primary-hover focus:outline-none"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Apply Filters
                            </button>
                            <a
                                href="{{ $hasActiveFilters ? route('account.directory') : '#' }}"
                                @class([
                                    'inline-flex items-center gap-1.5 rounded-button border px-4 py-2 text-sm font-semibold transition focus:outline-none',
                                    'border-grayTheme-border bg-white text-grayTheme-medium hover:border-danger hover:bg-danger-soft hover:text-danger' => $hasActiveFilters,
                                    'pointer-events-none cursor-not-allowed border-grayTheme-border bg-grayTheme-light text-grayTheme-medium/40' => ! $hasActiveFilters,
                                ])
                                @if (! $hasActiveFilters) aria-disabled="true" @endif
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear
                            </a>
                        </div>
                    </div>

                    {{-- Active filter chips --}}
                    @if (count($trainerFilter) || count($assessorFilter))
                        <div class="mt-3 flex flex-wrap gap-2 border-t border-grayTheme-border pt-3">
                            @foreach ($trainerFilter as $chip)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-soft px-3 py-1 text-xs font-semibold text-primary">
                                    <svg class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Trainer: {{ $chip }}
                                    <a
                                        href="{{ request()->fullUrlWithQuery(['trainer_qual' => array_values(array_filter($trainerFilter, fn($v) => $v !== $chip))]) }}"
                                        class="ml-0.5 rounded-full opacity-70 transition hover:opacity-100"
                                        aria-label="Remove trainer filter: {{ $chip }}"
                                    >
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </a>
                                </span>
                            @endforeach
                            @foreach ($assessorFilter as $chip)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-success-soft px-3 py-1 text-xs font-semibold text-success">
                                    <svg class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Assessor: {{ $chip }}
                                    <a
                                        href="{{ request()->fullUrlWithQuery(['assessor_qual' => array_values(array_filter($assessorFilter, fn($v) => $v !== $chip))]) }}"
                                        class="ml-0.5 rounded-full opacity-70 transition hover:opacity-100"
                                        aria-label="Remove assessor filter: {{ $chip }}"
                                    >
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </a>
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </form>

            <div id="live-search-results" class="space-y-6">

            {{-- Result count --}}
            <div class="flex items-center justify-between">
                <p class="text-sm text-grayTheme-medium">
                    Showing
                    <span class="font-semibold text-grayTheme-dark">{{ $people->total() }}</span>
                    {{ Str::plural('person', $people->total()) }}
                    @if ($search || count($trainerFilter) || count($assessorFilter))
                        <span class="text-grayTheme-medium">matching your filters</span>
                    @endif
                </p>
            </div>

            {{-- Results --}}
            @if ($people->isEmpty())
                <div class="surface flex flex-col items-center gap-4 py-16 text-center">
                    <svg class="h-14 w-14 text-grayTheme-border" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div>
                        <p class="text-base font-semibold text-grayTheme-dark">No people found</p>
                        <p class="mt-1 text-sm text-grayTheme-medium">Try adjusting your search or removing some filters.</p>
                    </div>
                    @if ($search || count($trainerFilter) || count($assessorFilter))
                        <a
                            href="{{ route('account.directory') }}"
                            class="inline-flex items-center gap-1.5 rounded-button border border-grayTheme-border bg-white px-4 py-2 text-sm font-semibold text-grayTheme-medium transition hover:border-primary hover:text-primary"
                        >
                            Clear all filters
                        </a>
                    @endif
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @include('user.directory.partials.person-cards', ['people' => $people])
                </div>

                <div class="mt-4">
                    {{ $people->links() }}
                </div>
            @endif

            </div>{{-- /live-search-results --}}

        </div>
    </div>
</x-app-layout>
