@foreach ($people as $user)
    @php
        $profile        = $user->profile;
        $photoUrl       = $profile?->profile_photo_url;
        $firstName      = $profile?->first_name ?? '';
        $lastName       = $profile?->last_name ?? '';
        $fullName       = trim($firstName . ' ' . $lastName) ?: $user->name;
        $initials       = collect(explode(' ', $fullName))
            ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->take(2)
            ->implode('');
        $trainerTitles  = $profile?->trainer_qualification_titles ?? [];
        $assessorTitles = $profile?->assessor_qualification_titles ?? [];
        $hasQuals       = !empty($trainerTitles) || !empty($assessorTitles);
    @endphp

    <div class="surface overflow-hidden flex flex-col">
        {{-- Accent bar --}}
        <div class="h-1 w-full bg-gradient-to-r from-primary to-accent"></div>

        <div class="flex flex-1 flex-col gap-4 p-4 sm:p-5">
            {{-- Person header --}}
            <div class="flex items-center gap-3">
                @if ($photoUrl)
                    <img
                        src="{{ $photoUrl }}"
                        alt="{{ $fullName }}"
                        class="h-12 w-12 shrink-0 rounded-full object-cover ring-2 ring-primary/20"
                    />
                @else
                    <span
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-white ring-2 ring-primary/20"
                        aria-hidden="true"
                    >{{ $initials }}</span>
                @endif

                <div class="min-w-0">
                    <p class="truncate text-sm font-bold text-grayTheme-dark">{{ $fullName }}</p>
                    @if ($profile?->position_title)
                        <p class="truncate text-xs text-grayTheme-medium">{{ $profile->position_title }}</p>
                    @endif
                </div>
            </div>

            {{-- Meta row: branch + employment status --}}
            @if ($profile?->branch || $profile?->employment_status)
                <div class="flex flex-wrap items-center gap-2 border-t border-grayTheme-border pt-3">
                    @if ($profile?->branch)
                        <span class="inline-flex items-center gap-1 text-xs text-grayTheme-medium">
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ $profile->branch }}
                        </span>
                    @endif

                    @if ($profile?->employment_status)
                        <span class="inline-flex items-center rounded-full bg-grayTheme-light px-2.5 py-0.5 text-xs font-semibold text-grayTheme-dark">
                            {{ ucwords(str_replace('_', ' ', $profile->employment_status)) }}
                        </span>
                    @endif
                </div>
            @endif

            {{-- Qualifications --}}
            @if ($hasQuals)
                <div class="flex flex-col gap-3 border-t border-grayTheme-border pt-3">
                    @if (!empty($trainerTitles))
                        <div>
                            <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-grayTheme-medium">Trainer</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($trainerTitles as $title)
                                    <span class="inline-flex items-center rounded-full bg-primary-soft px-2.5 py-1 text-[11px] font-semibold leading-none text-primary">
                                        {{ $title }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (!empty($assessorTitles))
                        <div>
                            <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-widest text-grayTheme-medium">Assessor</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($assessorTitles as $title)
                                    <span class="inline-flex items-center rounded-full bg-success-soft px-2.5 py-1 text-[11px] font-semibold leading-none text-success">
                                        {{ $title }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <p class="border-t border-grayTheme-border pt-3 text-xs italic text-grayTheme-medium">No qualifications listed.</p>
            @endif
        </div>
    </div>
@endforeach
