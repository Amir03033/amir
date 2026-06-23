<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Str;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Blog Artikelen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Meertalige Sectie met Tabs voor de Content
                Tabs::make('Translations')
                    ->tabs([
                        Tabs\Tab::make('Nederlands')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextInput::make('title.nl')
                                    ->label('Titel (NL)')
                                    ->required()
                                    // Genereer automatisch de slug op basis van de NL titel
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, $set) =>
                                    $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                                MarkdownEditor::make('content.nl')
                                    ->label('Inhoud (NL)')
                                    ->required(),
                            ]),
                        Tabs\Tab::make('English')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextInput::make('title.en')
                                    ->label('Title (EN)')
                                    ->required(),

                                MarkdownEditor::make('content.en')
                                    ->label('Content (EN)')
                                    ->required(),
                            ]),
                    ])->columnSpanFull(),

                // Systeeminformatie en Media
                Section::make('Blog Instellingen')
                    ->description('Beheer de URL en de omslagafbeelding van het artikel.')
                    ->schema([
                        TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->placeholder('mijn-eerste-blogpost'),

                        FileUpload::make('image')
                            ->label('Blog Banner / Omslag')
                            ->image()
                            ->directory('blogs') // Slaat op in storage/app/public/blogs
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Banner')
                    ->square(),

                TextColumn::make('title.nl')
                    ->label('Titel (NL)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('URL / Slug')
                    ->fontFamily('mono')
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Gepubliceerd op')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}