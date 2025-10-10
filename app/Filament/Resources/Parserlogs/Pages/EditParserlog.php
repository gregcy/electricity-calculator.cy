<?php

namespace App\Filament\Resources\Parserlogs\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Parserlogs\ParserlogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParserlog extends EditRecord
{
    protected static string $resource = ParserlogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
