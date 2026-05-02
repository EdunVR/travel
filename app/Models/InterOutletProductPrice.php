<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterOutletProductPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_produk',
        'outlet_id',
        'inter_outlet_price',
        'markup_percent'
    ];

    protected $casts = [
        'inter_outlet_price' => 'decimal:2',
        'markup_percent' => 'decimal:2'
    ];

    /**
     * Relationship to Product
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /**
     * Relationship to Outlet
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    /**
     * Get or create inter outlet price for a product
     */
    public static function getOrCreatePrice($productId, $outletId, $defaultPrice = 0)
    {
        return static::firstOrCreate(
            [
                'id_produk' => $productId,
                'outlet_id' => $outletId
            ],
            [
                'inter_outlet_price' => $defaultPrice,
                'markup_percent' => 0
            ]
        );
    }

    /**
     * Get inter outlet price for a product, fallback to regular price
     */
    public static function getInterOutletPrice($productId, $outletId)
    {
        $interOutletPrice = static::where('id_produk', $productId)
            ->where('outlet_id', $outletId)
            ->first();

        if ($interOutletPrice && $interOutletPrice->inter_outlet_price > 0) {
            return $interOutletPrice->inter_outlet_price;
        }

        // Fallback to regular product price
        $produk = Produk::find($productId);
        return $produk ? $produk->harga_jual : 0;
    }
}