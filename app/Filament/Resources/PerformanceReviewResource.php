<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerformanceReviewResource\Pages;
use App\Filament\Resources\PerformanceReviewResource\RelationManagers;
use App\Models\PerformanceReview;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PerformanceReviewResource extends Resource
{
    protected static ?string $model = PerformanceReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'id')
                    ->required(),
                Forms\Components\TextInput::make('review_period')
                    ->required(),
                Forms\Components\DatePicker::make('review_date')
                    ->required(),
                Forms\Components\TextInput::make('goal_completion_rate')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('overall_rating')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('manager_feedback')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('peer_feedback')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('self_assessment')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('areas_of_strength')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('areas_for_improvement')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('development_goals')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('reviewed_by')
                    ->required(),
                Forms\Components\TextInput::make('status')
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
                Tables\Columns\TextColumn::make('review_period')
                    ->searchable(),
                Tables\Columns\TextColumn::make('review_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('goal_completion_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('overall_rating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewed_by')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
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
            'index' => Pages\ListPerformanceReviews::route('/'),
            'create' => Pages\CreatePerformanceReview::route('/create'),
            'edit' => Pages\EditPerformanceReview::route('/{record}/edit'),
        ];
    }
}
