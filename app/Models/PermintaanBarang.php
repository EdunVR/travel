<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermintaanBarang extends Model
{
    protected $table = 'permintaan_barang';
    
    protected $fillable = [
        'nomor_permintaan',
        'judul',
        'deskripsi',
        'status',
        'prioritas',
        'tanggal_dibutuhkan',
        'estimasi_budget',
        'outlet_id',
        'user_id',
        'approved_by',
        'approved_at',
        'catatan_approval',
        'alasan_penolakan'
    ];

    protected $casts = [
        'tanggal_dibutuhkan' => 'date',
        'approved_at' => 'datetime',
        'estimasi_budget' => 'decimal:2'
    ];

    // Relationships
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'outlet_id', 'id_outlet');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PermintaanBarangItem::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'bg-gray-100 text-gray-800',
            'aktif' => 'bg-blue-100 text-blue-800',
            'disetujui' => 'bg-green-100 text-green-800',
            'ditolak' => 'bg-red-100 text-red-800'
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getPrioritasBadgeAttribute()
    {
        $badges = [
            'rendah' => 'bg-gray-100 text-gray-800',
            'normal' => 'bg-blue-100 text-blue-800',
            'tinggi' => 'bg-yellow-100 text-yellow-800',
            'urgent' => 'bg-red-100 text-red-800'
        ];

        return $badges[$this->prioritas] ?? 'bg-gray-100 text-gray-800';
    }

    // Methods
    public function generateNomorPermintaan()
    {
        $prefix = 'PB';
        $date = now()->format('Ymd');
        $lastNumber = static::whereDate('created_at', now()->toDateString())
            ->where('nomor_permintaan', 'like', $prefix . $date . '%')
            ->count();
        
        return $prefix . $date . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    public function canBeApproved()
    {
        return $this->status === 'aktif';
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'aktif']);
    }
}
