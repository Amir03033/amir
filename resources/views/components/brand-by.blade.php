@props(['placement' => 'logo'])
{{-- The small line under the wordmark: "handgemaakt bij Jebbari" / "handmade at Jebbari", linking to jebbari.nl (with UTM). --}}
<a href="{{ jebbari_url('home', $placement) }}" class="brand-by" target="_blank" rel="noopener"
   data-track="cta_jebbari" data-track-label="{{ $placement }}">{{ __('site.brand_by.prefix') }} <span class="brand-by-name">Jebbari</span></a>
