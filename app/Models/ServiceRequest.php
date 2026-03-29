<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'car_id',
        'requester_id',
        'fleet_manager_id',
        'problem_description',
        'service_type',
        'urgency_level',
        'status',
        'service_provider',
        'cost',
        'service_report_path',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function fleetManager()
    {
        return $this->belongsTo(User::class, 'fleet_manager_id');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => '#f59e0b',
            'approved' => '#3b82f6',
            'in_progress' => '#8b5cf6',
            'completed' => '#10b981',
            'rejected' => '#ef4444',
            default => '#6b7280',
        };
    }

    public function getUrgencyColorAttribute()
    {
        return match($this->urgency_level) {
            'low' => '#6b7280',
            'medium' => '#f59e0b',
            'high' => '#ef4444',
            'critical' => '#991b1b',
            default => '#6b7280',
        };
    }
}
