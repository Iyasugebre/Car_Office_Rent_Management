<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AgreementRenewal extends Model
{
    use HasUuids;

    protected $fillable = [
        'agreement_id',
        'action',
        'status',
        'new_monthly_rent',
        'new_start_date',
        'new_end_date',
        'new_payment_schedule',
        'reason',
        'amendment_notes',
        'approved_by',
        'approved_at',
        'approval_remarks',
    ];

    protected $casts = [
        'approved_at'   => 'datetime',
        'new_start_date'=> 'date',
        'new_end_date'  => 'date',
    ];

    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function actionLabel(): string
    {
        return match($this->action) {
            'renew'     => 'Renewal',
            'amend'     => 'Amendment',
            'terminate' => 'Termination',
            default     => ucfirst($this->action),
        };
    }

    public function actionColor(): string
    {
        return match($this->action) {
            'renew'     => '#10b981',
            'amend'     => '#3b82f6',
            'terminate' => '#ef4444',
            default     => '#6b7280',
        };
    }
}
