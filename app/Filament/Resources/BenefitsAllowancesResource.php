<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BenefitsAllowancesResource\Pages;
use App\Filament\Resources\BenefitsAllowancesResource\RelationManagers;
use App\Models\BenefitsAllowances;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BenefitsAllowancesResource extends Resource
{
    protected static ?string $model = BenefitsAllowances::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'id')
                    ->required(),
                Forms\Components\TextInput::make('internet_allowance')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('medical_allowance')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('home_office_setup')
                    ->required()
                    ->numeric()
                    ->default(1000),
                Forms\Components\Toggle::make('home_office_setup_claimed')
                    ->required(),
                Forms\Components\DatePicker::make('laptop_issued_date'),
                Forms\Components\TextInput::make('laptop_model'),
                Forms\Components\TextInput::make('laptop_serial'),
                Forms\Components\Toggle::make('birthday_allowance_claimed')
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->numeric()
                    ->default(2025),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('internet_allowance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('medical_allowance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('home_office_setup')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('home_office_setup_claimed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('laptop_issued_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('laptop_model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('laptop_serial')
                    ->searchable(),
                Tables\Columns\IconColumn::make('birthday_allowance_claimed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListBenefitsAllowances::route('/'),
            'create' => Pages\CreateBenefitsAllowances::route('/create'),
            'edit' => Pages\EditBenefitsAllowances::route('/{record}/edit'),
        ];
    }
}
