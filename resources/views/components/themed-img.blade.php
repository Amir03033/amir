@props(['light', 'dark' => null, 'alt' => '', 'eager' => false, 'sizes' => null])
{{--
    One image per theme. When both are the same (no separate dark upload), a single <img> is rendered.
    Otherwise two, and CSS hides the one that doesn't match the active theme (html[data-theme]); the
    hidden one has loading="lazy" + display:none, so the browser doesn't download it.
--}}
@php
    $dark = $dark ?: $light;
    $versions = $light === $dark ? ['both' => $light] : ['light' => $light, 'dark' => $dark];
@endphp
@foreach($versions as $theme => $src)
    @php([$w, $h] = image_size($src) ?? [1600, 1000])
    <img src="{{ $src }}" width="{{ $w }}" height="{{ $h }}" alt="{{ $alt }}"
         {{ $attributes->class([$theme !== 'both' ? 'only-'.$theme : null]) }}
         @if($sizes) sizes="{{ $sizes }}" @endif decoding="async"
         @if($eager && $theme !== 'dark') fetchpriority="high" @else loading="lazy" @endif>
@endforeach
