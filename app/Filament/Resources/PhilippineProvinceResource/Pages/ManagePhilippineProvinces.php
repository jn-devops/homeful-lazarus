<?php

namespace App\Filament\Resources\PhilippineProvinceResource\Pages;

use App\Filament\Resources\PhilippineProvinceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePhilippineProvinces extends ManageRecords
{
    protected static string $resource = PhilippineProvinceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
