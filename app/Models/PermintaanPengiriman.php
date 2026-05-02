<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanPengiriman extends Model
{
    use HasFactory;

    protected $table = 'permintaan_pengiriman';
    protected $primaryKey = 'id';
    protected $fillable = [
        'no_permintaan',
        'tanggal',
        'id_outlet_asal',
        'id_outlet_tujuan',
        'id_produk',
        'id_bahan',
        'id_inventori',
        'jumlah',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_permintaan)) {
                $model->no_permintaan = self::generateNoPermintaan();
            }
            if (empty($model->tanggal)) {
                $model->tanggal = now()->format('Y-m-d');
            }
        });
    }

    public static function generateNoPermintaan()
    {
        $prefix = 'TRF';
        $date = date('Ymd');
        
        // Get last number for today
        $lastPermintaan = self::where('no_permintaan', 'like', $prefix . $date . '%')
            ->orderBy('no_permintaan', 'desc')
            ->first();
        
        if ($lastPermintaan) {
            $lastNumber = (int) substr($lastPermintaan->no_permintaan, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function outletAsal()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet_asal');
    }

    public function outletTujuan()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet_tujuan');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }

    public function inventori()
    {
        return $this->belongsTo(Inventori::class, 'id_inventori');
    }
}
