<?php

namespace App\Filament\Admin\Resources\IdeaResource\Pages;

use App\Filament\Admin\Resources\IdeaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIdea extends EditRecord
{
    protected static string $resource = IdeaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
