@php($nl = app()->getLocale() === 'nl')
<button type="button" @click="switchLanguage(locale === 'nl' ? 'en' : 'nl')" class="relative inline-flex h-7 items-center rounded-lg border border-ink-line bg-ink-panel2 p-0.5 text-[10px] font-semibold" aria-label="{{ $nl ? 'Switch to English' : 'Schakel naar Nederlands' }}">
    <span class="absolute top-0.5 h-[calc(100%-4px)] w-8 rounded-md bg-scope-accent/15 ring-1 ring-scope-accent/40 {{ $nl ? 'translate-x-8' : '' }}"></span>
    <span class="relative z-10 w-8 text-center {{ $nl ? 'text-ink-muted' : 'text-scope-accent' }}">EN</span>
    <span class="relative z-10 w-8 text-center {{ $nl ? 'text-scope-accent' : 'text-ink-muted' }}">NL</span>
</button>
