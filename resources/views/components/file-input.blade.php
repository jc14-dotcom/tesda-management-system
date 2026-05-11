@props([
    'name',
    'id' => null,
    'accept' => null,
    'required' => false,
    'help' => null,
    'placeholder' => 'Choose file',
])

@php
    $inputId = $id ?? $name;
@endphp

<div class="space-y-2" x-data="{ fileName: @js($placeholder) }">
    <input
        id="{{ $inputId }}"
        name="{{ $name }}"
        type="file"
        @if ($accept)
            accept="{{ $accept }}"
        @endif
        @required($required)
        class="sr-only"
        x-ref="input"
        x-on:change="fileName = $event.target.files && $event.target.files.length ? $event.target.files[0].name : @js($placeholder)"
    />

    <label
        for="{{ $inputId }}"
        class="flex cursor-pointer items-center justify-between gap-4 rounded-card border border-dashed border-primary/25 bg-primary-soft/40 px-4 py-4 transition duration-250 hover:border-primary hover:bg-primary-soft"
    >
        <span class="min-w-0">
            <span class="block text-sm font-semibold text-grayTheme-dark">{{ $slot }}</span>
            @if ($help)
                <span class="mt-1 block text-xs text-grayTheme-medium">{{ $help }}</span>
            @endif
        </span>

        <span class="inline-flex shrink-0 items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-primary shadow-card">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" aria-hidden="true">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <path d="m7 10 5-5 5 5" />
                <path d="M12 5v12" />
            </svg>
            <span class="max-w-[10rem] truncate" x-text="fileName"></span>
        </span>
    </label>
</div>