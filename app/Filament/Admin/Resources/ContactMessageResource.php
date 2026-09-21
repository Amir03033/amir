<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Contactberichten';

    protected static ?string $modelLabel = 'Contactbericht';

    protected static ?string $pluralModelLabel = 'Contactberichten';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('message')
                    ->label('Bericht')
                    ->limit(80)
                    ->wrap(),

                IconColumn::make('mail_sent')
                    ->label('Mail verstuurd')
                    ->boolean(),

                TextColumn::make('read_at')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state) => $state ? 'Gelezen' : 'Ongelezen'
                    )
                    ->color(
                        fn ($state) => $state ? 'success' : 'warning'
                    )
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ontvangen')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('read')
                    ->label('Status')
                    ->options([
                        'unread' => 'Ongelezen',
                        'read' => 'Gelezen',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'unread' => $query->whereNull('read_at'),
                            'read' => $query->whereNotNull('read_at'),
                            default => $query,
                        };
                    }),
            ])

            ->actions([
                Action::make('markAsRead')
                    ->label('Gelezen')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (ContactMessage $record): bool => ! $record->isRead())
                    ->action(function (ContactMessage $record): void {
                        $record->markAsRead();
                    }),

                Action::make('markAsUnread')
                    ->label('Ongelezen')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->visible(fn (ContactMessage $record): bool => $record->isRead())
                    ->action(function (ContactMessage $record): void {
                        $record->markAsUnread();
                    }),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markAsRead')
                        ->label('Markeer als gelezen')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $records->each(function (ContactMessage $record) {
                                $record->markAsRead();
                            });
                        }),

                    Tables\Actions\BulkAction::make('markAsUnread')
                        ->label('Markeer als ongelezen')
                        ->icon('heroicon-o-envelope')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $records->each(function (ContactMessage $record) {
                                $record->markAsUnread();
                            });
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])

            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
        ];
    }
}