<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchUtilityResource\Pages;
use App\Models\BranchUtility;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Carbon\Carbon;

class BranchUtilityResource extends Resource
{
    protected static ?string $model = BranchUtility::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bolt';

    protected static string | \UnitEnum | null $navigationGroup = 'Office Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('office_id')
                    ->relationship('office', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('utility_type')
                    ->options([
                        'electricity' => 'Electricity',
                        'water' => 'Water',
                        'telephone' => 'Telephone',
                        'internet' => 'Internet',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('provider')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('account_number')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('payment_cycle')
                    ->options([
                        'monthly' => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'annual' => 'Annual',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('next_due_at')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('office.name')
                    ->label('Branch')
                    ->sortable(),
                Tables\Columns\TextColumn::make('utility_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'electricity' => 'warning',
                        'water' => 'info',
                        'internet' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('provider'),
                Tables\Columns\TextColumn::make('next_due_at')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->next_due_at?->isPast() ? 'danger' : null),
                Tables\Columns\ToggleColumn::make('is_active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('office_id')
                    ->relationship('office', 'name')
                    ->label('Branch'),
                Tables\Filters\SelectFilter::make('utility_type'),
            ])
            ->actions([
                Actions\Action::make('recordPayment')
                    ->label('Log Payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (BranchUtility $record) {
                        $nextDue = match($record->payment_cycle) {
                            'monthly' => Carbon::parse($record->next_due_at)->addMonth(),
                            'quarterly' => Carbon::parse($record->next_due_at)->addMonths(3),
                            'annual' => Carbon::parse($record->next_due_at)->addYear(),
                            default => Carbon::parse($record->next_due_at)->addMonth(),
                        };

                        $record->update([
                            'last_paid_at' => now(),
                            'next_due_at' => $nextDue,
                        ]);
                    }),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranchUtilities::route('/'),
            'create' => Pages\CreateBranchUtility::route('/create'),
            'edit' => Pages\EditBranchUtility::route('/{record}/edit'),
        ];
    }
}
