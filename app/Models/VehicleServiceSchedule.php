<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class VehicleServiceSchedule extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'schedule_type',
        'mileage_interval',
        'month_interval',
        'service_category',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'mileage_interval' => 'integer',
        'month_interval' => 'integer',
    ];

    public function serviceLogs()
    {
        return $this->hasMany(VehicleServiceLog::class, 'schedule_id');
    }

    /**
     * Check if a car is due for this schedule.
     */
    public function isDueForCar(Car $car): bool
    {
        if (!$this->is_active) return false;

        if ($this->schedule_type === 'mileage' && $this->mileage_interval) {
            $mileageSinceService = $car->mileage - $car->last_service_mileage;
            return $mileageSinceService >= $this->mileage_interval;
        }

        if ($this->schedule_type === 'time_based' && $this->month_interval && $car->last_service_date) {
            $nextDue = \Carbon\Carbon::parse($car->last_service_date)->addMonths($this->month_interval);
            return $nextDue->isPast();
        }

        return false;
    }

    /**
     * How urgent: returns overdue days/km for sorting.
     */
    public function overdueAmount(Car $car): int
    {
        if ($this->schedule_type === 'mileage' && $this->mileage_interval) {
            return max(0, ($car->mileage - $car->last_service_mileage) - $this->mileage_interval);
        }

        if ($this->schedule_type === 'time_based' && $this->month_interval && $car->last_service_date) {
            $nextDue = \Carbon\Carbon::parse($car->last_service_date)->addMonths($this->month_interval);
            return max(0, (int) $nextDue->diffInDays(now(), false));
        }

        return 0;
    }

    public function getCategoryColorAttribute(): string
    {
        return match($this->service_category) {
            'routine' => '#3b82f6',
            'inspection' => '#f59e0b',
            'major' => '#ef4444',
            default => '#6b7280',
        };
    }
}
