@extends('layouts.layout')

@section('content')
<div class="min-h-screen flex items-center justify-center px-6 relative overflow-hidden">

    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 text-center max-w-xl">
        <p class="font-mono text-cyan-400 text-sm tracking-widest mb-4">ERROR // NOT_FOUND</p>

        <h1 class="font-mono text-7xl sm:text-9xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-slate-200 to-amber-400 mb-6 tracking-tight">
            404
        </h1>

        <h2 class="text-2xl sm:text-3xl font-bold text-slate-100 mb-4">
            Deze pagina bestaat niet
        </h2>

        <p class="text-slate-400 mb-10 leading-relaxed">
            De pagina die je zoekt is verplaatst, verwijderd, of heeft nooit bestaan.
            Controleer de URL of ga terug naar de homepage.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/') }}"
               class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-cyan-500 text-black font-semibold hover:bg-cyan-400 transition-colors">
                Terug naar home
            </a>
            <button onclick="history.back()"
               class="inline-flex items-center justify-center px-6 py-3 rounded-lg border border-slate-700 text-slate-300 font-semibold hover:border-cyan-400 hover:text-cyan-400 transition-colors">
                Vorige pagina
            </button>
        </div>

        <p class="font-mono text-xs text-slate-600 mt-16">
            {{ url()->current() }}
        </p>
    </div>
</div>
@endsection
