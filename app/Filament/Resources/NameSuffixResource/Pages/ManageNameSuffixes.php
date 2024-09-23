<?php

namespace App\Filament\Resources\NameSuffixResource\Pages;

use App\Filament\Resources\NameSuffixResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNameSuffixes extends ManageRecords
{
    protected static string $resource = NameSuffixResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
