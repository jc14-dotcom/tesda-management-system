@foreach ($certificates as $certificate)
    <tr>
        <td class="py-2 font-medium text-grayTheme-dark">{{ $certificate->certificate_name }}</td>
        <td class="py-2">{{ $certificate->certificate_type_label }}</td>
        <td class="py-2">{{ $certificate->qualification_title ?? '—' }}</td>
        <td class="py-2">{{ $certificate->certificate_number ?? '—' }}</td>
        <td class="py-2">{{ $certificate->expiration_date?->format('Y-m-d') ?? '—' }}</td>
        <td class="py-2">{{ ucfirst($certificate->status) }}</td>
        <td class="py-2">
            @forelse ($certificate->documents as $document)
                <div>
                    <a class="text-primary hover:text-primary-hover" href="{{ route('documents.download', $document) }}">
                        {{ $document->document_name ?? $document->original_name }}
                    </a>
                </div>
            @empty
                <span class="text-grayTheme-medium">—</span>
            @endforelse
        </td>
    </tr>
@endforeach
