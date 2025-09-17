<?php

namespace App\Filament\Resources\PerformanceImprovementPlanResource\Pages;

use App\Filament\Resources\PerformanceImprovementPlanResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePerformanceImprovementPlan extends CreateRecord
{
    protected static string $resource = PerformanceImprovementPlanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function afterCreate(): void
    {
        //Mail::to($this->record->employee->email)->send(new pipemail($this->record));
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Performance Improvement Plan created successfully';
    }
}
