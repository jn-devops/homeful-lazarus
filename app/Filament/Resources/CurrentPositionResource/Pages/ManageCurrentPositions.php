<?php

namespace App\Filament\Resources\CurrentPositionResource\Pages;

use App\Filament\Resources\CurrentPositionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCurrentPositions extends ManageRecords
{
    protected static string $resource = CurrentPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
