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
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="mgmt_name">Full Name</label>
                        <input id="mgmt_name" type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="mt-1 form-input w-full" required maxlength="255" />
                    </div>

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="mgmt_email">Email Address</label>
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
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </div>
                </form>

                {{-- Reset Password --}}
                <div class="mt-8 border-t border-grayTheme-border pt-6">
                    <h4 class="text-sm font-bold text-grayTheme-dark">Reset Password</h4>
                    <p class="mt-1 text-sm text-grayTheme-medium">Set a new password for this account. The user will need to use this password on their next login.</p>
                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="mt-4 grid gap-4 md:grid-cols-2">
                        @csrf
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="new_password">New Password</label>
                            <input id="new_password" type="password" name="password" class="mt-1 form-input w-full" required minlength="8" autocomplete="new-password" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium" for="new_password_confirmation">Confirm New Password</label>
                            <input id="new_password_confirmation" type="password" name="password_confirmation" class="mt-1 form-input w-full" required minlength="8" autocomplete="new-password" />
                        </div>
                        <div class="flex items-center justify-end md:col-span-2">
                            <button type="submit" class="btn-secondary">Reset Password</button>
                        </div>
                    </form>
                </div>

                {{-- Danger Zone --}}
                <div class="mt-8 border-t border-red-200 pt-6">
                    <h4 class="text-sm font-bold text-danger">Danger Zone</h4>
                    <p class="mt-1 text-sm text-grayTheme-medium">Permanently deletes this user account, their profile, all certificates, and uploaded files. This cannot be undone.</p>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-4"
                          onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}\'s account? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">Delete Account</button>
                    </form>
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
                            <thead class="text-left text-grayTheme-medium">
                                <tr>
                                    <th class="py-2">Name</th>
                                    <th class="py-2">TESDA Classification</th>
                                    <th class="py-2">Program / Qualification</th>
                                    <th class="py-2">Number</th>
                                    <th class="py-2">Expires</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2">Verified</th>
                                    <th class="py-2">Documents</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <template x-if="items.length === 0">
                                    <tr>
                                        <td colspan="8" class="py-4 text-center text-grayTheme-medium">No certificates found.</td>
                                    </tr>
                                </template>
                                <template x-for="cert in items" :key="cert.id">
                                    <tr>
                                        <td class="py-2 font-medium text-grayTheme-dark" x-text="cert.name"></td>
                                        <td class="py-2" x-text="cert.type"></td>
                                        <td class="py-2" x-text="cert.qualification"></td>
                                        <td class="py-2" x-text="cert.number"></td>
                                        <td class="py-2" x-text="cert.expirationDate"></td>
                                        <td class="py-2" x-text="cert.status"></td>
                                        <td class="py-2">
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
                                        <td class="py-2">
                                            <template x-if="cert.documents.length === 0">
                                                <span class="text-grayTheme-medium">—</span>
                                            </template>
                                            <template x-for="doc in cert.documents" :key="doc.downloadUrl">
                                                <div>
                                                    <a class="text-primary hover:text-primary-hover" :href="doc.downloadUrl" x-text="doc.name"></a>
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
                            class="btn-secondary"
                            x-show="nextUrl"
                            x-on:click="loadMore"
                            :disabled="loading"
                        >
                            <span x-show="!loading">Load more</span>
                            <span x-show="loading">Loading...</span>
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
                        <p class="text-sm text-grayTheme-medium">No documents uploaded.</p>
                    </template>
                    <div class="space-y-2">
                        <template x-for="doc in items" :key="doc.id">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm text-grayTheme-medium" x-text="doc.type"></div>
                                    <div class="text-grayTheme-dark" x-text="doc.name"></div>
                                </div>
                                <a class="text-primary hover:text-primary-hover" :href="doc.downloadUrl">Download</a>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        {{ $documents->links() }}
                        <button
                            type="button"
                            class="btn-secondary"
                            x-show="nextUrl"
                            x-on:click="loadMore"
                            :disabled="loading"
                        >
                            <span x-show="!loading">Load more</span>
                            <span x-show="loading">Loading...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
