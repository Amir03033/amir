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

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'tags'];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record->getTranslation('title');
    }

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

                Forms\Components\Section::make('Media & stack')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Afbeelding')
                            ->image()
                            ->disk('public')
                            ->directory('projects')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios(['16:10', '16:9', null])
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth('1600')
                            ->imageResizeTargetHeight('1600')
                            ->maxSize(8192)
                            ->helperText('Wordt bij uploaden automatisch verkleind tot max. 1600px, zodat de site snel blijft.')
                            ->columnSpanFull(),

                        TextInput::make('tags')
                            ->label('Tags')
                            ->required()
                            ->helperText('Komma-gescheiden, bijvoorbeeld: Laravel, PHP, JavaScript. De eerste tags worden filters op de site.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Links')
                    ->description('Een demo-URL wordt automatisch elke 10 minuten gecontroleerd en verschijnt in het live-dashboard op de homepage.')
                    ->schema([
                        TextInput::make('demo_url')
                            ->label('Live URL')
                            ->url()
                            ->prefixIcon('heroicon-m-globe-alt')
                            ->nullable(),

                        TextInput::make('github_url')
                            ->label('GitHub URL')
                            ->url()
                            ->prefixIcon('heroicon-m-code-bracket')
                            ->nullable(),
                    ])
                    ->columns(2),

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
                    ->label('')
                    ->height(44)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover aspect-[16/10]'])
                    ->defaultImageUrl(fn (Project $record) => $record->coverUrl()),

                TextColumn::make('title.nl')
                    ->label('Titel')
                    ->weight('bold')
                    ->description(fn (Project $record) => $record->hasDemo() ? parse_url($record->demo_url, PHP_URL_HOST) : 'Geen live URL')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tags')
                    ->label('Tags')
                    ->badge()
                    ->separator(','),

                Tables\Columns\ToggleColumn::make('featured')
                    ->label('Uitgelicht'),

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
                Tables\Actions\Action::make('view')
                    ->label('Bekijk')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Project $record): string => route('project.show', $record), true),

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