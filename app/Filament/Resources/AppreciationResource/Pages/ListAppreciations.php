<?php

namespace App\Filament\Resources\AppreciationResource\Pages;

use App\Filament\Resources\AppreciationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAppreciations extends ListRecords
{
    protected static string $resource = AppreciationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create New Recognition')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Recognitions')
                ->badge(fn () => \App\Models\Appreciation::count()),

            'draft' => Tab::make('Draft')
                ->badge(fn () => \App\Models\Appreciation::where('status', 'draft')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft')),

            'pending_approval' => Tab::make('Pending Approval')
                ->badge(fn () => \App\Models\Appreciation::where('status', 'pending_approval')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending_approval')),

            'approved' => Tab::make('Approved')
                ->badge(fn () => \App\Models\Appreciation::where('status', 'approved')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved')),

            'published' => Tab::make('Published')
                ->badge(fn () => \App\Models\Appreciation::where('status', 'published')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'published')),
        ];
    }
}
