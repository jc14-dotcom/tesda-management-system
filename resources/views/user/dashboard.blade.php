<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Dashboard"
                subtitle="Quick snapshot of your certificates, documents, and alerts."
                eyebrow="Account"
            />

            @php
                $statCards = [
                    [
                        'label' => 'Total Certificates',
                        'value' => $certificatesCount,
                        'note' => 'All certificates',
                        'tone' => 'bg-primary-soft text-primary',
                        'icon' => 'M5 4h14a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Zm4 4h6m-6 4h6',
                    ],
                    [
                        'label' => 'Expiring Soon',
                        'value' => $expiringSoon30,
                        'note' => 'Within 30 days',
                        'tone' => 'bg-accent-soft text-accent-hover',
                        'icon' => 'M12 8v4l3 3m7-3A10 10 0 1 1 2 12a10 10 0 0 1 20 0Z',
                    ],
                    [
                        'label' => 'Documents Uploaded',
                        'value' => $documentsCount,
                        'note' => 'All documents',
                        'tone' => 'bg-primary-soft text-primary',
                        'icon' => 'M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm5 4h6m-6 4h6',
                    ],
                    [
                        'label' => 'Notifications',
                        'value' => 0,
                        'note' => 'No new alerts',
                        'tone' => 'bg-primary-soft text-primary',
                        'icon' => 'M6 8a6 6 0 1 1 12 0c0 7 3 7 3 7H3s3 0 3-7Zm3 11a3 3 0 0 0 6 0',
                    ],
                ];
            @endphp

            <div class="space-y-6">
                <section class="rounded-3xl border border-primary/15 bg-gradient-to-br from-primary to-primary-hover p-6 text-white shadow-card sm:p-8">
                    <div class="max-w-3xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/70">Account Overview</p>
                        <h2 class="mt-3 text-2xl font-semibold sm:text-3xl">Welcome back, {{ auth()->user()->name }}</h2>
                        <p class="mt-2 text-sm text-white/80 sm:text-base">Here is a snapshot of your certificates, documents, and upcoming renewals.</p>
                    </div>
                </section>

                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($statCards as $card)
                        <div class="surface p-5">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm text-grayTheme-medium">{{ $card['label'] }}</p>
                                    <p class="mt-2 text-3xl font-semibold text-grayTheme-dark">{{ $card['value'] }}</p>
                                </div>
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $card['tone'] }}">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                        <path d="{{ $card['icon'] }}" />
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">{{ $card['note'] }}</p>
                        </div>
                    @endforeach
                </section>

                <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                    <div class="surface p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-grayTheme-dark">Expiry Timeline</h3>
                                <p class="text-sm text-grayTheme-medium">Certificates expiring over the next 90 days.</p>
                            </div>
                            <span class="rounded-full bg-primary-soft px-3 py-1 text-xs font-semibold text-primary">Last 90 days</span>
                        </div>

                        <div class="mt-6 rounded-2xl border border-grayTheme-border bg-grayTheme-light p-4">
                            <svg viewBox="0 0 520 160" class="h-40 w-full">
                                <defs>
                                    <linearGradient id="expiryLine" x1="0" x2="1" y1="0" y2="1">
                                        <stop offset="0%" stop-color="#F4B400" />
                                        <stop offset="100%" stop-color="#2B2D7E" />
                                    </linearGradient>
                                </defs>
                                <path d="M10 120 C70 60, 130 140, 190 90 C250 40, 310 110, 370 70 C430 30, 470 80, 510 50" fill="none" stroke="url(#expiryLine)" stroke-width="4" />
                                <circle cx="70" cy="84" r="4" fill="#F4B400" />
                                <circle cx="190" cy="90" r="4" fill="#2B2D7E" />
                                <circle cx="310" cy="110" r="4" fill="#F4B400" />
                                <circle cx="430" cy="60" r="4" fill="#2B2D7E" />
                            </svg>
                            <div class="mt-4 grid grid-cols-3 gap-4 text-xs text-grayTheme-medium">
                                <div>
                                    <p class="font-semibold text-grayTheme-dark">{{ $expiringSoon30 }}</p>
                                    <p>Expiring in 30 days</p>
                                </div>
                                <div>
                                    <p class="font-semibold text-grayTheme-dark">{{ $expiring60 }}</p>
                                    <p>Expiring in 60 days</p>
                                </div>
                                <div>
                                    <p class="font-semibold text-grayTheme-dark">{{ $expiring90 }}</p>
                                    <p>Expiring in 90 days</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="surface p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-grayTheme-dark">Certificates by Level</h3>
                                <p class="text-sm text-grayTheme-medium">Distribution of TESDA classifications.</p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            @foreach ($programs as $program)
                                <div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-semibold text-grayTheme-dark">{{ $program['label'] }}</span>
                                        <span class="text-grayTheme-medium">{{ $program['value'] }}</span>
                                    </div>
                                    <div class="mt-2 h-2 w-full rounded-full bg-grayTheme-hover">
                                        <div class="h-2 rounded-full bg-primary" style="width: {{ $program['percent'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section class="grid gap-6 lg:grid-cols-2">
                    <div class="surface p-6">
                        <h3 class="text-lg font-semibold text-grayTheme-dark">Upcoming Expirations</h3>
                        <p class="text-sm text-grayTheme-medium">Plan renewals before deadlines.</p>

                        <div class="mt-4 space-y-3">
                            @foreach ($expiring as $item)
                                <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-grayTheme-border bg-white px-4 py-3">
                                    <div>
                                        <p class="text-sm font-semibold text-grayTheme-dark">{{ $item['name'] }}</p>
                                        <p class="text-xs text-grayTheme-medium">{{ $item['type'] }} • Expires {{ $item['date'] }}</p>
                                    </div>
                                    <span class="rounded-full bg-accent-soft px-3 py-1 text-xs font-semibold text-accent-hover">{{ $item['status'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="surface p-6">
                        <h3 class="text-lg font-semibold text-grayTheme-dark">Recent Uploads</h3>
                        <p class="text-sm text-grayTheme-medium">Your latest document activity.</p>

                        <div class="mt-4 space-y-3">
                            @foreach ($uploads as $upload)
                                <div class="flex items-center gap-4 rounded-2xl border border-grayTheme-border px-4 py-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-soft">
                                        <svg class="h-5 w-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                            <path d="M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="truncate text-sm font-semibold text-grayTheme-dark">{{ $upload['file'] }}</p>
                                        <p class="text-xs text-grayTheme-medium">{{ $upload['type'] }} • {{ $upload['time'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
