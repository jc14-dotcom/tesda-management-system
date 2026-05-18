<section x-data="{
    modalOpen: false,
    documentType: @js(old('type', 'cv')),
    confirmOpen: false,
    confirmTitle: '',
    confirmMessage: '',
    pendingDeleteUrl: '',
    askDelete(url, name) {
        this.pendingDeleteUrl = url;
        this.confirmTitle = 'Delete Document';
        this.confirmMessage = '\u201c' + name + '\u201d will be permanently deleted. This cannot be undone.';
        this.confirmOpen = true;
    },
    selectedDocument: {
        title: '',
        type: '',
        previewUrl: '',
        downloadUrl: '',
        originalName: ''
    },
    openDocument(document) {
        this.selectedDocument = document;
        this.modalOpen = true;
    },
    printDocument() {
        const frame = this.$refs.previewFrame;

        if (frame && frame.contentWindow) {
            frame.contentWindow.focus();
            frame.contentWindow.print();
            return;
        }

        window.print();
    },
    closeDocument() {
        this.modalOpen = false;
    }
}" @doc-confirm-delete.window="askDelete($event.detail.url, $event.detail.name)">

    {{-- Section header --}}
    <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-accent-soft">
            <svg class="h-5 w-5 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-grayTheme-dark">{{ __('Documents') }}</h2>
            <p class="text-sm text-grayTheme-medium">{{ __('Upload CVs, certificate files, and other supporting documents.') }}</p>
        </div>
    </div>

    {{-- Upload Form --}}
    <form method="post" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <x-input-label for="document_name" :value="__('Document Name')" :required="true" />
                <x-text-input id="document_name" name="document_name" type="text" class="mt-1 block w-full" :value="old('document_name')" required placeholder="e.g. Driver's License, Diploma, NBI Clearance" />
                <x-input-error class="mt-2" :messages="$errors->get('document_name')" />
            </div>

            <div>
                <x-input-label for="type" :value="__('Document Type')" :required="true" />
                <select id="type" name="type" class="mt-1 form-input" x-model="documentType" required>
                    <option value="cv" @selected(old('type') === 'cv')>CV / Resume</option>
                    <option value="training" @selected(old('type') === 'training')>Trainings/Workshop/Seminar Certificates</option>
                    <option value="other" @selected(old('type') === 'other')>Other</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('type')" />
            </div>

            <div class="md:col-span-2">
                <x-file-input
                    name="file"
                    id="file"
                    :required="false"
                    :help="__('Upload a CV, certificate file, or other supporting document.')"
                >
                    {{ __('File') }}
                </x-file-input>
                <x-input-error class="mt-2" :messages="$errors->get('file')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                {{ __('Upload Document') }}
            </x-primary-button>

            @if (session('status') === 'document-uploaded')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-success"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ __('Document uploaded.') }}
                </p>
            @endif
        </div>
    </form>

    {{-- Divider --}}
    <div class="my-8 border-t border-grayTheme-border"></div>

    {{-- Uploaded Documents --}}
    <div>
        <div class="flex items-center gap-2">
            <svg class="h-4 w-4 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
            <h3 class="text-sm font-semibold text-grayTheme-dark">Uploaded Documents</h3>
        </div>

        {{-- Filters --}}
        <form method="get" class="mt-3 rounded-xl border border-grayTheme-border bg-grayTheme-light px-4 py-3">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium" for="doc_type">Document Type</label>
                    <select id="doc_type" name="doc_type" class="mt-1 form-input">
                        <option value="all" @selected($docType === 'all')>All</option>
                        <option value="cv" @selected($docType === 'cv')>CV / Resume</option>
                        <option value="training" @selected($docType === 'training')>Training</option>
                        <option value="other" @selected($docType === 'other')>Other</option>
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
                nextUrl: @js($documents->nextPageUrl()), 
                partialParam: 'documents_partial' 
            })"
            x-init="items = @js($documents->map(function($doc) {
                return [
                    'id' => $doc->id,
                    'name' => $doc->document_name ?? $doc->original_name,
                    'originalName' => $doc->original_name,
                    'type' => strtoupper($doc->type),
                    'previewUrl' => route('documents.preview', $doc),
                    'downloadUrl' => route('documents.download', $doc),
                    'viewUrl' => route('documents.view', $doc),
                    'deleteUrl' => route('documents.destroy', $doc),
                ];
            }))"
        >
            <template x-if="items.length === 0">
                <div class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-grayTheme-border bg-grayTheme-light py-12 text-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-sm">
                        <svg class="h-7 w-7 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-grayTheme-dark">No documents uploaded</p>
                        <p class="mt-1 text-xs text-grayTheme-medium">Upload your CV, training certificates, or other documents above.</p>
                    </div>
                </div>
            </template>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3" x-ref="list" x-show="items.length > 0">
                <template x-for="doc in items" :key="doc.id">
                    <div class="group flex flex-col rounded-2xl border border-grayTheme-border bg-white shadow-sm transition hover:shadow-md">
                        {{-- Card top: click to open preview --}}
                        <button
                            type="button"
                            class="flex flex-1 items-start gap-3 p-4 text-left"
                            @click="openDocument({
                                title: doc.name,
                                type: doc.type,
                                previewUrl: doc.previewUrl,
                                downloadUrl: doc.downloadUrl,
                                originalName: doc.originalName,
                                viewUrl: doc.viewUrl,
                            })"
                        >
                            {{-- Type icon --}}
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                                :class="{
                                    'bg-primary-soft': doc.type === 'CV',
                                    'bg-accent-soft': doc.type === 'TRAINING',
                                    'bg-grayTheme-light': !['CV','TRAINING'].includes(doc.type)
                                }"
                            >
                                <svg x-show="doc.type === 'CV'" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <svg x-show="doc.type === 'TRAINING'" class="h-5 w-5 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <svg x-show="!['CV','TRAINING'].includes(doc.type)" class="h-5 w-5 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>

                            {{-- Name & type label --}}
                            <div class="min-w-0 flex-1">
                                <p
                                    class="text-[11px] font-semibold uppercase tracking-wide"
                                    :class="{
                                        'text-primary': doc.type === 'CV',
                                        'text-accent-active': doc.type === 'TRAINING',
                                        'text-grayTheme-medium': !['CV','TRAINING'].includes(doc.type)
                                    }"
                                    x-text="doc.type"></p>
                                <p class="mt-0.5 truncate text-sm font-semibold text-grayTheme-dark" x-text="doc.name"></p>
                                <p class="mt-0.5 text-xs text-grayTheme-medium">Click to view or print</p>
                            </div>

                            {{-- Chevron --}}
                            <svg class="mt-1 h-4 w-4 shrink-0 text-grayTheme-medium transition group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>

                        {{-- Card actions --}}
                        <div class="flex items-center gap-1 border-t border-grayTheme-border px-3 py-2">
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-primary transition hover:bg-primary-soft focus:outline-none"
                                @click="openDocument({
                                    title: doc.name,
                                    type: doc.type,
                                    previewUrl: doc.previewUrl,
                                    downloadUrl: doc.downloadUrl,
                                    originalName: doc.originalName,
                                    viewUrl: doc.viewUrl,
                                })"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View
                            </button>
                            <a
                                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-grayTheme-medium transition hover:bg-grayTheme-light hover:text-grayTheme-dark focus:outline-none"
                                :href="doc.downloadUrl"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download
                            </a>
                            <div class="ml-auto">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-danger transition hover:bg-danger-soft focus:outline-none"
                                    @click="askDelete(doc.deleteUrl, doc.name)"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Delete
                                </button>
                            </div>
                        </div>
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

    {{-- Document Preview Modal --}}
    <div
        x-cloak
        x-show="modalOpen"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 px-4 py-6"
        @keydown.escape.window="closeDocument()"
        @click.self="closeDocument()"
    >
        <div class="w-full max-w-6xl overflow-hidden rounded-3xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-grayTheme-border px-5 py-4 sm:px-6">
                <div class="min-w-0">
                    <div
                        class="text-xs font-semibold uppercase tracking-wide"
                        :class="{
                            'text-primary': selectedDocument.type === 'CV',
                            'text-accent-active': selectedDocument.type === 'TRAINING',
                            'text-grayTheme-medium': !['CV','TRAINING'].includes(selectedDocument.type)
                        }"
                        x-text="selectedDocument.type"
                    ></div>
                    <h3 class="mt-1 truncate text-lg font-semibold text-grayTheme-dark" x-text="selectedDocument.title"></h3>
                    <p class="mt-0.5 text-sm text-grayTheme-medium">Preview, print, or download this file.</p>
                </div>
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-full bg-grayTheme-light px-3 py-2 text-sm font-semibold text-grayTheme-dark transition hover:bg-grayTheme-hover focus:outline-none"
                    @click="closeDocument()"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Close
                </button>
            </div>

            <div class="grid gap-0 lg:grid-cols-[1fr_280px]">
                <div class="bg-grayTheme-light">
                    <iframe
                        x-ref="previewFrame"
                        :src="selectedDocument.previewUrl"
                        class="h-[75vh] w-full"
                        :title="selectedDocument.title"
                    ></iframe>
                </div>

                <aside class="border-t border-grayTheme-border bg-white p-5 lg:border-l lg:border-t-0">
                    <p class="text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">Actions</p>
                    <div class="mt-3 space-y-2">
                        <button
                            type="button"
                            class="btn-primary w-full gap-2"
                            @click="printDocument()"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print
                        </button>
                        <a
                            :href="selectedDocument.downloadUrl"
                            class="btn-secondary w-full gap-2"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download
                        </a>
                        <a
                            :href="selectedDocument.viewUrl"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-button border border-grayTheme-border bg-white px-4 py-2 text-sm font-semibold text-grayTheme-dark transition hover:bg-grayTheme-light focus:outline-none"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Open full page
                        </a>
                    </div>

                    <p class="mt-4 text-xs text-grayTheme-medium">
                        If the file does not preview in-browser, use Download or open the full page.
                    </p>
                </aside>
            </div>
        </div>
    </div>

    {{-- Hidden delete form --}}
    <form x-ref="docDeleteForm" method="post" :action="pendingDeleteUrl" class="hidden">
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
                <button type="button" class="btn-danger gap-2" @click="$refs.docDeleteForm.submit()">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </div>
        </div>
    </div>

</section>