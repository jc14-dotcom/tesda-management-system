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
                        @if ($document->type === 'certificate')
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
                        <button type="button" onclick="printDocumentFile('{{ addslashes($previewUrl) }}','{{ addslashes($document->original_name ?? '') }}')" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
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

<script>
function printDocumentFile(url, filename) {
    var isImage = /\.(jpe?g|png|gif|webp|bmp|tiff?)$/i.test(filename || '');

    if (isImage) {
        var win = window.open('', '_blank', 'width=900,height=1100');
        if (!win) { alert('Please allow popups to enable printing.'); return; }
        win.document.write(
            '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Document</title>' +
            '<style>@page{size:A4 portrait;margin:1.5cm}*{margin:0;padding:0;box-sizing:border-box}' +
            'body{background:#fff;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px}' +
            'img{max-width:100%;max-height:100vh;object-fit:contain;display:block}</style></head>' +
            '<body><img src="' + url + '" onload="setTimeout(function(){window.focus();window.print();},250);"></body></html>'
        );
        win.document.close();
        return;
    }

    var win = window.open(url, '_blank');
    if (!win) { alert('Please allow popups to enable printing.'); return; }
    var printed = false;
    var doPrint = function () {
        if (printed) return;
        printed = true;
        try { win.focus(); win.print(); } catch (e) {}
    };
    win.addEventListener('load', function () { setTimeout(doPrint, 1200); }, { once: true });
    setTimeout(doPrint, 2500);
}
</script>
</x-app-layout>