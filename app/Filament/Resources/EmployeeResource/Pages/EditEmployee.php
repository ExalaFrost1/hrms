<?php
// app/Filament/Resources/EmployeeResource/Pages/EditEmployee.php
namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('reset_password')
                ->label('Reset Password')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['password' => \Hash::make('password123')]);
                    $this->notify('success', 'Password reset to: password123');
                }),
            Actions\Action::make('deactivate')
                ->label($this->record->status === 'active' ? 'Deactivate' : 'Activate')
                ->icon('heroicon-o-power')
                ->color($this->record->status === 'active' ? 'danger' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    $newStatus = $this->record->status === 'active' ? 'inactive' : 'active';
                    $this->record->update(['status' => $newStatus]);
                    $this->notify('success', 'Employee status updated');
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle nested relationships data
        if (isset($data['personalInfo'])) {
            $personalInfo = $data['personalInfo'];
            unset($data['personalInfo']);

            if ($this->record->personalInfo) {
                $this->record->personalInfo->update($personalInfo);
            } else {
                $this->record->personalInfo()->create($personalInfo);
            }
        }

        if (isset($data['employmentHistory'])) {
            $employmentHistory = $data['employmentHistory'];
            unset($data['employmentHistory']);

            if ($this->record->employmentHistory) {
                $this->record->employmentHistory->update($employmentHistory);
            } else {
                $this->record->employmentHistory()->create($employmentHistory);
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

}
