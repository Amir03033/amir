@props(['project', 'sizes' => '(min-width: 1024px) 960px, 100vw', 'eager' => false])
{{-- A project's real screenshot (with an optional dark-theme version), or a neutral typographic panel. Never a stock image. --}}
@if($src = $project->coverUrl())
    <x-themed-img :light="$src" :dark="$project->coverDarkUrl()" :alt="$project->coverAlt()" :eager="$eager" :sizes="$sizes"
                  {{ $attributes->merge(['class' => 'cover-img']) }} />
@else
    <div {{ $attributes->merge(['class' => 'cover-empty']) }}>
        <span class="cover-empty-name">{{ $project->getTranslation('title') }}</span>
        @if($project->host())<span class="cover-empty-host">{{ $project->host() }}</span>@endif
        <x-ph label="screenshot {{ $project->getTranslation('title') }}" class="mt-3" />
    </div>
@endif
