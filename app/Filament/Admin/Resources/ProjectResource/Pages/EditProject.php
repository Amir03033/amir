<?php

namespace App\Filament\Admin\Resources\ProjectResource\Pages;

use App\Filament\Admin\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /** The form edits the gallery as rows (light, dark, alts); the database keeps three columns. */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['gallery_rows'] = Project::galleryRows($data);
        unset($data['gallery'], $data['gallery_dark'], $data['gallery_alts']);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return ProjectResource::galleryFromRows($data);
    }
}
