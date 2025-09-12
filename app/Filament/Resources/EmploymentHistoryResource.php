<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmploymentHistoryResource\Pages;
use App\Models\EmploymentHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmploymentHistoryResource extends Resource
{
    protected static ?string $model = EmploymentHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Employment History';

    protected static ?string $modelLabel = 'Employment Record';

    protected static ?string $pluralModelLabel = 'Employment Records';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Employee Information')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship(
                                'employee',
                                'name',
                                fn ($query) => $query->orderBy('name')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->id} - {$record->full_name}")
                            ->searchable(['name', 'email', 'id']) // Make it searchable by name, email, and ID
                            ->preload()
                            ->required()
                            ->placeholder('Select an employee')
                            ->helperText('Choose the employee for this employment record'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Employment Dates')
                    ->schema([
                        Forms\Components\DatePicker::make('joining_date')
                            ->required()
                            ->label('Joining Date')
                            ->default(now())
                            ->maxDate(now()),
                        Forms\Components\DatePicker::make('probation_end_date')
                            ->required()
                            ->label('Probation End Date')
                            ->after('joining_date'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Initial Position Details')
                    ->schema([
                        Forms\Components\TextInput::make('initial_department')
                            ->required()
                            ->label('Department')
                            ->maxLength(100)
                            ->placeholder('e.g., Human Resources'),
                        Forms\Components\TextInput::make('initial_role')
                            ->required()
                            ->label('Job Role')
                            ->maxLength(100)
                            ->placeholder('e.g., Software Developer'),
                        Forms\Components\TextInput::make('initial_grade')
                            ->required()
                            ->label('Grade/Level')
                            ->maxLength(50)
                            ->placeholder('e.g., L1, Junior, Senior'),
                        Forms\Components\TextInput::make('reporting_manager')
                            ->required()
                            ->label('Reporting Manager')
                            ->maxLength(100)
                            ->placeholder('Manager name'),
                        Forms\Components\TextInput::make('initial_salary')
                            ->required()
                            ->numeric()
                            ->label('Initial Salary')
                            ->prefix('$')
                            ->minValue(0)
                            ->step(0.01),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Current Position Details')
                    ->schema([
                        Forms\Components\TextInput::make('current_department')
                            ->required()
                            ->label('Department')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('current_role')
                            ->required()
                            ->label('Job Role')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('current_grade')
                            ->required()
                            ->label('Grade/Level')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('current_manager')
                            ->required()
                            ->label('Current Manager')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('current_salary')
                            ->required()
                            ->numeric()
                            ->label('Current Salary')
                            ->prefix('$')
                            ->minValue(0)
                            ->step(0.01),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Employment Type')
                    ->schema([
                        Forms\Components\Select::make('employment_type')
                            ->required()
                            ->options([
                                'full_time' => 'Full Time',
                                'part_time' => 'Part Time',
                                'contract' => 'Contract',
                                'intern' => 'Intern',
                            ])
                            ->default('full_time')
                            ->placeholder('Select employment type'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.id')
                    ->label('Emp ID')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('joining_date')
                    ->label('Joined')
                    ->date('M j, Y')
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('current_department')
                    ->label('Department')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('current_role')
                    ->label('Current Role')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('current_grade')
                    ->label('Grade')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('current_manager')
                    ->label('Manager')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('current_salary')
                    ->label('Salary')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('employment_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'full_time' => 'success',
                        'part_time' => 'warning',
                        'contract' => 'info',
                        'intern' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                        'intern' => 'Intern',
                    }),

                Tables\Columns\TextColumn::make('probation_end_date')
                    ->label('Probation Ends')
                    ->date('M j, Y')
                    ->sortable()
                    ->toggleable()
                    ->color(fn ($record) => $record->probation_end_date > now() ? 'warning' : 'success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employment_type')
                    ->label('Employment Type')
                    ->options([
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                        'intern' => 'Intern',
                    ]),

                Tables\Filters\SelectFilter::make('current_department')
                    ->label('Department')
                    ->options(function () {
                        return \App\Models\EmploymentHistory::distinct()
                            ->pluck('current_department', 'current_department')
                            ->filter()
                            ->sort();
                    }),

                Tables\Filters\Filter::make('probation_period')
                    ->label('In Probation')
                    ->query(fn (Builder $query): Builder =>
                    $query->where('probation_end_date', '>', now())
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relation managers if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmploymentHistories::route('/'),
            'create' => Pages\CreateEmploymentHistory::route('/create'),
            'edit' => Pages\EditEmploymentHistory::route('/{record}/edit'),
        ];
    }
}
