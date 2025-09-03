<?php

namespace App\Filament\Resources\AssetManagementResource\Pages;

use App\Filament\Resources\AssetManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetManagement extends EditRecord
{
    protected static string $resource = AssetManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
