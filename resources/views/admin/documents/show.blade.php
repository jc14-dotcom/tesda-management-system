<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="{{ $document->document_name ?: $document->original_name }}"
                subtitle="Document details, preview, and file actions."
                eyebrow="Administration / Documents"
            >
                <x-slot:actions>
                    @php
                        $backUrl = request()->query('back');
                        $backUrl = filled($backUrl) && str_starts_with($backUrl, url('/'))
                            ? $backUrl
                            : route('admin.documents.index');
                    @endphp
                    <a class="rounded-full border border-white/30 px-3 py-1 text-sm font-semibold text-white/90 hover:text-white"
                       href="{{ $backUrl }}">
                        ← Back
                    </a>
                </x-slot:actions>
            </x-page-header>

            @php
                $mime = strtolower($document->mime_type ?? '');
                $isPdf = str_contains($mime, 'pdf');
                $isImage = str_contains($mime, 'image');
                $hasPreview = ! empty($document->path) && ($isPdf || $isImage);
                $sizeLabel = '-';

                if (! empty($document->size)) {
                    $kb = $document->size / 1024;
                    $sizeLabel = $kb >= 1024 ? number_format($kb / 1024, 2) . ' MB' : number_format($kb, 1) . ' KB';
                }
            @endphp

            <div class="grid gap-6 md:grid-cols-3">

                <div class="space-y-6 md:col-span-2">

                    <div class="surface p-6">
                        <h3 class="mb-4 text-base font-semibold text-grayTheme-dark">Document Information</h3>
                        <dl class="grid gap-4 md:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Document Name</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $document->document_name ?: $document->original_name ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Original Name</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $document->original_name ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Type</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $document->type ? strtoupper($document->type) : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Uploaded</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $document->created_at ? $document->created_at->format('M d, Y') : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">File Size</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $sizeLabel }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Mime Type</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $document->mime_type ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Certificate No.</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $document->certificate_no ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Primary Document</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $document->is_primary ? 'Yes' : 'No' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Issued On</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $document->issued_on ? $document->issued_on->format('M d, Y') : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Valid Until</dt>
                                <dd class="mt-1 text-sm text-grayTheme-dark">{{ $document->valid_until ? $document->valid_until->format('M d, Y') : '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="surface p-6">
                        <h3 class="mb-4 text-base font-semibold text-grayTheme-dark">Document Preview</h3>
                        @if (! $document->path)
                            <p class="text-sm text-grayTheme-medium">No file attached to this document.</p>
                        @elseif (! $hasPreview)
                            <p class="text-sm text-grayTheme-medium">No preview available for this file type.</p>
                        @elseif ($isImage)
                            <div class="overflow-hidden rounded-2xl border border-grayTheme-border bg-grayTheme-light">
                                <img src="{{ route('documents.preview', $document) }}"
                                     alt="{{ $document->document_name ?: $document->original_name }}"
                                     class="h-auto w-full" />
                            </div>
                        @else
                            <div class="overflow-hidden rounded-2xl border border-grayTheme-border bg-grayTheme-light">
                                <iframe
                                    src="{{ route('documents.preview', $document) }}"
                                    title="{{ $document->document_name ?: $document->original_name }}"
                                    class="h-[65vh] w-full"
                                ></iframe>
                            </div>
                        @endif
                    </div>

                    <div class="surface p-6">
                        <h3 class="mb-4 text-base font-semibold text-grayTheme-dark">Linked Certificate</h3>
                        @if ($document->certificate)
                            @php
                                $statusTone = match ($document->certificate->status) {
                                    'valid'    => 'bg-success-soft text-success',
                                    'expiring' => 'bg-warning-soft text-warning',
                                    'expired'  => 'bg-danger-soft text-danger',
                                    default    => 'bg-grayTheme-light text-grayTheme-medium',
                                };
                            @endphp
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-grayTheme-dark">{{ $document->certificate->certificate_name }}</div>
                                    <div class="text-xs text-grayTheme-medium">
                                        {{ $document->certificate->certificate_type_label }}
                                        @if ($document->certificate->certificate_number)
                                            - No. {{ $document->certificate->certificate_number }}
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('admin.certificates.show', $document->certificate) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-primary-soft px-3 py-2 text-xs font-semibold text-primary transition hover:bg-primary hover:text-white">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    View Certificate
                                </a>
                            </div>
                            <div class="mt-4 grid gap-3 text-xs text-grayTheme-medium md:grid-cols-2">
                                <div>Issued: {{ $document->certificate->issue_date ? $document->certificate->issue_date->format('M d, Y') : '-' }}</div>
                                <div>Expires: {{ $document->certificate->expiration_date ? $document->certificate->expiration_date->format('M d, Y') : '-' }}</div>
                            </div>
                            <div class="mt-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusTone }}">
                                    {{ ucfirst($document->certificate->status ?? 'unknown') }}
                                </span>
                            </div>
                        @else
                            <p class="text-sm text-grayTheme-medium">No certificate linked to this document.</p>
                        @endif
                    </div>

                </div>

                <div class="space-y-6">

                    <div class="surface p-6">
                        <h3 class="mb-4 text-base font-semibold text-grayTheme-dark">Document Owner</h3>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-soft text-sm font-bold text-primary">
                                {{ strtoupper(substr($document->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-grayTheme-dark">{{ $document->user->name ?? '-' }}</div>
                                <div class="text-xs text-grayTheme-medium">{{ $document->user->email ?? '' }}</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.users.show', $document->user) }}"
                               class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-primary-soft px-3 py-2 text-xs font-semibold text-primary transition hover:bg-primary hover:text-white">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                View User Profile
                            </a>
                        </div>
                    </div>

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
                            @if ($document->path)
                                @if ($hasPreview)
                                    <a href="{{ route('documents.preview', $document) }}" target="_blank"
                                       class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white transition hover:bg-primary-hover">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Open Preview
                                    </a>
                                @endif
                                <a href="{{ route('documents.download', $document) }}"
                                   class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-grayTheme-hover px-3 py-2 text-sm font-semibold text-grayTheme-dark transition hover:bg-grayTheme-border">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download File
                                </a>
                            @else
                                <p class="text-sm text-grayTheme-medium">No file available for download.</p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
