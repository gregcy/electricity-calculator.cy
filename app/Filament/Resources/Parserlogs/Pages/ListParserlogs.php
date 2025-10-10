<?php

namespace App\Filament\Resources\Parserlogs\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Parserlogs\ParserlogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParserlogs extends ListRecords
{
    protected static string $resource = ParserlogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
