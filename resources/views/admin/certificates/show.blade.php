<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="{{ $certificate->certificate_name }}"
                subtitle="Certificate details, attached documents, and verification controls."
                eyebrow="Administration / Certificates"
            >
                <x-slot:actions>
                    @php
                        $backUrl = request()->query('back');
                        $backUrl = filled($backUrl) && str_starts_with($backUrl, url('/'))
                            ? $backUrl
                            : route('admin.certificates.index');
                    @endphp
                    <a class="rounded-full border border-white/30 px-3 py-1 text-sm font-semibold text-white/90 hover:text-white"
                       href="{{ $backUrl }}">
                        ← Back
                    </a>
                </x-slot:actions>
            </x-page-header>

            {{-- Flash messages handled by toast notifications --}}

            <div class="grid gap-6 md:grid-cols-3">

                {{-- Left column: details --}}
                <div class="space-y-6 md:col-span-2">

                    {{-- Certificate info --}}
                    <div class="surface p-6">
                        <h3 class="mb-4 text-base font-semibold text-grayTheme-dark">Certificate Information</h3>
                        <dl class="grid gap-4 md:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Certificate Name</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $certificate->certificate_name ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Certificate Number</dt>
                                <dd class="mt-1 font-mono text-sm text-grayTheme-dark">{{ $certificate->certificate_number ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Type</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $certificate->certificate_type_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Qualification Title</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $certificate->qualification_title ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Issued By</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $certificate->issued_by ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Issue Date</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">
                                    {{ $certificate->issue_date ? $certificate->issue_date->format('M d, Y') : '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Expiration Date</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">
                                    {{ $certificate->expiration_date ? $certificate->expiration_date->format('M d, Y') : '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Certificate Status</dt>
                                <dd class="mt-1">
                                    @php
                                        $statusTone = match ($certificate->status) {
                                            'valid'    => 'bg-success-soft text-success',
                                            'expiring' => 'bg-warning-soft text-warning',
                                            'expired'  => 'bg-danger-soft text-danger',
                                            default    => 'bg-grayTheme-light text-grayTheme-medium',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusTone }}">
                                        {{ ucfirst($certificate->status ?? 'unknown') }}
                                    </span>
                                </dd>
                            </div>
                            @if ($certificate->remarks)
                                <div class="md:col-span-2">
                                    <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Remarks</dt>
                                    <dd class="mt-1 text-sm text-grayTheme-dark">{{ $certificate->remarks }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    {{-- Documents / attachments --}}
                    <div class="surface p-6">
                        <h3 class="mb-4 text-base font-semibold text-grayTheme-dark">Attached Documents</h3>
                        @if ($certificate->documents->isEmpty())
                            <p class="text-sm text-grayTheme-medium">No documents attached to this certificate.</p>
                        @else
                            <ul class="divide-y divide-grayTheme-border">
                                @foreach ($certificate->documents as $doc)
                                    @php
                                        $isPdf   = str_contains(strtolower($doc->mime_type ?? ''), 'pdf');
                                        $isImage = str_contains(strtolower($doc->mime_type ?? ''), 'image');
                                    @endphp
                                    <li x-data="{ open: false }">
                                        {{-- Document row --}}
                                        <div class="flex items-center justify-between gap-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
                                                    @if ($isPdf)
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                        </svg>
                                                    @elseif ($isImage)
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-grayTheme-dark">
                                                        {{ $doc->document_name ?: $doc->original_name }}
                                                    </div>
                                                    <div class="text-xs text-grayTheme-medium">
                                                        {{ $doc->type ? ucfirst($doc->type) : 'Document' }}
                                                        @if ($doc->size)
                                                            · {{ number_format($doc->size / 1024, 1) }} KB
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                @if ($isPdf || $isImage)
                                                    <button type="button" @click="open = !open"
                                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition"
                                                        :class="open ? 'bg-primary text-white' : 'bg-primary-soft text-primary hover:bg-primary hover:text-white'">
                                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        <span x-text="open ? 'Hide' : 'Preview'"></span>
                                                    </button>
                                                @endif
                                                <a href="{{ route('documents.download', $doc) }}"
                                                   class="inline-flex items-center gap-1 rounded-lg bg-grayTheme-hover px-2.5 py-1.5 text-xs font-semibold text-grayTheme-dark transition hover:bg-grayTheme-border">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                    Download
                                                </a>
                                            </div>
                                        </div>

                                        {{-- Inline preview panel --}}
                                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="pb-4">
                                            <div class="overflow-hidden rounded-xl border border-grayTheme-border bg-grayTheme-light">
                                                @if ($isPdf)
                                                    <iframe src="{{ route('documents.preview', $doc) }}"
                                                        class="w-full"
                                                        style="height: 520px; border: none;"
                                                        loading="lazy">
                                                    </iframe>
                                                @elseif ($isImage)
                                                    <img src="{{ route('documents.preview', $doc) }}"
                                                        alt="{{ $doc->document_name ?: $doc->original_name }}"
                                                        class="mx-auto max-h-[520px] w-auto object-contain p-4">
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                </div>

                {{-- Right column: owner + verification --}}
                <div class="space-y-6">

                    {{-- Certificate owner --}}
                    <div class="surface p-6">
                        <h3 class="mb-4 text-base font-semibold text-grayTheme-dark">Certificate Owner</h3>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-soft text-sm font-bold text-primary">
                                {{ strtoupper(substr($certificate->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-grayTheme-dark">{{ $certificate->user->name ?? '—' }}</div>
                                <div class="text-xs text-grayTheme-medium">{{ $certificate->user->email ?? '' }}</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.users.show', $certificate->user) }}"
                               class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-primary-soft px-3 py-2 text-xs font-semibold text-primary transition hover:bg-primary hover:text-white">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                View User Profile
                            </a>
                        </div>
                    </div>

                    {{-- Verification status --}}
                    <div class="surface p-6">
                        <h3 class="mb-4 text-base font-semibold text-grayTheme-dark">Verification</h3>
                        @php
                            $vStatus = $certificate->verification_status ?? 'pending';
                            $verifyTone = match ($vStatus) {
                                'verified' => 'bg-success-soft text-success',
                                'rejected' => 'bg-danger-soft text-danger',
                                default    => 'bg-warning-soft text-warning',
                            };
                        @endphp
                        <div class="mb-4 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm font-semibold {{ $verifyTone }}">
                                @if ($vStatus === 'verified')
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @elseif ($vStatus === 'rejected')
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                @else
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                                {{ ucfirst($vStatus) }}
                            </span>
                        </div>

                        @if ($certificate->verifier)
                            <div class="mb-4 text-xs text-grayTheme-medium">
                                {{ $vStatus === 'verified' ? 'Verified' : 'Reviewed' }} by
                                <span class="font-semibold text-grayTheme-dark">{{ $certificate->verifier->name }}</span>
                                @if ($certificate->verified_at)
                                    on {{ $certificate->verified_at->format('M d, Y') }}
                                @endif
                            </div>
                        @endif

                        <div class="flex flex-col gap-2">
                            @if ($vStatus !== 'verified')
                                <form method="POST" action="{{ route('admin.certificates.verify', $certificate) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="verify">
                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-success px-3 py-2 text-sm font-semibold text-white transition hover:bg-success-hover focus:outline-none">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Mark as Verified
                                    </button>
                                </form>
                            @endif
                            @if ($vStatus !== 'rejected')
                                <form method="POST" action="{{ route('admin.certificates.verify', $certificate) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-danger px-3 py-2 text-sm font-semibold text-white transition hover:bg-danger-hover focus:outline-none">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Reject Certificate
                                    </button>
                                </form>
                            @endif
                            @if ($vStatus !== 'pending')
                                <form method="POST" action="{{ route('admin.certificates.verify', $certificate) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="action" value="reset">
                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-grayTheme-hover px-3 py-2 text-sm font-semibold text-grayTheme-dark transition hover:bg-grayTheme-border focus:outline-none">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Reset to Pending
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Print / actions --}}
                    <div class="surface p-6">
                        <h3 class="mb-4 text-base font-semibold text-grayTheme-dark">Actions</h3>
                        <div class="flex flex-col gap-2">
                            <button type="button" onclick="window.print()"
                                class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-primary-soft px-3 py-2 text-sm font-semibold text-primary transition hover:bg-primary hover:text-white">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Print Details
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Bridge session flash messages to toast notifications --}}
    @if (session('status') === 'cert-updated')
    <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Certificate Updated',message:'Verification status has been updated.'}}));</script>
    @endif
</x-app-layout>
