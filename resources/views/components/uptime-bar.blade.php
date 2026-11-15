@props(['days'])
{{-- One bar per day: full = all checks ok, shorter and in warning/error colour when not, hairline = not measured. --}}
<div {{ $attributes->merge(['class' => 'uptime-bar']) }} data-uptime-bar role="img"
     aria-label="{{ __('messages.status.bar_label', ['n' => $days->count()]) }}">
    @foreach($days as $day)
        @php
            $u = $day['uptime'];
            $state = $u === null ? 'none' : ($u >= 99.5 ? 'ok' : ($u >= 95 ? 'warn' : 'down'));
        @endphp
        <span data-state="{{ $state }}" title="{{ $day['date']->translatedFormat('j M') }} · {{ $u === null ? __('messages.status.no_data') : rtrim(rtrim(number_format($u, 2), '0'), '.').'%' }}"></span>
    @endforeach
</div>
<div class="mt-2 flex justify-between text-[.75rem] text-muted">
    <span>{{ __('messages.status.days_ago', ['n' => $days->count()]) }}</span>
    <span>{{ __('messages.status.today') }}</span>
</div>
