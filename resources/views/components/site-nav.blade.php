@props(['onHome' => false])

@php
    $link = fn (string $hash) => $onHome ? '#'.$hash : route('home').'#'.$hash;
@endphp

<nav class="fixed top-0 left-0 w-full z-50 border-b border-slate-800/80 bg-cyber-bg/80 backdrop-blur-md">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
        <a href="{{ $link('home') }}" class="text-lg font-mono font-bold tracking-wider bg-gradient-to-r from-cyan-400 to-violet-500 bg-clip-text text-transparent shrink-0">AMIR</a>

        <div class="hidden md:flex items-center gap-6 text-xs uppercase tracking-widest font-mono text-slate-400">
            <a href="{{ $link('home') }}" class="hover:text-cyan-400 transition">{{ __('messages.nav.home') }}</a>
            <a href="{{ $link('about') }}" class="hover:text-cyan-400 transition">{{ __('messages.nav.about') }}</a>
            <a href="{{ $link('skills') }}" class="hover:text-cyan-400 transition">{{ __('messages.nav.skills') }}</a>
            <a href="{{ $link('portfolio') }}" class="hover:text-cyan-400 transition">{{ __('messages.nav.portfolio') }}</a>
            <a href="{{ $link('blog') }}" class="hover:text-cyan-400 transition">{{ __('messages.nav.blog') }}</a>
            <a href="{{ $link('contact') }}" class="hover:text-cyan-400 transition">{{ __('messages.nav.contact') }}</a>
            <a href="{{ route('cv') }}" class="hover:text-cyan-400 transition">{{ __('messages.nav.cv') }}</a>
        </div>

        <div class="flex items-center gap-3">
            <x-lang-switch />
            <button type="button" class="md:hidden p-2 text-slate-300 hover:text-white" @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen.toString()" aria-controls="mobile-nav">
                <span class="sr-only" x-text="mobileOpen ? @js(__('messages.nav.close')) : @js(__('messages.nav.menu'))"></span>
                <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16M4 12h16M4 17h16"/></svg>
                <svg x-cloak x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <div id="mobile-nav" x-cloak x-show="mobileOpen" x-transition class="md:hidden border-t border-slate-800 bg-cyber-bg/95">
        <div class="px-4 py-4 flex flex-col gap-3 text-sm uppercase tracking-widest font-mono text-slate-300">
            <a href="{{ $link('home') }}" @click="mobileOpen = false" class="py-1">{{ __('messages.nav.home') }}</a>
            <a href="{{ $link('about') }}" @click="mobileOpen = false" class="py-1">{{ __('messages.nav.about') }}</a>
            <a href="{{ $link('skills') }}" @click="mobileOpen = false" class="py-1">{{ __('messages.nav.skills') }}</a>
            <a href="{{ $link('portfolio') }}" @click="mobileOpen = false" class="py-1">{{ __('messages.nav.portfolio') }}</a>
            <a href="{{ $link('blog') }}" @click="mobileOpen = false" class="py-1">{{ __('messages.nav.blog') }}</a>
            <a href="{{ $link('contact') }}" @click="mobileOpen = false" class="py-1">{{ __('messages.nav.contact') }}</a>
            <a href="{{ route('cv') }}" @click="mobileOpen = false" class="py-1">{{ __('messages.nav.cv') }}</a>
        </div>
    </div>
</nav>
