<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchUtility extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'office_id',
        'utility_type',
        'provider',
        'account_number',
        'payment_cycle',
        'last_paid_at',
        'next_due_at',
        'is_active',
    ];

    protected $casts = [
        'last_paid_at' => 'date',
        'next_due_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
