<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Activity Log"
                subtitle="Audit user activity and system events."
                eyebrow="Administration"
            />

            {{-- Filters --}}
        <form method="get" class="mb-4 flex flex-wrap items-center gap-3">
            <input
                type="text"
                name="search"
                value="{{ $search ?? '' }}"
                placeholder="Search description or user…"
                class="form-input w-64"
            />
            <select name="event" class="form-input w-40">
                <option value="all"       @selected(($event ?? 'all') === 'all')>All Events</option>
                <option value="created"   @selected(($event ?? '') === 'created')>Created</option>
                <option value="updated"   @selected(($event ?? '') === 'updated')>Updated</option>
                <option value="deleted"   @selected(($event ?? '') === 'deleted')>Deleted</option>
            </select>
            <select name="subject_type" class="form-input w-44">
                <option value="all"         @selected(($subjectType ?? 'all') === 'all')>All Types</option>
                <option value="user"        @selected(($subjectType ?? '') === 'user')>User</option>
                <option value="certificate" @selected(($subjectType ?? '') === 'certificate')>Certificate</option>
                <option value="document"    @selected(($subjectType ?? '') === 'document')>Document</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(($search ?? '') || ($event ?? 'all') !== 'all' || ($subjectType ?? 'all') !== 'all')
                <a href="{{ route('admin.activity.index') }}" class="text-sm text-grayTheme-medium underline">Reset</a>
            @endif
        </form>

        <div class="surface overflow-x-auto rounded-xl shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="border-b border-grayTheme-border">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-grayTheme-dark">When</th>
                        <th class="px-6 py-3 text-left font-semibold text-grayTheme-dark">Causer</th>
                        <th class="px-6 py-3 text-left font-semibold text-grayTheme-dark">Event</th>
                        <th class="px-6 py-3 text-left font-semibold text-grayTheme-dark">Subject</th>
                        <th class="px-6 py-3 text-left font-semibold text-grayTheme-dark">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-grayTheme-border">
                    @forelse($logs ?? [] as $log)
                        <tr class="hover:bg-grayTheme-light transition-colors">
                            <td class="px-6 py-3 text-grayTheme-medium whitespace-nowrap">
                                {{ $log->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-3">
                                @if($log->causer)
                                    <a href="{{ route('admin.users.show', $log->causer_id) }}" class="text-primary hover:underline">
                                        {{ $log->causer->name ?? 'Unknown' }}
                                    </a>
                                @else
                                    <span class="text-grayTheme-medium italic">System</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                @php
                                    $tone = match($log->event) {
                                        'created' => 'bg-success-soft text-success',
                                        'updated' => 'bg-primary-soft text-primary',
                                        'deleted' => 'bg-danger-soft text-danger',
                                        default   => 'bg-grayTheme-border text-grayTheme-medium',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $tone }}">
                                    {{ ucfirst($log->event ?? 'log') }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-grayTheme-medium">
                                {{ class_basename($log->subject_type ?? '') }}
                                @if($log->subject_id)
                                    <span class="text-xs">#{{ $log->subject_id }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-grayTheme-dark max-w-xs truncate">
                                {{ $log->description }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-grayTheme-medium">
                                No activity logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @isset($logs)
            <div class="mt-4">{{ $logs->links() }}</div>
        @endisset
        </div>
    </div>
</x-app-layout>
