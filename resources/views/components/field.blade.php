@props([
    'name',
    'label',
    'type' => 'text',
    'id' => null,
    'optional' => false,
    'help' => null,
    'error' => null,
    'textarea' => false,
    'rows' => 5,
    'model' => null, // Alpine x-model target, e.g. "form.email"; errors then come from "errors.<name>"
])

@php
    $id ??= 'f-'.$name;
    $serverError = $error ?? ($errors ?? null)?->first($name);
    $describedBy = trim(($help ? $id.'-help ' : '').$id.'-error');
@endphp

<div class="field">
    <label for="{{ $id }}" class="field-label">
        {{ $label }}@if($optional) <span class="optional">({{ __('messages.form.optional') }})</span>@endif
    </label>

    @if($textarea)
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
            {{ $attributes->merge(['class' => 'field-input']) }}
            aria-describedby="{{ $describedBy }}"
            @if($model) x-model="{{ $model }}" :aria-invalid="errors.{{ $name }} ? 'true' : 'false'" @elseif($serverError) aria-invalid="true" @endif
            @unless($optional) required @endunless>{{ $slot }}</textarea>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
            {{ $attributes->merge(['class' => 'field-input']) }}
            aria-describedby="{{ $describedBy }}"
            @if($model) x-model="{{ $model }}" :aria-invalid="errors.{{ $name }} ? 'true' : 'false'" @elseif($serverError) aria-invalid="true" @endif
            @unless($optional) required @endunless>
    @endif

    @if($help)
        <p id="{{ $id }}-help" class="field-help">{{ $help }}</p>
    @endif

    @if($model)
        <p id="{{ $id }}-error" class="field-error" x-show="errors.{{ $name }}" x-cloak>{{ icon('circle-alert') }}<span x-text="errors.{{ $name }} ? errors.{{ $name }}[0] : ''"></span></p>
    @else
        <p id="{{ $id }}-error" class="field-error">@if($serverError){{ icon('circle-alert') }}<span>{{ $serverError }}</span>@endif</p>
    @endif
</div>
