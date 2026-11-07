@extends('layouts.layout', [
    'title' => $project->getTranslation('title').' — '.config('site.name'),
    'description' => $project->excerpt(200),
    'ogImage' => route('og', ['type' => 'project', 'id' => $project->id, 'lang' => app()->getLocale()]),
])

@push('head')
    <script type="application/ld+json">{!! json_encode(array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'CreativeWork',
        'name' => $project->getTranslation('title'),
        'description' => $project->excerpt(300),
        'url' => url()->current(),
        'image' => $project->coverUrl(),
        'keywords' => implode(', ', $project->tagList()),
        'inLanguage' => app()->getLocale(),
        'sameAs' => $project->hasDemo() ? $project->demo_url : null,
        'author' => ['@type' => 'Person', 'name' => config('site.name'), 'url' => lroute('home')],
    ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('nav')
    <x-site-nav />
@endsection

@section('content')
    <article class="px-4 pb-24 pt-28 md:pt-36">
        <div class="mx-auto max-w-5xl">
            <nav data-reveal class="flex flex-wrap items-center gap-2 text-sm text-ink-muted" aria-label="Breadcrumb">
                <a href="{{ lroute('home') }}" class="hover:text-ink-text">{{ __('messages.hero.crumb') }}</a>
                <span>›</span>
                <a href="{{ lroute('home') }}#work" class="hover:text-ink-text">{{ __('messages.nav.portfolio') }}</a>
                <span>›</span>
                <span class="text-ink-soft">{{ $project->getTranslation('title') }}</span>
            </nav>

            <header class="mt-8 grid gap-8 md:grid-cols-[1fr_auto] md:items-end">
                <div>
                    <h1 data-split class="text-4xl font-semibold tracking-tight md:text-6xl">{{ $project->getTranslation('title') }}</h1>
                    <div data-reveal class="mt-5 flex flex-wrap gap-1.5">
                        @foreach($project->tagList() as $tag)
                            <span class="chip">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                <div data-reveal class="flex flex-wrap gap-3">
                    @if($project->hasDemo())
                        <a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" data-magnetic class="btn-primary">{{ __('messages.portfolio.live') }} ↗</a>
                    @endif
                    @if($project->hasGithub())
                        <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" data-magnetic class="btn-secondary">{{ __('messages.portfolio.github') }}</a>
                    @endif
                </div>
            </header>

            <div class="panel mt-10 overflow-hidden">
                <div class="panel-head">
                    <span class="flex items-center gap-1.5" aria-hidden="true">
                        <span class="h-2.5 w-2.5 rounded-full bg-ink-strong"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-ink-strong"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-ink-strong"></span>
                    </span>
                    <span class="truncate normal-case tracking-normal">{{ $project->hasDemo() ? parse_url($project->demo_url, PHP_URL_HOST) : $project->getTranslation('title') }}</span>
                    <span class="w-10"></span>
                </div>
                <img src="{{ $project->coverUrl() }}" alt="{{ $project->getTranslation('title') }}"
                     style="view-transition-name: project-{{ $project->id }}"
                     class="aspect-[16/9] w-full object-cover object-top">
            </div>

            <div class="mt-14 grid gap-12 md:grid-cols-[220px_1fr]">
                <aside class="space-y-6 md:sticky md:top-28 md:h-fit">
                    <div>
                        <p class="label mb-2">{{ __('messages.portfolio.stack') }}</p>
                        <p class="text-sm leading-relaxed text-ink-soft">{{ implode(', ', $project->tagList()) }}</p>
                    </div>
                    @if($uptime !== null)
                        <div>
                            <p class="label mb-2">{{ __('messages.status.uptime_30') }}</p>
                            <p class="flex items-baseline gap-2"><span class="text-2xl font-bold tabular-nums">{{ rtrim(rtrim(number_format($uptime, 2), '0'), '.') }}%</span></p>
                            <x-uptime-bar :days="$uptimeDays" class="mt-2 !h-6 !gap-[2px]" />
                            @if($measuredSince)
                                <p class="mt-2 text-[11px] text-ink-muted">{{ __('messages.status.since', ['date' => $measuredSince->translatedFormat('j M Y')]) }}</p>
                            @endif
                            <a href="{{ lroute('status') }}" class="mt-1 inline-block text-[11px] text-scope-accent hover:underline">{{ __('messages.status.full_page') }} →</a>
                        </div>
                    @endif
                    @if($project->hasDemo())
                        <div>
                            <p class="label mb-2">URL</p>
                            <a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" class="break-all text-xs text-ink-soft hover:text-scope-accent">{{ parse_url($project->demo_url, PHP_URL_HOST) }} ↗</a>
                        </div>
                    @endif
                </aside>

                <div class="max-w-2xl space-y-12">
                    <div data-reveal class="whitespace-pre-line text-base leading-relaxed text-ink-soft md:text-lg">{{ $project->getTranslation('description') }}</div>

                    @if($blocks = $project->caseBlocks())
                        <ol class="relative space-y-10 pl-8" data-reveal-group>
                            <span data-draw-line class="absolute bottom-2 left-[3px] top-2 w-px origin-top bg-gradient-to-b from-scope-accent via-scope-accent to-scope-green" aria-hidden="true"></span>
                            @foreach($blocks as $key => $text)
                                <li data-reveal class="relative">
                                    <span class="absolute -left-8 top-1.5 h-[7px] w-[7px] rounded-full {{ $loop->last ? 'bg-scope-green shadow-[0_0_0_4px_rgb(74_222_128/.2)]' : 'bg-scope-accent' }}"></span>
                                    <p class="label text-scope-accent">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} · {{ __('messages.case.'.$key) }}</p>
                                    <p class="mt-3 whitespace-pre-line text-base leading-relaxed text-ink-soft md:text-lg">{{ $text }}</p>
                                </li>
                            @endforeach
                        </ol>
                    @endif

                    @foreach($project->testimonials()->visible()->get() as $t)
                        <figure data-reveal class="panel panel-glow p-6">
                            <blockquote class="text-lg leading-relaxed">“{{ $t->getTranslation('quote') }}”</blockquote>
                            <figcaption class="mt-4 text-sm text-ink-muted"><span class="font-semibold text-ink-text">{{ $t->name }}</span>@if($t->role || $t->company) · {{ collect([$t->role, $t->company])->filter()->implode(', ') }}@endif</figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>

            @if($gallery = $project->galleryUrls())
                <section class="mt-20" x-data="{ open: null, items: @js($gallery) }" @keydown.escape.window="open = null" @keydown.arrow-right.window="open !== null && (open = (open + 1) % items.length)" @keydown.arrow-left.window="open !== null && (open = (open - 1 + items.length) % items.length)">
                    <h2 data-reveal class="label mb-6">{{ __('messages.case.gallery') }}</h2>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-reveal-group>
                        @foreach($gallery as $i => $src)
                            <button type="button" data-reveal @click="open = {{ $i }}" class="panel group overflow-hidden">
                                <img src="{{ $src }}" alt="{{ $project->getTranslation('title') }} — {{ $i + 1 }}" loading="lazy" decoding="async" class="aspect-[16/10] w-full object-cover object-top transition-transform duration-500 group-hover:scale-[1.04]">
                            </button>
                        @endforeach
                    </div>
                    <div x-cloak x-show="open !== null" x-transition.opacity class="fixed inset-0 z-[85] flex items-center justify-center bg-black/85 p-4 backdrop-blur-sm" @click.self="open = null" role="dialog" aria-modal="true">
                        <img :src="items[open]" alt="" class="max-h-[85vh] max-w-full rounded-panel border border-ink-line object-contain">
                        <button type="button" @click="open = null" class="absolute right-4 top-4 rounded-xl border border-ink-line bg-ink-panel px-3 py-1.5 text-sm" aria-label="{{ __('messages.nav.close') }}">esc</button>
                    </div>
                </section>
            @endif

            <div class="mt-20 grid gap-4 border-t border-ink-line pt-10 md:grid-cols-2">
                <a href="{{ lroute('home') }}#work" class="panel group flex items-center gap-3 p-5 text-sm font-semibold transition-colors hover:border-ink-strong">
                    <span class="transition-transform group-hover:-translate-x-1">←</span> {{ __('messages.portfolio.more') }}
                </a>
                @if($next)
                    <a href="{{ lroute('project.show', $next) }}" class="panel spotlight group flex items-center gap-4 overflow-hidden p-3 transition-colors hover:border-ink-strong">
                        <img src="{{ $next->coverUrl() }}" alt="" loading="lazy" class="h-14 w-20 rounded-xl object-cover">
                        <span class="flex-1">
                            <span class="block text-[11px] uppercase tracking-[.18em] text-ink-muted">Next</span>
                            <span class="block font-semibold">{{ $next->getTranslation('title') }}</span>
                        </span>
                        <span class="pr-2 transition-transform group-hover:translate-x-1">→</span>
                    </a>
                @endif
            </div>
        </div>
    </article>
@endsection
