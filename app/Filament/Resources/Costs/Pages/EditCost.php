<?php

namespace App\Filament\Resources\Costs\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Costs\CostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCost extends EditRecord
{
    protected static string $resource = CostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
