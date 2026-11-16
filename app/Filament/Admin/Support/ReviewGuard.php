<?php

namespace App\Filament\Admin\Support;

use Closure;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

/**
 * Drafts with "[VERIFY: …]" markers can't be published: the status field refuses it with a list of
 * where the markers are, and the table "Publish" action does the same as a notification.
 */
class ReviewGuard
{
    /**
     * Validation rule for the publish field (Blog "status", Project "is_published").
     *
     * @param  Closure(mixed): bool  $publishes  whether the chosen value means "public"
     */
    public static function rule(string $modelClass, Closure $publishes): Closure
    {
        return fn ($livewire) => function (string $attribute, mixed $value, Closure $fail) use ($livewire, $modelClass, $publishes) {
            if (! $publishes($value)) {
                return;
            }
            $found = (new $modelClass)->reviewMarkers($livewire->data ?? []);
            if ($found) {
                $fail($modelClass::markerMessage($found));
            }
        };
    }

    /** Table action: publish now, unless markers remain (then a clear notification lists where). */
    public static function publishAction(Closure $isPublished, Closure $publish): Action
    {
        return Action::make('publish')
            ->label('Publiceer')
            ->icon('heroicon-o-rocket-launch')
            ->color('success')
            ->requiresConfirmation()
            ->modalDescription('Na publiceren is dit zichtbaar voor iedereen, in de sitemap en in zoekmachines.')
            ->visible(fn (Model $record): bool => ! $isPublished($record))
            ->action(function (Model $record) use ($publish) {
                if ($found = $record->reviewMarkers()) {
                    Notification::make()
                        ->danger()
                        ->title('Nog niet te publiceren')
                        ->body($record::markerMessage($found))
                        ->persistent()
                        ->send();

                    return;
                }
                $publish($record);
                Notification::make()->success()->title('Gepubliceerd')->send();
            });
    }

    /** Table action: open the public page; drafts open as a private preview for the logged-in owner. */
    public static function previewAction(Closure $url, Closure $isPublished): Action
    {
        return Action::make('view')
            ->label(fn (Model $record) => $isPublished($record) ? 'Bekijk' : 'Voorbeeld')
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->url($url, shouldOpenInNewTab: true);
    }

    public static function markersColumn(): TextColumn
    {
        return TextColumn::make('review_markers')
            ->label('[VERIFY]')
            ->state(fn (Model $record) => count($record->reviewMarkers()) ?: null)
            ->placeholder('—')
            ->badge()
            ->color('warning')
            ->tooltip('Aantal [VERIFY]-markers dat nog weg moet voor publiceren');
    }
}
