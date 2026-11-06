<x-mail::message>
# {{ __('messages.confirmation.greeting', ['name' => $senderName]) }}

{{ __('messages.confirmation.body') }}

@if(config('site.booking_url'))
<x-mail::button :url="config('site.booking_url')">
{{ __('messages.booking.cta') }}
</x-mail::button>
@endif

**{{ __('messages.confirmation.your_message') }}**

<x-mail::panel>
{{ $senderMessage }}
</x-mail::panel>

{{ __('messages.confirmation.signoff') }}<br>
{{ config('site.name') }}
</x-mail::message>
