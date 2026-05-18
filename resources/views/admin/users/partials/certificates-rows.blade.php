@foreach ($certificates as $certificate)
    <tr class="transition hover:bg-grayTheme-light/60">
        <td class="px-4 py-3 font-medium text-grayTheme-dark">{{ $certificate->certificate_name }}</td>
        <td class="px-4 py-3">
            <span class="inline-flex items-center rounded-full bg-grayTheme-hover px-2.5 py-0.5 text-xs font-semibold text-grayTheme-dark">{{ $certificate->certificate_type_label }}</span>
        </td>
        <td class="px-4 py-3 text-sm text-grayTheme-medium">{{ $certificate->qualification_title ?? '—' }}</td>
        <td class="px-4 py-3 font-mono text-xs text-grayTheme-medium">{{ $certificate->certificate_number ?? '—' }}</td>
        <td class="px-4 py-3 text-sm text-grayTheme-medium">{{ $certificate->expiration_date?->format('M d, Y') ?? '—' }}</td>
        <td class="px-4 py-3">
            @php
                $statusTone = match($certificate->status) {
                    'valid'    => 'bg-success-soft text-success',
                    'expiring' => 'bg-warning-soft text-warning',
                    'expired'  => 'bg-danger-soft text-danger',
                    default    => 'bg-grayTheme-hover text-grayTheme-medium',
                };
            @endphp
            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusTone }}">
                @if ($certificate->status === 'valid')
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                @elseif ($certificate->status === 'expiring')
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @elseif ($certificate->status === 'expired')
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                @endif
                {{ ucfirst($certificate->status) }}
            </span>
        </td>
        <td class="px-4 py-3">
            @forelse ($certificate->documents as $document)
                <div>
                    <a class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline" href="{{ route('documents.download', $document) }}">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        {{ $document->document_name ?? $document->original_name }}
                    </a>
                </div>
            @empty
                <span class="text-xs text-grayTheme-medium">—</span>
            @endforelse
        </td>
    </tr>
@endforeach
