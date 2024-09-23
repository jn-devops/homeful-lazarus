<?php

namespace App\Filament\Resources\Maintenance;

use App\Filament\Clusters\Settings;
use App\Filament\Resources\Maintenance\CompaniesResource\Pages;
use App\Filament\Resources\Maintenance\CompaniesResource\RelationManagers;
use App\Models\Companies;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompaniesResource extends Resource
{
    protected static ?string $model = Companies::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Maintenance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('isActive')
                    ->label('Active')
                    ->inline(false),
                TextInput::make('code')
                    ->required()
                    ->columnSpan(3),
                TextInput::make('description')
                    ->label('Name')
                    ->required()
                    ->columnSpan(8),
                FileUpload::make('file_attachment')
                    ->acceptedFileTypes(['application/png','application/x-png','image/jpeg','image/png'])
                    ->directory('company_logo')
                    ->preserveFilenames()
                    ->required()
                    ->columnSpan(12),

            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(25)
            ->defaultSort('id','desc')
            ->columns([
                ToggleColumn::make('isActive')
                    ->label('Active')
                    ->sortable(),
                TextColumn::make('code')
                    ->sortable(),
                TextColumn::make('description') ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCompanies::route('/'),
        ];
    }
}
