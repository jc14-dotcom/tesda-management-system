@foreach ($certificates as $certificate)
    <tr class="transition hover:bg-grayTheme-light/60">
        <td class="px-4 py-3 font-medium text-grayTheme-dark">{{ $certificate->qualification_title ?? $certificate->certificate_name }}</td>
        <td class="px-4 py-3">
            <span class="inline-flex items-center rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-semibold text-primary">{{ $certificate->certificate_type_label }}</span>
        </td>
        <td class="px-4 py-3 font-mono text-xs text-grayTheme-medium">{{ $certificate->certificate_number ?? '—' }}</td>
        <td class="px-4 py-3">
            @if ($certificate->documents->isNotEmpty())
                <a href="{{ route('documents.view', $certificate->documents->first()) }}" target="_blank" class="inline-flex items-center gap-1 rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-semibold text-primary transition hover:bg-primary hover:text-white">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    View
                </a>
            @else
                <span class="inline-flex items-center gap-1 text-xs text-grayTheme-medium">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    No file
                </span>
            @endif
        </td>
        <td class="px-4 py-3 text-sm text-grayTheme-medium">
            {{ $certificate->expiration_date ? $certificate->expiration_date->format('M d, Y') : '—' }}
        </td>
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
        <td class="px-4 py-3 text-right">
            <button
                type="button"
                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-danger transition hover:bg-danger-soft focus:outline-none"
                @click="$dispatch('cert-confirm-delete', { url: '{{ route('certificates.destroy', $certificate) }}', name: '{{ addslashes($certificate->qualification_title ?? $certificate->certificate_name) }}' })"
            >
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
            </button>
        </td>
    </tr>
@endforeach
