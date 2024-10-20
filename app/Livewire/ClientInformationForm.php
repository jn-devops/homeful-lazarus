<?php

namespace App\Livewire;

use App\Models\CivilStatus;
use App\Models\Country;
use App\Models\CurrentPosition;
use App\Models\EmploymentStatus;
use App\Models\EmploymentType;
use App\Models\HomeOwnership;
use App\Models\NameSuffix;
use App\Models\Nationality;
use App\Models\PhilippineBarangay;
use App\Models\PhilippineCity;
use App\Models\PhilippineProvince;
use App\Models\PhilippineRegion;
use App\Models\Tenure;
use App\Models\WorkIndustry;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Homeful\Contacts\Models\Contact;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class ClientInformationForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public string $screenSize=''; // Default value is 'desktop'

    public $error='';

    public function mount(Request $request): void
    {
        $this->form->fill();

        $this->data['buyer']['last_name'] = $request->query('last_name', '');
        $this->data['buyer']['mobile'] = $request->query('mobile', '');
    }

    public function form(Form $form): Form
    {
        return $form
            ->live()
            ->reactive()
            ->schema([
                //desktop start
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Personal Information')->schema([
                            //Personal Information
                            Forms\Components\Fieldset::make('Personal')->schema([
                                TextInput::make('buyer.last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                TextInput::make('buyer.first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->hint('with ex. Sr. Jr.')
                                    ->maxLength(255)
                                    ->columnSpan(3),

                                TextInput::make('buyer.middle_name')
                                    ->label('Middle Name')
                                    ->maxLength(255)
//                                    ->required(fn (Get $get): bool => ! $get('no_middle_name'))
//                                    ->readOnly(fn (Get $get): bool => $get('no_middle_name'))
//                                            ->hidden(fn (Get $get): bool =>  $get('no_middle_name'))
                                    ->columnSpan(3),
//                                Select::make('buyer.name_suffix')
//                                    ->label('Suffix')
//                                    ->required()
//                                    ->native(false)
//                                    ->options(NameSuffix::all()->pluck('description','code'))
//                                    ->columnSpan(2),
//                                Forms\Components\Checkbox::make('no_middle_name')
//                                    ->live()
//                                    ->inline(false)
//                                    ->afterStateUpdated(function(Get $get,Set $set){
//                                        $set('buyer.middle_name',null);
////                                                if ($get('no_middle_name')) {
////                                                }
//                                    })
//                                    ->columnSpan(1),
                                Select::make('buyer.civil_status')
                                    ->live()
                                    ->label('Civil Status')
                                    ->required()
                                    ->native(false)
                                    ->options(CivilStatus::all()->pluck('description','code'))
                                    ->columnSpan(3),
                                Select::make('buyer.gender')
                                    ->label('Gender')
                                    ->required()
                                    ->native(false)
                                    ->options([
                                        'Male'=>'Male',
                                        'Female'=>'Female'
                                    ])
                                    ->columnSpan(3),
                                DatePicker::make('buyer.date_of_birth')
                                    ->label('Date of Birth')
                                    ->hint(function ($state): string {
                                        if ($state) {
                                            $dateOfBirth = Carbon::parse($state);
                                            $age = $dateOfBirth->age;
                                            return "Age: $age years";
                                        }
                                        return '';
                                    })
                                    ->hintColor('primary')
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(3),
//                                Select::make('buyer.nationality')
//                                    ->label('Nationality')
//                                    ->required()
//                                    ->native(false)
//                                    ->searchable()
//                                    ->options(Nationality::all()->pluck('description','code'))
//                                    ->columnSpan(3),
                                Forms\Components\ToggleButtons::make('has_pagibig_number')
                                    ->label('Has PAG-IBIG Number?')
                                    ->inline()
                                    ->required()
                                    ->boolean()
                                    ->live()
                                    ->columnSpan(3),
                                TextInput::make('buyer_employment.id.pag_ibig')
                                    ->label('PAG-IBIG Number')
                                    ->required(fn (Get $get): bool=>$get('has_pagibig_number')==true||$get('has_pagibig_number')==null)
                                    ->hidden(fn (Get $get): bool=>$get('has_pagibig_number')!=true||$get('has_pagibig_number')==null)
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                Forms\Components\ToggleButtons::make('hloan')
                                    ->label('Existing housing loan with PAG-IBIG?')
                                    ->inline()
                                    ->required()
                                    ->required(fn (Get $get): bool=>$get('has_pagibig_number')==true||$get('has_pagibig_number')==null)
                                    ->hidden(fn (Get $get): bool=>$get('has_pagibig_number')!=true||$get('has_pagibig_number')==null)
                                    ->boolean()
                                    ->columnSpan(3),
                                Select::make('buyer.address.present.ownership')
                                    ->label('Home Ownersip')
                                    ->options(HomeOwnership::all()->pluck('description','code'))
                                    ->native(false)
                                    ->required()
                                    ->columnSpan(3),

                            ])->columns(12)->columnSpanFull(),
                            \Filament\Forms\Components\Fieldset::make('Contact Information')
                                ->schema([
                                    Forms\Components\TextInput::make('buyer.email')
                                        ->label('Email')
                                        ->email()
                                        ->required()
                                        ->maxLength(255)
                                        ->live()
                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                            $livewire->validateOnly($component->getStatePath());
                                        })
                                        ->unique(ignoreRecord: true,table: Contact::class,column: 'email')
                                        ->columnSpan(3),

                                    Forms\Components\TextInput::make('buyer.mobile')
                                        ->label('Mobile')
                                        ->required()
                                        ->prefix('+63')
                                        ->regex("/^[0-9]+$/")
                                        ->minLength(10)
                                        ->maxLength(10)
                                        ->live()
                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                            $livewire->validateOnly($component->getStatePath());
                                        })
                                        ->columnSpan(3),

//                                    Forms\Components\TextInput::make('buyer.other_mobile')
//                                        ->label('Other Mobile')
//                                        ->prefix('+63')
//                                        ->regex("/^[0-9]+$/")
//                                        ->minLength(10)
//                                        ->maxLength(10)
//                                        ->live()
//                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
//                                            $livewire->validateOnly($component->getStatePath());
//                                        })
//                                        ->columnSpan(3),
//
//                                    Forms\Components\TextInput::make('buyer.landline')
//                                        ->label('Landline')
//                                        ->columnSpan(3),
                                ])->columns(12)->columnSpanFull(),
                            //Address
                            \Filament\Forms\Components\Fieldset::make('Address')
                                ->schema([
                                    Forms\Components\Fieldset::make('Present')->schema([
                                        Select::make('buyer.address.present.ownership')
                                            ->options(HomeOwnership::all()->pluck('description','code'))
                                            ->native(false)
                                            ->required()
                                            ->columnSpan(3),
                                        Select::make('buyer.address.present.country')
                                            ->searchable()
                                            ->options(Country::all()->pluck('description','code'))
                                            ->native(false)
                                            ->live()
                                            ->required()
                                            ->columnSpan(3),
                                        TextInput::make('buyer.address.present.postal_code')
                                            ->minLength(4)
                                            ->maxLength(4)
                                            ->required()
                                            ->columnSpan(3),
                                        Checkbox::make('buyer.address.present.same_as_permanent')
                                            ->label('Same as Permanent')
                                            ->inline(false)
                                            ->live()
                                            ->columnSpan(3),
                                        Select::make('buyer.address.present.region')
                                            ->searchable()
                                            ->options(PhilippineRegion::all()->pluck('region_description', 'region_code'))
                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
                                            ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
                                            ->native(false)
                                            ->live()
                                            ->afterStateUpdated(function (Set $set) {
                                                $set('buyer.address.present.province', '');
                                                $set('buyer.address.present.city', '');
                                                $set('buyer.address.present.barangay', '');
                                            })
                                            ->columnSpan(3),
                                        Select::make('buyer.address.present.province')
                                            ->searchable()
                                            ->options(fn(Get $get): Collection => PhilippineProvince::query()
                                                ->where('region_code', $get('buyer.address.present.region'))
                                                ->pluck('province_description', 'province_code'))
                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
                                            ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
                                            ->native(false)
                                            ->live()
                                            ->afterStateUpdated(function (Set $set) {
                                                $set('buyer.address.present.city', '');
                                                $set('buyer.address.present.barangay', '');
                                            })
                                            ->columnSpan(3),
                                        Select::make('buyer.address.present.city')
                                            ->searchable()
                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
                                            ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
                                            ->options(fn(Get $get): Collection => PhilippineCity::query()
                                                ->where('province_code', $get('buyer.address.present.province'))
                                                ->pluck('city_municipality_description', 'city_municipality_code'))
                                            ->native(false)
                                            ->live()
                                            ->afterStateUpdated(function (Set $set) {
                                                $set('buyer.address.present.barangay', '');
                                            })
                                            ->columnSpan(3),
                                        Select::make('buyer.address.present.barangay')
                                            ->searchable()
                                            ->options(fn(Get $get): Collection => PhilippineBarangay::query()
                                                ->where('region_code', $get('buyer.address.present.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                                ->where('city_municipality_code', $get('buyer.address.present.city'))
                                                ->pluck('barangay_description', 'barangay_code')
                                            )
                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') == 'PH')
                                            ->hidden(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH'&&$get('buyer.address.present.country')!=null)
                                            ->native(false)
                                            ->live()
                                            ->columnSpan(3),
                                        TextInput::make('buyer.address.present.address')
                                            ->label(fn(Get $get)=>$get('buyer.address.present.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
//                                        ->hint('Unit Number, House/Building/Street No, Street Name')
                                            ->placeholder(fn(Get $get)=>$get('buyer.address.present.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                            ->required(fn(Get $get):bool => $get('buyer.address.present.country') != 'PH')
                                            ->autocapitalize('words')
                                            ->maxLength(255)
                                            ->live()
                                            ->columnSpan(12),
                                        Placeholder::make('buyer.address.present.full_address')
                                            ->label('Full Address')
                                            ->live()
                                            ->content(function (Get $get): string {
                                                $region = PhilippineRegion::where('region_code', $get('buyer.address.present.region'))->first();
                                                $province = PhilippineProvince::where('province_code', $get('buyer.address.present.province'))->first();
                                                $city = PhilippineCity::where('city_municipality_code', $get('buyer.address.present.city'))->first();
                                                $barangay = PhilippineBarangay::where('barangay_code', $get('buyer.address.present.barangay'))->first();
                                                $address = $get('buyer.address.present.address');
                                                $addressParts = array_filter([
                                                    $address,
                                                    $barangay != null ? $barangay->barangay_description : '',
                                                    $city != null ? $city->city_municipality_description : '',
                                                    $province != null ? $province->province_description : '',
                                                    $region != null ? $region->region_description : '',
                                                ]);
                                                return implode(', ', $addressParts);
                                            })->columnSpanFull()


                                    ])->columns(12)->columnSpanFull(),
                                    Group::make()->schema(
                                        fn(Get $get) => $get('buyer.address.present.same_as_permanent') == null ? [
                                            Forms\Components\Fieldset::make('Permanent')->schema([
                                                Group::make()->schema([
                                                    Select::make('buyer.address.permanent.ownership')
                                                        ->options(HomeOwnership::all()->pluck('description','code'))
                                                        ->native(false)
                                                        ->required()
                                                        ->columnSpan(3),
                                                    Select::make('buyer.address.permanent.country')
                                                        ->searchable()
                                                        ->options(Country::all()->pluck('description','code'))
                                                        ->native(false)
                                                        ->live()
                                                        ->required()
                                                        ->columnSpan(3),
                                                    TextInput::make('buyer.address.permanent.postal_code')
                                                        ->minLength(4)
                                                        ->maxLength(4)
                                                        ->required()
                                                        ->columnSpan(3),
                                                ])
                                                    ->columns(12)->columnSpanFull(),


                                                Select::make('buyer.address.permanent.region')
                                                    ->searchable()
                                                    ->options(PhilippineRegion::all()->pluck('region_description', 'region_code'))
                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
                                                    ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
                                                    ->native(false)
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set) {
                                                        $set('buyer.address.permanent.province', '');
                                                        $set('buyer.address.permanent.city', '');
                                                        $set('buyer.address.permanent.barangay', '');
                                                    })
                                                    ->columnSpan(3),
                                                Select::make('buyer.address.permanent.province')
                                                    ->searchable()
                                                    ->options(fn(Get $get): Collection => PhilippineProvince::query()
                                                        ->where('region_code', $get('buyer.address.permanent.region'))
                                                        ->pluck('province_description', 'province_code'))
                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
                                                    ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
                                                    ->native(false)
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set) {
                                                        $set('buyer.address.permanent.city', '');
                                                        $set('buyer.address.permanent.barangay', '');
                                                    })
                                                    ->columnSpan(3),
                                                Select::make('buyer.address.permanent.city')
                                                    ->searchable()
                                                    ->options(fn(Get $get): Collection => PhilippineCity::query()
                                                        ->where('province_code', $get('buyer.address.permanent.province'))
                                                        ->pluck('city_municipality_description', 'city_municipality_code'))
                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
                                                    ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
                                                    ->native(false)
                                                    ->live()
                                                    ->afterStateUpdated(function (Set $set) {
                                                        $set('buyer.address.permanent.barangay', '');
                                                    })
                                                    ->columnSpan(3),
                                                Select::make('buyer.address.permanent.barangay')
                                                    ->searchable()
                                                    ->options(fn(Get $get): Collection => PhilippineBarangay::query()
                                                        ->where('region_code', $get('buyer.address.permanent.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                                        ->where('city_municipality_code', $get('buyer.address.permanent.city'))
                                                        ->pluck('barangay_description', 'barangay_code')
                                                    )
                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') == 'PH')
                                                    ->hidden(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH'&&$get('buyer.address.permanent.country')!=null)
                                                    ->native(false)
                                                    ->live()
                                                    ->columnSpan(3),
                                                TextInput::make('buyer.address.permanent.address')
                                                    ->label(fn(Get $get)=>$get('buyer.address.permanent.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                    ->placeholder(fn(Get $get)=>$get('buyer.address.permanent.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                    ->required(fn(Get $get):bool => $get('buyer.address.permanent.country') != 'PH')
                                                    ->autocapitalize('words')
                                                    ->maxLength(255)
                                                    ->live()
                                                    ->columnSpan(12),
                                                Placeholder::make('buyer.address.permanent.full_address')
                                                    ->label('Full Address')
                                                    ->live()
                                                    ->content(function (Get $get): string {
                                                        $region = PhilippineRegion::where('region_code', $get('buyer.address.permanent.region'))->first();
                                                        $province = PhilippineProvince::where('province_code', $get('buyer.address.permanent.province'))->first();
                                                        $city = PhilippineCity::where('city_municipality_code', $get('buyer.address.permanent.city'))->first();
                                                        $barangay = PhilippineBarangay::where('barangay_code', $get('buyer.address.permanent.barangay'))->first();
                                                        $address = $get('buyer.address.permanent.address');
                                                        $addressParts = array_filter([
                                                            $address,
                                                            $barangay != null ? $barangay->barangay_description : '',
                                                            $city != null ? $city->city_municipality_description : '',
                                                            $province != null ? $province->province_description : '',
                                                            $region != null ? $region->region_description : '',
                                                        ]);
                                                        return implode(', ', $addressParts);
                                                    })->columnSpan(12),


                                            ])->columns(12)->columnSpanFull(),
                                        ] : []
                                    )->columns(12)->columnSpanFull(),
                                ])->columns(12)->columnSpanFull(),
                            //Employment
                            \Filament\Forms\Components\Fieldset::make('Employment')->schema([
                                Select::make('buyer_employment.type')
                                    ->label('Employment Type')
                                    ->live()
                                    ->required()
                                    ->native(false)
                                    ->options(EmploymentType::all()->pluck('description','code'))
                                    ->columnSpan(3),
                                Select::make('buyer_employment.status')
                                    ->label('Employment Status')
                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->native(false)
                                    ->options(EmploymentStatus::all()->pluck('description','code'))
                                    ->columnSpan(3),
                                Select::make('buyer_employment.tenure')
                                    ->label('Tenure')
                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->native(false)
                                    ->options(Tenure::all()->pluck('description','code'))
                                    ->columnSpan(3),
                                Select::make('buyer_employment.position')
                                    ->label('Current Position')
                                    ->native(false)
                                    ->options(CurrentPosition::all()->pluck('description','code'))
                                    ->searchable()
                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->columnSpan(3),
                                TextInput::make('buyer_employment.rank')
                                    ->label('Rank')
                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                Select::make('buyer_employment.work_industry')
                                    ->label('Work Industry')
                                    ->required()
                                    ->native(false)
                                    ->options(WorkIndustry::all()->pluck('description','code'))
                                    ->searchable()
                                    ->columnSpan(3),
                                TextInput::make('buyer_employment.gross_monthly_income')
                                    ->label('Gross Monthly Income')
                                    ->numeric()
                                    ->prefix('PHP')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                Group::make()->schema([
                                    TextInput::make('buyer_employment.id.tin')
                                        ->label('Tax Identification Number')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(3),
                                    TextInput::make('buyer_employment.id.pag_ibig')
                                        ->label('PAG-IBIG Number')
                                        ->required(fn (Get $get): bool=>$get('has_pagibig_number')==true||$get('has_pagibig_number')==null)
                                        ->maxLength(255)
                                        ->columnSpan(3),
                                    TextInput::make('buyer_employment.id.sss_gsis')
                                        ->label('SSS/GSIS Number')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(3),
                                    TextInput::make('employee_id_number')
                                        ->label('Employee ID Number')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(3),
                                ])->columnSpanFull()->columns(12),


                            ])->columns(12)->columnSpanFull(),
                            //Employer
//                            Forms\Components\Fieldset::make('Employer/Business')->schema([
//                                TextInput::make('buyer_employment.employer.employer_business_name')
//                                    ->label('Employer / Business Name')
//                                    ->required()
//                                    ->maxLength(255)
//                                    ->columnSpan(3),
//                                TextInput::make('buyer_employment.employer.contact_person')
//                                    ->label('Contact Person')
//                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
//                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
//                                    ->maxLength(255)
//                                    ->columnSpan(3),
//                                TextInput::make('buyer_employment.employer.employer_email')
//                                    ->label('Email')
//                                    ->email()
//                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
//                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
//                                    ->maxLength(255)
//                                    ->columnSpan(3),
//                                TextInput::make('buyer_employment.employer.mobile')
//                                    ->label('Contact Number')
//                                    ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
//                                    ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
//                                    ->prefix('+63')
//                                    ->regex("/^[0-9]+$/")
//                                    ->minLength(10)
//                                    ->maxLength(10)
//                                    ->live()
////                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
//////                                            $livewire->validateOnly($component->getStatePath());
////                                        })
//                                    ->columnSpan(3),
//                                TextInput::make('buyer_employment.employer.year_established')
//                                    ->label('Year Established')
//                                    ->required()
//                                    ->numeric()
//                                    ->columnSpan(3),
////                                        Select::make('employment.employer.years_of_operation')
////                                            ->label('Years of Operation')
////                                            ->required()
////                                            ->native(false)
////                                            ->options(YearsOfOperation::all()->pluck('description','code'))
////                                            ->columnSpan(3),
//                                Forms\Components\Fieldset::make('Address')->schema([
//                                    Group::make()
//                                        ->schema([
//                                            Select::make('buyer_employment.employer.address.country')
//                                                ->searchable()
//                                                ->options(Country::all()->pluck('description','code'))
//                                                ->native(false)
//                                                ->live()
//                                                ->required()
//                                                ->columnSpan(3),
//                                        ])
//                                        ->columns(12)
//                                        ->columnSpanFull(),
//                                    Select::make('buyer_employment.employer.address.region')
//                                        ->searchable()
//                                        ->options(PhilippineRegion::all()->pluck('region_description','region_code'))
//                                        ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
//                                        ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
//                                        ->native(false)
//                                        ->live()
//                                        ->afterStateUpdated(function(Set $set, $state){
//                                            $set('buyer_employment.employer.address.province','');
//                                            $set('buyer_employment.employer.address.city','');
//                                            $set('buyer_employment.employer.address.barangay','');
//                                        })
//                                        ->columnSpan(3),
//                                    Select::make('buyer_employment.employer.address.province')
//                                        ->searchable()
//                                        ->options(fn (Get $get): Collection => PhilippineProvince::query()
//                                            ->where('region_code', $get('buyer_employment.employer.address.region'))
//                                            ->pluck('province_description', 'province_code'))
//                                        ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
//                                        ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
//                                        ->native(false)
//                                        ->live()
//                                        ->afterStateUpdated(function(Set $set, $state){
//                                            $set('buyer_employment.employer.address.city','');
//                                            $set('buyer_employment.employer.address.barangay','');
//                                        })
//                                        ->columnSpan(3),
//                                    Select::make('buyer_employment.employer.address.city')
//                                        ->searchable()
//                                        ->options(fn (Get $get): Collection => PhilippineCity::query()
//                                            ->where('province_code', $get('buyer_employment.employer.address.province'))
//                                            ->pluck('city_municipality_description', 'city_municipality_code'))
//                                        ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
//                                        ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
//                                        ->native(false)
//                                        ->live()
//                                        ->afterStateUpdated(function(Set $set, $state){
//                                            $set('buyer_employment.employer.address.barangay','');
//                                        })
//                                        ->columnSpan(3),
//                                    Select::make('buyer_employment.employer.address.barangay')
//                                        ->searchable()
//                                        ->options(fn (Get $get): Collection =>PhilippineBarangay::query()
//                                            ->where('region_code', $get('buyer_employment.employer.address.region'))
////                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
//                                            ->where('city_municipality_code', $get('buyer_employment.employer.address.city'))
//                                            ->pluck('barangay_description', 'barangay_code')
//                                        )
//                                        ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
//                                        ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
//                                        ->native(false)
//                                        ->live()
//                                        ->columnSpan(3),
//                                    TextInput::make('buyer_employment.employer.address.address')
//                                        ->label(fn(Get $get)=>$get('buyer_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
//                                        ->placeholder(fn(Get $get)=>$get('buyer_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
//                                        ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH')
//                                        ->autocapitalize('words')
//                                        ->maxLength(255)
//                                        ->live()
//                                        ->columnSpan(12),
//
//
//                                ])->columns(12)->columnSpanFull(),
//                            ])->columns(12)->columnSpanFull(),
//                            Forms\Components\Fieldset::make('Documents')
//                                ->schema([
//                                    FileUpload::make('valid_id')
//                                        ->label('Valid ID')
//                                        ->required(),
//                                    FileUpload::make('payslip')
//                                        ->label('Latest 1 month payslip')
//                                        ->required(),
//                                ])->columns(1),
//                            Forms\Components\Fieldset::make('Project')
//                                ->schema([
//                                    Select::make('preferred_project')
//                                        ->live()
//                                        ->label('Preferred Project')
//                                        ->required()
//                                        ->native(false)
//                                        ->options(Project::all()->pluck('description','code'))
//                                        ->columnSpan(3),
//                                ])->columnSpanFull()->columns(12),
                        ]),
                        //Spouse
                        Forms\Components\Tabs\Tab::make('Spouse')->schema([
                            //Personal Information
                            Forms\Components\Fieldset::make('Personal')->schema([
                                TextInput::make('spouse.last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                TextInput::make('spouse.first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(3),

                                TextInput::make('spouse.middle_name')
                                    ->label('Middle Name')
                                    ->maxLength(255)
                                    ->required(fn (Get $get): bool => ! $get('spouse.no_middle_name'))
                                    ->readOnly(fn (Get $get): bool =>  $get('spouse.no_middle_name'))
//                                            ->hidden(fn (Get $get): bool =>  $get('no_middle_name'))
                                    ->columnSpan(3),
                                Select::make('spouse.name_suffix')
                                    ->label('Suffix')
                                    ->required()
                                    ->native(false)
                                    ->searchable()
                                    ->options(NameSuffix::all()->pluck('description','code'))
                                    ->columnSpan(2),

                                Forms\Components\Checkbox::make('spouse.no_middle_name')
                                    ->live()
                                    ->inline(false)
                                    ->afterStateUpdated(function(Get $get,Set $set){
                                        $set('spouse.middle_name',null);
//                                                if ($get('no_middle_name')) {
//                                                }
                                    })
                                    ->columnSpan(1),
                                Select::make('spouse.civil_status')
                                    ->label('Civil Status')
                                    ->required()
                                    ->native(false)
                                    ->options(CivilStatus::all()->pluck('description','code'))
                                    ->columnSpan(3),
                                Select::make('spouse.gender')
                                    ->label('Gender')
                                    ->required()
                                    ->native(false)
                                    ->options([
                                        'Male'=>'Male',
                                        'Female'=>'Female'
                                    ])
                                    ->columnSpan(3),
                                DatePicker::make('spouse.date_of_birth')
                                    ->label('Date of Birth')
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(3),
                                Select::make('spouse.nationality')
                                    ->label('Nationality')
                                    ->required()
                                    ->native(false)
                                    ->options(Nationality::all()->pluck('description','code'))
                                    ->columnSpan(3),
                            ])->columns(12)->columnSpanFull(),
                            \Filament\Forms\Components\Fieldset::make('Contact Information')
                                ->schema([
                                    Forms\Components\TextInput::make('spouse.email')
                                        ->label('Email')
                                        ->email()
                                        ->required()
                                        ->maxLength(255)
                                        ->live()
                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                            $livewire->validateOnly($component->getStatePath());
                                        })
                                        ->columnSpan(3),

                                    Forms\Components\TextInput::make('spouse.mobile')
                                        ->label('Mobile')
                                        ->required()
                                        ->prefix('+63')
                                        ->regex("/^[0-9]+$/")
                                        ->minLength(10)
                                        ->maxLength(10)
                                        ->live()
                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                            $livewire->validateOnly($component->getStatePath());
                                        })
                                        ->columnSpan(3),

                                    Forms\Components\TextInput::make('spouse.other_mobile')
                                        ->label('Other Mobile')
                                        ->prefix('+63')
                                        ->regex("/^[0-9]+$/")
                                        ->minLength(10)
                                        ->maxLength(10)
                                        ->live()
                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                            $livewire->validateOnly($component->getStatePath());
                                        })
                                        ->columnSpan(3),

                                    Forms\Components\TextInput::make('spouse.landline')
                                        ->label('Landline')
                                        ->columnSpan(3),
                                ])->columns(12)->columnSpanFull(),
                        ])->hidden(fn (Get $get): bool => $get('buyer.civil_status')!=CivilStatus::where('description','Married')->first()->code &&  $get('buyer.civil_status')!=null),
                    ])->visible(false)
                    ->visible(fn():bool => $this->screenSize === 'desktop' || $this->screenSize === 'md'),
                //desktop end
                //mobile
                Group::make()
                    ->schema([
                        Forms\Components\Fieldset::make('Personal')->schema([
                            TextInput::make('last_name')
                                ->label('Last Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('first_name')
                                ->label('First Name')
                                ->required()
                                ->hint('with ex. Sr. Jr.')
                                ->maxLength(255),

                            TextInput::make('middle_name')
                                ->label('Middle Name')
                                ->maxLength(255),
                            Select::make('civil_status_code')
                                ->live()
                                ->label('Civil Status')
                                ->required()
                                ->native(false)
                                ->options(CivilStatus::all()->pluck('description','code')),
                            Select::make('gender_code')
                                ->label('Gender')
                                ->required()
                                ->native(false)
                                ->options([
                                    'Male'=>'Male',
                                    'Female'=>'Female'
                                ]),
                            DatePicker::make('date_of_birth')
                                ->label('Date of Birth')
                                ->hint(function ($state): string {
                                    if ($state) {
                                        $dateOfBirth = \Carbon\Carbon::parse($state);
                                        $age = $dateOfBirth->age;
                                        return "Age: $age years";
                                    }
                                    return 'Age: 0 year';
                                })
                                ->hintColor('primary')
                                ->required()
                                ->native(false),
                            Select::make('ownership_code')
                                ->label('Home Ownership')
                                ->options(HomeOwnership::all()->pluck('description','code'))
                                ->native(false)
                                ->required(),
                            TextInput::make('rent_amount')
                                ->label('Rent Amount')
                                ->numeric()
                                ->prefix('₱')
                                ->required(fn(Get $get):bool=>$get('ownership_code')==HomeOwnership::where('description','Rented')->first()->code)
                                ->hidden(fn(Get $get):bool=>$get('ownership_code')!=HomeOwnership::where('description','Rented')->first()->code || $get('ownership_code')==null)
                            ,

                            Forms\Components\ToggleButtons::make('has_pagibig_number')
                                ->label('Are you a Pag-Ibig Member?')
                                ->inline(true)
                                ->required()
                                ->boolean()
                                ->live(),
                            TextInput::make('mid')
                                ->label('What is your Pag-Ibig MID #?')
                                ->required(fn (Get $get): bool=>$get('has_pagibig_number')==true||$get('has_pagibig_number')==null)
                                ->hidden(fn (Get $get): bool=>$get('has_pagibig_number')!=true||$get('has_pagibig_number')==null)
                                ->maxLength(255),
                            Forms\Components\ToggleButtons::make('hloan')
                                ->label('Do you have an existing housing loan with Pag-Ibig?')
                                ->inline(true)
                                ->required()
                                ->required(fn (Get $get): bool=>$get('has_pagibig_number')==true||$get('has_pagibig_number')==null)
                                ->hidden(fn (Get $get): bool=>$get('has_pagibig_number')!=true||$get('has_pagibig_number')==null)
                                ->boolean(),

                        ]),
                        \Filament\Forms\Components\Fieldset::make('Contact Information')
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                        $livewire->validateOnly($component->getStatePath());
                                    }),

                                Forms\Components\TextInput::make('mobile')
                                    ->label('Mobile')
                                    ->required()
                                    ->prefix('+63')
                                    ->regex("/^[0-9]+$/")
                                    ->minLength(10)
                                    ->maxLength(10)
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                        $livewire->validateOnly($component->getStatePath());
                                    }),

