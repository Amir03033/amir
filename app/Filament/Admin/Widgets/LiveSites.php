<?php

namespace App\Filament\Admin\Widgets;

use App\Models\SiteCheck;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LiveSites extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Live sites (laatste metingen)';

    public function table(Table $table): Table
    {
        return $table
            ->query(SiteCheck::query()->where('checked_at', '>=', now()->subDay())->latest('checked_at'))
            ->defaultPaginationPageOption(10)
            ->columns([
                Tables\Columns\IconColumn::make('ok')->label('')->boolean(),
                Tables\Columns\TextColumn::make('label')->label('Site')->weight('bold')->searchable(),
                Tables\Columns\TextColumn::make('url')->label('URL')->url(fn ($record) => $record->url, true)->color('gray'),
                Tables\Columns\TextColumn::make('status_code')->label('HTTP')->badge()->color(fn ($state) => $state >= 200 && $state < 400 ? 'success' : 'danger')->placeholder('—'),
                Tables\Columns\TextColumn::make('response_ms')->label('Respons')->suffix(' ms')->placeholder('—')->sortable(),
                Tables\Columns\TextColumn::make('checked_at')->label('Gemeten')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('ok')->label('Status')->trueLabel('Online')->falseLabel('Storing'),
            ])
            ->emptyStateHeading('Nog geen metingen')
            ->emptyStateDescription('Draai `php artisan site:check` of zet de scheduler aan op de server.');
    }
}
