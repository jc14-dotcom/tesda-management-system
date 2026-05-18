@foreach ($documents as $document)
    @php
        $typeConfig = match($document->type) {
            'cv'       => ['bg' => 'bg-primary-soft', 'text' => 'text-primary', 'iconPath' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            'training' => ['bg' => 'bg-accent-soft',  'text' => 'text-accent-active', 'iconPath' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
            default    => ['bg' => 'bg-grayTheme-light', 'text' => 'text-grayTheme-medium', 'iconPath' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        };
    @endphp
    <div class="flex items-center gap-3 rounded-xl border border-grayTheme-border bg-white p-3 shadow-sm transition hover:shadow-md">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $typeConfig['bg'] }}">
            <svg class="h-5 w-5 {{ $typeConfig['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeConfig['iconPath'] }}" />
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-[11px] font-semibold uppercase tracking-wide {{ $typeConfig['text'] }}">{{ strtoupper($document->type) }}</p>
            <p class="truncate text-sm font-semibold text-grayTheme-dark">{{ $document->document_name ?? $document->original_name }}</p>
        </div>
        <a class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-primary transition hover:bg-primary-soft focus:outline-none" href="{{ route('documents.download', $document) }}">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download
        </a>
    </div>
@endforeach
