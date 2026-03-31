<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\VehicleServiceLog;
use App\Models\VehicleServiceSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VehicleServiceTrackerController extends Controller
{
    /**
     * Main tracker dashboard — shows all vehicles with their service health.
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $schedules = VehicleServiceSchedule::where('is_active', true)->get();

        $carsQuery = Car::with('office')->where('status', '!=', 'decommissioned');

        $cars = $carsQuery->get()->map(function ($car) use ($schedules) {
            $car->health = $car->service_health;
            $car->overdue_schedules = $car->getOverdueSchedules();
            return $car;
        });

        if ($filter === 'overdue') {
            $cars = $cars->filter(fn($c) => $c->health === 'overdue');
        } elseif ($filter === 'warning') {
            $cars = $cars->filter(fn($c) => $c->health === 'warning');
        } elseif ($filter === 'good') {
            $cars = $cars->filter(fn($c) => $c->health === 'good');
        }

        // Sort: overdue first, then warning, then good
        $cars = $cars->sortBy(function ($car) {
            return match($car->health) {
                'overdue' => 0,
                'warning' => 1,
                'good' => 2,
                default => 3,
            };
        });

        $stats = [
            'total' => Car::where('status', '!=', 'decommissioned')->count(),
            'overdue' => $cars->where('health', 'overdue')->count(),
            'warning' => $cars->where('health', 'warning')->count(),
            'good' => $cars->where('health', 'good')->count(),
        ];

        return view('admin.service-tracker.index', compact('cars', 'schedules', 'stats', 'filter'));
    }

    /**
     * Show service history and details for a specific vehicle.
     */
    public function show(Car $car)
    {
        $car->load(['office', 'serviceLogs.schedule', 'serviceLogs.performedBy']);
        $schedules = VehicleServiceSchedule::where('is_active', true)->get();
        $overdueSchedules = $car->getOverdueSchedules();

        return view('admin.service-tracker.show', compact('car', 'schedules', 'overdueSchedules'));
    }

    /**
     * Log a new service entry for a vehicle.
     */
    public function logService(Request $request, Car $car)
    {
        $validated = $request->validate([
            'schedule_id' => 'nullable|exists:vehicle_service_schedules,id',
            'service_type' => 'required|in:routine,inspection,major,repair',
            'description' => 'nullable|string|max:1000',
            'service_provider' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'mileage_at_service' => 'required|integer|min:0',
            'service_date' => 'required|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        $validated['car_id'] = $car->id;
        $validated['performed_by'] = auth()->id();
        $validated['status'] = 'completed';

        // Calculate next service date/mileage based on schedule
        if ($validated['schedule_id']) {
            $schedule = VehicleServiceSchedule::find($validated['schedule_id']);
            if ($schedule) {
                if ($schedule->schedule_type === 'mileage' && $schedule->mileage_interval) {
                    $validated['next_service_mileage'] = $validated['mileage_at_service'] + $schedule->mileage_interval;
                }
                if ($schedule->schedule_type === 'time_based' && $schedule->month_interval) {
                    $validated['next_service_date'] = Carbon::parse($validated['service_date'])->addMonths($schedule->month_interval);
                }
            }
        }

        VehicleServiceLog::create($validated);

        // Update the car's tracking fields
        $car->update([
            'mileage' => max($car->mileage, $validated['mileage_at_service']),
            'last_service_date' => $validated['service_date'],
            'last_service_mileage' => $validated['mileage_at_service'],
        ]);

        return redirect()->route('admin.service-tracker.show', $car)
            ->with('success', 'Service logged successfully. Vehicle tracking updated.');
    }

    /**
     * Update mileage for a vehicle (quick mileage update).
     */
    public function updateMileage(Request $request, Car $car)
    {
        $validated = $request->validate([
            'mileage' => 'required|integer|min:' . $car->mileage,
        ]);

        $car->update(['mileage' => $validated['mileage']]);

        return redirect()->route('admin.service-tracker.show', $car)
            ->with('success', 'Mileage updated to ' . number_format($validated['mileage']) . ' km.');
    }

    // ─── Service Schedule CRUD ────────────────────────────────────

    public function schedules()
    {
        $schedules = VehicleServiceSchedule::latest()->get();
        return view('admin.service-tracker.schedules', compact('schedules'));
    }

    public function createSchedule()
    {
        return view('admin.service-tracker.schedule-create');
    }

    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'schedule_type' => 'required|in:mileage,time_based',
            'mileage_interval' => 'required_if:schedule_type,mileage|nullable|integer|min:100',
            'month_interval' => 'required_if:schedule_type,time_based|nullable|integer|min:1',
            'service_category' => 'required|in:routine,inspection,major',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['is_active'] = true;

        VehicleServiceSchedule::create($validated);

        return redirect()->route('admin.service-tracker.schedules')
            ->with('success', 'Service schedule rule created.');
    }

    public function toggleSchedule(VehicleServiceSchedule $schedule)
    {
        $schedule->update(['is_active' => !$schedule->is_active]);
        $status = $schedule->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.service-tracker.schedules')
            ->with('success', "Schedule \"{$schedule->name}\" {$status}.");
    }

    public function destroySchedule(VehicleServiceSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.service-tracker.schedules')
            ->with('success', 'Schedule rule deleted.');
    }
}
