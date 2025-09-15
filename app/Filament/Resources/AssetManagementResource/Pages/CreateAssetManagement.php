<?php

namespace App\Filament\Resources\AssetManagementResource\Pages;

use App\Filament\Resources\AssetManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetManagement extends CreateRecord
{
    protected static string $resource = AssetManagementResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
