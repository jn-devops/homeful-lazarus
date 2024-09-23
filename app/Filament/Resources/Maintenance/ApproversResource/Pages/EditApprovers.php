<?php

namespace App\Filament\Resources\Maintenance\ApproversResource\Pages;

use App\Filament\Resources\Maintenance\ApproversResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Howdu\FilamentRecordSwitcher\Filament\Concerns\HasRecordSwitcher;

class EditApprovers extends EditRecord
{
    use HasRecordSwitcher;
    protected static string $resource = ApproversResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
