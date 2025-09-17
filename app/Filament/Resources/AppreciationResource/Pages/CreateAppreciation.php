<?php

namespace App\Filament\Resources\AppreciationResource\Pages;

use App\Filament\Resources\AppreciationResource;
use App\Mail\AppreciationEmail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateAppreciation extends CreateRecord
{
    protected static string $resource = AppreciationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        //Mail::to($this->record->employee->email)->send(new AppreciationEmail($this->record));
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Recognition created successfully';
    }
}
