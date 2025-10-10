<?php

namespace App\Filament\Resources\Costs\Pages;

use App\Filament\Resources\Costs\CostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCost extends CreateRecord
{
    protected static string $resource = CostResource::class;
}
