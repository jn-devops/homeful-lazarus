<?php

namespace App\Filament\Resources\Maintenance\DocumentsResource\Pages;

use App\Filament\Resources\Maintenance\DocumentsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Howdu\FilamentRecordSwitcher\Filament\Concerns\HasRecordSwitcher;

class EditDocuments extends EditRecord
{
    use HasRecordSwitcher;

    protected static string $resource = DocumentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
