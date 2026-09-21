<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VisitResource\Pages;
use App\Filament\Admin\Resources\VisitResource\RelationManagers;
use App\Models\Visit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->label('Pagina')
                    ->searchable(),
                Tables\Columns\TextColumn::make('browser')
                    ->label('Browser'),
                Tables\Columns\TextColumn::make('platform')
                    ->label('OS'),
                Tables\Columns\TextColumn::make('device_type')
                    ->label('Apparaat')
                    ->badge(),
                Tables\Columns\TextColumn::make('referrer')
                    ->label('Referrer')
                    ->limit(30),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Bezocht op')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'create' => Pages\CreateVisit::route('/create'),
            'edit' => Pages\EditVisit::route('/{record}/edit'),
        ];
    }
}