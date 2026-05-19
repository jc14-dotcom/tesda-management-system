@forelse ($recentNotifications as $notif)
    <div
        class="notif-item group relative flex cursor-pointer items-start gap-3 px-4 py-3.5 transition hover:bg-primary-soft/60 {{ $notif->read_at ? '' : 'bg-primary-soft/30' }}"
        data-notif-id="{{ $notif->id }}"
        data-read="{{ $notif->read_at ? '1' : '0' }}"
        data-read-url="{{ route('account.notifications.mark-read', $notif->id) }}"
        data-delete-url="{{ route('account.notifications.delete', $notif->id) }}"
        data-view-url="{{ $notificationsIndexUrl }}"
        role="button"
        tabindex="0"
    >
        <span data-notif-dot class="mt-2 h-2 w-2 shrink-0 rounded-full {{ $notif->read_at ? 'bg-transparent' : 'bg-primary' }}"></span>
        <div class="min-w-0 flex-1">
            <p data-notif-title class="truncate text-sm {{ $notif->read_at ? 'font-medium text-grayTheme-medium' : 'font-semibold text-grayTheme-dark' }}">
                {{ $notif->data['certificate_name'] ?? 'Notification' }}
            </p>
            <p class="mt-0.5 line-clamp-2 text-xs leading-relaxed text-grayTheme-medium">
                @if (!empty($notif->data['days_until_expiry']))
                    Expiring in <strong>{{ $notif->data['days_until_expiry'] }} {{ $notif->data['days_until_expiry'] == 1 ? 'day' : 'days' }}</strong>@if (!empty($notif->data['expiration_date'])) &mdash; {{ \Carbon\Carbon::parse($notif->data['expiration_date'])->format('M d, Y') }}@endif
                @else
                    {{ $notif->data['message'] ?? 'You have a new notification.' }}
                @endif
            </p>
            <p class="mt-1 text-[11px] text-grayTheme-medium/70">{{ $notif->created_at->diffForHumans() }}</p>
        </div>
        <button
            type="button"
            class="notif-delete-btn mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-grayTheme-medium opacity-0 transition hover:bg-red-100 hover:text-danger group-hover:opacity-100 focus:outline-none focus:opacity-100"
            aria-label="Delete notification"
        >
            <svg class="pointer-events-none h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@empty
    <div class="flex flex-col items-center justify-center gap-2 py-12 text-center">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-grayTheme-light">
            <svg class="h-6 w-6 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>
        <p class="text-sm font-semibold text-grayTheme-dark">All caught up!</p>
        <p class="text-xs text-grayTheme-medium">No notifications to show.</p>
    </div>
@endforelse
