@extends('layouts.layout', ['title' => __('messages.two_factor.title').' — '.config('site.name')])

@section('content')
    <div class="flex min-h-[100svh] items-center justify-center px-4">
        <div x-data="{ recovery: false }" class="panel w-full max-w-sm p-6 md:p-8">
            <svg class="h-7 w-7 text-scope-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/></svg>
            <h1 class="mt-4 text-xl font-bold tracking-tight">{{ __('messages.two_factor.title') }}</h1>
            <p class="mt-2 text-sm text-ink-soft" x-text="recovery ? @js(__('messages.two_factor.recovery_hint')) : @js(__('messages.two_factor.code_hint'))">{{ __('messages.two_factor.code_hint') }}</p>

            <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-6 space-y-4">
                @csrf
                <div x-show="!recovery">
                    <label for="code" class="sr-only">{{ __('messages.two_factor.code') }}</label>
                    <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" autofocus
                           class="input-field text-center text-2xl tracking-[.5em]" placeholder="000000" :disabled="recovery">
                </div>
                <div x-show="recovery" x-cloak>
                    <label for="recovery_code" class="sr-only">{{ __('messages.two_factor.recovery') }}</label>
                    <input id="recovery_code" name="recovery_code" type="text" autocomplete="off" class="input-field font-mono" placeholder="xxxxxxxxxx-xxxxxxxxxx" :disabled="!recovery">
                </div>
                @error('code')<p class="text-sm text-scope-red" role="alert">{{ $message }}</p>@enderror
                <button type="submit" class="btn-primary w-full">{{ __('messages.two_factor.verify') }}</button>
            </form>

            <div class="mt-5 flex items-center justify-between text-xs">
                <button type="button" @click="recovery = !recovery" class="text-scope-accent hover:underline" x-text="recovery ? @js(__('messages.two_factor.use_code')) : @js(__('messages.two_factor.use_recovery'))">{{ __('messages.two_factor.use_recovery') }}</button>
                <form method="POST" action="{{ route('filament.Admin.auth.logout') }}">@csrf<button type="submit" class="text-ink-muted hover:text-ink-text">{{ __('messages.two_factor.logout') }}</button></form>
            </div>
        </div>
    </div>
@endsection
