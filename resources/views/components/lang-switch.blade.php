@php($current = app()->getLocale())
<div class="lang-switch" role="group" aria-label="{{ __('messages.nav.language') }}">
    <a href="{{ alternate_url('nl') }}" hreflang="nl" lang="nl" @if($current === 'nl') aria-current="true" @endif>NL</a>
    <span class="sep" aria-hidden="true">/</span>
    <a href="{{ alternate_url('en') }}" hreflang="en" lang="en" @if($current === 'en') aria-current="true" @endif>EN</a>
</div>
