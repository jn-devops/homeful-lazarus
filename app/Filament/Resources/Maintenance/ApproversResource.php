<?php

namespace App\Filament\Resources\Maintenance;

use App\Filament\Resources\Maintenance\ApproversResource\Pages;
use App\Filament\Resources\Maintenance\ApproversResource\RelationManagers;
use App\Models\Maintenance\Approvers;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class ApproversResource extends Resource
{
    protected static ?string $model = Approvers::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Maintenance';
    protected static ?string $recordTitleAttribute = 'name';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
//                    Select::make('user_id')
//                        ->relationship(name: 'user', titleAttribute: 'name')
//                        ->searchable(['name', 'email'])
//                        ->preload()
//                        ->columnSpan(4),
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->columnSpan(4),
                    Forms\Components\TextInput::make('position')
                        ->required()
                        ->columnSpan(4),
                    Forms\Components\TextInput::make('id_type')
                        ->label('ID Type')
                        ->required()
                        ->columnSpan(4),
                    Forms\Components\TextInput::make('id_number')
                        ->label('ID Number')
                        ->required()
                        ->columnSpan(4),
                    Forms\Components\TextInput::make('issued_on')
                        ->label('Issued On')
                        ->required()
                        ->columnSpan(4),
                    Forms\Components\DatePicker::make('issued_date')
                        ->label('Issued Date')
                        ->required()
                        ->columnSpan(4),
                    Forms\Components\DatePicker::make('valid_until')
                        ->label('Valid Until')
                        ->required()
                        ->columnSpan(4),
                ])->columns(12)->columnSpan(8),
                Forms\Components\Section::make()->schema([
                    Placeholder::make('created_at')
                        ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;')),

                    Placeholder::make('updated_at')
                        ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? new HtmlString('&mdash;'))
                ])->columns(1)->columnSpan(4),

            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(25)
            ->defaultSort('id','desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_type')
                    ->label('ID Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_number')
                    ->label('ID Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issued_on')
                    ->label('Issued On')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issued_date')
                    ->label('Issued Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->label('Valid Until')
                    ->date()
                    ->sortable(),
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
            'index' => Pages\ListApprovers::route('/'),
            'create' => Pages\CreateApprovers::route('/create'),
            'edit' => Pages\EditApprovers::route('/{record}/edit'),
        ];
    }
}
