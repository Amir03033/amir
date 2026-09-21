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

                                MarkdownEditor::make('content.nl')
                                    ->label('Inhoud (NL)')
                                    ->required(),
                            ]),

                        Tabs\Tab::make('English')
                            ->schema([
                                TextInput::make('title.en')
                                    ->label('Title (EN)')
                                    ->required(),

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
                    ->maxSize(4096),

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
                            ->live(),

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
                        TextInput::make('meta_title')
                            ->label('Meta title')
                            ->maxLength(60)
                            ->helperText(
                                'Aanbevolen: ongeveer 50–60 tekens.'
                            ),

                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta description')
                            ->rows(4)
                            ->maxLength(160)
                            ->helperText(
                                'Aanbevolen: ongeveer 150–160 tekens.'
                            ),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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