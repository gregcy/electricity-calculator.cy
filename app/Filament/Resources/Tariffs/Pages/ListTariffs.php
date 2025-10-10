<?php

namespace App\Filament\Resources\Tariffs\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Tariffs\TariffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTariffs extends ListRecords
{
    protected static string $resource = TariffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
