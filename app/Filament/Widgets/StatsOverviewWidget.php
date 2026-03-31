<?php

namespace App\Filament\Widgets;

use App\Models\Car;
use App\Models\Office;
use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Cars', Car::count())
                ->description('Fleet inventory')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
            Stat::make('Total Offices', Office::count())
                ->description('Active locations')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('secondary'),
            Stat::make('Active Bookings', Booking::where('status', 'active')->count())
                ->description('Check for updates')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),
            Stat::make('Total Revenue', 'ETB ' . number_format(Booking::sum('total_price'), 2))
                ->description('+15% from last month')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
