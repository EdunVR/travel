<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoPenjualan extends Model
{
    protected $table = 'po_penjualan';
    protected $primaryKey = 'id_po_penjualan';
    
    protected $fillable = [
        'no_po',
        'tanggal',
        'id_member',
        'id_outlet',
        'total_item',
        'total_harga',
        'diskon',
        'ongkir',
        'bayar',
        'diterima',
        'status',
        'id_user',
        'tanggal_tempo'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_tempo' => 'date'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function details()
    {
        return $this->hasMany(PoPenjualanDetail::class, 'id_po_penjualan');
    }

    public function piutang()
    {
        return $this->hasOne(Piutang::class, 'id_po_penjualan');
    }
}