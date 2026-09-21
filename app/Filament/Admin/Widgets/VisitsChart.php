<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Visit;
use Filament\Widgets\ChartWidget;

class VisitsChart extends ChartWidget
{
    protected static ?string $heading = 'Bezoeken laatste 30 dagen';

    protected function getData(): array
    {
        $data = Visit::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $labels = collect(range(29, 0))->map(fn ($i) => now()->subDays($i)->format('d-m'));
        $values = collect(range(29, 0))->map(fn ($i) => $data[now()->subDays($i)->format('Y-m-d')] ?? 0);

        return [
            'datasets' => [
                [
                    'label' => 'Bezoeken',
                    'data' => $values,
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(249,115,22,0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}