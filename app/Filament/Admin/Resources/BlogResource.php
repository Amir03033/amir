<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Blogs';

    protected static ?string $modelLabel = 'Blog';

    protected static ?string $pluralModelLabel = 'Blogs';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'slug';

    public static function getNavigationBadge(): ?string
    {
        $drafts = Blog::count() - Blog::published()->count();

        return $drafts ? (string) $drafts : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Concepten en geplande blogs (nog niet zichtbaar op de site)';
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
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn ($state, $set) =>
                                        $set('slug', Str::slug($state))
                                    ),

                                Forms\Components\Textarea::make('excerpt.nl')
                                    ->label('Samenvatting (NL)')
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->helperText('Eén of twee zinnen voor lijsten en de meta-omschrijving. Leeg = begin van de tekst.'),

                                MarkdownEditor::make('content.nl')
                                    ->label('Inhoud (NL)')
                                    ->required(),
                            ]),

                        Tabs\Tab::make('English')
                            ->schema([
                                TextInput::make('title.en')
                                    ->label('Title (EN)')
                                    ->required(),

                                Forms\Components\Textarea::make('excerpt.en')
                                    ->label('Excerpt (EN)')
                                    ->rows(2)
                                    ->maxLength(300),

                                MarkdownEditor::make('content.en')
                                    ->label('Content (EN)')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                FileUpload::make('image')
                    ->label('Afbeelding')
                    ->image()
                    ->disk('public')
                    ->directory('blogs')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageResizeMode('contain')
                    ->imageResizeTargetWidth('1600')
                    ->imageResizeTargetHeight('1600')
                    ->maxSize(8192)
                    ->helperText('Wordt bij uploaden automatisch verkleind tot max. 1600px.'),

                Forms\Components\Section::make('Publicatie')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Concept',
                                'scheduled' => 'Gepland',
                                'published' => 'Gepubliceerd',
                            ])
                            ->required()
                            ->default('draft')
                            ->live()
                            ->rules([\App\Filament\Admin\Support\ReviewGuard::rule(Blog::class, fn ($v) => in_array($v, ['published', 'scheduled'], true))])
                            ->helperText('Concepten zijn alleen voor jou te zien (ingelogd, via "Voorbeeld"). Publiceren kan pas als er geen [VERIFY]-markers meer in de tekst staan.'),

                        DateTimePicker::make('published_at')
                            ->label('Publicatiedatum')
                            ->seconds(false)
                            ->nullable()
                            ->visible(
                                fn (Forms\Get $get): bool =>
                                in_array(
                                    $get('status'),
                                    ['scheduled', 'published'],
                                    true
                                )
                            )
                            ->required(
                                fn (Forms\Get $get): bool =>
                                    $get('status') === 'scheduled'
                            ),

                        TextInput::make('category')
                            ->label('Categorie')
                            ->placeholder('Bijvoorbeeld: Laravel'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Tags')
                    ->schema([
                        TagsInput::make('tags')
                            ->label('Tags')
                            ->placeholder('Voeg een tag toe...')
                            ->helperText(
                                'Druk op Enter om een tag toe te voegen.'
                            ),
                    ]),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title.nl')
                            ->label('SEO-titel (NL)')
                            ->maxLength(60)
                            ->helperText('Max. 60 tekens. Leeg = de titel met " – Amir Jebbari".'),

                        TextInput::make('meta_title.en')
                            ->label('SEO title (EN)')
                            ->maxLength(60),

                        Forms\Components\Textarea::make('meta_description.nl')
                            ->label('Meta-omschrijving (NL)')
                            ->rows(3)
                            ->maxLength(155)
                            ->helperText('Max. 155 tekens. Leeg = de samenvatting of het begin van de tekst.'),

                        Forms\Components\Textarea::make('meta_description.en')
                            ->label('Meta description (EN)')
                            ->rows(3)
                            ->maxLength(155),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \App\Filament\Admin\Support\ReviewGuard::markersColumn(),
                TextColumn::make('title.nl')
                    ->label('Titel (NL)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->label('Categorie')
                    ->badge()
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'draft' => 'Concept',
                            'scheduled' => 'Gepland',
                            'published' => 'Gepubliceerd',
                            default => $state,
                        }
                    )
                    ->color(
                        fn (string $state): string => match ($state) {
                            'draft' => 'gray',
                            'scheduled' => 'warning',
                            'published' => 'success',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('published_at')
                    ->label('Publicatiedatum')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Concept',
                        'scheduled' => 'Gepland',
                        'published' => 'Gepubliceerd',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Categorie')
                    ->options(
                        fn () => Blog::query()
                            ->whereNotNull('category')
                            ->where('category', '!=', '')
                            ->distinct()
                            ->orderBy('category')
                            ->pluck('category', 'category')
                            ->toArray()
                    ),
            ])

            ->actions([
                \App\Filament\Admin\Support\ReviewGuard::publishAction(
                    fn (Blog $record): bool => $record->isPublished(),
                    fn (Blog $record) => $record->update(['status' => 'published', 'published_at' => now()]),
                ),

                \App\Filament\Admin\Support\ReviewGuard::previewAction(
                    fn (Blog $record): string => route('blog.show', $record->slug),
                    fn (Blog $record): bool => $record->isPublished(),
                ),

                Tables\Actions\EditAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])

            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}