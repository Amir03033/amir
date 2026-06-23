<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProjectResource\Pages;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Translations')->tabs([
                    Tabs\Tab::make('Nederlands')->schema([
                        TextInput::make('title.nl')->label('Titel (NL)')->required(),
                        Textarea::make('description.nl')->label('Omschrijving (NL)')->required(),
                    ]),
                    Tabs\Tab::make('English')->schema([
                        TextInput::make('title.en')->label('Title (EN)')->required(),
                        Textarea::make('description.en')->label('Description (EN)')->required(),
                    ]),
                ])->columnSpanFull(),
                TextInput::make('tags')->required(),
                FileUpload::make('image')->image()->directory('projects'),
                TextInput::make('github_url')->url(),
                TextInput::make('demo_url')->url(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image')->circular(),
            TextColumn::make('title.nl')->label('Titel (NL)')->searchable(),
            TextColumn::make('tags')->badge(),
        ])->actions([Tables\Actions\EditAction::make()])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListProjects::route('/'), 'create' => Pages\CreateProject::route('/create'), 'edit' => Pages\EditProject::route('/{record}/edit')];
    }
}