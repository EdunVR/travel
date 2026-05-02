<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_preorder',
        'customer_id',
        'outlet_id',
        'tanggal',
        'status',
        'subtotal',
        'diskon',
        'pajak',
        'total',
        'dp_amount',
        'catatan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'pajak' => 'decimal:2',
        'total' => 'decimal:2',
        'dp_amount' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Member::class, 'customer_id', 'id_member');
    }

    public function outlet()
    {
        return $this->belongsTo(\App\Models\Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function items()
    {
        return $this->hasMany(PreOrderItem::class);
    }

    public function generateKodePreorder()
    {
        $year = now()->year;
        $month = now()->format('m');
        $romanMonth = $this->getRomanMonth($month);
        
        $lastOrder = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastOrder ? (int)substr($lastOrder->kode_preorder, 0, 3) + 1 : 1;
        
        return sprintf('%03d/DRN/OL/%s/%d', $sequence, $romanMonth, $year);
    }

    private function getRomanMonth($month)
    {
        $romans = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
            '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
            '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ];
        
        return $romans[$month];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'penawaran' => 'bg-gray-100 text-gray-800',
            'invoice' => 'bg-blue-100 text-blue-800',
            'lunas' => 'bg-green-100 text-green-800'
        ];
        
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getRemainingPaymentAttribute()
    {
        return $this->total - ($this->dp_amount ?? 0);
    }

    public function getTotalAdditionalCostsAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->calculateTotalBiayaTambahan();
        });
    }

    public function getGrandTotalWithAdditionalCostsAttribute()
    {
        return $this->total + $this->total_additional_costs;
    }

    public function getSubtotalWithAdditionalCostsAttribute()
    {
        return $this->subtotal + $this->total_additional_costs;
    }
}