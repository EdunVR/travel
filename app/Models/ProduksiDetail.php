<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProduksiDetail extends Model
{
    use HasFactory;

    protected $table = 'produksi_detail';
    protected $primaryKey = 'id_produksi_detail';
    protected $guarded = [];

    /**
     * Relasi ke model Produksi
     */
    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'id_produksi', 'id_produksi');
    }

    /**
     * Relasi ke model Bahan
     */
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'id_bahan', 'id_bahan');
    }

    /**
     * Relasi ke model Bahan dengan satuan
     */
    public function bahanWithSatuan()
    {
        return $this->belongsTo(Bahan::class, 'id_bahan', 'id_bahan')->with('satuan');
    }
}