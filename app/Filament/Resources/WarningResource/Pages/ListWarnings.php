<?php
// app/Filament/Resources/WarningResource/Pages/ListWarnings.php
namespace App\Filament\Resources\WarningResource\Pages;

use App\Filament\Resources\WarningResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListWarnings extends ListRecords
{
    protected static string $resource = WarningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Issue New Warning')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Warnings')
                ->badge(fn () => \App\Models\Warning::count()),

            'active' => Tab::make('Active')
                ->badge(fn () => \App\Models\Warning::where('status', 'active')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active')),

            'acknowledged' => Tab::make('Acknowledged')
                ->badge(fn () => \App\Models\Warning::where('status', 'acknowledged')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'acknowledged')),

            'resolved' => Tab::make('Resolved')
                ->badge(fn () => \App\Models\Warning::where('status', 'resolved')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'resolved')),

            'escalated' => Tab::make('Escalated')
                ->badge(fn () => \App\Models\Warning::where('status', 'escalated')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'escalated')),
        ];
    }
}
