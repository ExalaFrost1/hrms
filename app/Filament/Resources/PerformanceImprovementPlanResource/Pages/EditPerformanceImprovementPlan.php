<?php

namespace App\Filament\Resources\PerformanceImprovementPlanResource\Pages;

use App\Filament\Resources\PerformanceImprovementPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerformanceImprovementPlan extends EditRecord
{
    protected static string $resource = PerformanceImprovementPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete PIP')
                ->modalDescription('Are you sure you want to delete this Performance Improvement Plan? This action cannot be undone.'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Performance Improvement Plan updated successfully';
    }
}
