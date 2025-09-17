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
use Illuminate\Support\Facades\Storage;
use App\Models\Warning;
use App\Models\Appreciation;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?int $navigationSort = -100;

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
                                                Forms\Components\TextInput::make('username')
                                                    ->label('Username')
                                                    ->required()
                                                    ->maxLength(255),
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
                                            ->disk('public') // Add this line
                                            ->circleCropper()
                                            ->directory('employee-photos')
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->helperText('JPEG, PNG or WebP only, max 2MB')
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
                                                    ->displayFormat('d/m/Y')
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
                                                    ->displayFormat('d/m/Y')
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
                                                    ->displayFormat('d/m/Y')
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
                                                                    ->native(false)
                                                                    ->displayFormat('d/m/Y'),
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
                                                                    ->step(0.01)
                                                                    ->helperText('Percentage of goals achieved during this period'),
                                                                Forms\Components\TextInput::make('overall_rating')
                                                                    ->label('Overall Rating')
                                                                    ->numeric()
                                                                    ->minValue(1)
                                                                    ->maxValue(5)
                                                                    ->step(0.1)
                                                                    ->helperText('Rating from 1.0 to 5.0'),
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
                                                            ->displayFormat('d/m/Y')
                                                            ->required()
                                                            ->maxDate(now()),
                                                        Forms\Components\Select::make('action_type')
                                                            ->label('Action Type')
                                                            ->options([
                                                                'joining' => 'Joining',
                                                                'increment' => 'Increment',
                                                                'promotion' => 'Promotion',
                                                                'bonus' => 'Bonus',
                                                                'adjustment' => 'Adjustment',
                                                            ])
                                                            ->required()
                                                            ->reactive(),
                                                        Forms\Components\TextInput::make('approved_by')
                                                            ->label('Approved By')
                                                            ->maxLength(255),
                                                    ]),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('previous_salary')
                                                            ->label('Previous Salary')
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->step(1)
                                                            ->minValue(0)
                                                            ->visible(fn ($get) => !in_array($get('action_type'), ['joining', 'bonus'])),

                                                        Forms\Components\TextInput::make('new_salary')
                                                            ->label('New Salary')
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->step(1)
                                                            ->minValue(0)
                                                            ->required(fn ($get) => in_array($get('action_type'), ['joining', 'increment', 'promotion']))
                                                            ->visible(fn ($get) => !in_array($get('action_type'), ['bonus', 'adjustment'])),
                                                    ]),

                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('bonus_amount')
                                                            ->label('Bonus Amount')
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->step(1)
                                                            ->minValue(0)
                                                            ->visible(fn ($get) => in_array($get('action_type'), ['bonus', 'promotion']))
                                                            ->required(fn ($get) => $get('action_type') === 'bonus'),

                                                        Forms\Components\TextInput::make('incentive_amount')
                                                            ->label('Incentive Amount')
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->step(1)
                                                            ->minValue(0),

                                                        Forms\Components\TextInput::make('adjustment_amount')
                                                            ->label('Adjustment Amount')
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->step(1)
                                                            ->helperText('Positive for increase, negative for decrease')
                                                            ->visible(fn ($get) => $get('action_type') === 'adjustment')
                                                            ->required(fn ($get) => $get('action_type') === 'adjustment'),
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
                                                (isset($state['effective_date']) ? ' - ' . Carbon::parse($state['effective_date'])->format('M Y') : '') .
                                                (isset($state['new_salary']) ? ' (PKR ' . number_format($state['new_salary']) . ')' : '') .
                                                (isset($state['bonus_amount']) ? ' (Bonus: PKR ' . number_format($state['bonus_amount']) . ')' : '') .
                                                (isset($state['adjustment_amount']) ? ' (Adj: PKR ' . number_format($state['adjustment_amount']) . ')' : '')
                                            )
                                            ->addActionLabel('Add Compensation Record')
                                            ->reorderable(false)
                                            ->defaultItems(0),
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
                                    Forms\Components\Section::make('Monthly Benefits')
                                        ->schema([
                                            Forms\Components\Repeater::make('benefitsAllowances')
                                                ->label('Monthly Benefits & Allowances')
                                                ->relationship('benefitsAllowances')
                                                ->schema([
                                                    Forms\Components\Grid::make(2)
                                                        ->schema([
                                                            Forms\Components\TextInput::make('year')
                                                                ->label('Year')
                                                                ->numeric()
                                                                ->minValue(2020)
                                                                ->maxValue(2030)
                                                                ->default(date('Y'))
                                                                ->required(),
                                                            Forms\Components\Select::make('month')
                                                                ->label('Month')
                                                                ->options([
                                                                    1 => 'January', 2 => 'February', 3 => 'March',
                                                                    4 => 'April', 5 => 'May', 6 => 'June',
                                                                    7 => 'July', 8 => 'August', 9 => 'September',
                                                                    10 => 'October', 11 => 'November', 12 => 'December',
                                                                ])
                                                                ->default(date('n'))
                                                                ->required(),
                                                        ]),
                                                    Forms\Components\Grid::make(2)
                                                        ->schema([
                                                            Forms\Components\TextInput::make('internet_allowance')
                                                                ->label('Internet Allowance')
                                                                ->numeric()
                                                                ->prefix('$')
                                                                ->step(0.01)
                                                                ->minValue(0)
                                                                ->default(0)
                                                                ->nullable(),
                                                            Forms\Components\TextInput::make('medical_allowance')
                                                                ->label('Medical Allowance')
                                                                ->numeric()
                                                                ->prefix('$')
                                                                ->step(0.01)
                                                                ->minValue(0)
                                                                ->default(0)
                                                                ->nullable(),
                                                        ]),
                                                    Forms\Components\Grid::make(2)
                                                        ->schema([
                                                            Forms\Components\TextInput::make('home_office_setup')
                                                                ->label('Home Office Setup Budget')
                                                                ->numeric()
                                                                ->prefix('$')
                                                                ->step(0.01)
                                                                ->minValue(0)
                                                                ->default(1000.00)
                                                                ->nullable(),
                                                            Forms\Components\Toggle::make('home_office_setup_claimed')
                                                                ->label('Home Office Setup Claimed')
                                                                ->default(false),
                                                        ]),
                                                    Forms\Components\Toggle::make('birthday_allowance_claimed')
                                                        ->label('Birthday Allowance Claimed')
                                                        ->default(false),

                                                    Forms\Components\Repeater::make('other_benefits')
                                                        ->label('Custom Benefits')
                                                        ->schema([
                                                            Forms\Components\Grid::make(3)
                                                                ->schema([
                                                                    Forms\Components\TextInput::make('benefit_name')
                                                                        ->label('Benefit Name')
                                                                        ->required()
                                                                        ->placeholder('e.g., Netflix, Gym Membership'),
                                                                    Forms\Components\TextInput::make('benefit_value')
                                                                        ->label('Monthly Value')
                                                                        ->numeric()
                                                                        ->prefix('$')
                                                                        ->step(0.01)
                                                                        ->minValue(0)
                                                                        ->required(),
                                                                    Forms\Components\Toggle::make('is_claimed')
                                                                        ->label('Claimed')
                                                                        ->default(false),
                                                                ]),
                                                            Forms\Components\Textarea::make('benefit_description')
                                                                ->label('Description')
                                                                ->rows(2)
                                                                ->maxLength(500)
                                                                ->columnSpanFull(),
                                                        ])
                                                        ->collapsible()
                                                        ->itemLabel(fn (array $state): ?string =>
                                                            ($state['benefit_name'] ?? 'Custom Benefit') .
                                                            (isset($state['benefit_value']) ? ' - $' . $state['benefit_value'] : '')
                                                        )
                                                        ->addActionLabel('Add Custom Benefit')
                                                        ->defaultItems(0),
                                                ])
                                                ->collapsible()
                                                ->itemLabel(fn(array $state): ?string =>
                                                    'Benefits: ' .
                                                    (isset($state['month']) ? date('F', mktime(0, 0, 0, (int)$state['month'], 1)) : 'New') . ' ' .
                                                    ($state['year'] ?? date('Y'))
                                                )
                                                ->addActionLabel('Add Monthly Benefits')
                                                ->reorderable(false)
                                                ->defaultItems(fn ($livewire) =>
                                                $livewire instanceof Pages\CreateEmployee ? 1 : 0
                                                ),
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
                                                        Forms\Components\TextInput::make('asset_type')
                                                            ->required()
                                                            ->placeholder('e.g., laptop, desktop, phone, tablet, monitor'),
                                                        Forms\Components\TextInput::make('asset_name')
                                                            ->required()
                                                            ->placeholder('e.g., MacBook Pro, Dell OptiPlex, iPhone 14'),
                                                        Forms\Components\TextInput::make('model')
                                                            ->placeholder('e.g., MacBook Pro 16-inch, OptiPlex 7090, iPhone 14 Pro'),
                                                    ]),
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('serial_number')
                                                            ->required()
                                                            ->placeholder('Enter unique serial number')
                                                            ->unique(ignoreRecord: true),
                                                        Forms\Components\TextInput::make('purchase_value')
                                                            ->numeric()
                                                            ->prefix('PKR')
                                                            ->placeholder('0.00')
                                                            ->step(0.01),
                                                    ]),
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\DatePicker::make('issued_date')
                                                            ->required()
                                                            ->placeholder('Select issue date')
                                                            ->native(false)
                                                            ->displayFormat('d/m/Y')
                                                            ->default(now())
                                                            ->maxDate(now()),
                                                        Forms\Components\DatePicker::make('return_date')
                                                            ->placeholder('Select return date (if applicable)')
                                                            ->native(false)
                                                            ->displayFormat('d/m/Y')
                                                            ->after('issued_date')
                                                            ->hidden(fn (callable $get) => $get('status') === 'issued'),
                                                        Forms\Components\Select::make('status')
                                                            ->required()
                                                            ->options([
                                                                'issued' => 'Issued',
                                                                'returned' => 'Returned',
                                                                'damaged' => 'Damaged',
                                                                'lost' => 'Lost',
                                                            ])
                                                            ->default('issued')
                                                            ->placeholder('Select asset status')
                                                            ->live(),
                                                    ]),
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Select::make('condition_when_issued')
                                                            ->required()
                                                            ->options([
                                                                'new' => 'New',
                                                                'good' => 'Good',
                                                                'fair' => 'Fair',
                                                                'poor' => 'Poor',
                                                            ])
                                                            ->placeholder('Select condition when issued'),
                                                        Forms\Components\Select::make('condition_when_returned')
                                                            ->options([
                                                                'good' => 'Good',
                                                                'fair' => 'Fair',
                                                                'poor' => 'Poor',
                                                                'damaged' => 'Damaged',
                                                            ])
                                                            ->placeholder('Select condition when returned (if applicable)')
                                                            ->visible(fn (callable $get) => in_array($get('status'), ['returned', 'damaged'])),
                                                    ]),
                                                Forms\Components\Textarea::make('notes')
                                                    ->columnSpanFull()
                                                    ->placeholder('Add any additional notes or comments about the asset')
                                                    ->rows(2)
                                                    ->maxLength(500),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string =>
                                                ($state['asset_type'] ?? 'Asset') . ' - ' .
                                                ($state['asset_name'] ?? 'New Asset') .
                                                (isset($state['status']) ? ' [' . ucfirst($state['status']) . ']' : '') .
                                                (isset($state['serial_number']) ? ' (' . $state['serial_number'] . ')' : '')
                                            )
                                            ->addActionLabel('Add Asset')
                                            ->reorderable(false)
                                            ->defaultItems(0)
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                // The employee_id will be automatically set by the relationship
                                                return $data;
                                            })
                                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                                // Ensure proper data handling when saving
                                                return $data;
                                            }),
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
                                                            ->displayFormat('d/m/Y')
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
                                                            ->displayFormat('d/m/Y')
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
                                                            ->displayFormat('d/m/Y')
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
                                                            ->displayFormat('d/m/Y')
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

                        Forms\Components\Tabs\Tab::make('Disciplinary Actions')
                            ->schema([
                                Forms\Components\Section::make('Warning History')
                                    ->schema([
                                        Forms\Components\Placeholder::make('warnings_summary')
                                            ->label('Warning Summary')
                                            ->content(function ($record) {
                                                if (!$record) {
                                                    return 'No warnings issued yet.';
                                                }

                                                $totalWarnings = $record->warnings->count();
                                                $activeWarnings = $record->warnings->where('status', 'active')->count();
                                                $lastWarning = $record->warnings->first();

                                                $summary = "Total Warnings: {$totalWarnings} | Active Warnings: {$activeWarnings}";

                                                if ($lastWarning) {
                                                    $summary .= " | Last Warning: {$lastWarning->subject} (Warning #{$lastWarning->warning_number})";
                                                }

                                                return $summary;
                                            })
                                            ->columnSpanFull(),

                                        Forms\Components\Repeater::make('warnings')
                                            ->relationship('warnings')
                                            ->label('Warnings')
                                            ->schema([
                                                Forms\Components\Section::make('Warning Details')
                                                    ->schema([
                                                        Forms\Components\Grid::make(4)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('warning_number')
                                                                    ->label('Warning #')
                                                                    ->disabled()
                                                                    ->prefix('#'),
                                                                Forms\Components\Select::make('warning_type')
                                                                    ->label('Type')
                                                                    ->options([
                                                                        'attendance' => 'Attendance',
                                                                        'performance' => 'Performance',
                                                                        'conduct' => 'Conduct',
                                                                        'policy_violation' => 'Policy Violation',
                                                                        'safety' => 'Safety',
                                                                        'harassment' => 'Harassment',
                                                                        'insubordination' => 'Insubordination',
                                                                        'other' => 'Other',
                                                                    ])
                                                                    ->required(),
                                                                Forms\Components\Select::make('severity_level')
                                                                    ->label('Severity')
                                                                    ->options([
                                                                        'minor' => 'Minor',
                                                                        'moderate' => 'Moderate',
                                                                        'major' => 'Major',
                                                                        'critical' => 'Critical',
                                                                    ])
                                                                    ->required(),
                                                                Forms\Components\Select::make('status')
                                                                    ->label('Status')
                                                                    ->options([
                                                                        'active' => 'Active',
                                                                        'acknowledged' => 'Acknowledged',
                                                                        'resolved' => 'Resolved',
                                                                        'escalated' => 'Escalated',
                                                                    ])
                                                                    ->required(),
                                                            ]),
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\DatePicker::make('incident_date')
                                                                    ->label('Incident Date')
                                                                    ->required()
                                                                    ->native(false)
                                                                    ->displayFormat('d/m/Y'),
                                                                Forms\Components\TextInput::make('issued_by')
                                                                    ->label('Issued By')
                                                                    ->required(),
                                                            ]),
                                                        Forms\Components\TextInput::make('subject')
                                                            ->label('Subject')
                                                            ->required()
                                                            ->columnSpanFull(),
                                                        Forms\Components\Textarea::make('description')
                                                            ->label('Description')
                                                            ->rows(3)
                                                            ->required()
                                                            ->columnSpanFull(),
                                                        Forms\Components\Textarea::make('expected_improvement')
                                                            ->label('Expected Improvement')
                                                            ->rows(2)
                                                            ->required()
                                                            ->columnSpanFull(),
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\Toggle::make('employee_acknowledgment')
                                                                    ->label('Employee Acknowledged'),
                                                                Forms\Components\DatePicker::make('follow_up_date')
                                                                    ->label('Follow-up Date')
                                                                    ->native(false)
                                                                    ->displayFormat('d/m/Y'),
                                                            ]),
                                                    ]),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                "Warning #{$state['warning_number']} - " .
                                                ($state['subject'] ?? 'New Warning') .
                                                " [{$state['status']}]"
                                            )
                                            ->addActionLabel('Add Warning')
                                            ->reorderable(false)
                                            ->defaultItems(0)
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $livewire): array {
                                                // Auto-generate warning number and set defaults
                                                $employee = $livewire->record ?? Employee::find($livewire->data['employee_id'] ?? null);
                                                if ($employee) {
                                                    $lastWarning = $employee->warnings()->orderBy('warning_number', 'desc')->first();
                                                    $data['warning_number'] = $lastWarning ? $lastWarning->warning_number + 1 : 1;
                                                }

                                                $data['warning_date'] = $data['warning_date'] ?? now();

                                                return $data;
                                            }),
                                    ]),

                                Forms\Components\Section::make('Quick Actions')
                                    ->schema([
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('issue_new_warning')
                                                ->label('Issue New Warning')
                                                ->icon('heroicon-o-exclamation-triangle')
                                                ->color('danger')
                                                ->url(function ($record) {
                                                    return $record ? WarningResource::getUrl('create', ['employee' => $record->id]) : null;
                                                })
                                                ->visible(fn ($record) => $record !== null),
                                            Forms\Components\Actions\Action::make('view_all_warnings')
                                                ->label('View All Warnings')
                                                ->icon('heroicon-o-eye')
                                                ->color('info')
                                                ->url(function ($record) {
                                                    return $record ? WarningResource::getUrl('index', [
                                                        'tableFilters' => ['employee_id' => ['value' => $record->id]]
                                                    ]) : null;
                                                })
                                                ->visible(fn ($record) => $record && $record->warnings->count() > 0),
                                        ])
                                    ])
                                    ->visible(fn ($livewire) => !($livewire instanceof \App\Filament\Resources\EmployeeResource\Pages\CreateEmployee)),
                            ]),

                        Forms\Components\Tabs\Tab::make('Recognition & Appreciations')
                            ->schema([
                                Forms\Components\Section::make('Recognition Summary')
                                    ->schema([
                                        Forms\Components\Placeholder::make('appreciation_summary')
                                            ->label('Recognition Summary')
                                            ->content(function ($record) {
                                                if (!$record) {
                                                    return 'No recognitions awarded yet.';
                                                }

                                                $totalAppreciations = $record->appreciations->count();
                                                $publishedAppreciations = $record->appreciations->where('status', 'published')->count();
                                                $totalValue = $record->appreciations->whereNotNull('recognition_value')->sum('recognition_value');
                                                $lastAppreciation = $record->appreciations->first();

                                                $summary = "Total Recognitions: {$totalAppreciations} | Published: {$publishedAppreciations}";

                                                if ($totalValue > 0) {
                                                    $summary .= " | Total Value: $" . number_format($totalValue, 2);
                                                }

                                                if ($lastAppreciation) {
                                                    $summary .= " | Last Recognition: {$lastAppreciation->title} (#{$lastAppreciation->appreciation_number})";
                                                }

                                                return $summary;
                                            })
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('Recognition History')
                                    ->schema([
                                        Forms\Components\Repeater::make('appreciations')
                                            ->relationship('appreciations')
                                            ->label('Recognitions')
                                            ->schema([
                                                Forms\Components\Section::make('Recognition Details')
                                                    ->schema([
                                                        Forms\Components\Grid::make(4)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('appreciation_number')
                                                                    ->label('Recognition #')
                                                                    ->disabled()
                                                                    ->prefix('#'),
                                                                Forms\Components\Select::make('appreciation_type')
                                                                    ->label('Type')
                                                                    ->options([
                                                                        'spot_recognition' => 'Spot Recognition',
                                                                        'monthly_award' => 'Monthly Award',
                                                                        'quarterly_award' => 'Quarterly Award',
                                                                        'annual_award' => 'Annual Award',
                                                                        'peer_nomination' => 'Peer Nomination',
                                                                        'manager_recognition' => 'Manager Recognition',
                                                                        'customer_feedback' => 'Customer Feedback',
                                                                        'milestone_celebration' => 'Milestone Celebration',
                                                                    ])
                                                                    ->required(),
                                                                Forms\Components\Select::make('category')
                                                                    ->label('Category')
                                                                    ->options([
                                                                        'exceptional_performance' => 'Exceptional Performance',
                                                                        'innovation' => 'Innovation',
                                                                        'leadership' => 'Leadership',
                                                                        'teamwork' => 'Teamwork',
                                                                        'customer_service' => 'Customer Service',
                                                                        'problem_solving' => 'Problem Solving',
                                                                        'mentoring' => 'Mentoring',
                                                                        'milestone_achievement' => 'Milestone Achievement',
                                                                        'cultural_values' => 'Cultural Values',
                                                                        'continuous_improvement' => 'Continuous Improvement',
                                                                        'safety_excellence' => 'Safety Excellence',
                                                                        'other' => 'Other',
                                                                    ])
                                                                    ->required(),
                                                                Forms\Components\Select::make('status')
                                                                    ->label('Status')
                                                                    ->options([
                                                                        'draft' => 'Draft',
                                                                        'pending_approval' => 'Pending Approval',
                                                                        'approved' => 'Approved',
                                                                        'published' => 'Published',
                                                                        'archived' => 'Archived',
                                                                    ])
                                                                    ->required(),
                                                            ]),
                                                        Forms\Components\Grid::make(3)
                                                            ->schema([
                                                                Forms\Components\DatePicker::make('achievement_date')
                                                                    ->label('Achievement Date')
                                                                    ->required()
                                                                    ->native(false)
                                                                    ->displayFormat('d/m/Y'),
                                                                Forms\Components\TextInput::make('nominated_by')
                                                                    ->label('Nominated By')
                                                                    ->required(),
                                                                Forms\Components\TextInput::make('recognition_value')
                                                                    ->label('Recognition Value')
                                                                    ->numeric()
                                                                    ->prefix('$')
                                                                    ->step(0.01),
                                                            ]),
                                                        Forms\Components\TextInput::make('title')
                                                            ->label('Title')
                                                            ->required()
                                                            ->columnSpanFull(),
                                                        Forms\Components\Textarea::make('description')
                                                            ->label('Achievement Description')
                                                            ->rows(3)
                                                            ->required()
                                                            ->columnSpanFull(),
                                                        Forms\Components\Textarea::make('impact_description')
                                                            ->label('Business Impact')
                                                            ->rows(2)
                                                            ->required()
                                                            ->columnSpanFull(),
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\Toggle::make('public_recognition')
                                                                    ->label('Public Recognition'),
                                                                Forms\Components\DatePicker::make('publication_date')
                                                                    ->label('Publication Date')
                                                                    ->native(false)
                                                                    ->displayFormat('d/m/Y')
                                                                    ->visible(fn (callable $get) => $get('status') === 'published'),
                                                            ]),
                                                        Forms\Components\TagsInput::make('skills_demonstrated')
                                                            ->label('Skills Demonstrated')
                                                            ->placeholder('Enter skills and press Enter')
                                                            ->columnSpanFull(),
                                                    ]),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                "Recognition #{$state['appreciation_number']} - " .
                                                ($state['title'] ?? 'New Recognition') .
                                                " [{$state['status']}]"
                                            )
                                            ->addActionLabel('Add Recognition')
                                            ->reorderable(false)
                                            ->defaultItems(0)
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $livewire): array {
                                                // Auto-generate appreciation number and set defaults
                                                $employee = $livewire->record ?? Employee::find($livewire->data['employee_id'] ?? null);
                                                if ($employee) {
                                                    $lastAppreciation = $employee->appreciations()->orderBy('appreciation_number', 'desc')->first();
                                                    $data['appreciation_number'] = $lastAppreciation ? $lastAppreciation->appreciation_number + 1 : 1;
                                                }

                                                $data['recognition_date'] = $data['recognition_date'] ?? now();

                                                return $data;
                                            }),
                                    ]),

                                Forms\Components\Section::make('Quick Actions')
                                    ->schema([
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('create_new_recognition')
                                                ->label('Create New Recognition')
                                                ->icon('heroicon-o-trophy')
                                                ->color('success')
                                                ->url(function ($record) {
                                                    return $record ? AppreciationResource::getUrl('create', ['employee' => $record->id]) : null;
                                                })
                                                ->visible(fn ($record) => $record !== null),
                                            Forms\Components\Actions\Action::make('view_all_recognitions')
                                                ->label('View All Recognitions')
                                                ->icon('heroicon-o-eye')
                                                ->color('info')
                                                ->url(function ($record) {
                                                    return $record ? AppreciationResource::getUrl('index', [
                                                        'tableFilters' => ['employee_id' => ['value' => $record->id]]
                                                    ]) : null;
                                                })
                                                ->visible(fn ($record) => $record && $record->appreciations->count() > 0),
                                        ])
                                    ])
                                    ->visible(fn ($livewire) => !($livewire instanceof \App\Filament\Resources\EmployeeResource\Pages\CreateEmployee)),
                            ]),

                        Forms\Components\Tabs\Tab::make('Performance Improvement Plans')
                            ->schema([
                                Forms\Components\Section::make('PIP Summary')
                                    ->schema([
                                        Forms\Components\Placeholder::make('pip_summary')
                                            ->label('PIP Summary')
                                            ->content(function ($record) {
                                                if (!$record) {
                                                    return 'No Performance Improvement Plans initiated yet.';
                                                }

                                                $totalPips = $record->performanceImprovementPlans->count();
                                                $activePips = $record->performanceImprovementPlans->where('status', 'active')->count();
                                                $successfulPips = $record->performanceImprovementPlans->where('status', 'successful')->count();
                                                $unsuccessfulPips = $record->performanceImprovementPlans
                                                    ->whereIn('status', ['unsuccessful', 'terminated'])->count();
                                                $lastPip = $record->performanceImprovementPlans->first();

                                                $summary = "Total PIPs: {$totalPips} | Active: {$activePips} | Successful: {$successfulPips} | Unsuccessful: {$unsuccessfulPips}";

                                                if ($totalPips > 0) {
                                                    $successRate = round(($successfulPips / max(1, $successfulPips + $unsuccessfulPips)) * 100);
                                                    $summary .= " | Success Rate: {$successRate}%";
                                                }

                                                if ($lastPip) {
                                                    $summary .= " | Latest: {$lastPip->title} (#{$lastPip->pip_number} - {$lastPip->status})";
                                                }

                                                return $summary;
                                            })
                                            ->columnSpanFull(),

                                        Forms\Components\Placeholder::make('active_pip_alert')
                                            ->label('⚠️ ACTIVE PIP ALERT')
                                            ->content(function ($record) {
                                                if (!$record) return '';

                                                $activePip = $record->performanceImprovementPlans()->where('status', 'active')->first();
                                                if (!$activePip) return '';

                                                $daysRemaining = $activePip->days_remaining;
                                                $endDate = $activePip->end_date->format('M d, Y');

                                                if ($daysRemaining < 0) {
                                                    return "⚠️ OVERDUE: PIP #{$activePip->pip_number} was due on {$endDate} (overdue by " . abs($daysRemaining) . " days)";
                                                } else {
                                                    return "📋 ACTIVE: PIP #{$activePip->pip_number} - '{$activePip->title}' ends on {$endDate} ({$daysRemaining} days remaining)";
                                                }
                                            })
                                            ->visible(fn ($record) => $record && $record->performanceImprovementPlans()->where('status', 'active')->exists())
                                            ->columnSpanFull(),
                                    ]),

                                Forms\Components\Section::make('PIP History')
                                    ->schema([
                                        Forms\Components\Repeater::make('performanceImprovementPlans')
                                            ->relationship('performanceImprovementPlans')
                                            ->label('Performance Improvement Plans')
                                            ->schema([
                                                Forms\Components\Section::make('PIP Details')
                                                    ->schema([
                                                        Forms\Components\Grid::make(4)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('pip_number')
                                                                    ->label('PIP #')
                                                                    ->disabled()
                                                                    ->prefix('#'),
                                                                Forms\Components\Select::make('pip_type')
                                                                    ->label('Type')
                                                                    ->options([
                                                                        'performance_deficiency' => 'Performance Deficiency',
                                                                        'behavioral_issues' => 'Behavioral Issues',
                                                                        'attendance_problems' => 'Attendance Problems',
                                                                        'skills_gap' => 'Skills Gap',
                                                                        'goal_achievement' => 'Goal Achievement',
                                                                        'quality_standards' => 'Quality Standards',
                                                                        'communication_issues' => 'Communication Issues',
                                                                        'policy_compliance' => 'Policy Compliance',
                                                                    ])
                                                                    ->required(),
                                                                Forms\Components\Select::make('severity_level')
                                                                    ->label('Severity')
                                                                    ->options([
                                                                        'low' => 'Low',
                                                                        'moderate' => 'Moderate',
                                                                        'high' => 'High',
                                                                        'critical' => 'Critical',
                                                                    ])
                                                                    ->required(),
                                                                Forms\Components\Select::make('status')
                                                                    ->label('Status')
                                                                    ->options([
                                                                        'draft' => 'Draft',
                                                                        'active' => 'Active',
                                                                        'under_review' => 'Under Review',
                                                                        'successful' => 'Successful',
                                                                        'unsuccessful' => 'Unsuccessful',
                                                                        'terminated' => 'Terminated',
                                                                        'extended' => 'Extended',
                                                                    ])
                                                                    ->required(),
                                                            ]),
                                                        Forms\Components\Grid::make(3)
                                                            ->schema([
                                                                Forms\Components\DatePicker::make('start_date')
                                                                    ->label('Start Date')
                                                                    ->required()
                                                                    ->native(false)
                                                                    ->displayFormat('d/m/Y'),
                                                                Forms\Components\DatePicker::make('end_date')
                                                                    ->label('End Date')
                                                                    ->required()
                                                                    ->native(false)
                                                                    ->displayFormat('d/m/Y'),
                                                                Forms\Components\Select::make('review_frequency')
                                                                    ->label('Review Frequency')
                                                                    ->options([
                                                                        'weekly' => 'Weekly',
                                                                        'bi_weekly' => 'Bi-weekly',
                                                                        'monthly' => 'Monthly',
                                                                    ])
                                                                    ->required(),
                                                            ]),
                                                        Forms\Components\Grid::make(3)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('initiated_by')
                                                                    ->label('Initiated By')
                                                                    ->required(),
                                                                Forms\Components\TextInput::make('supervisor_assigned')
                                                                    ->label('Supervisor')
                                                                    ->required(),
                                                                Forms\Components\TextInput::make('hr_representative')
                                                                    ->label('HR Representative')
                                                                    ->required(),
                                                            ]),
                                                        Forms\Components\TextInput::make('title')
                                                            ->label('PIP Title')
                                                            ->required()
                                                            ->columnSpanFull(),
                                                        Forms\Components\Textarea::make('performance_concerns')
                                                            ->label('Performance Concerns')
                                                            ->rows(3)
                                                            ->required()
                                                            ->columnSpanFull(),
                                                        Forms\Components\Textarea::make('support_provided')
                                                            ->label('Support Provided')
                                                            ->rows(2)
                                                            ->required()
                                                            ->columnSpanFull(),
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\Toggle::make('employee_acknowledgment')
                                                                    ->label('Employee Acknowledged'),
                                                                Forms\Components\DatePicker::make('completion_date')
                                                                    ->label('Completion Date')
                                                                    ->native(false)
                                                                    ->displayFormat('d/m/Y')
                                                                    ->visible(fn (callable $get) => in_array($get('status'), ['successful', 'unsuccessful', 'terminated'])),
                                                            ]),
                                                        Forms\Components\Select::make('final_outcome')
                                                            ->label('Final Outcome')
                                                            ->options([
                                                                'successful_completion' => 'Successful Completion',
                                                                'unsuccessful_completion' => 'Unsuccessful Completion',
                                                                'early_termination' => 'Early Termination',
                                                                'resignation_during_pip' => 'Resignation During PIP',
                                                                'extended_pip' => 'Extended PIP',
                                                                'alternative_placement' => 'Alternative Placement',
                                                            ])
                                                            ->visible(fn (callable $get) => in_array($get('status'), ['successful', 'unsuccessful', 'terminated']))
                                                            ->columnSpanFull(),
                                                    ]),

                                                Forms\Components\Section::make('Progress & Notes')
                                                    ->schema([
                                                        Forms\Components\Textarea::make('employee_comments')
                                                            ->label('Employee Comments')
                                                            ->rows(2)
                                                            ->columnSpanFull(),
                                                        Forms\Components\Textarea::make('supervisor_notes')
                                                            ->label('Supervisor Notes')
                                                            ->rows(2)
                                                            ->columnSpanFull(),
                                                        Forms\Components\Textarea::make('hr_notes')
                                                            ->label('HR Notes')
                                                            ->rows(2)
                                                            ->columnSpanFull(),
                                                    ])
                                                    ->collapsible()
                                                    ->collapsed(),
                                            ])
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string =>
                                                "PIP #{$state['pip_number']} - " .
                                                ($state['title'] ?? 'New PIP') .
                                                " [{$state['status']}]" .
                                                (isset($state['end_date']) ? ' (Due: ' . \Carbon\Carbon::parse($state['end_date'])->format('M d, Y') . ')' : '')
                                            )
                                            ->addActionLabel('Add PIP')
                                            ->reorderable(false)
                                            ->defaultItems(0)
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $livewire): array {
                                                // Auto-generate PIP number and set defaults
                                                $employee = $livewire->record ?? Employee::find($livewire->data['employee_id'] ?? null);
                                                if ($employee) {
                                                    $lastPip = $employee->performanceImprovementPlans()->orderBy('pip_number', 'desc')->first();
                                                    $data['pip_number'] = $lastPip ? $lastPip->pip_number + 1 : 1;
                                                }

                                                $data['start_date'] = $data['start_date'] ?? now();
                                                $data['end_date'] = $data['end_date'] ?? now()->addDays(90);

                                                return $data;
                                            }),
                                    ]),

                                Forms\Components\Section::make('Quick Actions')
                                    ->schema([
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('create_new_pip')
                                                ->label('Create New PIP')
                                                ->icon('heroicon-o-clipboard-document-check')
                                                ->color('warning')
                                                ->url(function ($record) {
                                                    return $record ? PerformanceImprovementPlanResource::getUrl('create', ['employee' => $record->id]) : null;
                                                })
                                                ->visible(fn ($record) => $record !== null && !$record->performanceImprovementPlans()->where('status', 'active')->exists()),
                                            Forms\Components\Actions\Action::make('view_all_pips')
                                                ->label('View All PIPs')
                                                ->icon('heroicon-o-eye')
                                                ->color('info')
                                                ->url(function ($record) {
                                                    return $record ? PerformanceImprovementPlanResource::getUrl('index', [
                                                        'tableFilters' => ['employee_id' => ['value' => $record->id]]
                                                    ]) : null;
                                                })
                                                ->visible(fn ($record) => $record && $record->performanceImprovementPlans->count() > 0),
                                            Forms\Components\Actions\Action::make('active_pip_warning')
                                                ->label('⚠️ Active PIP - Cannot Create New')
                                                ->icon('heroicon-o-exclamation-triangle')
                                                ->color('danger')
                                                ->disabled()
                                                ->visible(fn ($record) => $record && $record->performanceImprovementPlans()->where('status', 'active')->exists()),
                                        ])
                                    ])
                                    ->visible(fn ($livewire) => !($livewire instanceof \App\Filament\Resources\EmployeeResource\Pages\CreateEmployee)),
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
                    ->defaultImageUrl('/storage/employee-photos/default-avatar.png')
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
                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
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
                Tables\Columns\TextColumn::make('warnings_count')
                    ->label('Total Warnings')
                    ->getStateUsing(fn ($record) => $record->warnings->count())
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state == 0 => 'success',
                        $state <= 2 => 'warning',
                        default => 'danger'
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('active_warnings_count')
                    ->label('Active Warnings')
                    ->getStateUsing(fn ($record) => $record->warnings()->where('status', 'active')->count())
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state == 0 => 'success',
                        $state == 1 => 'warning',
                        default => 'danger'
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('latest_warning')
                    ->label('Latest Warning')
                    ->getStateUsing(function ($record) {
                        $latest = $record->warnings()->latest()->first();
                        return $latest ? $latest->subject : 'None';
                    })
                    ->limit(30)
                    ->tooltip(function ($record) {
                        $latest = $record->warnings()->latest()->first();
                        return $latest ? "Warning #{$latest->warning_number} - {$latest->subject}" : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('appreciations_count')
                    ->label('Total Recognitions')
                    ->getStateUsing(fn ($record) => $record->appreciations->count())
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state == 0 => 'gray',
                        $state <= 2 => 'success',
                        $state <= 5 => 'primary',
                        default => 'warning'
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('published_appreciations_count')
                    ->label('Published Recognitions')
                    ->getStateUsing(fn ($record) => $record->appreciations()->where('status', 'published')->count())
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state == 0 => 'gray',
                        $state <= 2 => 'info',
                        default => 'success'
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_recognition_value')
                    ->label('Recognition Value')
                    ->getStateUsing(fn ($record) => $record->appreciations()->whereNotNull('recognition_value')->sum('recognition_value'))
                    ->money('USD')
                    ->placeholder('$0.00')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('latest_appreciation')
                    ->label('Latest Recognition')
                    ->getStateUsing(function ($record) {
                        $latest = $record->appreciations()->latest()->first();
                        return $latest ? $latest->title : 'None';
                    })
                    ->limit(30)
                    ->tooltip(function ($record) {
                        $latest = $record->appreciations()->latest()->first();
                        return $latest ? "Recognition #{$latest->appreciation_number} - {$latest->title}" : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pip_count')
                    ->label('Total PIPs')
                    ->getStateUsing(fn ($record) => $record->performanceImprovementPlans->count())
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state == 0 => 'success',
                        $state == 1 => 'warning',
                        default => 'danger'
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('active_pip_count')
                    ->label('Active PIPs')
                    ->getStateUsing(fn ($record) => $record->performanceImprovementPlans()->where('status', 'active')->count())
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state == 0 => 'success',
                        $state >= 1 => 'danger'
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('pip_success_rate')
                    ->label('PIP Success Rate')
                    ->getStateUsing(function ($record) {
                        $totalCompleted = $record->performanceImprovementPlans()
                            ->whereIn('status', ['successful', 'unsuccessful', 'terminated'])
                            ->count();

                        if ($totalCompleted === 0) {
                            return 'N/A';
                        }

                        $successful = $record->performanceImprovementPlans()
                            ->where('status', 'successful')
                            ->count();

                        return round(($successful / $totalCompleted) * 100) . '%';
                    })
                    ->badge()
                    ->color(function ($record) {
                        $rate = $record->pip_success_rate;
                        if ($rate === 'N/A') return 'gray';
                        $percentage = (int) str_replace('%', '', $rate);
                        return match(true) {
                            $percentage >= 80 => 'success',
                            $percentage >= 60 => 'warning',
                            default => 'danger'
                        };
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('latest_pip_status')
                    ->label('Latest PIP Status')
                    ->getStateUsing(function ($record) {
                        $latest = $record->performanceImprovementPlans()->first();
                        return $latest ? ucfirst($latest->status) : 'None';
                    })
                    ->badge()
                    ->color(function ($record) {
                        $latest = $record->performanceImprovementPlans()->first();
                        if (!$latest) return 'success';

                        return match($latest->status) {
                            'draft' => 'gray',
                            'active', 'extended' => 'warning',
                            'under_review' => 'info',
                            'successful' => 'success',
                            'unsuccessful', 'terminated' => 'danger',
                            default => 'gray'
                        };
                    })
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
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('joined_until')
                            ->label('Joined Until')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
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
                Tables\Filters\TernaryFilter::make('has_warnings')
                    ->label('Has Warnings')
                    ->placeholder('All employees')
                    ->trueLabel('With warnings')
                    ->falseLabel('Without warnings')
                    ->queries(
                        true: fn ($query) => $query->has('warnings'),
                        false: fn ($query) => $query->doesntHave('warnings'),
                    ),

                Tables\Filters\TernaryFilter::make('has_active_warnings')
                    ->label('Has Active Warnings')
                    ->placeholder('All employees')
                    ->trueLabel('With active warnings')
                    ->falseLabel('Without active warnings')
                    ->queries(
                        true: fn ($query) => $query->whereHas('warnings', fn ($q) => $q->where('status', 'active')),
                        false: fn ($query) => $query->whereDoesntHave('warnings', fn ($q) => $q->where('status', 'active')),
                    ),

                Tables\Filters\SelectFilter::make('warning_count')
                    ->label('Warning Count')
                    ->options([
                        '0' => 'No Warnings',
                        '1' => '1 Warning',
                        '2' => '2 Warnings',
                        '3+' => '3+ Warnings',
                    ])
                    ->query(function ($query, array $data) {
                        if (!isset($data['value']) || $data['value'] === '') return $query;

                        return match($data['value']) {
                            '0' => $query->doesntHave('warnings'),
                            '1' => $query->has('warnings', '=', 1),
                            '2' => $query->has('warnings', '=', 2),
                            '3+' => $query->has('warnings', '>=', 3),
                        };
                    }),
                Tables\Filters\Filter::make('has_appreciations')
                    ->label('Has Recognitions')
                    ->toggle()
                    ->query(fn ($query) => $query->has('appreciations')),

                Tables\Filters\Filter::make('has_published_appreciations')
                    ->label('Has Published Recognitions')
                    ->toggle()
                    ->query(fn ($query) => $query->whereHas('appreciations', fn ($q) => $q->where('status', 'published'))),

                Tables\Filters\Filter::make('recent_appreciations')
                    ->label('Recent Recognitions (6 months)')
                    ->toggle()
                    ->query(fn ($query) => $query->whereHas('appreciations', fn ($q) => $q->where('recognition_date', '>=', now()->subMonths(6)))),

                Tables\Filters\SelectFilter::make('appreciation_count')
                    ->label('Recognition Count')
                    ->options([
                        '0' => 'No Recognitions',
                        '1-2' => '1-2 Recognitions',
                        '3-5' => '3-5 Recognitions',
                        '6+' => '6+ Recognitions',
                    ])
                    ->query(function ($query, array $data) {
                        if (!$data['value']) return $query;

                        return match($data['value']) {
                            '0' => $query->doesntHave('appreciations'),
                            '1-2' => $query->has('appreciations', '>=', 1)->has('appreciations', '<=', 2),
                            '3-5' => $query->has('appreciations', '>=', 3)->has('appreciations', '<=', 5),
                            '6+' => $query->has('appreciations', '>=', 6),
                        };
                    }),
                Tables\Filters\Filter::make('has_pips')
                    ->label('Has PIPs')
                    ->toggle()
                    ->query(fn ($query) => $query->has('performanceImprovementPlans')),

                Tables\Filters\Filter::make('has_active_pips')
                    ->label('Has Active PIPs')
                    ->toggle()
                    ->query(fn ($query) => $query->whereHas('performanceImprovementPlans', fn ($q) => $q->where('status', 'active'))),

                Tables\Filters\Filter::make('overdue_pips')
                    ->label('Has Overdue PIPs')
                    ->toggle()
                    ->query(fn ($query) => $query->whereHas('performanceImprovementPlans', fn ($q) =>
                    $q->where('status', 'active')->where('end_date', '<', now()))),

                Tables\Filters\SelectFilter::make('pip_status')
                    ->label('Latest PIP Status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'under_review' => 'Under Review',
                        'successful' => 'Successful',
                        'unsuccessful' => 'Unsuccessful',
                        'terminated' => 'Terminated',
                        'extended' => 'Extended',
                    ])
                    ->query(function ($query, array $data) {
                        if (!$data['value']) return $query;

                        return $query->whereHas('performanceImprovementPlans', function ($q) use ($data) {
                            $q->where('status', $data['value'])
                                ->whereRaw('id = (SELECT MAX(id) FROM performance_improvement_plans WHERE employee_id = employees.id)');
                        });
                    }),

                Tables\Filters\SelectFilter::make('pip_count')
                    ->label('PIP Count')
                    ->options([
                        '0' => 'No PIPs',
                        '1' => '1 PIP',
                        '2' => '2 PIPs',
                        '3+' => '3+ PIPs',
                    ])
                    ->query(function ($query, array $data) {
                        if (!$data['value']) return $query;

                        return match($data['value']) {
                            '0' => $query->doesntHave('performanceImprovementPlans'),
                            '1' => $query->has('performanceImprovementPlans', '=', 1),
                            '2' => $query->has('performanceImprovementPlans', '=', 2),
                            '3+' => $query->has('performanceImprovementPlans', '>=', 3),
                        };
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
                Tables\Actions\Action::make('view_warnings')
                    ->label('View Warnings')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->url(fn ($record) => WarningResource::getUrl('index', ['tableFilters' => ['employee_id' => ['value' => $record->id]]]))
                    ->visible(fn ($record) => $record->warnings->count() > 0),

                Tables\Actions\Action::make('issue_warning')
                    ->label('Issue Warning')
                    ->icon('heroicon-o-plus')
                    ->color('danger')
                    ->url(fn ($record) => WarningResource::getUrl('create', ['employee' => $record->id])),
                Tables\Actions\Action::make('view_appreciations')
                    ->label('View Recognitions')
                    ->icon('heroicon-o-trophy')
                    ->color('success')
                    ->url(fn ($record) => AppreciationResource::getUrl('index', ['tableFilters' => ['employee_id' => ['value' => $record->id]]]))
                    ->visible(fn ($record) => $record->appreciations->count() > 0),

                Tables\Actions\Action::make('create_appreciation')
                    ->label('Create Recognition')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->url(fn ($record) => AppreciationResource::getUrl('create', ['employee' => $record->id])),
                Tables\Actions\Action::make('view_pips')
                    ->label('View PIPs')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('info')
                    ->url(fn ($record) => PerformanceImprovementPlanResource::getUrl('index', ['tableFilters' => ['employee_id' => ['value' => $record->id]]]))
                    ->visible(fn ($record) => $record->performanceImprovementPlans->count() > 0),

                Tables\Actions\Action::make('create_pip')
                    ->label('Create PIP')
                    ->icon('heroicon-o-plus')
                    ->color('warning')
                    ->url(fn ($record) => PerformanceImprovementPlanResource::getUrl('create', ['employee' => $record->id]))
                    ->visible(fn ($record) => !$record->performanceImprovementPlans()->where('status', 'active')->exists()),

                Tables\Actions\Action::make('pip_alert')
                    ->label('Active PIP!')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->disabled()
                    ->visible(fn ($record) => $record->performanceImprovementPlans()->where('status', 'active')->exists()),
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
            ->paginated([10, 25, 50, 100])
            ->defaultSort('employee_id');
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
                                        Components\TextEntry::make('status')
                                            ->badge()
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

                        // Add these columns for benefits summary
                        Tables\Columns\TextColumn::make('benefitsAllowances_count')
                            ->label('Benefits Records')
                            ->getStateUsing(fn ($record) => $record->benefitsAllowances->count())
                            ->badge()
                            ->color('info')
                            ->toggleable(),

                        Tables\Columns\TextColumn::make('current_month_benefits')
                            ->label('Current Month Benefits')
                            ->getStateUsing(function ($record) {
                                $currentBenefits = $record->benefitsAllowances()
                                    ->where('year', now()->year)
                                    ->where('month', now()->month)
                                    ->first();

                                if (!$currentBenefits) {
                                    return 'Not Set';
                                }

                                return '$' . number_format($currentBenefits->total_benefits, 2);
                            })
                            ->badge()
                            ->color(fn ($state) => $state === 'Not Set' ? 'gray' : 'success')
                            ->toggleable(),

                        Tables\Columns\IconColumn::make('has_current_benefits')
                            ->label('Current Benefits')
                            ->getStateUsing(function ($record) {
                                return $record->benefitsAllowances()
                                    ->where('year', now()->year)
                                    ->where('month', now()->month)
                                    ->exists();
                            })
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger')
                            ->toggleable(),
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

                Components\Section::make('Disciplinary Information')
                    ->schema([
                        Components\Grid::make(4)
                            ->schema([
                                Components\TextEntry::make('total_warnings')
                                    ->label('Total Warnings')
                                    ->getStateUsing(fn ($record) => $record->warnings->count())
                                    ->badge()
                                    ->color(fn ($state) => match(true) {
                                        $state == 0 => 'success',
                                        $state <= 2 => 'warning',
                                        default => 'danger'
                                    }),
                                Components\TextEntry::make('active_warnings')
                                    ->label('Active Warnings')
                                    ->getStateUsing(fn ($record) => $record->warnings()->where('status', 'active')->count())
                                    ->badge()
                                    ->color(fn ($state) => match(true) {
                                        $state == 0 => 'success',
                                        $state == 1 => 'warning',
                                        default => 'danger'
                                    }),
                                Components\TextEntry::make('last_warning_date')
                                    ->label('Last Warning')
                                    ->getStateUsing(function ($record) {
                                        $latest = $record->warnings()->latest()->first();
                                        return $latest ? $latest->created_at->format('M d, Y') : 'None';
                                    })
                                    ->icon('heroicon-o-calendar'),
                                Components\TextEntry::make('warning_status')
                                    ->label('Warning Status')
                                    ->getStateUsing(function ($record) {
                                        $activeCount = $record->warnings()->where('status', 'active')->count();
                                        return match(true) {
                                            $activeCount == 0 => 'Clear',
                                            $activeCount == 1 => 'Under Review',
                                            $activeCount >= 2 => 'Multiple Active',
                                        };
                                    })
                                    ->badge()
                                    ->color(function ($record) {
                                        $activeCount = $record->warnings()->where('status', 'active')->count();
                                        return match(true) {
                                            $activeCount == 0 => 'success',
                                            $activeCount == 1 => 'warning',
                                            default => 'danger'
                                        };
                                    }),
                            ]),
                        Components\TextEntry::make('latest_warning_subject')
                            ->label('Latest Warning Subject')
                            ->getStateUsing(function ($record) {
                                $latest = $record->warnings()->latest()->first();
                                return $latest ? "Warning #{$latest->warning_number}: {$latest->subject}" : 'No warnings issued';
                            })
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->warnings->count() > 0),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => $record->warnings->count() === 0),

                Components\Section::make('Recognition & Achievements')
                    ->schema([
                        Components\Grid::make(4)
                            ->schema([
                                Components\TextEntry::make('total_recognitions')
                                    ->label('Total Recognitions')
                                    ->getStateUsing(fn ($record) => $record->appreciations->count())
                                    ->badge()
                                    ->color(fn ($state) => match(true) {
                                        $state == 0 => 'gray',
                                        $state <= 2 => 'success',
                                        $state <= 5 => 'primary',
                                        default => 'warning'
                                    }),
                                Components\TextEntry::make('published_recognitions')
                                    ->label('Published Recognitions')
                                    ->getStateUsing(fn ($record) => $record->appreciations()->where('status', 'published')->count())
                                    ->badge()
                                    ->color(fn ($state) => match(true) {
                                        $state == 0 => 'gray',
                                        $state <= 2 => 'info',
                                        default => 'success'
                                    }),
                                Components\TextEntry::make('last_recognition_date')
                                    ->label('Last Recognition')
                                    ->getStateUsing(function ($record) {
                                        $latest = $record->appreciations()->latest()->first();
                                        return $latest ? $latest->recognition_date->format('M d, Y') : 'None';
                                    })
                                    ->icon('heroicon-o-trophy'),
                                Components\TextEntry::make('total_recognition_value')
                                    ->label('Total Recognition Value')
                                    ->getStateUsing(fn ($record) => $record->appreciations()->whereNotNull('recognition_value')->sum('recognition_value'))
                                    ->money('USD')
                                    ->placeholder('$0.00'),
                            ]),
                        Components\TextEntry::make('latest_recognition_title')
                            ->label('Latest Recognition')
                            ->getStateUsing(function ($record) {
                                $latest = $record->appreciations()->latest()->first();
                                return $latest ? "Recognition #{$latest->appreciation_number}: {$latest->title}" : 'No recognitions awarded';
                            })
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->appreciations->count() > 0),
                        Components\TextEntry::make('recognition_categories')
                            ->label('Recognition Categories')
                            ->getStateUsing(function ($record) {
                                $categories = $record->appreciations()
                                    ->distinct('category')
                                    ->pluck('category')
                                    ->map(fn ($category) => ucwords(str_replace('_', ' ', $category)))
                                    ->join(', ');
                                return $categories ?: 'No categories';
                            })
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->appreciations->count() > 0),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => $record->appreciations->count() === 0),

                Components\Section::make('Performance Improvement Plans')
                    ->schema([
                        Components\Grid::make(4)
                            ->schema([
                                Components\TextEntry::make('total_pips')
                                    ->label('Total PIPs')
                                    ->getStateUsing(fn ($record) => $record->performanceImprovementPlans->count())
                                    ->badge()
                                    ->color(fn ($state) => match(true) {
                                        $state == 0 => 'success',
                                        $state == 1 => 'warning',
                                        default => 'danger'
                                    }),
                                Components\TextEntry::make('active_pips')
                                    ->label('Active PIPs')
                                    ->getStateUsing(fn ($record) => $record->performanceImprovementPlans()->where('status', 'active')->count())
                                    ->badge()
                                    ->color(fn ($state) => match(true) {
                                        $state == 0 => 'success',
                                        default => 'danger'
                                    }),
                                Components\TextEntry::make('pip_success_rate')
                                    ->label('PIP Success Rate')
                                    ->getStateUsing(function ($record) {
                                        $totalCompleted = $record->performanceImprovementPlans()
                                            ->whereIn('status', ['successful', 'unsuccessful', 'terminated'])
                                            ->count();

                                        if ($totalCompleted === 0) return 'N/A';

                                        $successful = $record->performanceImprovementPlans()
                                            ->where('status', 'successful')
                                            ->count();

                                        return round(($successful / $totalCompleted) * 100) . '%';
                                    })
                                    ->badge()
                                    ->color(function ($record) {
                                        $rate = $record->pip_success_rate;
                                        if ($rate === 'N/A') return 'gray';
                                        $percentage = (int) str_replace('%', '', $rate);
                                        return match(true) {
                                            $percentage >= 80 => 'success',
                                            $percentage >= 60 => 'warning',
                                            default => 'danger'
                                        };
                                    }),
                                Components\TextEntry::make('last_pip_date')
                                    ->label('Last PIP')
                                    ->getStateUsing(function ($record) {
                                        $latest = $record->performanceImprovementPlans()->latest()->first();
                                        return $latest ? $latest->start_date->format('M d, Y') : 'None';
                                    })
                                    ->icon('heroicon-o-calendar'),
                            ]),
                        Components\TextEntry::make('active_pip_alert')
                            ->label('⚠️ Active PIP Alert')
                            ->getStateUsing(function ($record) {
                                $activePip = $record->performanceImprovementPlans()->where('status', 'active')->first();
                                if (!$activePip) return null;

                                $daysRemaining = $activePip->days_remaining;
                                $endDate = $activePip->end_date->format('M d, Y');

                                if ($daysRemaining < 0) {
                                    return "OVERDUE: PIP #{$activePip->pip_number} '{$activePip->title}' was due on {$endDate} (overdue by " . abs($daysRemaining) . " days)";
                                } else {
                                    return "ACTIVE: PIP #{$activePip->pip_number} '{$activePip->title}' ends on {$endDate} ({$daysRemaining} days remaining)";
                                }
                            })
                            ->badge()
                            ->color(function ($record) {
                                $activePip = $record->performanceImprovementPlans()->where('status', 'active')->first();
                                if (!$activePip) return 'success';

                                $daysRemaining = $activePip->days_remaining;
                                return match(true) {
                                    $daysRemaining < 0 => 'danger',
                                    $daysRemaining <= 7 => 'warning',
                                    default => 'info'
                                };
                            })
                            ->visible(fn ($record) => $record->performanceImprovementPlans()->where('status', 'active')->exists())
                            ->columnSpanFull(),
                        Components\TextEntry::make('latest_pip_details')
                            ->label('Latest PIP')
                            ->getStateUsing(function ($record) {
                                $latest = $record->performanceImprovementPlans()->latest()->first();
                                return $latest ? "PIP #{$latest->pip_number}: {$latest->title} [{$latest->status}]" : 'No PIPs initiated';
                            })
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->performanceImprovementPlans->count() > 0),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => $record->performanceImprovementPlans->count() === 0 && !$record->performanceImprovementPlans()->where('status', 'active')->exists()),
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
                'performanceReviews',
                'compensationHistory',
                'benefitsAllowances',
                'assetManagement',
                'healthInsurance',
                'complianceDocuments',
                'leaveAttendance',
                'warnings',
                'appreciations',
                'performanceImprovementPlans'
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['employee_id', 'full_name', 'email', 'employmentHistory.current_department', 'employmentHistory.current_role'];
    }
}
