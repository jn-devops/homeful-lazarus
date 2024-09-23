<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Resources\ContactResource;
use App\Imports\OSImport;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Maatwebsite\Excel\Facades\Excel;

class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
//        $data['user_id'] = auth()->id();

        Excel::queueImport(new OSImport, $data['file'], null, \Maatwebsite\Excel\Excel::XLSX);
        dd('done');
        return $data;
    }

}
