@props(['withLabel' => false])
{{-- Cycles system → light → dark. The visible icon follows the saved preference (see nav.css). --}}
<button type="button" data-theme-toggle
        class="theme-toggle {{ $withLabel ? 'btn-secondary btn-sm' : 'btn-icon' }}"
        aria-label="{{ __('messages.theme.toggle') }}"
        title="{{ __('messages.theme.toggle') }}"
        data-label-system="{{ __('messages.theme.system') }}" data-label-light="{{ __('messages.theme.light') }}" data-label-dark="{{ __('messages.theme.dark') }}">
    {{ icon('monitor', 'i-system') }}{{ icon('sun', 'i-light') }}{{ icon('moon', 'i-dark') }}
    @if($withLabel)<span data-theme-label>{{ __('messages.theme.system') }}</span>@endif
</button>
