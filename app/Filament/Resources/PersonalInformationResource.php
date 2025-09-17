<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalInformationResource\Pages;
use App\Models\PersonalInformation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PersonalInformationResource extends Resource
{
    protected static ?string $model = PersonalInformation::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Personal Information';

    protected static ?string $navigationGroup = 'Employee Management';

    protected static ?string $modelLabel = 'Personal Information';

    protected static ?string $pluralModelLabel = 'Personal Information';

    protected static ?int $navigationSort = 2;

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
                            ->preload()
                            ->required()
                            ->placeholder('Select an employee')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Personal Details')
                    ->schema([
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->required()
                            ->maxDate(now()->subYears(16)) // Minimum working age
                            ->reactive()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('age', now()->diffInYears($state));
                                }
                            }),

                        Forms\Components\TextInput::make('age')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false) // Don't save this field as it's auto-calculated
                            ->helperText('Automatically calculated from date of birth'),

                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female'
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('marital_status')
                            ->options([
                                'single' => 'Single',
                                'married' => 'Married',
                                'divorced' => 'Divorced',
                                'widowed' => 'Widowed',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->required()
                            ->placeholder('+92 300 1234567')
                            ->helperText('Include country code'),

                        Forms\Components\TextInput::make('personal_email')
                            ->email()
                            ->placeholder('john.doe@example.com'),

                        Forms\Components\Textarea::make('residential_address')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('city')
                            ->required(),

                        Forms\Components\TextInput::make('state')
                            ->required()
                            ->placeholder('Punjab, Sindh, KPK, Balochistan'),

                        Forms\Components\TextInput::make('postal_code')
                            ->required()
                            ->placeholder('12345'),

                        Forms\Components\TextInput::make('country')
                            ->required()
                            ->default('Pakistan'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Emergency Contact')
                    ->schema([
                        Forms\Components\TextInput::make('emergency_contact_name')
                            ->required()
                            ->placeholder('Full name of emergency contact'),

                        Forms\Components\TextInput::make('emergency_contact_relationship')
                            ->required()
                            ->placeholder('Father, Mother, Spouse, Sibling, etc.'),

                        Forms\Components\TextInput::make('emergency_contact_phone')
                            ->tel()
                            ->required()
                            ->placeholder('+92 300 1234567'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Government Documents')
                    ->schema([
                        Forms\Components\TextInput::make('national_id')
                            ->required()
                            ->placeholder('12345-6789012-3')
                            ->unique(ignoreRecord: true)
                            ->helperText('Format: 12345-6789012-3'),

                        Forms\Components\TextInput::make('passport_number')
                            ->placeholder('AB1234567')
                            ->helperText('Optional - if available'),

                        Forms\Components\TextInput::make('tax_number')
                            ->placeholder('1234567-8')
                            ->helperText('Optional - if available'),
                    ])
                    ->columns(3),
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
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(fn (?int $state): string => $state ? abs($state) . ' years' : 'N/A'),

                Tables\Columns\TextColumn::make('gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'blue',
                        'female' => 'pink',
                        'other' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('marital_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'single' => 'gray',
                        'married' => 'success',
                        'divorced' => 'warning',
                        'widowed' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-phone'),

                Tables\Columns\TextColumn::make('personal_email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-envelope')
                    ->limit(30),

                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('state')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('country')
                    ->searchable()
                    ->toggleable()
                    ->default('Pakistan'),

                Tables\Columns\TextColumn::make('national_id')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female'
                    ]),

                Tables\Filters\SelectFilter::make('marital_status')
                    ->options([
                        'single' => 'Single',
                        'married' => 'Married',
                        'divorced' => 'Divorced',
                        'widowed' => 'Widowed',
                    ]),

                Tables\Filters\SelectFilter::make('state')
                    ->options([
                        'Punjab' => 'Punjab',
                        'Sindh' => 'Sindh',
                        'KPK' => 'KPK',
                        'Balochistan' => 'Balochistan',
                        'Gilgit-Baltistan' => 'Gilgit-Baltistan',
                        'AJK' => 'AJK',
                        'ICT' => 'ICT',
                    ]),

                Tables\Filters\Filter::make('age_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('age_from')
                                    ->numeric()
                                    ->placeholder('From'),
                                Forms\Components\TextInput::make('age_to')
                                    ->numeric()
                                    ->placeholder('To'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['age_from'],
                                fn (Builder $query, $age): Builder => $query->where('age', '>=', $age),
                            )
                            ->when(
                                $data['age_to'],
                                fn (Builder $query, $age): Builder => $query->where('age', '<=', $age),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['age_from'] ?? null) {
                            $indicators[] = 'Age from ' . $data['age_from'];
                        }
                        if ($data['age_to'] ?? null) {
                            $indicators[] = 'Age to ' . $data['age_to'];
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->emptyStateHeading('No personal information found')
            ->emptyStateDescription('Start by creating the first personal information record.')
            ->emptyStateIcon('heroicon-o-user-plus');
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
            'index' => Pages\ListPersonalInformation::route('/'),
            'create' => Pages\CreatePersonalInformation::route('/create'),
//            'view' => Pages\ViewPersonalInformation::route('/{record}'),
            'edit' => Pages\EditPersonalInformation::route('/{record}/edit'),
        ];
    }
}
