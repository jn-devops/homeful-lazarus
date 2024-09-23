<?php

namespace App\Filament\Resources\Maintenance\ApproversResource\Pages;

use App\Filament\Resources\Maintenance\ApproversResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApprovers extends ListRecords
{
    protected static string $resource = ApproversResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
