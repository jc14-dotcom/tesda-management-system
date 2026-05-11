<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="User Details"
                subtitle="View the selected user account and documents."
                eyebrow="Administration"
            >
                <x-slot:actions>
                    <a class="rounded-full border border-white/30 px-3 py-1 text-sm font-semibold text-white/90 hover:text-white" href="{{ route('admin.users.index') }}">
                        Back to users
                    </a>
                </x-slot:actions>
            </x-page-header>

            <div class="surface p-6">
                <h3 class="text-lg font-semibold text-grayTheme-dark">Account</h3>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <div class="text-sm text-grayTheme-medium">Name</div>
                        <div class="text-grayTheme-dark">{{ $user->name }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-grayTheme-medium">Email</div>
                        <div class="text-grayTheme-dark">{{ $user->email }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-grayTheme-medium">Role</div>
                        <div class="text-grayTheme-dark">{{ $user->hasRole('admin') ? 'Admin' : 'User' }}</div>
                    </div>
                </div>
            </div>

            <div class="surface p-6">
                <h3 class="text-lg font-semibold text-grayTheme-dark">Profile Details</h3>
                @if ($user->profile)
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <div class="text-sm text-grayTheme-medium">Contact Number</div>
                            <div class="text-grayTheme-dark">{{ $user->profile->contact_number ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-grayTheme-medium">Status</div>
                            <div class="text-grayTheme-dark">{{ $user->profile->status ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-grayTheme-medium">Region / Branch</div>
                            <div class="text-grayTheme-dark">{{ $user->profile->region ?? '—' }} / {{ $user->profile->branch ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-grayTheme-medium">TESDA Registry Number</div>
                            <div class="text-grayTheme-dark">{{ $user->profile->tesda_registry_number ?? '—' }}</div>
                        </div>
                        <div class="md:col-span-2">
                            <div class="text-sm text-grayTheme-medium">Address</div>
                            <div class="text-grayTheme-dark">{{ $user->profile->address ?? '—' }}</div>
                        </div>
                    </div>
                @else
                    <p class="mt-2 text-sm text-grayTheme-medium">No profile details yet.</p>
                @endif
            </div>

            <div class="surface p-6">
                <h3 class="text-lg font-semibold text-grayTheme-dark">Certificates</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-grayTheme-medium">
                            <tr>
                                <th class="py-2">Name</th>
                                <th class="py-2">TESDA Classification</th>
                                <th class="py-2">Program / Qualification</th>
                                <th class="py-2">Number</th>
                                <th class="py-2">Expires</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Documents</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($user->certificates as $certificate)
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
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-grayTheme-medium">No certificates found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="surface p-6">
                <h3 class="text-lg font-semibold text-grayTheme-dark">Other Documents</h3>
                <div class="mt-4 space-y-2">
                    @forelse ($user->documents->where('type', '!=', 'certificate') as $document)
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-grayTheme-medium">{{ strtoupper($document->type) }}</div>
                                <div class="text-grayTheme-dark">{{ $document->document_name ?? $document->original_name }}</div>
                            </div>
                            <a class="text-primary hover:text-primary-hover" href="{{ route('documents.download', $document) }}">
                                Download
                            </a>
                        </div>
                    @empty
                        <p class="text-sm text-grayTheme-medium">No documents uploaded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
