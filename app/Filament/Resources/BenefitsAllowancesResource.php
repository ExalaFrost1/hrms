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
                            ->searchable(['name', 'email', 'id']) // Make it searchable by name, email, and ID
                            ->placeholder('Select an employee')
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->minValue(2020)
                            ->maxValue(2030)
                            ->default(date('Y'))
                            ->helperText('The year these benefits apply to'),
                    ])->columns(2),

                Forms\Components\Section::make('Monthly Allowances')
                    ->description('Recurring monthly allowances for the employee')
                    ->schema([
                        Forms\Components\TextInput::make('internet_allowance')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(10000)
                            ->prefix('PKR')
                            ->helperText('Monthly internet allowance'),
                        Forms\Components\TextInput::make('medical_allowance')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(50000)
                            ->prefix('PKR')
                            ->helperText('Monthly medical allowance'),
                    ])->columns(2),

                Forms\Components\Section::make('Home Office Setup')
                    ->description('One-time home office setup allowance and status')
                    ->schema([
                        Forms\Components\TextInput::make('home_office_setup')
                            ->required()
                            ->numeric()
                            ->default(1000)
                            ->minValue(0)
                            ->maxValue(5000)
                            ->prefix('$')
                            ->helperText('One-time home office setup allowance amount'),
                        Forms\Components\Toggle::make('home_office_setup_claimed')
                            ->required()
                            ->default(false)
                            ->helperText('Has the employee claimed this allowance?'),
                    ])->columns(2),

                Forms\Components\Section::make('Laptop Information')
                    ->description('Company laptop details and issuance information')
                    ->schema([
                        Forms\Components\DatePicker::make('laptop_issued_date')
                            ->maxDate(now())
                            ->helperText('Date when laptop was issued to employee'),
                        Forms\Components\TextInput::make('laptop_model')
                            ->maxLength(255)
                            ->helperText('Model of the issued laptop'),
                        Forms\Components\TextInput::make('laptop_serial')
                            ->maxLength(255)
                            ->helperText('Unique serial number of the laptop'),
                    ])->columns(3),

                Forms\Components\Section::make('Other Benefits')
                    ->schema([
                        Forms\Components\Toggle::make('birthday_allowance_claimed')
                            ->required()
                            ->default(false)
                            ->helperText('Has the employee claimed their birthday allowance this year?'),
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
                    ->formatStateUsing(fn (string $state): string => (string) $state)
                    ->sortable()
                    ->badge()
                    ->color('primary'),
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
                    ->label('Home Allowance Claimed')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('laptop_issued_date')
                    ->label('Laptop Issued')
                    ->date('M d, Y')
                    ->sortable()
                    ->placeholder('Not issued'),
                Tables\Columns\TextColumn::make('laptop_model')
                    ->label('Laptop Model')
                    ->searchable()
                    ->limit(20)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 20 ? $state : null;
                    }),
                Tables\Columns\TextColumn::make('laptop_serial')
                    ->label('Serial #')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Serial number copied!')
                    ->fontFamily('mono')
                    ->limit(15),
                Tables\Columns\IconColumn::make('birthday_allowance_claimed')
                    ->label('Birthday')
                    ->boolean()
                    ->trueIcon('heroicon-o-gift')
                    ->falseIcon('heroicon-o-gift')
                    ->trueColor('success')
                    ->falseColor('gray'),
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
                Tables\Filters\Filter::make('has_laptop')
                    ->label('Has Laptop')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('laptop_issued_date'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            ->defaultSort('created_at', 'desc')
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
