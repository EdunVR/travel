<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontraBonDetail extends Model
{
    use HasFactory;

    protected $table = 'kontra_bon_detail';
    protected $primaryKey = 'id_kontra_bon_detail';
    protected $guarded = [];

    public function kontraBon()
    {
        return $this->belongsTo(KontraBon::class, 'id_kontra_bon');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan');
    }
}