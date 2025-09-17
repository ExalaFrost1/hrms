<?php

namespace App\Filament\Resources\AppreciationResource\Pages;

use App\Filament\Resources\AppreciationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppreciation extends EditRecord
{
    protected static string $resource = AppreciationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete Recognition')
                ->modalDescription('Are you sure you want to delete this recognition? This action cannot be undone.'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Recognition updated successfully';
    }
}
