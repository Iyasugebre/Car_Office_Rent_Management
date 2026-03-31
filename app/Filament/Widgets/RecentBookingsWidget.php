<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentBookingsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => '#' . substr($state, 0, 8)),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer'),
                Tables\Columns\TextColumn::make('car.plate_number')
                    ->label('Vehicle'),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('ETB')
                    ->label('Amount'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Actions\ViewAction::make()
                    ->url(fn (Booking $record): string => route('filament.admin.resources.bookings.view', $record)),
            ]);
    }
}
