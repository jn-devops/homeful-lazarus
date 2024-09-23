<?php

namespace App\Filament\Resources\Maintenance\ProjectsResource\Pages;

use App\Filament\Resources\Maintenance\ProjectsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProjects extends ManageRecords
{
    protected static string $resource = ProjectsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
