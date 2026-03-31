<?php

namespace App\Filament\Widgets;

use App\Models\Car;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class VehicleServiceAlertsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Car::query()
                    ->where('status', '!=', 'decommissioned')
                    ->where(function (Builder $query) {
                        $query->whereRaw('(mileage - COALESCE((SELECT MAX(mileage_at_service) FROM vehicle_service_logs WHERE car_id = cars.id), 0)) >= (SELECT MIN(mileage_interval) FROM vehicle_service_schedules WHERE is_active = 1)')
                            ->orWhereRaw('(julianday("now") - julianday(COALESCE((SELECT MAX(service_date) FROM vehicle_service_logs WHERE car_id = cars.id), cars.created_at))) >= (SELECT MIN(month_interval) * 30 FROM vehicle_service_schedules WHERE is_active = 1)');
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('plate_number')
                    ->label('Vehicle')
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('make')
                    ->formatStateUsing(fn ($record) => $record->make . ' ' . $record->model),
                Tables\Columns\TextColumn::make('mileage')
                    ->label('Current Mileage')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' km'),
                Tables\Columns\TextColumn::make('service_health')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'overdue' => 'danger',
                        'warning' => 'warning',
                        'good' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('km_since_service')
                    ->label('Since Service')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' km')
                    ->description(fn ($record) => $record->days_since_service . ' days')
                    ->color(fn ($record) => $record->service_health === 'overdue' ? 'danger' : 'warning'),
            ])
            ->actions([
                Actions\Action::make('logService')
                    ->label('Service Now')
                    ->icon('heroicon-o-wrench')
                    ->color('primary')
                    ->url(fn (Car $record): string => route('filament.admin.resources.cars.edit', $record)),
            ])
            ->emptyStateHeading('All vehicles on track')
            ->emptyStateDescription('No vehicles currently due for service.');
    }
}
