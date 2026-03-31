<?php

namespace App\Console\Commands;

use App\Models\Car;
use App\Models\User;
use App\Models\VehicleServiceSchedule;
use App\Notifications\VehicleMaintenanceNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckVehicleMaintenance extends Command
{
    protected $signature = 'maintenance:check';
    protected $description = 'Check vehicle mileage and service dates against configured schedules to generate maintenance alerts.';

    public function handle()
    {
        $this->info("═══════════════════════════════════════════════════");
        $this->info("  Periodic Vehicle Service Tracker — Checking...");
        $this->info("═══════════════════════════════════════════════════");

        $schedules = VehicleServiceSchedule::where('is_active', true)->get();

        if ($schedules->isEmpty()) {
            $this->warn("No active service schedules found. Creating defaults...");
            $this->createDefaultSchedules();
            $schedules = VehicleServiceSchedule::where('is_active', true)->get();
        }

        $this->info("Active schedules: {$schedules->count()}");
        $schedules->each(fn($s) => $this->line("  ▸ {$s->name} ({$s->schedule_type})"));

        $cars = Car::whereNotIn('status', ['maintenance', 'decommissioned'])->get();
        $admins = User::all();
        $alertsGenerated = 0;

        $this->newLine();
        $this->info("Checking {$cars->count()} vehicle(s)...");
        $this->newLine();

        foreach ($cars as $car) {
            $carAlerted = false;

            foreach ($schedules as $schedule) {
                if (!$schedule->isDueForCar($car)) continue;

                $overdue = $schedule->overdueAmount($car);
                $overdueLabel = $schedule->schedule_type === 'mileage'
                    ? number_format($overdue) . ' km overdue'
                    : $overdue . ' days overdue';

                $msg = "{$schedule->name} — {$overdueLabel}. Schedule a service immediately.";

                foreach ($admins as $admin) {
                    $admin->notify(new VehicleMaintenanceNotification(
                        $car,
                        $schedule->service_category,
                        $msg
                    ));
                }

                $alertsGenerated++;
                $this->warn("  ⚠ {$car->plate_number}: {$schedule->name} ({$overdueLabel})");
                $carAlerted = true;
            }

            if (!$carAlerted) {
                $this->line("  ✓ {$car->plate_number}: All schedules within limits.");
            }
        }

        $this->newLine();
        $this->info("═══════════════════════════════════════════════════");
        $this->info("  Complete! Total alerts generated: {$alertsGenerated}");
        $this->info("═══════════════════════════════════════════════════");
    }

    /**
     * Seed default service schedules if none exist.
     */
    private function createDefaultSchedules(): void
    {
        $defaults = [
            [
                'name' => 'Every 5,000 km Routine Service',
                'schedule_type' => 'mileage',
                'mileage_interval' => 5000,
                'month_interval' => null,
                'service_category' => 'routine',
                'description' => 'Oil change, filter replacement, fluid top-up, basic inspection.',
            ],
            [
                'name' => 'Every 3 Months Inspection',
                'schedule_type' => 'time_based',
                'mileage_interval' => null,
                'month_interval' => 3,
                'service_category' => 'inspection',
                'description' => 'Brake check, tyre inspection, lights, wipers, battery test.',
            ],
            [
                'name' => 'Every 6 Months Major Service',
                'schedule_type' => 'time_based',
                'mileage_interval' => null,
                'month_interval' => 6,
                'service_category' => 'major',
                'description' => 'Complete mechanical overhaul, transmission check, engine diagnostics, full fluid replacement.',
            ],
        ];

        foreach ($defaults as $data) {
            VehicleServiceSchedule::create($data);
        }

        $this->info("Created 3 default service schedules.");
    }
}
