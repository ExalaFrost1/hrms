<?php

namespace App\Filament\Resources\CompensationHistoryResource\Pages;

use App\Filament\Resources\CompensationHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompensationHistories extends ListRecords
{
    protected static string $resource = CompensationHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
