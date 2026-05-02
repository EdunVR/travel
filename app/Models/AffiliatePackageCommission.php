<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliatePackageCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliator_id',
        'package_id',
        'click_commission',
        'sale_commission_type',
        'sale_commission_value',
        'is_active',
    ];

    protected $casts = [
        'click_commission' => 'decimal:2',
        'sale_commission_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function package()
    {
        return $this->belongsTo(TravelPackage::class, 'package_id');
    }

    public function affiliator()
    {
        return $this->belongsTo(Affiliator::class);
    }
}
