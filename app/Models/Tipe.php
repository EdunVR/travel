<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tipe extends Model
{
    use HasFactory;

    protected $table = 'tipe';
    protected $primaryKey = 'id_tipe';
    protected $guarded = [];

    protected $fillable = [
        'nama_tipe',
        'keterangan',
        'id_outlet'
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    public function produkTipe()
    {
        return $this->hasMany(ProdukTipe::class, 'id_tipe');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'id_tipe', 'id_tipe');
    }
}
