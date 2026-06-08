<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferRequestItem extends Model
{
    protected $table = 'transfer_request_items';

    protected $fillable = [
        'transfer_request_id',
        'item_type',
        'item_id',
        'item_name',
        'jumlah',
        'unit',
    ];

    public function transferRequest()
    {
        return $this->belongsTo(PermintaanPengiriman::class, 'transfer_request_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'item_id');
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'item_id');
    }

    public function inventori()
    {
        return $this->belongsTo(Inventori::class, 'item_id');
    }
}
