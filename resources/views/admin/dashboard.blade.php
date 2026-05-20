<x-app-layout>
    <div class="py-8">
        <div class="page-container space-y-8">
            <x-page-header
                title="Admin Dashboard"
                subtitle="Track users, certificates, and system activity at a glance."
                eyebrow="Administration"
            />

            {{-- ── Stat Cards ── --}}
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($statCards as $card)
                    <div class="surface overflow-hidden">
                        {{-- colored top accent stripe --}}
                        <div class="h-1 w-full bg-gradient-to-r from-primary to-accent"></div>
                        <div class="flex flex-col gap-4 p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">{{ $card['label'] }}</p>
                                    <p class="mt-2 text-4xl font-bold tracking-tight text-grayTheme-dark" data-live-key="{{ strtolower(str_replace(' ', '-', $card['label'])) }}">{{ $card['value'] ?? '—' }}</p>
                                </div>
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl {{ $card['tone'] }}">
                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75">
                                        <path d="{{ $card['icon'] }}" />
                                    </svg>
                                </div>
                            </div>
                            <div class="border-t border-grayTheme-border pt-3">
                                <p class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" data-live-note="{{ strtolower(str_replace(' ', '-', $card['label'])) }}">{{ $card['note'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>

            {{-- ── Expiring Certificates + Recent Activity ── --}}
            <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">

                {{-- Expiring Certificates --}}
                <div class="surface overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-grayTheme-border bg-warning-soft px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-warning/15">
                                <svg class="h-5 w-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-grayTheme-dark">Expiring Certificates</h3>
                                <p class="text-xs text-grayTheme-medium">Focus on renewals over the next 60 days.</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-warning px-3 py-1 text-xs font-semibold text-white shadow-sm">Next 60 days</span>
                    </div>

                    <div class="overflow-x-auto overflow-y-auto" style="max-height:320px;">
                        <table class="min-w-full text-sm">
                            <thead class="sticky top-0 z-10">
                                <tr class="border-b border-grayTheme-border bg-grayTheme-light">
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Certificate</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Expires</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-grayTheme-border">
                                @forelse ($expiringCertificates as $item)
                                    <tr class="transition-colors hover:bg-grayTheme-light/60">
                                        <td class="px-6 py-3.5 font-semibold text-grayTheme-dark">{{ $item['user'] }}</td>
                                        <td class="px-6 py-3.5 text-grayTheme-medium">{{ $item['certificate'] }}</td>
                                        <td class="px-6 py-3.5 text-grayTheme-medium">{{ $item['expires'] }}</td>
                                        <td class="px-6 py-3.5">
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item['tone'] }}">{{ $item['status'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-success-soft">
                                                    <svg class="h-5 w-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                                <p class="text-sm font-semibold text-grayTheme-dark">All clear!</p>
                                                <p class="text-xs text-grayTheme-medium">No certificates expiring in the next 60 days.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-grayTheme-border bg-grayTheme-light/40 px-6 py-3 text-right">
                        <a href="{{ route('admin.certificates.index') }}" class="text-xs font-semibold text-primary hover:underline">View all certificates →</a>
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="surface flex flex-col overflow-hidden">
                    <div class="flex items-center gap-3 border-b border-grayTheme-border bg-primary-soft px-6 py-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary/10">
                            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-grayTheme-dark">Recent Activity</h3>
                            <p class="text-xs text-grayTheme-medium">Latest updates from admins and users.</p>
                        </div>
                    </div>

                    <div id="live-activity-list" class="divide-y divide-grayTheme-border overflow-y-auto" style="max-height:380px;">
                        @forelse ($recentActivity as $activity)
                            <div class="flex items-start justify-between gap-3 px-6 py-4 transition-colors hover:bg-grayTheme-light/60">
                                <div class="flex items-start gap-3">
                                    <div class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-primary"></div>
                                    <div>
                                        <p class="text-sm font-semibold text-grayTheme-dark">{{ $activity['title'] }}</p>
                                        <p class="text-xs text-grayTheme-medium">{{ $activity['meta'] }}</p>
                                    </div>
                                </div>
                                <span class="shrink-0 text-xs font-medium text-grayTheme-medium">{{ $activity['time'] }}</span>
                            </div>
                        @empty
                            <div class="flex flex-1 flex-col items-center justify-center gap-2 text-center">
                                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-primary-soft">
                                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-grayTheme-dark">No activity yet</p>
                                <p class="text-xs text-grayTheme-medium">Actions taken by users will appear here.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="border-t border-grayTheme-border bg-grayTheme-light/40 px-6 py-3 text-right">
                        <a href="{{ route('admin.activity.index') }}" class="text-xs font-semibold text-primary hover:underline">View activity log →</a>
                    </div>
                </div>
            </section>

            {{-- ── Recent Users + Recent Uploads ── --}}
            <section class="grid gap-6 lg:grid-cols-3">

                {{-- Recent Users --}}
                <div class="surface overflow-hidden lg:col-span-2">
                    <div class="flex items-center justify-between gap-4 bg-primary px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white">Recent Users</h3>
                                <p class="text-xs text-white/70">New accounts created recently.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="shrink-0 whitespace-nowrap rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-semibold text-white transition-colors hover:bg-white/20">View all</a>
                    </div>

                    <div class="overflow-x-auto overflow-y-auto" style="max-height:320px;">
                        <table class="min-w-full text-sm">
                            <thead class="sticky top-0 z-10">
                                <tr class="border-b border-grayTheme-border bg-grayTheme-light">
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Joined</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-grayTheme-border">
                                @forelse ($recentUsers as $user)
                                    <tr class="transition-colors hover:bg-grayTheme-light/60">
                                        <td class="px-6 py-3.5 font-semibold text-grayTheme-dark">{{ $user['name'] }}</td>
                                        <td class="px-6 py-3.5 text-grayTheme-medium">{{ $user['email'] }}</td>
                                        <td class="px-6 py-3.5">
                                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $user['role'] === 'Admin' ? 'bg-primary-soft text-primary' : 'bg-grayTheme-light text-grayTheme-dark' }}">{{ $user['role'] }}</span>
                                        </td>
                                        <td class="px-6 py-3.5 text-grayTheme-medium">{{ $user['joined'] }}</td>
                                        <td class="px-6 py-3.5">
                                            @php
                                                $statusTone = match(strtolower($user['status'])) {
                                                    'active'   => 'bg-success-soft text-success',
                                                    'pending'  => 'bg-warning-soft text-warning',
                                                    'inactive', 'suspended' => 'bg-danger-soft text-danger',
                                                    default    => 'bg-grayTheme-light text-grayTheme-dark',
                                                };
                                            @endphp
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusTone }}">{{ $user['status'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center">
                                            <p class="text-sm text-grayTheme-medium">No recent users found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
                                <p class="text-xs text-white/70">Latest documents submitted by users.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.documents.index') }}" class="shrink-0 whitespace-nowrap rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-semibold text-white transition-colors hover:bg-white/20">View all</a>
                    </div>

                    <div class="divide-y divide-grayTheme-border overflow-y-auto" style="max-height:320px;">
                        @forelse ($recentUploads as $upload)
                            <div class="flex items-center justify-between gap-4 px-6 py-4 transition-colors hover:bg-grayTheme-light/60">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-info-soft">
                                        <svg class="h-5 w-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-grayTheme-dark">{{ $upload['file'] }}</p>
                                        <p class="text-xs text-grayTheme-medium">{{ $upload['user'] }} · {{ $upload['type'] }}</p>
                                    </div>
                                </div>
                                <span class="shrink-0 text-xs font-medium text-grayTheme-medium">{{ $upload['time'] }}</span>
                            </div>
                        @empty
                            <div class="flex flex-col items-center gap-2 py-10 text-center">
                                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-info-soft">
                                    <svg class="h-5 w-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-grayTheme-dark">No recent uploads</p>
                                <p class="text-xs text-grayTheme-medium">Documents submitted by users will appear here.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </section>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
