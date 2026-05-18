<x-app-layout>
    <div class="py-12" data-backup-page x-data="{
        confirmOpen: false,
        confirmTitle: '',
        confirmMessage: '',
        pendingDeleteUrl: '',
        pendingFormRef: 'deleteForm',
        askConfirm(title, message, url, formRef) {
            this.confirmTitle    = title;
            this.confirmMessage  = message;
            this.pendingDeleteUrl = url;
            this.pendingFormRef  = formRef;
            this.confirmOpen     = true;
        },
        saveScroll() {
            try { sessionStorage.setItem('backups:scrollY', String(window.scrollY)); } catch (e) {}
        },
        submitForm(ref) {
            this.saveScroll();
            const form = this.$refs[ref];
            if (!form) return;
            if (form.requestSubmit) form.requestSubmit();
            else form.submit();
        },
        runConfirm() {
            this.submitForm(this.pendingFormRef);
            this.confirmOpen = false;
        },
        restoreOpen: false,
        restoreFileName: '',
        restoreDisk: '',
        restorePath: '',
        openRestore(name, disk, path) {
            this.restoreFileName = name;
            this.restoreDisk     = disk;
            this.restorePath     = path;
            this.restoreOpen     = true;
        },
        submitRestore() {
            this.submitForm('restoreForm');
            this.restoreOpen = false;
        },
        schedFreq:     @js($schedule['frequency']),
        schedTime:     @js($schedule['time']),
        schedWeekday:  @js($schedule['weekday']),
        schedMonthday: @js($schedule['monthday']),
        origFreq:      @js($schedule['frequency']),
        origTime:      @js($schedule['time']),
        origWeekday:   @js($schedule['weekday']),
        origMonthday:  @js($schedule['monthday']),
        get hasSchedChanges() {
            return this.schedFreq !== this.origFreq
                || this.schedTime !== this.origTime
                || this.schedWeekday !== this.origWeekday
                || this.schedMonthday !== this.origMonthday;
        },
    }">
        <div class="page-container space-y-6">

            <x-page-header
                title="Backup & Restore"
                subtitle="Protect your data with automated backups and easy restoration."
                eyebrow="Administration"
            />

            {{-- Flash messages handled by toast notifications --}}
            @if (session('status') === 'backup-success')
            @elseif (session('status') === 'backup-failed')
            @elseif (session('status') === 'backup-deleted')
            @elseif (session('status') === 'backup-restore-failed')
            @elseif (session('status') === 'schedule-saved')
            @endif

            {{-- â”€â”€ Section 1: Database Information â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
            <div class="surface overflow-hidden rounded-xl shadow-sm">
                <div class="flex items-center gap-3 border-b border-grayTheme-border bg-grayTheme-light px-5 py-4">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-soft">
                        <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-grayTheme-dark">Database Information</h2>
                        <p class="mt-0.5 text-xs text-grayTheme-medium">Current database status and backup controls</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-5">
                    <div>
                        <p class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-grayTheme-medium">
                            <span>
                                Database:
                                <strong class="text-grayTheme-dark">{{ $dbName }}</strong>
                            </span>
                            @if ($dbSizeMb !== null)
                                <span>
                                    Size:
                                    <strong class="text-grayTheme-dark">
                                        {{ $dbSizeMb >= 1 ? number_format($dbSizeMb, 2).' MB' : number_format($dbSizeMb * 1024, 2).' KB' }}
                                    </strong>
                                </span>
                            @endif
                            @if ($stats['count'] > 0)
                                <span>
                                    Backups:
                                    <strong class="text-grayTheme-dark">{{ $stats['count'] }}</strong>
                                </span>
                            @endif
                        </p>
                    </div>
                    <form method="post" action="{{ route('admin.backups.run') }}" x-ref="runBackupForm" class="hidden">@csrf</form>
                    <button type="button" class="btn-primary inline-flex items-center gap-2"
                        @click="askConfirm('Create Backup Now', 'Start a new database backup? This usually takes a few seconds.', '{{ route('admin.backups.run') }}', 'runBackupForm')"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Backup Now
                    </button>
                </div>
            </div>

            {{-- â”€â”€ Section 2: Automatic Backup Settings â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
            <div class="surface overflow-hidden rounded-xl shadow-sm">
                <div class="flex items-center gap-3 border-b border-grayTheme-border bg-grayTheme-light px-5 py-4">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-soft">
                        <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-grayTheme-dark">Automatic Backup Settings</h2>
                        <p class="mt-0.5 text-xs text-grayTheme-medium">Configure how often backups are created automatically</p>
                    </div>
                </div>
                <form method="post" action="{{ route('admin.backups.schedule') }}" class="p-5" @submit="saveScroll()">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="sched_frequency" class="mb-1 block text-xs font-semibold text-grayTheme-dark">
                                Backup Schedule
                            </label>
                            <p class="mb-2 text-xs text-grayTheme-medium">Select how often automatic backups should be created</p>
                            <select id="sched_frequency" name="frequency" x-model="schedFreq"
                                class="w-full max-w-sm rounded-lg border border-grayTheme-border bg-white px-3 py-2 text-sm text-grayTheme-dark shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                                <option value="disabled">Disabled (off)</option>
                                <option value="daily">Daily (every day at midnight)</option>
                                <option value="weekly">Weekly (every Monday at midnight)</option>
                                <option value="monthly">Monthly (1st day of month)</option>
                                <option value="quarterly">Quarterly (every 3 months)</option>
                                <option value="yearly">Yearly (January 1st)</option>
                            </select>
                        </div>

                        {{-- Advanced time / day pickers --}}
                        <div x-show="schedFreq !== 'disabled'" x-cloak class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <label for="sched_time" class="mb-1.5 block text-xs font-semibold text-grayTheme-dark">Time of Day</label>
                                <input id="sched_time" type="time" name="time" x-model="schedTime"
                                    class="w-full rounded-lg border border-grayTheme-border bg-white px-3 py-2 text-sm text-grayTheme-dark shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                            </div>
                            <div x-show="schedFreq === 'weekly'" x-cloak>
                                <label for="sched_weekday" class="mb-1.5 block text-xs font-semibold text-grayTheme-dark">Day of Week</label>
                                <select id="sched_weekday" name="weekday" x-model="schedWeekday"
                                    class="w-full rounded-lg border border-grayTheme-border bg-white px-3 py-2 text-sm text-grayTheme-dark shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                                    <option value="1">Monday</option>
                                    <option value="2">Tuesday</option>
                                    <option value="3">Wednesday</option>
                                    <option value="4">Thursday</option>
                                    <option value="5">Friday</option>
                                    <option value="6">Saturday</option>
                                    <option value="7">Sunday</option>
                                </select>
                            </div>
                            <div x-show="schedFreq === 'monthly'" x-cloak>
                                <label for="sched_monthday" class="mb-1.5 block text-xs font-semibold text-grayTheme-dark">Day of Month</label>
                                <select id="sched_monthday" name="monthday" x-model="schedMonthday"
                                    class="w-full rounded-lg border border-grayTheme-border bg-white px-3 py-2 text-sm text-grayTheme-dark shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                                    @for ($d = 1; $d <= 28; $d++)
                                        <option value="{{ $d }}">{{ $d }}{{ match($d) { 1 => 'st', 2 => 'nd', 3 => 'rd', default => 'th' } }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Schedule summary + save --}}
                    <div class="mt-6 flex flex-wrap items-center justify-between gap-4 border-t border-grayTheme-border pt-5">
                        <p class="flex items-center gap-1.5 text-xs text-grayTheme-medium">
                            <svg class="h-4 w-4 shrink-0 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-show="schedFreq === 'disabled'">Automatic backups are <strong>disabled</strong>.</span>
                            <span x-show="schedFreq === 'daily'" x-cloak>Backup runs every day at <strong x-text="schedTime"></strong>.</span>
                            <span x-show="schedFreq === 'weekly'" x-cloak>
                                Backup runs every
                                <strong x-text="['','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'][parseInt(schedWeekday)]"></strong>
                                at <strong x-text="schedTime"></strong>.
                            </span>
                            <span x-show="schedFreq === 'monthly'" x-cloak>
                                Backup runs on the <strong x-text="schedMonthday"></strong>th of each month at <strong x-text="schedTime"></strong>.
                            </span>
                            <span x-show="schedFreq === 'quarterly'" x-cloak>
                                Backup runs on the 1st of January, April, July &amp; October at <strong x-text="schedTime"></strong>.
                            </span>
                            <span x-show="schedFreq === 'yearly'" x-cloak>Backup runs on <strong>January 1st</strong> at <strong x-text="schedTime"></strong>.</span>
                        </p>
                        <button type="submit" class="btn-primary inline-flex items-center gap-2"
                            :disabled="!hasSchedChanges"
                            :class="{'opacity-40 cursor-not-allowed': !hasSchedChanges}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>

            {{-- â”€â”€ Section 3: Restore from Backup â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
            <div class="surface overflow-hidden rounded-xl shadow-sm">
                <div class="flex items-center gap-3 border-b border-grayTheme-border bg-warning-soft px-5 py-4">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-warning/20">
                        <svg class="h-4 w-4 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-grayTheme-dark">Restore from Backup</h2>
                        <p class="mt-0.5 text-xs text-warning/80">Upload a backup file to restore the database</p>
                    </div>
                </div>
                <div class="space-y-5 p-5">
                    <div class="flex items-start gap-3 rounded-xl border border-warning/40 bg-warning-soft px-4 py-3.5">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        <p class="text-xs leading-5 text-grayTheme-dark">
                            <strong class="text-warning">Warning:</strong> Restoring will permanently replace all current data.
                            Make sure you have a recent backup before proceeding.
                            You will need to log in again after restore completes.
                        </p>
                    </div>
                    <form method="post" action="{{ route('admin.backups.restore-upload') }}"
                          enctype="multipart/form-data"
                          data-turbo="false"
                          class="space-y-4" x-data="{ fileName: null }" @submit="saveScroll()">
                        @csrf
                        <div>
                            <label class="mb-2 block text-xs font-semibold text-grayTheme-dark">
                                Upload Backup File <span class="font-normal text-grayTheme-medium">(.zip)</span>
                            </label>
                            <label for="backup_file"
                                class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-grayTheme-border bg-grayTheme-light/50 px-6 py-8 text-center transition hover:border-primary/40 hover:bg-primary-soft/30">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm">
                                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                </div>
                                <span class="text-sm font-semibold text-grayTheme-dark" x-text="fileName ?? 'Click to choose a backup file'"></span>
                                <span class="text-xs text-grayTheme-medium">Accepts .zip files only</span>
                                <input id="backup_file" type="file" name="backup_file" accept=".zip" class="sr-only"
                                    @change="fileName = $event.target.files[0]?.name ?? null">
                            </label>
                            @error('backup_file')
                                <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                :disabled="!fileName"
                                :class="{'opacity-40 cursor-not-allowed': !fileName}"
                                class="inline-flex items-center gap-2 rounded-xl bg-warning px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-warning/40">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Restore from File
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- â”€â”€ Section 4: Existing Backups â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
            <div class="surface overflow-hidden rounded-xl shadow-sm">
                <div class="flex items-center gap-3 border-b border-grayTheme-border bg-grayTheme-light px-5 py-4">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-soft">
                        <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-semibold text-grayTheme-dark">Existing Backups</h2>
                            @if ($stats['count'] > 0)
                                <span class="rounded-full bg-primary-soft px-2 py-0.5 text-xs font-semibold text-primary">{{ $stats['count'] }}</span>
                            @endif
                        </div>
                        <p class="mt-0.5 text-xs text-grayTheme-medium">
                            Stored on disk <strong class="text-grayTheme-dark">{{ $disk }}</strong>
                            @if ($stats['totalSize'] > 0)
                                &middot;
                                @php $totalMb = $stats['totalSize'] / 1048576; @endphp
                                {{ $totalMb >= 1 ? number_format($totalMb, 1).' MB' : number_format($stats['totalSize']/1024, 1).' KB' }} total
                            @endif
                        </p>
                    </div>
                </div>

                @forelse ($backups as $backup)
                    <div class="flex flex-wrap items-center gap-3 border-b border-grayTheme-border px-5 py-3.5 last:border-0 hover:bg-grayTheme-light/50 transition">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-soft">
                            <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-mono text-xs font-semibold text-grayTheme-dark">{{ $backup['name'] }}</p>
                            <p class="mt-0.5 text-xs text-grayTheme-medium">
                                {{ date('M d, Y g:i A', $backup['modified']) }}
                                &nbsp;&bull;&nbsp;
                                @php $kb = round($backup['size']/1024,1); $mb = round($backup['size']/1048576,2); echo $backup['size'] >= 1048576 ? "{$mb} MB" : "{$kb} KB"; @endphp
                            </p>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            <button type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-warning px-3 py-1.5 text-xs font-semibold text-white transition hover:opacity-90 focus:outline-none"
                                @click="openRestore('{{ $backup['name'] }}', '{{ $backup['disk'] }}', '{{ addslashes($backup['path']) }}')"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Restore
                            </button>
                            <a href="{{ route('admin.backups.download', ['disk' => $backup['disk'], 'path' => $backup['path']]) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white transition hover:opacity-90 focus:outline-none">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download
                            </a>
                            <button type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-danger px-3 py-1.5 text-xs font-semibold text-white transition hover:opacity-90 focus:outline-none"
                                @click="askConfirm('Delete Backup', 'Permanently delete {{ $backup['name'] }}? This cannot be undone.', '{{ route('admin.backups.destroy', ['disk' => $backup['disk'], 'path' => $backup['path']]) }}', 'deleteForm')"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center gap-4 px-4 py-20 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-grayTheme-light">
                            <svg class="h-8 w-8 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <div class="space-y-1.5">
                            <p class="text-sm font-semibold text-grayTheme-dark">No backups yet</p>
                            <p class="text-xs text-grayTheme-medium">Click &ldquo;Create Backup Now&rdquo; above to create the first backup.</p>
                        </div>
                    </div>
                @endforelse
            </div>

        </div>{{-- end page-container --}}

        {{-- Shared delete form --}}
        <form method="post" :action="pendingDeleteUrl" x-ref="deleteForm" class="hidden">
            @csrf
            <input type="hidden" name="_method" value="DELETE">
        </form>

        {{-- Restore (from existing list) form --}}
        <form method="post" action="{{ route('admin.backups.restore') }}" x-ref="restoreForm" data-turbo="false" class="hidden">
            @csrf
            <input type="hidden" name="disk" :value="restoreDisk">
            <input type="hidden" name="path" :value="restorePath">
        </form>

        {{-- Confirm modal --}}
        <div x-cloak x-show="confirmOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4"
            @keydown.escape.window="confirmOpen = false" @click.self="confirmOpen = false">
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-danger-soft">
                        <svg class="h-5 w-5 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-grayTheme-dark" x-text="confirmTitle"></h3>
                        <p class="mt-1 text-sm text-grayTheme-medium" x-text="confirmMessage"></p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn-secondary" @click="confirmOpen = false">Cancel</button>
                    <button type="button" class="btn-danger gap-2" @click="runConfirm()">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Confirm
                    </button>
                </div>
            </div>
        </div>

        {{-- Restore warning modal (restore from existing list) --}}
        <div x-cloak x-show="restoreOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4"
            @keydown.escape.window="restoreOpen = false" @click.self="restoreOpen = false">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-warning-soft">
                        <svg class="h-5 w-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-grayTheme-dark">Restore Database</h3>
                        <p class="mt-0.5 font-mono text-xs text-grayTheme-medium" x-text="restoreFileName"></p>
                    </div>
                </div>
                <div class="mt-4 rounded-lg border border-danger/20 bg-danger-soft px-4 py-3">
                    <p class="text-sm font-bold text-danger">âš  Destructive â€” cannot be undone</p>
                    <p class="mt-1 text-xs leading-5 text-danger/80">
                        This will permanently replace your entire database with data from this backup.
                        All changes made after this backup was created will be lost.
                        You will be signed out automatically after restore completes.
                    </p>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn-secondary" @click="restoreOpen = false">Cancel</button>
                    <button type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-warning px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-warning/40"
                        @click="submitRestore()">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Restore Database
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Bridge session flash messages to toast notifications --}}
    @if (session('status') === 'backup-success')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Backup Created',message:'Database backup completed successfully.'}}));</script>
    @elseif (session('status') === 'backup-failed')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'error',title:'Backup Failed',message:{{ Js::from(session('backup_error') ?? 'An error occurred while creating the backup.') }}}}));</script>
    @elseif (session('status') === 'backup-deleted')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Backup Deleted',message:'The backup file has been permanently removed.'}}));</script>
    @elseif (session('status') === 'backup-restore-failed')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'error',title:'Restore Failed',message:{{ Js::from(session('backup_error') ?? 'An error occurred during the restore process.') }}}}));</script>
    @elseif (session('status') === 'schedule-saved')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Schedule Updated',message:'Backup schedule settings have been saved.'}}));</script>
    @endif

    {{-- Preserve scroll position across backup form submissions --}}
    <script data-turbo-eval="true">
        (function () {
            if (window.__backupScrollBridgeInstalled) return;
            window.__backupScrollBridgeInstalled = true;
            const KEY = 'backups:scrollY';
            document.addEventListener('submit', function (event) {
                if (!event.target.closest('[data-backup-page]')) return;
                try { sessionStorage.setItem(KEY, String(window.scrollY)); } catch (e) {}
            }, true);
            const restore = function () {
                try {
                    const y = sessionStorage.getItem(KEY);
                    if (y !== null) {
                        sessionStorage.removeItem(KEY);
                        requestAnimationFrame(function () {
                            window.scrollTo(0, parseInt(y, 10) || 0);
                        });
                    }
                } catch (e) {}
            };
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', restore);
            } else {
                restore();
            }
            document.addEventListener('turbo:load', restore);
            document.addEventListener('turbo:render', restore);
        })();
    </script>
</x-app-layout>
