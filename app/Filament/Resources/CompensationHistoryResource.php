<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompensationHistoryResource\Pages;
use App\Filament\Resources\CompensationHistoryResource\RelationManagers;
use App\Models\CompensationHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompensationHistoryResource extends Resource
{
    protected static ?string $model = CompensationHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

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
                                'name',
                                fn ($query) => $query->orderBy('name')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->id} - {$record->full_name}")
                            ->searchable(['name', 'email', 'id']) // Make it searchable by name, email, and ID
                            ->preload()
                            ->required()
                            ->placeholder('Select an employee'),
                    ]),

                Forms\Components\Section::make('Compensation Details')
                    ->schema([
                        Forms\Components\DatePicker::make('effective_date')
                            ->required()
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

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('previous_salary')
                                    ->label('Previous Salary')
                                    ->numeric()
                                    ->prefix('PKR')
                                    ->placeholder('0.00')
                                    ->visible(fn ($get) => !in_array($get('action_type'), ['joining', 'bonus'])),

                                Forms\Components\TextInput::make('new_salary')
                                    ->label('New Salary')
                                    ->required()
                                    ->numeric()
                                    ->prefix('PKR')
                                    ->placeholder('0.00')
                                    ->visible(fn ($get) => !in_array($get('action_type'), ['bonus', 'adjustment'])),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('bonus_amount')
                                    ->label('Bonus Amount')
                                    ->numeric()
                                    ->prefix('PKR')
                                    ->placeholder('0.00')
                                    ->visible(fn ($get) => in_array($get('action_type'), ['bonus', 'promotion'])),

                                Forms\Components\TextInput::make('incentive_amount')
                                    ->label('Incentive Amount')
                                    ->numeric()
                                    ->prefix('PKR')
                                    ->placeholder('0.00'),

                                Forms\Components\TextInput::make('adjustment_amount')
                                    ->label('Adjustment Amount')
                                    ->numeric()
                                    ->prefix('PKR')
                                    ->placeholder('0.00')
                                    ->helperText('Positive for increase, negative for decrease')
                                    ->visible(fn ($get) => $get('action_type') === 'adjustment'),
                            ]),
                    ]),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('remarks')
                            ->label('Remarks/Notes')
                            ->placeholder('Add any additional notes or comments...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('approved_by')
                            ->label('Approved By')
                            ->placeholder('Name of approver')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.id') // Assuming employee has a name field
                ->label('Emp ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('effective_date')
                    ->label('Effective Date')
                    ->date()
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
                    ->toggleable(),

                Tables\Columns\TextColumn::make('new_salary')
                    ->label('New Salary')
                    ->money('PKR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('bonus_amount')
                    ->label('Bonus')
                    ->money('PKR')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('incentive_amount')
                    ->label('Incentive')
                    ->money('PKR')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('adjustment_amount')
                    ->label('Adjustment')
                    ->money('PKR')
                    ->sortable()
                    ->toggleable()
                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'secondary')),

                Tables\Columns\TextColumn::make('approved_by')
                    ->label('Approved By')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
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
                    ]),

                Tables\Filters\Filter::make('effective_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('effective_date', 'desc');
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
}
