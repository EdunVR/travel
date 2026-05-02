<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    protected $guarded = [];
    // protected $fillable = [
    //     'id_outlet',
    //     'id_member',
    //     'total_item',
    //     'total_harga',
    //     'diskon',
    //     'bayar',
    //     'diterima',
    //     'id_user',
    //     'created_at',
    //     'updated_at'
    // ];


    public function member()
    {
        return $this->hasOne(Member::class, 'id_member', 'id_member');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function details()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan', 'id_penjualan');
    }

    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan');
    }

    public function piutang()
    {
        return $this->hasOne(Piutang::class, 'id_penjualan', 'id_penjualan');
    }

    public function posSale()
    {
        return $this->hasOne(PosSale::class, 'id_penjualan', 'id_penjualan');
    }

    public function gerobak()
    {
        return $this->belongsTo(Gerobak::class, 'id_gerobak');
    }
}
