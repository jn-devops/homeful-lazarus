<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Clusters\Settings;
use App\Filament\Resources\ContactResource;
use App\Imports\OSImport;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Homeful\Contacts\Actions\PersistContactAction;
use Homeful\Contacts\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function handleRecordCreation( array $data): Model
    {
        $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        while (Contact::where('reference_code', $uuid)->exists()) {
            $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        }

        $attribs =[
            'reference_code'=> $uuid,
            'first_name' => $data['buyer']['first_name'],
            'middle_name' => $data['buyer']['middle_name'],
            'last_name' => $data['buyer']['last_name'],
            'name_suffix' => $data['buyer']['name_suffix'],
            'civil_status' => $data['buyer']['civil_status'],
            'sex' => $data['buyer']['gender'],
            'nationality' => $data['buyer']['nationality'],
            'date_of_birth' => $data['buyer']['date_of_birth'],
            'email' => $data['buyer']['email'],
            'mobile' => $data['buyer']['mobile'],
            'other_mobile' => $data['buyer']['other_mobile'],
            'landline' => $data['buyer']['landline'],

            // Create spouse if data is provided
            'spouse' => [
                'first_name' => $data['spouse']['first_name'] ?? null,
                'middle_name' => $data['spouse']['middle_name'] ?? null,
                'last_name' => $data['spouse']['last_name'] ?? null,
                'name_suffix' => $data['spouse']['name_suffix'] ?? null,
                'civil_status' => $data['spouse']['civil_status'] ?? null,
                'sex' => $data['spouse']['gender'] ?? null,
                'nationality' => $data['spouse']['nationality'] ?? null,
                'date_of_birth' => $data['spouse']['date_of_birth'] ?? null,
                'email' => $data['spouse']['email'] ?? null,
                'mobile' => $data['spouse']['mobile'] ?? null,
                'landline' => $data['spouse']['landline'] ?? null,
                'mothers_maiden_name' => $data['spouse']['mothers_maiden_name'] ?? null,
            ],

            // Add addresses
            'addresses' => [
                [
                    'type' => 'present',
                    'full_address' => $data['buyer']['address']['present']['full_address'] ?? null,
                    'address1'=>$data['buyer']['address']['present']['address'] ?? null,
                    'sublocality' => $data['buyer']['address']['present']['barangay'] ?? null,
                    'locality' => $data['buyer']['address']['present']['city'] ?? null,
                    'administrative_area' => $data['buyer']['address']['present']['province'] ?? null,
                    'postal_code' => $data['buyer']['address']['present']['postal_code'] ?? null,
                    'block' => $data['buyer']['address']['present']['block'] ?? null,
                    'street' => $data['buyer']['address']['present']['street'] ?? null,
                    'ownership' => $data['buyer']['address']['present']['ownership'] ?? null,
                    'country' => $data['buyer']['address']['present']['country'] ?? '',
                    'region' => $data['buyer']['address']['present']['region'] ?? '',
                ],
                [
                    'type' => 'permanent',
                    'full_address' => $data['buyer']['address']['permanent']['full_address'] ?? null,
                    'address1'=>$data['buyer']['address']['permanent']['address'] ?? null,
                    'sublocality' => $data['buyer']['address']['permanent']['barangay'] ?? null,
                    'locality' => $data['buyer']['address']['permanent']['city'] ?? null,
                    'administrative_area' => $data['buyer']['permanent']['province'] ?? null,
                    'postal_code' => $data['buyer']['address']['permanent']['postal_code'] ?? null,
                    'block' => $data['buyer']['address']['permanent']['block'] ?? null,
                    'street' => $data['buyer']['address']['permanent']['street'] ?? null,
                    'ownership' => $data['buyer']['address']['permanent']['ownership'] ?? null,
                    'country' => $data['buyer']['address']['permanent']['country'] ?? '',
                    'region' => $data['buyer']['address']['permanent']['region'] ?? '',
                ]
            ],

            // Add employment
            'employment' => [
                [
                    'type'=>'buyer',
                    'employment_status' => $data['buyer_employment']['employment_status'] ?? null,
                    'monthly_gross_income' => $data['buyer_employment']['monthly_gross_income'] ?? null,
                    'current_position' => $data['buyer_employment']['current_position'] ?? null,
                    'employment_type' => $data['buyer_employment']['employment_type'] ?? null,
                    'years_in_service' => $data['buyer_employment']['years_in_service'] ?? null,
                    'employer' => [
                        'name' => $data['buyer_employment']['employer']['name'] ?? null,
                        'industry' => $data['buyer_employment']['employer']['industry'] ?? null,
                        'nationality' => $data['buyer_employment']['employer']['nationality'] ?? null,
                        'contact_no' => $data['buyer_employment']['employer']['contact_no'] ?? null,
                        'year_established' => $data['buyer_employment']['employer']['year_established'] ?? null,
                        'total_number_of_employees' => $data['buyer_employment']['employer']['total_number_of_employees'] ?? null,
                        'email' => $data['buyer_employment']['employer']['email'] ?? null,
                        'fax' => $data['buyer_employment']['employer']['fax'] ?? null,

                        // Expanding the employer address structure
                        'address' => [
                            'full_address' => $data['buyer_employment']['employer']['address']['full_address'] ?? null,
                            'locality' => $data['buyer_employment']['employer']['address']['locality'] ?? null,
                            'administrative_area' => $data['buyer_employment']['employer']['address']['administrative_area'] ?? null,
                            'country' => $data['buyer_employment']['employer']['address']['country'] ?? null,
                            'ownership' =>'company',
                            'type'=>'company',
                        ]
                    ],
                    'id' => [
                        'tin' => $data['buyer_employment']['id']['tin'] ?? null,
                        'pagibig' => $data['buyer_employment']['id']['pag_ibig'] ?? null,
                        'sss' => $data['buyer_employment']['id']['sss_gsis'] ?? null,
                        'gsis' => $data['buyer_employment']['id']['sss_gsis'] ?? null,
                    ],
                    'character_reference'=> $data['buyer_employment']['character_reference']??[],
                ]
            ],
//            'employment'=>[
//                $data['buyer_employment']
//            ],
            'order'=>$data['order']
        ];
        dd($attribs, $data);
        $action = app(PersistContactAction::class);
        $validator = Validator::make($attribs, $action->rules());

        if ($validator->fails()) {
            dd($validator);
            throw new ValidationException($validator);
        }
        $validated = $validator->validated();
        // dd($attribs, $validated);
        $contact = $action->run($validated);

        $record = Contact::where('reference_code', $uuid)->first();
        return $record;
    }

}
