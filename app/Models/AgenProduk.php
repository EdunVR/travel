<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgenProduk extends Model
{
    protected $table = 'agen_produk';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function agen()
    {
        return $this->belongsTo(Member::class, 'id_agen', 'id_member');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}