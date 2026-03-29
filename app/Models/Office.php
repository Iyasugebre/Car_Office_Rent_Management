<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Office extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'type',
        'price_per_month',
        'status',
    ];

    public function cars()
    {
        return $this->hasMany(Car::class);
    }
}
