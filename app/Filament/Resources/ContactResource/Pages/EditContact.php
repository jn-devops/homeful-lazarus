<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Resources\ContactResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Homeful\Contacts\Data\ContactData;
use Illuminate\Database\Eloquent\Model;

class EditContact extends EditRecord
{
    protected static string $resource = ContactResource::class;
    protected static ?string $cluster = Settings::class;
    protected function getHeaderActions(): array
    {
        return [
//            Actions\DeleteAction::make(),
            $this->getSaveFormAction()
                ->formId('form'),
        ];
    }
    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {

        $contact_data = ContactData::fromModel($this->record);
        $new_data = [];

        // Extracting data from contact_data for form
        $buyer_address_present = collect($contact_data->addresses)->firstWhere('type', 'primary') ?? [];
        $buyer_employment = collect($contact_data->employment)->firstWhere('type', 'buyer') ?? [];

        $new_data['buyer_address_present'] = $buyer_address_present;
        $new_data['buyer_employment'] = $buyer_employment;

        // Spouse details if available
        $new_data['spouse'] = $contact_data->spouse->toArray() ?? [];

        // Profile data
        $new_data['profile'] = $contact_data->profile->toArray();

        // Order and seller details
        $new_data['order'] = $contact_data->order->toArray();
//        $new_data['seller'] = $contact_data->order->toArray()['seller'] ?? [];
        $new_data['reference_code'] = $contact_data->reference_code;
        $new_data['co_borrowers'] = $contact_data->co_borrowers->toArray();
        $new_data['uploads']=$contact_data->uploads->toArray();
//        dd($new_data);
        return $new_data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
//        $data['last_edited_by_id'] = auth()->id();
//            collect($data['addresses'])->firstWhere(function ($address) {
//                return in_array($address['type'], ['present', 'Present', 'Primary','primary']);
//            })??[];
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        dd($data);
        $record->update($data);

        return $record;
    }




}
