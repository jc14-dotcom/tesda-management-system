@foreach ($documents as $document)
    @php
        $typeConfig = match($document->type) {
            'cv'       => ['bg' => 'bg-primary-soft', 'text' => 'text-primary', 'iconPath' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            'training' => ['bg' => 'bg-accent-soft',  'text' => 'text-accent-active', 'iconPath' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
            default    => ['bg' => 'bg-grayTheme-light', 'text' => 'text-grayTheme-medium', 'iconPath' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        };
        $docName = $document->document_name ?? $document->original_name;
    @endphp
    <div class="group flex flex-col rounded-2xl border border-grayTheme-border bg-white shadow-sm transition hover:shadow-md">
        {{-- Card top: click to open preview --}}
        <button
            type="button"
            class="flex flex-1 items-start gap-3 p-4 text-left"
            @click="openDocument({
                title: @js($docName),
                type: @js(strtoupper($document->type)),
                previewUrl: @js(route('documents.preview', $document)),
                downloadUrl: @js(route('documents.download', $document)),
                originalName: @js($document->original_name),
                viewUrl: @js(route('documents.view', $document)),
            })"
        >
            {{-- Type icon --}}
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $typeConfig['bg'] }}">
                <svg class="h-5 w-5 {{ $typeConfig['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeConfig['iconPath'] }}" />
                </svg>
            </div>

            {{-- Name & type label --}}
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-semibold uppercase tracking-wide {{ $typeConfig['text'] }}">{{ strtoupper($document->type) }}</p>
                <p class="mt-0.5 truncate text-sm font-semibold text-grayTheme-dark">{{ $docName }}</p>
                <p class="mt-0.5 text-xs text-grayTheme-medium">Click to view or print</p>
            </div>

            {{-- Chevron --}}
            <svg class="mt-1 h-4 w-4 shrink-0 text-grayTheme-medium transition group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>

        {{-- Card actions --}}
        <div class="flex items-center gap-1 border-t border-grayTheme-border px-3 py-2">
            <button
                type="button"
                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-primary transition hover:bg-primary-soft focus:outline-none"
                @click="openDocument({
                    title: @js($docName),
                    type: @js(strtoupper($document->type)),
                    previewUrl: @js(route('documents.preview', $document)),
                    downloadUrl: @js(route('documents.download', $document)),
                    originalName: @js($document->original_name),
                    viewUrl: @js(route('documents.view', $document)),
                })"
            >
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                View
            </button>
            <a
                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-grayTheme-medium transition hover:bg-grayTheme-light hover:text-grayTheme-dark focus:outline-none"
                href="{{ route('documents.download', $document) }}"
            >
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download
            </a>
            <div class="ml-auto">
                <button
                    type="button"
                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-danger transition hover:bg-danger-soft focus:outline-none"
                    @click="$dispatch('doc-confirm-delete', { url: '{{ route('documents.destroy', $document) }}', name: '{{ addslashes($docName) }}' })"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </div>
        </div>
    </div>
@endforeach

