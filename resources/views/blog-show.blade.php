@extends('layouts.layout')

@section('nav')
    <nav class="fixed top-0 left-0 w-full z-50 backdrop-blur-md border-b border-slate-900/50 bg-cyber-bg/70">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-xs uppercase tracking-widest font-mono text-cyan-400 hover:underline flex items-center gap-2">
                <- <span x-text="messages.nav.back"></span>
            </a>
            <button @click="switchLanguage(locale === 'nl' ? 'en' : 'nl')" class="relative inline-flex items-center justify-between w-14 h-7 p-1 rounded-full bg-slate-900 border border-slate-800 cursor-pointer">
                <span class="text-[9px] font-bold z-10 pl-1" :class="locale === 'nl' ? 'text-slate-900' : 'text-slate-500'">EN</span>
                <span class="text-[9px] font-bold z-10 pr-1" :class="locale === 'en' ? 'text-slate-900' : 'text-slate-500'">NL</span>
                <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-gradient-to-r from-cyan-500 to-blue-600 transition-transform duration-300" :class="locale === 'nl' ? 'transform translate-x-7' : ''"></span>
            </button>
        </div>
    </nav>
@endsection

@section('content')
    <article class="max-w-3xl mx-auto px-4 pt-32 pb-24 space-y-6">
        <div class="space-y-2 font-mono">
            <span class="text-xs text-cyan-400">// LOG_ENTRY: {{ $blog->created_at->format('d.m.Y') }}</span>
            <h1 class="text-3xl md:text-5xl font-extrabold text-white tracking-tight">
                {{ $blog->getTranslation('title') }}
            </h1>
        </div>

        @if($blog->image)
            <div class="w-full h-64 md:h-96 bg-slate-950 rounded-2xl overflow-hidden border border-slate-900 shadow-2xl">
                <img src="{{ asset('storage/' . $blog->image) }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="prose prose-invert max-w-none text-slate-300 leading-relaxed space-y-4 text-sm md:text-base whitespace-pre-line border-t border-slate-900 pt-6">
            {{ $blog->getTranslation('content') }}
        </div>
    </article>
@endsection