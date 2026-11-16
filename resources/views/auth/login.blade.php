@extends('layouts.layout', ['title' => __('seo.pages.login.title'), 'description' => __('seo.pages.login.description'), 'noindex' => true])

@section('content')
    <section class="container flex min-h-[88svh] items-center justify-center pt-[var(--nav-h)]">
        <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm">
            @csrf
            <h1 class="statement">Inloggen</h1>
            @if($errors->any())
                <p class="field-error mt-6" role="alert">{{ icon('circle-alert') }}<span>{{ $errors->first() }}</span></p>
            @endif
            <div class="mt-8 grid gap-5">
                <x-field name="email" type="email" label="E-mail" :value="old('email')" autocomplete="username" autofocus />
                <x-field name="password" type="password" label="Wachtwoord" autocomplete="current-password" />
                <label class="flex items-center gap-2 text-small text-fg-2"><input type="checkbox" name="remember" class="h-4 w-4 accent-[rgb(var(--accent))]"> Onthoud mij</label>
                <button type="submit" class="btn-primary w-full">Inloggen</button>
            </div>
        </form>
    </section>
@endsection
