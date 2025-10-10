<?php

namespace App\Filament\Resources\Adjustments\Pages;

use App\Filament\Resources\Adjustments\AdjustmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAdjustment extends CreateRecord
{
    protected static string $resource = AdjustmentResource::class;
}
