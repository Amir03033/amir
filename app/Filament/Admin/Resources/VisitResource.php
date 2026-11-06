<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VisitResource\Pages;
use App\Filament\Admin\Resources\VisitResource\RelationManagers;
use App\Models\Visit;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Inzichten';

    protected static ?string $navigationLabel = 'Bezoeken';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('url')->label('Pagina')->disabled(),
                Forms\Components\TextInput::make('browser')->label('Browser')->disabled(),
                Forms\Components\TextInput::make('platform')->label('OS')->disabled(),
                Forms\Components\TextInput::make('device_type')->label('Apparaat')->disabled(),
                Forms\Components\TextInput::make('referrer')->label('Referrer')->disabled(),
                Forms\Components\TextInput::make('user_agent')->label('User agent')->disabled()->columnSpanFull(),
                Forms\Components\TextInput::make('ip_hash')->label('IP-hash (versleuteld)')->disabled(),
                Forms\Components\TextInput::make('ip_address')->label('IP-adres')->disabled(),
                Forms\Components\TextInput::make('session_id')->label('Sessie-ID')->disabled(),
                Forms\Components\TextInput::make('utm_source')->label('UTM Source')->disabled(),
                Forms\Components\TextInput::make('utm_campaign')->label('UTM Campaign')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Wanneer')
                    ->since()
                    ->dateTimeTooltip('d-m-Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->label('Pagina')
                    ->formatStateUsing(fn ($state) => '/'.ltrim($state, '/'))
                    ->weight('medium')
                    ->searchable(),
                Tables\Columns\TextColumn::make('referrer')
                    ->label('Bron')
                    ->formatStateUsing(fn ($state) => \App\Support\VisitStats::source($state))
                    ->placeholder('Direct')
                    ->badge()
                    ->color(fn ($state) => $state ? 'info' : 'gray'),
                Tables\Columns\TextColumn::make('country')
                    ->label('Land')
                    ->formatStateUsing(fn ($state) => \App\Support\VisitStats::flag($state).' '.$state)
                    ->placeholder('🌐'),
                Tables\Columns\IconColumn::make('device_type')
                    ->label('Apparaat')
                    ->icon(fn ($state) => match ($state) {
                        'mobile' => 'heroicon-o-device-phone-mobile',
                        'tablet' => 'heroicon-o-device-tablet',
                        default => 'heroicon-o-computer-desktop',
                    })
                    ->tooltip(fn ($state) => ucfirst((string) $state)),
                Tables\Columns\TextColumn::make('browser')
                    ->label('Browser')
                    ->description(fn ($record) => $record->platform),
                Tables\Columns\TextColumn::make('utm_source')
                    ->label('Campagne')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP-adres')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('device_type')
                    ->label('Apparaat')
                    ->options([
                        'desktop' => 'Desktop',
                        'mobile' => 'Mobiel',
                        'tablet' => 'Tablet',
                    ]),
                SelectFilter::make('browser')
                    ->label('Browser')
                    ->options(fn () => Visit::query()->distinct()->pluck('browser', 'browser')->filter()->toArray()),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('van'),
                        DatePicker::make('tot'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['van'], fn (Builder $q) => $q->whereDate('created_at', '>=', $data['van']))
                            ->when($data['tot'], fn (Builder $q) => $q->whereDate('created_at', '<=', $data['tot']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton(),
            ])
            ->poll('60s')
            ->striped()
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisits::route('/'),
        ];
    }
}