<?php

namespace App\Filament\Resources\Maintenance;

use App\Filament\Resources\Maintenance\LocationsResource\Pages;
use App\Filament\Resources\Maintenance\LocationsResource\RelationManagers;
use App\Models\Locations;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class LocationsResource extends Resource
{
    protected static ?string $model = Locations::class;
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
//    protected static ?string $navigationParentItem = 'Companies';
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make()
            ->schema([
                TextInput::make('code')
                    ->required()
                    ->columnSpan(4),
                TextInput::make('description')
                    ->label('Name')
                    ->required()
                    ->columnSpan(8),


            ])
            ->columns(12)->columnSpan(2),

            Forms\Components\Group::make()
                ->schema([
                    // Forms\Components\Section::make()
                    //     ->schema([

                    //         Placeholder::make('created_at')
                    //     ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;')),
                    //     Placeholder::make('updated_at')
                    //         ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;'))
                    // ]),
                    Forms\Components\Section::make()
                        ->schema([

                            Placeholder::make('created_at')
                        ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;')),
                        Placeholder::make('updated_at')
                            ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;'))
                    ]),
                ])
                ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(25)
            ->defaultSort('id','desc')
            ->columns([
                TextColumn::make('code'),
                TextColumn::make('description'),
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
            'index' => Pages\ManageLocations::route('/'),
        ];
    }
}
