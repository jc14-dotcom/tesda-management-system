<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Settings"
                subtitle="System configuration and notification preferences."
                eyebrow="Administration"
            />

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 lg:items-start">

                {{-- Flash messages handled by toast notifications --}}

                <form method="post" action="{{ route('admin.settings.update') }}" class="space-y-6 self-start"
                    x-data="{
                        submitting: false,
                        dirty: false,
                        origEnabled: {{ ($notificationsEnabled ?? false) ? 'true' : 'false' }},
                        origDays: {{ json_encode($expiryNoticeDays ?? [30, 14, 7, 3, 1]) }},
                        get isDirty() {
                            const enabledEl = document.getElementById('notifications_enabled');
                            if (!enabledEl) return false;
                            const enabledNow = enabledEl.checked;
                            if (enabledNow !== this.origEnabled) return true;
                            const dayInputs = [...document.querySelectorAll('input[name=\'expiry_notice_days[]\']')];
                            const daysNow = dayInputs.map(el => Number(el.value));
                            if (daysNow.length !== this.origDays.length) return true;
                            return daysNow.some((v, i) => v !== this.origDays[i]);
                        }
                    }"
                    @submit="submitting = true"
                    @input="dirty = isDirty"
                    @change="dirty = isDirty"
                >
                    @csrf

                    {{-- Certificate Notifications Card --}}
                    <div class="surface overflow-hidden rounded-xl !mt-0">

                        {{-- Colored header --}}
                        <div class="flex items-center gap-3 bg-primary px-6 py-4">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/15">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-white">Certificate Notifications</h2>
                                <p class="mt-0.5 text-xs text-white/70">Automatic expiry reminders sent to users via email</p>
                            </div>
                        </div>

                        <div class="space-y-6 p-6">

                            {{-- Enable / Disable toggle --}}
                            <div class="flex items-center justify-between gap-4 rounded-xl border border-grayTheme-border bg-grayTheme-light/50 px-4 py-3">
                                <div>
                                    <p class="text-sm font-semibold text-grayTheme-dark">Enable expiry notifications</p>
                                    <p class="mt-0.5 text-xs text-grayTheme-medium">Users will receive email reminders before their certificates expire based on the schedule below.</p>
                                </div>
                                <label class="inline-flex cursor-pointer items-center" for="notifications_enabled"
                                       x-data="{ on: {{ ($notificationsEnabled ?? false) ? 'true' : 'false' }} }">
                                    <input
                                        type="checkbox"
                                        id="notifications_enabled"
                                        name="notifications_enabled"
                                        value="1"
                                        class="sr-only"
                                        x-model="on"
                                    />
                                    <div class="relative h-6 w-11 shrink-0 rounded-full transition-colors duration-200"
                                         :class="on ? 'bg-primary' : 'bg-grayTheme-border'">
                                        <div class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition-transform duration-200"
                                             :class="on ? 'translate-x-5' : 'translate-x-0'"></div>
                                    </div>
                                </label>
                            </div>

                            {{-- Reminder schedule --}}
                            <div x-data="{ days: {{ json_encode($expiryNoticeDays ?? [30, 14, 7, 3, 1]) }} }">

                                <div class="mb-3 flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-grayTheme-dark">Reminder schedule</p>
                                        <p class="mt-0.5 text-xs text-grayTheme-medium">Send a reminder on each of these days before a certificate's expiry date. Edit the number or remove entries you don't need.</p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="days.push(7); $nextTick(() => { dirty = isDirty })"
                                        class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-accent px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-accent/90 focus:outline-none focus:ring-2 focus:ring-accent/40"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        Add reminder
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                    <template x-for="(day, i) in days" :key="i">
                                        <div class="flex items-center gap-2 rounded-lg border border-grayTheme-border bg-white px-3 py-2.5 shadow-sm">
                                            <input
                                                type="number"
                                                name="expiry_notice_days[]"
                                                x-model.number="days[i]"
                                                class="w-full border-0 bg-transparent p-0 text-sm font-bold text-grayTheme-dark focus:ring-0"
                                                min="1"
                                                max="365"
                                            />
                                            <span class="shrink-0 text-xs text-grayTheme-medium">days before</span>
                                            <button
                                                type="button"
                                                @click="days.splice(i, 1); $nextTick(() => { dirty = isDirty })"
                                                class="shrink-0 rounded p-0.5 text-grayTheme-medium transition hover:bg-danger-soft hover:text-danger"
                                                :aria-label="'Remove ' + day + '-day reminder'"
                                            >
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>

                                    <template x-if="days.length === 0">
                                        <div class="col-span-full rounded-lg border border-dashed border-grayTheme-border py-6 text-center">
                                            <p class="text-xs font-semibold text-grayTheme-medium">No reminders configured.</p>
                                            <p class="mt-0.5 text-xs text-grayTheme-medium">Click "Add reminder" to set one up.</p>
                                        </div>
                                    </template>
                                </div>

                                @error('expiry_notice_days')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="btn-primary inline-flex items-center gap-2" x-bind:disabled="submitting || !dirty">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            Save Settings
                        </button>
                    </div>
                </form>

                {{-- ── Qualification Titles ──────────────────────────────────────── --}}
                <div class="surface overflow-hidden rounded-xl self-start">

                    {{-- Colored header --}}
                    <div class="flex items-center gap-3 bg-accent px-6 py-4">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/15">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-white">Qualification Titles</h2>
                            <p class="mt-0.5 text-xs text-white/70">Define the qualification titles users can select when completing their profiles.</p>
                        </div>
                    </div>

                    <div class="divide-y divide-grayTheme-border">

                        {{-- ── Trainer Qualifications ────────────────────── --}}
                        <div class="space-y-4 p-6">
                            <div class="flex items-center gap-2">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-primary-soft">
                                    <svg class="h-3.5 w-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-semibold text-grayTheme-dark">Trainer Qualifications</h3>
                                <span class="ml-auto rounded-full bg-primary-soft px-2 py-0.5 text-xs font-bold text-primary">{{ $trainerQualifications->count() }}</span>
                            </div>

                            {{-- List --}}
                            @if($trainerQualifications->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($trainerQualifications as $q)
                                <div class="flex items-center justify-between gap-2 rounded-lg border border-grayTheme-border bg-white px-3 py-2 shadow-sm">
                                    <span class="truncate text-sm text-grayTheme-dark">{{ $q->title }}</span>
                                    <form method="POST" action="{{ route('admin.qualifications.destroy', $q) }}" class="shrink-0" id="trainer-qual-{{ $q->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="flex h-6 w-6 items-center justify-center rounded p-0.5 text-grayTheme-medium transition hover:bg-danger-soft hover:text-danger"
                                            title="Remove qualification"
                                            onclick="window.showConfirm({ title: 'Remove Qualification?', message: 'Remove &quot;{{ addslashes($q->title) }}&quot; from trainer qualifications?', confirmText: 'Remove', onConfirm: () => document.getElementById('trainer-qual-{{ $q->id }}').submit() })"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="rounded-lg border border-dashed border-grayTheme-border py-5 text-center">
                                <p class="text-xs font-semibold text-grayTheme-medium">No trainer qualifications configured yet.</p>
                                <p class="mt-0.5 text-xs text-grayTheme-medium">Add one below to get started.</p>
                            </div>
                            @endif

                            {{-- Add form --}}
                            <form method="POST" action="{{ route('admin.qualifications.store') }}" class="flex gap-2">
                                @csrf
                                <input type="hidden" name="type" value="trainer">
                                <div class="flex-1">
                                    <input
                                        type="text"
                                        name="title"
                                        class="form-input w-full text-sm"
                                        placeholder="e.g. Automotive Servicing NC II"
                                        maxlength="255"
                                        value="{{ old('type') === 'trainer' ? old('title', '') : '' }}"
                                        required
                                    >
                                    @error('title_trainer')
                                        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/40">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Add
                                </button>
                            </form>
                        </div>

                        {{-- ── Assessor Qualifications ───────────────────── --}}
                        <div class="space-y-4 p-6">
                            <div class="flex items-center gap-2">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-accent-soft">
                                    <svg class="h-3.5 w-3.5 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-semibold text-grayTheme-dark">Assessor Qualifications</h3>
                                <span class="ml-auto rounded-full bg-accent-soft px-2 py-0.5 text-xs font-bold text-accent-active">{{ $assessorQualifications->count() }}</span>
                            </div>

                            {{-- List --}}
                            @if($assessorQualifications->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($assessorQualifications as $q)
                                <div class="flex items-center justify-between gap-2 rounded-lg border border-grayTheme-border bg-white px-3 py-2 shadow-sm">
                                    <span class="truncate text-sm text-grayTheme-dark">{{ $q->title }}</span>
                                    <form method="POST" action="{{ route('admin.qualifications.destroy', $q) }}" class="shrink-0" id="assessor-qual-{{ $q->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="flex h-6 w-6 items-center justify-center rounded p-0.5 text-grayTheme-medium transition hover:bg-danger-soft hover:text-danger"
                                            title="Remove qualification"
                                            onclick="window.showConfirm({ title: 'Remove Qualification?', message: 'Remove &quot;{{ addslashes($q->title) }}&quot; from assessor qualifications?', confirmText: 'Remove', onConfirm: () => document.getElementById('assessor-qual-{{ $q->id }}').submit() })"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="rounded-lg border border-dashed border-grayTheme-border py-5 text-center">
                                <p class="text-xs font-semibold text-grayTheme-medium">No assessor qualifications configured yet.</p>
                                <p class="mt-0.5 text-xs text-grayTheme-medium">Add one below to get started.</p>
                            </div>
                            @endif

                            {{-- Add form --}}
                            <form method="POST" action="{{ route('admin.qualifications.store') }}" class="flex gap-2">
                                @csrf
                                <input type="hidden" name="type" value="assessor">
                                <div class="flex-1">
                                    <input
                                        type="text"
                                        name="title"
                                        class="form-input w-full text-sm"
                                        placeholder="e.g. Automotive Servicing NC II"
                                        maxlength="255"
                                        value="{{ old('type') === 'assessor' ? old('title', '') : '' }}"
                                        required
                                    >
                                    @error('title_assessor')
                                        <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-accent px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-accent/90 focus:outline-none focus:ring-2 focus:ring-accent/40">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Add
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bridge session flash messages to toast notifications --}}
    @if(session('status') === 'settings-saved')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Settings Saved',message:'System settings have been updated successfully.'}}));</script>
    @endif
    @if(session('status') === 'qualification-added')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Qualification Added',message:'The qualification title has been added successfully.'}}));</script>
    @endif
    @if(session('status') === 'qualification-deleted')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Qualification Removed',message:'The qualification title has been removed.'}}));</script>
    @endif
</x-app-layout>
