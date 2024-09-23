<?php

namespace App\Filament\Resources\HomeOwnershipResource\Pages;

use App\Filament\Resources\HomeOwnershipResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHomeOwnerships extends ManageRecords
{
    protected static string $resource = HomeOwnershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
