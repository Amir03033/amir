@props(['project', 'index' => 1, 'check' => null])

@php
    $tags = $project->tagList();
    $host = $project->hasDemo() ? parse_url($project->demo_url, PHP_URL_HOST) : null;
@endphp

<article data-reveal data-tilt data-project data-tags="{{ strtolower(implode('|', $tags)) }}"
         class="panel spotlight group flex flex-col overflow-hidden {{ $project->featured ? 'md:col-span-2 md:flex-row' : '' }}">
    <a href="{{ route('project.show', $project) }}" class="absolute inset-0 z-10 rounded-[inherit]" aria-label="{{ __('messages.portfolio.read_case') }}: {{ $project->getTranslation('title') }}"></a>

    <div class="relative overflow-hidden bg-ink-panel2 {{ $project->featured ? 'aspect-[16/10] md:aspect-auto md:w-[58%] md:border-r' : 'aspect-[16/10] border-b' }} border-ink-line">
        <img src="{{ $project->coverUrl() }}" alt="" loading="lazy" decoding="async"
             style="view-transition-name: project-{{ $project->id }}"
             class="absolute inset-0 h-full w-full object-cover object-top transition-transform duration-700 ease-out group-hover:scale-[1.05]">
        <div class="absolute inset-0 bg-gradient-to-t from-ink-panel/90 via-transparent to-transparent"></div>
        <span class="absolute left-3 top-3 rounded-lg border border-ink-line bg-ink-bg/80 px-2 py-0.5 text-[10px] font-semibold tabular-nums text-ink-soft backdrop-blur">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }}</span>
        @if($check)
            <span class="absolute right-3 top-3 flex items-center gap-1.5 rounded-lg border bg-ink-bg/80 px-2 py-0.5 text-[10px] font-semibold backdrop-blur {{ $check->ok ? 'border-scope-green/30 text-scope-green' : 'border-scope-red/30 text-scope-red' }}">
                <span class="dot h-1.5 w-1.5 {{ $check->ok ? 'bg-scope-green' : 'bg-scope-red' }}"></span>
                {{ $check->ok ? 'online' : 'offline' }}@if($check->response_ms !== null) · {{ $check->response_ms }} ms @endif
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5 md:p-6">
        @if($project->featured)
            <p class="label mb-3 text-scope-accent">★ {{ __('messages.serverscope.label') }}</p>
        @endif
        <h3 class="text-lg font-semibold tracking-tight {{ $project->featured ? 'md:text-2xl' : '' }}">{{ $project->getTranslation('title') }}</h3>
        <p class="mt-2 flex-1 text-sm leading-relaxed text-ink-soft">{{ $project->excerpt($project->featured ? 260 : 140) }}</p>
        <div class="mt-4 flex flex-wrap gap-1.5">
            @foreach($tags as $tag)
                <span class="chip">{{ $tag }}</span>
            @endforeach
        </div>
        <div class="mt-5 flex items-center justify-between gap-3 border-t border-ink-line pt-4 text-xs">
            <span class="font-semibold text-scope-accent">{{ __('messages.portfolio.read_case') }} <span class="inline-block transition-transform duration-300 group-hover:translate-x-1">→</span></span>
            @if($host)
                <a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" class="relative z-20 truncate text-ink-muted transition-colors hover:text-ink-text">{{ $host }} ↗</a>
            @endif
        </div>
    </div>
</article>
