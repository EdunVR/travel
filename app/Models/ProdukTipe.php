<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukTipe extends Model
{
    protected $table = 'produk_tipe';
    protected $guarded = [];

    protected $casts = [
        'diskon' => 'decimal:2',
        'harga_jual' => 'decimal:2',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function tipe()
    {
        return $this->belongsTo(Tipe::class, 'id_tipe');
    }
}
