<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAddon extends Model
{
    protected $fillable = [
        'id_jamaah_booking',
        'id_produk',
        'nama',
        'keterangan',
        'harga',
        'qty',
        'masuk_hpp',
    ];
    
    protected $casts = [
        'qty' => 'integer',
        'harga' => 'decimal:2',
        'masuk_hpp' => 'boolean',
    ];
    
    /**
     * Get the booking that owns this addon
     */
    public function booking()
    {
        return $this->belongsTo(JamaahBooking::class, 'id_jamaah_booking');
    }
    
    /**
     * Get the product for this addon
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
