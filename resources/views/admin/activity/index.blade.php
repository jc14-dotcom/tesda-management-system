<x-app-layout>
    <div class="py-12" x-data="{
        modalOpen: false,
        log: {},
        openLog(data) {
            this.log = data;
            this.modalOpen = true;
        }
    }">
        <div class="page-container space-y-6">
            <x-page-header
                title="Activity Log"
                subtitle="Audit user activity and system events."
                eyebrow="Administration"
            />

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Total Activities</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($totalCount) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-soft">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Today</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($todayCount) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-soft">
                        <svg class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                    </div>
                </div>
                <div class="surface flex items-center justify-between rounded-xl p-5 shadow-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">This Week</p>
                        <p class="mt-1 text-3xl font-bold text-grayTheme-dark">{{ number_format($thisWeekCount) }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-soft">
                        <svg class="h-6 w-6 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    </div>
                </div>
            </div>

            {{-- Causer filter banner --}}
            @if($causerUser)
                <div class="flex items-center justify-between rounded-xl border border-primary/20 bg-primary-soft/40 px-4 py-3">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">
                            {{ strtoupper(substr($causerUser->name, 0, 1)) }}
                        </div>
                        <span class="text-sm font-semibold text-primary">Showing activity by: {{ $causerUser->name }}</span>
                    </div>
                    <a href="{{ route('admin.activity.index') }}" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-primary transition hover:bg-primary hover:text-white">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear filter
                    </a>
                </div>
            @endif

            {{-- Filter Card --}}
            <div class="surface rounded-xl p-6 shadow-sm">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-soft">
                        <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-grayTheme-dark">Filter Activities</h3>
                        <p class="text-xs text-grayTheme-medium">Narrow down results by type, event, date, or keyword</p>
                    </div>
                </div>
                <form method="get" x-data="liveSearch()">
                    @if($causerId > 0)
                        <input type="hidden" name="causer_id" value="{{ $causerId }}">
                    @endif
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="w-full sm:w-auto">
                            <label class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium" for="subject_type_filter">Log Type</label>
                            <select id="subject_type_filter" name="subject_type" class="mt-1 form-input">
                                <option value="all"         @selected(($subjectType ?? 'all') === 'all')>All Types</option>
                                <option value="user"        @selected(($subjectType ?? '') === 'user')>User</option>
                                <option value="certificate" @selected(($subjectType ?? '') === 'certificate')>Certificate</option>
                                <option value="document"    @selected(($subjectType ?? '') === 'document')>Document</option>
                                <option value="profile"     @selected(($subjectType ?? '') === 'profile')>Profile</option>
                            </select>
                        </div>
                        <div class="w-full sm:w-auto">
                            <label class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium" for="event_filter">Event</label>
                            <select id="event_filter" name="event" class="mt-1 form-input">
                                <option value="all"       @selected(($event ?? 'all') === 'all')>All Events</option>
                                <option value="created"   @selected(($event ?? '') === 'created')>Created</option>
                                <option value="updated"   @selected(($event ?? '') === 'updated')>Updated</option>
                                <option value="deleted"   @selected(($event ?? '') === 'deleted')>Deleted</option>
                                <option value="login"     @selected(($event ?? '') === 'login')>Login</option>
                                <option value="logout"    @selected(($event ?? '') === 'logout')>Logout</option>
                            </select>
                        </div>
                        <div class="w-full sm:w-auto">
                            <label class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium" for="from_date">From Date</label>
                            <input id="from_date" type="date" name="from_date" value="{{ $fromDate ?? '' }}" class="mt-1 form-input" />
                        </div>
                        <div class="w-full sm:w-auto">
                            <label class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium" for="to_date">To Date</label>
                            <input id="to_date" type="date" name="to_date" value="{{ $toDate ?? '' }}" class="mt-1 form-input" />
                        </div>
                        <div class="w-full sm:flex-1 sm:min-w-52">
                            <label class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium" for="search_activity">Search</label>
                            <input id="search_activity" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search descriptions or user..." class="mt-1 form-input w-full"
                                @input.debounce.400ms="search($el.closest('form'))" />
                        </div>
                    </div>
                    @php $hasActivityFilters = ($search ?? '') || ($event ?? 'all') !== 'all' || ($subjectType ?? 'all') !== 'all' || ($fromDate ?? '') || ($toDate ?? ''); @endphp
                    <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                        <a href="{{ route('admin.activity.index', $causerId > 0 ? ['causer_id' => $causerId] : []) }}" class="btn-secondary inline-flex items-center gap-1.5 {{ !$hasActivityFilters ? 'pointer-events-none opacity-40' : '' }}">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reset
                        </a>
                        <button type="submit" class="btn-primary inline-flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <div id="live-search-results">
            {{-- Table --}}
            <div class="surface overflow-hidden rounded-xl shadow-sm">
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-primary">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Date/Time</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Event</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Log Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Modified By</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-grayTheme-border">
                        @forelse($logs ?? [] as $log)
                            @php
                                $props   = $log->properties ?? collect();
                                $old     = is_array($props->get('old'))        ? $props->get('old')        : [];
                                $new     = is_array($props->get('attributes')) ? $props->get('attributes') : [];
                                $skip    = ['password', 'remember_token', 'email_verified_at', 'updated_at', 'created_at'];
                                $keys    = array_diff(array_unique(array_merge(array_keys($old), array_keys($new))), $skip);
                                $changes = [];
                                foreach ($keys as $key) {
                                    $oldVal = array_key_exists($key, $old) ? $old[$key] : null;
                                    $newVal = array_key_exists($key, $new) ? $new[$key] : null;
                                    
                                    // Format date fields (all common date fields across models)
                                    $dateFields = ['date_of_birth', 'date_hired', 'issue_date', 'expiration_date', 'issued_on', 'valid_until'];
                                    if (in_array($key, $dateFields)) {
                                        if ($oldVal) {
                                            try {
                                                $oldVal = \Carbon\Carbon::parse($oldVal)->format('M d, Y');
                                            } catch (\Exception $e) {
                                                // Keep original if parsing fails
                                            }
                                        }
                                        if ($newVal) {
                                            try {
                                                $newVal = \Carbon\Carbon::parse($newVal)->format('M d, Y');
                                            } catch (\Exception $e) {
                                                // Keep original if parsing fails
                                            }
                                        }
                                    }
                                    
                                    $changes[] = [
                                        'field' => ucwords(str_replace('_', ' ', $key)),
                                        'old'   => $oldVal !== null ? (string) $oldVal : null,
                                        'new'   => $newVal !== null ? (string) $newVal : null,
                                    ];
                                }
                                $subjectClass = class_basename($log->subject_type ?? '');
                                $subjectName  = $subjectClass;
                                if ($log->subject) {
                                    $subjectName = match(true) {
                                        $log->subject instanceof \App\Models\Certificate => ($log->subject->certificate_name ?? $subjectClass),
                                        $log->subject instanceof \App\Models\User        => ($log->subject->name ?? $subjectClass),
                                        $log->subject instanceof \App\Models\Document    => ($log->subject->document_name ?? $log->subject->original_name ?? $subjectClass),
                                        $log->subject instanceof \App\Models\Profile     => (
                                            'Profile for ' . trim(
                                                ($log->subject->first_name ?? '') . ' ' . 
                                                ($log->subject->last_name ?? '')
                                            ) ?: $subjectClass
                                        ),
                                        default => $subjectClass,
                                    };
                                }
                                
                                // Build causer full name with role
                                $causerDisplay = 'System';
                                if ($log->causer) {
                                    $causerProfile = $log->causer->profile;
                                    if ($causerProfile) {
                                        $causerFullName = trim(
                                            ($causerProfile->first_name ?? '') . ' ' . 
                                            ($causerProfile->last_name ?? '')
                                        ) ?: $log->causer->name;
                                    } else {
                                        $causerFullName = $log->causer->name;
                                    }
                                    $causerRole = $log->causer->hasRole('admin') ? 'Admin' : 'User';
                                    $causerDisplay = $causerFullName . ' (' . $causerRole . ')';
                                }
                                
                                $logData = [
                                    'event'        => $log->event ?? 'log',
                                    'logType'      => $subjectClass,
                                    'dateTime'     => $log->created_at->format('M d, Y g:i:s A'),
                                    'modifiedBy'   => $causerDisplay,
                                    'subjectLabel' => $subjectName,
                                    'description'  => $log->description ?? '',
                                    'changes'      => $changes,
                                ];
                                $eventTone = match($log->event) {
                                    'created' => 'bg-success-soft text-success',
                                    'updated' => 'bg-primary-soft text-primary',
                                    'deleted' => 'bg-danger-soft text-danger',
                                    'login'   => 'bg-accent-soft text-accent',
                                    'logout'  => 'bg-warning-soft text-warning',
                                    default   => 'bg-grayTheme-hover text-grayTheme-medium',
                                };
                            @endphp
                            <tr class="transition hover:bg-grayTheme-light/60">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-grayTheme-dark">
                                    {{ $log->created_at->format('M d, Y') }}
                                    <div class="text-xs text-grayTheme-medium">{{ $log->created_at->format('g:i A') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $eventTone }}">
                                        @if($log->event === 'created')
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        @elseif($log->event === 'updated')
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        @elseif($log->event === 'deleted')
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        @endif
                                        {{ ucfirst($log->event ?? 'log') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-grayTheme-hover px-2.5 py-0.5 text-xs font-semibold text-grayTheme-dark">{{ $subjectClass ?: 'â€”' }}</span>
                                </td>
                                <td class="px-4 py-3 max-w-xs text-grayTheme-dark">
                                    <span class="line-clamp-2 text-sm">{{ $log->description }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($log->causer)
                                        @php
                                            $causerProfile = $log->causer->profile;
                                            $causerFullName = $causerProfile ? trim(
                                                ($causerProfile->first_name ?? '') . ' ' . 
                                                ($causerProfile->last_name ?? '')
                                            ) : null;
                                            $displayName = $causerFullName ?: $log->causer->name;
                                            $roleLabel = $log->causer->hasRole('admin') ? 'Admin' : 'User';
                                            $initials = strtoupper(substr($displayName ?? '?', 0, 1));
                                        @endphp
                                        <a href="{{ route('admin.users.show', $log->causer_id) }}" class="inline-flex items-center gap-1.5 font-medium text-primary hover:underline">
                                            <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-soft text-xs font-bold text-primary">{{ $initials }}</div>
                                            <div class="flex flex-col">
                                                <span class="text-sm">{{ $displayName }}</span>
                                                <span class="text-xs text-grayTheme-medium">({{ $roleLabel }})</span>
                                            </div>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs text-grayTheme-medium">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            System
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-primary transition hover:bg-primary-soft focus:outline-none"
                                        @click='openLog(@json($logData))'
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-grayTheme-light">
                                            <svg class="h-6 w-6 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        </div>
                                        <p class="text-sm font-semibold text-grayTheme-dark">No activity logs found</p>
                                        <p class="text-xs text-grayTheme-medium">Try adjusting your search or filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>{{-- /overflow-x-auto --}}
            </div>

            @isset($logs)
                <div class="mt-4">{{ $logs->links() }}</div>
            @endisset
            </div>
        </div>

        {{-- Activity Details Modal --}}
        <div
            x-cloak
            x-show="modalOpen"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4 py-8"
            @keydown.escape.window="modalOpen = false"
            @click.self="modalOpen = false"
        >
            <div
                class="w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
            >
                {{-- Modal Header --}}
                <div class="flex items-center justify-between bg-primary px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <h2 class="text-base font-bold text-white">Activity Details</h2>
                    </div>
                    <button type="button" class="rounded-lg p-1 text-white/70 hover:bg-white/20 hover:text-white focus:outline-none" @click="modalOpen = false">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="max-h-[75vh] overflow-y-auto p-6 space-y-3">

                    {{-- Row 1: Event | Log Type --}}
                    <div class="grid md:grid-cols-2 gap-3">
                        <div class="rounded-xl bg-grayTheme-light p-4">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Event</p>
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                :class="{
                                    'bg-success-soft text-success': log.event === 'created',
                                    'bg-primary-soft text-primary': log.event === 'updated',
                                    'bg-danger-soft text-danger':   log.event === 'deleted',
                                    'bg-grayTheme-hover text-grayTheme-medium': !['created','updated','deleted'].includes(log.event)
                                }"
                                x-text="log.event ? (log.event.charAt(0).toUpperCase() + log.event.slice(1)) : '-'"
                            ></span>
                        </div>
                        <div class="rounded-xl bg-grayTheme-light p-4">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Log Type</p>
                            <p class="text-sm font-bold text-grayTheme-dark" x-text="log.logType || '-'"></p>
                        </div>
                    </div>

                    {{-- Row 2: Date/Time | Modified By --}}
                    <div class="grid md:grid-cols-2 gap-3">
                        <div class="rounded-xl bg-grayTheme-light p-4">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Date/Time</p>
                            <p class="text-sm font-bold text-grayTheme-dark" x-text="log.dateTime || '-'"></p>
                        </div>
                        <div class="rounded-xl bg-grayTheme-light p-4">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Modified By</p>
                            <p class="text-sm font-bold text-grayTheme-dark" x-text="log.modifiedBy || '-'"></p>
                        </div>
                    </div>

                    {{-- Row 3: Record Modified | Description --}}
                    <div class="grid md:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-primary/25 bg-primary-soft/60 p-4">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-primary">Record Modified</p>
                            <p class="text-sm font-bold text-primary" x-text="log.subjectLabel || '-'"></p>
                        </div>
                        <div class="rounded-xl bg-grayTheme-light p-4">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Description</p>
                            <p class="text-sm text-grayTheme-dark" x-text="log.description || '-'"></p>
                        </div>
                    </div>

                    {{-- Changes --}}
                    <div class="rounded-xl bg-grayTheme-light p-4">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Changes</p>
                        <template x-if="log.changes && log.changes.length > 0">
                            <div class="overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0">
                                {{-- Created event: only Field + Value columns --}}
                                <template x-if="log.event === 'created'">
                                    <div class="overflow-hidden rounded-xl border border-grayTheme-border min-w-[300px]">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                                <tr class="bg-primary text-left text-xs font-semibold uppercase tracking-wide text-white">
                                                    <th class="px-4 py-2.5">Field</th>
                                                    <th class="px-4 py-2.5">Value</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-grayTheme-border">
                                                <template x-for="change in log.changes" :key="change.field">
                                                    <tr class="bg-white">
                                                        <td class="px-4 py-2.5 font-semibold text-grayTheme-dark" x-text="change.field"></td>
                                                        <td class="px-4 py-2.5 font-medium text-success" x-text="change.new != null ? change.new : '-'"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                                {{-- Updated / deleted: show Old Value + New Value --}}
                                <template x-if="log.event !== 'created'">
                                    <div class="overflow-hidden rounded-xl border border-grayTheme-border min-w-[600px]">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                                <tr class="bg-primary text-left text-xs font-semibold uppercase tracking-wide text-white">
                                                    <th class="px-4 py-2.5">Field</th>
                                                    <th class="px-4 py-2.5">Old Value</th>
                                                    <th class="px-4 py-2.5">New Value</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-grayTheme-border">
                                                <template x-for="change in log.changes" :key="change.field">
                                                    <tr class="bg-white">
                                                        <td class="px-4 py-2.5 font-semibold text-grayTheme-dark" x-text="change.field"></td>
                                                        <td class="px-4 py-2.5 font-medium text-danger" x-text="change.old != null ? change.old : '-'"></td>
                                                        <td class="px-4 py-2.5 font-medium text-success" x-text="change.new != null ? change.new : '-'"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!log.changes || log.changes.length === 0">
                            <p class="rounded-lg bg-white px-4 py-3 text-sm text-grayTheme-medium">No field-level changes recorded for this event.</p>
                        </template>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end border-t border-grayTheme-border bg-grayTheme-light px-6 py-4">
                    <button type="button" class="btn-secondary" @click="modalOpen = false">Close</button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
