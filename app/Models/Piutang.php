<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\DateHelper;

class Piutang extends Model
{
    protected $table = 'piutang';
    protected $primaryKey = 'id_piutang';
    
    protected $fillable = [
        'id_penjualan',
        'id_jamaah_booking',
        'source_type',
        'tanggal_tempo',
        'nama',
        'piutang',
        'id_member',
        'id_outlet',
        'jumlah_piutang',
        'jumlah_dibayar',
        'sisa_piutang',
        'tanggal_jatuh_tempo',
        'tanggal_piutang',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'piutang' => 'decimal:2',
        'jumlah_piutang' => 'decimal:2',
        'jumlah_dibayar' => 'decimal:2',
        'sisa_piutang' => 'decimal:2',
        'tanggal_tempo' => 'datetime',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_piutang' => 'date',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id_member');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function jamaahBooking()
    {
        return $this->belongsTo(JamaahBooking::class, 'id_jamaah_booking', 'id');
    }

    public function posSale()
    {
        return $this->hasOneThrough(
            PosSale::class,
            Penjualan::class,
            'id_penjualan', // Foreign key on penjualan table
            'id_penjualan', // Foreign key on pos_sales table
            'id_penjualan', // Local key on piutang table
            'id_penjualan'  // Local key on penjualan table
        );
    }

    // Accessors for formatted dates
    public function getTanggalTempoFormattedAttribute()
    {
        return DateHelper::formatDate($this->tanggal_tempo);
    }

    public function getTanggalJatuhTempoFormattedAttribute()
    {
        return DateHelper::formatDate($this->tanggal_jatuh_tempo);
    }

    // Scopes
    public function scopeByOutlet($query, $outletId)
    {
        if ($outletId && $outletId !== 'all') {
            return $query->where('id_outlet', $outletId);
        }
        return $query;
    }

    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    // Static method to prevent duplicate piutang
    public static function createSafely(array $data)
    {
        // Check if piutang already exists for this penjualan
        if (isset($data['id_penjualan']) && $data['id_penjualan']) {
            $existing = static::where('id_penjualan', $data['id_penjualan'])->first();
            if ($existing) {
                throw new \Exception("Piutang sudah ada untuk penjualan ID: {$data['id_penjualan']}");
            }
        }

        return static::create($data);
    }
}
