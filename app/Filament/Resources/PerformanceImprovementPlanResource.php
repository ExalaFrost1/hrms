<?php
// app/Filament/Resources/PerformanceImprovementPlanResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\PerformanceImprovementPlanResource\Pages;
use App\Mail\PIPEmail;
use App\Models\PerformanceImprovementPlan;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class PerformanceImprovementPlanResource extends Resource
{
    protected static ?string $model = PerformanceImprovementPlan::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?int $navigationSort = -80;
    protected static ?string $navigationLabel = 'Performance Improvement Plans';
    protected static ?string $modelLabel = 'PIP';
    protected static ?string $pluralModelLabel = 'PIPs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('PIP Overview')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('employee_id')
                                    ->label('Employee')
                                    ->relationship('employee', 'full_name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->getOptionLabelFromRecordUsing(fn(Employee $record): string => "{$record->employee_id} - {$record->full_name}")
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        if ($state) {
                                            $employee = Employee::find($state);
                                            if ($employee) {
                                                $pipCount = PerformanceImprovementPlan::where('employee_id', $state)->count();
                                                $set('pip_number', $pipCount + 1);
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('pip_number')
                                    ->label('PIP Number')
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Automatically generated based on employee PIP history'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('pip_type')
                                    ->label('PIP Type')
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
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Select::make('severity_level')
                                    ->label('Severity Level')
                                    ->options([
                                        'low' => 'Low',
                                        'moderate' => 'Moderate',
                                        'high' => 'High',
                                        'critical' => 'Critical',
                                    ])
                                    ->default('moderate')
                                    ->required(),
                                Forms\Components\Select::make('review_frequency')
                                    ->label('Review Frequency')
                                    ->options([
                                        'weekly' => 'Weekly',
                                        'bi_weekly' => 'Bi-weekly',
                                        'monthly' => 'Monthly',
                                    ])
                                    ->default('weekly')
                                    ->required(),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('initiated_by')
                                    ->label('Initiated By')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Name of person initiating PIP'),
                                Forms\Components\TextInput::make('supervisor_assigned')
                                    ->label('Assigned Supervisor')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Supervising manager'),
                                Forms\Components\TextInput::make('hr_representative')
                                    ->label('HR Representative')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('HR person overseeing PIP'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->default(now())
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        if ($state) {
                                            // Auto-set end date to 90 days later (standard PIP duration)
                                            $endDate = Carbon::parse($state)->addDays(90);
                                            $set('end_date', $endDate->format('Y-m-d'));
                                        }
                                    }),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->after('start_date')
                                    ->helperText('Automatically set to 90 days after start date'),
                            ]),
                    ]),

                Forms\Components\Section::make('Performance Issues')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('PIP Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Brief title describing the performance improvement focus')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('performance_concerns')
                            ->label('Performance Concerns')
                            ->required()
                            ->rows(4)
                            ->maxLength(2000)
                            ->placeholder('Detailed description of specific performance issues, behaviors, or deficiencies that led to this PIP')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('root_cause_analysis')
                            ->label('Root Cause Analysis')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Analysis of underlying causes of performance issues (skills gaps, training needs, resource constraints, etc.)')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Objectives & Success Metrics')
                    ->schema([
                        Forms\Components\Repeater::make('specific_objectives')
                            ->label('Specific Objectives')
                            ->schema([
                                Forms\Components\TextInput::make('objective')
                                    ->label('Objective')
                                    ->required()
                                    ->placeholder('Specific, measurable objective'),
                                Forms\Components\Textarea::make('description')
                                    ->label('Detailed Description')
                                    ->rows(2)
                                    ->placeholder('Detailed description of what needs to be achieved'),
                                Forms\Components\DatePicker::make('target_date')
                                    ->label('Target Completion Date')
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),
                            ])
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['objective'] ?? 'New Objective')
                            ->addActionLabel('Add Objective')
                            ->minItems(1)
                            ->defaultItems(1),

                        Forms\Components\Repeater::make('success_metrics')
                            ->label('Success Metrics')
                            ->schema([
                                Forms\Components\TextInput::make('metric')
                                    ->label('Metric Name')
                                    ->required()
                                    ->placeholder('e.g., Sales Target, Quality Score, Attendance Rate'),
                                Forms\Components\TextInput::make('current_performance')
                                    ->label('Current Performance')
                                    ->placeholder('Current level/baseline'),
                                Forms\Components\TextInput::make('target_performance')
                                    ->label('Target Performance')
                                    ->required()
                                    ->placeholder('Expected improvement target'),
                                Forms\Components\Select::make('measurement_frequency')
                                    ->label('Measurement Frequency')
                                    ->options([
                                        'daily' => 'Daily',
                                        'weekly' => 'Weekly',
                                        'bi_weekly' => 'Bi-weekly',
                                        'monthly' => 'Monthly',
                                    ])
                                    ->default('weekly'),
                            ])
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['metric'] ?? 'New Metric')
                            ->addActionLabel('Add Success Metric')
                            ->minItems(1)
                            ->defaultItems(1),
                    ]),

                Forms\Components\Section::make('Action Plan & Support')
                    ->schema([
                        Forms\Components\Repeater::make('required_actions')
                            ->label('Required Actions')
                            ->schema([
                                Forms\Components\TextInput::make('action')
                                    ->label('Action Item')
                                    ->required()
                                    ->placeholder('Specific action employee must take'),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(2)
                                    ->placeholder('Detailed description of the action'),
                                Forms\Components\DatePicker::make('due_date')
                                    ->label('Due Date')
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),
                            ])
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['action'] ?? 'New Action')
                            ->addActionLabel('Add Required Action')
                            ->minItems(1)
                            ->defaultItems(1),

                        Forms\Components\Textarea::make('support_provided')
                            ->label('Support Provided by Company')
                            ->required()
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Detail the support, resources, and assistance the company will provide')
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('training_requirements')
                            ->label('Training Requirements')
                            ->schema([
                                Forms\Components\TextInput::make('training_name')
                                    ->label('Training/Course Name')
                                    ->required(),
                                Forms\Components\TextInput::make('provider')
                                    ->label('Training Provider')
                                    ->placeholder('Internal or external provider'),
                                Forms\Components\DatePicker::make('completion_deadline')
                                    ->label('Completion Deadline')
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),
                                Forms\Components\Select::make('priority')
                                    ->label('Priority')
                                    ->options([
                                        'high' => 'High',
                                        'medium' => 'Medium',
                                        'low' => 'Low',
                                    ])
                                    ->default('medium'),
                            ])
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['training_name'] ?? 'New Training')
                            ->addActionLabel('Add Training Requirement')
                            ->defaultItems(0),

                        Forms\Components\Repeater::make('resources_allocated')
                            ->label('Resources Allocated')
                            ->schema([
                                Forms\Components\TextInput::make('resource_type')
                                    ->label('Resource Type')
                                    ->required()
                                    ->placeholder('e.g., Equipment, Software, Budget, Time'),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(2)
                                    ->placeholder('Description of resource and how it helps'),
                                Forms\Components\TextInput::make('quantity_value')
                                    ->label('Quantity/Value')
                                    ->placeholder('Amount allocated'),
                            ])
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['resource_type'] ?? 'New Resource')
                            ->addActionLabel('Add Resource')
                            ->defaultItems(0),
                    ]),

                Forms\Components\Section::make('Milestones & Consequences')
                    ->schema([
                        Forms\Components\Repeater::make('milestone_dates')
                            ->label('Important Milestones')
                            ->schema([
                                Forms\Components\TextInput::make('milestone')
                                    ->label('Milestone Description')
                                    ->required(),
                                Forms\Components\DatePicker::make('date')
                                    ->label('Target Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),
                                Forms\Components\Textarea::make('criteria')
                                    ->label('Success Criteria')
                                    ->rows(2)
                                    ->placeholder('What defines success for this milestone'),
                            ])
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['milestone'] ?? 'New Milestone')
                            ->addActionLabel('Add Milestone')
                            ->defaultItems(0),

                        Forms\Components\Textarea::make('consequences_of_failure')
                            ->label('Consequences of Failure to Improve')
                            ->required()
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Clearly state what will happen if performance does not improve (e.g., termination, demotion, role change)')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Status & Acknowledgment')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('PIP Status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'active' => 'Active',
                                        'under_review' => 'Under Review',
                                        'successful' => 'Successful',
                                        'unsuccessful' => 'Unsuccessful',
                                        'terminated' => 'Terminated',
                                        'extended' => 'Extended',
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->live(),
                                Forms\Components\Toggle::make('employee_acknowledgment')
                                    ->label('Employee Acknowledgment')
                                    ->helperText('Has the employee acknowledged receipt of this PIP?')
                                    ->default(false),
                            ]),
                        Forms\Components\DatePicker::make('completion_date')
                            ->label('Completion Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->visible(fn(callable $get) => in_array($get('status'), ['successful', 'unsuccessful', 'terminated']))
                            ->after('start_date'),
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
                            ->visible(fn(callable $get) => in_array($get('status'), ['successful', 'unsuccessful', 'terminated'])),
                        Forms\Components\Textarea::make('employee_comments')
                            ->label('Employee Comments')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Employee response or comments about the PIP')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('supervisor_notes')
                            ->label('Supervisor Notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Supervisor observations and progress notes')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('hr_notes')
                            ->label('HR Notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('HR notes and administrative observations')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Supporting Documents')
                    ->schema([
                        Forms\Components\FileUpload::make('supporting_documents')
                            ->label('Supporting Documents')
                            ->multiple()
                            ->directory('pip-documents')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                            ])
                            ->maxSize(5120)
                            ->helperText('Upload any supporting documents (performance records, training materials, etc.). Max 5MB each.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.employee_id')
                    ->label('Employee ID')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee Name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('pip_number')
                    ->label('PIP #')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => "#{$state}"),
                Tables\Columns\BadgeColumn::make('pip_type')
                    ->label('Type')
                    ->colors([
                        'danger' => ['performance_deficiency', 'behavioral_issues'],
                        'warning' => ['attendance_problems', 'quality_standards'],
                        'info' => ['skills_gap', 'communication_issues'],
                        'gray' => ['goal_achievement', 'policy_compliance'],
                    ]),
                Tables\Columns\BadgeColumn::make('severity_level')
                    ->label('Severity')
                    ->colors([
                        'info' => 'low',
                        'warning' => 'moderate',
                        'danger' => ['high', 'critical'],
                    ]),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(25)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 25 ? $state : null;
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Days Left')
                    ->getStateUsing(fn ($record) => $record->days_remaining)
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state <= 0 => 'danger',
                        $state <= 7 => 'warning',
                        $state <= 30 => 'info',
                        default => 'success'
                    })
                    ->visible(fn ($record) => $record && $record->status === 'active'),
                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Progress')
                    ->getStateUsing(fn ($record) => round($record->progress_percentage) . '%')
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        (int) str_replace('%', '', $state) >= 75 => 'warning',
                        (int) str_replace('%', '', $state) >= 50 => 'info',
                        default => 'success'
                    })
                    ->visible(fn ($record) => $record && $record->status === 'active'),
                Tables\Columns\TextColumn::make('initiated_by')
                    ->label('Initiated By')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => ['active', 'extended'],
                        'info' => 'under_review',
                        'success' => 'successful',
                        'danger' => ['unsuccessful', 'terminated'],
                    ])
                    ->icons([
                        'heroicon-o-pencil' => 'draft',
                        'heroicon-o-play' => 'active',
                        'heroicon-o-magnifying-glass' => 'under_review',
                        'heroicon-o-check-circle' => 'successful',
                        'heroicon-o-x-circle' => 'unsuccessful',
                        'heroicon-o-stop' => 'terminated',
                        'heroicon-o-arrow-path' => 'extended',
                    ]),
                Tables\Columns\IconColumn::make('employee_acknowledgment')
                    ->label('Acknowledged')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('pip_type')
                    ->label('PIP Type')
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
                    ->multiple(),
                Tables\Filters\SelectFilter::make('severity_level')
                    ->label('Severity Level')
                    ->options([
                        'low' => 'Low',
                        'moderate' => 'Moderate',
                        'high' => 'High',
                        'critical' => 'Critical',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'under_review' => 'Under Review',
                        'successful' => 'Successful',
                        'unsuccessful' => 'Unsuccessful',
                        'terminated' => 'Terminated',
                        'extended' => 'Extended',
                    ])
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('employee_acknowledgment')
                    ->label('Employee Acknowledged')
                    ->placeholder('All PIPs')
                    ->trueLabel('Acknowledged only')
                    ->falseLabel('Not acknowledged only'),
                Tables\Filters\Filter::make('active_pips')
                    ->label('Active PIPs')
                    ->toggle()
                    ->query(fn ($query) => $query->where('status', 'active')),
                Tables\Filters\Filter::make('overdue_pips')
                    ->label('Overdue PIPs')
                    ->toggle()
                    ->query(fn ($query) => $query->where('status', 'active')->where('end_date', '<', now())),
                Tables\Filters\Filter::make('start_date')
                    ->form([
                        Forms\Components\DatePicker::make('start_from')
                            ->label('Start From')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('start_until')
                            ->label('Start Until')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['start_from'], fn ($q) => $q->where('start_date', '>=', $data['start_from']))
                            ->when($data['start_until'], fn ($q) => $q->where('start_date', '<=', $data['start_until']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['start_from'] ?? null) {
                            $indicators['start_from'] = 'Start from ' . Carbon::parse($data['start_from'])->toFormattedDateString();
                        }
                        if ($data['start_until'] ?? null) {
                            $indicators['start_until'] = 'Start until ' . Carbon::parse($data['start_until'])->toFormattedDateString();
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
                    Tables\Actions\Action::make('activate')
                        ->label('Activate PIP')
                        ->icon('heroicon-o-play')
                        ->color('warning')
                        ->after(fn($record)=>Mail::to($record->employee->email)->send(new PIPEmail($record)))
                        ->visible(fn ($record) => $record && $record->status === 'draft')
                        ->action(function ($record) {
                            $record->update(['status' => 'active']);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Activate PIP')
                        ->modalDescription('This will activate the Performance Improvement Plan and begin the improvement period.'),
                    Tables\Actions\Action::make('mark_successful')
                        ->label('Mark Successful')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record) => $record && in_array($record->status, ['active', 'under_review']))
                        ->form([
                            Forms\Components\DatePicker::make('completion_date')
                                ->label('Completion Date')
                                ->required()
                                ->default(now())
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                            Forms\Components\Textarea::make('final_notes')
                                ->label('Final Notes')
                                ->rows(3)
                                ->placeholder('Notes about successful completion'),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'status' => 'successful',
                                'final_outcome' => 'successful_completion',
                                'completion_date' => $data['completion_date'],
                                'hr_notes' => ($record->hr_notes ? $record->hr_notes . "\n\n" : '') .
                                    "SUCCESSFUL COMPLETION: " . $data['final_notes'],
                            ]);
                        }),
                    Tables\Actions\Action::make('extend_pip')
                        ->label('Extend PIP')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->visible(fn ($record) => $record && $record->status === 'active')
                        ->form([
                            Forms\Components\DatePicker::make('new_end_date')
                                ->label('New End Date')
                                ->required()
                                ->after(fn ($record) => $record->end_date)
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                            Forms\Components\Textarea::make('extension_reason')
                                ->label('Reason for Extension')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'end_date' => $data['new_end_date'],
                                'status' => 'extended',
                                'supervisor_notes' => ($record->supervisor_notes ? $record->supervisor_notes . "\n\n" : '') .
                                    "PIP EXTENDED: " . $data['extension_reason'],
                            ]);
                        }),
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
                        ->modalHeading('Delete PIPs')
                        ->modalDescription('Are you sure you want to delete these PIPs? This action cannot be undone.'),
                    Tables\Actions\BulkAction::make('activate_bulk')
                        ->label('Activate PIPs')
                        ->icon('heroicon-o-play')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'draft') {
                                    $record->update(['status' => 'active']);
                                }
                            });
                        })
                        ->requiresConfirmation(),
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
                Components\Section::make('PIP Overview')
                    ->schema([
                        Components\Split::make([
                            Components\Grid::make(2)
                                ->schema([
                                    Components\Group::make([
                                        Components\TextEntry::make('employee.employee_id')
                                            ->label('Employee ID')
                                            ->badge()
                                            ->color('primary'),
                                        Components\TextEntry::make('employee.full_name')
                                            ->label('Employee Name')
                                            ->size(Components\TextEntry\TextEntrySize::Large)
                                            ->weight('bold'),
                                        Components\TextEntry::make('pip_number')
                                            ->label('PIP Number')
                                            ->formatStateUsing(fn($state) => "PIP #{$state}")
                                            ->badge()
                                            ->color('info'),
                                        Components\TextEntry::make('severity_level')
                                            ->badge()
                                            ->label('Severity Level')
                                            ->colors([
                                                'info' => 'low',
                                                'warning' => 'moderate',
                                                'danger' => ['high', 'critical'],
                                            ]),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('pip_type')
                                            ->badge()
                                            ->label('PIP Type'),
                                        Components\TextEntry::make('status')
                                            ->badge()
                                            ->colors([
                                                'gray' => 'draft',
                                                'warning' => ['active', 'extended'],
                                                'info' => 'under_review',
                                                'success' => 'successful',
                                                'danger' => ['unsuccessful', 'terminated'],
                                            ]),
                                        Components\TextEntry::make('initiated_by')
                                            ->label('Initiated By')
                                            ->icon('heroicon-o-user'),
                                        Components\IconEntry::make('employee_acknowledgment')
                                            ->label('Employee Acknowledged')
                                            ->boolean()
                                            ->trueIcon('heroicon-o-check-circle')
                                            ->falseIcon('heroicon-o-x-circle')
                                            ->trueColor('success')
                                            ->falseColor('danger'),
                                    ]),
                                ]),
                        ]),
                    ]),

                Components\Section::make('Timeline & Progress')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('start_date')
                                    ->label('Start Date')
                                    ->date('F j, Y')
                                    ->icon('heroicon-o-play'),
                                Components\TextEntry::make('end_date')
                                    ->label('End Date')
                                    ->date('F j, Y')
                                    ->icon('heroicon-o-stop'),
                                Components\TextEntry::make('review_frequency')
                                    ->label('Review Frequency')
                                    ->formatStateUsing(fn($state) => ucfirst($state))
                                    ->icon('heroicon-o-clock'),
                            ]),
                        Components\TextEntry::make('days_remaining')
                            ->label('Days Remaining')
                            ->getStateUsing(fn($record) => $record->days_remaining . ' days')
                            ->badge()
                            ->color(fn($record) => match (true) {
                                $record->days_remaining <= 0 => 'danger',
                                $record->days_remaining <= 7 => 'warning',
                                default => 'info'
                            })
                            ->visible(fn ($record) => $record && $record->status === 'active'),
                        Components\TextEntry::make('progress_percentage')
                            ->label('Time Progress')
                            ->getStateUsing(fn($record) => round($record->progress_percentage) . '%')
                            ->badge()
                            ->visible(fn ($record) => $record && $record->status === 'active'),
                    ])
                    ->collapsible(),

                Components\Section::make('Performance Issues & Analysis')
                    ->schema([
                        Components\TextEntry::make('title')
                            ->label('PIP Title')
                            ->columnSpanFull(),
                        Components\TextEntry::make('performance_concerns')
                            ->label('Performance Concerns')
                            ->columnSpanFull(),
                        Components\TextEntry::make('root_cause_analysis')
                            ->label('Root Cause Analysis')
                            ->placeholder('No root cause analysis provided')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Components\Section::make('Support & Resources')
                    ->schema([
                        Components\TextEntry::make('support_provided')
                            ->label('Support Provided')
                            ->columnSpanFull(),
                        Components\TextEntry::make('supervisor_assigned')
                            ->label('Assigned Supervisor')
                            ->icon('heroicon-o-user'),
                        Components\TextEntry::make('hr_representative')
                            ->label('HR Representative')
                            ->icon('heroicon-o-building-office'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Components\Section::make('Employee Response & Notes')
                    ->schema([
                        Components\TextEntry::make('employee_comments')
                            ->label('Employee Comments')
                            ->placeholder('No employee comments')
                            ->columnSpanFull(),
                        Components\TextEntry::make('supervisor_notes')
                            ->label('Supervisor Notes')
                            ->placeholder('No supervisor notes')
                            ->columnSpanFull(),
                        Components\TextEntry::make('hr_notes')
                            ->label('HR Notes')
                            ->placeholder('No HR notes')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerformanceImprovementPlans::route('/'),
            'create' => Pages\CreatePerformanceImprovementPlan::route('/create'),
            'view' => Pages\ViewPerformanceImprovementPlan::route('/{record}'),
            'edit' => Pages\EditPerformanceImprovementPlan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('employee');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'employee.full_name', 'employee.employee_id', 'initiated_by', 'supervisor_assigned'];
    }
}
