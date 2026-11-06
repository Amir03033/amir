@extends('layouts.layout', ['title' => '404 — '.config('site.name')])

@section('nav')
    <x-site-nav />
@endsection

@section('content')
    <div class="relative flex min-h-[100svh] items-center justify-center overflow-hidden px-4">
        <div class="grid-bg absolute inset-0" aria-hidden="true"></div>

        <div class="relative w-full max-w-lg">
            <div class="panel overflow-hidden">
                <div class="panel-head">
                    <span>incident</span>
                    <span class="flex items-center gap-2 normal-case tracking-normal text-scope-red"><span class="dot bg-scope-red"></span>404 not found</span>
                </div>
                <div class="p-6 md:p-8">
                    <p class="font-mono text-6xl font-semibold tracking-tight text-ink-text">404</p>
                    <h1 class="mt-4 text-2xl font-semibold tracking-tight">{{ __('messages.errors.not_found') }}</h1>
                    <p class="mt-3 leading-relaxed text-ink-soft">{{ __('messages.errors.not_found_text') }}</p>
                    <p class="mt-6 truncate rounded-[10px] border border-ink-line bg-ink-panel2 px-3 py-2 font-mono text-xs text-ink-muted">GET {{ request()->path() === '/' ? '/' : '/'.request()->path() }}</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('home') }}" class="btn-primary">{{ __('messages.errors.home') }}</a>
                        <button type="button" onclick="history.back()" class="btn-secondary">{{ __('messages.errors.back') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
