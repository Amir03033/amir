@extends('layouts.layout', [
    'title' => __('seo.pages.home.title'),
    'description' => __('seo.pages.home.description'),
    'pageType' => 'ProfilePage',
    'ogType' => 'profile',
    'ogImage' => \App\Models\Setting::media('og') ?? \App\Support\Seo::ogImage('page', 'home'),
])

@section('nav')
    <x-site-nav :on-home="true" />
@endsection

@section('compact-footer', '1')
@section('inline-terminal', '1')

@php
    $home = lroute('home');
    $jebbari = config('site.jebbari.url');
    $email = config('site.email');
    $aboutPhoto = \App\Models\Setting::media('about');
    $amsterdam = now('Europe/Amsterdam');
    $facts = [
        ['agency', $jebbari ? 'Jebbari' : null, $jebbari ? jebbari_url('home', 'about') : null],
        ['cities', __('site.about.cities_value'), null],
        ['product', 'ServerScope', config('site.serverscope.url')],
        ['since', null, null],  // missing fact
    ];
@endphp


@section('content')
    {{-- Hero: one statement, the role line, a live status line and the stamp. --}}
    <section id="home" class="hero container" data-hero data-stamp-area>
        <div class="hero-imprints" data-imprints aria-hidden="true"></div>
        <div class="hero-content">
            <h1 class="hero-type">
                @foreach(__('site.hero.lines') as $line)
                    <span class="block" data-hero-line style="--i: {{ $loop->index }}">{{ $line }}</span>
                @endforeach
            </h1>
            {{-- The one-sentence summary: who, what, where. Same wording as jebbari.nl (config/seo.php). --}}
            <p class="mx-auto mt-8 max-w-xl text-small text-fg-2" data-hero-line style="--i: 3">{{ seo_sentence(link: true) }}</p>
            <p class="status-line mt-3" data-hero-line style="--i: 4">{!! __('site.hero.time', ['time' => '<time class="tabular" datetime="'.e($amsterdam->toIso8601String()).'" data-clock="Europe/Amsterdam">'.e($amsterdam->format('H:i')).'</time>']) !!}@if($now) {{ __('messages.now.label') }} <span class="text-fg">{{ $now }}</span>@endif</p>
            <div data-hero-line style="--i: 5"><x-stamp /></div>
        </div>
    </section>

    {{-- Work: up to four projects as cards (featured first), then one link to everything. --}}
    <section id="work" class="section" aria-labelledby="work-title">
        <div class="container">
            <x-opener id="work-title" :label="__('site.work.label')">{{ __('site.work.title') }}</x-opener>

            @php($cards = $chapters->concat($more)->take(4))
            @if($cards->isEmpty())
                <p class="empty mt-stack text-center">{{ __('site.work.empty') }}</p>
            @else
                <div class="work-grid mt-stack" data-reveal-group>
                    @foreach($cards as $project)
                        <x-work-card :project="$project" />
                    @endforeach
                </div>
            @endif

            @if($projectCount > 0)
                <p class="mt-12 text-center" data-reveal>
                    <a href="{{ lroute('work') }}" class="btn-secondary">{{ __('site.work.all') }} <span class="tabular text-muted">{{ $projectCount }}</span></a>
                </p>
            @endif
        </div>
    </section>

    {{-- About: photo beside the story and a few real facts (stacked on phones). --}}
    <section id="about" class="section" aria-labelledby="about-title">
        <div class="container">
            <x-opener id="about-title" :label="__('site.about.label')">{{ __('site.about.title') }}</x-opener>

            <div class="about-grid mt-stack">
            <figure class="about-photo" data-reveal>
                <picture>
                    @if($aboutPhoto)
                        <img src="{{ $aboutPhoto }}" alt="{{ __('site.about.photo_alt') }}" width="960" height="1200" loading="lazy" decoding="async">
                    @else
                        <source type="image/avif" srcset="{{ asset('images/about/amir-640.avif') }} 640w, {{ asset('images/about/amir-960.avif') }} 960w, {{ asset('images/about/amir-1280.avif') }} 1280w" sizes="(min-width: 640px) 560px, 100vw">
                        <source type="image/webp" srcset="{{ asset('images/about/amir-640.webp') }} 640w, {{ asset('images/about/amir-960.webp') }} 960w, {{ asset('images/about/amir-1280.webp') }} 1280w" sizes="(min-width: 640px) 560px, 100vw">
                        <img src="{{ asset('images/about/amir-960.webp') }}" alt="{{ __('site.about.photo_alt') }}" width="960" height="1200" loading="lazy" decoding="async">
                    @endif
                </picture>
            </figure>

            <div class="about-text">
                <div class="space-y-5 text-lead text-fg-2" data-reveal-group>
                    <p data-reveal>{{ __('site.about.text') }}</p>
                    <p data-reveal>{{ __('site.about.text_2') }}</p>
                </div>

                <dl class="facts mt-12" data-reveal>
                    @foreach($facts as [$key, $value, $href])
                        @if($value || ! app()->isProduction())
                            <dt>{{ __('site.about.facts.'.$key) }}</dt>
                            <dd>
                                @if($value && $href)
                                    <a href="{{ $href }}" class="link" rel="noopener" target="_blank">{{ $value }}</a>
                                @elseif($value)
                                    {{ $value }}
                                @else
                                    <x-ph :label="__('site.about.facts.'.$key)" />
                                @endif
                            </dd>
                        @endif
                    @endforeach
                </dl>

                <div class="mt-10 flex flex-wrap gap-x-8 gap-y-3" data-reveal>
                    @if($jebbari)
                        <a href="{{ jebbari_url('home', 'about') }}" class="link-arrow" rel="noopener" target="_blank" data-track="cta_jebbari" data-track-label="about"><span>{{ __('site.about.to_jebbari') }}</span>{{ icon('arrow-up-right', 'icon-diag') }}</a>
                    @endif
                    <a href="{{ lroute('cv') }}" class="link-arrow"><span>{{ __('site.about.cv') }}</span>{{ icon('arrow-right') }}</a>
                </div>
            </div>
            </div>
        </div>
    </section>

    {{-- Real testimonials only; hidden until at least one is visible in Filament. --}}
    @if($testimonials->isNotEmpty())
        <section class="section" aria-labelledby="quotes-title">
            <div class="container read">
                <h2 id="quotes-title" class="label mb-10 text-center" data-reveal>{{ __('site.testimonials.label') }}</h2>
                <div class="space-y-16" data-reveal-group>
                    @foreach($testimonials as $t)
                        <figure data-reveal>
                            <blockquote class="quote">“{{ $t->getTranslation('quote') }}”</blockquote>
                            <figcaption class="mt-4 text-small text-muted">
                                — @if($t->url)<a href="{{ $t->url }}" class="link" rel="noopener noreferrer" target="_blank">{{ $t->name }}</a>@else{{ $t->name }}@endif@if($t->role || $t->company), {{ collect([$t->role, $t->company])->filter()->implode(', ') }}@endif
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Playground: Ask Amir, ideas, terminal, ping (Phase 4). --}}
    @includeIf('home.playground')

    {{-- Notes and certificates side by side: short lists, each with one link to the full page. --}}
    @if($blogs->isNotEmpty() || $certificates->isNotEmpty())
        <section class="section" aria-label="{{ __('site.notes.title') }}">
            <div class="container split">
                @if($blogs->isNotEmpty())
                    <div id="blog" class="split-col">
                        <p class="label" data-reveal>{{ __('messages.nav.blog') }}</p>
                        <h2 id="notes-title" class="split-title" data-reveal>{{ __('site.notes.title') }}</h2>
                        <ul class="rule-list mt-8" data-reveal-group>
                            @foreach($blogs as $blog)
                                <x-note-row :blog="$blog" />
                            @endforeach
                        </ul>
                        <p class="mt-6"><a href="{{ lroute('blog.index') }}" class="link-arrow"><span>{{ __('site.notes.all') }}</span>{{ icon('arrow-right') }}</a></p>
                    </div>
                @endif
                @if($certificates->isNotEmpty())
                    <div id="certificates" class="split-col">
                        <p class="label" data-reveal>{{ __('messages.nav.certificates') }}</p>
                        <h2 id="cert-title" class="split-title" data-reveal>{{ __('site.certificates.title') }}</h2>
                        <ul class="rule-list mt-8" data-reveal-group>
                            @foreach($certificates->take(4) as $cert)
                                <x-certificate-row :cert="$cert" />
                            @endforeach
                        </ul>
                        @if($certificates->count() > 4)
                            <p class="mt-6"><a href="{{ lroute('certificates') }}" class="link-arrow"><span>{{ __('site.certificates.all') }}</span>{{ icon('arrow-right') }}</a></p>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Contact: address and the path to Jebbari on the left, the form on the right (stacked on phones). --}}
    <section id="contact" class="section" aria-labelledby="contact-title">
        <div class="container contact-grid">
            <div class="contact-intro">
                <p class="label" data-reveal>{{ __('site.contact.label') }}</p>
                <h2 id="contact-title" class="split-title" data-reveal>{{ __('site.contact.title') }}</h2>
                <p class="mt-4 text-fg-2" data-reveal>{{ __('site.contact.lead') }}</p>
                <p class="mt-8" data-reveal>
                    <a href="mailto:{{ $email }}" class="contact-mail-sm">{{ $email }}</a>
                </p>
                @if(config('site.booking_url'))
                    <p class="mt-6" data-reveal>
                        <a href="{{ config('site.booking_url') }}" class="btn-secondary" target="_blank" rel="noopener" data-track="booking_click" data-track-label="contact">{{ __('site.contact.booking') }} {{ icon('arrow-up-right') }}</a>
                    </p>
                @endif
                <aside class="contact-wwm mt-10" aria-label="{{ __('site.wwm.label') }}" data-reveal>
                    <p class="font-semibold">{{ __('site.cta.title') }}</p>
                    <p class="mt-1 text-small text-fg-2">{{ __('site.cta.text') }}</p>
                    <p class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-3">
                        <a href="{{ lroute('work-with-me') }}?via=home" class="btn-primary btn-sm" data-track="cta_click" data-track-label="home">{{ __('site.cta.primary') }} {{ icon('arrow-right') }}</a>
                        <a href="{{ jebbari_url('request', 'home') }}" class="link-arrow text-small" rel="noopener" data-track="cta_jebbari" data-track-label="home"><span>{{ __('site.cta.direct') }}</span>{{ icon('arrow-up-right', 'icon-diag') }}</a>
                    </p>
                </aside>
            </div>
            <div class="contact-form-col" data-reveal>
                @include('partials.contact-form')
            </div>
        </div>
    </section>
@endsection
