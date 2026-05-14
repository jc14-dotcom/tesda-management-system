<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Notifications"
                subtitle="Stay up to date with your account alerts and certificate reminders."
                eyebrow="Account"
            />

            <div class="surface overflow-hidden">
                @php $unreadCount = Auth::user()->unreadNotifications()->count(); @endphp

                {{-- Header bar --}}
                <div class="flex items-center justify-between border-b border-grayTheme-border px-6 py-4">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-grayTheme-dark">All Notifications</span>
                        @if ($unreadCount > 0)
                            <span id="page-unread-badge" class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-danger px-1 text-[10px] font-bold text-white">{{ $unreadCount }}</span>
                        @endif
                    </div>
                    @if ($unreadCount > 0)
                        <button
                            type="button"
                            id="page-mark-all-btn"
                            class="inline-flex items-center gap-1.5 rounded-button border border-grayTheme-border bg-white px-3 py-1.5 text-xs font-semibold text-grayTheme-dark shadow-sm transition hover:border-primary hover:bg-primary-soft hover:text-primary focus:outline-none"
                            data-url="{{ route('account.notifications.mark-all-read') }}"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Mark all as read
                        </button>
                    @endif
                </div>

                {{-- Notification list --}}
                <div id="page-notif-list">
                    @forelse ($notifications as $notification)
                        <div
                            class="flex items-start gap-4 px-6 py-5 transition {{ !$loop->last ? 'border-b border-grayTheme-border' : '' }} {{ $notification->read_at ? '' : 'bg-primary-soft/20' }}"
                            id="page-notif-{{ $notification->id }}"
                        >
                            {{-- Icon badge --}}
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $notification->read_at ? 'bg-grayTheme-light' : 'bg-accent-soft' }}">
                                <svg class="h-5 w-5 {{ $notification->read_at ? 'text-grayTheme-medium' : 'text-accent-active' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>

                            {{-- Content --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-sm {{ $notification->read_at ? 'font-medium text-grayTheme-medium' : 'font-bold text-grayTheme-dark' }}">
                                            {{ $notification->data['certificate_name'] ?? 'Certificate Notification' }}
                                            @if (!empty($notification->data['certificate_type_label']))
                                                <span class="ml-1 text-xs font-normal text-grayTheme-medium">({{ $notification->data['certificate_type_label'] }})</span>
                                            @endif
                                        </p>
                                        <p class="mt-1 text-sm text-grayTheme-medium">
                                            @if (!empty($notification->data['days_until_expiry']))
                                                This certificate is expiring in
                                                <span class="font-semibold {{ $notification->data['days_until_expiry'] <= 7 ? 'text-danger' : ($notification->data['days_until_expiry'] <= 30 ? 'text-accent-active' : 'text-grayTheme-dark') }}">{{ $notification->data['days_until_expiry'] }} {{ $notification->data['days_until_expiry'] == 1 ? 'day' : 'days' }}</span>.
                                                @if (!empty($notification->data['expiration_date']))
                                                    Expiry date: <strong>{{ \Carbon\Carbon::parse($notification->data['expiration_date'])->format('F j, Y') }}</strong>.
                                                @endif
                                            @else
                                                {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-2">
                                        @if (!$notification->read_at)
                                            <span class="h-2 w-2 rounded-full bg-primary"></span>
                                        @endif
                                        <span class="whitespace-nowrap text-xs text-grayTheme-medium">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="mt-3 flex items-center gap-4">
                                    @if (!$notification->read_at)
                                        <button
                                            type="button"
                                            class="page-notif-read-btn inline-flex items-center gap-1 text-xs font-semibold text-primary transition hover:text-primary-hover focus:outline-none"
                                            data-notif-id="{{ $notification->id }}"
                                            data-url="{{ route('account.notifications.mark-read', $notification->id) }}"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Mark as read
                                        </button>
                                    @endif
                                    <button
                                        type="button"
                                        class="page-notif-delete-btn inline-flex items-center gap-1 text-xs font-semibold text-grayTheme-medium transition hover:text-danger focus:outline-none"
                                        data-notif-id="{{ $notification->id }}"
                                        data-url="{{ route('account.notifications.delete', $notification->id) }}"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </div>
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

        document.addEventListener('click', function (e) {
            // Mark all as read
            if (e.target.closest('#page-mark-all-btn')) {
                const btn = document.getElementById('page-mark-all-btn');
                const url = btn?.dataset.url;
                if (!url) return;
                btn.disabled = true;
                fetch(url, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) { btn.disabled = false; return; }
                        document.querySelectorAll('[id^="page-notif-"]').forEach(el => el.classList.remove('bg-primary-soft/20'));
                        document.querySelectorAll('.page-notif-read-btn').forEach(b => b.closest('.mt-3')?.querySelector('.page-notif-read-btn')?.remove());
                        document.getElementById('page-unread-badge')?.remove();
                        btn.remove();
                        window.dispatchEvent(new CustomEvent('show-toast', { detail: { type: 'success', title: 'All marked as read', message: 'All notifications have been marked as read.' } }));
                    }).catch(() => { btn.disabled = false; });
                return;
            }

            // Mark single as read
            if (e.target.closest('.page-notif-read-btn')) {
                const btn = e.target.closest('.page-notif-read-btn');
                const url = btn?.dataset.url;
                const id  = btn?.dataset.notifId;
                if (!url) return;
                fetch(url, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) return;
                        document.getElementById('page-notif-' + id)?.classList.remove('bg-primary-soft/20');
                        btn.remove();
                    }).catch(() => {});
                return;
            }

            // Delete notification
            if (e.target.closest('.page-notif-delete-btn')) {
                const btn = e.target.closest('.page-notif-delete-btn');
                const url = btn?.dataset.url;
                const id  = btn?.dataset.notifId;
                if (!url) return;
                fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) return;
                        const el = document.getElementById('page-notif-' + id);
                        if (el) { el.style.transition = 'opacity 200ms ease'; el.style.opacity = '0'; setTimeout(() => el.remove(), 200); }
                    }).catch(() => {});
                return;
            }
        });
    })();
    </script>
    @endpush
</x-app-layout>

