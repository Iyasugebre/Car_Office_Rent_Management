<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar';

    protected static string | \UnitEnum | null $navigationGroup = 'Rent Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Customer & Asset')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('car_id')
                            ->relationship('car', 'plate_number')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('office_id')
                            ->relationship('office', 'name')
                            ->label('Pickup Branch')
                            ->required(),
                    ])->columns(3),

                \Filament\Schemas\Components\Section::make('Schedule & Pricing')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->required(),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->required(),
                        Forms\Components\TextInput::make('total_price')
                            ->numeric()
                            ->prefix('ETB')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'active' => 'Active',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => '#' . substr($state, 0, 8)),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('car.plate_number')
                    ->label('Vehicle')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('ETB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'active' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('office_id')
                    ->relationship('office', 'name'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\ViewAction::make(),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
            'view' => Pages\ViewBooking::route('/{record}'),
        ];
    }
}
