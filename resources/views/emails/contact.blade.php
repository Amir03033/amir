<x-mail::message>
Nieuw bericht via het portfolio van **{{ $senderName }}** ({{ $senderEmail }}).

{{ $senderMessage }}

Thanks,<br>
{{ config('site.name') }}
</x-mail::message>
