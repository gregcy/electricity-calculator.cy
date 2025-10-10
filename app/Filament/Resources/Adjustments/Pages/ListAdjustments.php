<?php

namespace App\Filament\Resources\Adjustments\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Adjustments\AdjustmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdjustments extends ListRecords
{
    protected static string $resource = AdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
