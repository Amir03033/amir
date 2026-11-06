@props(['onHome' => false])

@php
    $link = fn (string $hash) => $onHome ? '#'.$hash : route('home').'#'.$hash;
    $items = [
        'work' => __('messages.nav.portfolio'),
        'serverscope' => 'ServerScope',
        'stack' => __('messages.nav.skills'),
        'about' => __('messages.nav.about'),
        'proof' => __('messages.nav.proof'),
        'blog' => __('messages.nav.blog'),
        'contact' => __('messages.nav.contact'),
    ];
@endphp

<nav data-nav class="group/nav fixed inset-x-0 top-0 z-50 transition-all duration-300">
    <div class="mx-auto mt-0 flex h-16 max-w-6xl items-center justify-between gap-4 border-b border-transparent px-4 transition-all duration-300 group-data-[scrolled]/nav:mt-3 group-data-[scrolled]/nav:h-12 group-data-[scrolled]/nav:max-w-5xl group-data-[scrolled]/nav:rounded-panel group-data-[scrolled]/nav:border group-data-[scrolled]/nav:border-ink-line group-data-[scrolled]/nav:bg-ink-bg/90 group-data-[scrolled]/nav:shadow-[0_10px_40px_-15px_rgb(0_0_0/.8)] group-data-[scrolled]/nav:backdrop-blur-md md:px-5">
        <a href="{{ $link('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="{{ config('site.name') }} — home">
            <svg class="h-6 w-6 text-scope-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12h4l3-8 4 16 3-8h6"/></svg>
            <span class="hidden leading-tight sm:block">
                <span class="block text-sm font-semibold tracking-tight">{{ config('site.name') }}</span>
                <span class="block text-[11px] text-ink-muted">{{ __('messages.home.subtitle') }}</span>
            </span>
        </a>

        <div class="relative hidden items-center xl:flex">
            <span data-nav-marker class="absolute inset-y-0 left-0 rounded-lg bg-ink-panel opacity-0 ring-1 ring-ink-line transition-[transform,width,opacity] duration-300 ease-out"></span>
            @foreach($items as $hash => $label)
                <a href="{{ $link($hash) }}" @if($onHome) data-nav-link="{{ $hash }}" @endif class="relative whitespace-nowrap rounded-lg px-2.5 py-1.5 text-[13px] text-ink-soft transition-colors hover:text-ink-text data-[active]:text-ink-text">{{ $label }}</a>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="$dispatch('open-palette')" class="hidden items-center gap-2 rounded-xl border border-ink-line bg-ink-panel px-3 py-1.5 text-[13px] text-ink-muted transition-colors hover:border-ink-strong hover:text-ink-soft sm:flex" aria-label="{{ __('messages.palette.open') }}">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                {{ __('messages.palette.open') }}
                <span class="kbd">⌘ K</span>
            </button>
            <button type="button" @click="$dispatch('open-palette')" class="rounded-lg p-2 text-ink-soft hover:text-ink-text sm:hidden" aria-label="{{ __('messages.palette.open') }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            </button>
            <button type="button" onclick="toggleTheme()" class="rounded-lg p-2 text-ink-soft transition-colors hover:text-ink-text" aria-label="{{ __('messages.theme.toggle') }}" title="{{ __('messages.theme.toggle') }}">
                <svg class="h-[18px] w-[18px] [[data-theme=light]_&]:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5Z"/></svg>
                <svg class="hidden h-[18px] w-[18px] [[data-theme=light]_&]:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
            </button>
            <x-lang-switch />
            <button type="button" class="rounded-lg p-2 text-ink-soft hover:text-ink-text xl:hidden" @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen.toString()" aria-controls="mobile-nav">
                <span class="sr-only" x-text="mobileOpen ? @js(__('messages.nav.close')) : @js(__('messages.nav.menu'))">{{ __('messages.nav.menu') }}</span>
                <svg x-show="!mobileOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h10"/></svg>
                <svg x-cloak x-show="mobileOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <div id="mobile-nav" x-cloak x-show="mobileOpen" x-transition.opacity.duration.200ms @keydown.escape.window="mobileOpen = false" class="mx-4 mt-2 rounded-panel border border-ink-line bg-ink-bg/95 p-3 backdrop-blur-md xl:hidden">
        @foreach($items as $hash => $label)
            <a href="{{ $link($hash) }}" @click="mobileOpen = false" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm text-ink-soft hover:bg-ink-panel hover:text-ink-text">
                {{ $label }} <span class="text-[11px] text-ink-muted">0{{ $loop->iteration }}</span>
            </a>
        @endforeach
        <a href="{{ route('cv') }}" class="mt-1 flex rounded-lg px-3 py-2.5 text-sm text-scope-accent hover:bg-ink-panel">{{ __('messages.nav.cv') }} →</a>
    </div>
</nav>
