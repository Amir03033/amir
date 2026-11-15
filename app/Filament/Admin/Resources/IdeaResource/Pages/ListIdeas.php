<?php

namespace App\Filament\Admin\Resources\IdeaResource\Pages;

use App\Filament\Admin\Resources\IdeaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIdeas extends ListRecords
{
    protected static string $resource = IdeaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Eigen roadmap-item')];
    }
}
