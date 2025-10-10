<?php

namespace App\Filament\Resources\Tariffs\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Tariffs\TariffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTariff extends EditRecord
{
    protected static string $resource = TariffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
