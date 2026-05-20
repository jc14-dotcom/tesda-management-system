<x-app-layout>
    <div class="py-12" x-data="{
        editOpen: false,
        confirmDelete: false,
    }">
        <div class="page-container space-y-6">

            {{-- Page Header --}}
            <x-page-header
                title="{{ $certificate->certificate_name }}"
                subtitle="Certificate details and attached file."
                eyebrow="My Certificates"
            >
                <x-slot:actions>
                    <a href="{{ route('account.certificates') }}"
                       class="rounded-full border border-white/30 px-3 py-1 text-sm font-semibold text-white/90 transition hover:text-white">
                        ← Back
                    </a>
                </x-slot:actions>
            </x-page-header>

            {{-- Bridge flash to toast --}}
            @if (session('status') === 'certificate-updated')
            <script data-turbo-eval="true">window.dispatchEvent(new CustomEvent('show-toast',{detail:{type:'success',title:'Certificate Updated',message:'Your certificate details have been saved successfully.'}}));</script>
            @endif

            {{-- Expired alert --}}
            @if ($certificate->status === 'expired')
                <div class="flex items-center gap-3 rounded-xl bg-danger-soft px-5 py-3 text-sm font-medium text-danger">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    This certificate has expired. Click <strong>Edit Certificate</strong> on the right to update and renew it.
                </div>
            @endif

            <div class="grid gap-6 md:grid-cols-3">

                {{-- ── Left column (2/3) ─────────────────────────────────── --}}
                <div class="space-y-6 md:col-span-2">

                    {{-- Certificate Information --}}
                    <div class="surface p-6">
                        <h3 class="mb-5 text-base font-semibold text-grayTheme-dark">Certificate Information</h3>
                        <dl class="grid gap-x-8 gap-y-5 md:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Certificate Name</dt>
                                <dd class="mt-1.5 text-base font-medium text-grayTheme-dark">{{ $certificate->certificate_name ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Certificate Number</dt>
                                <dd class="mt-1.5 font-mono text-base font-medium text-grayTheme-dark">{{ $certificate->certificate_number ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Classification / Level</dt>
                                <dd class="mt-1.5 text-base font-medium text-grayTheme-dark">{{ $certificate->certificate_type_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Program / Qualification Title</dt>
                                <dd class="mt-1.5 text-base font-medium text-grayTheme-dark">{{ $certificate->qualification_title ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Issued By</dt>
                                <dd class="mt-1.5 text-base font-medium text-grayTheme-dark">{{ $certificate->issued_by ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Issue Date</dt>
                                <dd class="mt-1.5 text-base font-medium text-grayTheme-dark">
                                    {{ $certificate->issue_date?->format('M d, Y') ?? '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Expiration Date</dt>
                                <dd class="mt-1.5 text-base font-medium text-grayTheme-dark">
                                    {{ $certificate->expiration_date?->format('M d, Y') ?? '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Certificate Status</dt>
                                <dd class="mt-1.5">
                                    @php
                                        $statusTone = match ($certificate->status) {
                                            'valid'    => 'bg-success-soft text-success',
                                            'expiring' => 'bg-warning-soft text-warning',
                                            'expired'  => 'bg-danger-soft text-danger',
                                            default    => 'bg-grayTheme-light text-grayTheme-medium',
                                        };
                                        $statusIcon = match ($certificate->status) {
                                            'valid'    => 'M5 13l4 4L19 7',
                                            'expiring' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                            'expired'  => 'M6 18L18 6M6 6l12 12',
                                            default    => '',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold {{ $statusTone }}">
                                        @if ($statusIcon)
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $statusIcon }}"/>
                                            </svg>
                                        @endif
                                        {{ ucfirst($certificate->status ?? 'unknown') }}
                                    </span>
                                </dd>
                            </div>
                            @if ($certificate->remarks)
                                <div class="md:col-span-2">
                                    <dt class="text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Remarks</dt>
                                    <dd class="mt-1.5 text-sm text-grayTheme-dark">{{ $certificate->remarks }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    {{-- Certificate File --}}
                    @php
                        $doc = $certificate->documents->first();
                        $isPdf   = $doc && str_contains(strtolower($doc->mime_type ?? ''), 'pdf');
                        $isImage = $doc && str_contains(strtolower($doc->mime_type ?? ''), 'image');
                        $canPreview = $isPdf || $isImage;
                    @endphp

                    <div class="surface p-6">
                        <h3 class="mb-4 text-base font-semibold text-grayTheme-dark">Certificate File</h3>

                        @if ($doc)
                            {{-- File meta row --}}
                            <div class="flex items-center justify-between gap-4 rounded-xl border border-grayTheme-border bg-grayTheme-light px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
                                        @if ($isPdf)
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        @elseif ($isImage)
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @else
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-grayTheme-dark">{{ $doc->document_name ?: $doc->original_name }}</div>
                                        <div class="text-xs text-grayTheme-medium">
                                            {{ ucfirst($doc->type ?? 'Document') }}
                                            @if ($doc->size) · {{ number_format($doc->size / 1024, 1) }} KB @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <a href="{{ route('documents.download', $doc) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-grayTheme-border bg-white px-3 py-1.5 text-xs font-semibold text-grayTheme-dark shadow-sm transition hover:bg-grayTheme-light">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Download
                                    </a>
                                </div>
                            </div>

                            {{-- Embedded preview --}}
                            @if ($canPreview)
                                <div class="mt-4 overflow-hidden rounded-xl border border-grayTheme-border" style="height: clamp(500px, 75vh, 820px);">
                                    <iframe
                                        src="{{ route('documents.preview', $doc) }}"
                                        class="h-full w-full border-0"
                                        title="Certificate file preview"
                                    ></iframe>
                                </div>
                            @endif
                        @else
                            <div class="rounded-xl border border-dashed border-grayTheme-border bg-grayTheme-light py-12 text-center">
                                <svg class="mx-auto mb-3 h-10 w-10 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm font-medium text-grayTheme-dark">No file attached</p>
                                <p class="mt-1 text-xs text-grayTheme-medium">Use the Edit Certificate action to upload a file.</p>
                            </div>
                        @endif
                    </div>

                </div>

                {{-- ── Right column (1/3) ────────────────────────────────── --}}
                <div class="space-y-6">

                    {{-- Status card --}}
                    <div class="surface p-6">
                        <h3 class="mb-4 text-base font-semibold text-grayTheme-dark">Status</h3>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold {{ $statusTone }}">
                            @if ($statusIcon)
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $statusIcon }}"/>
                                </svg>
                            @endif
                            {{ ucfirst($certificate->status ?? 'unknown') }}
                        </span>

                        @if ($certificate->expiration_date)
                            @php $daysLeft = (int) now()->diffInDays($certificate->expiration_date, false); @endphp
                            <div class="mt-4 text-sm text-grayTheme-medium">
                                <span class="font-medium text-grayTheme-dark">Expires:</span>
                                {{ $certificate->expiration_date->format('M d, Y') }}
                            </div>
                            <div class="mt-1 text-sm">
                                @if ($daysLeft > 0)
                                    <span class="{{ $daysLeft <= 30 ? 'text-warning font-semibold' : 'text-grayTheme-medium' }}">
                                        {{ $daysLeft }} day{{ $daysLeft !== 1 ? 's' : '' }} remaining
                                    </span>
                                @elseif ($daysLeft === 0)
                                    <span class="font-semibold text-danger">Expires today</span>
                                @else
                                    <span class="font-semibold text-danger">Expired {{ abs($daysLeft) }} day{{ abs($daysLeft) !== 1 ? 's' : '' }} ago</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Actions card --}}
                    <div class="surface p-6">
                        <h3 class="mb-4 text-base font-semibold text-grayTheme-dark">Actions</h3>
                        <div class="space-y-3">
                            {{-- Edit --}}
                            <button
                                type="button"
                                @click="editOpen = true"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-90 active:scale-95"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit Certificate
                            </button>

                            {{-- Print --}}
                            <button
                                type="button"
                                onclick="window.print()"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-grayTheme-border bg-white px-4 py-2.5 text-sm font-semibold text-grayTheme-dark shadow-sm transition hover:bg-grayTheme-light"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Print Details
                            </button>

                            {{-- Delete --}}
                            <button
                                type="button"
                                @click="confirmDelete = true"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-danger/30 px-4 py-2.5 text-sm font-semibold text-danger transition hover:bg-danger-soft"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Delete Certificate
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ── Edit Certificate Modal ──────────────────────────────────── --}}
            <div
                x-cloak
                x-show="editOpen"
                x-transition.opacity
                class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-slate-950/60 px-4 py-8"
                @keydown.escape.window="editOpen = false"
                @click.self="editOpen = false"
            >
                <div
                    class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                >
                    {{-- Header --}}
                    <div class="flex items-center justify-between gap-4 border-b border-grayTheme-border px-6 py-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Edit Certificate</p>
                            <h3 class="mt-0.5 text-lg font-bold text-grayTheme-dark">{{ $certificate->certificate_name }}</h3>
                        </div>
                        <button type="button" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-grayTheme-medium transition hover:bg-grayTheme-light" @click="editOpen = false">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Expired notice --}}
                    @if ($certificate->status === 'expired')
                        <div class="mx-6 mt-4 flex items-center gap-2 rounded-lg bg-danger-soft px-4 py-2.5 text-sm text-danger">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                            This certificate is expired. Update the expiration date to renew it.
                        </div>
                    @endif

                    {{-- Form --}}
                    <form action="{{ route('certificates.update', $certificate) }}" method="POST" enctype="multipart/form-data" class="space-y-4 px-6 py-5"
                          x-data="{ certType: '{{ old('certificate_type', $certificate->certificate_type) }}' }">
                        @csrf
                        @method('PATCH')

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                @php
                                    $trainerTitles = array_values(array_filter((array) ($profile?->trainer_qualification_titles ?? [])));
                                    $assessorTitles = array_values(array_filter((array) ($profile?->assessor_qualification_titles ?? [])));
                                    $hasTitles = !empty($trainerTitles) || !empty($assessorTitles);
                                    $currentTitle = old('qualification_title', $certificate->qualification_title);
                                    $initIsOther = old('certificate_type', $certificate->certificate_type) === 'other';
                                @endphp
                                <x-input-label for="edit_qualification_title" :value="__('Program / Qualification Title')" :required="true" />
                                @if ($hasTitles)
                                    <select id="edit_qualification_title" name="qualification_title" class="mt-1 form-input w-full"
                                            x-show="certType !== 'other'" :disabled="certType === 'other'" :required="certType !== 'other'"
                                            style="{{ $initIsOther ? 'display:none' : '' }}">
                                        <option value="">Select a qualification title</option>
                                        @if (!empty($trainerTitles))
                                            <optgroup label="Trainer">
                                                @foreach ($trainerTitles as $title)
                                                    <option value="{{ $title }}" @selected($currentTitle === $title)>{{ $title }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        @if (!empty($assessorTitles))
                                            <optgroup label="Assessor">
                                                @foreach ($assessorTitles as $title)
                                                    <option value="{{ $title }}" @selected($currentTitle === $title)>{{ $title }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        {{-- Preserve existing title for non-Other types if not in profile --}}
                                        @if (!$initIsOther && $currentTitle && !in_array($currentTitle, array_merge($trainerTitles, $assessorTitles)))
                                            <optgroup label="Previously entered">
                                                <option value="{{ $currentTitle }}" selected>{{ $currentTitle }}</option>
                                            </optgroup>
                                        @endif
                                    </select>
                                    <input type="text" name="qualification_title" class="mt-1 form-input w-full"
                                           value="{{ $currentTitle }}"
                                           placeholder="e.g. First Aid Certificate, Driver's License"
                                           x-show="certType === 'other'" :disabled="certType !== 'other'" :required="certType === 'other'"
                                           style="{{ $initIsOther ? '' : 'display:none' }}">
                                    <p class="mt-1 text-xs text-grayTheme-medium"
                                       x-show="certType !== 'other'" style="{{ $initIsOther ? 'display:none' : '' }}">Titles are pulled from your <a href="{{ route('account.profile') }}#update-profile-details" class="font-medium text-primary hover:underline">profile settings</a>.</p>
                                    <p class="mt-1 text-xs text-grayTheme-medium"
                                       x-show="certType === 'other'" style="{{ $initIsOther ? '' : 'display:none' }}">Enter the name of the credential or qualification.</p>
                                @else
                                    <input type="text"
                                           id="edit_qualification_title"
                                           name="qualification_title"
                                           class="mt-1 form-input w-full"
                                           value="{{ $currentTitle }}"
                                           required>
                                @endif
                                <x-input-error class="mt-1" :messages="$errors->get('qualification_title')" />
                            </div>

                            <div>
                                <x-input-label for="edit_certificate_type" :value="__('Classification / Level')" :required="true" />
                                <select id="edit_certificate_type" name="certificate_type" class="mt-1 form-input" x-model="certType" required>
                                    @foreach (\App\Models\Certificate::TYPE_LABELS as $val => $label)
                                        <option value="{{ $val }}" @selected(old('certificate_type', $certificate->certificate_type) === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-1" :messages="$errors->get('certificate_type')" />
                            </div>

                            <div>
                                <x-input-label for="edit_certificate_number" :value="__('Certificate Number')" :required="true" />
                                <x-text-input
                                    id="edit_certificate_number"
                                    name="certificate_number"
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]+"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                    class="mt-1 block w-full"
                                    :value="old('certificate_number', $certificate->certificate_number)"
                                    required
                                />
                                <x-input-error class="mt-1" :messages="$errors->get('certificate_number')" />
                            </div>

                            <div>
                                <x-input-label for="edit_issued_by" :value="__('Issued By')" :required="true" />
                                <x-text-input
                                    id="edit_issued_by"
                                    name="issued_by"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('issued_by', $certificate->issued_by)"
                                    required
                                />
                                <x-input-error class="mt-1" :messages="$errors->get('issued_by')" />
                            </div>

                            <div>
                                <x-input-label for="edit_issue_date" :value="__('Issue Date')" />
                                <x-text-input
                                    id="edit_issue_date"
                                    name="issue_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    :value="old('issue_date', $certificate->issue_date?->format('Y-m-d'))"
                                />
                                <x-input-error class="mt-1" :messages="$errors->get('issue_date')" />
                            </div>

                            <div>
                                <x-input-label for="edit_expiration_date" :value="__('Expiration Date')" />
                                <x-text-input
                                    id="edit_expiration_date"
                                    name="expiration_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    :value="old('expiration_date', $certificate->expiration_date?->format('Y-m-d'))"
                                />
                                <x-input-error class="mt-1" :messages="$errors->get('expiration_date')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-file-input
                                    name="certificate_file"
                                    id="edit_certificate_file"
                                    :accept="'.pdf,.jpg,.jpeg,.png,.webp,.gif,.bmp,.tif,.tiff,image/*,application/pdf'"
                                    :required="false"
                                    :help="__('Leave blank to keep the existing file. Upload a new file to replace it.')"
                                >
                                    {{ __('Replace Certificate File (optional)') }}
                                </x-file-input>
                                <x-input-error class="mt-1" :messages="$errors->get('certificate_file')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="edit_remarks" :value="__('Remarks')" />
                            <textarea id="edit_remarks" name="remarks" class="mt-1 form-input w-full" rows="3">{{ old('remarks', $certificate->remarks) }}</textarea>
                            <x-input-error class="mt-1" :messages="$errors->get('remarks')" />
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-grayTheme-border pt-4">
                            <button type="button" class="btn-secondary" @click="editOpen = false">Cancel</button>
                            <button type="submit" class="inline-flex items-center gap-2 btn-primary">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Delete Confirmation Modal ────────────────────────────────── --}}
            <div
                x-cloak
                x-show="confirmDelete"
                x-transition.opacity
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4"
                @keydown.escape.window="confirmDelete = false"
                @click.self="confirmDelete = false"
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
                        <div>
                            <h3 class="text-base font-bold text-grayTheme-dark">Delete Certificate</h3>
                            <p class="mt-1 text-sm text-grayTheme-medium">
                                &ldquo;{{ $certificate->certificate_name }}&rdquo; and all its attached files will be permanently removed. This cannot be undone.
                            </p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" class="btn-secondary" @click="confirmDelete = false">Cancel</button>
                        <form action="{{ route('certificates.destroy', $certificate) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
