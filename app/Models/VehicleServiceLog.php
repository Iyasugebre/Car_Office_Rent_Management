<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class VehicleServiceLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'car_id',
        'schedule_id',
        'performed_by',
        'service_type',
        'description',
        'service_provider',
        'cost',
        'mileage_at_service',
        'service_date',
        'next_service_date',
        'next_service_mileage',
        'status',
        'notes',
    ];

    protected $casts = [
        'service_date' => 'date',
        'next_service_date' => 'date',
        'cost' => 'decimal:2',
        'mileage_at_service' => 'integer',
        'next_service_mileage' => 'integer',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function schedule()
    {
        return $this->belongsTo(VehicleServiceSchedule::class, 'schedule_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => '#10b981',
            'scheduled' => '#3b82f6',
            'overdue' => '#ef4444',
            'in_progress' => '#8b5cf6',
            default => '#6b7280',
        };
    }

    public function getServiceTypeColorAttribute(): string
    {
        return match($this->service_type) {
            'routine' => '#3b82f6',
            'inspection' => '#f59e0b',
            'major' => '#ef4444',
            'repair' => '#8b5cf6',
            default => '#6b7280',
        };
    }
}
