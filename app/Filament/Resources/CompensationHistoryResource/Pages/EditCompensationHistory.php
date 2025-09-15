<?php

namespace App\Filament\Resources\CompensationHistoryResource\Pages;

use App\Filament\Resources\CompensationHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompensationHistory extends EditRecord
{
    protected static string $resource = CompensationHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
