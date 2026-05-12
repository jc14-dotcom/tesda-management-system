@foreach ($certificates as $certificate)
    <tr>
        <td class="py-2 font-medium text-grayTheme-dark">{{ $certificate->certificate_name }}</td>
        <td class="py-2 text-sm text-grayTheme-medium">{{ $certificate->certificate_type_label }}</td>
        <td class="py-2 text-sm text-grayTheme-medium">{{ $certificate->qualification_title ?? '—' }}</td>
        <td class="py-2">
            @if ($certificate->documents->isNotEmpty())
                <a href="{{ route('documents.view', $certificate->documents->first()) }}" target="_blank" class="text-sm font-semibold text-primary hover:underline">View file</a>
            @else
                <span class="text-xs text-grayTheme-medium">No file</span>
            @endif
        </td>
        <td class="py-2 text-sm text-grayTheme-medium">
            {{ $certificate->expiration_date ? $certificate->expiration_date->format('M d, Y') : '—' }}
        </td>
        <td class="py-2">
            @php
                $statusTone = match($certificate->status) {
                    'valid'    => 'bg-success-soft text-success',
                    'expiring' => 'bg-warning-soft text-warning',
                    'expired'  => 'bg-danger-soft text-danger',
                    default    => 'bg-grayTheme-hover text-grayTheme-medium',
                };
            @endphp
            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusTone }}">
                {{ ucfirst($certificate->status) }}
            </span>
        </td>
        <td class="py-2">
            <form method="post" action="{{ route('certificates.destroy', $certificate) }}" onsubmit="return confirm('Delete this certificate and all its files?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-800">Delete</button>
            </form>
        </td>
    </tr>
@endforeach
