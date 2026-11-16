@props(['placement'])
{{--
    The "Work with me" call to action, the same everywhere (home, end of case studies, end of notes).
    Clicks are logged per placement: cta_click (to the Work-with-me page) and cta_jebbari (straight to jebbari.nl).
--}}
<aside {{ $attributes->merge(['class' => 'cta']) }} aria-labelledby="cta-{{ $placement }}">
    <p class="label">{{ __('site.wwm.label') }}</p>
    <h2 id="cta-{{ $placement }}" class="text-title mt-3 font-semibold">{{ __('site.cta.title') }}</h2>
    <p class="mt-3 text-fg-2">{{ __('site.cta.text') }}</p>
    <div class="mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-4">
        <a href="{{ lroute('work-with-me') }}?via={{ $placement }}" class="btn-primary"
           data-track="cta_click" data-track-label="{{ $placement }}">{{ __('site.cta.primary') }} {{ icon('arrow-right') }}</a>
        <a href="{{ jebbari_url('request', $placement) }}" class="link-arrow" rel="noopener"
           data-track="cta_jebbari" data-track-label="{{ $placement }}"><span>{{ __('site.cta.direct') }}</span>{{ icon('arrow-up-right', 'icon-diag') }}</a>
    </div>
</aside>
