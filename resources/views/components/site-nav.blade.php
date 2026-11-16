@props(['onHome' => false, 'current' => null])

@php
    $home = lroute('home');
    $link = fn (string $hash) => $onHome ? '#'.$hash : $home.'#'.$hash;
    $items = [
        'work' => __('messages.nav.portfolio'),
        'about' => __('messages.nav.about'),
        'play' => __('messages.nav.play'),
        'contact' => __('messages.nav.contact'),
    ];
@endphp

<header class="site-nav" data-nav>
    <div class="container nav-inner">
        <div class="brand">
            <a href="{{ $home }}" class="wordmark" aria-label="amirs.life — {{ __('messages.nav.home') }}"><x-wordmark /></a>
            <x-brand-by placement="logo" />
        </div>

        <nav class="nav-links" aria-label="{{ __('messages.nav.main') }}">
            @foreach($items as $hash => $label)
                <a href="{{ $link($hash) }}" class="nav-link" data-nav-link="{{ $hash }}" @if($current === $hash) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
            <a href="{{ lroute('work-with-me') }}?via=nav" class="nav-link nav-link-cta" @if(request()->routeIs('work-with-me', 'en.work-with-me')) aria-current="page" @endif
               data-track="cta_click" data-track-label="nav">{{ __('site.wwm.nav') }}</a>
        </nav>

        <div class="nav-tools">
            <x-theme-toggle />
            <x-lang-switch />
            <button type="button" class="nav-menu-btn" data-nav-open aria-haspopup="dialog" aria-controls="nav-sheet">{{ __('messages.nav.menu') }}</button>
        </div>
    </div>
</header>

<dialog id="nav-sheet" class="nav-sheet" aria-label="{{ __('messages.nav.menu') }}">
    <div class="container nav-sheet-head">
        <div class="brand">
            <a href="{{ $home }}" class="wordmark"><x-wordmark /></a>
            <x-brand-by placement="logo" />
        </div>
        <button type="button" class="btn-icon" data-nav-close aria-label="{{ __('messages.nav.close') }}">{{ icon('x') }}</button>
    </div>
    <nav class="container nav-sheet-links" aria-label="{{ __('messages.nav.main') }}">
        @foreach($items as $hash => $label)
            <a href="{{ $link($hash) }}" style="--i: {{ $loop->index }}" data-nav-close>{{ $label }}</a>
        @endforeach
        <a href="{{ lroute('blog.index') }}" style="--i: {{ count($items) }}">{{ __('messages.nav.blog') }}</a>
        <a href="{{ lroute('work-with-me') }}?via=nav" style="--i: {{ count($items) + 1 }}" class="nav-sheet-cta" data-track="cta_click" data-track-label="nav">{{ __('site.wwm.nav') }}</a>
    </nav>
    <div class="container nav-sheet-foot">
        <x-lang-switch />
        <x-theme-toggle :with-label="true" />
    </div>
</dialog>
