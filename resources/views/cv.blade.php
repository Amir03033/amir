@extends('layouts.layout', [
    'title' => __('messages.cv.title').' — '.config('site.name'),
    'description' => __('messages.cv.intro'),
])

@section('nav')
    <x-site-nav />
@endsection

@section('content')
    <div class="px-4 pb-24 pt-28 md:pt-36">
        <div class="mx-auto max-w-3xl">
            <header class="flex flex-wrap items-start justify-between gap-6 border-b border-ink-line pb-10">
                <div>
                    <p class="label mb-3" data-reveal>{{ __('messages.cv.title') }}</p>
                    <h1 data-split class="text-4xl font-semibold tracking-tight md:text-5xl">{{ config('site.name') }}</h1>
                    <p data-reveal class="mt-2 text-scope-accent">{{ __('messages.home.subtitle') }} · {{ config('site.location') }}</p>
                    <p data-reveal class="mt-4 max-w-xl text-sm leading-relaxed text-ink-soft">{{ __('messages.cv.intro') }}</p>
                </div>
                <div class="no-print flex flex-wrap gap-2">
                    <a href="{{ route('cv.pdf') }}" data-magnetic class="btn-primary">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 4v11m0 0-4-4m4 4 4-4M5 20h14"/></svg>
                        {{ __('messages.cv.download') }}
                    </a>
                    <button type="button" onclick="window.print()" class="btn-secondary">{{ __('messages.cv.print') }}</button>
                </div>
            </header>

            @foreach([
                'education' => null,
                'skills' => null,
                'projects' => null,
                'contact' => null,
            ] as $block => $_)
                <section data-reveal class="grid gap-4 border-b border-ink-line py-10 md:grid-cols-[180px_1fr]">
                    <h2 class="text-[11px] uppercase tracking-[.16em] text-ink-muted">
                        {{ $block === 'skills' ? __('messages.skills.label') : __('messages.cv.'.$block) }}
                    </h2>

                    <div>
                        @switch($block)
                            @case('education')
                                <p>{{ __('messages.cv.education_item') }}</p>
                                @break

                            @case('skills')
                                <div class="space-y-3">
                                    @foreach(__('messages.skills.groups') as $group)
                                        <p class="text-sm"><span class="text-ink-muted">{{ $group['label'] }}:</span> <span class="text-ink-soft">{{ implode(', ', $group['items']) }}</span></p>
                                    @endforeach
                                </div>
                                @break

                            @case('projects')
                                <div class="space-y-7">
                                    <div>
                                        <h3 class="font-semibold">ServerScope</h3>
                                        <p class="mt-1 text-sm leading-relaxed text-ink-soft">{{ __('messages.serverscope.tagline') }}</p>
                                        <p class="mt-1.5 text-xs text-scope-accent">{{ implode(' · ', __('messages.serverscope.stack')) }}</p>
                                    </div>
                                    @foreach($projects as $project)
                                        <div>
                                            <h3 class="font-semibold">{{ $project->getTranslation('title') }}</h3>
                                            <p class="mt-1 text-sm leading-relaxed text-ink-soft">{{ $project->excerpt(220) }}</p>
                                            <p class="mt-1.5 text-xs text-scope-accent">{{ implode(' · ', $project->tagList()) }}</p>
                                        </div>
                                    @endforeach
                                </div>
                                @break

                            @case('contact')
                                <div class="space-y-1.5 text-sm">
                                    <a class="block text-ink-soft hover:text-scope-accent" href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>
                                    @if(config('site.phone'))
                                        <a class="block text-ink-soft hover:text-scope-accent" href="tel:{{ config('site.phone_href') }}">{{ config('site.phone') }}</a>
                                    @endif
                                    <a class="block text-ink-soft hover:text-scope-accent" href="{{ config('site.linkedin') }}" target="_blank" rel="noopener noreferrer">LinkedIn</a>
                                    @if(config('site.github'))
                                        <a class="block text-ink-soft hover:text-scope-accent" href="{{ config('site.github') }}" target="_blank" rel="noopener noreferrer">GitHub</a>
                                    @endif
                                </div>
                                @break
                        @endswitch
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection
