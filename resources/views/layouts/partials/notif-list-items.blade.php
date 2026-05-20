@forelse ($recentNotifications as $notif)
    @php
        $nTypeKey = $notif->data['type'] ?? null;
        [$nIconPath, $nIconBg, $nIconColor] = match($nTypeKey) {
            'certificate_submitted' => ['M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'bg-primary-soft', 'text-primary'],
            'certificate_expiry'    => ['M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                                               'bg-warning-soft', 'text-warning'],
            'certificate_expired'   => ['M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',                                                    'bg-danger-soft',  'text-danger'],
            'document_uploaded'     => ['M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',                                                           'bg-primary-soft', 'text-primary'],
            'user_registered'       => ['M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',                                    'bg-success-soft', 'text-success'],
            'user_status_changed'   => ['M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',                         'bg-warning-soft', 'text-warning'],
            'verification_reminder' => ['M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',                                                                              'bg-warning-soft', 'text-warning'],
            'weekly_digest'         => ['M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'bg-primary-soft', 'text-primary'],
            default                 => ['M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'bg-warning-soft', 'text-warning'],
        };
    @endphp
    <div
        class="notif-item group relative flex cursor-pointer items-start gap-3 px-4 py-3.5 transition {{ $notif->read_at ? 'hover:bg-grayTheme-light' : 'bg-primary-soft hover:bg-[#E2E6FF]' }}"
        data-notif-id="{{ $notif->id }}"
        data-read="{{ $notif->read_at ? '1' : '0' }}"
        data-read-url="{{ route('account.notifications.mark-read', $notif->id) }}"
        data-delete-url="{{ route('account.notifications.delete', $notif->id) }}"
        data-view-url="{{ $notificationsIndexUrl }}"
        role="button"
        tabindex="0"
    >
        {{-- Unread dot — top-right corner of the row --}}
        @if (!$notif->read_at)
            <span data-notif-dot class="absolute right-3 top-3 h-2 w-2 rounded-full bg-primary"></span>
        @else
            <span data-notif-dot class="absolute right-3 top-3 h-2 w-2 rounded-full bg-transparent"></span>
        @endif

        {{-- Icon circle --}}
        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $nIconBg }} mt-0.5">
            <svg class="h-3.5 w-3.5 {{ $nIconColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $nIconPath }}"/>
            </svg>
        </div>
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
