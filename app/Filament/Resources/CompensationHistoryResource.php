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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'id')
                    ->required(),
                Forms\Components\DatePicker::make('effective_date')
                    ->required(),
                Forms\Components\TextInput::make('action_type')
                    ->required(),
                Forms\Components\TextInput::make('new_salary')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('previous_salary')
                    ->numeric(),
                Forms\Components\TextInput::make('bonus_amount')
                    ->numeric(),
                Forms\Components\TextInput::make('incentive_amount')
                    ->numeric(),
                Forms\Components\Textarea::make('remarks')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('approved_by'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('effective_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('action_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('new_salary')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('previous_salary')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bonus_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('incentive_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_by')
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
            'index' => Pages\ListCompensationHistories::route('/'),
            'create' => Pages\CreateCompensationHistory::route('/create'),
            'edit' => Pages\EditCompensationHistory::route('/{record}/edit'),
        ];
    }
}
