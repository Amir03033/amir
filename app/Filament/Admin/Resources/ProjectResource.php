<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Projecten';

    protected static ?string $modelLabel = 'Project';

    protected static ?string $pluralModelLabel = 'Projecten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Translations')
                    ->tabs([
                        Tabs\Tab::make('Nederlands')
                            ->schema([
                                TextInput::make('title.nl')
                                    ->label('Titel (NL)')
                                    ->required(),

                                Textarea::make('description.nl')
                                    ->label('Omschrijving (NL)')
                                    ->required()
                                    ->rows(8),
                            ]),

                        Tabs\Tab::make('English')
                            ->schema([
                                TextInput::make('title.en')
                                    ->label('Title (EN)')
                                    ->required(),

                                Textarea::make('description.en')
                                    ->label('Description (EN)')
                                    ->required()
                                    ->rows(8),
                            ]),
                    ])
                    ->columnSpanFull(),

                TextInput::make('tags')
                    ->label('Tags')
                    ->required()
                    ->helperText(
                        'Komma-gescheiden, bijvoorbeeld: Laravel, PHP, JavaScript'
                    ),

                FileUpload::make('image')
                    ->label('Afbeelding')
                    ->image()
                    ->disk('public')
                    ->directory('projects')
                    ->visibility('public')
                    ->maxSize(4096),

                TextInput::make('github_url')
                    ->label('GitHub URL')
                    ->url()
                    ->nullable(),

                TextInput::make('demo_url')
                    ->label('Demo URL')
                    ->url()
                    ->nullable(),

                Forms\Components\Toggle::make('featured')
                    ->label('Uitgelicht project')
                    ->helperText(
                        'Toon dit project als featured/uitgelicht op je website.'
                    )
                    ->default(false),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Sorteervolgorde')
                    ->numeric()
                    ->integer()
                    ->default(0)
                    ->helperText(
                        'Lager nummer = eerder in de lijst. Je kunt ook drag-and-drop gebruiken in de projectenlijst.'
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Afbeelding')
                    ->circular(),

                TextColumn::make('title.nl')
                    ->label('Titel (NL)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tags')
                    ->label('Tags')
                    ->badge(),

                IconColumn::make('featured')
                    ->label('Featured')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Volgorde')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])

            ->reorderable('sort_order')

            ->defaultSort('sort_order', 'asc')

            ->actions([
                Tables\Actions\EditAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}