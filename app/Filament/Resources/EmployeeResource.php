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
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(50),
                                                Forms\Components\TextInput::make('full_name')
                                                    ->label('Full Name')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('password')
                                                    ->label('Password')
                                                    ->password()
                                                    ->required(fn($livewire) => $livewire instanceof Pages\CreateEmployee)
                                                    ->dehydrated(fn($state, $record) => filled($state) || is_null($record))
                                                    ->placeholder(fn($record) => $record?->password ? '••••••••' : 'Enter password'),
                                                Forms\Components\TextInput::make('email')
                                                    ->label('Email')
                                                    ->email()
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(255),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\FileUpload::make('profile_photo')
                                                    ->label('Profile Photo')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->circleCropper()
                                                    ->directory('employee-photos')
                                                    ->maxSize(2048),
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
                                    ]),

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
                                                    ->maxLength(255),
                                                Forms\Components\DatePicker::make('date_of_birth')
                                                    ->label('Date of Birth')
                                                    ->maxDate(now()->subYears(16))
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                                        if ($state) {
                                                            $birthDate = \Carbon\Carbon::parse($state);
                                                            $now = now();

                                                            $years = $now->diffInYears($birthDate);

                                                            $ageString = ((int)$birthDate->age) . " Years";
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
                                                        'female' => 'Female'
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
                                                    ->maxLength(50),
                                            ]),
                                        Forms\Components\TextInput::make('passport_number')
                                            ->label('Passport Number')
                                            ->maxLength(50),
                                    ]),

                                Forms\Components\Section::make('Address Information')
                                    ->relationship('personalInfo')
                                    ->schema([
                                        Forms\Components\Textarea::make('residential_address')
                                            ->label('Residential Address')
                                            ->rows(3)
                                            ->required()
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('city')
                                                    ->label('City')
                                                    ->maxLength(100)
                                                    ->required(),
                                                Forms\Components\TextInput::make('state')
                                                    ->label('State')
                                                    ->maxLength(100),
                                                Forms\Components\TextInput::make('postal_code')
                                                    ->label('Postal Code')
                                                    ->maxLength(20),
                                            ]),
                                        Forms\Components\TextInput::make('country')
                                            ->label('Country')
                                            ->maxLength(100),
                                    ]),

                                Forms\Components\Section::make('Emergency Contact')
                                    ->relationship('personalInfo')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('emergency_contact_name')
                                                    ->label('Emergency Contact Name')
                                                    ->maxLength(255),
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
                                                    ->format('Y-m-d')
                                                    ->native(false)
                                                    ->maxDate(now()),
                                                Forms\Components\DatePicker::make('probation_end_date')
                                                    ->label('Probation End Date')
                                                    ->native(false)
                                                    ->after('joining_date')->format('Y-m-d'),
                                                Forms\Components\Select::make('employment_type')
                                                    ->label('Employment Type')
                                                    ->options([
                                                        'full_time' => 'Full Time',
                                                        'part_time' => 'Part Time',
                                                        'contract' => 'Contract',
                                                        'intern' => 'Intern',
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
                                                    ->step(0.01),
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
                                                    ->step(0.01),
                                            ]),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Performance Reviews')
                            ->schema([
                              Forms\Components\Repeater::make('performance_reviews')
                                  ->relationship('performanceReviews')
                                ->label('Performance Reviews')
                                ->schema([
                                    Forms\Components\Section::make('Review Information')
                                        ->schema([
                                            Forms\Components\Select::make('employee_id')
                                                ->label('Employee')
                                                ->relationship('employee', 'full_name')
                                                ->searchable()
                                                ->preload()
                                                ->required()
                                                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->employee_id} - {$record->full_name}"),
                                            Forms\Components\TextInput::make('review_period')
                                                ->required()
                                                ->placeholder('e.g., Q1 2024, Annual 2024')
                                                ->helperText('Specify the review period (quarterly, annual, etc.)'),
                                            Forms\Components\DatePicker::make('review_date')
                                                ->required()
                                                ->default(now())
                                                ->maxDate(now()),
                                            Forms\Components\TextInput::make('reviewed_by')
                                                ->required()
                                                ->default('Direct Manager')
                                                ->placeholder('Name of the reviewer'),
                                            Forms\Components\Select::make('status')
                                                ->options([
                                                    'draft' => 'Draft',
                                                    'submitted' => 'Submitted',
                                                    'approved' => 'Approved',
                                                ])
                                                ->default('draft')
                                                ->required(),
                                        ])->columns(2),

                                    Forms\Components\Section::make('Performance Metrics')
                                        ->schema([
                                            Forms\Components\TextInput::make('goal_completion_rate')
                                                ->label('Goal Completion Rate (%)')
                                                ->numeric()
                                                ->suffix('%')
                                                ->minValue(0)
                                                ->maxValue(100)
                                                ->required()
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
                                                ->required()
                                                ->helperText('Select the overall performance rating'),
                                        ])->columns(2),

                                    Forms\Components\Section::make('Detailed Feedback')
                                        ->schema([
                                            Forms\Components\Textarea::make('self_assessment')
                                                ->label('Employee Self Assessment')
                                                ->rows(4)
                                                ->placeholder('Employee self-evaluation of their performance during this period')
                                                ->helperText('What the employee thinks about their own performance'),

                                            Forms\Components\Textarea::make('manager_feedback')
                                                ->label('Manager Feedback')
                                                ->rows(4)
                                                ->placeholder('Detailed manager evaluation and feedback')
                                                ->helperText('Manager assessment of employee performance')
                                                ->required(),

                                            Forms\Components\Textarea::make('peer_feedback')
                                                ->label('Peer Feedback')
                                                ->rows(3)
                                                ->placeholder('Feedback from colleagues and team members')
                                                ->helperText('Input from peers who work closely with the employee'),

                                            Forms\Components\Textarea::make('areas_of_strength')
                                                ->label('Areas of Strength')
                                                ->rows(3)
                                                ->placeholder('Key strengths demonstrated during this period')
                                                ->helperText('What the employee does well'),

                                            Forms\Components\Textarea::make('areas_for_improvement')
                                                ->label('Areas for Improvement')
                                                ->rows(3)
                                                ->placeholder('Areas where employee can improve')
                                                ->helperText('Constructive feedback for development'),

                                            Forms\Components\Textarea::make('development_goals')
                                                ->label('Development Goals for Next Period')
                                                ->rows(4)
                                                ->placeholder('Specific goals and development objectives for the upcoming period')
                                                ->helperText('What should be focused on in the next review period'),
                                        ])->columns(1),

                                    Forms\Components\Section::make('Additional Information')
                                        ->schema([
                                            Forms\Components\KeyValue::make('key_achievements')
                                                ->label('Key Achievements')
                                                ->keyLabel('Achievement')
                                                ->valueLabel('Details')
                                                ->addActionLabel('Add Achievement')
                                                ->helperText('List major accomplishments during this period'),

                                            Forms\Components\TagsInput::make('skills_demonstrated')
                                                ->label('Skills Demonstrated')
                                                ->placeholder('Enter skills and press Enter')
                                                ->helperText('Technical and soft skills shown during this period'),

                                            Forms\Components\FileUpload::make('supporting_documents')
                                                ->label('Supporting Documents')
                                                ->multiple()
                                                ->directory('performance-reviews')
                                                ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword'])
                                                ->helperText('Upload any supporting documents (PDFs, images, etc.)'),
                                        ])->columns(1)
                                        ->collapsible()
                                        ->collapsed(),
                                ])

                            ]),



                        Forms\Components\Tabs\Tab::make('Compensation History')
                            ->schema([
                                Forms\Components\Section::make('Compensation Records')
                                    ->schema([
                                        Forms\Components\Repeater::make('compensationHistory')
                                            ->label('Compensation History')
                                            ->relationship('compensationHistory', function ($query) {
                                                return $query->orderBy('created_at', 'desc'); // Order by newest first
                                            })
                                            ->schema([
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\DatePicker::make('effective_date')
                                                            ->label('Effective Date')
                                                            ->native(false)
                                                            ->format('Y-m-d')
                                                            ->required(),
                                                        Forms\Components\Select::make('action_type')
                                                            ->label('Action Type')
                                                            ->options([
                                                                'increment' => 'Salary Increase',
                                                                'promotion' => 'Promotion',
                                                                'bonus' => 'Bonus',
                                                                'adjustment' => 'Adjustment',
                                                            ])
                                                            ->required()
                                                            ->live(), // Make it reactive to update the value field label
                                                        Forms\Components\TextInput::make('value')
                                                            ->label(function (callable $get) {
                                                                $actionType = $get('action_type');
                                                                return match($actionType) {
                                                                    'increment' => 'New Salary',
                                                                    'promotion' => 'Promotion',
                                                                    'bonus' => 'Bonus Amount',
                                                                    'adjustment' => 'Adjustment Amount',
                                                                    default => 'Value'
                                                                };
                                                            })
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->step(function (callable $get) {
                                                                $actionType = $get('action_type');
                                                                return match($actionType) {
                                                                    'bonus' => 100,
                                                                    default => 0.01
                                                                };
                                                            }),
                                                    ]),
                                                Forms\Components\Grid::make(1)
                                                    ->schema([
                                                        Forms\Components\Textarea::make('remarks')
                                                            ->label('Remarks')
                                                            ->rows(2),
                                                    ]),
                                            ])
                                            ->collapsible()
                                            ->collapsed() // This makes items collapsed by default
                                            ->addActionLabel('Add Compensation Record')
                                            ->itemLabel(fn(array $state): ?string => $state['action_type'] ?? null)
                                            ->defaultItems(0)
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                // Save the value to the appropriate field based on action_type
                                                if (isset($data['value']) && isset($data['action_type'])) {
                                                    switch ($data['action_type']) {
                                                        case 'increment':
                                                            $data['increment'] = $data['value'];
                                                        case 'promotion':
                                                            $data['new_salary'] = $data['value'];
                                                            break;
                                                        case 'bonus':
                                                            $data['bonus_amount'] = $data['value'];
                                                            break;
                                                        case 'adjustment':
                                                            $data['adjustment_amount'] = $data['value'];
                                                            break;
                                                    }
                                                    // Remove the temporary value field
                                                    unset($data['value']);
                                                }
                                                return $data;
                                            })
                                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                                // Save the value to the appropriate field based on action_type for updates
                                                if (isset($data['value']) && isset($data['action_type'])) {
                                                    switch ($data['action_type']) {
                                                        case 'increment':
                                                            $data['increment'] = $data['value'];
                                                        case 'promotion':
                                                            $data['new_salary'] = $data['value'];
                                                            break;
                                                        case 'bonus':
                                                            $data['bonus_amount'] = $data['value'];
                                                            break;
                                                        case 'adjustment':
                                                            $data['adjustment_amount'] = $data['value'];
                                                            break;
                                                    }
                                                    // Remove the temporary value field
                                                    unset($data['value']);
                                                }
                                                return $data;
                                            })
                                            ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                                                // When loading existing records, populate the value field from the appropriate field
                                                if (isset($data['action_type'])) {
                                                    switch ($data['action_type']) {
                                                        case 'increment':
                                                            $data['value'] = $data['increment'] ?? null;
                                                            break;
                                                        case 'promotion':
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
//TODO Fix this
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
//Todo Fix this to Date
                        Forms\Components\Tabs\Tab::make('Benefits & Allowances')
                            ->schema([
                                Forms\Components\Section::make('Annual Benefits')
                                    ->schema([
                                        Forms\Components\Repeater::make('benefitsAllowances')
                                            ->label('Benefits & Allowances')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\Grid::make(4)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('year')
                                                            ->label('Year')
                                                            ->numeric()
                                                            ->default(now()->year)
                                                            ->required(),
                                                        Forms\Components\TextInput::make('internet_allowance')
                                                            ->label('Internet Allowance')
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->default(0),
                                                        Forms\Components\TextInput::make('medical_allowance')
                                                            ->label('Medical Allowance')
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->default(0),
                                                        Forms\Components\TextInput::make('home_office_setup')
                                                            ->label('Home Office Setup Budget')
                                                            ->numeric()
                                                            ->prefix('$')
                                                            ->default(1000),
                                                    ]),
                                                Forms\Components\Toggle::make('home_office_setup_claimed')
                                                    ->label('Home Office Setup Claimed')
                                                    ->default(false),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string => 'Year ' . ($state['year'] ?? ''))
                                            ->addActionLabel('Add Benefits Record')
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
                                                                'other' => 'Other',
                                                            ])
                                                            ->required(),
                                                        Forms\Components\TextInput::make('asset_name')
                                                            ->label('Asset Name')
                                                            ->required()
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('serial_number')
                                                            ->label('Serial Number')
                                                            ->maxLength(255),
                                                    ]),
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\DatePicker::make('issued_date')
                                                            ->label('Issued Date')
                                                            ->default(now()),
                                                        Forms\Components\Select::make('status')
                                                            ->label('Status')
                                                            ->options([
                                                                'assigned' => 'Assigned',
                                                                'returned' => 'Returned',
                                                                'damaged' => 'Damaged',
                                                                'lost' => 'Lost',
                                                            ])
                                                            ->default('assigned')
                                                            ->required(),
                                                    ]),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string => ($state['asset_type'] ?? '') . ' - ' . ($state['asset_name'] ?? ''))
                                            ->addActionLabel('Add Asset')
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
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('policy_number')
                                                            ->label('Policy Number')
                                                            ->required()
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('annual_premium')
                                                            ->label('Annual Premium')
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->step(0.01),
                                                    ]),
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\DatePicker::make('policy_start_date')
                                                            ->label('Policy Start Date')
                                                            ->required(),
                                                        Forms\Components\DatePicker::make('policy_end_date')
                                                            ->label('Policy End Date')
                                                            ->required()
                                                            ->after('policy_start_date'),
                                                        Forms\Components\Toggle::make('annual_checkup_used')
                                                            ->label('Annual Checkup Used')
                                                            ->default(false),
                                                    ]),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string => $state['provider_name'] ?? 'Insurance Policy')
                                            ->addActionLabel('Add Insurance Policy')
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
                                                                'other' => 'Other',
                                                            ])
                                                            ->required(),
                                                        Forms\Components\TextInput::make('document_name')
                                                            ->label('Document Name')
                                                            ->required()
                                                            ->maxLength(255),
                                                        Forms\Components\DatePicker::make('submission_date')
                                                            ->label('Submission Date')
                                                            ->default(now()),
                                                    ]),
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Select::make('status')
                                                            ->label('Status')
                                                            ->options([
                                                                'pending' => 'Pending',
                                                                'verified' => 'Verified',
                                                                'rejected' => 'Rejected',
                                                            ])
                                                            ->default('pending')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('verified_by')
                                                            ->label('Verified By')
                                                            ->maxLength(255),
                                                    ]),
                                                Forms\Components\FileUpload::make('document_file')
                                                    ->label('Document File')
                                                    ->directory('employee-documents')
                                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                    ->maxSize(5120), // 5MB
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string => ($state['document_type'] ?? '') . ' - ' . ($state['document_name'] ?? ''))
                                            ->addActionLabel('Add Document')
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
                    ->circular(),
                Tables\Columns\TextColumn::make('employee_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employmentHistory.current_department')
                    ->label('Department')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employmentHistory.current_role')
                    ->label('Role')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'primary' => 'on_leave',
                        'danger' => 'terminated',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('current_department')
                    ->relationship('employmentHistory', 'current_department'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
                                            ->label('Employee ID'),
                                        Components\TextEntry::make('full_name')
                                            ->label('Full Name'),
                                        Components\TextEntry::make('email'),
                                        Components\BadgeEntry::make('status'),
                                    ]),
                                    Components\Group::make([
                                        Components\ImageEntry::make('profile_photo')
                                            ->hiddenLabel()
                                            ->circular(),
                                    ]),
                                ]),
                        ]),
                    ]),

                Components\Section::make('Employment Details')
                    ->schema([
                        Components\TextEntry::make('employmentHistory.joining_date')
                            ->label('Joining Date')
                            ->date()->format('Y-m-d'),
                        Components\TextEntry::make('employmentHistory.current_manager')
                            ->label('Current Manager'),
                    ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // We'll define these relation managers next
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
}
