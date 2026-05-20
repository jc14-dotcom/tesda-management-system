<section x-data="{
    confirmOpen: false,
    confirmTitle: '',
    confirmMessage: '',
    pendingDeleteUrl: '',
    askDelete(url, name) {
        this.pendingDeleteUrl = url;
        this.confirmTitle = 'Delete Certificate';
        this.confirmMessage = '\u201c' + name + '\u201d and all its attached files will be permanently removed. This cannot be undone.';
        this.confirmOpen = true;
    },
}" @cert-confirm-delete.window="askDelete($event.detail.url, $event.detail.name)">

    {{-- Section header --}}
    <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-primary-soft">
            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-grayTheme-dark">{{ __('Certificates') }}</h2>
            <p class="text-sm text-grayTheme-medium">{{ __('Track TESDA certifications by level or classification, then record the related program or qualification title.') }}</p>
        </div>
    </div>

    {{-- Add Certificate Form --}}
    <form method="post" action="{{ route('certificates.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6"
          x-data="{ certType: '{{ old('certificate_type', 'nc_i') }}' }">
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                @php
                    $trainerTitles = array_values(array_filter((array) ($profile?->trainer_qualification_titles ?? [])));
                    $assessorTitles = array_values(array_filter((array) ($profile?->assessor_qualification_titles ?? [])));
                    $hasTitles = !empty($trainerTitles) || !empty($assessorTitles);
                    $initIsOther = old('certificate_type', 'nc_i') === 'other';
                @endphp
                <x-input-label for="qualification_title" :value="__('Program / Qualification Title')" :required="true" />
                @if ($hasTitles)
                    <select id="qualification_title" name="qualification_title" class="mt-1 form-input w-full"
                            x-show="certType !== 'other'" :disabled="certType === 'other'" :required="certType !== 'other'"
                            style="{{ $initIsOther ? 'display:none' : '' }}">
                        <option value="">Select a qualification title</option>
                        @if (!empty($trainerTitles))
                            <optgroup label="Trainer">
                                @foreach ($trainerTitles as $title)
                                    <option value="{{ $title }}" @selected(old('qualification_title') === $title)>{{ $title }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                        @if (!empty($assessorTitles))
                            <optgroup label="Assessor">
                                @foreach ($assessorTitles as $title)
                                    <option value="{{ $title }}" @selected(old('qualification_title') === $title)>{{ $title }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                    <input type="text" name="qualification_title" class="mt-1 form-input w-full"
                           value="{{ old('qualification_title') }}"
                           placeholder="e.g. First Aid Certificate, Driver's License"
                           x-show="certType === 'other'" :disabled="certType !== 'other'" :required="certType === 'other'"
                           style="{{ $initIsOther ? '' : 'display:none' }}">
                    <p class="mt-1 text-xs text-grayTheme-medium"
                       x-show="certType !== 'other'" style="{{ $initIsOther ? 'display:none' : '' }}">Titles are pulled from your <a href="{{ route('account.profile') }}#update-profile-details" class="font-medium text-primary hover:underline">profile settings</a>.</p>
                    <p class="mt-1 text-xs text-grayTheme-medium"
                       x-show="certType === 'other'" style="{{ $initIsOther ? '' : 'display:none' }}">Enter the name of the credential or qualification.</p>
                @else
                    <input type="text" id="qualification_title" name="qualification_title" class="mt-1 form-input w-full"
                           value="{{ old('qualification_title') }}" required
                           placeholder="e.g. First Aid Certificate, Bookkeeping">
                    <p class="mt-1 text-xs text-grayTheme-medium">No qualification titles in your profile. <a href="{{ route('account.profile') }}#update-profile-details" class="font-medium text-primary hover:underline">Add them here</a> or type manually.</p>
                @endif
                <x-input-error class="mt-2" :messages="$errors->get('qualification_title')" />
            </div>

            <div>
                <x-input-label for="certificate_type" :value="__('Classification / Level')" :required="true" />
                <select id="certificate_type" name="certificate_type" class="mt-1 form-input" x-model="certType" required>
                    <option value="nc_i" @selected(old('certificate_type', 'nc_i') === 'nc_i')>NC I</option>
                    <option value="nc_ii" @selected(old('certificate_type', 'nc_i') === 'nc_ii')>NC II</option>
                    <option value="nc_iii" @selected(old('certificate_type', 'nc_i') === 'nc_iii')>NC III</option>
                    <option value="nc_iv" @selected(old('certificate_type', 'nc_i') === 'nc_iv')>NC IV</option>
                    <option value="nttc" @selected(old('certificate_type', 'nc_i') === 'nttc')>NTTC</option>
                    <option value="assessor" @selected(old('certificate_type', 'nc_i') === 'assessor')>Assessor</option>
                    <option value="other" @selected(old('certificate_type', 'nc_i') === 'other')>Other</option>
                </select>
                <p class="mt-1 text-xs text-grayTheme-medium">Use this for the TESDA level or credential classification, such as NC II or NTTC.</p>
                <x-input-error class="mt-2" :messages="$errors->get('certificate_type')" />
            </div>

            <div>
                <x-input-label for="certificate_number" :value="__('Certificate Number')" :required="true" />
                <x-text-input id="certificate_number" name="certificate_number" type="text" inputmode="numeric" pattern="[0-9]+" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="mt-1 block w-full" :value="old('certificate_number')" required />
                <x-input-error class="mt-2" :messages="$errors->get('certificate_number')" />
            </div>

            <div>
                <x-input-label for="issued_by" :value="__('Issued By')" :required="true" />
                <x-text-input id="issued_by" name="issued_by" type="text" class="mt-1 block w-full" :value="old('issued_by')" required />
                <x-input-error class="mt-2" :messages="$errors->get('issued_by')" />
            </div>

            <div>
                <x-input-label for="issue_date" :value="__('Issue Date')" />
                <x-text-input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full" :value="old('issue_date')" />
                <x-input-error class="mt-2" :messages="$errors->get('issue_date')" />
            </div>

            <div>
                <x-input-label for="expiration_date" :value="__('Expiration Date')" />
                <x-text-input id="expiration_date" name="expiration_date" type="date" class="mt-1 block w-full" :value="old('expiration_date')" />
                <x-input-error class="mt-2" :messages="$errors->get('expiration_date')" />
            </div>

            <div class="md:col-span-2">
                <x-file-input
                    name="certificate_file"
                    id="certificate_file"
                    :accept="'.pdf,.jpg,.jpeg,.png,.webp,.gif,.bmp,.tif,.tiff,image/*,application/pdf'"
                    :required="false"
                    :help="__('Upload a scanned copy or photo of the certificate. Accepted formats: PDF or image files.')"
                >
                    {{ __('Certificate File (Image or PDF)') }}
                </x-file-input>
                <x-input-error class="mt-2" :messages="$errors->get('certificate_file')" />
            </div>
        </div>

        <div>
            <x-input-label for="remarks" :value="__('Remarks')" />
            <textarea id="remarks" name="remarks" class="mt-1 form-input" rows="3">{{ old('remarks') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('remarks')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Add Certificate') }}
            </x-primary-button>

            @if (session('status') === 'certificate-added')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-success"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Certificate added.') }}
                </p>
            @endif
            @if (session('status') === 'certificate-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-success"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Certificate updated.') }}
                </p>
            @endif
        </div>
    </form>

    {{-- Divider --}}
    <div class="my-8 border-t border-grayTheme-border"></div>

    {{-- Existing Certificates --}}
    <div>
        <div class="flex items-center gap-2">
            <svg class="h-4 w-4 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="text-sm font-semibold text-grayTheme-dark">Existing Certificates</h3>
        </div>

        {{-- Filters --}}
        <form method="get" class="mt-3 rounded-xl border border-grayTheme-border bg-grayTheme-light px-4 py-3">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium" for="cert_status">Status</label>
                    <select id="cert_status" name="cert_status" class="mt-1 form-input">
                        <option value="all" @selected($certStatus === 'all')>All</option>
                        <option value="valid" @selected($certStatus === 'valid')>Valid</option>
                        <option value="expiring" @selected($certStatus === 'expiring')>Expiring</option>
                        <option value="expired" @selected($certStatus === 'expired')>Expired</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium" for="cert_window">Expiration Window</label>
                    <select id="cert_window" name="cert_window" class="mt-1 form-input">
                        <option value="0" @selected($certWindow === 0)>All dates</option>
                        <option value="30" @selected($certWindow === 30)>Next 30 days</option>
                        <option value="60" @selected($certWindow === 60)>Next 60 days</option>
                        <option value="90" @selected($certWindow === 90)>Next 90 days</option>
                    </select>
                </div>
                <button class="inline-flex items-center gap-1.5 btn-primary" type="submit">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    Apply
                </button>
            </div>
        </form>

        <div
            class="mt-4"
            x-data="loadMoreList({ 
                nextUrl: @js($certificates->nextPageUrl()), 
                partialParam: 'certificates_partial' 
            })"
            x-init="items = @js($certificates->map(function($cert) {
                $firstDoc = $cert->documents->first();
                return [
                    'id'                => $cert->id,
                    'name'              => $cert->certificate_name,
                    'type'              => $cert->certificate_type_label,
                    'qualification'     => $cert->qualification_title ?? $cert->certificate_name ?? 'â€”',
                    'certificateNumber' => $cert->certificate_number ?? '',
                    'issuedBy'          => $cert->issued_by ?? 'â€”',
                    'issueDate'         => $cert->issue_date ? $cert->issue_date->format('M d, Y') : 'â€”',
                    'expirationDate'    => $cert->expiration_date ? $cert->expiration_date->format('M d, Y') : 'â€”',
                    'status'            => $cert->status,
                    'statusLabel'       => ucfirst($cert->status),
                    'remarks'            => $cert->remarks ?? '',
                    'hasFile'            => (bool) $firstDoc,
                    'previewUrl'         => $firstDoc ? route('documents.preview', $firstDoc) : null,
                    'downloadUrl'        => $firstDoc ? route('documents.download', $firstDoc) : null,
                    'deleteUrl'          => route('certificates.destroy', $cert),
                    'updateUrl'          => route('certificates.update', $cert),
                    'showUrl'            => route('account.certificates.show', $cert),
                    'certificateTypeRaw' => $cert->certificate_type,
                    'issueDateRaw'       => $cert->issue_date ? $cert->issue_date->format('Y-m-d') : '',
                    'expirationDateRaw'  => $cert->expiration_date ? $cert->expiration_date->format('Y-m-d') : '',
                ];
            }))"
        >
            <div class="overflow-hidden rounded-xl border border-grayTheme-border">
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-grayTheme-border bg-grayTheme-light">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-grayTheme-medium">Program / Qualification Title</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-grayTheme-medium">TESDA Classification</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-grayTheme-medium">Cert. No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-grayTheme-medium">File</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-grayTheme-medium">Expires</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-grayTheme-medium">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-grayTheme-border bg-white" x-ref="list">
                        <template x-if="items.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-grayTheme-light">
                                            <svg class="h-6 w-6 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-grayTheme-dark">No certificates found</p>
                                        <p class="text-xs text-grayTheme-medium">Add your first TESDA certificate using the form above.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-for="cert in items" :key="cert.id">
                            <tr class="cursor-pointer transition hover:bg-grayTheme-light/60" @click="window.location.href = cert.showUrl">
                                <td class="px-4 py-3 font-medium text-grayTheme-dark" x-text="cert.qualification"></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-semibold text-primary" x-text="cert.type"></span>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-grayTheme-medium" x-text="cert.certificateNumber || '\u2014'"></td>
                                <td class="px-4 py-3">
                                    <a
                                        x-show="cert.hasFile"
                                        :href="cert.showUrl"
                                        @click.stop
                                        class="inline-flex items-center gap-1 rounded-full bg-primary-soft px-2.5 py-0.5 text-xs font-semibold text-primary transition hover:bg-primary hover:text-white"
                                    >
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View
                                    </a>
                                    <span x-show="!cert.hasFile" class="inline-flex items-center gap-1 text-xs text-grayTheme-medium">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        No file
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-grayTheme-medium" x-text="cert.expirationDate"></td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        :class="{
                                            'bg-success-soft text-success': cert.status === 'valid',
                                            'bg-warning-soft text-warning': cert.status === 'expiring',
                                            'bg-danger-soft text-danger': cert.status === 'expired',
                                            'bg-grayTheme-hover text-grayTheme-medium': !['valid', 'expiring', 'expired'].includes(cert.status)
                                        }"
                                    >
                                        <svg x-show="cert.status === 'valid'" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <svg x-show="cert.status === 'expiring'" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <svg x-show="cert.status === 'expired'" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span x-text="cert.statusLabel"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-danger transition hover:bg-danger-soft focus:outline-none"
                                        @click.stop="askDelete(cert.deleteUrl, cert.qualification)"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                </div>{{-- /overflow-x-auto --}}
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

    {{-- Hidden delete form --}}
    <form x-ref="certDeleteForm" method="post" :action="pendingDeleteUrl" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    {{-- Confirmation Modal --}}
    <div
        x-cloak
        x-show="confirmOpen"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4"
        @keydown.escape.window="confirmOpen = false"
        @click.self="confirmOpen = false"
    >
        <div
            class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
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
                <button type="button" class="btn-danger gap-2" @click="$refs.certDeleteForm.submit()">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </div>
        </div>
    </div>

