<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RabTemplate extends Model
{
    use HasFactory;

    protected $table = 'rab_template';
    protected $primaryKey = 'id_rab';
    protected $fillable = [
        'outlet_id',
        'book_id',
        'nama_template',
        'deskripsi',
        'total_biaya',
        'is_active'
    ];
    protected $appends = ['total_nilai_disetujui', 'total_realisasi', 'status'];
    protected $casts = [
        'komponen_utama' => 'boolean'
    ];

    public function details()
    {
        return $this->hasMany(RabDetail::class, 'id_rab');
    }

    public function products()
    {
        return $this->belongsToMany(Produk::class, 'produk_rab', 'id_rab', 'id_produk')
            ->withTimestamps();
    }

    // Helper methods for status calculation
    public function getTotalBudgetAttribute()
    {
        return $this->details->sum('budget');
    }

    public function getTotalNilaiDisetujuiAttribute()
    {
        return $this->details->sum('nilai_disetujui');
    }

    public function getTotalRealisasiAttribute()
    {
        return $this->details->sum('realisasi_pemakaian');
    }

    public function getStatusAttribute()
    {
        // Check if any item has been transferred
        if ($this->details->whereNotNull('bukti_transfer')->count() > 0) {
            return 'Ditransfer';
        }

        // Check if any item has been approved (either checkbox or nilai_disetujui > 0)
        $hasApprovals = $this->details->where('disetujui', true)->count() > 0 || 
                    $this->details->where('nilai_disetujui', '>', 0)->count() > 0;

        // If no approvals at all, it's still Draft
        if (!$hasApprovals) {
            return 'Draft';
        }

        $allApproved = $this->details->where('disetujui', false)->count() === 0;
        $budgetEqualsApproved = $this->total_budget == $this->total_nilai_disetujui;
        
        if ($allApproved) {
            return $budgetEqualsApproved ? 'Disetujui Semua' : 'Disetujui dengan Revisi';
        } else {
            return $budgetEqualsApproved ? 'Disetujui Sebagian' : 'Disetujui Sebagian dengan Revisi';
        }
    }
}