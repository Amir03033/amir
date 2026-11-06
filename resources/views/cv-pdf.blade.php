@php
    use App\Models\Setting;
    $avatar = Setting::get('media.avatar') ? storage_path('app/public/'.Setting::get('media.avatar')) : public_path('images/amir-avatar.webp');
    $accent = '#3b6fd8';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>CV — {{ config('site.name') }}</title>
    <style>
        @page { margin: 34px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1f2328; line-height: 1.5; }
        h1 { font-size: 24px; margin: 0; letter-spacing: -0.5px; }
        h2 { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: #6b7280; margin: 0 0 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .sub { color: {{ $accent }}; font-size: 12px; margin-top: 2px; }
        .muted { color: #6b7280; }
        .section { margin-top: 18px; }
        .item { margin-bottom: 10px; }
        .item b { font-size: 11.5px; }
        .tags { color: {{ $accent }}; font-size: 9px; }
        table.head td { vertical-align: middle; }
        .avatar { width: 64px; height: 64px; border-radius: 32px; }
        .contact td { padding: 1px 12px 1px 0; }
        a { color: {{ $accent }}; text-decoration: none; }
    </style>
</head>
<body>
    <table class="head" width="100%">
        <tr>
            <td width="76">@if(is_file($avatar))<img class="avatar" src="{{ $avatar }}">@endif</td>
            <td>
                <h1>{{ config('site.name') }}</h1>
                <div class="sub">{{ __('messages.home.subtitle') }} · {{ config('site.location') }}</div>
                <div class="muted" style="margin-top:4px">{{ __('messages.about.availability') }}: {{ __('messages.about.availability_value') }}</div>
            </td>
            <td align="right" class="muted">
                <table class="contact">
                    <tr><td><a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a></td></tr>
                    @if(config('site.phone'))<tr><td>{{ config('site.phone') }}</td></tr>@endif
                    <tr><td><a href="{{ route('home') }}">{{ parse_url(route('home'), PHP_URL_HOST) }}</a></td></tr>
                    <tr><td><a href="{{ config('site.linkedin') }}">LinkedIn</a>@if(config('site.github')) · <a href="{{ config('site.github') }}">GitHub</a>@endif</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section">
        <h2>{{ __('messages.about.label') }}</h2>
        <p style="margin:0">{{ __('messages.about.text') }}</p>
    </div>

    <div class="section">
        <h2>{{ __('messages.cv.projects') }}</h2>
        <div class="item">
            <b>ServerScope</b>@if(config('site.serverscope.url')) <span class="muted">· {{ parse_url(config('site.serverscope.url'), PHP_URL_HOST) }}</span>@endif<br>
            {{ __('messages.serverscope.tagline') }} {{ __('messages.serverscope.intro') }}<br>
            <span class="tags">{{ implode(' · ', __('messages.serverscope.stack')) }}</span>
        </div>
        @foreach($projects as $project)
            <div class="item">
                <b>{{ $project->getTranslation('title') }}</b>@if($project->hasDemo()) <span class="muted">· {{ parse_url($project->demo_url, PHP_URL_HOST) }}</span>@endif<br>
                {{ $project->excerpt(260) }}<br>
                <span class="tags">{{ implode(' · ', $project->tagList()) }}</span>
            </div>
        @endforeach
    </div>

    <div class="section">
        <h2>{{ __('messages.skills.label') }}</h2>
        @foreach(__('messages.skills.groups') as $group)
            <div><b>{{ $group['label'] }}:</b> {{ implode(', ', $group['items']) }}</div>
        @endforeach
    </div>

    <div class="section">
        <h2>{{ __('messages.cv.education') }}</h2>
        <div>{{ __('messages.cv.education_item') }}</div>
    </div>

    <p class="muted" style="margin-top:22px; font-size:8.5px">{{ __('messages.cv.generated', ['date' => now()->format('d-m-Y')]) }} · {{ route('cv') }}</p>
</body>
</html>
