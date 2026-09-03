@extends('layouts.layout', [
    'title' => __('messages.cv.title').' | '.config('site.name'),
    'description' => __('messages.cv.intro'),
])

@section('nav')
    <x-site-nav />
@endsection

@section('content')
    <div class="max-w-3xl mx-auto px-4 pt-28 pb-24">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-10">
            <div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-white">{{ config('site.name') }}</h1>
                <p class="text-cyan-400 mt-1">{{ __('messages.home.subtitle') }} · {{ config('site.location') }}</p>
                <p class="text-slate-400 text-sm mt-3 max-w-xl">{{ __('messages.cv.intro') }}</p>
            </div>
            <button type="button" onclick="window.print()" class="btn-secondary print:hidden">{{ __('messages.cv.print') }}</button>
        </div>

        <section class="mb-10">
            <h2 class="text-sm uppercase tracking-widest text-slate-500 mb-3">{{ __('messages.cv.education') }}</h2>
            <p class="text-slate-200">{{ __('messages.cv.education_item') }}</p>
        </section>

        <section class="mb-10">
            <h2 class="text-sm uppercase tracking-widest text-slate-500 mb-3">{{ __('messages.skills.title') }}</h2>
            <div class="flex flex-wrap gap-2">
                @foreach(__('messages.skills.groups') as $group)
                    @foreach($group['items'] as $item)
                        <span class="px-2.5 py-1 rounded-md border border-slate-800 text-xs text-slate-300">{{ $item }}</span>
                    @endforeach
                @endforeach
            </div>
        </section>

        <section class="mb-10">
            <h2 class="text-sm uppercase tracking-widest text-slate-500 mb-4">{{ __('messages.cv.projects') }}</h2>
            <div class="space-y-5">
                @foreach($projects as $project)
                    <div>
                        <h3 class="text-white font-semibold">{{ $project->getTranslation('title') }}</h3>
                        <p class="text-slate-400 text-sm mt-1">{{ $project->excerpt(220) }}</p>
                        <p class="text-cyan-400/80 text-xs font-mono mt-1">{{ implode(' · ', $project->tagList()) }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section>
            <h2 class="text-sm uppercase tracking-widest text-slate-500 mb-3">{{ __('messages.cv.contact') }}</h2>
            <p class="text-slate-300 text-sm leading-relaxed">
                <a class="text-cyan-400 hover:underline" href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a><br>
                <a class="text-cyan-400 hover:underline" href="tel:{{ config('site.phone_href') }}">{{ config('site.phone') }}</a><br>
                <a class="text-cyan-400 hover:underline" href="{{ config('site.linkedin') }}" target="_blank" rel="noopener noreferrer">LinkedIn</a>
            </p>
        </section>
    </div>
@endsection
