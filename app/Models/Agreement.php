<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agreement extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'branch_request_id',
        'agreement_id',
        'landlord_name',
        'property_address',
        'monthly_rent',
        'payment_schedule',
        'start_date',
        'end_date',
        'contract_path',
        'status',
        'legal_status',
        'legal_reviewer_id',
        'legal_reviewed_at',
        'legal_remarks',
    ];

    public function branchRequest()
    {
        return $this->belongsTo(BranchRequest::class);
    }

    public function legalReviewer()
    {
        return $this->belongsTo(User::class, 'legal_reviewer_id');
    }

    public function renewals()
    {
        return $this->hasMany(AgreementRenewal::class);
    }
}
