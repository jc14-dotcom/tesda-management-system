<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Backups"
                subtitle="Database and file backups."
                eyebrow="Administration"
            />

            {{-- Flash messages --}}
        @if(session('status') === 'backup-success')
            <div class="mb-4 rounded-lg bg-success-soft px-4 py-3 text-sm font-semibold text-success">Backup created successfully.</div>
        @elseif(session('status') === 'backup-failed')
            <div class="mb-4 rounded-lg bg-danger-soft px-4 py-3 text-sm font-semibold text-danger">Backup failed: {{ session('backup_error') }}</div>
        @elseif(session('status') === 'backup-deleted')
            <div class="mb-4 rounded-lg bg-success-soft px-4 py-3 text-sm font-semibold text-success">Backup deleted.</div>
        @endif

        {{-- Run backup --}}
        <div class="mb-6 flex items-center justify-between">
            <p class="text-sm text-grayTheme-medium">Backups are stored on the <strong>{{ $disk ?? 'local' }}</strong> disk.</p>
            <form method="post" action="{{ route('admin.backups.run') }}" onsubmit="return confirm('Start a new backup? This may take a moment.')">
                @csrf
                <button type="submit" class="btn-primary">Run Backup Now</button>
            </form>
        </div>

        <div class="surface overflow-x-auto rounded-xl shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="border-b border-grayTheme-border">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-grayTheme-dark">File</th>
                        <th class="px-6 py-3 text-left font-semibold text-grayTheme-dark">Size</th>
                        <th class="px-6 py-3 text-left font-semibold text-grayTheme-dark">Created</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-grayTheme-border">
                    @forelse($backups ?? [] as $backup)
                        <tr class="hover:bg-grayTheme-light transition-colors">
                            <td class="px-6 py-3 font-mono text-xs text-grayTheme-dark">{{ $backup['name'] }}</td>
                            <td class="px-6 py-3 text-grayTheme-medium">
                                @php
                                    $kb = round($backup['size'] / 1024, 1);
                                    $mb = round($backup['size'] / 1048576, 2);
                                    echo $backup['size'] >= 1048576 ? "{$mb} MB" : "{$kb} KB";
                                @endphp
                            </td>
                            <td class="px-6 py-3 text-grayTheme-medium">{{ date('Y-m-d H:i', $backup['modified']) }}</td>
                            <td class="px-6 py-3 flex items-center gap-3">
                                <a href="{{ route('admin.backups.download', ['disk' => $backup['disk'], 'path' => $backup['path']]) }}" class="text-xs text-primary hover:underline">Download</a>
                                <form method="post" action="{{ route('admin.backups.destroy', ['disk' => $backup['disk'], 'path' => $backup['path']]) }}" onsubmit="return confirm('Delete this backup file?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-danger hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-grayTheme-medium">No backups found. Click &ldquo;Run Backup Now&rdquo; to create the first one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </div>
</x-app-layout>
