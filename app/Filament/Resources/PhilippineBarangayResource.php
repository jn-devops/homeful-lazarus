<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PhilippineBarangayExporter;
use App\Filament\Resources\PhilippineBarangayResource\Pages;
use App\Filament\Resources\PhilippineBarangayResource\RelationManagers;
use App\Models\PhilippineBarangay;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PhilippineBarangayResource extends Resource
{
    protected static ?string $model = PhilippineBarangay::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Dropdowns';
    protected static ?string $recordTitleAttribute ='barangay_description';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('barangay_code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('barangay_description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('region_code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('province_code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('city_municipality_code')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barangay_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('barangay_description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region.region_description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('province.province_description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.city_municipality_description')
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->headerActions([
                ExportAction::make('Export')
                ->exporter(PhilippineBarangayExporter::class)
                ->columnMapping(false)
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePhilippineBarangays::route('/'),
        ];
    }
}
