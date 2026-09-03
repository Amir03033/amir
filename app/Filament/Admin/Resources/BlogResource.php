<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BlogResource\Pages;
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
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Translations')->tabs([
                    Tabs\Tab::make('Nederlands')->schema([
                        TextInput::make('title.nl')->label('Titel (NL)')->required()->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                        MarkdownEditor::make('content.nl')->label('Inhoud (NL)')->required(),
                    ]),
                    Tabs\Tab::make('English')->schema([
                        TextInput::make('title.en')->label('Title (EN)')->required(),
                        MarkdownEditor::make('content.en')->label('Content (EN)')->required(),
                    ]),
                ])->columnSpanFull(),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('blogs')
                    ->visibility('public')
                    ->maxSize(4096),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title.nl')->label('Titel (NL)'),
            TextColumn::make('slug'),
            TextColumn::make('created_at')->dateTime(),
        ])->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListBlogs::route('/'), 'create' => Pages\CreateBlog::route('/create'), 'edit' => Pages\EditBlog::route('/{record}/edit')];
    }
}