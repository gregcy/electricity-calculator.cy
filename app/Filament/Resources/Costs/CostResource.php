<?php

namespace App\Filament\Resources\Costs;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Costs\Pages\ListCosts;
use App\Filament\Resources\Costs\Pages\CreateCost;
use App\Filament\Resources\Costs\Pages\EditCost;
use App\Filament\Resources\CostResource\Pages;
use App\Filament\Resources\CostResource\RelationManagers;
use App\Models\Cost;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CostResource extends Resource
{
    protected static ?string $model = Cost::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $modelLabel = 'Other Cost';

    protected static ?int $navigationSort = 3;


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dates')->schema ([
                    DatePicker::make('start_date')
                        ->required(),
                    DatePicker::make('end_date'),
                ])->columns(2),
                Section::make('Tariff Code')->schema ([
                    Select::make('code')
                    ->options(['01' => '01 - Single Rate Domestic Use', '02' => '02 - Two Rate Domestic Use', '08' => '08 - Special Rate for Vunerable Customers'])
                    ->default(fn ($record) => $record->code ?? ''),
                ])->columns(1),
                Section::make('Cost Values')->schema ([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('value')
                        ->required()
                        ->numeric(),
                    TextInput::make('prefix')
                        ->maxLength(255),
                    TextInput::make('suffix')
                        ->maxLength(255),
                ])->columns(2),
                Section::make('Source')->schema ([
                    TextInput::make('source')
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
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->placeholder('Current'),
                TextColumn::make('prefix')
                    ->searchable(),
                TextColumn::make('value')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('suffix')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('code')
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
            ->defaultSort('name', 'asc');
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
            'index' => ListCosts::route('/'),
            'create' => CreateCost::route('/create'),
            'edit' => EditCost::route('/{record}/edit'),
        ];
    }
}
