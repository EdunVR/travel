<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateFeeDistribution extends Model
{
    protected $fillable = [
        'referral_id',
        'from_affiliator_id',
        'to_affiliator_id',
        'level_type',
        'amount',
        'percentage',
        'termin',
        'status',
        'released_at',
        'paid_at',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'percentage'  => 'decimal:2',
        'released_at' => 'datetime',
        'paid_at'     => 'datetime',
    ];

    public function referral()
    {
        return $this->belongsTo(AffiliateReferral::class, 'referral_id');
    }

    public function fromAffiliator()
    {
        return $this->belongsTo(Affiliator::class, 'from_affiliator_id');
    }

    public function toAffiliator()
    {
        return $this->belongsTo(Affiliator::class, 'to_affiliator_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReleased($query)
    {
        return $query->where('status', 'released');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
