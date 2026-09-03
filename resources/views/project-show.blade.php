@extends('layouts.layout', [
    'title' => $project->getTranslation('title').' | '.config('site.name'),
    'description' => $project->excerpt(200),
])

@section('nav')
    <x-site-nav />
@endsection

@section('content')
    <article class="max-w-3xl mx-auto px-4 pt-28 pb-24">
        <a href="{{ route('home') }}#portfolio" class="text-xs uppercase tracking-widest font-mono text-cyan-400 hover:underline">← {{ __('messages.nav.back') }}</a>

        <div class="mt-8 rounded-2xl overflow-hidden border border-slate-800 h-64 md:h-80 bg-slate-950">
            <img src="{{ $project->coverUrl() }}" alt="{{ $project->getTranslation('title') }}" class="w-full h-full object-cover">
        </div>

        <h1 class="text-3xl md:text-5xl font-extrabold text-white tracking-tight mt-8">{{ $project->getTranslation('title') }}</h1>

        <div class="flex flex-wrap gap-2 mt-4">
            @foreach($project->tagList() as $tag)
                <span class="px-2.5 py-1 rounded-md border border-slate-800 text-xs font-mono text-cyan-400/90">{{ $tag }}</span>
            @endforeach
        </div>

        <div class="flex flex-wrap gap-3 mt-6">
            @if($project->hasDemo())
                <a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" class="btn-primary">{{ __('messages.portfolio.live') }}</a>
            @endif
            @if($project->hasGithub())
                <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="btn-secondary">{{ __('messages.portfolio.github') }}</a>
            @endif
        </div>

        <div class="mt-10 space-y-4 text-slate-300 leading-relaxed text-sm md:text-base whitespace-pre-line border-t border-slate-800 pt-8">
            {{ $project->getTranslation('description') }}
        </div>
    </article>
@endsection
