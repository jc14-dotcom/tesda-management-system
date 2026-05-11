<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                :title="($document->document_name ?: $document->original_name)"
                subtitle="Document preview"
                eyebrow="Documents"
            >
                <x-slot:actions>
                    <a href="{{ url()->previous() }}" class="rounded-full border border-white/30 px-3 py-1 text-sm font-semibold text-white/90 hover:text-white">
                        Back
                    </a>
                </x-slot:actions>
            </x-page-header>

            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 pb-4">
                    <div>
                        <div class="text-sm font-semibold uppercase tracking-wide text-slate-500">{{ strtoupper($document->type) }}</div>
                        <div class="text-lg font-semibold text-slate-900">{{ $document->document_name ?: $document->original_name }}</div>
                        <div class="mt-1 text-sm text-slate-500">{{ $document->original_name }}</div>
                        @if (in_array($document->type, ['nc', 'nttc']))
                            <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-500">
                                @if ($document->certificate_no)
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Cert No.: {{ $document->certificate_no }}</span>
                                @endif
                                @if ($document->issued_on)
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Issued On: {{ $document->issued_on->format('Y-m-d') }}</span>
                                @endif
                                @if ($document->valid_until)
                                    <span class="rounded-full bg-slate-100 px-3 py-1">Valid Until: {{ $document->valid_until->format('Y-m-d') }}</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button" onclick="window.print()" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Print
                        </button>
                        <a href="{{ route('documents.download', $document) }}" class="rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover">
                            Download
                        </a>
                    </div>
                </div>

                <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                    <iframe
                        src="{{ $previewUrl }}"
                        title="{{ $document->document_name ?: $document->original_name }}"
                        class="h-[80vh] w-full"
                    ></iframe>
                </div>

                <div class="mt-4 text-sm text-slate-500">
                    If the file does not render in the browser, use Download or open it in a supported viewer.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>