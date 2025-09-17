<?php
namespace App\Filament\Resources\WarningResource\Pages;

use App\Filament\Resources\WarningResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWarning extends ViewRecord
{
    protected static string $resource = WarningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('acknowledge')
                ->label('Mark as Acknowledged')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => !$this->record->employee_acknowledgment)
                ->action(function () {
                    $this->record->update([
                        'employee_acknowledgment' => true,
                        'status' => 'acknowledged'
                    ]);
                })
                ->requiresConfirmation()
                ->modalHeading('Mark Warning as Acknowledged')
                ->modalDescription('This will mark the warning as acknowledged by the employee.'),
            Actions\Action::make('resolve')
                ->label('Mark as Resolved')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn () => $this->record->status !== 'resolved')
                ->action(function () {
                    $this->record->update([
                        'status' => 'resolved',
                        'resolution_date' => now(),
                    ]);
                })
                ->requiresConfirmation()
                ->modalHeading('Mark Warning as Resolved')
                ->modalDescription('This will mark the warning as resolved.'),
        ];
    }
}
