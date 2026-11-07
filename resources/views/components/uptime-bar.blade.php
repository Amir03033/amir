@props(['days'])

{{-- 30 bars, one per day: green ≥ 99.5 %, yellow ≥ 95 %, red below, grey = not measured. --}}
<div {{ $attributes->merge(['class' => 'flex h-8 items-end gap-[3px]']) }} data-uptime-bar role="img"
     aria-label="{{ __('messages.status.bar_label', ['n' => $days->count()]) }}">
    @foreach($days as $day)
        @php
            $u = $day['uptime'];
            $color = $u === null ? 'bg-ink-line' : ($u >= 99.5 ? 'bg-scope-green' : ($u >= 95 ? 'bg-scope-yellow' : 'bg-scope-red'));
        @endphp
        <span class="group/bar relative h-full flex-1 rounded-[3px] {{ $color }} transition-transform duration-200 hover:scale-y-110" title="{{ $day['date']->translatedFormat('j M') }} · {{ $u === null ? __('messages.status.no_data') : rtrim(rtrim(number_format($u, 2), '0'), '.').'%' }}"></span>
    @endforeach
</div>
<div class="mt-1.5 flex justify-between text-[11px] text-ink-muted">
    <span>{{ __('messages.status.days_ago', ['n' => $days->count()]) }}</span>
    <span>{{ __('messages.status.today') }}</span>
</div>
