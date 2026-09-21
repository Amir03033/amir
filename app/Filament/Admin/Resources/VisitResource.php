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
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP-adres')
                    ->searchable(),
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