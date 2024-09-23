<?php

namespace App\Filament\Resources\PhilippineRegionResource\Pages;

use App\Filament\Resources\PhilippineRegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePhilippineRegions extends ManageRecords
{
    protected static string $resource = PhilippineRegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
