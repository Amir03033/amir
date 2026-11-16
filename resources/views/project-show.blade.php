@php
    $title = $project->getTranslation('title');
    $role = $project->getTranslation('role');
    $blocks = $project->caseBlocks();
    $gallery = $project->galleryUrls();
@endphp

@extends('layouts.layout', [
    'title' => \App\Support\Seo::projectTitle($project),
    'description' => \App\Support\Seo::projectDescription($project),
    'ogImage' => \App\Support\Seo::ogImage('project', $project->id),
    'ogType' => 'article',
    'crumbs' => [[__('seo.breadcrumbs.work'), lroute('work')], [$title, \App\Support\Seo::canonical()]],
    'schema' => [\App\Support\Seo::project($project, \App\Support\Seo::canonical())],
    'mainEntity' => \App\Support\Seo::canonical().'#project',
    'draft' => $project->is_published ? null : $project->reviewMarkers(),
])


@section('content')
    <article>
        <header class="page-head container">
            <a href="{{ lroute('work') }}" class="crumb" data-hero-line style="--i: 0">{{ icon('arrow-left') }} {{ __('site.case.crumb') }}</a>
            <h1 class="hero-type mx-auto mt-6 max-w-[14ch] !text-[calc(var(--t-hero)*.75)]" data-hero-line style="--i: 1">{{ $title }}</h1>
            <p class="lead mx-auto mt-6 max-w-2xl" data-hero-line style="--i: 2">{{ $project->oneLiner() }}</p>
        </header>

        <div class="container">
            <dl class="case-meta read !max-w-content" data-reveal>
                {{-- Empty facts are hidden in production (no empty labels in indexable pages); placeholders show elsewhere. --}}
                @if($project->year || ! app()->isProduction())
                    <div><dt class="label">{{ __('site.work.year') }}</dt><dd class="tabular">{{ $project->year ?: '' }}@unless($project->year)<x-ph :label="__('site.work.year')" />@endunless</dd></div>
                @endif
                @if($role || ! app()->isProduction())
                    <div><dt class="label">{{ __('site.work.role') }}</dt><dd>{{ $role }}@unless($role)<x-ph :label="__('site.work.role')" />@endunless</dd></div>
                @endif
                @if($project->status)
                    <div><dt class="label">{{ __('site.case.status') }}</dt><dd>{{ __('site.case.status_'.$project->status) }}</dd></div>
                @endif
                <div><dt class="label">{{ __('site.work.stack') }}</dt><dd>{{ implode(', ', $project->tagList()) }}</dd></div>
                <div><dt class="label">{{ __('site.work.link') }}</dt><dd>
                    @if($project->hasDemo())
                        <a href="{{ $project->demo_url }}" class="link" target="_blank" rel="noopener">{{ $project->host() }}</a>
                    @elseif($project->hasGithub())
                        <a href="{{ $project->github_url }}" class="link" target="_blank" rel="noopener">GitHub</a>
                    @else — @endif
                </dd></div>
            </dl>

            @if($jebbari = $project->jebbariUrl())
                {{-- Built with Jebbari: link the agency and its page about this project (anchor text "Jebbari"). --}}
                <p class="mt-6 text-center text-small text-muted" data-reveal>
                    {!! __('seo.jebbari.built_with', ['jebbari' => '<a href="'.e(config('seo.company.url')).'" class="link" rel="noopener">Jebbari</a>']) !!}
                    <a href="{{ $jebbari }}" class="link-arrow ml-2 text-small" rel="noopener"><span>{!! __('seo.jebbari.see_on', ['jebbari' => 'Jebbari']) !!}</span>{{ icon('arrow-up-right', 'icon-diag') }}</a>
                </p>
            @endif

            <figure class="case-hero mt-stack" data-reveal>
                <x-project-cover :project="$project" :eager="true" sizes="(min-width: 1280px) 1200px, 100vw" data-scrub-view="zoom-settle" />
            </figure>
        </div>

        <div class="container section !pb-0">
            <div class="read">
                @if($blocks)
                    @foreach($blocks as $key => $text)
                        <section class="case-block" data-reveal>
                            <h2 class="label">{{ __('site.case.'.$key) }}</h2>
                            {{-- Light Markdown (paragraphs, lists, bold); HTML in the text is escaped. --}}
                            <div class="case-text mt-4">{{ review_marks(Str::markdown($text, ['html_input' => 'escape', 'allow_unsafe_links' => false])) }}</div>
                        </section>
                    @endforeach
                @else
                    <section class="case-block" data-reveal>
                        <h2 class="label">{{ __('site.case.about') }}</h2>
                        <p class="mt-4 whitespace-pre-line text-lead text-fg-2">{{ $project->getTranslation('description') }}</p>
                        <x-ph :label="__('site.case.missing_case')" :block="true" class="mt-8" />
                    </section>
                @endif

                @foreach($project->testimonials()->visible()->get() as $t)
                    <figure class="case-block" data-reveal>
                        <blockquote class="quote">“{{ $t->getTranslation('quote') }}”</blockquote>
                        <figcaption class="mt-4 text-small text-muted">— {{ $t->name }}@if($t->role || $t->company), {{ collect([$t->role, $t->company])->filter()->implode(', ') }}@endif</figcaption>
                    </figure>
                @endforeach

                @if($uptime !== null)
                    <section class="case-block" data-reveal>
                        <h2 class="label">{{ __('site.case.uptime') }}</h2>
                        <p class="mt-4 text-title font-semibold tabular">{{ rtrim(rtrim(number_format($uptime, 2), '0'), '.') }}%</p>
                        <x-uptime-bar :days="$uptimeDays" class="mt-6" />
                        <p class="mt-4 text-small text-muted">
                            {{ __('site.case.uptime_note') }}
                            @if($measuredSince) {{ __('site.case.since', ['date' => $measuredSince->translatedFormat('j M Y')]) }}. @endif
                            <a href="{{ lroute('status') }}" class="link">{{ __('messages.status.full_page') }}</a>
                        </p>
                    </section>
                @endif

                @if($project->hasDemo() || $project->hasGithub())
                    <div class="case-block flex flex-wrap gap-3" data-reveal>
                        @if($project->hasDemo())
                            <a href="{{ $project->demo_url }}" class="btn-primary" target="_blank" rel="noopener">{{ __('site.case.visit') }} {{ icon('arrow-up-right') }}</a>
                        @endif
                        @if($project->hasGithub())
                            <a href="{{ $project->github_url }}" class="btn-secondary" target="_blank" rel="noopener">{{ __('site.case.source') }}</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if($gallery)
            <section class="container section" aria-labelledby="gallery-title" x-data="{ i: 0, items: @js($gallery), show(n) { this.i = n; this.$refs.dlg.showModal(); } }">
                <h2 id="gallery-title" class="label mb-10 text-center" data-reveal>{{ __('site.case.gallery') }}</h2>
                <div class="gallery" data-reveal-group>
                    @foreach($gallery as $n => $src)
                        <button type="button" data-reveal @click="show({{ $n }})" aria-label="{{ $project->galleryAlt($n) }}" @if((image_size($src)[1] ?? 0) > (image_size($src)[0] ?? 1)) data-portrait @endif>
                            @php([$gw, $gh] = image_size($src) ?? [1600, 1000])
                            <img src="{{ $src }}" width="{{ $gw }}" height="{{ $gh }}" alt="{{ $project->galleryAlt($n) }}" loading="lazy" decoding="async" @if($gh > $gw) data-portrait @endif>
                        </button>
                    @endforeach
                </div>
                <dialog x-ref="dlg" class="dialog !w-[min(calc(100vw-32px),1200px)] !max-w-none" aria-label="{{ __('site.case.gallery') }}"
                        @keydown.arrow-right="i = (i + 1) % items.length" @keydown.arrow-left="i = (i - 1 + items.length) % items.length"
                        @click="$event.target === $el && $el.close()">
                    <div class="dialog-head"><p class="label tabular" x-text="(i + 1) + ' / ' + items.length"></p><button type="button" class="btn-icon" @click="$refs.dlg.close()" aria-label="{{ __('messages.nav.close') }}">{{ icon('x') }}</button></div>
                    <div class="dialog-body"><img :src="items[i]" alt="" class="w-full rounded-1"></div>
                </dialog>
            </section>
        @endif

        @if($previous && $next)
            <nav class="pager mt-section" aria-label="{{ __('site.work.label') }}">
                <a href="{{ lroute('project.show', $previous) }}" rel="prev">
                    <span class="label">← {{ __('site.case.previous') }}</span>
                    <span class="mt-3 block text-title font-semibold">{{ $previous->getTranslation('title') }}</span>
                </a>
                <a href="{{ lroute('project.show', $next) }}" rel="next">
                    <span class="label">{{ __('site.case.next') }} →</span>
                    <span class="mt-3 block text-title font-semibold">{{ $next->getTranslation('title') }}</span>
                </a>
            </nav>
        @endif
    </article>
@endsection
