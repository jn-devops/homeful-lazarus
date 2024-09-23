<?php

namespace App\Filament\Resources\PhilippineCityResource\Pages;

use App\Filament\Resources\PhilippineCityResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePhilippineCities extends ManageRecords
{
    protected static string $resource = PhilippineCityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
