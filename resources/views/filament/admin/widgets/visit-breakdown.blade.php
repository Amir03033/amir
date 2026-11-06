<x-filament-widgets::widget>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($cards as $card)
            <x-filament::section>
                <x-slot name="heading">
                    <span class="flex items-center gap-2">
                        <x-filament::icon :icon="$card['icon']" class="h-4 w-4 text-gray-400" />
                        {{ $card['title'] }}
                    </span>
                </x-slot>
                <x-slot name="description">Laatste 30 dagen</x-slot>

                <ul class="space-y-2.5">
                    @forelse($card['rows'] as $row)
                        <li>
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="truncate font-medium text-gray-950 dark:text-white" title="{{ $row['label'] }}">{{ $row['label'] }}</span>
                                <span class="shrink-0 tabular-nums text-gray-500 dark:text-gray-400">{{ $row['count'] }} <span class="text-xs">· {{ $row['pct'] }}%</span></span>
                            </div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-gray-100 dark:bg-white/5">
                                <div class="h-full rounded-full" style="width: {{ max($row['pct'], 2) }}%; background: linear-gradient(90deg, #6e9cf7, #4ade80);"></div>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500">Nog geen data</li>
                    @endforelse
                </ul>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-widgets::widget>
