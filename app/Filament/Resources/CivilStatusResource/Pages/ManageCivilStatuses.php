<?php

namespace App\Filament\Resources\CivilStatusResource\Pages;

use App\Filament\Resources\CivilStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCivilStatuses extends ManageRecords
{
    protected static string $resource = CivilStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
