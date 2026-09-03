@props(['id', 'title' => null, 'intro' => null, 'bg' => 'normal'])

<section
    id="{{ $id }}"
    {{ $attributes->merge([
        'class' => 'py-24 px-4 relative scroll-mt-20 ' .
        ($bg === 'card' ? 'bg-cyber-card/30 border-y border-slate-800/60' : '')
    ]) }}
>
    <div class="max-w-5xl mx-auto">
        @if($title)
            <div class="space-y-3 mb-12">
                <h2 class="text-3xl font-bold tracking-tight text-white">{{ $title }}</h2>
                @if($intro)
                    <p class="text-slate-400 text-sm md:text-base max-w-2xl">{{ $intro }}</p>
                @endif
                <div class="w-12 h-[2px] bg-gradient-to-r from-cyan-500 to-transparent"></div>
            </div>
        @endif

        {{ $slot }}
    </div>
</section>
