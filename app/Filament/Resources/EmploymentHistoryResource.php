<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmploymentHistoryResource\Pages;
use App\Filament\Resources\EmploymentHistoryResource\RelationManagers;
use App\Models\EmploymentHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmploymentHistoryResource extends Resource
{
    protected static ?string $model = EmploymentHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'id')
                    ->required(),
                Forms\Components\DatePicker::make('joining_date')
                    ->required(),
                Forms\Components\DatePicker::make('probation_end_date')
                    ->required(),
                Forms\Components\TextInput::make('initial_department')
                    ->required(),
                Forms\Components\TextInput::make('initial_role')
                    ->required(),
                Forms\Components\TextInput::make('initial_grade')
                    ->required(),
                Forms\Components\TextInput::make('reporting_manager')
                    ->required(),
                Forms\Components\TextInput::make('current_department')
                    ->required(),
                Forms\Components\TextInput::make('current_role')
                    ->required(),
                Forms\Components\TextInput::make('current_grade')
                    ->required(),
                Forms\Components\TextInput::make('current_manager')
                    ->required(),
                Forms\Components\TextInput::make('initial_salary')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('current_salary')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('employment_type')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('joining_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('probation_end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('initial_department')
                    ->searchable(),
                Tables\Columns\TextColumn::make('initial_role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('initial_grade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reporting_manager')
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_department')
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_grade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_manager')
                    ->searchable(),
                Tables\Columns\TextColumn::make('initial_salary')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_salary')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employment_type')
                    ->searchable(),
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
            'index' => Pages\ListEmploymentHistories::route('/'),
            'create' => Pages\CreateEmploymentHistory::route('/create'),
            'edit' => Pages\EditEmploymentHistory::route('/{record}/edit'),
        ];
    }
}
