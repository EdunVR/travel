<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliator_id',
        'package_id',
        'ip_address',
        'user_agent',
        'referrer_url',
        'landing_page',
        'commission_amount',
        'status',
        'clicked_at',
    ];

    protected $casts = [
        'commission_amount' => 'decimal:2',
        'clicked_at' => 'datetime',
    ];

    public function affiliator()
    {
        return $this->belongsTo(Affiliator::class);
    }

    public function package()
    {
        return $this->belongsTo(TravelPackage::class, 'package_id');
    }
}
