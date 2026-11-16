@props(['placement' => 'logo'])
{{-- The small line under the wordmark: "handgemaakt bij Jebbari" / "handmade at Jebbari". Only "Jebbari" is the link (consistent anchor text, with UTM). --}}
<span class="brand-by">{{ __('site.brand_by.prefix') }} <a href="{{ jebbari_url('home', $placement) }}" class="brand-by-name" target="_blank" rel="noopener"
   data-track="cta_jebbari" data-track-label="{{ $placement }}">Jebbari</a></span>
