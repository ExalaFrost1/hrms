<?php
// Improved CompensationHistoryResource.php with better validation and consistency

namespace App\Filament\Resources;

use App\Filament\Resources\CompensationHistoryResource\Pages;
use App\Models\CompensationHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CompensationHistoryResource extends Resource
{
    protected static ?string $model = CompensationHistory::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?string $navigationLabel = 'Compensation History';
    protected static ?string $modelLabel = 'Compensation Record';
    protected static ?string $pluralModelLabel = 'Compensation Records';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Employee Information')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship(
                                'employee',
                                'full_name',
                                fn ($query) => $query->orderBy('full_name')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->employee_id} - {$record->full_name}")
                            ->searchable(['full_name', 'email', 'employee_id'])
                            ->preload()
                            ->required()
                            ->placeholder('Select an employee'),
                    ]),

                Forms\Components\Section::make('Compensation Details')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('effective_date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->default(now())
                                    ->maxDate(now()),

                                Forms\Components\Select::make('action_type')
                                    ->options([
                                        'joining' => 'Joining',
                                        'increment' => 'Increment',
                                        'promotion' => 'Promotion',
                                        'bonus' => 'Bonus',
                                        'adjustment' => 'Adjustment',
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->placeholder('Select action type'),

                                Forms\Components\TextInput::make('approved_by')
                                    ->label('Approved By')
                                    ->placeholder('Name of approver')
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
                                    ->placeholder('0')
                                    ->visible(fn ($get) => !in_array($get('action_type'), ['joining', 'bonus']))
                                    ->required(fn ($get) => in_array($get('action_type'), ['increment', 'promotion']) && $get('action_type')),

                                Forms\Components\TextInput::make('new_salary')
                                    ->label('New Salary')
                                    ->numeric()
                                    ->prefix('PKR')
                                    ->step(1)
                                    ->minValue(0)
                                    ->placeholder('0')
                                    ->visible(fn ($get) => !in_array($get('action_type'), ['bonus', 'adjustment']))
                                    ->required(fn ($get) => in_array($get('action_type'), ['joining', 'increment', 'promotion']))
                                    ->rules([
                                        fn ($get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                            if (in_array($get('action_type'), ['increment', 'promotion']) &&
                                                $get('previous_salary') &&
                                                $value &&
                                                $value <= $get('previous_salary')) {
                                                $fail('New salary must be greater than previous salary for increments and promotions.');
                                            }
                                        },
                                    ]),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('bonus_amount')
                                    ->label('Bonus Amount')
                                    ->numeric()
                                    ->prefix('PKR')
                                    ->step(1)
                                    ->minValue(0)
                                    ->placeholder('0')
                                    ->visible(fn ($get) => in_array($get('action_type'), ['bonus', 'promotion']))
                                    ->required(fn ($get) => $get('action_type') === 'bonus'),

                                Forms\Components\TextInput::make('incentive_amount')
                                    ->label('Incentive Amount')
                                    ->numeric()
                                    ->prefix('PKR')
                                    ->step(1)
                                    ->minValue(0)
                                    ->placeholder('0'),

                                Forms\Components\TextInput::make('adjustment_amount')
                                    ->label('Adjustment Amount')
                                    ->numeric()
                                    ->prefix('PKR')
                                    ->step(1)
                                    ->placeholder('0')
                                    ->helperText('Positive for increase, negative for decrease')
                                    ->visible(fn ($get) => $get('action_type') === 'adjustment')
                                    ->required(fn ($get) => $get('action_type') === 'adjustment'),
                            ]),
                    ]),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('remarks')
                            ->label('Remarks/Notes')
                            ->placeholder('Add any additional notes or comments...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->maxLength(1000),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.employee_id')
                    ->label('Emp ID')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee Name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('effective_date')
                    ->label('Effective Date')
                    ->date('M d, Y')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('action_type')
                    ->label('Action Type')
                    ->colors([
                        'success' => 'joining',
                        'primary' => 'increment',
                        'warning' => 'promotion',
                        'info' => 'bonus',
                        'secondary' => 'adjustment',
                    ])
                    ->searchable(),

                Tables\Columns\TextColumn::make('previous_salary')
                    ->label('Previous Salary')
                    ->money('PKR')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('new_salary')
                    ->label('New Salary')
                    ->money('PKR')
                    ->sortable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('bonus_amount')
                    ->label('Bonus')
                    ->money('PKR')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('incentive_amount')
                    ->label('Incentive')
                    ->money('PKR')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('adjustment_amount')
                    ->label('Adjustment')
                    ->money('PKR')
                    ->sortable()
                    ->toggleable()
                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'secondary'))
                    ->placeholder('N/A'),

                Tables\Columns\TextColumn::make('approved_by')
                    ->label('Approved By')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('N/A'),

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
                Tables\Filters\SelectFilter::make('action_type')
                    ->label('Action Type')
                    ->options([
                        'joining' => 'Joining',
                        'increment' => 'Increment',
                        'promotion' => 'Promotion',
                        'bonus' => 'Bonus',
                        'adjustment' => 'Adjustment',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('employee')
                    ->relationship('employee', 'full_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\Filter::make('effective_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('effective_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('effective_date', '<=', $date),
                            );
                    }),
            ], Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->label('Actions')
                    ->color('gray')
                    ->button()
                    ->size('sm'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('effective_date', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompensationHistories::route('/'),
            'create' => Pages\CreateCompensationHistory::route('/create'),
            'edit' => Pages\EditCompensationHistory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['employee']);
    }
}
