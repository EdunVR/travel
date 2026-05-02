<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliatePayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliator_id',
        'payout_reference',
        'amount',
        'payment_method',
        'status',
        'payment_details',
        'notes',
        'requested_at',
        'processed_at',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function affiliator()
    {
        return $this->belongsTo(Affiliator::class);
    }

    public static function generateReference()
    {
        return 'PAYOUT-' . strtoupper(uniqid());
    }
}