(function () {
    if (window.__alcattAdminDashPoll) return;
    window.__alcattAdminDashPoll = true;

    var timer = null;

    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function poll() {
        fetch('/admin/dashboard/live', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
        .then(function (d) {
            // Update stat card values
            document.querySelectorAll('[data-live-key]').forEach(function (el) {
                var k = el.dataset.liveKey;
                if (d[k] !== undefined) el.textContent = d[k];
            });
            // Update activity list
            var list = document.getElementById('live-activity-list');
            if (list && Array.isArray(d.recentActivity)) {
                if (d.recentActivity.length) {
                    list.innerHTML = d.recentActivity.map(function (a) {
                        return '<div class="flex items-start justify-between gap-3 px-6 py-4 transition-colors hover:bg-grayTheme-light/60">'
                            + '<div class="flex items-start gap-3">'
                            + '<div class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-primary"></div>'
                            + '<div><p class="text-sm font-semibold text-grayTheme-dark">' + esc(a.title) + '</p>'
                            + '<p class="text-xs text-grayTheme-medium">' + esc(a.meta) + '</p></div>'
                            + '</div>'
                            + '<span class="shrink-0 text-xs font-medium text-grayTheme-medium">' + esc(a.time) + '</span>'
                            + '</div>';
                    }).join('');
                } else {
                    list.innerHTML = '<div class="flex flex-col items-center justify-center gap-2 p-8 text-center">'
                        + '<p class="text-sm font-semibold text-grayTheme-dark">No activity yet</p>'
                        + '<p class="text-xs text-grayTheme-medium">Actions taken by users will appear here.</p></div>';
                }
            }
        })
        .catch(function () {});
    }

    function start() {
        if (document.getElementById('live-activity-list')) {
            clearInterval(timer);
            timer = setInterval(poll, 60000);
        }
    }

    function stop() {
        clearInterval(timer);
        timer = null;
    }

    document.addEventListener('turbo:load', start);
    document.addEventListener('turbo:before-render', stop);
    start();
}());
</script>
@endpush
