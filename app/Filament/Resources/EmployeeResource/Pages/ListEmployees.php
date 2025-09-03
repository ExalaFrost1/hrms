<?php
// app/Filament/Resources/EmployeeResource/Pages/ListEmployees.php
namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export')
                ->label('Export Employees')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    // Export functionality will be implemented
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Employees'),
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active')),
            'inactive' => Tab::make('Inactive')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'inactive')),
            'on_leave' => Tab::make('On Leave')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'on_leave')),
            'terminated' => Tab::make('Terminated')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'terminated')),
        ];
    }
}
