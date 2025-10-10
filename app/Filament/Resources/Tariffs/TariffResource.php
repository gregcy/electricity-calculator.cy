<?php

namespace App\Filament\Resources\Tariffs;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Tariffs\Pages\ListTariffs;
use App\Filament\Resources\Tariffs\Pages\CreateTariff;
use App\Filament\Resources\Tariffs\Pages\EditTariff;
use App\Filament\Resources\TariffResource\Pages;
use App\Filament\Resources\TariffResource\RelationManagers;
use App\Models\Tariff;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TariffResource extends Resource
{
    protected static ?string $model = Tariff::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dates')->schema ([
                    DatePicker::make('start_date')
                        ->required(),
                    DatePicker::make('end_date'),
                ])->columns(2),
                Section::make('Code')->schema ([
                    Select::make('code')
                    ->options(['01' => '01 - Single Rate Domestic Use', '02' => '02 - Two Rate Domestic Use', '08' => '08 - Special Rate for Vunerable Customers'])
                    ->required()
                    ->default(fn ($record) => $record->code ?? '01'),
                ])->columns(1),
                Section::make('Recurring Charges')->schema ([
                    TextInput::make('recurring_supply_charge')
                        ->numeric(),
                    TextInput::make('recurring_meter_reading')
                        ->numeric(),
                ])->columns(2),
                Section::make('Normal Charges')->schema ([
                    TextInput::make('energy_charge_normal')
                        ->numeric(),
                    TextInput::make('network_charge_normal')
                        ->numeric(),
                    TextInput::make('ancillary_services_normal')
                        ->numeric(),
                ])->columns(3),
                Section::make('Reduced Charges')->schema ([
                    TextInput::make('energy_charge_reduced')
                        ->numeric(),
                    TextInput::make('network_charge_reduced')
                        ->numeric(),
                    TextInput::make('ancillary_services_reduced')
                        ->numeric(),
                ])->columns(3),
                Section::make('Subsidised Charges')->schema ([
                    TextInput::make('energy_charge_subsidy_first')
                        ->numeric(),
                    TextInput::make('energy_charge_subsidy_second')
                        ->numeric(),
                    TextInput::make('energy_charge_subsidy_third')
                        ->numeric(),
                    TextInput::make('supply_subsidy_first')
                        ->numeric(),
                    TextInput::make('supply_subsidy_second')
                        ->numeric(),
                    TextInput::make('supply_subsidy_third')
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
                    ->placeholder('Current')
                    ->sortable(),
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('source_name')
                    ->searchable(),
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
            'index' => ListTariffs::route('/'),
            'create' => CreateTariff::route('/create'),
            'edit' => EditTariff::route('/{record}/edit'),
        ];
    }
}
