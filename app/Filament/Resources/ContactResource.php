<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Settings;
use App\Filament\Imports\ContactImporter;
use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Filament\Resources\Maintenance\CompaniesResource;
use App\Models\ClientInformations;
use App\Models\Companies;
use App\Models\Documents;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
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
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Fieldset::make('Personal Information')->schema([
                            Forms\Components\TextInput::make('profile.first_name')
                                ->label('First Name')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('profile.middle_name')
                                ->label('Middle Name')
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('profile.last_name')
                                ->label('Last Name')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('profile.name_suffix')
                                ->label('Name Suffix')
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('profile.civil_status')
                                ->label('Civil Status')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('profile.sex')
                                ->label('Sex')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('profile.nationality')
                                ->label('Nationality')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\DatePicker::make('profile.date_of_birth')
                                ->label('Date of Birth')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('profile.email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('profile.mobile')
                                ->label('Mobile')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('profile.other_mobile')
                                ->label('Other Mobile')
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('profile.landline')
                                ->label('Landline')
                                ->columnSpan(3),
                        ])->columns(12)->columnSpanFull(),


                        Forms\Components\Fieldset::make('Spouse Information')->schema([
                            Forms\Components\TextInput::make('spouse.first_name')
                                ->label('First Name')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('spouse.middle_name')
                                ->label('Middle Name')
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('spouse.last_name')
                                ->label('Last Name')
                                ->required()
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('spouse.name_suffix')
                                ->label('Name Suffix')
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('spouse.civil_status')
                                ->label('Civil Status')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('spouse.sex')
                                ->label('Sex')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('spouse.nationality')
                                ->label('Nationality')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\DatePicker::make('spouse.date_of_birth')
                                ->label('Date of Birth')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('spouse.email')
                                ->label('Email Address')
                                ->email()
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('spouse.mobile')
                                ->label('Mobile Number')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('spouse.other_mobile')
                                ->label('Other Mobile')
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('spouse.landline')
                                ->label('Landline')
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('spouse.mothers_maiden_name')
                                ->label('Mother\'s Maiden Name')
                                ->columnSpan(3),
                        ])->columns(12)->columnSpanFull(),
                        Forms\Components\Fieldset::make('Address')->schema([
                            Forms\Components\TextInput::make('buyer_address_present.type')
                                ->label('Address Type')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_address_present.ownership')
                                ->label('Ownership')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_address_present.full_address')
                                ->label('Full Address')
                                ->columnSpan(6),

                            Forms\Components\TextInput::make('buyer_address_present.sublocality')
                                ->label('Barangay')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_address_present.locality')
                                ->label('City/Municipality')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_address_present.administrative_area')
                                ->label('Province')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_address_present.postal_code')
                                ->label('Postal Code')
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_address_present.block')
                                ->label('Block')
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_address_present.street')
                                ->label('Street')
                                ->columnSpan(3),
                        ])->columns(12)->columnSpanFull(),
                        Forms\Components\Fieldset::make('Employment Information')
                            ->schema([
                                Forms\Components\TextInput::make('buyer_employment.employment_status')
                                    ->label('Employment Status')
                                    ->required()
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('buyer_employment.monthly_gross_income')
                                    ->label('Monthly Gross Income')
                                    ->numeric()
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('buyer_employment.current_position')
                                    ->label('Current Position')
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('buyer_employment.rank')
                                    ->label('Rank')
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('buyer_employment.employment_type')
                                    ->label('Employment Type')
                                    ->required()
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('buyer_employment.years_in_service')
                                    ->label('Years in Service')
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('buyer_employment.salary_range')
                                    ->label('Salary Range')
                                    ->columnSpan(3),
                                Forms\Components\TextInput::make('buyer_employment.id.tin')
                                    ->label('TIN')
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('buyer_employment.id.pagibig')
                                    ->label('Pag-IBIG')
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('buyer_employment.id.sss')
                                    ->label('SSS')
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('buyer_employment.id.gsis')
                                    ->label('GSIS')
                                    ->columnSpan(3),
                                Forms\Components\TextInput::make('buyer_employment.character_reference.name')
                                    ->label('Character Reference Name')
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('buyer_employment.character_reference.mobile')
                                    ->label('Character Reference Mobile')
                                    ->columnSpan(3),
                            ])->columns(12)->columnSpanFull(),
                        Forms\Components\Fieldset::make('Employer Information')->schema([
                            Forms\Components\TextInput::make('buyer_employment.employer.name')
                                ->label('Employer Name')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_employment.employer.industry')
                                ->label('Employer Industry')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_employment.employer.nationality')
                                ->label('Employer Nationality')
                                ->required()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_employment.employer.contact_no')
                                ->label('Employer Contact Number')
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_employment.employer.year_established')
                                ->label('Year Established')
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_employment.employer.total_number_of_employees')
                                ->label('Total Number of Employees')
                                ->numeric()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_employment.employer.email')
                                ->label('Employer Email')
                                ->email()
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_employment.employer.fax')
                                ->label('Employer Fax')
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('buyer_employment.employer.address.full_address')
                                ->label('Full Address')
                                ->columnSpan(6),

                            Forms\Components\TextInput::make('buyer_employment.employer.address.locality')
                                ->label('City')
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_employment.employer.address.administrative_area')
                                ->label('Province')
                                ->columnSpan(3),

                            Forms\Components\TextInput::make('buyer_employment.employer.address.country')
                                ->label('Country')
                                ->required()
                                ->columnSpan(3),
                        ])->columns(12)->columnSpanFull(),
                        Section::make('Co-Borrowers Information')->schema([
                            // Co-Borrower Fields
                            Forms\Components\Repeater::make('co_borrowers')
                                ->label('Co-Borrowers')
                                ->schema([
                                    Forms\Components\TextInput::make('first_name')
                                        ->label('First Name')
                                        ->required()
                                        ->columnSpan(3),

                                    Forms\Components\TextInput::make('middle_name')
                                        ->label('Middle Name')
                                        ->columnSpan(3),

                                    Forms\Components\TextInput::make('last_name')
                                        ->label('Last Name')
                                        ->required()
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
                                        ->columnSpan(3),

                                    Forms\Components\TextInput::make('other_mobile')
                                        ->label('Other Mobile Number')
                                        ->columnSpan(3),

                                    Forms\Components\TextInput::make('help_number')
                                        ->label('Help Number')
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
                                ])
                                ->columns(12)
                                ->columnSpanFull(),
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
                        ])
                            ->columns(12)
                            ->columnSpanFull(),

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
