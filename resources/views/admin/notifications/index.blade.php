<x-app-layout>
    <div class="py-12" x-data="{
        confirmOpen: false,
        submitting: false,
        confirmTitle: '',
        confirmMessage: '',
        pendingDeleteUrl: '',
        pendingFormRef: 'notifDeleteForm',
        askConfirm(title, message, url, formRef) {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.pendingDeleteUrl = url;
            this.pendingFormRef = formRef;
            this.confirmOpen = true;
        },
        runConfirm() {
            this.submitting = true;
            document.getElementById(this.pendingFormRef).submit();
            this.confirmOpen = false;
        }
    }">
        <div class="page-container space-y-6">
            <x-page-header
                title="Notifications"
                subtitle="System alerts, user activity, and certificate expiry notifications."
                eyebrow="Administration"
            />

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Total Notifications</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                </div>
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Unread</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($stats['unread']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-warning-soft">
                        <svg class="h-6 w-6 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Sent This Week</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($stats['thisWeek']) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-soft">
                        <svg class="h-6 w-6 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    </div>
                </div>
            </div>

            {{-- Flash messages handled by toast notifications --}}

        {{-- Filters --}}
        <div class="surface p-6">
            <form method="get" x-data="liveSearch()">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="w-full sm:flex-1 sm:min-w-48">
                        <label class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium" for="notif_search">Search</label>
                        <input id="notif_search" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search user name…" class="mt-1 form-input w-full"
                            @input.debounce.400ms="search($el.closest('form'))" />
                    </div>
                    <div class="w-full sm:w-auto">
                        <label class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium" for="notif_status">Status</label>
                        <select id="notif_status" name="status" class="mt-1 form-input">
                            <option value="all"    @selected(($status ?? 'all') === 'all')>All</option>
                            <option value="unread" @selected(($status ?? '') === 'unread')>Unread</option>
                            <option value="read"   @selected(($status ?? '') === 'read')>Read</option>
                        </select>
                    </div>
                    <div class="w-full sm:w-auto">
                        <label class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium" for="notif_type">Type</label>
                        <select id="notif_type" name="type" class="mt-1 form-input">
                            <option value="all"                    @selected(($type ?? 'all') === 'all')>All Types</option>
                            <option value="admin"                  @selected(($type ?? '') === 'admin')>Admin Notifications</option>
                            <option value="user"                   @selected(($type ?? '') === 'user')>User Expiry Alerts</option>
                            <option disabled>──────────</option>
                            <option value="certificate_submitted"  @selected(($type ?? '') === 'certificate_submitted')>New Certificate</option>
                            <option value="verification_reminder"  @selected(($type ?? '') === 'verification_reminder')>Pending Verification</option>
                            <option value="certificate_expiry"     @selected(($type ?? '') === 'certificate_expiry')>Certificate Expiring</option>
                            <option value="certificate_expired"    @selected(($type ?? '') === 'certificate_expired')>Certificate Expired</option>
                            <option value="document_uploaded"      @selected(($type ?? '') === 'document_uploaded')>New Document</option>
                            <option value="user_registered"        @selected(($type ?? '') === 'user_registered')>New User</option>
                            <option value="user_status_changed"    @selected(($type ?? '') === 'user_status_changed')>Status Changed</option>
                            <option value="weekly_digest"          @selected(($type ?? '') === 'weekly_digest')>Weekly Digest</option>
                        </select>
                    </div>
                </div>
                @php $hasFilters = ($search ?? '') || ($status ?? 'all') !== 'all' || ($type ?? 'all') !== 'all'; @endphp
                <div class="mt-4 flex items-center gap-2">
                    <button type="button" class="btn-danger inline-flex items-center gap-1.5 text-sm"
                        @click="askConfirm('Clear All Notifications', 'Permanently delete all notifications? This cannot be undone.', '{{ route('admin.notifications.destroy-all') }}', 'clearAllForm')"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Clear All
                    </button>
                    <div class="ml-auto flex items-center gap-2">
                        <a href="{{ route('admin.notifications.index') }}" class="btn-secondary inline-flex items-center gap-1.5 {{ !$hasFilters ? 'pointer-events-none opacity-40' : '' }}">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reset
                        </a>
                        <button type="submit" class="btn-primary inline-flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                            Apply
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Hidden action forms (outside nested form and live-search area so they always exist in DOM) --}}
        <form id="clearAllForm" method="post" action="{{ route('admin.notifications.destroy-all') }}" class="hidden">@csrf <input type="hidden" name="_method" value="DELETE"></form>
        <form id="notifDeleteForm" method="post" :action="pendingDeleteUrl" class="hidden">@csrf <input type="hidden" name="_method" value="DELETE"></form>

        <div id="live-search-results">
        <div class="surface overflow-hidden rounded-xl shadow-sm">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-primary">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Notification</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Sent</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-grayTheme-border">
                    @forelse($notifications ?? [] as $notif)
                        @php
                            $subjectName = $notif->data['user_name'] ?? $notif->notifiable?->name;
                            $subjectId   = $notif->data['user_id']   ?? $notif->notifiable_id;
                            $typeKey     = $notif->data['type'] ?? null;
                            [$typeLabel, $typeCss] = match($typeKey) {
                                'certificate_submitted' => ['New Certificate',   'bg-primary-soft text-primary'],
                                'certificate_expiry'    => ['Expiring Soon',     'bg-warning-soft text-warning'],
                                'certificate_expired'   => ['Expired',           'bg-danger-soft text-danger'],
                                'document_uploaded'     => ['New Document',      'bg-primary-soft text-primary'],
                                'user_registered'       => ['New User',          'bg-success-soft text-success'],
                                'user_status_changed'   => ['Status Changed',    'bg-warning-soft text-warning'],
                                'verification_reminder' => ['Pending Review',    'bg-warning-soft text-warning'],
                                'weekly_digest'         => ['Weekly Digest',     'bg-primary-soft text-primary'],
                                default                 => ['Expiry Alert',      'bg-primary-soft text-primary'],
                            };
                        @endphp
                        <tr class="transition hover:bg-grayTheme-light/60">
                            <td class="px-4 py-3">
                                @if($subjectName)
                                    <a href="{{ route('admin.users.show', $subjectId) }}" class="inline-flex items-center gap-1.5 font-medium text-primary hover:underline">
                                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-soft text-xs font-bold text-primary">{{ strtoupper(substr($subjectName, 0, 1)) }}</div>
                                        {{ $subjectName }}
                                    </a>
                                @else
                                    <span class="text-xs italic text-grayTheme-medium">Unknown</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $typeCss }}">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                @if(!empty($notif->data['title']))
                                    <p class="text-xs font-semibold text-grayTheme-dark">{{ $notif->data['title'] }}</p>
                                @endif
                                <p class="line-clamp-2 text-sm text-grayTheme-medium">{{ $notif->data['message'] ?? ($notif->data['body'] ?? \Illuminate\Support\Str::limit(json_encode($notif->data), 60)) }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @if($notif->read_at)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-grayTheme-hover px-2.5 py-0.5 text-xs font-semibold text-grayTheme-medium">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Read
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-semibold text-primary">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                        Unread
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-grayTheme-medium">{{ $notif->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1">
                                    @if(!empty($notif->data['url']))
                                        <a href="{{ $notif->data['url'] }}" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-primary transition hover:bg-primary-soft focus:outline-none">
                                            View
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                        </a>
                                    @endif
                                    <button type="button"
                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-danger transition hover:bg-danger-soft focus:outline-none"
                                        @click="askConfirm('Delete Notification', 'Permanently delete this notification?', '{{ route('admin.notifications.destroy', $notif->id) }}', 'notifDeleteForm')"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex w-full flex-col items-center gap-2">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-grayTheme-light">
                                        <svg class="h-6 w-6 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-grayTheme-dark">No notifications found</p>
                                    <p class="text-xs text-grayTheme-medium">All caught up!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @isset($notifications)
            <div class="mt-4">{{ $notifications->links() }}</div>
        @endisset
        </div>

        {{-- Confirmation Modal --}}
        <div x-cloak x-show="confirmOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4"
            @keydown.escape.window="confirmOpen = false" @click.self="confirmOpen = false">
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-danger-soft">
                        <svg class="h-5 w-5 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-grayTheme-dark" x-text="confirmTitle"></h3>
                        <p class="mt-1 text-sm text-grayTheme-medium" x-text="confirmMessage"></p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn-secondary" @click="confirmOpen = false">Cancel</button>
                    <button type="button" class="btn-danger gap-2" @click="runConfirm()" x-bind:disabled="submitting">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Confirm
                    </button>
                </div>
            </div>
        </div>

        </div>
    </div>

    {{-- Bridge session flash messages to toast notifications --}}
    @if(session('status') === 'notification-deleted')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Notification Deleted',message:'The notification has been removed.'}}));</script>
    @elseif(session('status') === 'all-notifications-deleted')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'All Cleared',message:'All notifications have been deleted.'}}));</script>
    @endif
</x-app-layout>
