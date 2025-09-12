<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetManagementResource\Pages;
use App\Filament\Resources\AssetManagementResource\RelationManagers;
use App\Models\AssetManagement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetManagementResource extends Resource
{
    protected static ?string $model = AssetManagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Employee Management';

    public static function form(Form $form): Form
    {
        return $form
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

                Forms\Components\TextInput::make('asset_type')
                    ->required()
                    ->placeholder('e.g., laptop, desktop, phone, tablet, monitor'),

                Forms\Components\TextInput::make('asset_name')
                    ->required()
                    ->placeholder('e.g., MacBook Pro, Dell OptiPlex, iPhone 14'),

                Forms\Components\TextInput::make('model')
                    ->placeholder('e.g., MacBook Pro 16-inch, OptiPlex 7090, iPhone 14 Pro'),

                Forms\Components\TextInput::make('serial_number')
                    ->required()
                    ->placeholder('Enter unique serial number')
                    ->unique(ignoreRecord: true),

                Forms\Components\DatePicker::make('issued_date')
                    ->required()
                    ->placeholder('Select issue date'),

                Forms\Components\DatePicker::make('return_date')
                    ->placeholder('Select return date (if applicable)'),

                Forms\Components\Select::make('condition_when_issued')
                    ->required()
                    ->options([
                        'new' => 'New',
                        'good' => 'Good',
                        'fair' => 'Fair',
                        'poor' => 'Poor',
                    ])
                    ->placeholder('Select condition when issued'),

                Forms\Components\Select::make('condition_when_returned')
                    ->options([
                        'good' => 'Good',
                        'fair' => 'Fair',
                        'poor' => 'Poor',
                        'damaged' => 'Damaged',
                    ])
                    ->placeholder('Select condition when returned (if applicable)'),

                Forms\Components\TextInput::make('purchase_value')
                    ->numeric()
                    ->prefix('PKR')
                    ->placeholder('0.00')
                    ->step(100),

                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull()
                    ->placeholder('Add any additional notes or comments about the asset'),

                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'issued' => 'Issued',
                        'returned' => 'Returned',
                        'damaged' => 'Damaged',
                        'lost' => 'Lost',
                    ])
                    ->default('issued')
                    ->placeholder('Select asset status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.id')
                    ->label('Emp ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('asset_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('asset_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issued_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('condition_when_issued')
                    ->colors([
                        'success' => 'new',
                        'primary' => 'good',
                        'warning' => 'fair',
                        'danger' => 'poor',
                    ]),
                Tables\Columns\BadgeColumn::make('condition_when_returned')
                    ->colors([
                        'primary' => 'good',
                        'warning' => 'fair',
                        'danger' => ['poor', 'damaged'],
                    ]),
                Tables\Columns\TextColumn::make('purchase_value')
                    ->money('PKR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'issued',
                        'primary' => 'returned',
                        'danger' => ['damaged', 'lost'],
                    ]),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'issued' => 'Issued',
                        'returned' => 'Returned',
                        'damaged' => 'Damaged',
                        'lost' => 'Lost',
                    ]),
                Tables\Filters\SelectFilter::make('condition_when_issued')
                    ->options([
                        'new' => 'New',
                        'good' => 'Good',
                        'fair' => 'Fair',
                        'poor' => 'Poor',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('bulk_mark_returned')
                        ->label('Mark as Returned')
                        ->icon('heroicon-o-arrow-left-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\DatePicker::make('return_date')
                                ->label('Return Date')
                                ->default(now())
                                ->required(),
                            Forms\Components\Select::make('condition_when_returned')
                                ->label('Condition When Returned')
                                ->options([
                                    'good' => 'Good',
                                    'fair' => 'Fair',
                                    'poor' => 'Poor',
                                    'damaged' => 'Damaged',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function (AssetManagement $record) use ($data) {
                                if ($record->status === 'issued') {
                                    $record->update([
                                        'return_date' => $data['return_date'],
                                        'condition_when_returned' => $data['condition_when_returned'],
                                        'status' => 'returned',
                                    ]);
                                }
                            });
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\ExportBulkAction::make()
                        ->label('Export Selected'),
                ]),
            ])
            ->emptyStateHeading('No assets found')
            ->emptyStateDescription('Get started by creating your first asset record.')
            ->emptyStateIcon('heroicon-o-computer-desktop')
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
            'index' => Pages\ListAssetManagement::route('/'),
            'create' => Pages\CreateAssetManagement::route('/create'),
            'edit' => Pages\EditAssetManagement::route('/{record}/edit'),
        ];
    }
}
