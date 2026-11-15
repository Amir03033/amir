@props(['project', 'sizes' => '(min-width: 1024px) 960px, 100vw', 'eager' => false])
{{-- A project's real screenshot, or a neutral typographic panel. Never a stock image. --}}
@if($src = $project->coverUrl())
    <img src="{{ $src }}" alt="{{ __('site.work.screenshot_alt', ['name' => $project->getTranslation('title')]) }}"
         {{ $attributes->merge(['class' => 'cover-img']) }}
         sizes="{{ $sizes }}" decoding="async" @if($eager) fetchpriority="high" @else loading="lazy" @endif>
@else
    <div {{ $attributes->merge(['class' => 'cover-empty']) }}>
        <span class="cover-empty-name">{{ $project->getTranslation('title') }}</span>
        @if($project->host())<span class="cover-empty-host">{{ $project->host() }}</span>@endif
        <x-ph label="screenshot {{ $project->getTranslation('title') }}" class="mt-3" />
    </div>
@endif
