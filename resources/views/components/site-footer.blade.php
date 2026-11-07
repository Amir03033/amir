@php
    $github = config('site.github');
@endphp

<footer class="relative border-t border-ink-line">
    <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-10 text-xs text-ink-muted md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-3">
            <span class="dot bg-scope-green"></span>
            <span>&copy; {{ date('Y') }} {{ config('site.name') }} · {{ __('messages.footer.line') }}</span>
        </div>
        <div class="flex flex-wrap gap-x-5 gap-y-2">
            <a href="mailto:{{ config('site.email') }}" class="transition-colors hover:text-scope-accent">E-mail</a>
            <a href="{{ config('site.linkedin') }}" target="_blank" rel="noopener noreferrer" class="transition-colors hover:text-scope-accent">LinkedIn</a>
            @if($github)
                <a href="{{ $github }}" target="_blank" rel="noopener noreferrer" class="transition-colors hover:text-scope-accent">GitHub</a>
            @endif
            @if(config('site.serverscope.url'))
                <a href="{{ config('site.serverscope.url') }}" target="_blank" rel="noopener" class="transition-colors hover:text-scope-accent">ServerScope</a>
            @endif
            <a href="{{ lroute('cv') }}" class="transition-colors hover:text-scope-accent">{{ __('messages.nav.cv') }}</a>
            <a href="{{ lroute('privacy') }}" class="transition-colors hover:text-scope-accent">{{ __('messages.footer.privacy') }}</a>
        </div>
    </div>
</footer>
