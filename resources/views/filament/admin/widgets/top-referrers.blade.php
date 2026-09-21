<x-filament-widgets::widget>
    <x-filament::section heading="Top verwijzende sites">
        <ul class="space-y-2">
            @forelse($referrers as $domain => $count)
                <li class="flex justify-between text-sm">
                    <span>{{ $domain }}</span>
                    <span class="font-semibold">{{ $count }}</span>
                </li>
            @empty
                <li class="text-sm text-gray-500">Nog geen data</li>
            @endforelse
        </ul>
    </x-filament::section>
</x-filament-widgets::widget>