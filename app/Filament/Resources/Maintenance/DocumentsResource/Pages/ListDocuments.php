<?php

namespace App\Filament\Resources\Maintenance\DocumentsResource\Pages;

use App\Filament\Resources\Maintenance\DocumentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
