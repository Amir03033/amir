<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\IdeaResource\Pages;
use App\Models\Idea;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

/** "Suggest something": approve visitor ideas and move them across the public roadmap. */
class IdeaResource extends Resource
{
    protected static ?string $model = Idea::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationLabel = 'Ideeën & roadmap';

    protected static ?string $modelLabel = 'Idee';

    protected static ?string $pluralModelLabel = 'Ideeën';

    protected static ?string $navigationGroup = 'Inbox';

    protected static ?int $navigationSort = 3;

    public const STATUS_LABELS = [
        'new' => 'Nieuw / idee',
        'planned' => 'Gepland',
        'building' => 'Mee bezig',
        'shipped' => 'Gelanceerd',
        'declined' => 'Niet gedaan',
    ];

    public static function getNavigationBadge(): ?string
    {
        $pending = Idea::where('is_public', false)->where('status', 'new')->count();

        return $pending ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('title')->label('Idee')->required()->maxLength(140),
                Forms\Components\Textarea::make('body')->label('Toelichting')->rows(4)->maxLength(1000),
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Select::make('status')->label('Status')->options(self::STATUS_LABELS)->required()->default('new'),
                    Forms\Components\Toggle::make('is_public')->label('Op de roadmap')->inline(false)
                        ->helperText('Nieuwe ideeën van bezoekers staan pas online als je dit aanzet.'),
                    Forms\Components\TextInput::make('sort_order')->label('Volgorde')->numeric()->integer()->default(0)
                        ->helperText('Lager = hoger in de lijst; daarna op stemmen.'),
                ]),
                Forms\Components\Placeholder::make('votes_count')->label('Stemmen')->content(fn (?Idea $record) => $record?->votes_count ?? 0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')->label('Idee')->wrap()->searchable()->description(fn (Idea $i) => str($i->body)->limit(80)),
                SelectColumn::make('status')->label('Status')->options(self::STATUS_LABELS)->selectablePlaceholder(false),
                TextColumn::make('votes_count')->label('Stemmen')->sortable()->alignEnd(),
                ToggleColumn::make('is_public')->label('Op de roadmap'),
                TextColumn::make('locale')->label('Taal')->badge()->color('gray')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Ingestuurd')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(self::STATUS_LABELS),
                TernaryFilter::make('is_public')->label('Op de roadmap'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')->label('Op de roadmap zetten')->icon('heroicon-m-eye')
                        ->action(fn (Collection $records) => $records->each->update(['is_public' => true]))->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('status')->label('Status wijzigen')->icon('heroicon-m-arrow-path')
                        ->form([Forms\Components\Select::make('status')->options(self::STATUS_LABELS)->required()])
                        ->action(fn (Collection $records, array $data) => $records->each->update(['status' => $data['status']]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIdeas::route('/'),
            'create' => Pages\CreateIdea::route('/create'),
            'edit' => Pages\EditIdea::route('/{record}/edit'),
        ];
    }
}
