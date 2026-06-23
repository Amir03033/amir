@props(['id', 'cyberTitle' => null, 'bg' => 'normal'])

<section
        id="{{ $id }}"
        {{ $attributes->merge([
            'class' => 'py-24 px-4 relative scroll-mt-16 transition-all duration-700 ' .
            ($bg === 'card' ? 'bg-cyber-card/30 border-y border-slate-900/50 backdrop-blur-sm' : '')
        ]) }}
>
    <div class="max-w-5xl mx-auto">
        @if($cyberTitle)
            <div class="space-y-2 mb-12">
                <h2 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-slate-100 to-slate-400 bg-clip-text text-transparent flex items-center gap-3">
                    <span class="text-cyan-500 font-mono text-xl animate-pulse">//</span>
                    <span x-text="{{ $cyberTitle }}"></span>
                </h2>
                <div class="w-12 h-[2px] bg-gradient-to-r from-cyan-500 to-transparent"></div>
            </div>
        @endif

        {{ $slot }}
    </div>
</section>