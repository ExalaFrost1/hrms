<?php

namespace App\Filament\Resources\PerformanceImprovementPlanResource\Pages;

use App\Filament\Resources\PerformanceImprovementPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPerformanceImprovementPlans extends ListRecords
{
    protected static string $resource = PerformanceImprovementPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create New PIP')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All PIPs')
                ->badge(fn () => \App\Models\PerformanceImprovementPlan::count()),

            'draft' => Tab::make('Draft')
                ->badge(fn () => \App\Models\PerformanceImprovementPlan::where('status', 'draft')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft')),

            'active' => Tab::make('Active')
                ->badge(fn () => \App\Models\PerformanceImprovementPlan::where('status', 'active')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active')),

            'under_review' => Tab::make('Under Review')
                ->badge(fn () => \App\Models\PerformanceImprovementPlan::where('status', 'under_review')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'under_review')),

            'successful' => Tab::make('Successful')
                ->badge(fn () => \App\Models\PerformanceImprovementPlan::where('status', 'successful')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'successful')),

            'unsuccessful' => Tab::make('Unsuccessful')
                ->badge(fn () => \App\Models\PerformanceImprovementPlan::whereIn('status', ['unsuccessful', 'terminated'])->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['unsuccessful', 'terminated'])),

            'overdue' => Tab::make('Overdue')
                ->badge(fn () => \App\Models\PerformanceImprovementPlan::where('status', 'active')->where('end_date', '<', now())->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active')->where('end_date', '<', now())),
        ];
    }
}
