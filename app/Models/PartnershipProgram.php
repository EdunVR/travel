<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnershipProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'target_audience',
        'benefits',
        'registration_fee',
        'min_sale_commission',
        'default_ppc_commission',
        'requires_previous_booking',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'benefits' => 'array',
        'registration_fee' => 'decimal:2',
        'min_sale_commission' => 'decimal:2',
        'default_ppc_commission' => 'decimal:2',
        'requires_previous_booking' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function affiliators()
    {
        return $this->hasMany(Affiliator::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Accessors
    public function getFormattedFeeAttribute()
    {
        if ($this->registration_fee == 0) {
            return 'GRATIS';
        }
        return 'Rp ' . number_format($this->registration_fee, 0, ',', '.');
    }

    public function getFormattedCommissionAttribute()
    {
        return 'Rp ' . number_format($this->min_sale_commission, 0, ',', '.');
    }
}
