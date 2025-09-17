<?php

namespace App\Filament\Resources\WarningResource\Pages;

use App\Filament\Resources\WarningResource;
use App\Mail\WarningEmail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateWarning extends CreateRecord
{
    protected static string $resource = WarningResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        Mail::to($this->record->employee->email)->send(new WarningEmail($this->record));
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Warning issued successfully';
    }
}
