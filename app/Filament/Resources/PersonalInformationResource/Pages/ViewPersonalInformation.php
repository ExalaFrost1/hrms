<?php

namespace App\Filament\Resources\PersonalInformationResource\Pages;

use App\Filament\Resources\PersonalInformationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPersonalInformation extends ViewRecord
{
    protected static string $resource = PersonalInformationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square'),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Employee Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('employee.full_name')
                            ->label('Employee Name')
                            ->icon('heroicon-o-user')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('employee.employee_id')
                            ->label('Employee ID')
                            ->icon('heroicon-o-identification')
                            ->copyable(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Infolists\Components\Section::make('Personal Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('date_of_birth')
                            ->label('Date of Birth')
                            ->date('F j, Y')
                            ->icon('heroicon-o-calendar-days'),
                        Infolists\Components\TextEntry::make('age')
                            ->label('Age')
                            ->suffix(' years old')
                            ->icon('heroicon-o-clock'),
                        Infolists\Components\TextEntry::make('gender')
                            ->label('Gender')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'male' => 'blue',
                                'female' => 'pink',
                                'other' => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('marital_status')
                            ->label('Marital Status')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'single' => 'gray',
                                'married' => 'success',
                                'divorced' => 'warning',
                                'widowed' => 'danger',
                            }),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Infolists\Components\Section::make('Contact Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('phone_number')
                            ->label('Phone Number')
                            ->icon('heroicon-o-phone')
                            ->copyable()
                            ->url(fn ($state) => $state ? 'tel:' . $state : null),
                        Infolists\Components\TextEntry::make('personal_email')
                            ->label('Personal Email')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->url(fn ($state) => $state ? 'mailto:' . $state : null)
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('residential_address')
                            ->label('Residential Address')
                            ->icon('heroicon-o-home')
                            ->copyable()
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('city')
                            ->label('City')
                            ->icon('heroicon-o-map-pin'),
                        Infolists\Components\TextEntry::make('state')
                            ->label('State/Province')
                            ->icon('heroicon-o-map'),
                        Infolists\Components\TextEntry::make('postal_code')
                            ->label('Postal Code')
                            ->icon('heroicon-o-hashtag'),
                        Infolists\Components\TextEntry::make('country')
                            ->label('Country')
                            ->icon('heroicon-o-globe-alt')
                            ->default('Pakistan'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Infolists\Components\Section::make('Emergency Contact')
                    ->schema([
                        Infolists\Components\TextEntry::make('emergency_contact_name')
                            ->label('Contact Name')
                            ->icon('heroicon-o-user')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('emergency_contact_relationship')
                            ->label('Relationship')
                            ->icon('heroicon-o-heart')
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        Infolists\Components\TextEntry::make('emergency_contact_phone')
                            ->label('Contact Phone')
                            ->icon('heroicon-o-phone')
                            ->copyable()
                            ->url(fn ($state) => $state ? 'tel:' . $state : null),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Infolists\Components\Section::make('Government Documents')
                    ->schema([
                        Infolists\Components\TextEntry::make('national_id')
                            ->label('National ID (CNIC)')
                            ->icon('heroicon-o-identification')
                            ->copyable()
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('passport_number')
                            ->label('Passport Number')
                            ->icon('heroicon-o-document-text')
                            ->copyable()
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('tax_number')
                            ->label('Tax Number')
                            ->icon('heroicon-o-calculator')
                            ->copyable()
                            ->placeholder('Not provided'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Infolists\Components\Section::make('System Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Record Created')
                            ->dateTime('F j, Y \a\t g:i A')
                            ->icon('heroicon-o-plus-circle')
                            ->color('success'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('F j, Y \a\t g:i A')
                            ->icon('heroicon-o-pencil-square')
                            ->color('warning')
                            ->since(),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
        ];
    }

    public function getTitle(): string
    {
        return "Personal Information - " . ($this->record->employee->name ?? 'Employee #' . $this->record->employee_id);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // You can add widgets here if needed
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // You can add widgets here if needed
        ];
    }
}
