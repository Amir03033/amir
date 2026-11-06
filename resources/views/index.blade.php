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
    $lastCheck = $dashboard['last_check'];
@endphp

@section('content')
    {{-- Boot sequence: once per session, skipped by any key or click, never shown without JS or with reduced motion. --}}
    <div data-boot class="fixed inset-0 z-[90] hidden cursor-pointer items-center justify-center bg-ink-bg [.js-motion:not(.booted)_&]:flex" aria-hidden="true">
        <div class="w-full max-w-md px-6 font-mono text-[13px]">
            <svg class="mb-6 h-8 w-8 text-scope-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path data-boot-pulse d="M2 12h4l3-8 4 16 3-8h6"/></svg>
            @foreach(__('messages.boot.lines') as $line)
                <p data-boot-line="{{ $line }}" class="flex items-center justify-between gap-4 py-1 opacity-0">
                    <span><span class="text-scope-accent">&gt;</span> <span data-boot-text></span></span>
                    <span data-boot-ok class="text-scope-green opacity-0">{{ $loop->last ? '●' : 'ok' }}</span>
                </p>
            @endforeach
            <p class="mt-6 text-[11px] text-ink-muted">{{ __('messages.boot.hint') }}</p>
        </div>
    </div>

    {{-- Hero: a dashboard, like ServerScope's --}}
    <section id="home" data-hero class="relative overflow-hidden px-4 pb-20 pt-28 md:pt-32">
        <canvas data-field class="absolute inset-x-0 top-0 h-[720px] w-full opacity-70 [mask-image:linear-gradient(to_bottom,#000,transparent)]" aria-hidden="true"></canvas>

        <div class="relative mx-auto max-w-6xl">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p data-reveal class="flex items-center gap-2 text-sm text-ink-muted">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 11.5 12 4l9 7.5M5 10v10h14V10"/></svg>
                        <span>›</span> {{ __('messages.hero.crumb') }}
                    </p>

                    <div data-reveal class="mt-6 flex items-center gap-3">
                        <img src="{{ $avatar }}" alt="{{ config('site.name') }}" width="48" height="48" class="h-12 w-12 rounded-full object-cover ring-2 ring-ink-line ring-offset-2 ring-offset-ink-bg">
                        <div class="leading-tight">
                            <p class="text-gradient text-[15px] font-semibold">{{ config('site.name') }}</p>
                            <p class="text-sm text-ink-muted">{{ __('messages.home.subtitle') }} · {{ config('site.location') }}</p>
                        </div>
                    </div>

                    <h1 data-split class="mt-6 text-[2.5rem] font-bold leading-[1.04] tracking-[-.04em] sm:text-6xl lg:text-[4.5rem]">{{ __('messages.home.headline') }}</h1>
                    <p data-reveal class="mt-5 max-w-2xl text-base leading-relaxed text-ink-soft md:text-lg">{{ __('messages.home.text') }}</p>
                </div>

                <div data-reveal class="flex flex-wrap items-center gap-3 lg:justify-end">
                    <span class="pill border-scope-green/40 bg-scope-green/10 text-scope-green"><span class="dot bg-scope-green h-1.5 w-1.5"></span>{{ __('messages.hero.available') }}</span>
                    <span class="text-xs leading-tight text-ink-muted">
                        {{ __('messages.hero.last_check') }}<br>
                        <span class="font-semibold text-ink-text">{{ $lastCheck ? $lastCheck->diffForHumans() : __('messages.hero.never') }}</span>
                    </span>
                    @if(config('site.booking_url'))
                        <a href="{{ config('site.booking_url') }}" target="_blank" rel="noopener" data-magnetic class="btn-secondary">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
                            {{ __('messages.booking.cta') }}
                        </a>
                    @endif
                    <a href="#contact" data-magnetic class="btn-primary">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        {{ __('messages.hero.contact') }}
                    </a>
                </div>
            </div>

            <div class="mt-10">
                <x-health-dashboard :d="$dashboard" />
            </div>

            @if($statusUrl = config('site.serverscope.status_url'))
                {{-- Official, documented ServerScope badge (public status page, no token). --}}
                <a data-reveal href="{{ $statusUrl }}" target="_blank" rel="noopener" class="panel group mt-4 flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 transition-colors hover:border-ink-strong">
                    <span class="flex items-center gap-3 text-sm">
                        <svg class="h-5 w-5 text-scope-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12h4l3-8 4 16 3-8h6"/></svg>
                        <span><span class="font-semibold">{{ __('messages.serverscope.monitored') }}</span> <span class="text-ink-muted">· {{ __('messages.serverscope.monitored_hint') }}</span></span>
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
    </section>

    {{-- Featured case: ServerScope --}}
    <x-section id="serverscope" index="1" :label="$ss['label']" :title="$ss['title']" :intro="$ss['tagline']" class="border-t border-ink-line/60">
        <div class="grid gap-10 lg:grid-cols-[1fr_1.2fr] lg:gap-16" data-features>
            <div>
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

                <ol class="mt-12 space-y-2 lg:space-y-0" data-reveal-group>
                    @foreach($ss['features'] as $key => $feature)
                        <li data-reveal>
                            <button type="button" data-feature
                                    class="group/f relative w-full rounded-panel border border-transparent p-4 text-left transition-colors duration-300 data-[active]:border-ink-line data-[active]:bg-ink-panel lg:py-7">
                                <span class="flex items-baseline gap-4">
                                    <span class="text-[11px] font-semibold tabular-nums text-ink-muted transition-colors group-data-[active]/f:text-scope-accent">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span>
                                        <span class="block font-semibold text-ink-soft transition-colors group-data-[active]/f:text-ink-text">{{ $feature['title'] }}</span>
                                        <span class="mt-1 block text-sm leading-relaxed text-ink-muted">{{ $feature['text'] }}</span>
                                    </span>
                                </span>
                                <span class="absolute bottom-3 left-0 top-3 w-[2px] origin-top scale-y-0 rounded-full bg-scope-accent transition-transform duration-500 group-data-[active]/f:scale-y-100"></span>
                            </button>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="order-first lg:order-none lg:sticky lg:top-28 lg:h-fit">
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
                <div data-reveal data-filters class="mb-8 flex flex-wrap items-center gap-2" role="toolbar" aria-label="Filter">
                    <button type="button" data-filter="" data-active class="rounded-xl border border-ink-line px-3.5 py-1.5 text-[13px] font-semibold text-ink-muted transition-colors hover:text-ink-text data-[active]:border-scope-accent/50 data-[active]:bg-scope-accent/10 data-[active]:text-ink-text">{{ __('messages.portfolio.filter_all') }} <span class="ml-1 tabular-nums text-ink-muted">{{ $projects->count() }}</span></button>
                    @foreach($tags as $tag)
                        <button type="button" data-filter="{{ strtolower($tag) }}" class="rounded-xl border border-ink-line px-3.5 py-1.5 text-[13px] font-semibold text-ink-muted transition-colors hover:text-ink-text data-[active]:border-scope-accent/50 data-[active]:bg-scope-accent/10 data-[active]:text-ink-text">{{ $tag }}</button>
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

    {{-- Proof: real numbers + how I work --}}
    <x-section id="proof" index="3" :label="__('messages.proof.label')" :title="__('messages.proof.title')" :intro="__('messages.proof.intro')" class="border-t border-ink-line/60">
        <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-panel border border-ink-line bg-ink-line lg:grid-cols-4" data-reveal-group>
            @foreach([
                ['live', $liveProjects, ''],
                ['uptime', $dashboard['uptime_7d'], '%'],
                ['notes', $blogCount, ''],
                ['avg', $dashboard['avg_ms'], ' ms'],
            ] as [$key, $value, $unit])
                <div data-reveal class="bg-ink-panel p-6 md:p-8">
                    <dd class="text-4xl font-bold tracking-tight tabular-nums md:text-5xl">
                        @if($value !== null)
                            <span data-count="{{ $value }}" @if(is_float($value)) data-decimals="1" @endif>{{ $value }}</span><span class="text-ink-muted">{{ $unit }}</span>
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
                <li data-reveal class="panel spotlight relative p-6">
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
                <a href="{{ route('cv.pdf') }}" class="btn-secondary w-full">
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
                <a data-reveal href="{{ route('blog.index') }}" class="btn-secondary mt-6">{{ __('messages.blog.all') }} ({{ $blogCount }}) →</a>
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
                        <a href="{{ config('site.booking_url') }}" target="_blank" rel="noopener" class="panel group flex items-center justify-between border-scope-accent/40 bg-scope-accent/10 px-4 py-3 text-sm font-medium transition-colors hover:border-scope-accent">
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
                form: { name: '', email: '', message: '', website: '' },
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
                        if (data.success) { this.success = true; this.form = { name: '', email: '', message: '', website: '' }; }
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
