<?php
// app/Filament/Resources/WarningResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\WarningResource\Pages;
use App\Models\Warning;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Carbon\Carbon;

class WarningResource extends Resource
{
    protected static ?string $model = Warning::class;
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?int $navigationSort = -90;
    protected static ?string $navigationLabel = 'Employee Warnings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Warning Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('employee_id')
                                    ->label('Employee')
                                    ->relationship('employee', 'full_name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->getOptionLabelFromRecordUsing(fn (Employee $record): string => "{$record->employee_id} - {$record->full_name}")
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        if ($state) {
                                            $employee = Employee::find($state);
                                            if ($employee) {
                                                $warningCount = Warning::where('employee_id', $state)->count();
                                                $set('warning_number', $warningCount + 1);
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('warning_number')
                                    ->label('Warning Number')
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Automatically generated based on employee warning history'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('warning_type')
                                    ->label('Warning Type')
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
                                    ->required()
                                    ->searchable(),
                                Forms\Components\Select::make('severity_level')
                                    ->label('Severity Level')
                                    ->options([
                                        'minor' => 'Minor',
                                        'moderate' => 'Moderate',
                                        'major' => 'Major',
                                        'critical' => 'Critical',
                                    ])
                                    ->default('minor')
                                    ->required(),
                                Forms\Components\TextInput::make('issued_by')
                                    ->label('Issued By')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Name of HR/Manager issuing warning'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('incident_date')
                                    ->label('Incident Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->maxDate(now())
                                    ->helperText('Date when the incident occurred'),
                                Forms\Components\DatePicker::make('warning_date')
                                    ->label('Warning Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->default(now())
                                    ->maxDate(now())
                                    ->helperText('Date when warning is officially issued'),
                            ]),
                    ]),

                Forms\Components\Section::make('Incident Information')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Brief subject of the warning')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Detailed Description')
                            ->required()
                            ->rows(4)
                            ->maxLength(2000)
                            ->placeholder('Provide detailed description of the incident, behavior, or policy violation')
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('incident_location')
                                    ->label('Incident Location')
                                    ->maxLength(255)
                                    ->placeholder('Where did the incident occur?'),
                                Forms\Components\Textarea::make('witnesses')
                                    ->label('Witnesses')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->placeholder('Names and details of any witnesses'),
                            ]),
                        Forms\Components\Textarea::make('previous_discussions')
                            ->label('Previous Discussions')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Any previous verbal warnings, discussions, or counseling sessions related to this issue')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Improvement & Consequences')
                    ->schema([
                        Forms\Components\Textarea::make('expected_improvement')
                            ->label('Expected Improvement')
                            ->required()
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Clearly state what improvement or behavioral change is expected')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('consequences_if_repeated')
                            ->label('Consequences if Behavior Repeats')
                            ->required()
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Specify what disciplinary action will be taken if the behavior continues')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('follow_up_date')
                            ->label('Follow-up Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('warning_date')
                            ->helperText('When to review progress on improvement'),
                    ]),

                Forms\Components\Section::make('Employee Response & Status')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('employee_acknowledgment')
                                    ->label('Employee Acknowledgment')
                                    ->helperText('Has the employee acknowledged receipt of this warning?')
                                    ->default(false),
                                Forms\Components\Select::make('status')
                                    ->label('Warning Status')
                                    ->options([
                                        'active' => 'Active',
                                        'acknowledged' => 'Acknowledged',
                                        'resolved' => 'Resolved',
                                        'escalated' => 'Escalated',
                                    ])
                                    ->default('active')
                                    ->required(),
                            ]),
                        Forms\Components\Textarea::make('employee_comments')
                            ->label('Employee Comments')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Employee response or comments about the warning')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('hr_notes')
                            ->label('HR Notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Additional HR notes or observations')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('resolution_date')
                            ->label('Resolution Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->visible(fn (callable $get) => in_array($get('status'), ['resolved', 'escalated']))
                            ->after('warning_date'),
                    ]),

                Forms\Components\Section::make('Supporting Documents')
                    ->schema([
                        Forms\Components\FileUpload::make('supporting_documents')
                            ->label('Supporting Documents')
                            ->multiple()
                            ->directory('warning-documents')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                            ])
                            ->maxSize(5120)
                            ->helperText('Upload any supporting documents (emails, reports, photos, etc.). Max 5MB each.')
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
                Tables\Columns\TextColumn::make('warning_number')
                    ->label('Warning #')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => "#{$state}"),
                Tables\Columns\BadgeColumn::make('warning_type')
                    ->label('Type')
                    ->colors([
                        'danger' => ['attendance', 'safety', 'harassment'],
                        'warning' => ['performance', 'conduct'],
                        'info' => ['policy_violation', 'insubordination'],
                        'gray' => 'other',
                    ]),
                Tables\Columns\BadgeColumn::make('severity_level')
                    ->label('Severity')
                    ->colors([
                        'success' => 'minor',
                        'warning' => 'moderate',
                        'danger' => ['major', 'critical'],
                    ]),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                Tables\Columns\TextColumn::make('incident_date')
                    ->label('Incident Date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('issued_by')
                    ->label('Issued By')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'active',
                        'info' => 'acknowledged',
                        'success' => 'resolved',
                        'danger' => 'escalated',
                    ])
                    ->icons([
                        'heroicon-o-exclamation-triangle' => 'active',
                        'heroicon-o-check-circle' => 'acknowledged',
                        'heroicon-o-check-badge' => 'resolved',
                        'heroicon-o-arrow-up' => 'escalated',
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
                Tables\Filters\SelectFilter::make('warning_type')
                    ->label('Warning Type')
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
                    ->multiple(),
                Tables\Filters\SelectFilter::make('severity_level')
                    ->label('Severity Level')
                    ->options([
                        'minor' => 'Minor',
                        'moderate' => 'Moderate',
                        'major' => 'Major',
                        'critical' => 'Critical',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'acknowledged' => 'Acknowledged',
                        'resolved' => 'Resolved',
                        'escalated' => 'Escalated',
                    ])
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('employee_acknowledgment')
                    ->label('Employee Acknowledged')
                    ->placeholder('All warnings')
                    ->trueLabel('Acknowledged only')
                    ->falseLabel('Not acknowledged only'),
                Tables\Filters\Filter::make('incident_date')
                    ->form([
                        Forms\Components\DatePicker::make('incident_from')
                            ->label('Incident From')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('incident_until')
                            ->label('Incident Until')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['incident_from'], fn ($q) => $q->where('incident_date', '>=', $data['incident_from']))
                            ->when($data['incident_until'], fn ($q) => $q->where('incident_date', '<=', $data['incident_until']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['incident_from'] ?? null) {
                            $indicators['incident_from'] = 'Incident from ' . Carbon::parse($data['incident_from'])->toFormattedDateString();
                        }
                        if ($data['incident_until'] ?? null) {
                            $indicators['incident_until'] = 'Incident until ' . Carbon::parse($data['incident_until'])->toFormattedDateString();
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
                    Tables\Actions\Action::make('acknowledge')
                        ->label('Mark as Acknowledged')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record) => !$record->employee_acknowledgment)
                        ->action(function ($record) {
                            $record->update([
                                'employee_acknowledgment' => true,
                                'status' => 'acknowledged'
                            ]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Mark Warning as Acknowledged')
                        ->modalDescription('This will mark the warning as acknowledged by the employee.'),
                    Tables\Actions\Action::make('resolve')
                        ->label('Mark as Resolved')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn ($record) => $record->status !== 'resolved')
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'resolved',
                                'resolution_date' => now(),
                            ]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Mark Warning as Resolved')
                        ->modalDescription('This will mark the warning as resolved.'),
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
                        ->modalHeading('Delete Warnings')
                        ->modalDescription('Are you sure you want to delete these warnings? This action cannot be undone.'),
                    Tables\Actions\BulkAction::make('acknowledge_bulk')
                        ->label('Mark as Acknowledged')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'employee_acknowledgment' => true,
                                    'status' => 'acknowledged'
                                ]);
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
                Components\Section::make('Warning Overview')
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
                                        Components\TextEntry::make('warning_number')
                                            ->label('Warning Number')
                                            ->formatStateUsing(fn ($state) => "Warning #{$state}")
                                            ->badge()
                                            ->color('warning'),
                                        Components\TextEntry::make('severity_level')
                                            ->label('Severity Level')
                                            ->badge()
                                            ->colors([
                                                'success' => 'minor',
                                                'warning' => 'moderate',
                                                'danger' => ['major', 'critical'],
                                            ]),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('warning_type')
                                            ->badge()
                                            ->label('Warning Type'),
                                        Components\TextEntry::make('status')
                                            ->badge()
                                            ->colors([
                                                'warning' => 'active',
                                                'info' => 'acknowledged',
                                                'success' => 'resolved',
                                                'danger' => 'escalated',
                                            ]),
                                        Components\TextEntry::make('issued_by')
                                            ->label('Issued By')
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

                Components\Section::make('Incident Details')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('incident_date')
                                    ->label('Incident Date')
                                    ->date('F j, Y')
                                    ->icon('heroicon-o-calendar'),
                                Components\TextEntry::make('warning_date')
                                    ->label('Warning Date')
                                    ->date('F j, Y')
                                    ->icon('heroicon-o-document-text'),
                            ]),
                        Components\TextEntry::make('subject')
                            ->label('Subject')
                            ->columnSpanFull(),
                        Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        Components\TextEntry::make('incident_location')
                            ->label('Incident Location')
                            ->placeholder('Not specified'),
                        Components\TextEntry::make('witnesses')
                            ->label('Witnesses')
                            ->placeholder('No witnesses'),
                    ])
                    ->collapsible(),

                Components\Section::make('Expected Improvements & Consequences')
                    ->schema([
                        Components\TextEntry::make('expected_improvement')
                            ->label('Expected Improvement')
                            ->columnSpanFull(),
                        Components\TextEntry::make('consequences_if_repeated')
                            ->label('Consequences if Repeated')
                            ->columnSpanFull(),
                        Components\TextEntry::make('follow_up_date')
                            ->label('Follow-up Date')
                            ->date('F j, Y')
                            ->placeholder('No follow-up date set'),
                    ])
                    ->collapsible(),

                Components\Section::make('Employee Response & HR Notes')
                    ->schema([
                        Components\TextEntry::make('employee_comments')
                            ->label('Employee Comments')
                            ->placeholder('No employee comments')
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
            'index' => Pages\ListWarnings::route('/'),
            'create' => Pages\CreateWarning::route('/create'),
            'view' => Pages\ViewWarning::route('/{record}'),
            'edit' => Pages\EditWarning::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('employee');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['subject', 'employee.full_name', 'employee.employee_id', 'issued_by'];
    }
}
