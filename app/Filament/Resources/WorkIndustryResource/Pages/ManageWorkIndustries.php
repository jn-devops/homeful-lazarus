<?php

namespace App\Filament\Resources\WorkIndustryResource\Pages;

use App\Filament\Resources\WorkIndustryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageWorkIndustries extends ManageRecords
{
    protected static string $resource = WorkIndustryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
