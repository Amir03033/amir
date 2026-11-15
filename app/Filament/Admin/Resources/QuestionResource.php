<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\QuestionResource\Pages;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** "Ask Amir": read visitor questions, answer them, and choose which answers appear on the site. */
class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Vragen (Ask Amir)';

    protected static ?string $modelLabel = 'Vraag';

    protected static ?string $pluralModelLabel = 'Vragen';

    protected static ?string $navigationGroup = 'Inbox';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $open = Question::whereNull('answer')->count();

        return $open ? (string) $open : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false; // questions come from visitors
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Vraag')
                ->schema([
                    Forms\Components\Textarea::make('body')->label('Vraag')->rows(4)->required()->maxLength(1000)
                        ->helperText('Je mag de vraag licht redigeren (spelling) voordat je hem openbaar maakt.'),
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('name')->label('Naam')->maxLength(80)->placeholder('Anoniem'),
                        Forms\Components\TextInput::make('email')->label('E-mail')->email()->disabled()
                            ->helperText('Nooit zichtbaar op de site.')
                            ->suffixAction(fn (?string $state) => $state ? Forms\Components\Actions\Action::make('mail')->icon('heroicon-m-envelope')->url('mailto:'.$state) : null),
                        Forms\Components\Select::make('locale')->label('Taal')->options(['nl' => 'Nederlands', 'en' => 'English'])->disabled(),
                    ]),
                ]),
            Forms\Components\Section::make('Antwoord')
                ->schema([
                    Forms\Components\Textarea::make('answer')->label('Antwoord')->rows(8)->maxLength(5000)->live(onBlur: true)
                        ->helperText('Schrijf in de taal van de vraag. Leegmaken haalt de vraag ook van de site.'),
                    Forms\Components\Toggle::make('is_public')->label('Tonen op de site')
                        ->helperText('Alleen beantwoorde vragen kunnen openbaar zijn.')
                        ->disabled(fn (Forms\Get $get) => blank($get('answer'))),
                    Forms\Components\Placeholder::make('answered_at')->label('Beantwoord op')
                        ->content(fn (?Question $record) => $record?->answered_at?->translatedFormat('j F Y, H:i') ?? '—'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('body')->label('Vraag')->limit(90)->wrap()->searchable(),
                TextColumn::make('name')->label('Naam')->placeholder('Anoniem')->toggleable(),
                TextColumn::make('locale')->label('Taal')->badge()->color('gray'),
                TextColumn::make('answered_at')->label('Status')->badge()
                    ->getStateUsing(fn (Question $q) => $q->isAnswered() ? 'Beantwoord' : 'Open')
                    ->color(fn (string $state) => $state === 'Open' ? 'warning' : 'success'),
                ToggleColumn::make('is_public')->label('Openbaar')
                    ->disabled(fn (Question $q) => ! $q->isAnswered()),
                TextColumn::make('created_at')->label('Ontvangen')->since()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('answered')->label('Beantwoord')
                    ->queries(true: fn (Builder $q) => $q->whereNotNull('answer'), false: fn (Builder $q) => $q->whereNull('answer')),
                TernaryFilter::make('is_public')->label('Openbaar'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Beantwoorden'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
