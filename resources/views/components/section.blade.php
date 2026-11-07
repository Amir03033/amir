@props(['id', 'index' => null, 'label' => null, 'title' => null, 'intro' => null])

<section id="{{ $id }}" {{ $attributes->merge(['class' => 'relative scroll-mt-24 px-4 py-16 sm:py-24 md:py-32']) }}>
    <div class="mx-auto max-w-6xl">
        @if($title)
            <header class="mb-10 max-w-2xl md:mb-16">
                @if($label)
                    <p class="label mb-4 flex items-center gap-3" data-reveal>
                        @if($index)<span class="tabular-nums text-scope-accent">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }}</span><span class="h-px w-6 bg-ink-strong"></span>@endif
                        <span data-scramble>{{ $label }}</span>
                    </p>
                @endif
                <h2 data-split class="text-3xl font-bold tracking-[-.03em] text-ink-text md:text-5xl">{{ $title }}</h2>
                @if($intro)
                    <p data-reveal class="mt-5 text-base leading-relaxed text-ink-soft md:text-lg">{{ $intro }}</p>
                @endif
            </header>
        @endif

        {{ $slot }}
    </div>
</section>
