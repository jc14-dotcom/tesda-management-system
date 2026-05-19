<x-app-layout>
    <div class="py-8">
        <div class="page-container space-y-8">

            {{-- ── Hero Welcome Banner ── --}}
            <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary to-primary-hover p-6 text-white shadow-card sm:p-8">
                {{-- decorative background shapes --}}
                <div class="pointer-events-none absolute -right-8 -top-8 h-44 w-44 rounded-full bg-white/5"></div>
                <div class="pointer-events-none absolute -bottom-6 right-28 h-28 w-28 rounded-full bg-accent/10"></div>
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/60">Account Overview</p>
                        <h2 class="mt-2 text-2xl font-bold sm:text-3xl">Welcome back, {{ auth()->user()->name }}</h2>
                        <p class="mt-1.5 text-sm text-white/75">Here is a snapshot of your certificates, documents, and upcoming renewals.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('account.certificates') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-white/20">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Zm4 4h6m-6 4h6"/></svg>
                            Certificates
                        </a>
                        <a href="{{ route('account.documents') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-white/20">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Documents
                        </a>
                    </div>
                </div>
            </section>

            {{-- ── Stat Cards ── --}}
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($statCards as $card)
                    <div class="surface overflow-hidden">
                        <div class="h-1 w-full bg-gradient-to-r from-primary to-accent"></div>
                        <div class="flex flex-col gap-4 p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">{{ $card['label'] }}</p>
                                    <p class="mt-2 text-4xl font-bold tracking-tight text-grayTheme-dark">{{ $card['value'] }}</p>
                                </div>
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl {{ $card['tone'] }}">
                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75">
                                        <path d="{{ $card['icon'] }}" />
                                    </svg>
                                </div>
                            </div>
                            <div class="border-t border-grayTheme-border pt-3">
                                <p class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">{{ $card['note'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>

            {{-- ── Expiry Timeline + Certificates by Level ── --}}
            <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">

                {{-- Expiry Timeline --}}
                <div class="surface overflow-hidden">
                    <div class="flex items-center justify-between gap-4 border-b border-grayTheme-border bg-warning-soft px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-warning/15">
                                <svg class="h-5 w-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m7-3A10 10 0 1 1 2 12a10 10 0 0 1 20 0Z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-grayTheme-dark">Expiry Timeline</h3>
                                <p class="text-xs text-grayTheme-medium">Certificates expiring over the next 90 days.</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-primary px-3 py-1 text-xs font-semibold text-white shadow-sm">Next 90 days</span>
                    </div>

                    <div class="p-6">
                        <div class="rounded-2xl border border-grayTheme-border bg-grayTheme-light p-4">
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
                            <div class="mt-4 grid grid-cols-3 gap-4 border-t border-grayTheme-border pt-4">
                                <div class="text-center">
                                    <p class="text-lg font-bold text-grayTheme-dark">{{ $expiringSoon30 }}</p>
                                    <p class="text-xs text-grayTheme-medium">In 30 days</p>
                                </div>
                                <div class="border-x border-grayTheme-border text-center">
                                    <p class="text-lg font-bold text-grayTheme-dark">{{ $expiring60 }}</p>
                                    <p class="text-xs text-grayTheme-medium">In 60 days</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-bold text-grayTheme-dark">{{ $expiring90 }}</p>
                                    <p class="text-xs text-grayTheme-medium">In 90 days</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Certificates by Level --}}
                <div class="surface overflow-hidden">
                    <div class="flex items-center gap-3 border-b border-grayTheme-border bg-primary-soft px-6 py-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary/10">
                            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-grayTheme-dark">Certificates by Level</h3>
                            <p class="text-xs text-grayTheme-medium">Distribution of TESDA classifications.</p>
                        </div>
                    </div>

                    <div class="space-y-4 p-6">
                        @forelse ($programs as $program)
                            <div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-semibold text-grayTheme-dark">{{ $program['label'] }}</span>
                                    <span class="text-xs font-semibold text-grayTheme-medium">{{ $program['value'] }} <span class="text-grayTheme-medium/60">({{ $program['percent'] }}%)</span></span>
                                </div>
                                <div class="mt-2 h-2.5 w-full overflow-hidden rounded-full bg-grayTheme-hover">
                                    <div class="h-2.5 rounded-full bg-gradient-to-r from-primary to-accent transition-all duration-500" style="width: {{ $program['percent'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center gap-2 py-6 text-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-soft">
                                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-grayTheme-dark">No certificates yet</p>
                                <p class="text-xs text-grayTheme-medium">Your certificate distribution will appear here.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            {{-- ── Upcoming Expirations + Recent Uploads ── --}}
            <section class="grid gap-6 lg:grid-cols-2">

                {{-- Upcoming Expirations --}}
                <div class="surface overflow-hidden">
                    <div class="flex items-center justify-between gap-4 bg-warning px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white">Upcoming Expirations</h3>
                                <p class="text-xs text-white/70">Plan renewals before deadlines.</p>
                            </div>
                        </div>
                        <a href="{{ route('account.certificates') }}" class="shrink-0 whitespace-nowrap rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-semibold text-white transition-colors hover:bg-white/20">View all</a>
                    </div>

                    <div class="divide-y divide-grayTheme-border">
                        @forelse ($expiring as $item)
                            <div class="flex items-center justify-between gap-4 px-6 py-4 transition-colors hover:bg-grayTheme-light/60">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-warning-soft">
                                        <svg class="h-5 w-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Zm4 4h6m-6 4h6"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-grayTheme-dark">{{ $item['name'] }}</p>
                                        <p class="text-xs text-grayTheme-medium">{{ $item['type'] }} · Expires {{ $item['date'] }}</p>
                                    </div>
                                </div>
                                <span class="shrink-0 rounded-full bg-accent-soft px-3 py-1 text-xs font-semibold text-accent-hover">{{ $item['status'] }}</span>
                            </div>
                        @empty
                            <div class="flex flex-col items-center gap-2 py-10 text-center">
                                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-success-soft">
                                    <svg class="h-5 w-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-grayTheme-dark">All good!</p>
                                <p class="text-xs text-grayTheme-medium">No certificates expiring soon.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Uploads --}}
                <div class="surface overflow-hidden">
                    <div class="flex items-center justify-between gap-4 bg-info px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white">Recent Uploads</h3>
                                <p class="text-xs text-white/70">Your latest document activity.</p>
                            </div>
                        </div>
                        <a href="{{ route('account.documents') }}" class="shrink-0 whitespace-nowrap rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-semibold text-white transition-colors hover:bg-white/20">View all</a>
                    </div>

                    <div class="divide-y divide-grayTheme-border">
                        @forelse ($uploads as $upload)
                            <div class="flex items-center gap-4 px-6 py-4 transition-colors hover:bg-grayTheme-light/60">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-info-soft">
                                    <svg class="h-5 w-5 text-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75">
                                        <path d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-grayTheme-dark">{{ $upload['file'] }}</p>
                                    <p class="text-xs text-grayTheme-medium">{{ $upload['type'] }} · {{ $upload['time'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center gap-2 py-10 text-center">
                                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-info-soft">
                                    <svg class="h-5 w-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-grayTheme-dark">No documents yet</p>
                                <p class="text-xs text-grayTheme-medium">Uploaded documents will appear here.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </section>
        </div>
    </div>
</x-app-layout>
