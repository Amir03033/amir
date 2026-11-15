@props(['mail' => true])
@php
    $email = config('site.email');
    $jebbari = config('site.jebbari.url');
@endphp

<footer class="site-footer">
    <div class="container text-center">
        @if($mail)
            <p class="label">{{ __('messages.footer.write') }}</p>
            <a href="mailto:{{ $email }}" class="footer-mail mt-4" style="--chars: {{ mb_strlen($email) }}">{{ $email }}</a>
        @endif

        <div class="footer-row text-left {{ $mail ? '' : 'mt-0 border-t-0 pt-0' }}">
            <nav class="footer-links" aria-label="{{ __('messages.footer.nav') }}">
                <a href="{{ lroute('home') }}#work">{{ __('messages.nav.portfolio') }}</a>
                <a href="{{ lroute('blog.index') }}">{{ __('messages.nav.blog') }}</a>
                <a href="{{ lroute('roadmap') }}">Roadmap</a>
                <a href="{{ lroute('ask') }}">{{ __('site.ask.label') }}</a>
                <a href="{{ lroute('cv') }}">{{ __('messages.nav.cv') }}</a>
                <a href="{{ lroute('status') }}">{{ __('messages.footer.status') }}</a>
                <a href="{{ lroute('privacy') }}">{{ __('messages.footer.privacy') }}</a>
                <a href="{{ config('site.linkedin') }}" rel="me noopener" target="_blank">LinkedIn</a>
                @if(config('site.github'))
                    <a href="{{ config('site.github') }}" rel="me noopener" target="_blank">GitHub</a>
                @endif
            </nav>
            <div class="flex items-center gap-2">
                <x-lang-switch />
                <x-theme-toggle />
            </div>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-x-8 gap-y-2 text-left text-[.875rem] text-muted">
            <p>{{ __('messages.footer.line') }}</p>
            <p>
                @if($jebbari)
                    {{ __('messages.footer.agency') }} <a href="{{ $jebbari }}" class="link" rel="noopener" target="_blank">Jebbari</a> ·
                @endif
                &copy; {{ date('Y') }} {{ config('site.name') }}
            </p>
        </div>
    </div>
</footer>
