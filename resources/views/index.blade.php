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
        <div class="hero-content" data-scrub-view="hero-exit">
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

    {{-- Work: pinned chapters for the featured projects, then a quiet list. --}}
    <section id="work" class="section" aria-labelledby="work-title">
        <div class="container">
            <x-opener id="work-title" :label="__('site.work.label')">{{ __('site.work.title') }}</x-opener>
        </div>

        @forelse($chapters as $project)
            <x-work-chapter :project="$project" :index="$loop->iteration" />
        @empty
            <p class="empty container">{{ __('site.work.empty') }}</p>
        @endforelse

        @if($more->isNotEmpty())
            <div class="container mt-stack">
                <h3 class="label mb-6 text-center" data-reveal>{{ __('site.work.more') }}</h3>
                <ul class="project-list read" data-reveal-group>
                    @foreach($more as $project)
                        <x-project-row :project="$project" />
                    @endforeach
                </ul>
            </div>
        @endif

        @if($projectCount > 0)
            <p class="container mt-12 text-center" data-reveal>
                <a href="{{ lroute('work') }}" class="btn-secondary">{{ __('site.work.all') }} <span class="tabular text-muted">{{ $projectCount }}</span></a>
            </p>
        @endif
    </section>

    {{-- About: a statement, one big photo, a few real facts. --}}
    <section id="about" class="section" aria-labelledby="about-title">
        <div class="container">
            <x-opener id="about-title" :label="__('site.about.label')">{{ __('site.about.title') }}</x-opener>

            <figure class="about-photo mt-stack" data-reveal>
                <picture>
                    @if($aboutPhoto)
                        <img src="{{ $aboutPhoto }}" alt="{{ __('site.about.photo_alt') }}" width="960" height="1200" loading="lazy" decoding="async" data-scrub-view="zoom-settle">
                    @else
                        <source type="image/avif" srcset="{{ asset('images/about/amir-640.avif') }} 640w, {{ asset('images/about/amir-960.avif') }} 960w, {{ asset('images/about/amir-1280.avif') }} 1280w" sizes="(min-width: 640px) 560px, 100vw">
                        <source type="image/webp" srcset="{{ asset('images/about/amir-640.webp') }} 640w, {{ asset('images/about/amir-960.webp') }} 960w, {{ asset('images/about/amir-1280.webp') }} 1280w" sizes="(min-width: 640px) 560px, 100vw">
                        <img src="{{ asset('images/about/amir-960.webp') }}" alt="{{ __('site.about.photo_alt') }}" width="960" height="1200" loading="lazy" decoding="async" data-scrub-view="zoom-settle">
                    @endif
                </picture>
            </figure>

            <div class="read mt-stack">
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

    {{-- Certificates: a quiet list. --}}
    @if($certificates->isNotEmpty())
        <section id="certificates" class="section" aria-labelledby="cert-title">
            <div class="container read">
                <h2 id="cert-title" class="statement text-center" data-scrub-view="dim-in">{{ __('site.certificates.title') }}</h2>
                <ul class="rule-list mt-stack" data-reveal-group>
                    @foreach($certificates->take(5) as $cert)
                        <x-certificate-row :cert="$cert" />
                    @endforeach
                </ul>
                @if($certificates->count() > 5)
                    <p class="mt-8 text-center"><a href="{{ lroute('certificates') }}" class="link-arrow"><span>{{ __('site.certificates.all') }}</span>{{ icon('arrow-right') }}</a></p>
                @endif
            </div>
        </section>
    @endif

    {{-- Notes: latest three as text rows. --}}
    @if($blogs->isNotEmpty())
        <section id="blog" class="section" aria-labelledby="notes-title">
            <div class="container read">
                <h2 id="notes-title" class="statement text-center" data-scrub-view="dim-in">{{ __('site.notes.title') }}</h2>
                <ul class="rule-list mt-stack" data-reveal-group>
                    @foreach($blogs as $blog)
                        <x-note-row :blog="$blog" />
                    @endforeach
                </ul>
                @if($blogCount > $blogs->count())
                    <p class="mt-8 text-center"><a href="{{ lroute('blog.index') }}" class="link-arrow"><span>{{ __('site.notes.all') }}</span>{{ icon('arrow-right') }}</a></p>
                @endif
            </div>
        </section>
    @endif

    {{-- Work with me: the path to Jebbari. --}}
    <section class="section" aria-label="{{ __('site.wwm.label') }}">
        <div class="container">
            <x-cta placement="home" class="read" data-reveal />
        </div>
    </section>

    {{-- Contact: the big address, then the form. --}}
    <section id="contact" class="section" aria-labelledby="contact-title">
        <div class="container">
            <x-opener id="contact-title" :label="__('site.contact.label')" :lead="__('site.contact.lead')">{{ __('site.contact.title') }}</x-opener>
            <p class="mt-10 text-center" data-reveal>
                <a href="mailto:{{ $email }}" class="contact-mail" style="--chars: {{ mb_strlen($email) }}">{{ $email }}</a>
            </p>
            @if(config('site.booking_url'))
                <p class="mt-6 text-center" data-reveal>
                    <a href="{{ config('site.booking_url') }}" class="btn-secondary" target="_blank" rel="noopener" data-track="booking_click" data-track-label="contact">{{ __('site.contact.booking') }} {{ icon('arrow-up-right') }}</a>
                </p>
            @endif
            <p class="label mt-stack text-center" data-reveal>{{ __('site.contact.or') }}</p>

            @include('partials.contact-form')
        </div>
    </section>
@endsection
