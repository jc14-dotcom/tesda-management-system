@foreach ($documents as $document)
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm text-grayTheme-medium">{{ strtoupper($document->type) }}</div>
            <div class="text-grayTheme-dark">{{ $document->document_name ?? $document->original_name }}</div>
        </div>
        <a class="text-primary hover:text-primary-hover" href="{{ route('documents.download', $document) }}">
            Download
        </a>
    </div>
@endforeach
