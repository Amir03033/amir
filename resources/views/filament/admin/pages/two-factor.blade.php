<x-filament-panels::page>
    @if($user->hasConfirmedTwoFactor())
        <x-filament::section>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-shield-check" class="h-8 w-8 text-success-500" />
                    <div>
                        <p class="font-semibold text-gray-950 dark:text-white">Tweestapsverificatie is actief</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ingesteld op {{ $user->two_factor_confirmed_at->format('d-m-Y H:i') }}</p>
                    </div>
                </div>
                <x-filament::button color="gray" icon="heroicon-o-arrow-path" wire:click="regenerate" wire:confirm="Nieuwe herstelcodes maken? De oude werken daarna niet meer.">
                    Nieuwe herstelcodes
                </x-filament::button>
            </div>
        </x-filament::section>
    @elseif(! $pending)
        <x-filament::section>
            <div class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-300">Na het inloggen vraagt het beheer om een 6-cijferige code uit je authenticator-app. Zelfs als iemand je wachtwoord heeft, komt diegene er zo niet in.</p>
                <x-filament::button icon="heroicon-o-qr-code" wire:click="start">Instellen</x-filament::button>
            </div>
        </x-filament::section>
    @else
        <x-filament::section heading="1. Scan de QR-code" description="Open je authenticator-app en kies 'account toevoegen'.">
            <div class="flex flex-wrap items-start gap-6">
                <div class="rounded-xl bg-white p-3">{!! $qr !!}</div>
                <div class="space-y-2 text-sm">
                    <p class="text-gray-600 dark:text-gray-300">Lukt scannen niet? Voer deze sleutel handmatig in:</p>
                    <code class="block rounded-lg bg-gray-100 px-3 py-2 font-mono text-sm tracking-wider dark:bg-white/5">{{ $secret }}</code>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section heading="2. Bevestig met een code">
            <form wire:submit="confirm" class="flex flex-wrap items-start gap-3">
                <div>
                    <x-filament::input.wrapper :valid="! $errors->has('code')">
                        <x-filament::input type="text" wire:model="code" inputmode="numeric" maxlength="6" placeholder="000000" autocomplete="one-time-code" />
                    </x-filament::input.wrapper>
                    @error('code')<p class="mt-1 text-sm text-danger-600 dark:text-danger-400">{{ $message }}</p>@enderror
                </div>
                <x-filament::button type="submit" icon="heroicon-o-check">Bevestigen</x-filament::button>
            </form>
        </x-filament::section>
    @endif

    @if(count($codes))
        <x-filament::section heading="Herstelcodes" description="Elke code werkt één keer, voor als je je telefoon kwijt bent. Bewaar ze in je wachtwoordmanager; ze worden niet opnieuw getoond.">
            <div class="grid gap-2 sm:grid-cols-2">
                @foreach($codes as $c)
                    <code class="rounded-lg bg-gray-100 px-3 py-2 font-mono text-sm dark:bg-white/5">{{ $c }}</code>
                @endforeach
            </div>
            <x-filament::button class="mt-4" color="gray" icon="heroicon-o-clipboard-document" x-on:click="navigator.clipboard.writeText(@js(implode(PHP_EOL, $codes))); $tooltip('Gekopieerd')">Kopieer alle codes</x-filament::button>
        </x-filament::section>
    @endif
</x-filament-panels::page>
