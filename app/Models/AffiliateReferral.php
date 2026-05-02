<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AffiliateFeeDistribution;

class AffiliateReferral extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliator_id',
        'booking_id',
        'package_id',
        'order_reference',
        'order_amount',
        'commission_amount',
        'commission_type',
        'commission_rate',
        'voucher_discount',
        'status',
        'termin',
        'termin_1_amount',
        'termin_2_amount',
        'termin_1_paid_at',
        'termin_2_paid_at',
        'termin_1_released',
        'termin_2_released',
        'notes',
        'order_date',
        'verified_at',
        'paid_at',
    ];

    protected $casts = [
        'order_amount'      => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_rate'   => 'decimal:2',
        'termin_1_amount'   => 'decimal:2',
        'termin_2_amount'   => 'decimal:2',
        'termin_1_released' => 'boolean',
        'termin_2_released' => 'boolean',
        'order_date'        => 'datetime',
        'verified_at'       => 'datetime',
        'paid_at'           => 'datetime',
        'termin_1_paid_at'  => 'datetime',
        'termin_2_paid_at'  => 'datetime',
    ];

    public function affiliator()
    {
        return $this->belongsTo(Affiliator::class);
    }

    public function booking()
    {
        return $this->belongsTo(JamaahBooking::class, 'booking_id');
    }

    public function package()
    {
        return $this->belongsTo(TravelPackage::class, 'package_id');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function feeDistributions()
    {
        return $this->hasMany(AffiliateFeeDistribution::class, 'referral_id');
    }

    public function getTermin1StatusLabelAttribute(): string
    {
        if ($this->termin_1_released) return 'Sudah Cair';
        return 'Belum Cair';
    }

    public function getTermin2StatusLabelAttribute(): string
    {
        if ($this->termin_2_released) return 'Sudah Cair';
        return 'Belum Cair (Tunggu Keberangkatan)';
    }
}
