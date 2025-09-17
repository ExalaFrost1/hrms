<?php
// app/Filament/Resources/AppreciationResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\AppreciationResource\Pages;
use App\Mail\AppreciationEmail;
use App\Models\Appreciation;
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

class AppreciationResource extends Resource
{
    protected static ?string $model = Appreciation::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?int $navigationSort = -85;
    protected static ?string $navigationLabel = 'Employee Recognition';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Recognition Details')
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
                                                $appreciationCount = Appreciation::where('employee_id', $state)->count();
                                                $set('appreciation_number', $appreciationCount + 1);
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('appreciation_number')
                                    ->label('Recognition Number')
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Automatically generated based on employee recognition history'),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('appreciation_type')
                                    ->label('Recognition Type')
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
                                    ->required()
                                    ->searchable(),
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
                                    ->default('exceptional_performance')
                                    ->required(),
                                Forms\Components\TextInput::make('nominated_by')
                                    ->label('Nominated By')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Name of person nominating'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('achievement_date')
                                    ->label('Achievement Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->maxDate(now())
                                    ->helperText('Date when the achievement occurred'),
                                Forms\Components\DatePicker::make('recognition_date')
                                    ->label('Recognition Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->default(now())
                                    ->maxDate(now())
                                    ->helperText('Date when recognition is officially given'),
                            ]),
                    ]),

                Forms\Components\Section::make('Achievement Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Recognition Title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Brief title for this recognition')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Achievement Description')
                            ->required()
                            ->rows(4)
                            ->maxLength(2000)
                            ->placeholder('Provide detailed description of the achievement, contribution, or exceptional performance')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('impact_description')
                            ->label('Business/Team Impact')
                            ->required()
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Describe the positive impact this achievement had on the business, team, or customers')
                            ->columnSpanFull(),
                        Forms\Components\KeyValue::make('achievement_metrics')
                            ->label('Quantifiable Achievements')
                            ->keyLabel('Metric')
                            ->valueLabel('Result')
                            ->addActionLabel('Add Metric')
                            ->helperText('Add measurable results (e.g., "Sales Increase": "25%", "Cost Savings": "$10,000")')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Recognition Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('recognition_value')
                                    ->label('Recognition Value')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->helperText('Monetary value of recognition (bonus, gift card, etc.)'),
                                Forms\Components\Toggle::make('public_recognition')
                                    ->label('Public Recognition')
                                    ->helperText('Can this recognition be shared publicly?')
                                    ->default(true),
                            ]),
                        Forms\Components\TagsInput::make('skills_demonstrated')
                            ->label('Skills Demonstrated')
                            ->placeholder('Enter skills and press Enter')
                            ->helperText('Skills or competencies demonstrated in this achievement'),
                        Forms\Components\TagsInput::make('team_members_involved')
                            ->label('Team Members Involved')
                            ->placeholder('Enter names and press Enter')
                            ->helperText('Other team members who contributed to this achievement'),
                        Forms\Components\Repeater::make('peer_nominations')
                            ->label('Peer Nominations')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('nominator_name')
                                            ->label('Nominator Name')
                                            ->required(),
                                        Forms\Components\Select::make('relationship')
                                            ->label('Relationship')
                                            ->options([
                                                'peer' => 'Peer/Colleague',
                                                'direct_report' => 'Direct Report',
                                                'manager' => 'Manager',
                                                'customer' => 'Customer',
                                                'other' => 'Other',
                                            ])
                                            ->required(),
                                    ]),
                                Forms\Components\Textarea::make('nomination_text')
                                    ->label('Nomination Comments')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['nominator_name'] ?? 'New Nomination')
                            ->addActionLabel('Add Peer Nomination')
                            ->defaultItems(0),
                    ]),

                Forms\Components\Section::make('Approval & Status')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Recognition Status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'pending_approval' => 'Pending Approval',
                                        'approved' => 'Approved',
                                        'published' => 'Published',
                                        'archived' => 'Archived',
                                    ])
                                    ->default('draft')
                                    ->required()
                                    ->live(),
                                Forms\Components\TextInput::make('approved_by')
                                    ->label('Approved By')
                                    ->maxLength(255)
                                    ->visible(fn (callable $get) => in_array($get('status'), ['approved', 'published'])),
                            ]),
                        Forms\Components\DatePicker::make('publication_date')
                            ->label('Publication Date')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->visible(fn (callable $get) => $get('status') === 'published')
                            ->after('recognition_date'),
                        Forms\Components\Textarea::make('employee_response')
                            ->label('Employee Response')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Employee response or comments about the recognition')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('hr_notes')
                            ->label('HR Notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Additional HR notes or observations')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Supporting Documents')
                    ->schema([
                        Forms\Components\FileUpload::make('supporting_documents')
                            ->label('Supporting Documents')
                            ->multiple()
                            ->directory('recognition-documents')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                            ])
                            ->maxSize(5120)
                            ->helperText('Upload any supporting documents (certificates, photos, customer feedback, etc.). Max 5MB each.')
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
                Tables\Columns\TextColumn::make('appreciation_number')
                    ->label('Recognition #')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => "#{$state}"),
                Tables\Columns\BadgeColumn::make('appreciation_type')
                    ->label('Type')
                    ->colors([
                        'success' => ['spot_recognition', 'peer_nomination'],
                        'primary' => ['monthly_award', 'quarterly_award'],
                        'warning' => ['annual_award', 'milestone_celebration'],
                        'info' => ['manager_recognition', 'customer_feedback'],
                    ]),
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Category')
                    ->colors([
                        'success' => ['exceptional_performance', 'milestone_achievement'],
                        'info' => ['innovation', 'problem_solving'],
                        'warning' => ['leadership', 'mentoring'],
                        'primary' => ['teamwork', 'customer_service'],
                        'gray' => 'other',
                    ]),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                Tables\Columns\TextColumn::make('recognition_value')
                    ->label('Value')
                    ->money('USD')
                    ->placeholder('Recognition Only')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('achievement_date')
                    ->label('Achievement Date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nominated_by')
                    ->label('Nominated By')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'pending_approval',
                        'success' => 'approved',
                        'info' => 'published',
                        'gray' => 'archived',
                    ])
                    ->icons([
                        'heroicon-o-pencil' => 'draft',
                        'heroicon-o-clock' => 'pending_approval',
                        'heroicon-o-check-circle' => 'approved',
                        'heroicon-o-megaphone' => 'published',
                        'heroicon-o-archive-box' => 'archived',
                    ]),
                Tables\Columns\IconColumn::make('public_recognition')
                    ->label('Public')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('appreciation_type')
                    ->label('Recognition Type')
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
                    ->multiple(),
                Tables\Filters\SelectFilter::make('category')
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
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending_approval' => 'Pending Approval',
                        'approved' => 'Approved',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('public_recognition')
                    ->label('Public Recognition')
                    ->placeholder('All recognitions')
                    ->trueLabel('Public only')
                    ->falseLabel('Private only'),
                Tables\Filters\Filter::make('has_monetary_value')
                    ->label('Has Monetary Value')
                    ->toggle()
                    ->query(fn ($query) => $query->whereNotNull('recognition_value')->where('recognition_value', '>', 0)),
                Tables\Filters\Filter::make('achievement_date')
                    ->form([
                        Forms\Components\DatePicker::make('achievement_from')
                            ->label('Achievement From')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('achievement_until')
                            ->label('Achievement Until')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['achievement_from'], fn ($q) => $q->where('achievement_date', '>=', $data['achievement_from']))
                            ->when($data['achievement_until'], fn ($q) => $q->where('achievement_date', '<=', $data['achievement_until']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['achievement_from'] ?? null) {
                            $indicators['achievement_from'] = 'Achievement from ' . Carbon::parse($data['achievement_from'])->toFormattedDateString();
                        }
                        if ($data['achievement_until'] ?? null) {
                            $indicators['achievement_until'] = 'Achievement until ' . Carbon::parse($data['achievement_until'])->toFormattedDateString();
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
                    Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record) => $record->status === 'pending_approval')
                        ->after(fn ($record) => Mail::to($record->employee->email)->send(new AppreciationEmail($record)))
                        ->form([
                            Forms\Components\TextInput::make('approved_by')
                                ->label('Approved By')
                                ->required()
                                ->default(auth()->user()->name ?? 'HR Manager'),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'status' => 'approved',
                                'approved_by' => $data['approved_by'],
                            ]);
                        }),
                    Tables\Actions\Action::make('publish')
                        ->label('Publish')
                        ->icon('heroicon-o-megaphone')
                        ->color('info')
                        ->visible(fn ($record) => $record->status === 'approved')
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'published',
                                'publication_date' => now(),
                            ]);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Publish Recognition')
                        ->modalDescription('This will make the recognition visible to the organization.'),
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
                        ->modalHeading('Delete Recognitions')
                        ->modalDescription('Are you sure you want to delete these recognitions? This action cannot be undone.'),
                    Tables\Actions\BulkAction::make('approve_bulk')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\TextInput::make('approved_by')
                                ->label('Approved By')
                                ->required()
                                ->default(auth()->user()->name ?? 'HR Manager'),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                if ($record->status === 'pending_approval') {
                                    $record->update([
                                        'status' => 'approved',
                                        'approved_by' => $data['approved_by'],
                                    ]);
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
                Components\Section::make('Recognition Overview')
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
                                        Components\TextEntry::make('appreciation_number')
                                            ->label('Recognition Number')
                                            ->formatStateUsing(fn ($state) => "Recognition #{$state}")
                                            ->badge()
                                            ->color('success'),
                                        Components\TextEntry::make('category')
                                            ->badge()
                                            ->label('Category'),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('appreciation_type')
                                            ->label('Recognition Type'),
                                        Components\TextEntry::make('status')
                                            ->badge()
                                            ->colors([
                                                'gray' => 'draft',
                                                'warning' => 'pending_approval',
                                                'success' => 'approved',
                                                'info' => 'published',
                                                'gray' => 'archived',
                                            ]),
                                        Components\TextEntry::make('nominated_by')
                                            ->badge()
                                            ->label('Nominated By')
                                            ->icon('heroicon-o-user'),
                                        Components\IconEntry::make('public_recognition')
                                            ->label('Public Recognition')
                                            ->boolean()
                                            ->trueIcon('heroicon-o-eye')
                                            ->falseIcon('heroicon-o-eye-slash')
                                            ->trueColor('success')
                                            ->falseColor('gray'),
                                    ]),
                                ]),
                        ]),
                    ]),

                Components\Section::make('Achievement Details')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('achievement_date')
                                    ->label('Achievement Date')
                                    ->date('F j, Y')
                                    ->icon('heroicon-o-calendar'),
                                Components\TextEntry::make('recognition_date')
                                    ->label('Recognition Date')
                                    ->date('F j, Y')
                                    ->icon('heroicon-o-trophy'),
                            ]),
                        Components\TextEntry::make('title')
                            ->label('Title')
                            ->columnSpanFull(),
                        Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        Components\TextEntry::make('impact_description')
                            ->label('Business Impact')
                            ->columnSpanFull(),
                        Components\TextEntry::make('recognition_value')
                            ->label('Recognition Value')
                            ->money('USD')
                            ->placeholder('Recognition Only'),
                    ])
                    ->collapsible(),

                Components\Section::make('Skills & Team Involvement')
                    ->schema([
                        Components\TextEntry::make('skills_demonstrated')
                            ->label('Skills Demonstrated')
                            ->listWithLineBreaks()
                            ->placeholder('No specific skills listed'),
                        Components\TextEntry::make('team_members_involved')
                            ->label('Team Members Involved')
                            ->listWithLineBreaks()
                            ->placeholder('Individual achievement'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Components\Section::make('Employee Response & HR Notes')
                    ->schema([
                        Components\TextEntry::make('employee_response')
                            ->label('Employee Response')
                            ->placeholder('No employee response')
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
            'index' => Pages\ListAppreciations::route('/'),
            'create' => Pages\CreateAppreciation::route('/create'),
            'view' => Pages\ViewAppreciation::route('/{record}'),
            'edit' => Pages\EditAppreciation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('employee');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'employee.full_name', 'employee.employee_id', 'nominated_by'];
    }
}
