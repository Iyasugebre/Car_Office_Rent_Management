<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'office_id',
        'make',
        'model',
        'year',
        'plate_number',
        'price_per_day',
        'status',
        'image_url',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
