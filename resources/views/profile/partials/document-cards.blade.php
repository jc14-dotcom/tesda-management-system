@foreach ($documents as $document)
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
        <button
            type="button"
            class="block w-full text-left"
            @click="openDocument({
                title: @js($document->document_name ?? $document->original_name),
                type: @js(strtoupper($document->type)),
                previewUrl: @js(route('documents.preview', $document)),
                downloadUrl: @js(route('documents.download', $document)),
                originalName: @js($document->original_name),
                viewUrl: @js(route('documents.view', $document)),
            })"
        >
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ strtoupper($document->type) }}</div>
                    <div class="mt-1 truncate text-base font-semibold text-gray-900">{{ $document->document_name ?? $document->original_name }}</div>
                    <div class="mt-1 text-sm text-gray-500">Click to view or print</div>
                </div>
                <div class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">Open</div>
            </div>
        </button>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button type="button" class="text-sm font-semibold text-primary hover:text-blue-800" @click="openDocument({
                title: @js($document->document_name ?? $document->original_name),
                type: @js(strtoupper($document->type)),
                previewUrl: @js(route('documents.preview', $document)),
                downloadUrl: @js(route('documents.download', $document)),
                originalName: @js($document->original_name),
                viewUrl: @js(route('documents.view', $document)),
            })">
                View
            </button>
            <a class="text-sm font-semibold text-primary hover:text-blue-800" href="{{ route('documents.download', $document) }}">
                Download
            </a>

            <form method="post" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('Delete this document?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-800">
                    Delete
                </button>
            </form>
        </div>
    </div>
@endforeach
