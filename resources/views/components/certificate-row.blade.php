@props(['cert'])
@php($url = $cert->external_url ?: $cert->fileUrl())
<li data-reveal>
    <div class="grid grid-cols-[3.5rem_1fr_auto] items-baseline gap-4 py-5 md:grid-cols-[4.5rem_1fr_auto]">
        <span class="label tabular">{{ $cert->issued_at?->format('Y') ?? '—' }}</span>
        <span class="min-w-0">
            <span class="block font-medium">{{ $cert->getTranslation('title') }}</span>
            @if($cert->issuer)<span class="mt-0.5 block text-small text-muted">{{ $cert->issuer }}</span>@endif
        </span>
        @if($url)
            <a href="{{ $url }}" class="link-arrow text-small" target="_blank" rel="noopener"><span>{{ __('site.certificates.view') }}</span>{{ icon('arrow-up-right', 'icon-diag') }}<span class="sr-only">: {{ $cert->getTranslation('title') }}</span></a>
        @endif
    </div>
</li>
