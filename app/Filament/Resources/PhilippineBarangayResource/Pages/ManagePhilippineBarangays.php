<?php

namespace App\Filament\Resources\PhilippineBarangayResource\Pages;

use App\Filament\Resources\PhilippineBarangayResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePhilippineBarangays extends ManageRecords
{
    protected static string $resource = PhilippineBarangayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
