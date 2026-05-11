<section x-data="{
    modalOpen: false,
    documentType: @js(old('type', 'cv')),
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
}">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Documents') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Upload CVs, certificate files, and other supporting documents.') }}
        </p>
    </header>

    <form method="post" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <x-input-label for="document_name" :value="__('Document Name')" />
                <x-text-input id="document_name" name="document_name" type="text" class="mt-1 block w-full" :value="old('document_name')" required placeholder="e.g. Driver’s License, Diploma, NBI Clearance" />
                <x-input-error class="mt-2" :messages="$errors->get('document_name')" />
            </div>

            <div>
                <x-input-label for="type" :value="__('Document Type')" />
                <select id="type" name="type" class="mt-1 form-input" x-model="documentType" required>
                    <option value="cv" @selected(old('type') === 'cv')>CV</option>
                    <option value="certificate" @selected(old('type') === 'certificate')>Certificate File</option>
                    <option value="other" @selected(old('type') === 'other')>Other</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('type')" />
            </div>

            <div x-show="documentType === 'certificate'" x-cloak>
                <x-input-label for="certificate_id" :value="__('Certificate')" />
                <select id="certificate_id" name="certificate_id" class="mt-1 form-input">
                    <option value="">None</option>
                    @foreach (($certificatesSelect ?? $certificates) as $certificate)
                        <option value="{{ $certificate->id }}" @selected(old('certificate_id') == $certificate->id)>
                            {{ $certificate->certificate_name }}{{ $certificate->qualification_title ? ' - '.$certificate->qualification_title : '' }} ({{ $certificate->certificate_type_label }})
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('certificate_id')" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="file" :value="__('File')" />
                <input id="file" name="file" type="file" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('file')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Upload Document') }}</x-primary-button>

            @if (session('status') === 'document-uploaded')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Uploaded.') }}</p>
            @endif
        </div>
    </form>

    <div class="mt-6">
        <h3 class="text-sm font-semibold text-gray-700">Uploaded Documents</h3>
        <div class="mt-3 space-y-3">
            @forelse ($documents as $document)
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <button
                        type="button"
                        class="block w-full text-left"
                        @click="openDocument({
                            title: @js($document->document_name ?? $document->original_name),
                            type: @js(strtoupper($document->type)),
                            previewUrl: @js(route('documents.preview', $document)),
                            downloadUrl: @js(route('documents.download', $document)),
                            originalName: @js($document->original_name),
                            viewUrl: @js(route('documents.view', $document)),
                        })"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ strtoupper($document->type) }}</div>
                                <div class="mt-1 truncate text-base font-semibold text-gray-900">{{ $document->document_name ?? $document->original_name }}</div>
                                <div class="mt-1 text-sm text-gray-500">Click to view or print</div>
                            </div>
                            <div class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">Open</div>
                        </div>
                    </button>

                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <button type="button" class="text-sm font-semibold text-primary hover:text-blue-800" @click="openDocument({
                            title: @js($document->document_name ?? $document->original_name),
                            type: @js(strtoupper($document->type)),
                            previewUrl: @js(route('documents.preview', $document)),
                            downloadUrl: @js(route('documents.download', $document)),
                            originalName: @js($document->original_name),
                            viewUrl: @js(route('documents.view', $document)),
                        })">
                            View
                        </button>
                        <a class="text-sm font-semibold text-primary hover:text-blue-800" href="{{ route('documents.download', $document) }}">
                            Download
                        </a>

                        <form method="post" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('Delete this document?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-800">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No documents uploaded.</p>
            @endforelse
        </div>

        @if (method_exists($documents, 'links'))
            <div class="mt-4">
                {{ $documents->links() }}
            </div>
        @endif

        @if (session('status') === 'document-deleted')
            <p class="mt-4 text-sm text-gray-600">Document deleted.</p>
        @endif
    </div>

    <div
        x-cloak
        x-show="modalOpen"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 px-4 py-6"
        @keydown.escape.window="closeDocument()"
        @click.self="closeDocument()"
    >
        <div class="w-full max-w-6xl overflow-hidden rounded-3xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                <div class="min-w-0">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500" x-text="selectedDocument.type"></div>
                    <h3 class="mt-1 truncate text-lg font-semibold text-slate-900" x-text="selectedDocument.title"></h3>
                    <p class="mt-1 text-sm text-slate-500">Preview, print, or download this file.</p>
                </div>
                <button type="button" class="rounded-full bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200" @click="closeDocument()">
                    Close
                </button>
            </div>

            <div class="grid gap-0 lg:grid-cols-[1fr_280px]">
                <div class="bg-slate-50">
                    <iframe
                        x-ref="previewFrame"
                        :src="selectedDocument.previewUrl"
                        class="h-[75vh] w-full"
                        :title="selectedDocument.title"
                    ></iframe>
                </div>

                <aside class="border-t border-slate-200 bg-white p-5 lg:border-t-0 lg:border-l">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="text-sm font-semibold text-slate-700">Actions</div>
                        <div class="mt-4 space-y-3">
                            <button type="button" class="w-full rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover" @click="printDocument()">
                                Print
                            </button>
                            <a :href="selectedDocument.downloadUrl" class="block w-full rounded-full border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                Download
                            </a>
                            <a :href="selectedDocument.viewUrl" class="block w-full rounded-full border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                Open full page
                            </a>
                        </div>

                        <p class="mt-4 text-sm text-slate-500">
                            If the file does not preview in-browser, use Download or open the full page.
                        </p>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>
