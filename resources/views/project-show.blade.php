@extends('layouts.layout', [
    'title' => $project->getTranslation('title').' — '.config('site.name'),
    'description' => $project->excerpt(200),
])

@section('nav')
    <x-site-nav />
@endsection

@section('content')
    <article class="px-4 pb-24 pt-28 md:pt-36">
        <div class="mx-auto max-w-5xl">
            <nav data-reveal class="flex flex-wrap items-center gap-2 text-sm text-ink-muted" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-ink-text">{{ __('messages.hero.crumb') }}</a>
                <span>›</span>
                <a href="{{ route('home') }}#work" class="hover:text-ink-text">{{ __('messages.nav.portfolio') }}</a>
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

            <div class="mt-14 grid gap-12 md:grid-cols-[200px_1fr]">
                <aside class="space-y-6 md:sticky md:top-28 md:h-fit">
                    <div>
                        <p class="label mb-2">{{ __('messages.portfolio.stack') }}</p>
                        <p class="text-sm leading-relaxed text-ink-soft">{{ implode(', ', $project->tagList()) }}</p>
                    </div>
                    @if($project->hasDemo())
                        <div>
                            <p class="label mb-2">URL</p>
                            <a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" class="break-all text-xs text-ink-soft hover:text-scope-accent">{{ parse_url($project->demo_url, PHP_URL_HOST) }} ↗</a>
                        </div>
                    @endif
                </aside>

                <div data-reveal class="max-w-2xl whitespace-pre-line text-base leading-relaxed text-ink-soft md:text-lg">{{ $project->getTranslation('description') }}</div>
            </div>

            <div class="mt-20 grid gap-4 border-t border-ink-line pt-10 md:grid-cols-2">
                <a href="{{ route('home') }}#work" class="panel group flex items-center gap-3 p-5 text-sm font-semibold transition-colors hover:border-ink-strong">
                    <span class="transition-transform group-hover:-translate-x-1">←</span> {{ __('messages.portfolio.more') }}
                </a>
                @if($next)
                    <a href="{{ route('project.show', $next) }}" class="panel spotlight group flex items-center gap-4 overflow-hidden p-3 transition-colors hover:border-ink-strong">
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
