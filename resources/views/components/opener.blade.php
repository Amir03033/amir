@props(['label' => null, 'lead' => null, 'tag' => 'h2', 'wide' => false, 'id' => null])
{{-- Section opener: optional mono label, one big centered statement, optional short lead. --}}
<header class="text-center" @if($id) id="{{ $id }}" @endif>
    @if($label)
        <p class="label mb-5" data-reveal>{{ $label }}</p>
    @endif
    <{{ $tag }} class="statement opener {{ $wide ? 'opener-wide' : '' }}" data-scrub-view="dim-in">{{ $slot }}</{{ $tag }}>
    @if($lead)
        <p class="lead opener-lead" data-reveal>{{ $lead }}</p>
    @endif
</header>
