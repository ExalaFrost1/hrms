<?php

namespace App\Filament\Resources\PerformanceImprovementPlanResource\Pages;

use App\Filament\Resources\PerformanceImprovementPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPerformanceImprovementPlan extends ViewRecord
{
    protected static string $resource = PerformanceImprovementPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('activate')
                ->label('Activate PIP')
                ->icon('heroicon-o-play')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'draft')
                ->action(function () {
                    $this->record->update(['status' => 'active']);
                })
                ->requiresConfirmation()
                ->modalHeading('Activate PIP')
                ->modalDescription('This will activate the Performance Improvement Plan and begin the improvement period.'),
            Actions\Action::make('mark_successful')
                ->label('Mark Successful')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => in_array($this->record->status, ['active', 'under_review']))
                ->form([
                    \Filament\Forms\Components\DatePicker::make('completion_date')
                        ->label('Completion Date')
                        ->required()
                        ->default(now())
                        ->native(false)
                        ->displayFormat('d/m/Y'),
                    \Filament\Forms\Components\Textarea::make('final_notes')
                        ->label('Final Notes')
                        ->rows(3)
                        ->placeholder('Notes about successful completion'),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'successful',
                        'final_outcome' => 'successful_completion',
                        'completion_date' => $data['completion_date'],
                        'hr_notes' => ($this->record->hr_notes ? $this->record->hr_notes . "\n\n" : '') .
                            "SUCCESSFUL COMPLETION: " . $data['final_notes'],
                    ]);
                }),
            Actions\Action::make('extend_pip')
                ->label('Extend PIP')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'active')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('new_end_date')
                        ->label('New End Date')
                        ->required()
                        ->after($this->record->end_date)
                        ->native(false)
                        ->displayFormat('d/m/Y'),
                    \Filament\Forms\Components\Textarea::make('extension_reason')
                        ->label('Reason for Extension')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'end_date' => $data['new_end_date'],
                        'status' => 'extended',
                        'supervisor_notes' => ($this->record->supervisor_notes ? $this->record->supervisor_notes . "\n\n" : '') .
                            "PIP EXTENDED: " . $data['extension_reason'],
                    ]);
                }),
        ];
    }
}
