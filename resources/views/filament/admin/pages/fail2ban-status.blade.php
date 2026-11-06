@php
    $t = $this->totals;
    $labels = ['sshd' => ['SSH', 'heroicon-o-command-line'], 'plesk-panel' => ['Plesk-paneel', 'heroicon-o-server-stack'], 'proftpd' => ['FTP', 'heroicon-o-folder-arrow-down']];
@endphp

<x-filament-panels::page>
    <div @if($autoRefresh) wire:poll.30s="refreshStatus" @endif class="space-y-6">
        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                @if($t['errors'] === 0)
                    <x-filament::badge color="success" icon="heroicon-m-shield-check">Beveiliging actief</x-filament::badge>
                @else
                    <x-filament::badge color="danger" icon="heroicon-m-exclamation-triangle">{{ $t['errors'] }} jail(s) niet leesbaar</x-filament::badge>
                @endif
                <span class="text-sm text-gray-500 dark:text-gray-400">Bijgewerkt om <span class="font-medium text-gray-950 dark:text-white">{{ $updatedAt }}</span></span>
            </div>
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                    <x-filament::input.checkbox wire:model.live="autoRefresh" />
                    Elke 30 s verversen
                </label>
                <x-filament::button wire:click="refreshStatus" icon="heroicon-o-arrow-path" wire:loading.attr="disabled" color="gray">
                    <span wire:loading.remove wire:target="refreshStatus">Ververs</span>
                    <span wire:loading wire:target="refreshStatus">Bezig…</span>
                </x-filament::button>
            </div>
        </div>

        {{-- Totals --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach([
                ['Nu geblokkeerd', $t['current_banned'], 'heroicon-o-no-symbol', $t['current_banned'] ? 'text-danger-500' : 'text-gray-400'],
                ['Ooit geblokkeerd', $t['total_banned'], 'heroicon-o-archive-box-x-mark', 'text-gray-400'],
                ['Mislukte pogingen nu', $t['current_failed'], 'heroicon-o-key', $t['current_failed'] ? 'text-warning-500' : 'text-gray-400'],
                ['Mislukte pogingen totaal', $t['total_failed'], 'heroicon-o-finger-print', 'text-gray-400'],
            ] as [$label, $value, $icon, $color])
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ $label }}</span>
                        <x-filament::icon :icon="$icon" class="h-5 w-5 {{ $color }}" />
                    </div>
                    <p class="mt-3 text-3xl font-bold tabular-nums text-gray-950 dark:text-white">{{ number_format($value, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>

        {{-- Jails --}}
        <div class="grid gap-6 xl:grid-cols-3">
            @foreach ($jails as $name => $jail)
                @php([$title, $icon] = $labels[$name] ?? [$name, 'heroicon-o-shield-check'])
                <section x-data="{ q: '' }" class="flex flex-col rounded-2xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                    <header class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4 dark:border-white/10">
                        <div class="flex items-center gap-3">
                            <span class="grid h-9 w-9 place-items-center rounded-xl bg-primary-500/10 text-primary-500">
                                <x-filament::icon :icon="$icon" class="h-5 w-5" />
                            </span>
                            <div>
                                <h3 class="font-semibold text-gray-950 dark:text-white">{{ $title }}</h3>
                                <p class="font-mono text-xs text-gray-500">{{ $name }}</p>
                            </div>
                        </div>
                        @if(isset($jail['error']))
                            <x-filament::badge color="danger">Fout</x-filament::badge>
                        @elseif($jail['current_banned'] > 0)
                            <x-filament::badge color="warning">{{ $jail['current_banned'] }} geblokkeerd</x-filament::badge>
                        @else
                            <x-filament::badge color="success">Rustig</x-filament::badge>
                        @endif
                    </header>

                    @if (isset($jail['error']))
                        <div class="space-y-2 p-5 text-sm">
                            <p class="text-danger-600 dark:text-danger-400">Kon de status niet lezen.</p>
                            <pre class="overflow-x-auto rounded-lg bg-gray-50 p-3 text-xs text-gray-600 dark:bg-black/30 dark:text-gray-400">{{ $jail['error'] }}</pre>
                            <p class="text-xs text-gray-500">Controleer of de webserver-gebruiker <code>sudo -n /usr/bin/fail2ban-client status</code> mag draaien.</p>
                        </div>
                    @else
                        <dl class="grid grid-cols-2 gap-px bg-gray-100 dark:bg-white/10">
                            @foreach([
                                ['Geblokkeerd nu', $jail['current_banned'], 'danger'],
                                ['Geblokkeerd totaal', $jail['total_banned'], 'gray'],
                                ['Mislukt nu', $jail['current_failed'], 'warning'],
                                ['Mislukt totaal', $jail['total_failed'], 'gray'],
                            ] as [$label, $value, $tone])
                                <div class="bg-white p-4 dark:bg-gray-900">
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                                    <dd class="mt-1 text-xl font-semibold tabular-nums {{ $value ? ['danger' => 'text-danger-600 dark:text-danger-400', 'warning' => 'text-warning-600 dark:text-warning-400', 'gray' => 'text-gray-950 dark:text-white'][$tone] : 'text-gray-950 dark:text-white' }}">{{ number_format($value, 0, ',', '.') }}</dd>
                                </div>
                            @endforeach
                        </dl>

                        <div class="flex flex-1 flex-col p-5">
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <h4 class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Geblokkeerde IP's ({{ count($jail['banned_ips']) }})</h4>
                                @if(count($jail['banned_ips']) > 6)
                                    <input x-model="q" type="search" placeholder="Zoek IP…" class="w-32 rounded-lg border-0 bg-gray-50 px-2.5 py-1 text-xs ring-1 ring-gray-950/10 focus:ring-primary-500 dark:bg-white/5 dark:ring-white/10">
                                @endif
                            </div>
                            @if(count($jail['banned_ips']))
                                <ul class="max-h-64 space-y-1 overflow-y-auto pr-1">
                                    @foreach ($jail['banned_ips'] as $ip)
                                        <li x-show="!q || @js($ip).includes(q)" class="group flex items-center justify-between rounded-lg px-2.5 py-1.5 font-mono text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5">
                                            <span class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-danger-500"></span>{{ $ip }}</span>
                                            <button type="button" x-on:click="navigator.clipboard.writeText(@js($ip)); $tooltip('Gekopieerd')" class="opacity-0 transition group-hover:opacity-100" title="Kopieer">
                                                <x-filament::icon icon="heroicon-m-clipboard-document" class="h-4 w-4 text-gray-400" />
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="flex flex-1 items-center justify-center gap-2 rounded-xl border border-dashed border-gray-200 py-6 text-sm text-gray-400 dark:border-white/10">
                                    <x-filament::icon icon="heroicon-o-check-circle" class="h-5 w-5 text-success-500" /> Niemand geblokkeerd
                                </p>
                            @endif
                        </div>
                    @endif
                </section>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
