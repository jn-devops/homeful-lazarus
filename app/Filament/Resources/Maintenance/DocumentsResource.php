<?php

namespace App\Filament\Resources\Maintenance;

use App\Filament\Resources\Maintenance\DocumentsResource\Pages;
use App\Filament\Resources\Maintenance\DocumentsResource\RelationManagers;
use App\Livewire\DocumentPreviewComponent;
use App\Models\Companies;
use App\Models\Documents;
use App\Models\Maintenance\Approvers;
use App\Models\Projects;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Homeful\Contacts\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use phpDocumentor\Reflection\Types\False_;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Schema;
use Filament\Forms\Components\Livewire;
use ValentinMorice\FilamentJsonColumn\FilamentJsonColumn;

class DocumentsResource extends Resource
{
    protected static ?string $model = Documents::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Section::make()->schema([

                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->required(),
                        Forms\Components\Select::make('company_code')
                            ->label('Company')
                            ->options(
                                Companies::all()->mapWithKeys(function($company){
                                    return [$company->code=>$company->description];
                                })->toArray()
                            )->native(false)
                            ->multiple()
                            ->required(),
                        Forms\Components\Select::make('projects')
                            ->label('Projects')
                            ->options(
                                Projects::all()->mapWithKeys(function($company){
                                    return [$company->code=>$company->description];
                                })->toArray()
                            )->native(false)
                            ->multiple()
                            ->required(),
                        FileUpload::make('file_attachment')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->unique('documents')
                            ->directory('documents')
                            ->downloadable()
                            ->preserveFilenames()
                            ->maxSize(1024*12)
                            ->required(),
                        Forms\Components\Select::make('approvers')
                            ->label('Approvers')
                            ->options(
                                Approvers::all()->mapWithKeys(function($approver){
                                    return [$approver->id=>$approver->name];
                                })->toArray()
                            )->native(false)
                            ->multiple(),


                    ])->columns(1)->columnSpan(4),

                    FilamentJsonColumn::make('data')->columnSpan(8),
                ])->columns(12)->columnSpanFull(),
                Livewire::make(DocumentPreviewComponent::class)
                    ->key(Carbon::now()->format('Y-m-d H:i:s'))
                    ->columnSpanFull(),
//                Forms\Components\Section::make()->schema([
//                    Livewire::make(DocumentPreviewComponent::class)
//                        ->key(Carbon::now()->format('Y-m-d H:i:s'))
//                        ->columnSpanFull()
//                ])->columnSpan(8),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {



        return $table
            ->defaultPaginationPageOption(25)
            ->defaultSort('id','desc')
            ->columns([
                Tables\Columns\TextColumn::make('company_code')
                    ->label('Company')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocuments::route('/create'),
            'edit' => Pages\EditDocuments::route('/{record}/edit'),
        ];
    }
}
