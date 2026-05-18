<x-app-layout>
    <div class="py-12" x-data="{
        confirmOpen: false,
        confirmTitle: '',
        confirmMessage: '',
        askDelete(title, message) {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.confirmOpen = true;
        }
    }">
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

            {{-- Manage Account --}}
            <div class="surface p-6">
                <h3 class="text-lg font-semibold text-grayTheme-dark">Manage Account</h3>

                @if (session('status') === 'user-updated')
                    <div class="mt-3 rounded-lg bg-success-soft px-4 py-2 text-sm font-semibold text-success">Account updated successfully.</div>
                @endif

                @if (session('status') === 'user-created')
                    <div class="mt-3 rounded-lg bg-success-soft px-4 py-2 text-sm font-semibold text-success">Account created successfully.</div>
                @endif

                @if (session('status') === 'password-reset')
                    <div class="mt-3 rounded-lg bg-success-soft px-4 py-2 text-sm font-semibold text-success">Password has been reset successfully.</div>
                @endif

                @if ($errors->any())
                    <div class="mt-3 rounded-lg bg-danger-soft px-4 py-2 text-sm text-danger">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-4 grid gap-5 md:grid-cols-2">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="mgmt_name">Full Name<span class="ml-0.5 text-red-500" aria-hidden="true">*</span></label>
                        <input id="mgmt_name" type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="mt-1 form-input w-full" required maxlength="255" />
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="mgmt_email">Email Address<span class="ml-0.5 text-red-500" aria-hidden="true">*</span></label>
                        <input id="mgmt_email" type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="mt-1 form-input w-full" required maxlength="255" />
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="mgmt_role">Role</label>
                        <select id="mgmt_role" name="role" class="mt-1 form-input w-full">
                            <option value="user" @selected(! $user->hasRole('admin'))>User</option>
                            <option value="admin" @selected($user->hasRole('admin'))>Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="mgmt_status">Account Status</label>
                        <select id="mgmt_status" name="status" class="mt-1 form-input w-full">
                            <option value="active" @selected(($user->profile?->status ?? 'active') === 'active')>Active</option>
                            <option value="inactive" @selected(($user->profile?->status ?? 'active') === 'inactive')>Inactive</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end md:col-span-2">
                        <button type="submit" class="btn-primary gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Save Changes
                        </button>
                    </div>
                </form>

                {{-- Danger Zone --}}
                @if(auth()->id() !== $user->id)
                <div class="mt-8 border-t border-red-200 pt-6">
                    <h4 class="text-sm font-bold text-danger">Danger Zone</h4>
                    <p class="mt-1 text-sm text-grayTheme-medium">Permanently deletes this user account, their profile, all certificates, and uploaded files. This cannot be undone.</p>
                    <form id="delete-user-form" method="POST" action="{{ route('admin.users.destroy', $user) }}" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button
                        type="button"
                        class="btn-danger mt-4 gap-2"
                        @click="askDelete('Delete Account', 'Permanently delete {{ addslashes($user->name) }}\'s account, profile, all certificates and files? This cannot be undone.')"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete Account
                    </button>
                </div>
                @else
                <div class="mt-8 border-t border-grayTheme-border pt-6">
                    <h4 class="text-sm font-bold text-grayTheme-medium">Danger Zone</h4>
                    <p class="mt-1 text-sm text-grayTheme-medium">You cannot delete your own account.</p>
                </div>
                @endif
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
                <form method="get" class="mt-4 flex flex-wrap items-end gap-3 text-sm">
                    @if ($docType !== 'all')
                        <input type="hidden" name="doc_type" value="{{ $docType }}" />
                    @endif
                    <div>
                        <label class="text-xs font-semibold uppercase text-grayTheme-medium" for="cert_status">Status</label>
                        <select id="cert_status" name="cert_status" class="mt-1 form-input">
                            <option value="all" @selected($certStatus === 'all')>All</option>
                            <option value="valid" @selected($certStatus === 'valid')>Valid</option>
                            <option value="expiring" @selected($certStatus === 'expiring')>Expiring</option>
                            <option value="expired" @selected($certStatus === 'expired')>Expired</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase text-grayTheme-medium" for="cert_window">Expiration Window</label>
                        <select id="cert_window" name="cert_window" class="mt-1 form-input">
                            <option value="0" @selected($certWindow === 0)>All dates</option>
                            <option value="30" @selected($certWindow === 30)>Next 30 days</option>
                            <option value="60" @selected($certWindow === 60)>Next 60 days</option>
                            <option value="90" @selected($certWindow === 90)>Next 90 days</option>
                        </select>
                    </div>
                    <button class="btn-primary" type="submit">Apply</button>
                </form>

                <div
                    class="mt-4"
                    x-data="loadMoreList({ nextUrl: @js($certificates->nextPageUrl()), partialParam: 'certificates_partial' })"
                    x-init="items = @js($certificates->map(fn($cert) => [
                        'id'                 => $cert->id,
                        'name'               => $cert->certificate_name,
                        'type'               => $cert->certificate_type_label,
                        'qualification'      => $cert->qualification_title ?? '—',
                        'number'             => $cert->certificate_number ?? '—',
                        'expirationDate'     => $cert->expiration_date?->format('Y-m-d') ?? '—',
                        'status'             => ucfirst($cert->status),
                        'verificationStatus' => ucfirst($cert->verification_status ?? 'pending'),
                        'documents'          => $cert->documents->map(fn($d) => [
                            'name'        => $d->document_name ?? $d->original_name,
                            'downloadUrl' => route('documents.download', $d),
                        ])->values()->all(),
                    ]))"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-primary">
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">TESDA Classification</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Qualification</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Number</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Expires</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Verified</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-white">Documents</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-grayTheme-border">
                                <template x-if="items.length === 0">
                                    <tr>
                                        <td colspan="8" class="px-4 py-10 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-grayTheme-light">
                                                    <svg class="h-5 w-5 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                </div>
                                                <p class="text-sm text-grayTheme-medium">No certificates found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="cert in items" :key="cert.id">
                                    <tr class="transition hover:bg-grayTheme-light/60">
                                        <td class="px-4 py-3 font-medium text-grayTheme-dark" x-text="cert.name"></td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center rounded-full bg-grayTheme-hover px-2.5 py-0.5 text-xs font-semibold text-grayTheme-dark" x-text="cert.type"></span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-grayTheme-medium" x-text="cert.qualification"></td>
                                        <td class="px-4 py-3 font-mono text-xs text-grayTheme-medium" x-text="cert.number"></td>
                                        <td class="px-4 py-3 text-sm text-grayTheme-medium" x-text="cert.expirationDate"></td>
                                        <td class="px-4 py-3">
                                            <span
                                                x-text="cert.status"
                                                :class="{
                                                    'bg-success-soft text-success': cert.status === 'Valid',
                                                    'bg-warning-soft text-warning': cert.status === 'Expiring',
                                                    'bg-danger-soft text-danger':   cert.status === 'Expired',
                                                    'bg-grayTheme-hover text-grayTheme-medium': !['Valid','Expiring','Expired'].includes(cert.status),
                                                }"
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                            ></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                x-text="cert.verificationStatus"
                                                :class="{
                                                    'bg-success-soft text-success': cert.verificationStatus === 'Verified',
                                                    'bg-danger-soft text-danger':   cert.verificationStatus === 'Rejected',
                                                    'bg-warning-soft text-warning': cert.verificationStatus === 'Pending',
                                                }"
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                            ></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <template x-if="cert.documents.length === 0">
                                                <span class="text-xs text-grayTheme-medium">—</span>
                                            </template>
                                            <template x-for="doc in cert.documents" :key="doc.downloadUrl">
                                                <div>
                                                    <a class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline" :href="doc.downloadUrl">
                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        <span x-text="doc.name"></span>
                                                    </a>
                                                </div>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        {{ $certificates->links() }}
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 btn-secondary"
                            x-show="nextUrl"
                            x-on:click="loadMore"
                            :disabled="loading"
                        >
                            <svg x-show="!loading" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            <svg x-show="loading" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span x-show="!loading">Load more</span>
                            <span x-show="loading">Loading&hellip;</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="surface p-6">
                <h3 class="text-lg font-semibold text-grayTheme-dark">Other Documents</h3>
                <form method="get" class="mt-4 flex flex-wrap items-end gap-3 text-sm">
                    @if ($certStatus !== 'all')
                        <input type="hidden" name="cert_status" value="{{ $certStatus }}" />
                    @endif
                    @if ($certWindow > 0)
                        <input type="hidden" name="cert_window" value="{{ $certWindow }}" />
                    @endif
                    <div>
                        <label class="text-xs font-semibold uppercase text-grayTheme-medium" for="doc_type">Document Type</label>
                        <select id="doc_type" name="doc_type" class="mt-1 form-input">
                            <option value="all" @selected($docType === 'all')>All</option>
                            <option value="cv" @selected($docType === 'cv')>CV</option>
                            <option value="other" @selected($docType === 'other')>Other</option>
                        </select>
                    </div>
                    <button class="btn-primary" type="submit">Apply</button>
                </form>

                <div
                    class="mt-4"
                    x-data="loadMoreList({ nextUrl: @js($documents->nextPageUrl()), partialParam: 'documents_partial' })"
                    x-init="items = @js($documents->map(fn($doc) => [
                        'id'          => $doc->id,
                        'type'        => strtoupper($doc->type),
                        'name'        => $doc->document_name ?? $doc->original_name,
                        'downloadUrl' => route('documents.download', $doc),
                    ]))"
                >
                    <template x-if="items.length === 0">
                        <div class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-grayTheme-border bg-grayTheme-light py-10 text-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-sm">
                                <svg class="h-6 w-6 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-sm text-grayTheme-medium">No documents uploaded.</p>
                        </div>
                    </template>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <template x-for="doc in items" :key="doc.id">
                            <div class="flex items-center gap-3 rounded-xl border border-grayTheme-border bg-white p-3 shadow-sm transition hover:shadow-md">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                    :class="{
                                        'bg-primary-soft': doc.type === 'CV',
                                        'bg-accent-soft': doc.type === 'TRAINING',
                                        'bg-grayTheme-light': !['CV','TRAINING'].includes(doc.type)
                                    }"
                                >
                                    <svg x-show="doc.type === 'CV'" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <svg x-show="doc.type === 'TRAINING'" class="h-5 w-5 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    <svg x-show="!['CV','TRAINING'].includes(doc.type)" class="h-5 w-5 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide"
                                        :class="{
                                            'text-primary': doc.type === 'CV',
                                            'text-accent-active': doc.type === 'TRAINING',
                                            'text-grayTheme-medium': !['CV','TRAINING'].includes(doc.type)
                                        }"
                                        x-text="doc.type"></p>
                                    <p class="truncate text-sm font-semibold text-grayTheme-dark" x-text="doc.name"></p>
                                </div>
                                <a
                                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-primary transition hover:bg-primary-soft focus:outline-none"
                                    :href="doc.downloadUrl"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download
                                </a>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        {{ $documents->links() }}
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 btn-secondary"
                            x-show="nextUrl"
                            x-on:click="loadMore"
                            :disabled="loading"
                        >
                            <svg x-show="!loading" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            <svg x-show="loading" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span x-show="!loading">Load more</span>
                            <span x-show="loading">Loading&hellip;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Confirmation Modal --}}
        <div x-cloak x-show="confirmOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4"
            @keydown.escape.window="confirmOpen = false" @click.self="confirmOpen = false">
            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-danger-soft">
                        <svg class="h-5 w-5 text-danger" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-grayTheme-dark" x-text="confirmTitle"></h3>
                        <p class="mt-1 text-sm text-grayTheme-medium" x-text="confirmMessage"></p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn-secondary" @click="confirmOpen = false">Cancel</button>
                    <button type="button" class="btn-danger gap-2" @click="document.getElementById('delete-user-form').submit()">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
