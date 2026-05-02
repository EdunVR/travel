<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hutang extends Model
{
    protected $table = 'hutang';
    protected $primaryKey = 'id_hutang';
    
    protected $fillable = [
        'id_pembelian',
        'id_supplier',
        'id_outlet',
        'jumlah_hutang',
        'jumlah_dibayar',
        'sisa_hutang',
        'tanggal_jatuh_tempo',
        'status',
        'tanggal_tempo',
        'id_purchase_invoice',
        'nama',
        'hutang',
    ];

    protected $casts = [
        'jumlah_hutang' => 'decimal:2',
        'jumlah_dibayar' => 'decimal:2',
        'sisa_hutang' => 'decimal:2',
        'tanggal_tempo' => 'date',
        'tanggal_jatuh_tempo' => 'date',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }
}
