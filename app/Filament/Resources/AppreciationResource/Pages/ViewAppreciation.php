<?php

namespace App\Filament\Resources\AppreciationResource\Pages;

use App\Filament\Resources\AppreciationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAppreciation extends ViewRecord
{
    protected static string $resource = AppreciationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status === 'pending_approval')
                ->form([
                    \Filament\Forms\Components\TextInput::make('approved_by')
                        ->label('Approved By')
                        ->required()
                        ->default(auth()->user()->name ?? 'HR Manager'),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'approved',
                        'approved_by' => $data['approved_by'],
                    ]);
                }),
            Actions\Action::make('publish')
                ->label('Publish')
                ->icon('heroicon-o-megaphone')
                ->color('info')
                ->visible(fn () => $this->record->status === 'approved')
                ->action(function () {
                    $this->record->update([
                        'status' => 'published',
                        'publication_date' => now(),
                    ]);
                })
                ->requiresConfirmation()
                ->modalHeading('Publish Recognition')
                ->modalDescription('This will make the recognition visible to the organization.'),
        ];
    }
}
