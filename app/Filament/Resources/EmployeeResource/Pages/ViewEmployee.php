<?php
// app/Filament/Resources/EmployeeResource/Pages/ViewEmployee.php
namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('download_profile')
                ->label('Download Profile')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // Generate PDF profile
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Tabs::make('Employee Profile')
                    ->tabs([
                        Components\Tabs\Tab::make('Personal Information')
                            ->schema([
                                Components\Section::make('Basic Details')
                                    ->schema([
                                        Components\Split::make([
                                            Components\Grid::make(2)
                                                ->schema([
                                                    Components\Group::make([
                                                        Components\TextEntry::make('employee_id')
                                                            ->label('Employee ID'),
                                                        Components\TextEntry::make('full_name')
                                                            ->label('Full Name'),
                                                        Components\TextEntry::make('email'),
                                                        Components\TextEntry::make('personalInfo.phone_number')
                                                            ->label('Phone Number'),
                                                        Components\TextEntry::make('personalInfo.personal_email')
                                                            ->label('Personal Email'),
                                                    ]),
                                                    Components\Group::make([
                                                        Components\ImageEntry::make('profile_photo')
                                                            ->hiddenLabel()
                                                            ->circular()
                                                            ->size(150),
                                                        Components\TextEntry::make('status')->badge(),
                                                    ]),
                                                ]),
                                        ]),
                                    ]),
                                Components\Section::make('Personal Details')
                                    ->schema([
                                        Components\TextEntry::make('personalInfo.date_of_birth')
                                            ->label('Date of Birth')
                                            ->date(),
                                        Components\TextEntry::make('personalInfo.age')
                                            ->label('Age'),
                                        Components\TextEntry::make('personalInfo.gender')
                                            ->label('Gender')
                                            ->badge(),
                                        Components\TextEntry::make('personalInfo.marital_status')
                                            ->label('Marital Status')
                                            ->badge(),
                                        Components\TextEntry::make('personalInfo.national_id')
                                            ->label('National ID'),
                                        Components\TextEntry::make('personalInfo.passport_number')
                                            ->label('Passport Number'),
                                    ])->columns(3),
                                Components\Section::make('Address Information')
                                    ->schema([
                                        Components\TextEntry::make('personalInfo.residential_address')
                                            ->label('Address'),
                                        Components\TextEntry::make('personalInfo.city')
                                            ->label('City'),
                                        Components\TextEntry::make('personalInfo.state')
                                            ->label('State'),
                                        Components\TextEntry::make('personalInfo.postal_code')
                                            ->label('Postal Code'),
                                        Components\TextEntry::make('personalInfo.country')
                                            ->label('Country'),
                                    ])->columns(3),
                                Components\Section::make('Emergency Contact')
                                    ->schema([
                                        Components\TextEntry::make('personalInfo.emergency_contact_name')
                                            ->label('Name'),
                                        Components\TextEntry::make('personalInfo.emergency_contact_relationship')
                                            ->label('Relationship'),
                                        Components\TextEntry::make('personalInfo.emergency_contact_phone')
                                            ->label('Phone'),
                                    ])->columns(3),
                            ]),

                        Components\Tabs\Tab::make('Employment')
                            ->schema([
                                Components\Section::make('Current Employment')
                                    ->schema([
                                        Components\TextEntry::make('employmentHistory.joining_date')
                                            ->label('Joining Date')
                                            ->date(),
                                        Components\TextEntry::make('employmentHistory.probation_end_date')
                                            ->label('Probation End Date')
                                            ->date(),
                                        Components\TextEntry::make('employmentHistory.employment_type')
                                            ->label('Employment Type')
                                            ->badge(),
                                        Components\TextEntry::make('employmentHistory.current_department')
                                            ->label('Department'),
                                        Components\TextEntry::make('employmentHistory.current_role')
                                            ->label('Role'),
                                        Components\TextEntry::make('employmentHistory.current_grade')
                                            ->label('Grade'),
                                        Components\TextEntry::make('employmentHistory.current_manager')
                                            ->label('Current Manager'),
                                        Components\TextEntry::make('employmentHistory.current_salary')
                                            ->label('Current Salary')
                                            ->money('USD'),
                                    ])->columns(2),
                                Components\Section::make('Initial Employment')
                                    ->schema([
                                        Components\TextEntry::make('employmentHistory.initial_department')
                                            ->label('Initial Department'),
                                        Components\TextEntry::make('employmentHistory.initial_role')
                                            ->label('Initial Role'),
                                        Components\TextEntry::make('employmentHistory.initial_grade')
                                            ->label('Initial Grade'),
                                        Components\TextEntry::make('employmentHistory.reporting_manager')
                                            ->label('Initial Manager'),
                                        Components\TextEntry::make('employmentHistory.initial_salary')
                                            ->label('Initial Salary')
                                            ->money('USD'),
                                    ])->columns(3),
                            ]),

                        Components\Tabs\Tab::make('Compensation')
                            ->schema([
                                Components\Section::make('Compensation History')
                                    ->schema([
                                        Components\RepeatableEntry::make('compensationHistory')
                                            ->hiddenLabel()
                                            ->schema([
                                                Components\TextEntry::make('effective_date')
                                                    ->label('Date')
                                                    ->date(),
                                                Components\TextEntry::make('action_type')
                                                    ->label('Action')
                                                    ->badge(),
                                                Components\TextEntry::make('new_salary')
                                                    ->label('New Salary')
                                                    ->money('USD'),
                                                Components\TextEntry::make('bonus_amount')
                                                    ->label('Bonus')
                                                    ->money('USD'),
                                                Components\TextEntry::make('remarks')
                                                    ->label('Remarks'),
                                            ])
                                            ->columns(3),
                                    ]),
                            ]),

                        Components\Tabs\Tab::make('Performance')
                            ->schema([
                                Components\Section::make('Performance Overview')
                                    ->schema([
                                        Components\Grid::make(4)
                                            ->schema([
                                                Components\TextEntry::make('latest_performance_rating')
                                                    ->label('Latest Rating')
                                                    ->getStateUsing(function ($record) {
                                                        return $record->performanceReviews()
                                                            ->latest('review_date')
                                                            ->first()?->overall_rating ?? 'N/A';
                                                    })
                                                    ->badge()
                                                    ->color('success'),
                                                Components\TextEntry::make('goal_completion')
                                                    ->label('Goal Completion')
                                                    ->getStateUsing(function ($record) {
                                                        return $record->performanceReviews()
                                                            ->latest('review_date')
                                                            ->first()?->goal_completion_rate . '%' ?? 'N/A';
                                                    })
                                                    ->badge()
                                                    ->color('primary'),
                                                Components\TextEntry::make('tenure_months')
                                                    ->label('Tenure (Months)')
                                                    ->getStateUsing(fn ($record) => $record->getTenureInMonths()),
                                                Components\TextEntry::make('total_reviews')
                                                    ->label('Total Reviews')
                                                    ->getStateUsing(fn ($record) => $record->performanceReviews()->count()),
                                            ]),
                                    ]),
                            ]),

                        Components\Tabs\Tab::make('Leave & Attendance')
                            ->schema([
                                Components\Section::make('Current Year Leave Summary')
                                    ->schema([
                                        Components\Grid::make(4)
                                            ->schema([
                                                Components\TextEntry::make('annual_leave_remaining')
                                                    ->label('Annual Leave Remaining')
                                                    ->getStateUsing(function ($record) {
                                                        $currentYear = $record->leaveAttendance()
                                                            ->where('year', now()->year)
                                                            ->first();
                                                        return $currentYear
                                                            ? ($currentYear->annual_leave_quota - $currentYear->annual_leave_used)
                                                            : 'N/A';
                                                    })
                                                    ->badge()
                                                    ->color('success'),
                                                Components\TextEntry::make('sick_leave_used')
                                                    ->label('Sick Leave Used')
                                                    ->getStateUsing(function ($record) {
                                                        return $record->leaveAttendance()
                                                            ->where('year', now()->year)
                                                            ->first()?->sick_leave_used ?? 0;
                                                    }),
                                                Components\TextEntry::make('casual_leave_used')
                                                    ->label('Casual Leave Used')
                                                    ->getStateUsing(function ($record) {
                                                        return $record->leaveAttendance()
                                                            ->where('year', now()->year)
                                                            ->first()?->casual_leave_used ?? 0;
                                                    }),
                                                Components\TextEntry::make('avg_login_hours')
                                                    ->label('Avg Daily Hours')
                                                    ->getStateUsing(function ($record) {
                                                        return $record->leaveAttendance()
                                                            ->where('year', now()->year)
                                                            ->first()?->average_login_hours ?? 'N/A';
                                                    }),
                                            ]),
                                    ]),
                            ]),

                        Components\Tabs\Tab::make('Benefits & Assets')
                            ->schema([
                                Components\Section::make('Benefits Overview')
                                    ->schema([
                                        Components\Grid::make(3)
                                            ->schema([
                                                Components\TextEntry::make('internet_allowance')
                                                    ->label('Internet Allowance')
                                                    ->getStateUsing(function ($record) {
                                                        return '$' . ($record->benefitsAllowances()
                                                                ->where('year', now()->year)
                                                                ->first()?->internet_allowance ?? 0);
                                                    }),
                                                Components\TextEntry::make('medical_allowance')
                                                    ->label('Medical Allowance')
                                                    ->getStateUsing(function ($record) {
                                                        return '$' . ($record->benefitsAllowances()
                                                                ->where('year', now()->year)
                                                                ->first()?->medical_allowance ?? 0);
                                                    }),
                                                Components\TextEntry::make('home_office_setup')
                                                    ->label('Home Office Setup')
                                                    ->getStateUsing(function ($record) {
                                                        $benefit = $record->benefitsAllowances()
                                                            ->where('year', now()->year)
                                                            ->first();
                                                        return $benefit?->home_office_setup_claimed
                                                            ? 'Claimed ($' . $benefit->home_office_setup . ')'
                                                            : 'Available ($' . ($benefit?->home_office_setup ?? 1000) . ')';
                                                    })
                                                    ->badge()
                                                    ->color(function ($record) {
                                                        return $record->benefitsAllowances()
                                                            ->where('year', now()->year)
                                                            ->first()?->home_office_setup_claimed
                                                            ? 'success' : 'warning';
                                                    }),
                                            ]),
                                    ]),
                                Components\Section::make('Assigned Assets')
                                    ->schema([
                                        Components\RepeatableEntry::make('assetManagement')
                                            ->hiddenLabel()
                                            ->schema([
                                                Components\TextEntry::make('asset_type')
                                                    ->label('Asset Type')
                                                    ->badge(),
                                                Components\TextEntry::make('asset_name')
                                                    ->label('Asset Name'),
                                                Components\TextEntry::make('serial_number')
                                                    ->label('Serial Number'),
                                                Components\TextEntry::make('issued_date')
                                                    ->label('Issued Date')
                                                    ->date(),
                                                Components\TextEntry::make('status')
                                                    ->label('Status')
                                                    ->badge(),
                                            ])
                                            ->columns(3),
                                    ]),
                            ]),

                        Components\Tabs\Tab::make('Health & Insurance')
                            ->schema([
                                Components\Section::make('Insurance Details')
                                    ->schema([
                                        Components\RepeatableEntry::make('healthInsurance')
                                            ->hiddenLabel()
                                            ->schema([
                                                Components\TextEntry::make('provider_name')
                                                    ->label('Provider'),
                                                Components\TextEntry::make('policy_number')
                                                    ->label('Policy Number'),
                                                Components\TextEntry::make('policy_start_date')
                                                    ->label('Start Date')
                                                    ->date(),
                                                Components\TextEntry::make('policy_end_date')
                                                    ->label('End Date')
                                                    ->date(),
                                                Components\TextEntry::make('annual_premium')
                                                    ->label('Annual Premium')
                                                    ->money('USD'),
                                                Components\TextEntry::make('annual_checkup_used')
                                                    ->label('Annual Checkup')
                                                    ->formatStateUsing(fn ($state) => $state ? 'Used' : 'Available')
                                                    ->badge()
                                                    ->color(fn ($state) => $state ? 'success' : 'warning'),
                                            ])
                                            ->columns(3),
                                    ]),
                            ]),

                        Components\Tabs\Tab::make('Documents & Compliance')
                            ->schema([
                                Components\Section::make('Document Status')
                                    ->schema([
                                        Components\RepeatableEntry::make('complianceDocuments')
                                            ->hiddenLabel()
                                            ->schema([
                                                Components\TextEntry::make('document_type')
                                                    ->label('Document Type')
                                                    ->badge(),
                                                Components\TextEntry::make('document_name')
                                                    ->label('Document Name'),
                                                Components\TextEntry::make('submission_date')
                                                    ->label('Submitted')
                                                    ->date(),
                                                Components\TextEntry::make('status')
                                                    ->label('Status')
                                                    ->badge()
                                                    ->color(fn ($state) => match ($state) {
                                                        'verified' => 'success',
                                                        'pending' => 'warning',
                                                        'rejected' => 'danger',
                                                        default => 'gray',
                                                    }),
                                                Components\TextEntry::make('verified_by')
                                                    ->label('Verified By'),
                                            ])
                                            ->columns(3),
                                    ]),
                            ]),
                    ]),
            ])->columns(1);
    }
}
