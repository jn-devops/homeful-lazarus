<?php

namespace App\Filament\Resources\TenureResource\Pages;

use App\Filament\Resources\TenureResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTenures extends ManageRecords
{
    protected static string $resource = TenureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
