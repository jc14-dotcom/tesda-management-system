@foreach ($certificates as $certificate)
    @php($certificateFile = $certificate->documents->first())
    <tr>
        <td class="py-2 font-medium text-grayTheme-dark">{{ $certificate->certificate_name }}</td>
        <td class="py-2">{{ $certificate->certificate_type_label }}</td>
        <td class="py-2">{{ $certificate->qualification_title ?? '—' }}</td>
        <td class="py-2">
            @if ($certificateFile)
                <a class="text-primary hover:text-primary-hover" href="{{ route('documents.download', $certificateFile) }}">
                    {{ $certificateFile->original_name }}
                </a>
            @else
                <span class="text-grayTheme-medium">—</span>
            @endif
        </td>
        <td class="py-2">{{ $certificate->expiration_date?->format('Y-m-d') ?? '—' }}</td>
        <td class="py-2">{{ ucfirst($certificate->status) }}</td>
        <td class="py-2 text-right">
            <form method="post" action="{{ route('certificates.destroy', $certificate) }}">
                @csrf
                @method('delete')
                <button class="text-red-600 hover:text-red-900" type="submit">
                    Delete
                </button>
            </form>
        </td>
    </tr>
@endforeach
