<?php

namespace App\Filament\Resources\Adjustments\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Adjustments\AdjustmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdjustment extends EditRecord
{
    protected static string $resource = AdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
