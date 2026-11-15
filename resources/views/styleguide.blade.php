@extends('layouts.layout', ['title' => 'Styleguide — amirs.life'])

{{-- Local-only review page: every Phase 1 component, in the current theme. Not linked anywhere. --}}
@php
    $swatches = [
        ['bg', '--bg'], ['surface', '--surface'], ['fill', '--fill'], ['line', '--line'], ['line-strong', '--line-strong'],
        ['muted', '--muted'], ['text-2', '--text-2'], ['text', '--text'], ['accent', '--accent'], ['accent-text', '--accent-text'],
        ['ok', '--ok'], ['err', '--err'],
    ];
@endphp

@section('content')
    {{-- Hero type sample: centered statement, role line, status line. The stamp arrives in Phase 2. --}}
    <section data-hero class="container flex min-h-[92svh] flex-col items-center justify-center pt-[var(--nav-h)] text-center">
        <div data-scrub-view="hero-exit">
            <h1 class="hero-type mx-auto max-w-[12ch]">
                <span class="block" data-hero-line style="--i: 0">Styleguide.</span>
                <span class="block" data-hero-line style="--i: 1">Rustig, groot,</span>
                <span class="block" data-hero-line style="--i: 2">en een beetje mij.</span>
            </h1>
            <p class="mt-8 text-small text-muted" data-hero-line style="--i: 3">Founder of Jebbari · Zwolle &amp; Amsterdam</p>
            <p class="status-line mt-3" data-hero-line style="--i: 4"><span class="live-dot" aria-hidden="true"></span>Amsterdam <time class="tabular">14:32</time> · Nu bezig met: <span class="text-fg-2">[PLACEHOLDER: currently building]</span></p>
        </div>
    </section>

    {{-- Opener + colour tokens --}}
    <section class="section container">
        <x-opener label="01 — Kleur" lead="Gebroken wit en inkt. Vermiljoen alleen voor heel kleine details: de punt in het woordmerk, de focusring, het stempel.">
            Bijna alles is papier en inkt.
        </x-opener>

        <div class="mt-stack grid grid-cols-2 gap-x-6 gap-y-8 sm:grid-cols-3 lg:grid-cols-6" data-reveal-group>
            @foreach($swatches as [$name, $var])
                <div data-reveal>
                    <div class="aspect-[4/3] rounded-1 border border-line" style="background: rgb(var({{ $var }}))"></div>
                    <p class="mt-3 text-small font-medium">{{ $name }}</p>
                    <p class="label mt-1 normal-case tracking-normal">{{ $var }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Type scale --}}
    <section class="section container">
        <x-opener label="02 — Type">Instrument Sans, één familie.</x-opener>
        <div class="read mt-stack space-y-10" data-reveal-group>
            <div data-reveal><p class="label">hero · 56–120</p><p class="hero-type mt-2">Amir</p></div>
            <div data-reveal><p class="label">statement · 36–72</p><p class="statement mt-2">Ik bouw dingen.</p></div>
            <div data-reveal><p class="label">title · 28–48</p><p class="text-title mt-2 font-semibold">ServerScope</p></div>
            <div data-reveal><p class="label">h3 · 20–24</p><p class="text-h3 mt-2 font-semibold">Wat ik bouwde</p></div>
            <div data-reveal><p class="label">lead · 19–23</p><p class="lead mt-2">Een korte inleiding, rustig en grijs, nooit langer dan twee regels.</p></div>
            <div data-reveal><p class="label">body · 17–18 / 1.6</p><p class="mt-2">Lopende tekst staat links uitgelijnd in een kolom van maximaal 720 pixels. Korte zinnen, eerste persoon, geen superlatieven. <a href="#" class="link">Een gewone link</a> heeft een onderstreping die donkerder wordt bij hover.</p></div>
            <div data-reveal><p class="label">label · Martian Mono 12–13</p><p class="label mt-2 text-fg">Werk · 2026 · Laravel</p></div>
        </div>
    </section>

    {{-- Buttons, links, tags --}}
    <section class="section container">
        <x-opener label="03 — Acties">Knoppen, links en tags.</x-opener>
        <div class="mt-stack flex flex-col items-center gap-10" data-reveal>
            <div class="flex flex-wrap justify-center gap-3">
                <button type="button" class="btn-primary">Primaire knop</button>
                <button type="button" class="btn-secondary">Secundaire knop</button>
                <button type="button" class="btn-primary" disabled>Uitgeschakeld</button>
                <button type="button" class="btn-secondary btn-sm">Klein</button>
                <button type="button" class="btn-icon" aria-label="Zoeken">{{ icon('search') }}</button>
            </div>
            <div class="flex flex-wrap justify-center gap-8">
                <a href="#" class="link-arrow"><span>Bekijk de case</span>{{ icon('arrow-right') }}</a>
                <a href="#" class="link-arrow"><span>jebbari.nl</span>{{ icon('arrow-up-right', 'icon-diag') }}</a>
                <a href="#" class="link">Tekstlink</a>
            </div>
            <div class="flex flex-wrap justify-center gap-2">
                <span class="tag">Laravel</span><span class="tag">Filament</span><span class="tag">Livewire</span><span class="tag">Tailwind CSS</span>
            </div>
        </div>
    </section>

    {{-- Ruled list --}}
    <section class="section container">
        <x-opener label="04 — Lijsten">Lijnen in plaats van kaarten.</x-opener>
        <ul class="rule-list read mt-stack" data-reveal-group>
            @foreach([['2026', '[PLACEHOLDER: project]', 'Eén regel over wat het is.'], ['2025', '[PLACEHOLDER: project]', 'Eén regel over wat het is.'], ['2024', '[PLACEHOLDER: project]', 'Eén regel over wat het is.']] as [$year, $name, $line])
                <li data-reveal>
                    <a href="#" class="grid grid-cols-[4rem_1fr_auto] items-baseline gap-4 py-6 transition-opacity duration-ui hover:opacity-100">
                        <span class="label">{{ $year }}</span>
                        <span><span class="block text-h3 font-semibold">{{ $name }}</span><span class="mt-1 block text-small text-muted">{{ $line }}</span></span>
                        {{ icon('arrow-right', 'text-muted') }}
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- Pinned scroll demo --}}
    <section class="pin" aria-label="Scroll demo">
        <div class="pin-stage">
            <div class="pin-stage-inner container text-center" {{ scrub('fade-out', 85, 100) }}>
                <p class="label" {{ scrub('fade-up', 0, 20) }}>05 — Beweging</p>
                <p class="statement mx-auto mt-5 max-w-[14ch]" {{ scrub('fade-up', 5, 30) }}>Scrollen bepaalt het tempo.</p>
                <div class="mx-auto mt-12 aspect-[16/9] w-full max-w-4xl rounded-1 bg-fill" {{ scrub('scale-in', 15, 60) }}>
                    <div class="grid h-full place-items-center text-small text-muted">[PLACEHOLDER: screenshot]</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Forms, toast, dialog --}}
    <section class="section section-band">
        <div class="container">
            <x-opener label="06 — Formulieren" lead="Label boven het veld, rustige rand, een duidelijke fout met icoon en tekst.">Praat terug.</x-opener>
            <form class="read mt-stack grid gap-6" onsubmit="event.preventDefault(); window.toast('Bericht ontvangen. Ik reageer binnen een paar dagen.')" novalidate>
                <div class="grid gap-6 sm:grid-cols-2">
                    <x-field name="sg_name" label="Naam" :optional="true" autocomplete="name" />
                    <x-field name="sg_email" label="E-mailadres" type="email" error="Dit lijkt geen geldig e-mailadres." value="amir@" />
                </div>
                <x-field name="sg_message" label="Bericht" :textarea="true" help="Minimaal tien tekens." placeholder="Waar denk je aan?" />
                <div class="hp" aria-hidden="true"><label for="sg_website">Website</label><input id="sg_website" name="website" tabindex="-1" autocomplete="off"></div>
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="btn-primary">Versturen {{ icon('arrow-right') }}</button>
                    <button type="button" class="btn-primary" disabled>{{ icon('loader-circle', 'spinner') }} {{ __('messages.form.sending') }}</button>
                    <button type="button" class="btn-secondary" onclick="window.toast('Dat lukte niet. Probeer het zo nog eens.', { type: 'error' })">Fouttoast</button>
                    <button type="button" class="btn-secondary" data-dialog-open="sg-dialog">Open dialoog</button>
                </div>
            </form>
        </div>
    </section>

    <x-dialog id="sg-dialog" title="Stel een vraag">
        <p class="text-fg-2">Dialogen zijn native <code>&lt;dialog&gt;</code>-elementen: focus blijft erin, Esc sluit. Op een telefoon worden ze een sheet vanaf de onderkant.</p>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" class="btn-secondary" data-dialog-close>Annuleren</button>
            <button type="button" class="btn-primary" data-dialog-close>Oké</button>
        </div>
    </x-dialog>

    {{-- States --}}
    <section class="section container">
        <x-opener label="07 — Staten">Leeg, laden, fout.</x-opener>
        <div class="mt-stack grid gap-12 md:grid-cols-3" data-reveal-group>
            <div data-reveal>
                <p class="label">Leeg</p>
                <div class="empty"><p>Nog geen ideeën. Wees de eerste.</p><button type="button" class="btn-secondary btn-sm mt-5">Stel iets voor</button></div>
            </div>
            <div data-reveal aria-busy="true">
                <p class="label">Laden</p>
                <div class="rule-list mt-8">
                    @foreach([70, 55, 80] as $w)
                        <div class="py-5"><div class="skeleton" style="width: {{ $w }}%"></div></div>
                    @endforeach
                </div>
            </div>
            <div data-reveal>
                <p class="label">Fout</p>
                <div class="mt-8 flex items-start gap-3 text-err" role="alert">{{ icon('circle-alert') }}<div><p>Laden lukte niet.</p><button type="button" class="link mt-2 text-fg">{{ __('messages.errors.retry') }}</button></div></div>
            </div>
        </div>
    </section>
@endsection
