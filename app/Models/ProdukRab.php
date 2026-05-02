<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukRab extends Model
{
    protected $table = 'produk_rab';
    public $timestamps = true;
    
    protected $fillable = [
        'id_produk',
        'id_rab'
    ];

    public function rab()
    {
        return $this->belongsTo(RabTemplate::class, 'id_rab');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}