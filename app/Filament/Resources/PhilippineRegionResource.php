<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhilippineRegionResource\Pages;
use App\Filament\Resources\PhilippineRegionResource\RelationManagers;
use App\Models\PhilippineRegion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PhilippineRegionResource extends Resource
{
    protected static ?string $model = PhilippineRegion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Dropdowns';
    protected static ?string $recordTitleAttribute ='region_description';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('psgc_code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('region_description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('region_code')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('psgc_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region_description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region_code')
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePhilippineRegions::route('/'),
        ];
    }
}
