<?php

namespace App\Filament\Resources\Adjustments;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Adjustments\Pages\ListAdjustments;
use App\Filament\Resources\Adjustments\Pages\CreateAdjustment;
use App\Filament\Resources\Adjustments\Pages\EditAdjustment;
use App\Filament\Resources\AdjustmentResource\Pages;
use App\Filament\Resources\AdjustmentResource\RelationManagers;
use App\Models\Adjustment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;


class AdjustmentResource extends Resource
{
    protected static ?string $model = Adjustment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $modelLabel = 'Fuel Adjustment';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dates')->schema ([
                    DatePicker::make('start_date')
                        ->required(),
                    DatePicker::make('end_date')
                        ->required(),
                ])->columns(2),
                Section::make('Options')->schema ([
                    Select::make('consumer_type')
                        ->options(['Monthly' => 'Monthly', 'Bi-Monthly' => 'Bi-Monthly'])
                        ->required()
                        ->default(fn ($record) => $record->consumer_type ?? 'Monthly'),
                    Select::make('voltage_type')
                        ->options(['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'])
                        ->default(fn ($record) => $record->consumer_type ?? 'Low')
                        ->required(),
                ])->columns(2),
                Section::make('Calculation Parameters')->schema ([
                    TextInput::make('weighted_average_fuel_price')
                        ->required()
                        ->numeric(),
                    TextInput::make('fuel_adjustment_coefficient')
                        ->required()
                        ->numeric(),
                ])->columns(2),
                Section::make('Adjustment Prices')->schema ([
                    TextInput::make('total')
                        ->required()
                        ->numeric(),
                    TextInput::make('fuel')
                        ->required()
                        ->numeric(),
                    TextInput::make('co2_emissions')
                        ->required()
                        ->numeric(),
                    TextInput::make('cosmos')
                        ->required()
                        ->numeric(),
                    TextInput::make('revised_fuel_adjustment_price')
                        ->numeric(),
                ])->columns(3),
                Section::make('Source')->schema ([
                    TextInput::make('source')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('source_name')
                        ->required()
                        ->maxLength(255),
                ])->columns(2),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('total')
                    ->numeric(decimalPlaces: 6)
                    ->sortable(),
                TextColumn::make('revised_fuel_adjustment_price')
                    ->label('Revised')
                    ->numeric(decimalPlaces: 6)
                    ->sortable(),
                TextColumn::make('fuel')
                    ->numeric(decimalPlaces: 6)
                    ->sortable(),
                TextColumn::make('co2_emissions')
                    ->label('CO2')
                    ->numeric(decimalPlaces: 6)
                    ->sortable(),
                TextColumn::make('cosmos')
                    ->numeric(decimalPlaces: 6)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_date', 'desc');
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
            'index' => ListAdjustments::route('/'),
            'create' => CreateAdjustment::route('/create'),
            'edit' => EditAdjustment::route('/{record}/edit'),
        ];
    }
}
