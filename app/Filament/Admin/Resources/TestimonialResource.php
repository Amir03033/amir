<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Reviews';

    protected static ?string $modelLabel = 'Review';

    protected static ?string $pluralModelLabel = 'Reviews';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Wie')
                ->description('Alleen echte reviews van echte opdrachtgevers. Vraag altijd toestemming om naam en quote te tonen.')
                ->schema([
                    Forms\Components\TextInput::make('name')->label('Naam')->required(),
                    Forms\Components\TextInput::make('role')->label('Functie')->placeholder('Eigenaar'),
                    Forms\Components\TextInput::make('company')->label('Bedrijf'),
                    Forms\Components\TextInput::make('url')->label('Website / LinkedIn')->url(),
                    Forms\Components\Select::make('project_id')->label('Bij project')->relationship('project', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('title'))
                        ->searchable()->preload(),
                    Forms\Components\FileUpload::make('avatar')->label('Foto')->image()->avatar()
                        ->disk('public')->directory('testimonials')->visibility('public')
                        ->imageCropAspectRatio('1:1')->imageResizeMode('cover')
                        ->imageResizeTargetWidth('200')->imageResizeTargetHeight('200'),
                ])->columns(2),

            Forms\Components\Tabs::make('Quote')->tabs([
                Forms\Components\Tabs\Tab::make('Nederlands')->schema([
                    Forms\Components\Textarea::make('quote.nl')->label('Quote (NL)')->required()->rows(4)->maxLength(600),
                ]),
                Forms\Components\Tabs\Tab::make('English')->schema([
                    Forms\Components\Textarea::make('quote.en')->label('Quote (EN)')->rows(4)->maxLength(600)
                        ->helperText('Leeg = de Nederlandse quote wordt getoond.'),
                ]),
            ])->columnSpanFull(),

            Forms\Components\Toggle::make('is_visible')->label('Tonen op de site')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')->label('')->circular(),
                Tables\Columns\TextColumn::make('name')->label('Naam')->weight('bold')
                    ->description(fn (Testimonial $r) => collect([$r->role, $r->company])->filter()->implode(' · '))
                    ->searchable(),
                Tables\Columns\TextColumn::make('quote.nl')->label('Quote')->limit(70)->wrap(),
                Tables\Columns\ToggleColumn::make('is_visible')->label('Zichtbaar'),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->emptyStateHeading('Nog geen reviews')
            ->emptyStateDescription('Vraag een opdrachtgever om 2–3 zinnen over de samenwerking. De sectie verschijnt pas op de site zodra er een zichtbare review is.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
