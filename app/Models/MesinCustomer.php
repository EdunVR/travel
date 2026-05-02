<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MesinCustomer extends Model
{
    use HasFactory;

    protected $table = 'mesin_customer';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = ['id_member', 'closing_type', 'id_ongkir', 'biaya_service', 'kode_mesin', 'nama_mesin'];
    
    protected $attributes = [
        'kode_mesin' => '-'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function ongkosKirim()
    {
        return $this->belongsTo(OngkosKirim::class, 'id_ongkir');
    }

    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'mesin_customer_produk', 'id_mesin_customer', 'id_produk')
            ->withPivot(['biaya_service', 'closing_type', 'jumlah'])
            ->withTimestamps();
    }
}