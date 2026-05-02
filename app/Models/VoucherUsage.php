<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_voucher',
        'id_jamaah_booking',
        'discount_amount',
        'original_amount',
        'final_amount',
        'used_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'used_at' => 'datetime',
    ];

    // Relationships
    public function voucher()
    {
        return $this->belongsTo(AffiliateVoucher::class, 'id_voucher');
    }

    public function booking()
    {
        return $this->belongsTo(JamaahBooking::class, 'id_jamaah_booking');
    }
}
