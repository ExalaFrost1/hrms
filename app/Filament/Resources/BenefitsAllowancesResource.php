<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BenefitsAllowancesResource\Pages;
use App\Models\BenefitsAllowances;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BenefitsAllowancesResource extends Resource
{
    protected static ?string $model = BenefitsAllowances::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?string $navigationLabel = 'Benefits & Allowances';

    protected static ?string $modelLabel = 'Benefits & Allowances';

    protected static ?string $pluralModelLabel = 'Benefits & Allowances';

    protected static ?int $navigationSort = 3;

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
                            ->searchable(['name', 'email', 'id'])
                            ->placeholder('Select an employee')
                            ->preload()
                            ->nullable(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('year')
                                    ->required()
                                    ->numeric()
                                    ->minValue(2020)
                                    ->maxValue(2030)
                                    ->default(date('Y'))
                                    ->helperText('The year these benefits apply to'),
                                Forms\Components\Select::make('month')
                                    ->required()
                                    ->options([
                                        1 => 'January',
                                        2 => 'February',
                                        3 => 'March',
                                        4 => 'April',
                                        5 => 'May',
                                        6 => 'June',
                                        7 => 'July',
                                        8 => 'August',
                                        9 => 'September',
                                        10 => 'October',
                                        11 => 'November',
                                        12 => 'December',
                                    ])
                                    ->default(date('n'))
                                    ->helperText('The month these benefits apply to'),
                            ]),
                    ])->columns(1),

                Forms\Components\Section::make('Monthly Allowances')
                    ->description('Recurring monthly allowances for the employee')
                    ->schema([
                        Forms\Components\TextInput::make('internet_allowance')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10000)
                            ->prefix('$')
                            ->helperText('Monthly internet allowance')
                            ->nullable(),
                        Forms\Components\TextInput::make('medical_allowance')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(50000)
                            ->prefix('$')
                            ->helperText('Monthly medical allowance')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Home Office Setup')
                    ->description('One-time home office setup allowance and status')
                    ->schema([
                        Forms\Components\TextInput::make('home_office_setup')
                            ->numeric()
                            ->default(1000.00)
                            ->minValue(0)
                            ->maxValue(5000)
                            ->prefix('$')
                            ->helperText('One-time home office setup allowance amount')
                            ->nullable(),
                        Forms\Components\Toggle::make('home_office_setup_claimed')
                            ->default(false)
                            ->helperText('Has the employee claimed this allowance?'),
                    ])->columns(2),

                Forms\Components\Section::make('Other Benefits')
                    ->schema([
                        Forms\Components\Toggle::make('birthday_allowance_claimed')
                            ->default(false)
                            ->helperText('Has the employee claimed their birthday allowance this month?'),

                        Forms\Components\Repeater::make('other_benefits')
                            ->label('Custom Benefits')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('benefit_name')
                                            ->label('Benefit Name')
                                            ->required()
                                            ->placeholder('e.g., Netflix Subscription, Gym Membership'),
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
                                    ->placeholder('Optional description or notes about this benefit')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                ($state['benefit_name'] ?? 'Custom Benefit') .
                                (isset($state['benefit_value']) ? ' - $' . $state['benefit_value'] : '')
                            )
                            ->addActionLabel('Add Custom Benefit')
                            ->defaultItems(0)
                            ->reorderable(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.id')
                    ->label('Emp ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('year')
                    ->label('Year')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('month')
                    ->label('Month')
                    ->formatStateUsing(fn (string $state): string => date('F', mktime(0, 0, 0, (int)$state, 1)))
                    ->sortable()
                    ->badge()
                    ->color('secondary'),
                Tables\Columns\TextColumn::make('internet_allowance')
                    ->label('Internet')
                    ->money('USD')
                    ->sortable()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('medical_allowance')
                    ->label('Medical')
                    ->money('USD')
                    ->sortable()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('home_office_setup')
                    ->label('Home Office')
                    ->money('USD')
                    ->sortable()
                    ->alignEnd(),
                Tables\Columns\IconColumn::make('home_office_setup_claimed')
                    ->label('Home Claimed')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\IconColumn::make('birthday_allowance_claimed')
                    ->label('Birthday')
                    ->boolean()
                    ->trueIcon('heroicon-o-gift')
                    ->falseIcon('heroicon-o-gift')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('other_benefits_count')
                    ->label('Custom Benefits')
                    ->getStateUsing(fn ($record) => $record->other_benefits ? count($record->other_benefits) : 0)
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('total_benefits')
                    ->label('Total Value')
                    ->getStateUsing(function ($record) {
                        $total = ($record->internet_allowance ?? 0) + ($record->medical_allowance ?? 0);
                        if ($record->other_benefits) {
                            foreach ($record->other_benefits as $benefit) {
                                $total += $benefit['benefit_value'] ?? 0;
                            }
                        }
                        return $total;
                    })
                    ->money('USD')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold'),
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
                Tables\Filters\SelectFilter::make('year')
                    ->options([
                        '2023' => '2023',
                        '2024' => '2024',
                        '2025' => '2025',
                        '2026' => '2026',
                    ])
                    ->default(date('Y')),
                Tables\Filters\SelectFilter::make('month')
                    ->options([
                        1 => 'January',
                        2 => 'February',
                        3 => 'March',
                        4 => 'April',
                        5 => 'May',
                        6 => 'June',
                        7 => 'July',
                        8 => 'August',
                        9 => 'September',
                        10 => 'October',
                        11 => 'November',
                        12 => 'December',
                    ])
                    ->default(date('n')),
                Tables\Filters\TernaryFilter::make('home_office_setup_claimed')
                    ->label('Home Office Setup Claimed')
                    ->boolean()
                    ->trueLabel('Claimed')
                    ->falseLabel('Not Claimed')
                    ->native(false),
                Tables\Filters\TernaryFilter::make('birthday_allowance_claimed')
                    ->label('Birthday Allowance Claimed')
                    ->boolean()
                    ->trueLabel('Claimed')
                    ->falseLabel('Not Claimed')
                    ->native(false),
                Tables\Filters\Filter::make('has_custom_benefits')
                    ->label('Has Custom Benefits')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('other_benefits'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplicate to Next Month')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('warning')
                    ->action(function ($record) {
                        $nextMonth = $record->month == 12 ? 1 : $record->month + 1;
                        $nextYear = $record->month == 12 ? $record->year + 1 : $record->year;

                        $exists = BenefitsAllowances::where('employee_id', $record->employee_id)
                            ->where('year', $nextYear)
                            ->where('month', $nextMonth)
                            ->exists();

                        if (!$exists) {
                            $newRecord = $record->replicate();
                            $newRecord->month = $nextMonth;
                            $newRecord->year = $nextYear;
                            $newRecord->home_office_setup_claimed = false;
                            $newRecord->birthday_allowance_claimed = false;

                            // Reset custom benefits claimed status
                            if ($newRecord->other_benefits) {
                                $otherBenefits = $newRecord->other_benefits;
                                foreach ($otherBenefits as &$benefit) {
                                    $benefit['is_claimed'] = false;
                                }
                                $newRecord->other_benefits = $otherBenefits;
                            }

                            $newRecord->save();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Duplicate Benefits to Next Month')
                    ->modalDescription('This will create a copy of these benefits for the next month with claim statuses reset.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_home_office_claimed')
                        ->label('Mark Home Office as Claimed')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['home_office_setup_claimed' => true]);
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('mark_birthday_claimed')
                        ->label('Mark Birthday Allowance as Claimed')
                        ->icon('heroicon-o-gift')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['birthday_allowance_claimed' => true]);
                            });
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('year', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relation managers here if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBenefitsAllowances::route('/'),
            'create' => Pages\CreateBenefitsAllowances::route('/create'),
            'edit' => Pages\EditBenefitsAllowances::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }
}
