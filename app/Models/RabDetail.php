<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RabDetail extends Model
{
    use HasFactory;

    protected $table = 'rab_detail';
    protected $primaryKey = 'id';
    protected $guarded = [];
    
    protected $casts = [
        'qty' => 'decimal:2',
        'jumlah' => 'decimal:2',
        'harga' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'budget' => 'decimal:2',
        'biaya' => 'decimal:2',
        'nilai_disetujui' => 'decimal:2',
        'realisasi_pemakaian' => 'decimal:2',
        'disetujui' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(RabTemplate::class, 'id_rab');
    }
}