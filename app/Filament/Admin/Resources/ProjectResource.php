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

    /**
     * Admin links are built from the model's route key, which is the slug (or the id when a project
     * has no slug). Accept both here, so every edit link opens the record instead of a 404.
     */
    public static function resolveRecordRouteBinding(int|string $key): ?\Illuminate\Database\Eloquent\Model
    {
        $query = static::getEloquentQuery();

        return (clone $query)->where('slug', $key)->first()
            ?? (ctype_digit((string) $key) ? $query->whereKey((int) $key)->first() : null);
    }

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

                                TextInput::make('summary.nl')
                                    ->label('Eén regel (NL)')
                                    ->maxLength(140)
                                    ->helperText('Korte zin onder de projectnaam op de homepage en in /werk. Leeg = begin van de omschrijving.'),

                                TextInput::make('role.nl')
                                    ->label('Mijn rol (NL)')
                                    ->maxLength(80)
                                    ->placeholder('Ontwerp en bouw'),

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

                                TextInput::make('summary.en')
                                    ->label('One line (EN)')
                                    ->maxLength(140),

                                TextInput::make('role.en')
                                    ->label('My role (EN)')
                                    ->maxLength(80)
                                    ->placeholder('Design and build'),

                                Textarea::make('description.en')
                                    ->label('Description (EN)')
                                    ->required()
                                    ->rows(8),
                            ]),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Section::make('Adres & jaar')
                    ->schema([
                        TextInput::make('slug')
                            ->label('Slug')
                            ->prefix('/project/')
                            ->maxLength(120)
                            ->alphaDash()
                            ->unique(ignoreRecord: true)
                            ->rule('not_regex:/^\\d+$/')
                            ->helperText('Leeg = automatisch uit de Nederlandse titel. Let op: wijzig je hem later, dan werken oude links niet meer.'),
                        TextInput::make('year')
                            ->label('Jaar')
                            ->numeric()
                            ->integer()
                            ->minValue(2000)
                            ->maxValue(2100),
                    ])
                    ->columns(2),

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
                            ->helperText('Echte screenshot (16:10 werkt het best). Verschijnt op de homepage, in /werk en bovenaan de case. Wordt verkleind tot max. 1600px.')
                            ->columnSpanFull(),

                        FileUpload::make('video')
                            ->label('Korte video (optioneel)')
                            ->helperText('5–10 seconden schermopname, MP4 of WebM, zonder geluid, max. 20 MB. (Wordt in het nieuwe ontwerp nog niet getoond.)')
                            ->acceptedFileTypes(['video/mp4', 'video/webm'])
                            ->disk('public')
                            ->directory('projects/video')
                            ->visibility('public')
                            ->maxSize(20480)
                            ->columnSpanFull(),

                        TextInput::make('tags')
                            ->label('Tags')
                            ->required()
                            ->helperText('Komma-gescheiden, bijvoorbeeld: Laravel, PHP, JavaScript. De eerste tags worden filters op de site.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Case')
                    ->description('Optioneel maar sterk: vertel het verhaal in drie stappen. Lege velden worden niet getoond; zonder case wordt alleen de omschrijving gebruikt.')
                    ->collapsible()
                    ->schema([
                        Tabs::make('CaseTranslations')->tabs([
                            Tabs\Tab::make('Nederlands')->schema([
                                Textarea::make('problem.nl')->label('Het probleem (NL)')->rows(3)->placeholder('Wat wilde de klant, en wat ging er mis zonder deze site?'),
                                Textarea::make('solution.nl')->label('Wat ik bouwde (NL)')->rows(6)->placeholder('Welke onderdelen, welke technische keuzes?')->helperText('Markdown mag: lijstjes met "- ", **vet**.'),
                                Textarea::make('lessons.nl')->label('Wat lastig was, wat ik leerde (NL)')->rows(5),
                                Textarea::make('result.nl')->label('Resultaat (NL)')->rows(3)->placeholder('Wat is er nu beter? Liefst concreet en controleerbaar.'),
                            ]),
                            Tabs\Tab::make('English')->schema([
                                Textarea::make('problem.en')->label('The problem (EN)')->rows(3),
                                Textarea::make('solution.en')->label('What I built (EN)')->rows(6),
                                Textarea::make('lessons.en')->label('Hard parts, what I learned (EN)')->rows(5),
                                Textarea::make('result.en')->label('Result (EN)')->rows(3),
                            ]),
                        ]),
                        FileUpload::make('gallery')
                            ->label('Galerij')
                            ->helperText('Extra screenshots voor de casepagina. Sleep om de volgorde te wijzigen.')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->maxFiles(12)
                            ->disk('public')
                            ->directory('projects/gallery')
                            ->visibility('public')
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth('1600')
                            ->imageResizeTargetHeight('1600')
                            ->maxSize(8192)
                            ->panelLayout('grid'),
                        Forms\Components\Repeater::make('gallery_alts')
                            ->label('Alt-teksten van de galerij (in dezelfde volgorde)')
                            ->schema([
                                TextInput::make('nl')->label('NL')->maxLength(180),
                                TextInput::make('en')->label('EN')->maxLength(180),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->collapsible(),
                    ]),

                Forms\Components\Section::make('Links')
                    ->description('Een live-URL wordt elke 10 minuten gecontroleerd; de uptime staat op de casepagina en in het Ping-blok op de homepage.')
                    ->schema([
                        TextInput::make('demo_url')
                            ->label('Live URL')
                            ->url()
                            ->prefixIcon('heroicon-m-globe-alt')
                            ->nullable(),

                        TextInput::make('jebbari_url.nl')
                            ->label('Projectpagina op jebbari.nl (NL)')
                            ->url()
                            ->rule('starts_with:https://jebbari.nl')
                            ->placeholder('https://jebbari.nl/nl/werk/…')
                            ->helperText('Alleen voor projecten die met Jebbari zijn gebouwd: de case linkt dan naar Jebbari en deze pagina.'),

                        TextInput::make('jebbari_url.en')
                            ->label('Project page on jebbari.nl (EN)')
                            ->url()
                            ->rule('starts_with:https://jebbari.nl')
                            ->placeholder('https://jebbari.nl/en/work/…'),

                        TextInput::make('github_url')
                            ->label('GitHub URL')
                            ->url()
                            ->prefixIcon('heroicon-m-code-bracket')
                            ->nullable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Publicatie & SEO')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Gepubliceerd')
                            ->default(true)
                            ->rules([\App\Filament\Admin\Support\ReviewGuard::rule(Project::class, fn ($v) => (bool) $v)])
                            ->helperText('Uit = concept: alleen jij ziet de case (ingelogd, via "Voorbeeld"); niet in lijsten, sitemap of zoekmachines. Publiceren kan pas zonder [VERIFY]-markers.'),
                        Forms\Components\Select::make('status')
                            ->label('Projectstatus')
                            ->options(['live' => 'Live', 'building' => 'In ontwikkeling'])
                            ->placeholder('—'),
                        TextInput::make('image_alt.nl')->label('Alt-tekst afbeelding (NL)')->maxLength(180),
                        TextInput::make('image_alt.en')->label('Image alt text (EN)')->maxLength(180),
                        TextInput::make('seo_title.nl')->label('SEO-titel (NL)')->maxLength(60)->helperText('Max. 60 tekens. Leeg = automatisch.'),
                        TextInput::make('seo_title.en')->label('SEO title (EN)')->maxLength(60),
                        Textarea::make('seo_description.nl')->label('Meta-omschrijving (NL)')->rows(2)->maxLength(155)->helperText('Max. 155 tekens. Leeg = de eerste regel.'),
                        Textarea::make('seo_description.en')->label('Meta description (EN)')->rows(2)->maxLength(155),
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
                \App\Filament\Admin\Support\ReviewGuard::markersColumn(),
                \Filament\Tables\Columns\IconColumn::make('is_published')->label('Online')->boolean(),
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
                \App\Filament\Admin\Support\ReviewGuard::publishAction(
                    fn (Project $record): bool => (bool) $record->is_published,
                    fn (Project $record) => $record->update(['is_published' => true]),
                ),

                \App\Filament\Admin\Support\ReviewGuard::previewAction(
                    fn (Project $record): string => route('project.show', $record),
                    fn (Project $record): bool => (bool) $record->is_published,
                ),

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