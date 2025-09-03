<?php

namespace App\Filament\Resources\AssetManagementResource\Pages;

use App\Filament\Resources\AssetManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssetManagement extends ListRecords
{
    protected static string $resource = AssetManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
