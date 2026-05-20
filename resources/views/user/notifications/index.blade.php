<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Notifications"
                subtitle="Stay up to date with your account alerts and certificate reminders."
                eyebrow="Account"
            />

            @php $unreadCount = Auth::user()->unreadNotifications()->count(); @endphp

            <div class="surface overflow-hidden">
                {{-- Header bar --}}
                <div class="flex items-center justify-between border-b border-grayTheme-border px-6 py-4">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-grayTheme-dark">All Notifications</span>
                        @if ($unreadCount > 0)
                            <span id="page-unread-badge"
                                class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-danger px-1 text-[10px] font-bold text-white">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </div>
                    @if ($unreadCount > 0)
                        <button
                            type="button"
                            id="page-mark-all-btn"
                            data-url="{{ route('account.notifications.mark-all-read') }}"
                            class="inline-flex items-center gap-1.5 rounded-button border border-grayTheme-border bg-white px-3 py-1.5 text-xs font-semibold text-grayTheme-dark shadow-sm transition hover:border-primary hover:bg-primary-soft hover:text-primary focus:outline-none"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Mark all as read
                        </button>
                    @endif
                </div>

                {{-- Notification list --}}
                <div id="page-notif-list">
                    @forelse ($notifications as $notification)
                        @php
                            $nDays    = $notification->data['days_until_expiry'] ?? null;
                            $nTypeKey = $notification->data['type'] ?? null;

                            if ($nTypeKey === 'certificate_expired' || ($nDays !== null && $nDays <= 0)) {
                                $nIconPath  = 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z';
                                $nIconBg    = $notification->read_at ? 'bg-grayTheme-light' : 'bg-danger-soft';
                                $nIconColor = $notification->read_at ? 'text-grayTheme-medium' : 'text-danger';
                                $nBadge     = 'Expired';
                                $nBadgeCss  = $notification->read_at ? 'bg-grayTheme-light text-grayTheme-medium' : 'bg-danger-soft text-danger';
                            } elseif ($nTypeKey === 'certificate_expiry' || $nDays !== null) {
                                $nIconPath  = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
                                $nIconBg    = $notification->read_at ? 'bg-grayTheme-light' : 'bg-warning-soft';
                                $nIconColor = $notification->read_at ? 'text-grayTheme-medium' : 'text-warning';
                                $nBadge     = ($nDays !== null && $nDays <= 7) ? 'Expiring Soon' : 'Expiring';
                                $nBadgeCss  = $notification->read_at ? 'bg-grayTheme-light text-grayTheme-medium' : 'bg-warning-soft text-warning';
                            } else {
                                $nIconPath  = 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9';
                                $nIconBg    = $notification->read_at ? 'bg-grayTheme-light' : 'bg-primary-soft';
                                $nIconColor = $notification->read_at ? 'text-grayTheme-medium' : 'text-primary';
                                $nBadge     = 'Notification';
                                $nBadgeCss  = $notification->read_at ? 'bg-grayTheme-light text-grayTheme-medium' : 'bg-primary-soft text-primary';
                            }
                        @endphp

                        <div
                            id="page-notif-{{ $notification->id }}"
                            data-notif-id="{{ $notification->id }}"
                            data-read="{{ $notification->read_at ? '1' : '0' }}"
                            data-read-url="{{ route('account.notifications.mark-read', $notification->id) }}"
                            class="notif-row group relative flex items-start gap-4 px-6 py-5 transition-colors
                                {{ !$loop->last ? 'border-b border-grayTheme-border' : '' }}
                                {{ $notification->read_at ? 'hover:bg-grayTheme-light' : 'cursor-pointer bg-primary-soft hover:bg-[#E2E6FF]' }}"
                        >
                            {{-- Type icon --}}
                            <div class="notif-icon-wrap flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $nIconBg }}">
                                <svg class="notif-icon-svg h-5 w-5 {{ $nIconColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $nIconPath }}" />
                                </svg>
                            </div>

                            {{-- Content --}}
                            <div class="min-w-0 flex-1">
                                {{-- Top row: title + badge  |  unread dot + time + delete --}}
                                <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-1">
                                    <div class="flex min-w-0 flex-wrap items-center gap-2">
                                        <p class="notif-title text-sm {{ $notification->read_at ? 'font-medium text-grayTheme-medium' : 'font-bold text-grayTheme-dark' }}">
                                            {{ $notification->data['certificate_name'] ?? 'Certificate Notification' }}
                                        </p>
                                        <span class="notif-badge inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $nBadgeCss }}">
                                            {{ $nBadge }}
                                        </span>
                                    </div>

                                    <div class="flex shrink-0 items-center gap-2">
                                        @if (!$notification->read_at)
                                            <span class="notif-unread-dot h-2 w-2 shrink-0 rounded-full bg-primary"></span>
                                        @endif
                                        <span class="whitespace-nowrap text-xs text-grayTheme-medium">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                        <button
                                            type="button"
                                            data-delete-url="{{ route('account.notifications.delete', $notification->id) }}"
                                            data-notif-id="{{ $notification->id }}"
                                            class="notif-delete-btn flex h-6 w-6 shrink-0 items-center justify-center rounded text-grayTheme-medium opacity-0 transition-all hover:bg-danger-soft hover:text-danger group-hover:opacity-100 focus:opacity-100 focus:outline-none"
                                            title="Delete"
                                            onclick="event.stopPropagation()"
                                        >
                                            <svg class="pointer-events-none h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Body text --}}
                                <p class="mt-1 text-sm text-grayTheme-medium">
                                    @if ($nDays !== null && $nDays <= 0)
                                        This certificate has expired.
                                        @if (!empty($notification->data['expiration_date']))
                                            Expired on <strong>{{ \Carbon\Carbon::parse($notification->data['expiration_date'])->format('F j, Y') }}</strong>.
                                        @endif
                                    @elseif ($nDays !== null)
                                        Expires in
                                        <span class="font-semibold {{ $nDays <= 7 ? 'text-danger' : ($nDays <= 30 ? 'text-warning' : 'text-grayTheme-dark') }}">
                                            {{ $nDays }} {{ $nDays == 1 ? 'day' : 'days' }}
                                        </span>.
                                        @if (!empty($notification->data['expiration_date']))
                                            Expiry date: <strong>{{ \Carbon\Carbon::parse($notification->data['expiration_date'])->format('F j, Y') }}</strong>.
                                        @endif
                                    @else
                                        {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                    @endif
                                </p>

                                {{-- Certificate type label --}}
                                @if (!empty($notification->data['certificate_type_label']))
                                    <p class="mt-0.5 text-xs text-grayTheme-medium">
                                        Type: <span class="font-medium text-grayTheme-dark">{{ $notification->data['certificate_type_label'] }}</span>
                                    </p>
                                @endif

                                {{-- Explicit mark-as-read link (unread only) --}}
                                @if (!$notification->read_at)
                                    <button
                                        type="button"
                                        data-notif-id="{{ $notification->id }}"
                                        data-read-url="{{ route('account.notifications.mark-read', $notification->id) }}"
                                        class="notif-mark-read-btn mt-2 inline-flex items-center gap-1 text-xs font-semibold text-primary transition hover:text-primary-hover focus:outline-none"
                                        onclick="event.stopPropagation()"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Mark as read
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center gap-3 py-20 text-center">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-grayTheme-light">
                                <svg class="h-8 w-8 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <p class="text-base font-bold text-grayTheme-dark">No notifications yet</p>
                            <p class="text-sm text-grayTheme-medium">You're all caught up! We'll notify you when something important happens.</p>
                        </div>
                    @endforelse
                </div>

                @if ($notifications->hasPages())
                    <div class="border-t border-grayTheme-border px-6 py-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (function () {
        const CSRF = document.head.querySelector('meta[name="csrf-token"]')?.content ?? '';

        function updateUnreadBadge(delta) {
            const badge = document.getElementById('page-unread-badge');
            if (!badge) return;
            const next = (parseInt(badge.textContent) || 0) + delta;
            if (next <= 0) {
                badge.remove();
                document.getElementById('page-mark-all-btn')?.remove();
            } else {
                badge.textContent = next;
            }
        }

        function markRowAsRead(row) {
            // Row background + cursor
            row.classList.remove('bg-primary-soft', 'cursor-pointer');
            row.style.removeProperty('background-color');
            row.classList.add('hover:bg-grayTheme-light');
            row.dataset.read = '1';

            // Unread indicators
            row.querySelector('.notif-unread-dot')?.remove();
            row.querySelector('.notif-mark-read-btn')?.remove();

            // Title weight
            const title = row.querySelector('.notif-title');
            if (title) {
                title.classList.remove('font-bold', 'text-grayTheme-dark');
                title.classList.add('font-medium', 'text-grayTheme-medium');
            }

            // Icon circle colour
            const iconWrap = row.querySelector('.notif-icon-wrap');
            if (iconWrap) {
                ['bg-danger-soft', 'bg-warning-soft', 'bg-primary-soft'].forEach(c => iconWrap.classList.remove(c));
                iconWrap.classList.add('bg-grayTheme-light');
            }

            // Icon SVG colour
            const iconSvg = row.querySelector('.notif-icon-svg');
            if (iconSvg) {
                ['text-danger', 'text-warning', 'text-primary'].forEach(c => iconSvg.classList.remove(c));
                iconSvg.classList.add('text-grayTheme-medium');
            }

            // Badge pill colour
            const badge = row.querySelector('.notif-badge');
            if (badge) {
                ['bg-danger-soft', 'bg-warning-soft', 'bg-primary-soft',
                 'text-danger', 'text-warning', 'text-primary'].forEach(c => badge.classList.remove(c));
                badge.classList.add('bg-grayTheme-light', 'text-grayTheme-medium');
            }
        }

        document.addEventListener('click', function (e) {

            // ── Mark all as read ─────────────────────────────────────────
            const markAllBtn = e.target.closest('#page-mark-all-btn');
            if (markAllBtn) {
                const url = markAllBtn.dataset.url;
                markAllBtn.disabled = true;
                fetch(url, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) { markAllBtn.disabled = false; return; }
                        document.querySelectorAll('.notif-row[data-read="0"]').forEach(row => markRowAsRead(row));
                        document.getElementById('page-unread-badge')?.remove();
                        markAllBtn.remove();
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: { type: 'success', title: 'All marked as read', message: 'All notifications have been marked as read.' }
                        }));
                    })
                    .catch(() => { markAllBtn.disabled = false; });
                return;
            }

            // ── Mark single as read (explicit button) ────────────────────
            const markReadBtn = e.target.closest('.notif-mark-read-btn');
            if (markReadBtn) {
                const url = markReadBtn.dataset.readUrl;
                const id  = markReadBtn.dataset.notifId;
                fetch(url, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) return;
                        const row = document.getElementById('page-notif-' + id);
                        if (row) markRowAsRead(row);
                        updateUnreadBadge(-1);
                    })
                    .catch(() => {});
                return;
            }

            // ── Delete ───────────────────────────────────────────────────
            const deleteBtn = e.target.closest('.notif-delete-btn');
            if (deleteBtn) {
                const url       = deleteBtn.dataset.deleteUrl;
                const id        = deleteBtn.dataset.notifId;
                const row       = document.getElementById('page-notif-' + id);
                const wasUnread = row?.dataset.read === '0';
                fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) return;
                        if (row) {
                            row.style.transition = 'opacity 200ms ease';
                            row.style.opacity = '0';
                            setTimeout(() => row.remove(), 210);
                        }
                        if (wasUnread) updateUnreadBadge(-1);
                    })
                    .catch(() => {});
                return;
            }

            // ── Row click → mark as read ─────────────────────────────────
            const row = e.target.closest('.notif-row');
            if (row && row.dataset.read === '0') {
                const url = row.dataset.readUrl;
                fetch(url, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) return;
                        markRowAsRead(row);
                        updateUnreadBadge(-1);
                    })
                    .catch(() => {});
            }
        });
    })();
    </script>
    @endpush
</x-app-layout>
