<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CertificateResource\Pages;
use App\Models\Certificate;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Certificaten';

    protected static ?string $modelLabel = 'Certificaat';

    protected static ?string $pluralModelLabel = 'Certificaten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Translations')
                    ->tabs([
                        Tabs\Tab::make('Nederlands')
                            ->schema([
                                TextInput::make('title.nl')->label('Titel (NL)')->required(),
                            ]),
                        Tabs\Tab::make('English')
                            ->schema([
                                TextInput::make('title.en')->label('Title (EN)')->required(),
                            ]),
                    ])
                    ->columnSpanFull(),

                TextInput::make('issuer')
                    ->label('Uitgever')
                    ->helperText('Bijv. Coursera, Udemy, Laravel'),

                DatePicker::make('issued_at')
                    ->label('Behaald op')
                    ->native(false),

                FileUpload::make('file')
                    ->label('Bestand (PDF of afbeelding)')
                    ->disk('public')
                    ->directory('certificates')
                    ->visibility('public')
                    ->acceptedFileTypes(['application/pdf', 'image/png', 'image/jpeg'])
                    ->maxSize(8192),

                TextInput::make('external_url')
                    ->label('Verificatie-URL')
                    ->url()
                    ->nullable(),

                Forms\Components\Toggle::make('featured')
                    ->label('Uitgelicht')
                    ->default(false),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Sorteervolgorde')
                    ->numeric()
                    ->integer()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title.nl')->label('Titel')->searchable()->sortable(),
                TextColumn::make('issuer')->label('Uitgever')->sortable(),
                TextColumn::make('issued_at')->label('Datum')->date('d-m-Y')->sortable(),
                IconColumn::make('featured')->label('Featured')->boolean(),
                TextColumn::make('sort_order')->label('Volgorde')->sortable(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificates::route('/'),
            'create' => Pages\CreateCertificate::route('/create'),
            'edit' => Pages\EditCertificate::route('/{record}/edit'),
        ];
    }
}