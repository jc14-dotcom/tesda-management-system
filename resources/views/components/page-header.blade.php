@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
])

<section {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-3xl border border-primary/20 bg-gradient-to-br from-primary to-primary-hover px-6 py-6 text-white shadow-card sm:px-8']) }}>
    <div class="relative z-10 flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-2xl">
            @if ($eyebrow)
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/70">{{ $eyebrow }}</p>
            @endif
            <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-2 text-sm text-white/80 sm:text-base">{{ $subtitle }}</p>
            @endif
        </div>
        @if (isset($actions))
            <div class="flex items-center gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>
    <div class="absolute right-0 top-0 h-40 w-40 translate-x-1/3 -translate-y-1/3 rounded-full bg-accent/25 blur-3xl"></div>
    <div class="absolute bottom-0 left-6 h-24 w-24 translate-y-1/3 rounded-full bg-white/10 blur-2xl"></div>
</section>
