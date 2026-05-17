<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Documents"
                subtitle="Monitor certificate files and supporting documents across all users."
                eyebrow="Administration"
            />

            {{-- Filters --}}
            <div class="surface p-6">
                <form method="get" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-48">
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="search">Search</label>
                        <input id="search" type="text" name="search" value="{{ $search }}"
                            class="mt-1 form-input w-full" placeholder="Document name or user…" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="type">Type</label>
                        <select id="type" name="type" class="mt-1 form-input">
                            <option value="all" @selected($type === 'all')>All Types</option>
                            <option value="cv" @selected($type === 'cv')>CV</option>
                            <option value="certificate" @selected($type === 'certificate')>Certificate</option>
                            <option value="other" @selected($type === 'other')>Other</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Apply</button>
                    @if ($search || $type !== 'all')
                        <a href="{{ route('admin.documents.index') }}" class="btn-secondary">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Results --}}
            <div class="surface">
                <div class="flex items-center gap-4 border-b px-6 py-4">
                    <span class="text-sm font-semibold text-grayTheme-dark">
                        {{ number_format($documents->total()) }} {{ Str::plural('document', $documents->total()) }} found
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-grayTheme-medium">
                            <tr>
                                <th class="px-6 py-3">User</th>
                                <th class="px-6 py-3">Document</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Size</th>
                                <th class="px-6 py-3">Uploaded</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($documents as $doc)
                                <tr>
                                    <td class="px-6 py-3">
                                        <a href="{{ route('admin.users.show', $doc->user) }}" class="font-medium text-primary hover:text-primary-hover">
                                            {{ $doc->user->name ?? '—' }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="font-medium text-grayTheme-dark">{{ $doc->document_name ?: $doc->original_name }}</div>
                                        @if ($doc->document_name && $doc->original_name !== $doc->document_name)
                                            <div class="text-xs text-grayTheme-medium">{{ $doc->original_name }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-semibold text-primary">
                                            {{ strtoupper($doc->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-grayTheme-medium">
                                        @php
                                            $kb = $doc->size / 1024;
                                            echo $kb >= 1024
                                                ? number_format($kb / 1024, 2) . ' MB'
                                                : number_format($kb, 1) . ' KB';
                                        @endphp
                                    </td>
                                    <td class="px-6 py-3 text-grayTheme-medium">{{ $doc->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('documents.download', $doc) }}" class="font-medium text-primary hover:text-primary-hover">Download</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-grayTheme-medium">No documents found matching your filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($documents->hasPages())
                    <div class="border-t px-6 py-4">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
