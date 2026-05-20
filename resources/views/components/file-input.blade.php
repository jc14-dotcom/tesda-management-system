@props([
    'name',
    'id'       => null,
    'accept'   => null,
    'required' => false,
    'help'     => null,
])

@php
    $inputId = $id ?? $name;
@endphp

<div
    x-data="{
        fileName:   '',
        fileSize:   '',
        isImage:    false,
        previewUrl: '',
        dragging:   false,

        handleFile(file) {
            if (!file) return;
            this.fileName = file.name;
            this.fileSize = file.size < 1048576
                ? (file.size / 1024).toFixed(1) + ' KB'
                : (file.size / 1048576).toFixed(1) + ' MB';
            this.isImage = file.type.startsWith('image/');
            if (this.isImage) {
                const reader = new FileReader();
                reader.onload = (e) => { this.previewUrl = e.target.result; };
                reader.readAsDataURL(file);
            } else {
                this.previewUrl = '';
            }
        },

        onDrop(e) {
            this.dragging = false;
            const file = e.dataTransfer.files[0];
            if (!file) return;
            const dt = new DataTransfer();
            dt.items.add(file);
            this.$refs.input.files = dt.files;
            this.handleFile(file);
        },

        clearFile() {
            this.fileName   = '';
            this.fileSize   = '';
            this.isImage    = false;
            this.previewUrl = '';
            this.$refs.input.value = '';
        },
    }"
    data-file-input
>
    {{-- Hidden file input --}}
    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="file"
        @if ($accept) accept="{{ $accept }}" @endif
        @required($required)
        class="sr-only"
        x-ref="input"
        @change="handleFile($event.target.files[0])"
    />

    {{-- Drop zone --}}
    <label
        for="{{ $inputId }}"
        class="flex cursor-pointer flex-col items-center gap-3 rounded-xl border-2 border-dashed px-6 py-8 text-center transition duration-200"
        :class="{
            'border-primary/30 bg-primary-soft/30 hover:border-primary hover:bg-primary-soft/60': !fileName && !dragging,
            'border-primary bg-primary-soft/70 scale-[1.01] shadow-sm':                           dragging,
            'border-success/40 bg-success-soft/20 hover:border-success/70':                       fileName && !dragging,
        }"
        @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        @drop.prevent="onDrop($event)"
    >
        {{-- Upload / check icon --}}
        <span
            class="flex h-12 w-12 items-center justify-center rounded-2xl transition"
            :class="fileName ? 'bg-success/10 text-success' : 'bg-white text-primary shadow-sm'"
        >
            <svg x-show="!fileName" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            <svg x-show="fileName" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
        </span>

        {{-- Idle state text --}}
        <span x-show="!fileName">
            <span class="block text-sm font-semibold text-grayTheme-dark">
                {{ $slot }}@if($required)<span class="ml-0.5 text-red-500" aria-hidden="true">*</span>@endif
            </span>
            <span class="mt-1 block text-xs text-grayTheme-medium">
                @if ($help){{ $help }}@else Drag &amp; drop, or <span class="font-medium text-primary">browse</span>@endif
            </span>
        </span>

        {{-- Selected state text --}}
        <span x-show="fileName">
            <span class="block text-sm font-semibold text-success">File ready</span>
            <span class="mt-1 block text-xs text-grayTheme-medium">Click or drag to replace</span>
        </span>
    </label>

    {{-- Preview card (shown after file is selected) --}}
    <div
        x-show="fileName"
        x-transition:enter="transition duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="mt-2 flex items-center gap-3 rounded-xl border border-grayTheme-border bg-white p-3 shadow-sm"
    >
        {{-- Thumbnail or file icon --}}
        <div class="relative h-16 w-16 shrink-0 overflow-hidden rounded-lg border border-grayTheme-border bg-grayTheme-light">
            {{-- Image preview --}}
            <img
                x-show="isImage && previewUrl"
                :src="previewUrl"
                class="h-full w-full object-cover"
                alt=""
            />
            {{-- Non-image file icon --}}
            <div
                x-show="!isImage || !previewUrl"
                class="flex h-full w-full items-center justify-center"
            >
                <svg class="h-8 w-8 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
            </div>
        </div>

        {{-- File info --}}
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold text-grayTheme-dark" x-text="fileName"></p>
            <p class="mt-0.5 text-xs text-grayTheme-medium" x-text="fileSize"></p>
            <p class="mt-1 text-xs font-medium text-primary">Click the zone above to change</p>
        </div>

        {{-- Remove button --}}
        <button
            type="button"
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-grayTheme-medium transition hover:bg-danger-soft hover:text-danger"
            @click.prevent="clearFile()"
            title="Remove file"
        >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>