<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Projecten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Meertalige Sectie met Tabs
                Tabs::make('Translations')
                    ->tabs([
                        Tabs\Tab::make('Nederlands')
                            ->icon('heroicon-o-language')
                            ->schema([
                                TextInput::make('title.nl')
                                    ->label('Titel (NL)')
                                    ->required(),
                                Textarea::make('description.nl')
                                    ->label('Omschrijving (NL)')
                                    ->rows(3)
                                    ->required(),
                            ]),
                        Tabs\Tab::make('English')
                            ->icon('heroicon-o-language')
                            ->schema([
                                TextInput::make('title.en')
                                    ->label('Title (EN)')
                                    ->required(),
                                Textarea::make('description.en')
                                    ->label('Description (EN)')
                                    ->rows(3)
                                    ->required(),
                            ]),
                    ])->columnSpanFull(),

                // Algemene Project Details
                Section::make('Project Assets & Links')
                    ->description('Beheer hier de afbeelding en externe links van je project.')
                    ->schema([
                        TextInput::make('tags')
                            ->label('Tags (bijv: Laravel, Tailwind, Vue)')
                            ->placeholder('Laravel, Tailwind, Alpine.js')
                            ->required(),

                        FileUpload::make('image')
                            ->label('Project Screenshot')
                            ->image()
                            ->directory('projects') // Slaat op in storage/app/public/projects
                            ->imageEditor()
                            ->columnSpanFull(),

                        TextInput::make('github_url')
                            ->label('GitHub URL')
                            ->url()
                            ->placeholder('https://github.com/amir/...'),

                        TextInput::make('demo_url')
                            ->label('Live Demo URL')
                            ->url()
                            ->placeholder('https://amirjebbari.nl/...'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Preview')
                    ->circular(),

                TextColumn::make('title.nl')
                    ->label('Titel (NL)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tags')
                    ->label('Tech Stack')
                    ->badge()
                    ->color('info')
                    ->separator(','),

                TextColumn::make('created_at')
                    ->label('Datum')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}