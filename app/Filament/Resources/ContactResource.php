<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Settings;
use App\Filament\Imports\ContactImporter;
use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Filament\Resources\Maintenance\CompaniesResource;
use App\Models\CivilStatus;
use App\Models\ClientInformations;
use App\Models\Companies;
use App\Models\Country;
use App\Models\CurrentPosition;
use App\Models\Documents;
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
use App\Models\YearsOfOperation;
use Faker\Core\Number;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Homeful\Common\Classes\Input;
use Homeful\Contacts\Actions\PersistContactAction;
use Homeful\Contacts\Data\ContactData;
use Homeful\Contacts\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
//use RLI\Booking\Imports\Cornerstone\OSReportsImport;
use App\Imports\OSImport;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Ramsey\Uuid\Uuid;
use function PHPUnit\Framework\throwException;
class ContactResource extends Resource
{
    protected static ?string $label ='Contacts Information';
    protected static ?string $model = Contact::class;
    protected static ?string $recordTitleAttribute ='last_name';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function  infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Personal')
                            ->schema([
                                Fieldset::make('Personal Info')->schema([
                                    TextEntry::make('first_name')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('middle_name')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('last_name')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('sex')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('nationality')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('date_of_birth')
                                        ->weight(FontWeight::Bold)
                                        ->date(),
                                    TextEntry::make('email')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('mobile')
                                        ->label('Mobile Number')
                                        ->weight(FontWeight::Bold),
                                ]),
                                Fieldset::make('Spouse Info')->schema([
                                    TextEntry::make('spouse.first_name')
                                        ->label('First Name'),
                                    TextEntry::make('spouse.middle_name')
                                        ->label('Middle Name'),
                                    TextEntry::make('spouse.last_name')
                                        ->label('Last Name'),
                                ]),
                            ]),
                        Tabs\Tab::make('Employment')
                            ->schema([
                                // ...
                            ]),
                        Tabs\Tab::make('Co-Borrowers')
                            ->schema([
                                // ...
                            ]),
                        Tabs\Tab::make('Order')
                            ->schema([
                                // ...
                            ]),
                    ])
                    ->activeTab(1)->columnSpanFull(),

                ])->inlineLabel(); // TODO: Change the autogenerated stub
    }

    public static function form(Form $form): Form
    {
        return $form
//            ->inlineLabel()
            ->schema([
                Section::make('Client Information')
                    ->compact()
                    ->key(\Ramsey\Uuid\Uuid::uuid4()->toString())
                    ->headerActions([
                    ])
                    ->schema([
                        Forms\Components\Tabs::make()
                            ->persistTabInQueryString()
                            ->contained(false)
                            ->tabs([
                                //Buyer Information
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
                                            ->maxLength(255)
                                            ->columnSpan(3),

                                        TextInput::make('buyer.middle_name')
                                            ->label('Middle Name')
                                            ->maxLength(255)
                                            ->required(fn (Get $get): bool => ! $get('no_middle_name'))
                                            ->readOnly(fn (Get $get): bool => $get('no_middle_name'))
//                                            ->hidden(fn (Get $get): bool =>  $get('no_middle_name'))
                                            ->columnSpan(3),
                                        Select::make('buyer.name_suffix')
                                            ->label('Suffix')
                                            ->required()
                                            ->native(false)
                                            ->options(NameSuffix::all()->pluck('description','code'))
                                            ->columnSpan(2),
                                        Forms\Components\Checkbox::make('no_middle_name')
                                            ->live()
                                            ->inline(false)
                                            ->afterStateUpdated(function(Get $get,Set $set){
                                                    $set('buyer.middle_name',null);
//                                                if ($get('no_middle_name')) {
//                                                }
                                            })
                                            ->columnSpan(1),
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
                                            ->required()
                                            ->native(false)
                                            ->columnSpan(3),
                                        Select::make('buyer.nationality')
                                            ->label('Nationality')
                                            ->required()
                                            ->native(false)
                                            ->options(Nationality::all()->pluck('description','code'))
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

                                            Forms\Components\TextInput::make('buyer.other_mobile')
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

                                            Forms\Components\TextInput::make('buyer.landline')
                                                ->label('Landline')
                                                ->columnSpan(3),
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
                                                    ->afterStateUpdated(function (Set $set, $state) {
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
                                                    ->afterStateUpdated(function (Set $set, $state) {
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
                                                    ->afterStateUpdated(function (Set $set, $state) {
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
                                                            ->afterStateUpdated(function (Set $set, $state) {
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
                                                            ->afterStateUpdated(function (Set $set, $state) {
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
                                                            ->afterStateUpdated(function (Set $set, $state) {
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
                                            TextInput::make('buyer_employment.tin')
                                                ->label('Tax Identification Number')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            TextInput::make('buyer_employment.pag_ibig')
                                                ->label('PAG-IBIG Number')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                            TextInput::make('buyer_employment.sss_gsis')
                                                ->label('SSS/GSIS Number')
                                                ->required()
                                                ->maxLength(255)
                                                ->columnSpan(3),
                                        ])->columnSpanFull()->columns(12),


                                    ])->columns(12)->columnSpanFull(),
                                    //Employer
                                    Forms\Components\Fieldset::make('Employer/Business')->schema([
                                        TextInput::make('buyer_employment.employer.employer_business_name')
                                            ->label('Employer / Business Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(3),
                                        TextInput::make('buyer_employment.employer.contact_person')
                                            ->label('Contact Person')
                                            ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                            ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                            ->maxLength(255)
                                            ->columnSpan(3),
                                        TextInput::make('buyer_employment.employer.employer_email')
                                            ->label('Email')
                                            ->email()
                                            ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                            ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                            ->maxLength(255)
                                            ->columnSpan(3),
                                        TextInput::make('buyer_employment.employer.mobile')
                                            ->label('Contact Number')
                                            ->required(fn (Get $get): bool =>   $get('buyer_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                            ->hidden(fn (Get $get): bool =>   $get('buyer_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                            ->prefix('+63')
                                            ->regex("/^[0-9]+$/")
                                            ->minLength(10)
                                            ->maxLength(10)
                                            ->live()
//                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
////                                            $livewire->validateOnly($component->getStatePath());
//                                        })
                                            ->columnSpan(3),
                                        TextInput::make('buyer_employment.employer.year_established')
                                            ->label('Year Established')
                                            ->required()
                                            ->numeric()
                                            ->columnSpan(3),
//                                        Select::make('employment.employer.years_of_operation')
//                                            ->label('Years of Operation')
//                                            ->required()
//                                            ->native(false)
//                                            ->options(YearsOfOperation::all()->pluck('description','code'))
//                                            ->columnSpan(3),
                                        Forms\Components\Fieldset::make('Address')->schema([
                                            Group::make()
                                                ->schema([
                                                    Select::make('buyer_employment.employer.address.country')
                                                        ->searchable()
                                                        ->options(Country::all()->pluck('description','code'))
                                                        ->native(false)
                                                        ->live()
                                                        ->required()
                                                        ->columnSpan(3),
                                                ])
                                                ->columns(12)
                                                ->columnSpanFull(),
                                            Select::make('buyer_employment.employer.address.region')
                                                ->searchable()
                                                ->options(PhilippineRegion::all()->pluck('region_description','region_code'))
                                                ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                                ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                                ->native(false)
                                                ->live()
                                                ->afterStateUpdated(function(Set $set, $state){
                                                    $set('buyer_employment.employer.address.province','');
                                                    $set('buyer_employment.employer.address.city','');
                                                    $set('buyer_employment.employer.address.barangay','');
                                                })
                                                ->columnSpan(3),
                                            Select::make('buyer_employment.employer.address.province')
                                                ->searchable()
                                                ->options(fn (Get $get): Collection => PhilippineProvince::query()
                                                    ->where('region_code', $get('buyer_employment.employer.address.region'))
                                                    ->pluck('province_description', 'province_code'))
                                                ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                                ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                                ->native(false)
                                                ->live()
                                                ->afterStateUpdated(function(Set $set, $state){
                                                    $set('buyer_employment.employer.address.city','');
                                                    $set('buyer_employment.employer.address.barangay','');
                                                })
                                                ->columnSpan(3),
                                            Select::make('buyer_employment.employer.address.city')
                                                ->searchable()
                                                ->options(fn (Get $get): Collection => PhilippineCity::query()
                                                    ->where('province_code', $get('buyer_employment.employer.address.province'))
                                                    ->pluck('city_municipality_description', 'city_municipality_code'))
                                                ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                                ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                                ->native(false)
                                                ->live()
                                                ->afterStateUpdated(function(Set $set, $state){
                                                    $set('buyer_employment.employer.address.barangay','');
                                                })
                                                ->columnSpan(3),
                                            Select::make('buyer_employment.employer.address.barangay')
                                                ->searchable()
                                                ->options(fn (Get $get): Collection =>PhilippineBarangay::query()
                                                    ->where('region_code', $get('buyer_employment.employer.address.present.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                                    ->where('city_municipality_code', $get('buyer_employment.employer.address.present.city'))
                                                    ->pluck('barangay_description', 'barangay_code')
                                                )
                                                ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') == 'PH')
                                                ->hidden(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH'&&$get('buyer_employment.employer.address.country')!=null)
                                                ->native(false)
                                                ->live()
                                                ->columnSpan(3),
                                            TextInput::make('buyer_employment.employer.address.present.address')
                                                ->label(fn(Get $get)=>$get('buyer_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                ->placeholder(fn(Get $get)=>$get('buyer_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                ->required(fn(Get $get):bool => $get('buyer_employment.employer.address.country') != 'PH')
                                                ->autocapitalize('words')
                                                ->maxLength(255)
                                                ->live()
                                                ->columnSpan(12),


                                        ])->columns(12)->columnSpanFull(),
                                    ])->columns(12)->columnSpanFull(),
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
                                Forms\Components\Tabs\Tab::make('Co-Borrower')->schema([
                                    Section::make('Co-Borrowers Information')->schema([
                                        // Co-Borrower Fields
                                        Forms\Components\Repeater::make('co_borrowers')
                                            ->label('Co-Borrowers')
                                            ->schema([
                                                Forms\Components\TextInput::make('first_name')
                                                    ->label('First Name')
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('middle_name')
                                                    ->label('Middle Name')
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('last_name')
                                                    ->label('Last Name')
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('name_suffix')
                                                    ->label('Name Suffix')
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('civil_status')
                                                    ->label('Civil Status')
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('sex')
                                                    ->label('Sex')
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('nationality')
                                                    ->label('Nationality')
                                                    ->columnSpan(3),

                                                Forms\Components\DatePicker::make('date_of_birth')
                                                    ->label('Date of Birth')
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('email')
                                                    ->label('Email')
                                                    ->email()
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('mobile')
                                                    ->label('Mobile Number')
                                                    ->prefix('+63')
                                                    ->regex("/^[0-9]+$/")
                                                    ->minLength(10)
                                                    ->maxLength(10)
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                        $livewire->validateOnly($component->getStatePath());
                                                    })
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('other_mobile')
                                                    ->label('Other Mobile Number')
                                                    ->prefix('+63')
                                                    ->regex("/^[0-9]+$/")
                                                    ->minLength(10)
                                                    ->maxLength(10)
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                        $livewire->validateOnly($component->getStatePath());
                                                    })
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('help_number')
                                                    ->label('Help Number')
                                                    ->prefix('+63')
                                                    ->regex("/^[0-9]+$/")
                                                    ->minLength(10)
                                                    ->maxLength(10)
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                        $livewire->validateOnly($component->getStatePath());
                                                    })
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('landline')
                                                    ->label('Landline')
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('mothers_maiden_name')
                                                    ->label('Mother\'s Maiden Name')
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('age')
                                                    ->label('Age')
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('relationship_to_buyer')
                                                    ->label('Relationship to Buyer')
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('passport')
                                                    ->label('Passport Number')
                                                    ->columnSpan(3),

                                                Forms\Components\DatePicker::make('date_issued')
                                                    ->label('Date Issued')
                                                    ->columnSpan(3),

                                                Forms\Components\TextInput::make('place_issued')
                                                    ->label('Place Issued')
                                                    ->columnSpan(3),

                                                //Cobo Address
                                                \Filament\Forms\Components\Fieldset::make('Address')
                                                    ->schema([
                                                        Forms\Components\Fieldset::make('Present')->schema([
                                                            Select::make('coborrower.address.present.ownership')
                                                                ->options(HomeOwnership::all()->pluck('description','code'))
                                                                ->native(false)
                                                                ->required()
                                                                ->columnSpan(3),
                                                            Select::make('coborrower.address.present.country')
                                                                ->searchable()
                                                                ->options(Country::all()->pluck('description','code'))
                                                                ->native(false)
                                                                ->live()
                                                                ->required()
                                                                ->columnSpan(3),
                                                            TextInput::make('coborrower.address.present.postal_code')
                                                                ->minLength(4)
                                                                ->maxLength(4)
                                                                ->required()
                                                                ->columnSpan(3),
                                                            Checkbox::make('coborrower.address.present.same_as_permanent')
                                                                ->label('Same as Permanent')
                                                                ->inline(false)
                                                                ->live()
                                                                ->columnSpan(3),
                                                            Select::make('coborrower.address.present.region')
                                                                ->searchable()
                                                                ->options(PhilippineRegion::all()->pluck('region_description', 'region_code'))
                                                                ->required(fn(Get $get):bool => $get('coborrower.address.present.country') == 'PH')
                                                                ->hidden(fn(Get $get):bool => $get('coborrower.address.present.country') != 'PH'&&$get('coborrower.address.present.country')!=null)
                                                                ->native(false)
                                                                ->live()
                                                                ->afterStateUpdated(function (Set $set, $state) {
                                                                    $set('coborrower.address.present.province', '');
                                                                    $set('coborrower.address.present.city', '');
                                                                    $set('coborrower.address.present.barangay', '');
                                                                })
                                                                ->columnSpan(3),
                                                            Select::make('coborrower.address.present.province')
                                                                ->searchable()
                                                                ->options(fn(Get $get): Collection => PhilippineProvince::query()
                                                                    ->where('region_code', $get('coborrower.address.present.region'))
                                                                    ->pluck('province_description', 'province_code'))
                                                                ->required(fn(Get $get):bool => $get('coborrower.address.present.country') == 'PH')
                                                                ->hidden(fn(Get $get):bool => $get('coborrower.address.present.country') != 'PH'&&$get('coborrower.address.present.country')!=null)
                                                                ->native(false)
                                                                ->live()
                                                                ->afterStateUpdated(function (Set $set, $state) {
                                                                    $set('coborrower.address.present.city', '');
                                                                    $set('coborrower.address.present.barangay', '');
                                                                })
                                                                ->columnSpan(3),
                                                            Select::make('coborrower.address.present.city')
                                                                ->searchable()
                                                                ->required(fn(Get $get):bool => $get('coborrower.address.present.country') == 'PH')
                                                                ->hidden(fn(Get $get):bool => $get('coborrower.address.present.country') != 'PH'&&$get('coborrower.address.present.country')!=null)
                                                                ->options(fn(Get $get): Collection => PhilippineCity::query()
                                                                    ->where('province_code', $get('coborrower.address.present.province'))
                                                                    ->pluck('city_municipality_description', 'city_municipality_code'))
                                                                ->native(false)
                                                                ->live()
                                                                ->afterStateUpdated(function (Set $set, $state) {
                                                                    $set('coborrower.address.present.barangay', '');
                                                                })
                                                                ->columnSpan(3),
                                                            Select::make('coborrower.address.present.barangay')
                                                                ->searchable()
                                                                ->options(fn(Get $get): Collection => PhilippineBarangay::query()
                                                                    ->where('region_code', $get('coborrower.address.present.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                                                    ->where('city_municipality_code', $get('coborrower.address.present.city'))
                                                                    ->pluck('barangay_description', 'barangay_code')
                                                                )
                                                                ->required(fn(Get $get):bool => $get('coborrower.address.present.country') == 'PH')
                                                                ->hidden(fn(Get $get):bool => $get('coborrower.address.present.country') != 'PH'&&$get('coborrower.address.present.country')!=null)
                                                                ->native(false)
                                                                ->live()
                                                                ->columnSpan(3),
                                                            TextInput::make('coborrower.address.present.address')
                                                                ->label(fn(Get $get)=>$get('coborrower.address.present.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
//                                        ->hint('Unit Number, House/Building/Street No, Street Name')
                                                                ->placeholder(fn(Get $get)=>$get('coborrower.address.present.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                                ->required(fn(Get $get):bool => $get('coborrower.address.present.country') != 'PH')
                                                                ->autocapitalize('words')
                                                                ->maxLength(255)
                                                                ->live()
                                                                ->columnSpan(12),
                                                            Placeholder::make('coborrower.address.present.full_address')
                                                                ->label('Full Address')
                                                                ->live()
                                                                ->content(function (Get $get): string {
                                                                    $region = PhilippineRegion::where('region_code', $get('coborrower.address.present.region'))->first();
                                                                    $province = PhilippineProvince::where('province_code', $get('coborrower.address.present.province'))->first();
                                                                    $city = PhilippineCity::where('city_municipality_code', $get('coborrower.address.present.city'))->first();
                                                                    $barangay = PhilippineBarangay::where('barangay_code', $get('coborrower.address.present.barangay'))->first();
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
                                                            fn(Get $get) => $get('coborrower.address.present.same_as_permanent') == null ? [
                                                                Forms\Components\Fieldset::make('Permanent')->schema([
                                                                    Group::make()->schema([
                                                                        Select::make('coborrower.address.permanent.ownership')
                                                                            ->options(HomeOwnership::all()->pluck('description','code'))
                                                                            ->native(false)
                                                                            ->required()
                                                                            ->columnSpan(3),
                                                                        Select::make('coborrower.address.permanent.country')
                                                                            ->searchable()
                                                                            ->options(Country::all()->pluck('description','code'))
                                                                            ->native(false)
                                                                            ->live()
                                                                            ->required()
                                                                            ->columnSpan(3),
                                                                        TextInput::make('coborrower.address.permanent.postal_code')
                                                                            ->minLength(4)
                                                                            ->maxLength(4)
                                                                            ->required()
                                                                            ->columnSpan(3),
                                                                    ])
                                                                        ->columns(12)->columnSpanFull(),


                                                                    Select::make('coborrower.address.permanent.region')
                                                                        ->searchable()
                                                                        ->options(PhilippineRegion::all()->pluck('region_description', 'region_code'))
                                                                        ->required(fn(Get $get):bool => $get('coborrower.address.permanent.country') == 'PH')
                                                                        ->hidden(fn(Get $get):bool => $get('coborrower.address.permanent.country') != 'PH'&&$get('coborrower.address.permanent.country')!=null)
                                                                        ->native(false)
                                                                        ->live()
                                                                        ->afterStateUpdated(function (Set $set, $state) {
                                                                            $set('coborrower.address.permanent.province', '');
                                                                            $set('coborrower.address.permanent.city', '');
                                                                            $set('coborrower.address.permanent.barangay', '');
                                                                        })
                                                                        ->columnSpan(3),
                                                                    Select::make('coborrower.address.permanent.province')
                                                                        ->searchable()
                                                                        ->options(fn(Get $get): Collection => PhilippineProvince::query()
                                                                            ->where('region_code', $get('coborrower.address.permanent.region'))
                                                                            ->pluck('province_description', 'province_code'))
                                                                        ->required(fn(Get $get):bool => $get('coborrower.address.permanent.country') == 'PH')
                                                                        ->hidden(fn(Get $get):bool => $get('coborrower.address.permanent.country') != 'PH'&&$get('coborrower.address.permanent.country')!=null)
                                                                        ->native(false)
                                                                        ->live()
                                                                        ->afterStateUpdated(function (Set $set, $state) {
                                                                            $set('coborrower.address.permanent.city', '');
                                                                            $set('coborrower.address.permanent.barangay', '');
                                                                        })
                                                                        ->columnSpan(3),
                                                                    Select::make('coborrower.address.permanent.city')
                                                                        ->searchable()
                                                                        ->options(fn(Get $get): Collection => PhilippineCity::query()
                                                                            ->where('province_code', $get('coborrower.address.permanent.province'))
                                                                            ->pluck('city_municipality_description', 'city_municipality_code'))
                                                                        ->required(fn(Get $get):bool => $get('coborrower.address.permanent.country') == 'PH')
                                                                        ->hidden(fn(Get $get):bool => $get('coborrower.address.permanent.country') != 'PH'&&$get('coborrower.address.permanent.country')!=null)
                                                                        ->native(false)
                                                                        ->live()
                                                                        ->afterStateUpdated(function (Set $set, $state) {
                                                                            $set('coborrower.address.permanent.barangay', '');
                                                                        })
                                                                        ->columnSpan(3),
                                                                    Select::make('coborrower.address.permanent.barangay')
                                                                        ->searchable()
                                                                        ->options(fn(Get $get): Collection => PhilippineBarangay::query()
                                                                            ->where('region_code', $get('coborrower.address.permanent.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                                                            ->where('city_municipality_code', $get('coborrower.address.permanent.city'))
                                                                            ->pluck('barangay_description', 'barangay_code')
                                                                        )
                                                                        ->required(fn(Get $get):bool => $get('coborrower.address.permanent.country') == 'PH')
                                                                        ->hidden(fn(Get $get):bool => $get('coborrower.address.permanent.country') != 'PH'&&$get('coborrower.address.permanent.country')!=null)
                                                                        ->native(false)
                                                                        ->live()
                                                                        ->columnSpan(3),
                                                                    TextInput::make('coborrower.address.permanent.address')
                                                                        ->label(fn(Get $get)=>$get('coborrower.address.permanent.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                                        ->placeholder(fn(Get $get)=>$get('coborrower.address.permanent.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                                        ->required(fn(Get $get):bool => $get('coborrower.address.permanent.country') != 'PH')
                                                                        ->autocapitalize('words')
                                                                        ->maxLength(255)
                                                                        ->live()
                                                                        ->columnSpan(12),
                                                                    Placeholder::make('coborrower.address.permanent.full_address')
                                                                        ->label('Full Address')
                                                                        ->live()
                                                                        ->content(function (Get $get): string {
                                                                            $region = PhilippineRegion::where('region_code', $get('coborrower.address.permanent.region'))->first();
                                                                            $province = PhilippineProvince::where('province_code', $get('coborrower.address.permanent.province'))->first();
                                                                            $city = PhilippineCity::where('city_municipality_code', $get('coborrower.address.permanent.city'))->first();
                                                                            $barangay = PhilippineBarangay::where('barangay_code', $get('coborrower.address.permanent.barangay'))->first();
                                                                            $address = $get('coborrower.address.permanent.address');
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
                                                //Cobo Employment
                                                \Filament\Forms\Components\Fieldset::make('Employment')->schema([
                                                    Select::make('coborrower_employment.type')
                                                        ->label('Employment Type')
                                                        ->live()
                                                        ->required()
                                                        ->native(false)
                                                        ->options(EmploymentType::all()->pluck('description','code'))
                                                        ->columnSpan(3),
                                                    Select::make('coborrower_employment.status')
                                                        ->label('Employment Status')
                                                        ->required(fn (Get $get): bool =>   $get('coborrower_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->hidden(fn (Get $get): bool =>   $get('coborrower_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->native(false)
                                                        ->options(EmploymentStatus::all()->pluck('description','code'))
                                                        ->columnSpan(3),
                                                    Select::make('coborrower_employment.tenure')
                                                        ->label('Tenure')
                                                        ->required(fn (Get $get): bool =>   $get('coborrower_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->hidden(fn (Get $get): bool =>   $get('coborrower_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->native(false)
                                                        ->options(Tenure::all()->pluck('description','code'))
                                                        ->columnSpan(3),
                                                    Select::make('coborrower_employment.position')
                                                        ->label('Current Position')
                                                        ->native(false)
                                                        ->options(CurrentPosition::all()->pluck('description','code'))
                                                        ->searchable()
                                                        ->required(fn (Get $get): bool =>   $get('coborrower_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->hidden(fn (Get $get): bool =>   $get('coborrower_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->columnSpan(3),
                                                    TextInput::make('coborrower_employment.rank')
                                                        ->label('Rank')
                                                        ->required(fn (Get $get): bool =>   $get('coborrower_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->hidden(fn (Get $get): bool =>   $get('coborrower_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->maxLength(255)
                                                        ->columnSpan(3),
                                                    Select::make('coborrower_employment.work_industry')
                                                        ->label('Work Industry')
                                                        ->required()
                                                        ->native(false)
                                                        ->options(WorkIndustry::all()->pluck('description','code'))
                                                        ->searchable()
                                                        ->columnSpan(3),
                                                    TextInput::make('coborrower_employment.gross_monthly_income')
                                                        ->label('Gross Monthly Income')
                                                        ->numeric()
                                                        ->prefix('PHP')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->columnSpan(3),
                                                    Group::make()->schema([
                                                        TextInput::make('coborrower_employment.tin')
                                                            ->label('Tax Identification Number')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->columnSpan(3),
                                                        TextInput::make('coborrower_employment.pag_ibig')
                                                            ->label('PAG-IBIG Number')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->columnSpan(3),
                                                        TextInput::make('coborrower_employment.sss_gsis')
                                                            ->label('SSS/GSIS Number')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->columnSpan(3),
                                                    ])->columnSpanFull()->columns(12),


                                                ])->columns(12)->columnSpanFull(),
                                                //Cobo Employer
                                                Forms\Components\Fieldset::make('Employer/Business')->schema([
                                                    TextInput::make('coborrower_employment.employer.employer_business_name')
                                                        ->label('Employer / Business Name')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->columnSpan(3),
                                                    TextInput::make('coborrower_employment.employer.contact_person')
                                                        ->label('Contact Person')
                                                        ->required(fn (Get $get): bool =>   $get('coborrower_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->hidden(fn (Get $get): bool =>   $get('coborrower_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->maxLength(255)
                                                        ->columnSpan(3),
                                                    TextInput::make('coborrower_employment.employer.employer_email')
                                                        ->label('Email')
                                                        ->email()
                                                        ->required(fn (Get $get): bool =>   $get('coborrower_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->hidden(fn (Get $get): bool =>   $get('coborrower_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->maxLength(255)
                                                        ->columnSpan(3),
                                                    TextInput::make('coborrower_employment.employer.mobile')
                                                        ->label('Contact Number')
                                                        ->required(fn (Get $get): bool =>   $get('coborrower_employment.type')!=EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->hidden(fn (Get $get): bool =>   $get('coborrower_employment.type')==EmploymentType::where('description','Self-Employed with Business')->first()->code)
                                                        ->prefix('+63')
                                                        ->regex("/^[0-9]+$/")
                                                        ->minLength(10)
                                                        ->maxLength(10)
                                                        ->live()
//                                        ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
////                                            $livewire->validateOnly($component->getStatePath());
//                                        })
                                                        ->columnSpan(3),
                                                    TextInput::make('coborrower_employment.employer.year_established')
                                                        ->label('Year Established')
                                                        ->required()
                                                        ->numeric()
                                                        ->columnSpan(3),
//                                        Select::make('employment.employer.years_of_operation')
//                                            ->label('Years of Operation')
//                                            ->required()
//                                            ->native(false)
//                                            ->options(YearsOfOperation::all()->pluck('description','code'))
//                                            ->columnSpan(3),
                                                    Forms\Components\Fieldset::make('Address')->schema([
                                                        Group::make()
                                                            ->schema([
                                                                Select::make('coborrower_employment.employer.address.country')
                                                                    ->searchable()
                                                                    ->options(Country::all()->pluck('description','code'))
                                                                    ->native(false)
                                                                    ->live()
                                                                    ->required()
                                                                    ->columnSpan(3),
                                                            ])
                                                            ->columns(12)
                                                            ->columnSpanFull(),
                                                        Select::make('coborrower_employment.employer.address.region')
                                                            ->searchable()
                                                            ->options(PhilippineRegion::all()->pluck('region_description','region_code'))
                                                            ->required(fn(Get $get):bool => $get('coborrower_employment.employer.address.country') == 'PH')
                                                            ->hidden(fn(Get $get):bool => $get('coborrower_employment.employer.address.country') != 'PH'&&$get('coborrower_employment.employer.address.country')!=null)
                                                            ->native(false)
                                                            ->live()
                                                            ->afterStateUpdated(function(Set $set, $state){
                                                                $set('coborrower_employment.employer.address.province','');
                                                                $set('coborrower_employment.employer.address.city','');
                                                                $set('coborrower_employment.employer.address.barangay','');
                                                            })
                                                            ->columnSpan(3),
                                                        Select::make('coborrower_employment.employer.address.province')
                                                            ->searchable()
                                                            ->options(fn (Get $get): Collection => PhilippineProvince::query()
                                                                ->where('region_code', $get('coborrower_employment.employer.address.region'))
                                                                ->pluck('province_description', 'province_code'))
                                                            ->required(fn(Get $get):bool => $get('coborrower_employment.employer.address.country') == 'PH')
                                                            ->hidden(fn(Get $get):bool => $get('coborrower_employment.employer.address.country') != 'PH'&&$get('coborrower_employment.employer.address.country')!=null)
                                                            ->native(false)
                                                            ->live()
                                                            ->afterStateUpdated(function(Set $set, $state){
                                                                $set('coborrower_employment.employer.address.city','');
                                                                $set('coborrower_employment.employer.address.barangay','');
                                                            })
                                                            ->columnSpan(3),
                                                        Select::make('coborrower_employment.employer.address.city')
                                                            ->searchable()
                                                            ->options(fn (Get $get): Collection => PhilippineCity::query()
                                                                ->where('province_code', $get('coborrower_employment.employer.address.province'))
                                                                ->pluck('city_municipality_description', 'city_municipality_code'))
                                                            ->required(fn(Get $get):bool => $get('coborrower_employment.employer.address.country') == 'PH')
                                                            ->hidden(fn(Get $get):bool => $get('coborrower_employment.employer.address.country') != 'PH'&&$get('coborrower_employment.employer.address.country')!=null)
                                                            ->native(false)
                                                            ->live()
                                                            ->afterStateUpdated(function(Set $set, $state){
                                                                $set('coborrower_employment.employer.address.barangay','');
                                                            })
                                                            ->columnSpan(3),
                                                        Select::make('coborrower_employment.employer.address.barangay')
                                                            ->searchable()
                                                            ->options(fn (Get $get): Collection =>PhilippineBarangay::query()
                                                                ->where('region_code', $get('coborrower_employment.employer.address.present.region'))
//                                                    ->where('province_code', $get('buyer.address.present.province'))                                            ->where('province_code', $get('province'))
                                                                ->where('city_municipality_code', $get('coborrower_employment.employer.address.present.city'))
                                                                ->pluck('barangay_description', 'barangay_code')
                                                            )
                                                            ->required(fn(Get $get):bool => $get('coborrower_employment.employer.address.country') == 'PH')
                                                            ->hidden(fn(Get $get):bool => $get('coborrower_employment.employer.address.country') != 'PH'&&$get('coborrower_employment.employer.address.country')!=null)
                                                            ->native(false)
                                                            ->live()
                                                            ->columnSpan(3),
                                                        TextInput::make('coborrower_employment.employer.address.present.address')
                                                            ->label(fn(Get $get)=>$get('coborrower_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                            ->placeholder(fn(Get $get)=>$get('coborrower_employment.employer.address.country')!='PH'?'Full Address':'Unit Number, House/Building/Street No, Street Name')
                                                            ->required(fn(Get $get):bool => $get('coborrower_employment.employer.address.country') != 'PH')
                                                            ->autocapitalize('words')
                                                            ->maxLength(255)
                                                            ->live()
                                                            ->columnSpan(12),


                                                    ])->columns(12)->columnSpanFull(),
                                                ])->columns(12)->columnSpanFull(),
                                            ])
                                            ->columns(12)
                                            ->defaultItems(0)
                                            ->maxItems(1)
                                            ->columnSpanFull(),


                                    ])
                                        ->columns(12)
                                        ->columnSpanFull(),
                                    ])->columns(12)->columnSpanFull(),
                                Forms\Components\Tabs\Tab::make('Order')->schema([
                                    Forms\Components\Fieldset::make('Order')->schema([
                                        // Property and Project Information
                                        Forms\Components\TextInput::make('order.sku')
                                            ->label('SKU')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.seller_commission_code')
                                            ->label('Seller Commission Code')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.property_code')
                                            ->label('Property Code')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.property_type')
                                            ->label('Property Type')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.company_name')
                                            ->label('Company Name')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.project_name')
                                            ->label('Project Name')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.project_code')
                                            ->label('Project Code')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.property_name')
                                            ->label('Property Name')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.block')
                                            ->label('Block')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.lot')
                                            ->label('Lot')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.lot_area')
                                            ->label('Lot Area (sqm)')
                                            ->numeric()
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.floor_area')
                                            ->label('Floor Area (sqm)')
                                            ->numeric()
                                            ->columnSpan(3),

                                        // Loan and Transaction Details
                                        Forms\Components\TextInput::make('order.loan_term')
                                            ->label('Loan Term')
                                            ->numeric()
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.loan_interest_rate')
                                            ->label('Loan Interest Rate (%)')
                                            ->numeric()
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.tct_no')
                                            ->label('TCT Number')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.project_location')
                                            ->label('Project Location')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.project_address')
                                            ->label('Project Address')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.reservation_rate')
                                            ->label('Reservation Rate')
                                            ->numeric()
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.unit_type')
                                            ->label('Unit Type')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.unit_type_interior')
                                            ->label('Unit Type (Interior)')
                                            ->columnSpan(3),

                                        Forms\Components\DatePicker::make('order.reservation_date')
                                            ->label('Reservation Date')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.transaction_reference')
                                            ->label('Transaction Reference')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.transaction_status')
                                            ->label('Transaction Status')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.total_payments_made')
                                            ->label('Total Payments Made')
                                            ->numeric()
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.staging_status')
                                            ->label('Staging Status')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.period_id')
                                            ->label('Period ID')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.buyer_age')
                                            ->label('Buyer Age')
                                            ->numeric()
                                            ->columnSpan(3),

                                        // Seller Information
                                        Forms\Components\TextInput::make('order.seller.name')
                                            ->label('Seller Name')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.seller.id')
                                            ->label('Seller ID')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.seller.superior')
                                            ->label('Superior')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.seller.team_head')
                                            ->label('Team Head')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.seller.chief_seller_officer')
                                            ->label('Chief Seller Officer')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.seller.deputy_chief_seller_officer')
                                            ->label('Deputy Chief Seller Officer')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.seller.unit')
                                            ->label('Seller Unit')
                                            ->columnSpan(3),

                                        // Payment Scheme Section (Repeater for Fees)
                                        Forms\Components\TextInput::make('order.payment_scheme.scheme')
                                            ->label('Payment Scheme')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.payment_scheme.method')
                                            ->label('Payment Method')
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.payment_scheme.total_contract_price')
                                            ->label('Total Contract Price')
                                            ->numeric()
                                            ->columnSpan(3),
                                        Forms\Components\TextInput::make('order.payment_scheme.collectible_price')
                                            ->label('Collectible Price')
                                            ->numeric()
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.payment_scheme.commissionable_amount')
                                            ->label('Commissionable Amount')
                                            ->numeric()
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.payment_scheme.evat_percentage')
                                            ->label('EVAT Percentage')
                                            ->numeric()
                                            ->columnSpan(3),

                                        Forms\Components\TextInput::make('order.payment_scheme.evat_amount')
                                            ->label('EVAT Amount')
                                            ->numeric()
                                            ->columnSpan(3),

                                        Forms\Components\Repeater::make('order.payment_scheme.fees')
                                            ->label('Fees')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Fee Name')
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('amount')
                                                    ->label('Amount')
                                                    ->numeric()
                                                    ->columnSpan(3),
                                            ])->columns(6)
                                            ->columnSpanFull(),

                                    ])->columns(12)->columnSpanFull(),
                                ])->columns(12)->columnSpanFull(),
                            ])->columnSpanFull(),
        ])->columns(12)->columnSpan(9),
                Section::make()
                ->schema([
                    Forms\Components\TextInput::make('reference_code')
                        ->label('Reference Code')
                        ->required()
                        ->columnSpanFull(),
                    // Media Uploads
                    Forms\Components\FileUpload::make('idImage')
                        ->label('ID Image')
                        ->image()
                        ->disk('public')
                        ->directory('id-images')
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('selfieImage')
                        ->label('Selfie Image')
                        ->image()
                        ->disk('public')
                        ->directory('selfie-images')
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('payslipImage')
                        ->label('Payslip Image')
                        ->image()
                        ->disk('public')
                        ->directory('payslip-images')
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('signatureImage')
                        ->label('Signature Image')
                        ->image()
                        ->disk('public')
                        ->directory('signature-images')
                        ->columnSpanFull(),
                ])->columnSpan(3)->columns(12),

            ])->columns(12);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->poll('10')
            ->defaultPaginationPageOption(50)
            ->extremePaginationLinks()
            ->defaultSort('id','desc')
//            ->query(
//                Contact::query()
//                    ->whereIn('project',Auth::user()->projects()->pluck('description'))
//                    ->whereIn('location',Auth::user()->locations()->pluck('description'))
//            )
            ->columns([

                Tables\Columns\TextColumn::make('reference_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('civil_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sex')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nationality')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('View Details')->button(),

                Tables\Actions\Action::make('document')
                    ->button()
                    ->form([
                        Select::make('document')
                            ->label('Select Document')
                            ->native(false)
                            ->options(
                                Documents::all()->mapWithKeys(function ($document) {
                                    return [$document->id => $document->name];
                                })->toArray()
                            )
                            ->multiple()
                            ->searchable()
                            ->required(),
                        ToggleButtons::make('action')
                            ->options([
                                'view' => 'View',
                                'download' => 'Download',
                            ])
                            ->icons([
                                'view' => 'heroicon-o-eye',
                                'download' => 'heroicon-o-arrow-down-tray',
                            ])
                            ->inline()
                            ->columns(2)
                            ->default('view')
                            ->required(),
                    ])
                    ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
                    ->action(function (array $data, Contact $record, Component $livewire) {

                        foreach ($data['document'] as $d){
                        $livewire->dispatch('open-link-new-tab-event',route('contacts_docx_to_pdf', [$record,$d,$data['action']=='view'?1:0,$record->last_name]));
                        }
                    })
                    ->modalWidth(MaxWidth::Small)
            ], position: ActionsPosition::BeforeCells)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('Import OS Report')
                    ->label('Import OS Report')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('OS Report')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->maxSize(1024*12)
                            ->storeFiles(false)
                            ->live(),

                        Forms\Components\Placeholder::make('error')
                            ->label('')
                            ->content('')

                    ])
                    ->action(function (array $arguments, $form, $data,Set $set): void {
//                        Excel::import(new OSImport, $data['file'], null, \Maatwebsite\Excel\Excel::XLSX);
                        try {
                            Excel::queueImport(new OSImport, $data['file'], null, \Maatwebsite\Excel\Excel::XLSX);
                        } catch (\Exception $e) {
                            if (property_exists($e, 'validator')) {
                                $messages = $e->validator->messages()->toArray();

                                $errorMessages = collect($messages)->map(function($message, $field) {
                                    return "$field: " . implode(', ', $message) . '<br>';
                                })->implode('');


                                Log::error('Excel Import failed: ' . $errorMessages);
                                Notification::make()
                                    ->title('Excel Import failed:')
                                    ->danger()
                                    ->persistent()
                                    ->body($errorMessages)
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Excel Import failed:')
                                    ->danger()
                                    ->persistent()
                                    ->body($e->getMessage())
                                    ->send();
                                Log::error('Excel Import failed: ' . $e->getMessage());
                            }
                        }
                    })

            ])->filters([
//                SelectFilter::make('project')
//                    ->multiple()
//                    ->options(
//                        Auth::user()->projects()->get()->mapWithKeys(function ($item,$keys) {
//                            return [$item->description => $item->description];
//                        })->toArray()
//                    )->columnSpan(2),
//                SelectFilter::make('location')
//                    ->multiple()
//                    ->options(
//                        Auth::user()->locations()->get()->mapWithKeys(function ($item,$keys) {
//                            return [$item->description => $item->description];
//                        })->toArray()
//                    )->columnSpan(2)
            ], layout: FiltersLayout::AboveContent);
    }

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
