<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerformanceReviewResource\Pages;
use App\Models\PerformanceReview;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;

class PerformanceReviewResource extends Resource
{
    protected static ?string $model = PerformanceReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Performance Reviews';

    protected static ?string $modelLabel = 'Performance Review';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Review Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
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
                                    ->columnSpanFull()
                                    ->helperText('Select the employee being reviewed'),

                                Forms\Components\TextInput::make('reviewed_by')
                                    ->label('Reviewed By')
                                    ->required()
                                    ->placeholder('Enter reviewer name')
                                    ->helperText('Manager or supervisor conducting the review'),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('review_period')
                                    ->label('Review Period')
                                    ->required()
                                    ->placeholder('e.g., Q1 2024, Annual 2024')
                                    ->helperText('The period this review covers'),

                                Forms\Components\DatePicker::make('review_date')
                                    ->label('Review Date')
                                    ->required()
                                    ->default(now())
                                    ->maxDate(now()),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'submitted' => 'Submitted',
                                        'approved' => 'Approved',
                                    ])
                                    ->default('draft')
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('Performance Metrics')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('goal_completion_rate')
                                    ->label('Goal Completion Rate (%)')
                                    ->numeric()
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.01)
                                    ->helperText('Percentage of goals completed'),

                                Forms\Components\TextInput::make('overall_rating')
                                    ->label('Overall Rating')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(5)
                                    ->step(0.1)
                                    ->helperText('Rating from 1.0 to 5.0'),
                            ]),
                    ]),

                Forms\Components\Section::make('Feedback & Assessment')
                    ->schema([
                        Forms\Components\Textarea::make('manager_feedback')
                            ->label('Manager Feedback')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('peer_feedback')
                            ->label('Peer Feedback')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('self_assessment')
                            ->label('Self Assessment')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Development Areas')
                    ->schema([
                        Forms\Components\Textarea::make('areas_of_strength')
                            ->label('Areas of Strength')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('areas_for_improvement')
                            ->label('Areas for Improvement')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('development_goals')
                            ->label('Development Goals')
                            ->rows(3)
                            ->columnSpanFull(),
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
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('review_period')
                    ->label('Review Period')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('review_date')
                    ->label('Review Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviewed_by')
                    ->label('Reviewed By')
                    ->searchable(),

                Tables\Columns\TextColumn::make('overall_rating')
                    ->label('Rating')
                    ->numeric(decimalPlaces: 1)
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state >= 4.0 => 'success',
                        $state >= 3.0 => 'info',
                        $state >= 2.0 => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('goal_completion_rate')
                    ->label('Goal Completion')
                    ->formatStateUsing(fn ($state) => $state ? $state . '%' : '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'approved' => 'success',
                    }),

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
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                    ])
                    ->multiple(),

                SelectFilter::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'id')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('overall_rating')
                    ->label('Rating Range')
                    ->options([
                        'excellent' => '4.0+ (Excellent)',
                        'good' => '3.0-3.9 (Good)',
                        'average' => '2.0-2.9 (Average)',
                        'poor' => '1.0-1.9 (Needs Improvement)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] === 'excellent',
                            fn (Builder $query): Builder => $query->where('overall_rating', '>=', 4.0)
                        )->when(
                            $data['value'] === 'good',
                            fn (Builder $query): Builder => $query->whereBetween('overall_rating', [3.0, 3.9])
                        )->when(
                            $data['value'] === 'average',
                            fn (Builder $query): Builder => $query->whereBetween('overall_rating', [2.0, 2.9])
                        )->when(
                            $data['value'] === 'poor',
                            fn (Builder $query): Builder => $query->whereBetween('overall_rating', [1.0, 1.9])
                        );
                    }),

                Filter::make('review_date')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('review_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('review_date', '<=', $date),
                            );
                    }),

                Filter::make('goal_completion_rate')
                    ->label('Goal Completion Rate')
                    ->form([
                        Forms\Components\Select::make('completion_range')
                            ->options([
                                'high' => '90%+ (High Performance)',
                                'medium' => '70-89% (Good Performance)',
                                'low' => 'Below 70% (Needs Improvement)',
                            ])
                            ->placeholder('Select completion range'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['completion_range'] === 'high',
                            fn (Builder $query): Builder => $query->where('goal_completion_rate', '>=', 90)
                        )->when(
                            $data['completion_range'] === 'medium',
                            fn (Builder $query): Builder => $query->whereBetween('goal_completion_rate', [70, 89])
                        )->when(
                            $data['completion_range'] === 'low',
                            fn (Builder $query): Builder => $query->where('goal_completion_rate', '<', 70)
                        );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->tooltip('Create a copy of this review')
                    ->action(function (PerformanceReview $record) {
                        $newReview = $record->replicate();
                        $newReview->status = 'draft';
                        $newReview->review_date = now();
                        $newReview->review_period = null; // Clear to avoid conflicts
                        $newReview->save();

                        return redirect()->route('filament.admin.resources.performance-reviews.edit', $newReview);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Duplicate Performance Review')
                    ->modalDescription('This will create a copy of the review with status set to draft and today\'s date.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Performance Reviews')
                        ->modalDescription('Are you sure you want to approve the selected performance reviews?')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'approved']);
                            });
                        }),

                    Tables\Actions\BulkAction::make('submit')
                        ->label('Submit Selected')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Submit Performance Reviews')
                        ->modalDescription('Are you sure you want to submit the selected performance reviews?')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'draft') {
                                    $record->update(['status' => 'submitted']);
                                }
                            });
                        }),
                ]),
            ])
            ->defaultSort('review_date', 'desc')
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
            'index' => Pages\ListPerformanceReviews::route('/'),
            'create' => Pages\CreatePerformanceReview::route('/create'),
            'edit' => Pages\EditPerformanceReview::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'submitted')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('status', 'submitted')->count() > 0 ? 'warning' : null;
    }
}
