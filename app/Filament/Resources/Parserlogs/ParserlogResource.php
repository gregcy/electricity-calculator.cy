<?php

namespace App\Filament\Resources\Parserlogs;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Parserlogs\Pages\ListParserlogs;
use App\Filament\Resources\Parserlogs\Pages\CreateParserlog;
use App\Filament\Resources\Parserlogs\Pages\EditParserlog;
use App\Filament\Resources\ParserlogResource\Pages;
use App\Filament\Resources\ParserlogResource\RelationManagers;
use App\Models\Parserlog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParserlogResource extends Resource
{
    protected static ?string $model = Parserlog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $modelLabel = 'Parser Log';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('type')
                    ->maxLength(255),
                TextInput::make('status')
                    ->maxLength(255),
                TextInput::make('message')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('message')
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (Parserlog $record) => match ($record->status) {
                        'success' => 'success',
                        'error' => 'danger',
                        default => 'warning',
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => ListParserlogs::route('/'),
            'create' => CreateParserlog::route('/create'),
            'edit' => EditParserlog::route('/{record}/edit'),
        ];
    }
}
