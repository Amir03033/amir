@extends('layouts.layout', [
    'title' => __('seo.pages.certificates.title'),
    'description' => __('seo.pages.certificates.description'),
    'ogImage' => \App\Support\Seo::ogImage('page', 'certificates'),
    'pageType' => 'CollectionPage',
    'crumbs' => [[__('seo.breadcrumbs.certificates'), lroute('certificates')]],
])

@section('content')
    <header class="page-head container">
        <p class="label" data-hero-line style="--i: 0">{{ __('site.certificates.label') }}</p>
        <h1 class="hero-type mx-auto mt-5 max-w-[12ch]" data-hero-line style="--i: 1">{{ __('site.certificates.title') }}</h1>
        <p class="lead mx-auto mt-6 max-w-xl" data-hero-line style="--i: 2">{{ __('site.certificates.lead') }}</p>
    </header>

    <section class="container pb-section">
        @if($certificates->isEmpty())
            <p class="empty">{{ __('site.certificates.empty') }}</p>
        @else
            <ul class="rule-list read" data-reveal-group>
                @foreach($certificates as $cert)
                    <x-certificate-row :cert="$cert" />
                @endforeach
            </ul>
        @endif
    </section>
@endsection
