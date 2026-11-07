@php($nl = app()->getLocale() === 'nl')
{{-- Real links to the same page in the other language (no cookie), so search engines see both versions. --}}
<nav class="relative inline-flex h-7 items-center rounded-lg border border-ink-line bg-ink-panel2 p-0.5 text-[10px] font-semibold" aria-label="{{ $nl ? 'Taal' : 'Language' }}">
    <span class="pointer-events-none absolute top-0.5 h-[calc(100%-4px)] w-8 rounded-md bg-scope-accent/15 ring-1 ring-scope-accent/40 transition-transform duration-300 {{ $nl ? 'translate-x-8' : '' }}" aria-hidden="true"></span>
    <a href="{{ alternate_url('en') }}" hreflang="en" lang="en" class="relative z-10 w-8 text-center {{ $nl ? 'text-ink-muted hover:text-ink-text' : 'text-scope-accent' }}" @unless($nl) aria-current="true" @endunless>EN</a>
    <a href="{{ alternate_url('nl') }}" hreflang="nl" lang="nl" class="relative z-10 w-8 text-center {{ $nl ? 'text-scope-accent' : 'text-ink-muted hover:text-ink-text' }}" @if($nl) aria-current="true" @endif>NL</a>
</nav>
