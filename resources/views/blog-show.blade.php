@extends('layouts.layout', [
    'title' => $blog->getTranslation('title').' | '.config('site.name'),
    'description' => $blog->excerpt(200),
])

@section('nav')
    <x-site-nav />
@endsection

@section('content')
    <article class="max-w-3xl mx-auto px-4 pt-28 pb-24">
        <a href="{{ route('home') }}#blog" class="text-xs uppercase tracking-widest font-mono text-cyan-400 hover:underline">← {{ __('messages.nav.back') }}</a>

        <p class="mt-8 text-xs font-mono text-slate-500">{{ __('messages.blog.written') }} {{ $blog->created_at->format('d M Y') }}</p>
        <h1 class="text-3xl md:text-5xl font-extrabold text-white tracking-tight mt-2">
            {{ $blog->getTranslation('title') }}
        </h1>

        @if($blog->image)
            <div class="mt-8 w-full h-64 md:h-96 bg-slate-950 rounded-2xl overflow-hidden border border-slate-800">
                <img src="{{ asset('storage/'.$blog->image) }}" alt="" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="prose prose-invert prose-sm md:prose-base max-w-none mt-10 text-slate-300 leading-relaxed border-t border-slate-800 pt-8 prose-a:text-cyan-400">
            {!! Str::markdown($blog->getTranslation('content')) !!}
        </div>
    </article>
@endsection
