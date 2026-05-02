<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembelianDetail extends Model
{
    use HasFactory;

    protected $table = 'pembelian_detail';
    protected $primaryKey = 'id_pembelian_detail';
    protected $guarded = [];

    public function bahan()
    {
        return $this->hasOne(Bahan::class, 'id_bahan', 'id_bahan');
    }

    public function hargaBahan()
    {
        return $this->hasMany(BahanDetail::class, 'id_bahan', 'id_bahan');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }
}
