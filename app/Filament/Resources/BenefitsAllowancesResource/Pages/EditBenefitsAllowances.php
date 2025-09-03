<?php

namespace App\Filament\Resources\BenefitsAllowancesResource\Pages;

use App\Filament\Resources\BenefitsAllowancesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBenefitsAllowances extends EditRecord
{
    protected static string $resource = BenefitsAllowancesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
