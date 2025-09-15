<?php

namespace App\Filament\Resources\BenefitsAllowancesResource\Pages;

use App\Filament\Resources\BenefitsAllowancesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBenefitsAllowances extends CreateRecord
{
    protected static string $resource = BenefitsAllowancesResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
