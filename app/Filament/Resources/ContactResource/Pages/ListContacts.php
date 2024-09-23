<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Resources\ContactResource;
use App\Imports\OSImport;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use YOS\FilamentExcel\Actions\Import;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;
    protected static ?string $cluster = Settings::class;
    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
//            Import::make()
//                ->import(OSImport::class)
//                ->type(\Maatwebsite\Excel\Excel::XLSX)
//                ->label('Import from excel')
//                ->hint('Upload xlsx type')
////                ->icon(HeroIcons::C_ARROW_UP)
//                ->color('success'),
//            Actions\Action::make('test')
//            ->action(function(){
//                Notification::make()
//                    ->title('Saved successfully')
////                    ->sendToDatabase(auth()->user())
//                    ->broadcast(auth()->user());
//            })
        ];
    }
}
