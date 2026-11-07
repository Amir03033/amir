@extends('layouts.layout')

@section('nav')
    <x-site-nav :on-home="true" />
@endsection

@php
    $ss = __('messages.serverscope');
    // Screenshots and avatar are uploaded in Filament under "Foto's & media".
    $screenFor = fn (string $key) => \App\Models\Setting::media("ss-{$key}");
    $avatar = \App\Models\Setting::media('avatar', 'images/amir-avatar.webp');
    $liveProjects = $projects->filter->hasDemo()->count();
    $now = \App\Models\Setting::get('now.'.app()->getLocale()) ?: \App\Models\Setting::get('now.nl');
@endphp

@push('head')
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            array_filter([
                '@type' => 'Person',
                '@id' => lroute('home').'#person',
                'name' => config('site.name'),
                'url' => lroute('home'),
                'image' => $avatar,
                'jobTitle' => __('messages.home.subtitle'),
                'email' => 'mailto:'.config('site.email'),
                'address' => ['@type' => 'PostalAddress', 'addressRegion' => config('site.location'), 'addressCountry' => 'NL'],
                'sameAs' => array_values(array_filter([config('site.linkedin'), config('site.github')])),
                'knowsAbout' => collect(__('messages.skills.groups'))->pluck('items')->flatten()->values()->all(),
            ]),
            [
                '@type' => 'WebSite',
                'name' => config('site.name'),
                'url' => lroute('home'),
                'inLanguage' => app()->getLocale(),
                'author' => ['@id' => lroute('home').'#person'],
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    {{-- Hero: one message, two actions, and a compact live-status card (the full dashboard lives in "Live status"). --}}
    <section id="home" data-hero class="relative flex items-center overflow-hidden px-4 pb-12 pt-28 sm:min-h-[86svh] sm:pb-16 md:pt-32">
        <canvas data-field class="absolute inset-0 h-full w-full opacity-60 [mask-image:radial-gradient(ellipse_70%_60%_at_50%_40%,#000,transparent)]" aria-hidden="true"></canvas>

        <div class="relative mx-auto grid w-full max-w-6xl items-center gap-12 lg:grid-cols-[1.2fr_.8fr] lg:gap-14">
            <div>
                <div data-reveal class="flex items-center gap-3">
                    <img src="{{ $avatar }}" alt="{{ config('site.name') }}" width="44" height="44" class="h-11 w-11 rounded-full object-cover ring-2 ring-ink-line ring-offset-2 ring-offset-ink-bg">
                    <div class="leading-tight">
                        <p class="text-gradient text-[15px] font-semibold">{{ config('site.name') }}</p>
                        <p class="text-sm text-ink-muted">{{ __('messages.home.subtitle') }} · {{ config('site.location') }}</p>
                    </div>
                </div>

                <h1 data-split class="mt-7 text-[2.35rem] font-bold leading-[1.05] tracking-[-.04em] min-[400px]:text-[2.6rem] sm:text-6xl lg:text-6xl xl:text-7xl">{{ __('messages.home.headline') }}</h1>
                <p data-reveal class="mt-5 max-w-2xl text-base leading-relaxed text-ink-soft sm:text-lg">{{ __('messages.home.text') }}</p>

                <div data-reveal class="mt-8 flex flex-col gap-3 min-[420px]:flex-row min-[420px]:flex-wrap">
                    <a href="#serverscope" data-magnetic class="btn-primary">{{ __('messages.home.cta') }} <span aria-hidden="true">↓</span></a>
                    @if(config('site.booking_url'))
                        <a href="{{ config('site.booking_url') }}" target="_blank" rel="noopener" data-magnetic data-track="booking_click" data-track-label="hero" class="btn-secondary">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
                            {{ __('messages.booking.cta') }}
                        </a>
                    @else
                        <a href="#contact" data-magnetic class="btn-secondary">{{ __('messages.hero.contact') }}</a>
                    @endif
                </div>

                @if($now)
                    <p data-reveal class="mt-8 inline-flex items-center gap-2 text-sm text-ink-muted">
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-scope-accent"></span>
                        <span><span class="font-medium text-ink-soft">{{ __('messages.now.label') }}</span> {{ $now }}</span>
                    </p>
                @endif
            </div>

            @if($dashboard['total'])
                <div data-reveal class="mx-auto w-full max-w-md lg:max-w-none">
                    <x-hero-status :d="$dashboard" />
                </div>
            @endif
        </div>
    </section>

    {{-- Featured case: ServerScope --}}
    <x-section id="serverscope" index="1" :label="$ss['label']" :title="$ss['title']" :intro="$ss['tagline']" class="border-t border-ink-line/60">
        <div class="grid gap-10 lg:grid-cols-[1fr_1.2fr] lg:gap-16" data-features>
            {{-- min-w-0: without it the swipe carousel stretches this grid column (and the page) to its full scroll width. --}}
            <div class="min-w-0">
                <p data-reveal class="leading-relaxed text-ink-soft">{{ $ss['intro'] }}</p>

                <div data-reveal class="mt-6 flex flex-wrap gap-1.5">
                    @foreach($ss['stack'] as $item)
                        <span class="chip">{{ $item }}</span>
                    @endforeach
                </div>

                @if(config('site.serverscope.url') || config('site.serverscope.docs_url'))
                    <div data-reveal class="mt-7 flex flex-wrap gap-3">
                        @if(config('site.serverscope.url'))
                            <a href="{{ config('site.serverscope.url') }}" target="_blank" rel="noopener" data-magnetic class="btn-primary">{{ $ss['visit'] }} ↗</a>
                        @endif
                        @if(config('site.serverscope.docs_url'))
                            <a href="{{ config('site.serverscope.docs_url') }}" target="_blank" rel="noopener" data-magnetic class="btn-secondary">{{ $ss['docs'] }}</a>
                        @endif
                    </div>
                @endif

                {{-- Phones/tablets: swipe through the features; each slide carries its own screenshot. --}}
                <div class="mt-8 lg:hidden" data-carousel>
                    <div class="mb-3 flex items-center justify-between text-xs text-ink-muted">
                        <span><span data-carousel-current class="font-semibold tabular-nums text-ink-text">1</span> / {{ count($ss['features']) }}</span>
                        <span class="flex items-center gap-1.5">{{ __('messages.serverscope.swipe') }} <span class="motion-safe:animate-[nudge_1.6s_ease-in-out_infinite]">→</span></span>
                    </div>

                    <div data-carousel-track class="-mx-4 flex snap-x snap-mandatory gap-3 overflow-x-auto scroll-smooth px-4 pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" role="region" aria-roledescription="carousel" aria-label="ServerScope">
                        @foreach($ss['features'] as $key => $feature)
                            @php($src = $screenFor($key))
                            <article data-carousel-slide @if($loop->first) data-active @endif aria-roledescription="slide" aria-label="{{ $loop->iteration }} / {{ count($ss['features']) }}"
                                     class="panel w-[86%] max-w-sm shrink-0 snap-center overflow-hidden opacity-60 transition-[opacity,transform] duration-300 [transform:scale(.96)] data-[active]:opacity-100 data-[active]:[transform:scale(1)] sm:w-[60%]">
                                <div class="flex items-center gap-1.5 border-b border-ink-line px-3 py-2" aria-hidden="true">
                                    <span class="h-2 w-2 rounded-full bg-ink-strong"></span><span class="h-2 w-2 rounded-full bg-ink-strong"></span><span class="h-2 w-2 rounded-full bg-ink-strong"></span>
                                </div>
                                <div class="relative aspect-[16/10] bg-ink-panel2">
                                    @if($src)
                                        <img src="{{ $src }}" alt="ServerScope — {{ $feature['title'] }}" loading="lazy" decoding="async" class="h-full w-full object-cover object-top">
                                    @else
                                        <div class="grid-bg absolute inset-0 opacity-60" aria-hidden="true"></div>
                                        <span class="absolute inset-0 grid place-items-center text-xs text-ink-muted">{{ $ss['screenshot_pending'] }}</span>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <p class="text-[11px] font-semibold tabular-nums text-scope-accent">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                                    <h3 class="mt-1 font-semibold">{{ $feature['title'] }}</h3>
                                    <p class="mt-1.5 text-sm leading-relaxed text-ink-muted">{{ $feature['text'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-3 flex justify-center gap-1.5">
                        @foreach($ss['features'] as $feature)
                            <button type="button" data-carousel-dot @if($loop->first) data-active @endif class="h-1.5 w-1.5 rounded-full bg-ink-strong transition-all duration-300 data-[active]:w-5 data-[active]:bg-scope-accent" aria-label="{{ $feature['title'] }}"></button>
                        @endforeach
                    </div>
                </div>

                <ol class="mt-12 hidden lg:block" data-reveal-group>
                    @foreach($ss['features'] as $key => $feature)
                        <li data-reveal>
                            <button type="button" data-feature
                                    class="group/f relative w-full rounded-panel border border-transparent px-4 py-3 text-left transition-colors duration-300 data-[active]:border-ink-line data-[active]:bg-ink-panel lg:py-7">
                                <span class="flex items-baseline gap-4">
                                    <span class="text-[11px] font-semibold tabular-nums text-ink-muted transition-colors group-data-[active]/f:text-scope-accent">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span>
                                        <span class="block font-semibold text-ink-soft transition-colors group-data-[active]/f:text-ink-text">{{ $feature['title'] }}</span>
                                        <span class="mt-1 block text-sm leading-relaxed text-ink-muted max-lg:hidden max-lg:group-data-[active]/f:block">{{ $feature['text'] }}</span>
                                    </span>
                                </span>
                                <span class="absolute bottom-3 left-0 top-3 w-[2px] origin-top scale-y-0 rounded-full bg-scope-accent transition-transform duration-500 group-data-[active]/f:scale-y-100"></span>
                            </button>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="hidden lg:sticky lg:top-28 lg:block lg:h-fit">
                <div data-reveal data-tilt class="panel overflow-hidden shadow-[0_40px_100px_-40px_rgb(110_156_247/.35)]">
                    <div class="flex items-center gap-3 border-b border-ink-line px-4 py-3">
                        <span class="flex items-center gap-1.5" aria-hidden="true">
                            <span class="h-2.5 w-2.5 rounded-full bg-ink-strong"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-ink-strong"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-ink-strong"></span>
                        </span>
                        <span class="mx-auto flex items-center gap-2 rounded-lg border border-ink-line bg-ink-panel2 px-3 py-1 text-[11px] text-ink-muted">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                            {{ config('site.serverscope.url') ? parse_url(config('site.serverscope.url'), PHP_URL_HOST) : 'serverscope' }}
                        </span>
                        <span class="w-[42px]"></span>
                    </div>
                    <div class="relative aspect-[16/10] bg-ink-panel2">
                        @foreach($ss['features'] as $key => $feature)
                            @php($src = $screenFor($key))
                            <figure data-screen class="absolute inset-0 scale-[1.03] opacity-0 transition-[opacity,transform] duration-500 ease-out data-[active]:scale-100 data-[active]:opacity-100">
                                @if($src)
                                    <img src="{{ $src }}" alt="ServerScope — {{ $feature['title'] }}" loading="lazy" decoding="async" class="h-full w-full object-cover object-top">
                                @else
                                    <div class="grid-bg absolute inset-0 opacity-60" aria-hidden="true"></div>
                                    <figcaption class="absolute inset-0 grid place-items-center text-center">
                                        <span>
                                            <span class="label block text-scope-accent">{{ $feature['title'] }}</span>
                                            <span class="mt-2 block text-xs text-ink-muted">{{ $ss['screenshot_pending'] }}</span>
                                        </span>
                                    </figcaption>
                                @endif
                            </figure>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if(! empty($ss['decisions']))
            <div class="mt-20">
                <h3 data-reveal class="label mb-6">{{ $ss['decisions_title'] }}</h3>
                <div class="grid gap-4 md:grid-cols-3" data-reveal-group>
                    @foreach($ss['decisions'] as $decision)
                        <div data-reveal class="panel spotlight p-6">
                            <h4 class="font-semibold">{{ $decision['title'] }}</h4>
                            <p class="mt-2 text-sm leading-relaxed text-ink-soft">{{ $decision['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </x-section>

    {{-- Work: filterable, scales to any number of projects --}}
    <x-section id="work" index="2" :label="__('messages.portfolio.label')" :title="__('messages.portfolio.title')" :intro="__('messages.portfolio.intro')" class="border-t border-ink-line/60">
        @if($projects->isNotEmpty())
            @if($tags->count() > 1)
                <div data-reveal data-filters class="-mx-4 mb-8 flex items-center gap-2 overflow-x-auto px-4 pb-1 [scrollbar-width:none] sm:mx-0 sm:flex-wrap sm:overflow-visible sm:px-0 [&::-webkit-scrollbar]:hidden" role="toolbar" aria-label="Filter">
                    <button type="button" data-filter="" data-active class="shrink-0 whitespace-nowrap rounded-xl border border-ink-line px-3.5 py-1.5 text-[13px] font-semibold text-ink-muted transition-colors hover:text-ink-text data-[active]:border-scope-accent/50 data-[active]:bg-scope-accent/10 data-[active]:text-ink-text">{{ __('messages.portfolio.filter_all') }} <span class="ml-1 tabular-nums text-ink-muted">{{ $projects->count() }}</span></button>
                    @foreach($tags as $tag)
                        <button type="button" data-filter="{{ strtolower($tag) }}" class="shrink-0 whitespace-nowrap rounded-xl border border-ink-line px-3.5 py-1.5 text-[13px] font-semibold text-ink-muted transition-colors hover:text-ink-text data-[active]:border-scope-accent/50 data-[active]:bg-scope-accent/10 data-[active]:text-ink-text">{{ $tag }}</button>
                    @endforeach
                </div>
            @endif
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-reveal-group data-project-grid>
                @foreach($projects as $project)
                    <x-project-card :project="$project" :index="$loop->iteration" :check="$project->hasDemo() ? $live->get(rtrim($project->demo_url, '/')) : null" />
                @endforeach
            </div>
        @else
            <p class="text-sm text-ink-muted">{{ __('messages.portfolio.empty') }}</p>
        @endif
    </x-section>

    {{-- Reviews: only real ones, managed in Filament; hidden until there is at least one --}}
    @if($testimonials->isNotEmpty())
        <x-section id="reviews" :label="__('messages.testimonials.label')" :title="__('messages.testimonials.title')" class="border-t border-ink-line/60">
            <div class="grid gap-4 {{ $testimonials->count() > 1 ? 'md:grid-cols-2' : '' }} {{ $testimonials->count() > 2 ? 'lg:grid-cols-3' : '' }}" data-reveal-group>
                @foreach($testimonials as $t)
                    <figure data-reveal class="panel spotlight flex flex-col p-6 md:p-7">
                        <svg class="h-6 w-6 text-scope-accent" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9.6 6C6.5 7.3 4.5 10 4.5 13.6V18h5.4v-5.4H7.2c0-2.2 1.2-3.9 3.3-4.8L9.6 6Zm9 0c-3.1 1.3-5.1 4-5.1 7.6V18h5.4v-5.4h-2.7c0-2.2 1.2-3.9 3.3-4.8L18.6 6Z"/></svg>
                        <blockquote class="mt-4 flex-1 text-[15px] leading-relaxed text-ink-soft md:text-base">{{ $t->getTranslation('quote') }}</blockquote>
                        <figcaption class="mt-6 flex items-center gap-3 border-t border-ink-line pt-4">
                            @if($t->avatarUrl())
                                <img src="{{ $t->avatarUrl() }}" alt="" loading="lazy" class="h-10 w-10 rounded-full object-cover">
                            @else
                                <span class="grid h-10 w-10 place-items-center rounded-full bg-scope-accent/15 text-sm font-semibold text-scope-accent">{{ $t->initials() }}</span>
                            @endif
                            <span class="min-w-0">
                                <span class="block truncate font-semibold">
                                    @if($t->url)<a href="{{ $t->url }}" target="_blank" rel="noopener noreferrer" class="hover:text-scope-accent">{{ $t->name }}</a>@else{{ $t->name }}@endif
                                </span>
                                <span class="block truncate text-xs text-ink-muted">{{ collect([$t->role, $t->company])->filter()->implode(' · ') }}@if($t->project) · {{ $t->project->getTranslation('title') }}@endif</span>
                            </span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </x-section>
    @endif

    {{-- Live status: the ServerScope-style dashboard, fed by real checks --}}
    <x-section id="status" :label="__('messages.status.section_label')" :title="__('messages.status.section_title')" :intro="__('messages.status.section_intro')" class="border-t border-ink-line/60">
        <x-health-dashboard :d="$dashboard" />

        <div class="mt-4 grid gap-4 {{ config('site.serverscope.status_url') ? 'md:grid-cols-2' : '' }}">
            <a data-reveal href="{{ lroute('status') }}" class="panel group flex items-center justify-between gap-3 px-5 py-4 transition-colors hover:border-ink-strong">
                <span class="text-sm"><span class="font-semibold">{{ __('messages.status.full_page') }}</span> <span class="text-ink-muted">· {{ __('messages.status.full_page_hint') }}</span></span>
                <span class="text-ink-muted transition-transform group-hover:translate-x-1">→</span>
            </a>
            @if($statusUrl = config('site.serverscope.status_url'))
                {{-- Official, documented ServerScope badge (public status page, no token). --}}
                <a data-reveal href="{{ $statusUrl }}" target="_blank" rel="noopener" class="panel group flex flex-wrap items-center justify-between gap-3 px-5 py-4 transition-colors hover:border-ink-strong">
                    <span class="flex items-center gap-3 text-sm">
                        <svg class="h-5 w-5 text-scope-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12h4l3-8 4 16 3-8h6"/></svg>
                        <span class="font-semibold">{{ __('messages.serverscope.monitored') }}</span>
                    </span>
                    <span class="flex items-center gap-3">
                        <img data-theme-src="{{ rtrim($statusUrl, '/') }}/badge.svg?lang={{ app()->getLocale() }}&theme=" alt="ServerScope status" height="20" class="h-5 w-auto">
                        <script>document.currentScript.previousElementSibling.src = document.currentScript.previousElementSibling.dataset.themeSrc + document.documentElement.dataset.theme;</script>
                        <noscript><img src="{{ rtrim($statusUrl, '/') }}/badge.svg?lang={{ app()->getLocale() }}&theme=dark" alt="ServerScope status" height="20" class="h-5 w-auto"></noscript>
                        <span class="text-xs text-ink-muted transition-transform group-hover:translate-x-1">↗</span>
                    </span>
                </a>
            @endif
        </div>
    </x-section>

    {{-- Proof: real numbers + how I work --}}
    <x-section id="proof" index="3" :label="__('messages.proof.label')" :title="__('messages.proof.title')" :intro="__('messages.proof.intro')" class="border-t border-ink-line/60">
        <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-panel border border-ink-line bg-ink-line lg:grid-cols-4" data-reveal-group>
            @foreach([
                ['live', $liveProjects, ''],
                ['uptime', $dashboard['uptime_7d'], '%'],
                ['notes', $blogCount, ''],
                ['avg', $dashboard['avg_ms'], ' ms'],
            ] as [$key, $value, $unit])
                <div data-reveal class="bg-ink-panel p-5 sm:p-6 md:p-8">
                    <dd class="whitespace-nowrap text-3xl font-bold tracking-tight tabular-nums sm:text-4xl md:text-5xl">
                        @if($value !== null)
                            <span data-count="{{ $value }}" @if(is_float($value)) data-decimals="1" @endif>{{ $value }}</span><span class="text-[.6em] text-ink-muted">{{ $unit }}</span>
                        @else — @endif
                    </dd>
                    <dt class="mt-2 text-sm text-ink-muted">{{ __('messages.proof.stats.'.$key) }}</dt>
                </div>
            @endforeach
        </dl>

        <h3 data-reveal class="label mb-6 mt-16">{{ __('messages.proof.steps_title') }}</h3>
        <ol class="relative grid gap-4 md:grid-cols-4" data-reveal-group data-steps>
            <span data-steps-line class="absolute left-0 right-0 top-[27px] hidden h-px origin-left bg-gradient-to-r from-scope-accent via-scope-green to-transparent md:block" aria-hidden="true"></span>
            @foreach(__('messages.proof.steps') as $step)
                <li data-reveal class="panel spotlight relative p-5 sm:p-6">
                    <span class="relative grid h-7 w-7 place-items-center rounded-full border border-ink-strong bg-ink-bg text-[11px] font-bold tabular-nums {{ $loop->last ? 'border-scope-green/60 text-scope-green' : 'text-scope-accent' }}">{{ $loop->iteration }}</span>
                    <h4 class="mt-5 font-semibold">{{ $step['title'] }}</h4>
                    <p class="mt-2 text-sm leading-relaxed text-ink-soft">{{ $step['text'] }}</p>
                </li>
            @endforeach
        </ol>
    </x-section>

    {{-- Stack --}}
    @php($groups = __('messages.skills.groups'))
    <x-section id="stack" index="4" :label="__('messages.skills.label')" :title="__('messages.skills.title')" :intro="__('messages.skills.intro')" class="overflow-hidden border-t border-ink-line/60">
        <div class="grid gap-4 md:grid-cols-3" data-reveal-group>
            @foreach($groups as $group)
                <div data-reveal class="panel spotlight p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="label">{{ $group['label'] }}</h3>
                        <span class="text-[11px] tabular-nums text-ink-muted">{{ count($group['items']) }}</span>
                    </div>
                    <div class="mt-5 flex flex-wrap gap-2">
                        @foreach($group['items'] as $item)
                            <span class="rounded-xl border border-ink-line bg-ink-panel2 px-3 py-1.5 text-sm font-medium text-ink-soft transition-colors hover:border-scope-accent/40 hover:text-ink-text">{{ $item }}</span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="group/m relative mt-12 space-y-3 overflow-hidden [mask-image:linear-gradient(90deg,transparent,#000_12%,#000_88%,transparent)]" aria-hidden="true">
            @foreach([false, true] as $reverse)
                <div class="flex w-max animate-marquee gap-3 group-hover/m:[animation-play-state:paused] {{ $reverse ? '[animation-direction:reverse] [animation-duration:55s]' : '' }}">
                    @foreach([1, 2] as $copy)
                        @foreach(collect($groups)->pluck('items')->flatten()->when($reverse, fn ($c) => $c->reverse()) as $item)
                            <span class="flex items-center gap-2 whitespace-nowrap rounded-full border border-ink-line px-4 py-2 text-xs font-medium text-ink-muted">
                                <span class="h-1 w-1 rounded-full {{ $loop->index % 3 === 0 ? 'bg-scope-green' : 'bg-scope-accent' }}"></span>{{ $item }}
                            </span>
                        @endforeach
                    @endforeach
                </div>
            @endforeach
        </div>
    </x-section>

    {{-- About --}}
    <x-section id="about" index="5" :label="__('messages.about.label')" :title="__('messages.about.title')" class="border-t border-ink-line/60">
        <div class="grid gap-10 lg:grid-cols-[300px_1fr] lg:gap-16">
            <div data-reveal class="space-y-4">
                <div class="panel flex items-center gap-4 p-4">
                    <img src="{{ $avatar }}" alt="{{ config('site.name') }}" width="64" height="64" loading="lazy" class="h-16 w-16 rounded-2xl object-cover">
                    <div>
                        <p class="font-semibold">{{ config('site.name') }}</p>
                        <p class="text-sm text-ink-muted">{{ __('messages.home.subtitle') }}</p>
                    </div>
                </div>
                <dl class="panel divide-y divide-ink-line text-sm">
                    @foreach([
                        ['about.location', config('site.location')],
                        ['about.role', __('messages.about.role_value')],
                        ['about.availability', __('messages.about.availability_value')],
                    ] as [$key, $value])
                        <div class="flex justify-between gap-4 px-4 py-3">
                            <dt class="text-ink-muted">{{ __('messages.'.$key) }}</dt>
                            <dd class="text-right font-medium">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
                <a href="{{ lroute('cv.pdf') }}" class="btn-secondary w-full">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 4v11m0 0-4-4m4 4 4-4M5 20h14"/></svg>
                    {{ __('messages.cv.download') }}
                </a>
            </div>

            <div>
                <div class="max-w-2xl space-y-5 text-lg leading-relaxed text-ink-soft">
                    <p data-reveal>{{ __('messages.about.text') }}</p>
                    <p data-reveal>{{ __('messages.about.text_2') }}</p>
                </div>

                <h3 data-reveal class="label mb-6 mt-14">{{ __('messages.about.timeline_title') }}</h3>
                <ol class="relative space-y-8 pl-8" data-reveal-group>
                    <span data-draw-line class="absolute bottom-1 left-0 top-1 w-px origin-top bg-gradient-to-b from-ink-strong via-scope-accent to-scope-green" aria-hidden="true"></span>
                    @foreach(__('messages.about.timeline') as $step)
                        <li data-reveal class="relative">
                            <span class="absolute -left-[36px] top-1.5 h-[9px] w-[9px] rounded-full {{ $loop->last ? 'bg-scope-green shadow-[0_0_0_4px_rgb(74_222_128/.2)]' : 'border border-ink-strong bg-ink-bg' }}"></span>
                            <h4 class="font-semibold">{{ $step['title'] }}</h4>
                            <p class="mt-1 text-sm leading-relaxed text-ink-muted">{{ $step['text'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </x-section>

    {{-- Certificates --}}
    @if($certificates->isNotEmpty())
        <x-section id="certificates" index="6" :label="__('messages.certificates.label')" :title="__('messages.certificates.title')" :intro="__('messages.certificates.intro')" class="border-t border-ink-line/60">
            <div class="panel divide-y divide-ink-line" data-reveal-group>
                @foreach($certificates as $cert)
                    <div data-reveal class="grid grid-cols-[80px_1fr_auto] items-center gap-4 px-5 py-4">
                        <span class="text-xs tabular-nums text-ink-muted">{{ $cert->issued_at?->format('M Y') ?? '—' }}</span>
                        <span>
                            <span class="block font-medium">{{ $cert->getTranslation('title') }}</span>
                            @if($cert->issuer)<span class="text-xs text-ink-muted">{{ $cert->issuer }}</span>@endif
                        </span>
                        @if($cert->fileUrl())
                            <a href="{{ $cert->fileUrl() }}" target="_blank" class="text-xs font-semibold text-scope-accent hover:underline">{{ __('messages.certificates.view') }} →</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-section>
    @endif

    {{-- Notes --}}
    @if($blogs->isNotEmpty())
        <x-section id="blog" :label="__('messages.blog.label')" :title="__('messages.blog.title')" :intro="__('messages.blog.intro')" class="border-t border-ink-line/60">
            <x-blog-list :blogs="$blogs" />
            @if($blogCount > $blogs->count())
                <a data-reveal href="{{ lroute('blog.index') }}" class="btn-secondary mt-6">{{ __('messages.blog.all') }} ({{ $blogCount }}) →</a>
            @endif
        </x-section>
    @endif

    {{-- Contact --}}
    <x-section id="contact" class="border-t border-ink-line/60">
        <div class="grid items-start gap-12 lg:grid-cols-2">
            <div>
                <p class="label mb-4" data-reveal>{{ __('messages.contact.label') }}</p>
                <h2 data-split class="text-4xl font-bold tracking-[-.03em] md:text-6xl">{{ __('messages.contact.title') }}</h2>
                <p data-reveal class="mt-5 max-w-md text-lg leading-relaxed text-ink-soft">{{ __('messages.contact.subtitle') }}</p>

                <div data-reveal class="mt-10 space-y-2">
                    <p class="label mb-3">{{ __('messages.contact.or') }}</p>
                    <a href="mailto:{{ config('site.email') }}" class="panel group flex items-center justify-between px-4 py-3 text-sm font-medium transition-colors hover:border-ink-strong">
                        <span class="flex items-center gap-3"><span class="dot bg-scope-green"></span>{{ config('site.email') }}</span>
                        <span class="text-ink-muted transition-transform group-hover:translate-x-1">→</span>
                    </a>
                    <a href="{{ config('site.linkedin') }}" target="_blank" rel="noopener noreferrer" class="panel group flex items-center justify-between px-4 py-3 text-sm font-medium transition-colors hover:border-ink-strong">
                        <span class="flex items-center gap-3"><span class="dot bg-scope-accent"></span>LinkedIn</span>
                        <span class="text-ink-muted transition-transform group-hover:translate-x-1">↗</span>
                    </a>
                    @if(config('site.booking_url'))
                        <a href="{{ config('site.booking_url') }}" target="_blank" rel="noopener" data-track="booking_click" data-track-label="contact" class="panel group flex items-center justify-between border-scope-accent/40 bg-scope-accent/10 px-4 py-3 text-sm font-medium transition-colors hover:border-scope-accent">
                            <span class="flex items-center gap-3"><span class="dot bg-scope-accent"></span>{{ __('messages.booking.cta') }} <span class="text-ink-muted">· {{ __('messages.booking.hint') }}</span></span>
                            <span class="text-ink-muted transition-transform group-hover:translate-x-1">↗</span>
                        </a>
                    @endif
                    @if(config('site.phone'))
                        <a href="tel:{{ config('site.phone_href') }}" class="panel group flex items-center justify-between px-4 py-3 text-sm font-medium transition-colors hover:border-ink-strong">
                            <span class="flex items-center gap-3"><span class="dot bg-scope-accent"></span>{{ config('site.phone') }}</span>
                            <span class="text-ink-muted transition-transform group-hover:translate-x-1">→</span>
                        </a>
                    @endif
                </div>
            </div>

            <div data-reveal x-data="{
                form: { name: '', email: '', message: '', website: '', locale: @js(app()->getLocale()) },
                errors: {},
                success: false,
                failed: false,
                loading: false,
                async submit() {
                    this.loading = true; this.errors = {}; this.success = false; this.failed = false;
                    try {
                        let response = await fetch('{{ route('contact.send') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            body: JSON.stringify(this.form)
                        });
                        let data = await response.json();
                        this.loading = false;
                        if (data.success) { this.success = true; this.form = { name: '', email: '', message: '', website: '', locale: this.form.locale }; }
                        else if (response.status === 429) { this.failed = true; }
                        else { this.errors = data.errors || {}; this.failed = !data.errors; }
                    } catch (e) {
                        this.loading = false;
                        this.failed = true;
                    }
                }
            }" class="panel spotlight overflow-hidden">
                <div class="panel-head">
                    <span>{{ __('messages.contact.label') }}</span>
                    <span class="flex items-center gap-2 normal-case tracking-normal" :class="success ? 'text-scope-green' : (failed ? 'text-scope-red' : 'text-ink-muted')">
                        <span class="dot" :class="success ? 'bg-scope-green' : (failed ? 'bg-scope-red' : 'bg-scope-accent')"></span>
                        <span x-text="loading ? @js(__('messages.contact.sending')) : (success ? '200 OK' : (failed ? 'error' : 'ready'))">ready</span>
                    </span>
                </div>

                <form @submit.prevent="submit" class="space-y-4 p-5 md:p-6" novalidate>
                    <div x-show="success" x-cloak x-transition class="rounded-xl border border-scope-green/30 bg-scope-green/10 p-3.5 text-sm text-scope-green" role="status">{{ __('messages.contact.success') }}</div>
                    <div x-show="failed" x-cloak x-transition class="rounded-xl border border-scope-red/30 bg-scope-red/10 p-3.5 text-sm text-scope-red" role="alert">{{ __('messages.contact.error') }}</div>

                    {{-- Honeypot for bots: hidden from people and screen readers. --}}
                    <div class="absolute -left-[9999px] h-px w-px overflow-hidden" aria-hidden="true">
                        <label for="c-website">Website</label>
                        <input id="c-website" type="text" x-model="form.website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="c-name" class="mb-1.5 block text-xs font-semibold text-ink-soft">{{ __('messages.contact.label_name') }}</label>
                            <input id="c-name" type="text" x-model="form.name" class="input-field" autocomplete="name" required>
                            <p class="mt-1 text-xs text-scope-red" x-text="errors.name ? errors.name[0] : ''"></p>
                        </div>
                        <div>
                            <label for="c-email" class="mb-1.5 block text-xs font-semibold text-ink-soft">{{ __('messages.contact.label_email') }}</label>
                            <input id="c-email" type="email" x-model="form.email" class="input-field" autocomplete="email" required>
                            <p class="mt-1 text-xs text-scope-red" x-text="errors.email ? errors.email[0] : ''"></p>
                        </div>
                    </div>
                    <div>
                        <label for="c-message" class="mb-1.5 block text-xs font-semibold text-ink-soft">{{ __('messages.contact.label_message') }}</label>
                        <textarea id="c-message" x-model="form.message" rows="5" class="input-field resize-y" required></textarea>
                        <p class="mt-1 text-xs text-scope-red" x-text="errors.message ? errors.message[0] : ''"></p>
                    </div>
                    <button type="submit" :disabled="loading" data-magnetic class="btn-primary w-full disabled:opacity-60">
                        <span x-show="loading" x-cloak class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                        <span x-text="loading ? @js(__('messages.contact.sending')) : @js(__('messages.contact.btn_send'))">{{ __('messages.contact.btn_send') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </x-section>
@endsection
