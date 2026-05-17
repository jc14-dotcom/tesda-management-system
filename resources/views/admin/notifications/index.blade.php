<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Notifications"
                subtitle="All certificate expiry and system notifications."
                eyebrow="Administration"
            />

            {{-- Flash messages --}}
        @if(session('status') === 'notification-deleted')
            <div class="mb-4 rounded-lg bg-success-soft px-4 py-3 text-sm font-semibold text-success">Notification deleted.</div>
        @elseif(session('status') === 'all-notifications-deleted')
            <div class="mb-4 rounded-lg bg-success-soft px-4 py-3 text-sm font-semibold text-success">All notifications cleared.</div>
        @endif

        {{-- Filters --}}
        <form method="get" class="mb-4 flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search user name…" class="form-input w-64" />
            <select name="status" class="form-input w-40">
                <option value="all"    @selected(($status ?? 'all') === 'all')>All</option>
                <option value="unread" @selected(($status ?? '') === 'unread')>Unread</option>
                <option value="read"   @selected(($status ?? '') === 'read')>Read</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(($search ?? '') || ($status ?? 'all') !== 'all')
                <a href="{{ route('admin.notifications.index') }}" class="text-sm text-grayTheme-medium underline">Reset</a>
            @endif
        </form>

        {{-- Clear all --}}
        <div class="mb-4 flex justify-end">
            <form method="post" action="{{ route('admin.notifications.destroy-all') }}" onsubmit="return confirm('Delete ALL notifications? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger text-xs">Clear All Notifications</button>
            </form>
        </div>

        <div class="surface overflow-x-auto rounded-xl shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="border-b border-grayTheme-border">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-grayTheme-dark">User</th>
                        <th class="px-6 py-3 text-left font-semibold text-grayTheme-dark">Type</th>
                        <th class="px-6 py-3 text-left font-semibold text-grayTheme-dark">Message</th>
                        <th class="px-6 py-3 text-left font-semibold text-grayTheme-dark">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-grayTheme-dark">Sent</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-grayTheme-border">
                    @forelse($notifications ?? [] as $notif)
                        <tr class="hover:bg-grayTheme-light transition-colors">
                            <td class="px-6 py-3">
                                @if($notif->notifiable)
                                    <a href="{{ route('admin.users.show', $notif->notifiable_id) }}" class="text-primary hover:underline">{{ $notif->notifiable->name }}</a>
                                @else
                                    <span class="text-grayTheme-medium italic">Unknown</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-grayTheme-medium text-xs">{{ class_basename($notif->type) }}</td>
                            <td class="px-6 py-3 text-grayTheme-dark max-w-xs truncate">
                                {{ $notif->data['message'] ?? ($notif->data['body'] ?? \Illuminate\Support\Str::limit(json_encode($notif->data), 60)) }}
                            </td>
                            <td class="px-6 py-3">
                                @if($notif->read_at)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-grayTheme-border text-grayTheme-medium">Read</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-primary-soft text-primary">Unread</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-grayTheme-medium whitespace-nowrap">{{ $notif->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-3">
                                <form method="post" action="{{ route('admin.notifications.destroy', $notif->id) }}" onsubmit="return confirm('Delete this notification?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-danger hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-grayTheme-medium">No notifications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @isset($notifications)
            <div class="mt-4">{{ $notifications->links() }}</div>
        @endisset
        </div>
    </div>
</x-app-layout>
