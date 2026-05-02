<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produksi extends Model
{
    use HasFactory;

    protected $table = 'produksi';
    protected $primaryKey = 'id_produksi';
    protected $guarded = [];

    /**
     * Relasi ke model Produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    /**
     * Relasi ke model Outlet
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    /**
     * Relasi ke model ProduksiDetail
     */
    public function detail()
    {
        return $this->hasMany(ProduksiDetail::class, 'id_produksi', 'id_produksi');
    }

    /**
     * Relasi ke model ProduksiDetail dengan bahan dan satuan
     */
    public function detailWithBahan()
    {
        return $this->hasMany(ProduksiDetail::class, 'id_produksi', 'id_produksi')
                    ->with(['bahan.satuan']);
    }
}