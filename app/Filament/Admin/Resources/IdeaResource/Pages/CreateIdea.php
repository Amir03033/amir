<?php

namespace App\Filament\Admin\Resources\IdeaResource\Pages;

use App\Filament\Admin\Resources\IdeaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIdea extends CreateRecord
{
    protected static string $resource = IdeaResource::class;

    /** Amir's own roadmap items have no visitor fingerprint. */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data + ['voter_hash' => 'admin', 'locale' => app()->getLocale()];
    }
}
