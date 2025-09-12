<?php
// app/Filament/Resources/EmployeeResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Carbon\Carbon;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Employee Information')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Personal Information')
                            ->schema([
                                Forms\Components\Section::make('Basic Details')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('employee_id')
                                                    ->label('Employee ID')
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(50)
                                                    ->helperText('Automatically generated'),
                                                Forms\Components\TextInput::make('full_name')
                                                    ->label('Full Name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->rules(['regex:/^[a-zA-Z\s]+$/'])
                                                    ->helperText('Only letters and spaces allowed'),
                                                Forms\Components\TextInput::make('password')
                                                    ->label('Password')
                                                    ->password()
                                                    ->required(fn($livewire) => $livewire instanceof Pages\CreateEmployee)
                                                    ->dehydrated(fn($state, $record) => filled($state) || is_null($record))
                                                    ->placeholder(fn($record) => $record?->password ? '••••••••' : 'Enter password')
                                                    ->minLength(8)
                                                    ->helperText('Minimum 8 characters'),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('email')
                                                    ->label('Email')
                                                    ->email()
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(255),
                                                Forms\Components\Select::make('status')
                                                    ->label('Status')
                                                    ->options([
                                                        'active' => 'Active',
                                                        'inactive' => 'Inactive',
                                                        'terminated' => 'Terminated',
                                                        'on_leave' => 'On Leave',
                                                    ])
                                                    ->default('active')
                                                    ->required(),
                                            ]),
                                        Forms\Components\FileUpload::make('profile_photo')
                                            ->label('Profile Photo')
                                            ->image()
                                            ->imageEditor()
                                            ->circleCropper()
                                            ->directory('employee-photos')
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->helperText('JPEG, PNG or WebP only, max 2MB'),
                                    ])
                                    ->columns(1),

                                Forms\Components\Section::make('Personal Details')
                                    ->relationship('personalInfo')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('phone_number')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->maxLength(20),
                                                Forms\Components\TextInput::make('personal_email')
                                                    ->label('Personal Email')
                                                    ->email()
                                                    ->maxLength(255)
                                                    ->different('email')
                                                    ->helperText('Must be different from work email'),
                                                Forms\Components\DatePicker::make('date_of_birth')
                                                    ->label('Date of Birth')
                                                    ->maxDate(now()->subYears(16))
                                                    ->minDate(now()->subYears(40))
                                                    ->native(false)
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                                        if ($state) {
                                                            $birthDate = Carbon::parse($state);
                                                            $age = $birthDate->age;
                                                            $ageString = $age . " Years";
                                                            $set('age', $ageString);
                                                        }
                                                    }),
                                            ]),
                                        Forms\Components\Grid::make(4)
                                            ->schema([
                                                Forms\Components\TextInput::make('age')
                                                    ->label('Age')
                                                    ->disabled()
                                                    ->dehydrated(),
                                                Forms\Components\Select::make('gender')
                                                    ->label('Gender')
                                                    ->options([
                                                        'male' => 'Male',
                                                        'female' => 'Female',
                                                        'other' => 'Other',
                                                        'prefer_not_to_say' => 'Prefer not to say'
                                                    ]),
                                                Forms\Components\Select::make('marital_status')
                                                    ->label('Marital Status')
                                                    ->options([
                                                        'single' => 'Single',
                                                        'married' => 'Married',
                                                        'divorced' => 'Divorced',
                                                        'widowed' => 'Widowed',
                                                    ]),
                                                Forms\Components\TextInput::make('national_id')
                                                    ->label('National ID')
                                                    ->maxLength(50)
                                                    ->alphaDash()
                                                    ->unique(ignoreRecord: true),
                                            ]),
                                        Forms\Components\TextInput::make('passport_number')
                                            ->label('Passport Number')
                                            ->maxLength(50)
                                            ->alphaDash(),
                                    ]),

                                Forms\Components\Section::make('Address Information')
                                    ->relationship('personalInfo')
                                    ->schema([
                                        Forms\Components\Textarea::make('residential_address')
                                            ->label('Residential Address')
                                            ->rows(3)
                                            ->required()
                                            ->maxLength(500)
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('city')
                                                    ->label('City')
                                                    ->maxLength(100)
                                                    ->required()
                                                    ->alpha(),
                                                Forms\Components\TextInput::make('state')
                                                    ->label('State')
                                                    ->maxLength(100)
                                                    ->alpha(),
                                                Forms\Components\TextInput::make('postal_code')
                                                    ->label('Postal Code')
                                                    ->maxLength(20)
                                                    ->numeric(),
                                            ]),
                                        Forms\Components\TextInput::make('country')
                                            ->label('Country')
                                            ->maxLength(100)
                                            ->default('Pakistan'),
                                    ]),

                                Forms\Components\Section::make('Emergency Contact')
                                    ->relationship('personalInfo')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('emergency_contact_name')
                                                    ->label('Emergency Contact Name')
                                                    ->maxLength(255)
                                                    ->rules(['regex:/^[a-zA-Z\s]+$/']),
                                                Forms\Components\TextInput::make('emergency_contact_relationship')
                                                    ->label('Relationship')
                                                    ->maxLength(100),
                                                Forms\Components\TextInput::make('emergency_contact_phone')
                                                    ->label('Emergency Contact Phone')
                                                    ->tel()
                                                    ->maxLength(20),
                                            ]),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Employment')
                            ->schema([
                                Forms\Components\Section::make('Employment Details')
                                    ->relationship('employmentHistory')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\DatePicker::make('joining_date')
                                                    ->label('Joining Date')
                                                    ->required()
                                                    ->native(false)
                                                    ->maxDate(now())
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                                        if ($state) {
                                                            // Auto-set probation end date to 6 months after joining
                                                            $probationEnd = Carbon::parse($state)->addMonths(6);
                                                            $set('probation_end_date', $probationEnd->format('Y-m-d'));
                                                        }
                                                    }),
                                                Forms\Components\DatePicker::make('probation_end_date')
                                                    ->label('Probation End Date')
                                                    ->native(false)
                                                    ->after('joining_date')
                                                    ->helperText('Automatically set to 6 months after joining date'),
                                                Forms\Components\Select::make('employment_type')
                                                    ->label('Employment Type')
                                                    ->options([
                                                        'full_time' => 'Full Time',
                                                        'part_time' => 'Part Time',
                                                        'contract' => 'Contract',
                                                        'intern' => 'Intern',
                                                        'consultant' => 'Consultant',
                                                    ])
                                                    ->required(),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Current Position')
                                    ->relationship('employmentHistory')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('current_department')
                                                    ->label('Department')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('current_role')
                                                    ->label('Role')
                                                    ->required()
                                                    ->maxLength(255),
                                            ]),
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('current_grade')
                                                    ->label('Grade')
                                                    ->maxLength(50),
                                                Forms\Components\TextInput::make('current_manager')
                                                    ->label('Current Manager')
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('current_salary')
                                                    ->label('Current Salary')
                                                    ->numeric()
                                                    ->prefix('PKR')
                                                    ->step(1)
                                                    ->minValue(0)
                                                    ->helperText('Enter amount without decimals'),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Initial Position')
                                    ->relationship('employmentHistory')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('initial_department')
                                                    ->label('Initial Department')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('initial_role')
                                                    ->label('Initial Role')
                                                    ->maxLength(255),
                                            ]),
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('initial_grade')
                                                    ->label('Initial Grade')
                                                    ->maxLength(50),
                                                Forms\Components\TextInput::make('reporting_manager')
                                                    ->label('Initial Manager')
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('initial_salary')
                                                    ->label('Initial Salary')
                                                    ->numeric()
                                                    ->prefix('PKR')
                                                    ->step(1)
                                                    ->minValue(0)
                                                    ->helperText('Enter amount without decimals'),
                                            ]),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Performance Reviews')
                            ->schema([
                                Forms\Components\Section::make('Performance Review History')
                                    ->schema([
                                        Forms\Components\Repeater::make('performance_reviews')
                                            ->relationship('performanceReviews')
                                            ->label('Performance Reviews')
                                            ->schema([
                                                Forms\Components\Section::make('Review Information')
                                                    ->schema([
                                                        Forms\Components\Grid::make(4)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('review_period')
                                                                    ->required()
                                                                    ->placeholder('e.g., Q1 2024, Annual 2024')
                                                                    ->helperText('Specify the review period (quarterly, annual, etc.)'),
                                                                Forms\Components\DatePicker::make('review_date')
                                                                    ->required()
                                                                    ->default(now())
                                                                    ->maxDate(now())
                                                                    ->native(false),
                                                                Forms\Components\TextInput::make('reviewed_by')
                                                                    ->required()
                                                                    ->default('Direct Manager')
                                                                    ->placeholder('Name of the reviewer'),
                                                                Forms\Components\Select::make('status')
                                                                    ->options([
                                                                        'draft' => 'Draft',
                                                                        'submitted' => 'Submitted',
                                                                        'approved' => 'Approved',
                                                                        'completed' => 'Completed',
                                                                    ])
                                                                    ->default('draft')
                                                                    ->required(),
                                                            ]),
                                                    ]),

                                                Forms\Components\Section::make('Performance Metrics')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('goal_completion_rate')
                                                                    ->label('Goal Completion Rate (%)')
                                                                    ->numeric()
                                                                    ->suffix('%')
                                                                    ->minValue(0)
                                                                    ->maxValue(100)
                                                                    ->helperText('Percentage of goals achieved during this period'),
                                                                Forms\Components\Select::make('overall_rating')
                                                                    ->label('Overall Rating')
                                                                    ->options([
                                                                        '5.0' => '5.0 - Outstanding',
                                                                        '4.5' => '4.5 - Exceeds Expectations',
                                                                        '4.0' => '4.0 - Meets Expectations+',
                                                                        '3.5' => '3.5 - Meets Expectations',
                                                                        '3.0' => '3.0 - Partially Meets',
                                                                        '2.5' => '2.5 - Below Expectations',
                                                                        '2.0' => '2.0 - Unsatisfactory',
                                                                    ])
                                                                    ->helperText('Select the overall performance rating'),
                                                            ]),
                                                    ]),

                                                Forms\Components\Section::make('Detailed Feedback')
                                                    ->schema([
                                                        Forms\Components\Textarea::make('self_assessment')
                                                            ->label('Employee Self Assessment')
                                                            ->rows(3)
                                                            ->maxLength(1000)
                                                            ->placeholder('Employee self-evaluation of their performance during this period'),

                                                        Forms\Components\Textarea::make('manager_feedback')
                                                            ->label('Manager Feedback')
                                                            ->rows(3)
                                                            ->maxLength(1000)
                                                            ->placeholder('Detailed manager evaluation and feedback'),

                                                        Forms\Components\Textarea::make('peer_feedback')
                                                            ->label('Peer Feedback')
                                                            ->rows(3)
                                                            ->maxLength(1000)
                                                            ->placeholder('Feedback from colleagues and team members'),

                                                        Forms\Components\Textarea::make('areas_of_strength')
                                                            ->label('Areas of Strength')
                                                            ->rows(2)
                                                            ->maxLength(500)
                                                            ->placeholder('Key strengths demonstrated during this period'),

                                                        Forms\Components\Textarea::make('areas_for_improvement')
                                                            ->label('Areas for Improvement')
                                                            ->rows(2)
                                                            ->maxLength(500)
                                                            ->placeholder('Areas where employee can improve'),

                                                        Forms\Components\Textarea::make('development_goals')
                                                            ->label('Development Goals for Next Period')
                                                            ->rows(3)
                                                            ->maxLength(1000)
                                                            ->placeholder('Specific goals and development objectives for the upcoming period'),
                                                    ])
                                                    ->columns(1),

                                                Forms\Components\Section::make('Additional Information')
                                                    ->schema([
                                                        Forms\Components\KeyValue::make('key_achievements')
                                                            ->label('Key Achievements')
                                                            ->keyLabel('Achievement')
                                                            ->valueLabel('Details')
                                                            ->addActionLabel('Add Achievement'),

                                                        Forms\Components\TagsInput::make('skills_demonstrated')
                                                            ->label('Skills Demonstrated')
                                                            ->placeholder('Enter skills and press Enter'),

                                                        Forms\Components\FileUpload::make('supporting_documents')
                                                            ->label('Supporting Documents')
                                                            ->multiple()
                                                            ->directory('performance-reviews')
                                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                                            ->maxSize(5120)
                                                            ->helperText('Upload supporting documents (PDF, images, Word docs only, max 5MB each)'),
                                                    ])
                                                    ->columns(1)
                                                    ->collapsible()
                                                    ->collapsed(),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string =>
                                                ($state['review_period'] ?? 'New Review') .
                                                (isset($state['overall_rating']) ? ' - Rating: ' . $state['overall_rating'] : '')
                                            )
                                            ->addActionLabel('Add Performance Review')
                                            ->reorderable(false)
                                            ->defaultItems(0),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Compensation History')
                            ->schema([
                                Forms\Components\Section::make('Compensation Records')
                                    ->schema([
                                        Forms\Components\Repeater::make('compensationHistory')
                                            ->label('Compensation History')
                                            ->relationship('compensationHistory', function ($query) {
                                                return $query->orderBy('effective_date', 'desc');
                                            })
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\DatePicker::make('effective_date')
                                                            ->label('Effective Date')
                                                            ->native(false)
                                                            ->required()
                                                            ->maxDate(now()),
                                                        Forms\Components\Select::make('action_type')
                                                            ->label('Action Type')
                                                            ->options([
                                                                'increment' => 'Salary Increase',
                                                                'promotion' => 'Promotion',
                                                                'bonus' => 'Bonus',
                                                                'adjustment' => 'Adjustment',
                                                                'demotion' => 'Demotion',
                                                            ])
                                                            ->required()
                                                            ->live(),
                                                        Forms\Components\TextInput::make('value')
                                                            ->label(function (callable $get) {
                                                                return match($get('action_type')) {
                                                                    'increment', 'promotion' => 'New Salary',
                                                                    'bonus' => 'Bonus Amount',
                                                                    'adjustment' => 'Adjustment Amount',
                                                                    'demotion' => 'New Salary',
                                                                    default => 'Value'
                                                                };
                                                            })
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->step(1)
                                                            ->minValue(0)
                                                            ->required(),
                                                    ]),
                                                Forms\Components\Textarea::make('remarks')
                                                    ->label('Remarks')
                                                    ->rows(2)
                                                    ->maxLength(500)
                                                    ->placeholder('Reason for this compensation change')
                                                    ->columnSpanFull(),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string =>
                                                ($state['action_type'] ?? 'New Record') .
                                                (isset($state['effective_date']) ? ' - ' . Carbon::parse($state['effective_date'])->format('M Y') : '')
                                            )
                                            ->addActionLabel('Add Compensation Record')
                                            ->reorderable(false)
                                            ->defaultItems(0)
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                if (isset($data['value']) && isset($data['action_type'])) {
                                                    switch ($data['action_type']) {
                                                        case 'increment':
                                                            $data['increment'] = $data['value'];
                                                            break;
                                                        case 'promotion':
                                                        case 'demotion':
                                                            $data['new_salary'] = $data['value'];
                                                            break;
                                                        case 'bonus':
                                                            $data['bonus_amount'] = $data['value'];
                                                            break;
                                                        case 'adjustment':
                                                            $data['adjustment_amount'] = $data['value'];
                                                            break;
                                                    }
                                                    unset($data['value']);
                                                }
                                                return $data;
                                            })
                                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                                if (isset($data['value']) && isset($data['action_type'])) {
                                                    switch ($data['action_type']) {
                                                        case 'increment':
                                                            $data['increment'] = $data['value'];
                                                            break;
                                                        case 'promotion':
                                                        case 'demotion':
                                                            $data['new_salary'] = $data['value'];
                                                            break;
                                                        case 'bonus':
                                                            $data['bonus_amount'] = $data['value'];
                                                            break;
                                                        case 'adjustment':
                                                            $data['adjustment_amount'] = $data['value'];
                                                            break;
                                                    }
                                                    unset($data['value']);
                                                }
                                                return $data;
                                            })
                                            ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                                                if (isset($data['action_type'])) {
                                                    switch ($data['action_type']) {
                                                        case 'increment':
                                                            $data['value'] = $data['increment'] ?? null;
                                                            break;
                                                        case 'promotion':
                                                        case 'demotion':
                                                            $data['value'] = $data['new_salary'] ?? null;
                                                            break;
                                                        case 'bonus':
                                                            $data['value'] = $data['bonus_amount'] ?? null;
                                                            break;
                                                        case 'adjustment':
                                                            $data['value'] = $data['adjustment_amount'] ?? null;
                                                            break;
                                                    }
                                                }
                                                return $data;
                                            }),
                                    ]),
                            ]),

                        // Leave this tab as requested - TODO Fix this
                        Forms\Components\Tabs\Tab::make('Leave & Attendance')
                            ->schema([
                                Forms\Components\Section::make('Annual Leave Settings')
                                    ->schema([
                                        Forms\Components\Repeater::make('leaveAttendance')
                                            ->label('Leave & Attendance Records')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\Grid::make(4)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('year')
                                                            ->label('Year')
                                                            ->numeric()
                                                            ->default(now()->year)
                                                            ->required(),
                                                        Forms\Components\TextInput::make('annual_leave_quota')
                                                            ->label('Annual Leave Quota')
                                                            ->numeric()
                                                            ->default(25),
                                                        Forms\Components\TextInput::make('annual_leave_used')
                                                            ->label('Annual Leave Used')
                                                            ->numeric()
                                                            ->default(0),
                                                        Forms\Components\TextInput::make('sick_leave_used')
                                                            ->label('Sick Leave Used')
                                                            ->numeric()
                                                            ->default(0),
                                                    ]),
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('casual_leave_used')
                                                            ->label('Casual Leave Used')
                                                            ->numeric()
                                                            ->default(0),
                                                        Forms\Components\TextInput::make('average_login_hours')
                                                            ->label('Average Login Hours')
                                                            ->numeric()
                                                            ->step(0.1),
                                                        Forms\Components\TextInput::make('overtime_hours')
                                                            ->label('Overtime Hours')
                                                            ->numeric()
                                                            ->step(0.1)
                                                            ->default(0),
                                                    ]),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string => 'Year ' . ($state['year'] ?? ''))
                                            ->addActionLabel('Add Year Record')
                                            ->defaultItems(1),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Benefits & Allowances')
                            ->schema([
                                Forms\Components\Section::make('Annual Benefits')
                                    ->schema([
                                        Forms\Components\Repeater::make('benefitsAllowances')
                                            ->label('Benefits & Allowances')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\DatePicker::make('effective_from')
                                                            ->label('Effective From')
                                                            ->native(false)
                                                            ->required()
                                                            ->default(now()->startOfYear()),
                                                        Forms\Components\DatePicker::make('effective_to')
                                                            ->label('Effective To')
                                                            ->native(false)
                                                            ->after('effective_from')
                                                            ->default(now()->endOfYear()),
                                                    ]),
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('internet_allowance')
                                                            ->label('Internet Allowance (Monthly)')
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->step(1)
                                                            ->minValue(0)
                                                            ->default(0),
                                                        Forms\Components\TextInput::make('medical_allowance')
                                                            ->label('Medical Allowance (Annual)')
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->step(1)
                                                            ->minValue(0)
                                                            ->default(0),
                                                        Forms\Components\TextInput::make('home_office_setup')
                                                            ->label('Home Office Setup Budget')
                                                            ->numeric()
                                                            ->prefix('USD')
                                                            ->step(1)
                                                            ->minValue(0)
                                                            ->default(1000),
                                                    ]),
                                                Forms\Components\Toggle::make('home_office_setup_claimed')
                                                    ->label('Home Office Setup Claimed')
                                                    ->default(false),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string =>
                                                'Benefits: ' .
                                                (isset($state['effective_from']) ? Carbon::parse($state['effective_from'])->format('M Y') : 'New') .
                                                (isset($state['effective_to']) ? ' - ' . Carbon::parse($state['effective_to'])->format('M Y') : '')
                                            )
                                            ->addActionLabel('Add Benefits Record')
                                            ->reorderable(false)
                                            ->defaultItems(1),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Asset Management')
                            ->schema([
                                Forms\Components\Section::make('Assigned Assets')
                                    ->schema([
                                        Forms\Components\Repeater::make('assetManagement')
                                            ->label('Assets')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\Select::make('asset_type')
                                                            ->label('Asset Type')
                                                            ->options([
                                                                'laptop' => 'Laptop',
                                                                'desktop' => 'Desktop',
                                                                'mobile' => 'Mobile Phone',
                                                                'monitor' => 'Monitor',
                                                                'keyboard' => 'Keyboard',
                                                                'mouse' => 'Mouse',
                                                                'headset' => 'Headset',
                                                                'tablet' => 'Tablet',
                                                                'webcam' => 'Webcam',
                                                                'docking_station' => 'Docking Station',
                                                                'other' => 'Other',
                                                            ])
                                                            ->required()
                                                            ->searchable(),
                                                        Forms\Components\TextInput::make('asset_name')
                                                            ->label('Asset Name')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->placeholder('e.g., MacBook Pro 16"'),
                                                        Forms\Components\TextInput::make('serial_number')
                                                            ->label('Serial Number')
                                                            ->maxLength(255)
                                                            ->alphaDash()
                                                            ->unique(ignoreRecord: true),
                                                    ]),
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\DatePicker::make('issued_date')
                                                            ->label('Issued Date')
                                                            ->native(false)
                                                            ->required()
                                                            ->default(now())
                                                            ->maxDate(now()),
                                                        Forms\Components\DatePicker::make('return_date')
                                                            ->label('Return Date')
                                                            ->native(false)
                                                            ->after('issued_date')
                                                            ->hidden(fn (callable $get) => $get('status') === 'issued'),
                                                        Forms\Components\Select::make('status')
                                                            ->label('Status')
                                                            ->options([
                                                                'issued' => 'Issued',        // Changed from 'assigned' => 'Assigned'
                                                                'returned' => 'Returned',
                                                                'damaged' => 'Damaged',
                                                                'lost' => 'Lost',
                                                                // Remove 'under_repair' => 'Under Repair'
                                                            ])
                                                            ->default('issued')
                                                            ->required()
                                                            ->live(),
                                                    ]),
                                                Forms\Components\Textarea::make('notes')
                                                    ->label('Notes')
                                                    ->rows(2)
                                                    ->maxLength(500)
                                                    ->placeholder('Any additional notes about this asset')
                                                    ->columnSpanFull(),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string =>
                                                ($state['asset_type'] ?? 'Asset') . ' - ' .
                                                ($state['asset_name'] ?? 'New Asset') .
                                                (isset($state['status']) ? ' [' . ($state['status'] === 'issued' ? 'Issued' : ucfirst($state['status'])) . ']' : '')
                                            )
                                            ->addActionLabel('Add Asset')
                                            ->reorderable(false)
                                            ->defaultItems(0),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Health Insurance')
                            ->schema([
                                Forms\Components\Section::make('Insurance Policies')
                                    ->schema([
                                        Forms\Components\Repeater::make('healthInsurance')
                                            ->label('Health Insurance')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('provider_name')
                                                            ->label('Insurance Provider')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->placeholder('e.g., Jubilee Health Insurance'),
                                                        Forms\Components\TextInput::make('policy_number')
                                                            ->label('Policy Number')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->alphaDash()
                                                            ->unique(ignoreRecord: true),
                                                        Forms\Components\TextInput::make('annual_premium')
                                                            ->label('Annual Premium')
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->step(1)
                                                            ->minValue(0),
                                                    ]),
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\DatePicker::make('policy_start_date')
                                                            ->label('Policy Start Date')
                                                            ->native(false)
                                                            ->required()
                                                            ->live()
                                                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                                                if ($state) {
                                                                    // Auto-set end date to one year later
                                                                    $endDate = Carbon::parse($state)->addYear()->subDay();
                                                                    $set('policy_end_date', $endDate->format('Y-m-d'));
                                                                }
                                                            }),
                                                        Forms\Components\DatePicker::make('policy_end_date')
                                                            ->label('Policy End Date')
                                                            ->native(false)
                                                            ->required()
                                                            ->after('policy_start_date')
                                                            ->helperText('Automatically set to one year after start date'),
                                                        Forms\Components\Toggle::make('annual_checkup_used')
                                                            ->label('Annual Checkup Used')
                                                            ->default(false),
                                                    ]),
                                                Forms\Components\Textarea::make('coverage_details')
                                                    ->label('Coverage Details')
                                                    ->rows(2)
                                                    ->maxLength(500)
                                                    ->placeholder('Brief description of coverage benefits')
                                                    ->columnSpanFull(),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string =>
                                                ($state['provider_name'] ?? 'Insurance Policy') .
                                                (isset($state['policy_start_date']) ? ' - ' . Carbon::parse($state['policy_start_date'])->format('Y') : '')
                                            )
                                            ->addActionLabel('Add Insurance Policy')
                                            ->reorderable(false)
                                            ->defaultItems(0),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Documents')
                            ->schema([
                                Forms\Components\Section::make('Compliance Documents')
                                    ->schema([
                                        Forms\Components\Repeater::make('complianceDocuments')
                                            ->label('Documents')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\Select::make('document_type')
                                                            ->label('Document Type')
                                                            ->options([
                                                                'passport' => 'Passport',
                                                                'national_id' => 'National ID',
                                                                'driver_license' => 'Driver License',
                                                                'degree_certificate' => 'Degree Certificate',
                                                                'experience_letter' => 'Experience Letter',
                                                                'medical_certificate' => 'Medical Certificate',
                                                                'bank_statement' => 'Bank Statement',
                                                                'contract' => 'Employment Contract',
                                                                'offer_letter' => 'Offer Letter',
                                                                'resignation_letter' => 'Resignation Letter',
                                                                'other' => 'Other',
                                                            ])
                                                            ->required()
                                                            ->searchable(),
                                                        Forms\Components\TextInput::make('document_name')
                                                            ->label('Document Name')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->placeholder('Descriptive name for this document'),
                                                        Forms\Components\DatePicker::make('submission_date')
                                                            ->label('Submission Date')
                                                            ->native(false)
                                                            ->required()
                                                            ->default(now())
                                                            ->maxDate(now()),
                                                    ]),
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\Select::make('status')
                                                            ->label('Verification Status')
                                                            ->options([
                                                                'pending' => 'Pending',
                                                                'verified' => 'Verified',
                                                                'rejected' => 'Rejected',
                                                                'expired' => 'Expired',
                                                            ])
                                                            ->default('pending')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('verified_by')
                                                            ->label('Verified By')
                                                            ->maxLength(255)
                                                            ->visible(fn (callable $get) => $get('status') === 'verified'),
                                                        Forms\Components\DatePicker::make('expiry_date')
                                                            ->label('Expiry Date')
                                                            ->native(false)
                                                            ->after('submission_date')
                                                            ->helperText('If applicable (e.g., passport, license)'),
                                                    ]),
                                                Forms\Components\FileUpload::make('document_file')
                                                    ->label('Document File')
                                                    ->directory('employee-documents')
                                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                                                    ->maxSize(5120)
                                                    ->helperText('PDF, JPEG, PNG or WebP only, max 5MB')
                                                    ->columnSpanFull(),
                                                Forms\Components\Textarea::make('notes')
                                                    ->label('Notes')
                                                    ->rows(2)
                                                    ->maxLength(500)
                                                    ->placeholder('Additional notes about this document')
                                                    ->columnSpanFull(),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string =>
                                                ($state['document_type'] ?? 'Document') . ' - ' .
                                                ($state['document_name'] ?? 'New Document') .
                                                (isset($state['status']) ? ' [' . ucfirst($state['status']) . ']' : '')
                                            )
                                            ->addActionLabel('Add Document')
                                            ->reorderable(false)
                                            ->defaultItems(0),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl('/images/default-avatar.png')
                    ->size(40),
                Tables\Columns\TextColumn::make('employee_id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),
                Tables\Columns\TextColumn::make('employmentHistory.current_department')
                    ->label('Department')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('employmentHistory.current_role')
                    ->label('Role')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('employmentHistory.joining_date')
                    ->label('Joining Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'info' => 'on_leave',
                        'danger' => 'terminated',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'active',
                        'heroicon-o-clock' => 'inactive',
                        'heroicon-o-calendar' => 'on_leave',
                        'heroicon-o-x-circle' => 'terminated',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'terminated' => 'Terminated',
                        'on_leave' => 'On Leave',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('current_department')
                    ->relationship('employmentHistory', 'current_department')
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('employment_type')
                    ->relationship('employmentHistory', 'employment_type')
                    ->options([
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                        'intern' => 'Intern',
                        'consultant' => 'Consultant',
                    ])
                    ->multiple(),
                Tables\Filters\Filter::make('joining_date')
                    ->form([
                        Forms\Components\DatePicker::make('joined_from')
                            ->label('Joined From')
                            ->native(false),
                        Forms\Components\DatePicker::make('joined_until')
                            ->label('Joined Until')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['joined_from'], fn ($q) => $q->whereHas('employmentHistory', fn ($q) => $q->where('joining_date', '>=', $data['joined_from'])))
                            ->when($data['joined_until'], fn ($q) => $q->whereHas('employmentHistory', fn ($q) => $q->where('joining_date', '<=', $data['joined_until'])));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['joined_from'] ?? null) {
                            $indicators['joined_from'] = 'Joined from ' . Carbon::parse($data['joined_from'])->toFormattedDateString();
                        }
                        if ($data['joined_until'] ?? null) {
                            $indicators['joined_until'] = 'Joined until ' . Carbon::parse($data['joined_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\Action::make('download_profile')
                        ->label('Download Profile')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($record) {
                            // Add your profile download logic here
                            return redirect()->route('employee.profile.download', $record);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Download Employee Profile')
                        ->modalDescription('Are you sure you want to download this employee\'s profile data?'),
                ])
                    ->label('Actions')
                    ->color('gray')
                    ->button()
                    ->size('sm'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Employees')
                        ->modalDescription('Are you sure you want to delete these employees? This action cannot be undone.'),
                    Tables\Actions\BulkAction::make('export_selected')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // Add your export logic here
                            return redirect()->route('employees.export', ['ids' => $records->pluck('id')]);
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Employee Overview')
                    ->schema([
                        Components\Split::make([
                            Components\Grid::make(2)
                                ->schema([
                                    Components\Group::make([
                                        Components\TextEntry::make('employee_id')
                                            ->label('Employee ID')
                                            ->badge()
                                            ->color('primary'),
                                        Components\TextEntry::make('full_name')
                                            ->label('Full Name')
                                            ->size(Components\TextEntry\TextEntrySize::Large)
                                            ->weight('bold'),
                                        Components\TextEntry::make('email')
                                            ->icon('heroicon-o-envelope')
                                            ->copyable(),
                                        Components\BadgeEntry::make('status')
                                            ->colors([
                                                'success' => 'active',
                                                'warning' => 'inactive',
                                                'info' => 'on_leave',
                                                'danger' => 'terminated',
                                            ]),
                                    ]),
                                    Components\Group::make([
                                        Components\ImageEntry::make('profile_photo')
                                            ->hiddenLabel()
                                            ->circular()
                                            ->defaultImageUrl('/images/default-avatar.png')
                                            ->size(120),
                                    ]),
                                ]),
                        ]),
                    ])
                    ->collapsible(),

                Components\Section::make('Employment Details')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('employmentHistory.joining_date')
                                    ->label('Joining Date')
                                    ->date('F j, Y')
                                    ->icon('heroicon-o-calendar'),
                                Components\TextEntry::make('employmentHistory.current_department')
                                    ->label('Department')
                                    ->badge()
                                    ->color('info'),
                                Components\TextEntry::make('employmentHistory.current_role')
                                    ->label('Current Role')
                                    ->weight('bold'),
                            ]),
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('employmentHistory.current_manager')
                                    ->label('Current Manager')
                                    ->icon('heroicon-o-user'),
                                Components\TextEntry::make('employmentHistory.employment_type')
                                    ->label('Employment Type')
                                    ->badge(),
                            ]),
                    ])
                    ->collapsible(),

                Components\Section::make('Personal Information')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('personalInfo.phone_number')
                                    ->label('Phone')
                                    ->icon('heroicon-o-phone')
                                    ->copyable(),
                                Components\TextEntry::make('personalInfo.date_of_birth')
                                    ->label('Date of Birth')
                                    ->date('F j, Y')
                                    ->icon('heroicon-o-cake'),
                                Components\TextEntry::make('personalInfo.age')
                                    ->label('Age'),
                            ]),
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('personalInfo.emergency_contact_name')
                                    ->label('Emergency Contact')
                                    ->icon('heroicon-o-exclamation-triangle'),
                                Components\TextEntry::make('personalInfo.emergency_contact_phone')
                                    ->label('Emergency Phone')
                                    ->copyable(),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Consider adding relation managers for complex data:
            // PerformanceReviewsRelationManager::class,
            // CompensationHistoryRelationManager::class,
            // AssetManagementRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'personalInfo',
                'employmentHistory',
                'performanceReviews' => fn ($query) => $query->latest('review_date')->limit(1),
                'compensationHistory' => fn ($query) => $query->latest('effective_date')->limit(1),
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['employee_id', 'full_name', 'email', 'employmentHistory.current_department', 'employmentHistory.current_role'];
    }
}
