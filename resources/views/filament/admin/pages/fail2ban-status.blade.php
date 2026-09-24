<x-filament-panels::page>
    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-500">Laatst ververst: {{ $updatedAt }}</span>
        <x-filament::button wire:click="refreshStatus" icon="heroicon-o-arrow-path" wire:loading.attr="disabled">
            Ververs
        </x-filament::button>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        @foreach ($jails as $name => $jail)
            <x-filament::section :heading="$name">
                @if (isset($jail['error']))
                    <p class="text-sm text-danger-600">{{ $jail['error'] }}</p>
                @else
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div><dt class="text-gray-500">Failed nu</dt><dd class="text-xl font-semibold">{{ $jail['current_failed'] }}</dd></div>
                        <div><dt class="text-gray-500">Failed totaal</dt><dd class="text-xl font-semibold">{{ $jail['total_failed'] }}</dd></div>
                        <div><dt class="text-gray-500">Banned nu</dt><dd class="text-xl font-semibold">{{ $jail['current_banned'] }}</dd></div>
                        <div><dt class="text-gray-500">Banned totaal</dt><dd class="text-xl font-semibold">{{ $jail['total_banned'] }}</dd></div>
                    </dl>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @forelse ($jail['banned_ips'] as $ip)
                            <x-filament::badge color="danger">{{ $ip }}</x-filament::badge>
                        @empty
                            <span class="text-sm text-gray-400">Geen geband IP's</span>
                        @endforelse
                    </div>
                @endif
            </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>