<?php

namespace App\Filament\Resources\BenefitsAllowancesResource\Pages;

use App\Filament\Resources\BenefitsAllowancesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBenefitsAllowances extends ListRecords
{
    protected static string $resource = BenefitsAllowancesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
