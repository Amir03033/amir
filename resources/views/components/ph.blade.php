@props(['label', 'block' => false])
{{--
    Placeholder for missing copy, facts or media. Visible (dashed, labelled) everywhere except production.
    In production it renders its slot (a fallback/draft) when given, otherwise nothing.
--}}
@if(! app()->isProduction())
    <{{ $block ? 'div' : 'span' }} {{ $attributes->merge(['class' => 'ph'.($block ? ' ph-block' : '')]) }} data-placeholder="{{ $label }}"><span class="ph-label">[PLACEHOLDER: {{ $label }}]</span>@if($slot->isNotEmpty()) {{ $slot }}@endif</{{ $block ? 'div' : 'span' }}>
@elseif($slot->isNotEmpty())
    {{ $slot }}
@endif
