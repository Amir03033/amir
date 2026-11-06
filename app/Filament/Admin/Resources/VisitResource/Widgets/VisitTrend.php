<?php

namespace App\Filament\Admin\Resources\VisitResource\Widgets;

use App\Support\VisitStats;
use Filament\Widgets\ChartWidget;

class VisitTrend extends ChartWidget
{
    protected static ?string $heading = 'Verloop';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '280px';

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return ['7' => '7 dagen', '30' => '30 dagen', '90' => '90 dagen'];
    }

    protected function getData(): array
    {
        $d = VisitStats::daily((int) $this->filter);

        return [
            'datasets' => [
                [
                    'label' => 'Paginaweergaven',
                    'data' => $d['views'],
                    'borderColor' => '#6e9cf7',
                    'backgroundColor' => 'rgba(110,156,247,0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointRadius' => 0,
                ],
                [
                    'label' => 'Unieke bezoekers',
                    'data' => $d['visitors'],
                    'borderColor' => '#4ade80',
                    'backgroundColor' => 'transparent',
                    'tension' => 0.35,
                    'pointRadius' => 0,
                ],
            ],
            'labels' => $d['labels'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'interaction' => ['intersect' => false, 'mode' => 'index'],
            'plugins' => ['legend' => ['position' => 'bottom']],
            'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
