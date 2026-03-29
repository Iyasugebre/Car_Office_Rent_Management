<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchRequest extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'reference_number',
        'branch_name',
        'location',
        'proposed_office',
        'landlord_details',
        'estimated_rent',
        'priority',
        'status',
        'remarks',
        'requester_id',
    ];

    /**
     * Boot the model to generate the reference number.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->reference_number)) {
                $lastRequest = self::orderBy('created_at', 'desc')->first();
                $number = $lastRequest ? intval(substr($lastRequest->reference_number, 8)) + 1 : 1;
                $model->reference_number = 'BR-' . date('Y') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
}
