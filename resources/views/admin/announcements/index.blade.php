<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Announcements"
                subtitle="Compose and send email announcements to registered users."
                eyebrow="Administration"
            />

            {{-- Flash messages handled by toast notifications --}}

            <div class="grid gap-6 xl:grid-cols-5">

                {{-- ── Compose Panel ─────────────────────────────────── --}}
                <div class="xl:col-span-2">
                    <div class="surface overflow-hidden rounded-xl">

                        {{-- Coloured header --}}
                        <div class="flex items-center gap-3 bg-primary px-6 py-4">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/15">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 0 1-3.417.592l-2.147-6.15M18 13a3 3 0 1 0 0-6M5.436 13.683A4.001 4.001 0 0 1 7 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 0 1-1.564-.317Z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-white">Compose Announcement</h2>
                                <p class="mt-0.5 text-xs text-white/70">Recipients will receive this via email</p>
                            </div>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('admin.announcements.store') }}"
                            class="space-y-5 p-6"
                            x-data="{ submitting: false, charCount: {{ strlen(old('message', '')) }} }"
                            @submit="submitting = true"
                        >
                            @csrf

                            {{-- Title --}}
                            <div>
                                <x-input-label for="ann_title" :value="__('Subject / Title')" />
                                <x-text-input
                                    id="ann_title"
                                    name="title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('title')"
                                    maxlength="255"
                                    required
                                />
                                <x-input-error class="mt-1" :messages="$errors->get('title')" />
                            </div>

                            {{-- Message --}}
                            <div>
                                <x-input-label for="ann_message" :value="__('Message')" />
                                <textarea
                                    id="ann_message"
                                    name="message"
                                    rows="8"
                                    class="mt-1 form-input w-full"
                                    maxlength="5000"
                                    required
                                    placeholder="Write your announcement here…"
                                    x-on:input="charCount = $el.value.length"
                                >{{ old('message') }}</textarea>
                                <div class="mt-1 flex items-center justify-between">
                                    <x-input-error :messages="$errors->get('message')" />
                                    <p class="ml-auto text-xs text-grayTheme-medium" :class="charCount > 4800 ? 'text-danger' : ''">
                                        <span x-text="charCount"></span> / 5000
                                    </p>
                                </div>
                            </div>

                            {{-- Recipients info --}}
                            <div class="flex items-start gap-2.5 rounded-lg border border-primary-soft bg-primary-soft/50 px-4 py-3">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <p class="text-xs text-primary">
                                    This announcement will be sent to <strong>all registered users</strong> via email.
                                </p>
                            </div>

                            {{-- Submit --}}
                            <button
                                type="submit"
                                class="btn-primary inline-flex w-full items-center justify-center gap-2"
                                :disabled="submitting"
                            >
                                <svg x-show="!submitting" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z" />
                                </svg>
                                <svg x-show="submitting" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span x-text="submitting ? 'Sending…' : 'Send Announcement'"></span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ── History Panel ─────────────────────────────────── --}}
                <div class="xl:col-span-3">
                    <div class="surface overflow-hidden rounded-xl">

                        <div class="flex items-center justify-between border-b border-grayTheme-border px-6 py-4">
                            <div>
                                <h2 class="text-sm font-bold text-grayTheme-dark">Sent Announcements</h2>
                                <p class="mt-0.5 text-xs text-grayTheme-medium">History of all announcements dispatched from this panel</p>
                            </div>
                            @if ($announcements->total() > 0)
                                <span class="rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-semibold text-primary">
                                    {{ number_format($announcements->total()) }} total
                                </span>
                            @endif
                        </div>

                        @if ($announcements->isEmpty())
                            <div class="flex flex-col items-center gap-3 py-16 text-center">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-grayTheme-light">
                                    <svg class="h-7 w-7 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 0 1-3.417.592l-2.147-6.15M18 13a3 3 0 1 0 0-6M5.436 13.683A4.001 4.001 0 0 1 7 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 0 1-1.564-.317Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-grayTheme-dark">No announcements sent yet</p>
                                    <p class="mt-1 text-xs text-grayTheme-medium">Use the form on the left to compose and send your first announcement.</p>
                                </div>
                            </div>
                        @else
                            <div class="divide-y divide-grayTheme-border">
                                @foreach ($announcements as $announcement)
                                    <div class="px-6 py-4">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-semibold text-grayTheme-dark">
                                                    {{ $announcement->title }}
                                                </p>
                                                <p class="mt-1 line-clamp-2 text-xs text-grayTheme-medium">
                                                    {{ $announcement->message }}
                                                </p>
                                            </div>
                                            <div class="shrink-0 text-right">
                                                <p class="text-xs font-semibold text-grayTheme-dark">
                                                    {{ $announcement->created_at->format('M d, Y') }}
                                                </p>
                                                <p class="mt-0.5 text-xs text-grayTheme-medium">
                                                    {{ $announcement->created_at->format('h:i A') }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                            {{-- Recipients count badge --}}
                                            <span class="inline-flex items-center gap-1 rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-semibold text-primary">
                                                <svg class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                {{ number_format($announcement->recipients_count) }}
                                                {{ $announcement->recipients_count === 1 ? 'recipient' : 'recipients' }}
                                            </span>

                                            {{-- Audience badge --}}
                                            <span class="inline-flex items-center rounded-full bg-grayTheme-light px-2.5 py-0.5 text-xs font-medium text-grayTheme-medium">
                                                @if ($announcement->recipient_type === 'all')
                                                    All users
                                                @else
                                                    Role: {{ ucfirst($announcement->recipient_role ?? '—') }}
                                                @endif
                                            </span>

                                            {{-- Sent-by attribution --}}
                                            @if ($announcement->sentBy)
                                                <span class="text-xs text-grayTheme-medium">
                                                    by {{ $announcement->sentBy->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($announcements->hasPages())
                                <div class="border-t border-grayTheme-border px-6 py-4">
                                    {{ $announcements->links() }}
                                </div>
                            @endif
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
