@extends('layouts.layout', ['title' => __('messages.two_factor.title').' – amirs.life', 'noindex' => true])

@section('content')
    <section class="container flex min-h-[88svh] items-center justify-center pt-[var(--nav-h)]">
        <div x-data="{ recovery: false }" class="w-full max-w-sm">
            <h1 class="statement">{{ __('messages.two_factor.title') }}</h1>
            <p class="mt-4 text-fg-2" x-text="recovery ? @js(__('messages.two_factor.recovery_hint')) : @js(__('messages.two_factor.code_hint'))">{{ __('messages.two_factor.code_hint') }}</p>

            <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-8 grid gap-5">
                @csrf
                <div x-show="!recovery">
                    <label for="code" class="sr-only">{{ __('messages.two_factor.code') }}</label>
                    <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" autofocus
                           class="field-input text-center text-2xl tracking-[.5em] tabular" placeholder="000000" :disabled="recovery">
                </div>
                <div x-show="recovery" x-cloak>
                    <label for="recovery_code" class="sr-only">{{ __('messages.two_factor.recovery') }}</label>
                    <input id="recovery_code" name="recovery_code" type="text" autocomplete="off" class="field-input font-mono" placeholder="xxxxxxxxxx-xxxxxxxxxx" :disabled="!recovery">
                </div>
                @error('code')<p class="field-error" role="alert">{{ icon('circle-alert') }}<span>{{ $message }}</span></p>@enderror
                <button type="submit" class="btn-primary w-full">{{ __('messages.two_factor.verify') }}</button>
            </form>

            <div class="mt-6 flex items-center justify-between text-small">
                <button type="button" @click="recovery = !recovery" class="link" x-text="recovery ? @js(__('messages.two_factor.use_code')) : @js(__('messages.two_factor.use_recovery'))">{{ __('messages.two_factor.use_recovery') }}</button>
                <form method="POST" action="{{ route('filament.Admin.auth.logout') }}">@csrf<button type="submit" class="text-muted hover:text-fg">{{ __('messages.two_factor.logout') }}</button></form>
            </div>
        </div>
    </section>
@endsection
