<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Car extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'office_id',
        'make',
        'model',
        'year',
        'plate_number',
        'registration_number',
        'price_per_day',
        'status',
        'image_url',
        'mileage',
        'last_service_date',
        'last_service_mileage',
        'bolo_expiry_date',
        'inspection_expiry_date',
        'bolo_certificate_path',
        'inspection_certificate_path',
    ];

    protected $casts = [
        'mileage' => 'integer',
        'last_service_mileage' => 'integer',
        'last_service_date' => 'date',
        'bolo_expiry_date' => 'date',
        'inspection_expiry_date' => 'date',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function serviceLogs()
    {
        return $this->hasMany(VehicleServiceLog::class)->orderByDesc('service_date');
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    /**
     * Get km since last service.
     */
    public function getKmSinceServiceAttribute(): int
    {
        return max(0, $this->mileage - $this->last_service_mileage);
    }

    /**
     * Get days since last service.
     */
    public function getDaysSinceServiceAttribute(): ?int
    {
        if (!$this->last_service_date) return null;
        return (int) Carbon::parse($this->last_service_date)->diffInDays(now());
    }

    /**
     * Get the overall service health status.
     * Returns: good, warning, overdue
     */
    public function getServiceHealthAttribute(): string
    {
        $schedules = VehicleServiceSchedule::where('is_active', true)->get();

        foreach ($schedules as $schedule) {
            if ($schedule->isDueForCar($this)) {
                return 'overdue';
            }
        }

        // Warning if approaching any threshold (within 80%)
        foreach ($schedules as $schedule) {
            if ($schedule->schedule_type === 'mileage' && $schedule->mileage_interval) {
                $pct = $this->km_since_service / $schedule->mileage_interval;
                if ($pct >= 0.8) return 'warning';
            }
            if ($schedule->schedule_type === 'time_based' && $schedule->month_interval && $this->last_service_date) {
                $nextDue = Carbon::parse($this->last_service_date)->addMonths($schedule->month_interval);
                $totalDays = Carbon::parse($this->last_service_date)->diffInDays($nextDue);
                $daysLeft = max(0, (int) now()->diffInDays($nextDue, false));
                if ($totalDays > 0 && ($daysLeft / $totalDays) <= 0.2) return 'warning';
            }
        }

        return 'good';
    }

    /**
     * Get all overdue schedules for this car.
     */
    public function getOverdueSchedules(): \Illuminate\Support\Collection
    {
        return VehicleServiceSchedule::where('is_active', true)
            ->get()
            ->filter(fn($schedule) => $schedule->isDueForCar($this))
            ->sortByDesc(fn($schedule) => $schedule->overdueAmount($this));
    }
    /**
     * Get days remaining for Bolo.
     */
    public function getBoloDaysRemainingAttribute(): ?int
    {
        if (!$this->bolo_expiry_date) return null;
        return (int) now()->diffInDays($this->bolo_expiry_date, false);
    }

    /**
     * Get Bolo Status (good, warning, overdue)
     */
    public function getBoloStatusAttribute(): string
    {
        $days = $this->bolo_days_remaining;
        if ($days === null) return 'none';
        if ($days <= 0) return 'overdue';
        if ($days <= 60) return 'warning';
        return 'good';
    }

    /**
     * Get days remaining for Inspection.
     */
    public function getInspectionDaysRemainingAttribute(): ?int
    {
        if (!$this->inspection_expiry_date) return null;
        return (int) now()->diffInDays($this->inspection_expiry_date, false);
    }

    /**
     * Get Inspection Status (good, warning, overdue)
     */
    public function getInspectionStatusAttribute(): string
    {
        $days = $this->inspection_days_remaining;
        if ($days === null) return 'none';
        if ($days <= 0) return 'overdue';
        if ($days <= 60) return 'warning';
        return 'good';
    }

    /**
     * Get overall fleet status including services and legal requirements.
     */
    public function getOverallStatusAttribute(): string
    {
        if ($this->service_health === 'overdue' || $this->bolo_status === 'overdue' || $this->inspection_status === 'overdue') {
            return 'danger';
        }
        if ($this->service_health === 'warning' || $this->bolo_status === 'warning' || $this->inspection_status === 'warning') {
            return 'warning';
        }
        return 'success';
    }
}
