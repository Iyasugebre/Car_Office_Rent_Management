<?php

namespace App\Console\Commands;

use App\Models\Car;
use App\Models\User;
use App\Notifications\VehicleLegalExpiryNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:check-vehicle-legal-expiries')]
#[Description('Check cars for Bolo and Inspection expiry and notify admins')]
class CheckVehicleLegalExpiries extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            $this->error('No admin found to notify.');
            return;
        }

        $cars = Car::all();
        $alertDays = [60, 30, 7];

        foreach ($cars as $car) {
            // Check Bolo
            if ($car->bolo_expiry_date) {
                $days = $car->bolo_days_remaining;
                
                // Alert if it matches exactly 60, 30, 7 OR is already overdue
                if (in_array($days, $alertDays) || $days <= 0) {
                    foreach ($admins as $admin) {
                        $admin->notify(new VehicleLegalExpiryNotification($car, 'bolo', $days));
                    }
                    $this->info("Notified for Bolo: {$car->plate_number} ({$days} days left)");
                }
            }

            // Check Inspection
            if ($car->inspection_expiry_date) {
                $days = $car->inspection_days_remaining;
                
                if (in_array($days, $alertDays) || $days <= 0) {
                    foreach ($admins as $admin) {
                        $admin->notify(new VehicleLegalExpiryNotification($car, 'inspection', $days));
                    }
                    $this->info("Notified for Inspection: {$car->plate_number} ({$days} days left)");
                }
            }
        }

        $this->info('Legal expiry checks completed.');
    }
}