//                                    Forms\Components\TextInput::make('buyer.other_mobile')
//                                        ->label('Other Mobile')
//                                        ->prefix('+63')
//                                        ->regex("/^[0-9]+$/")
//                                        ->minLength(10)
//                                        ->maxLength(10)
//                                        ->live()
//                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
//                                            $livewire->validateOnly($component->getStatePath());
//                                        })
//                                        ->columnSpan(3),
//
//                                    Forms\Components\TextInput::make('buyer.landline')
//                                        ->label('Landline')
//                                        ->columnSpan(3),
                            ]),
                        Forms\Components\Fieldset::make('Employment')
                            ->schema([
                                Select::make('employment_status')
                                    ->label('Employment Status')
                                    ->required()
                                    ->native(false)
                                    ->options(EmploymentStatus::all()->pluck('description','code')),
                                Select::make('employment_tenure')
                                    ->label('Tenure')
                                    ->required()
                                    ->native(false)
                                    ->options(Tenure::all()->pluck('description','code')),
                                TextInput::make('salary')
                                    ->label('Gross Monthly Income')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->required(),
                                TextInput::make('employee_id_number')
                                    ->label('Employee ID Number')
                                    ->required(),
                            ]),
                    ])->columnSpanFull()->columns(1)
                    ->visible(fn():bool => $this->screenSize === 'mobile' ),
                //mobile end
            ])
            ->statePath('data')
            ->model(Contact::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        dd($data);
        $record = Contact::create($data);

        $this->form->model($record)->saveRelationships();
    }

    public function render(): View
    {
        return view('livewire.client-information-form');
    }
}
