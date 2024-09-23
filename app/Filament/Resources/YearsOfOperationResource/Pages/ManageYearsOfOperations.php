<?php

namespace App\Filament\Resources\YearsOfOperationResource\Pages;

use App\Filament\Resources\YearsOfOperationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageYearsOfOperations extends ManageRecords
{
    protected static string $resource = YearsOfOperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
