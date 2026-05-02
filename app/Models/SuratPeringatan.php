<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SuratPeringatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_peringatan';

    protected $fillable = [
        'recruitment_id',
        'outlet_id',
        'nomor_sp',
        'jenis_sp',
        'tanggal_sp',
        'tanggal_berlaku',
        'tanggal_berakhir',
        'alasan',
        'catatan',
        'file_path',
        'status',
    ];

    protected $casts = [
        'tanggal_sp' => 'date',
        'tanggal_berlaku' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }
    
    // Alias for backward compatibility
    public function employee()
    {
        return $this->recruitment();
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    // Cek apakah SP akan habis (30 hari ke depan)
    public function getAkanHabisAttribute()
    {
        if ($this->tanggal_berakhir && $this->status === 'aktif') {
            $sisaHari = Carbon::now()->diffInDays($this->tanggal_berakhir, false);
            return $sisaHari > 0 && $sisaHari <= 30;
        }
        return false;
    }

    // Cek apakah SP sudah habis
    public function getSudahHabisAttribute()
    {
        if ($this->tanggal_berakhir) {
            return Carbon::now()->isAfter($this->tanggal_berakhir) && $this->status === 'aktif';
        }
        return false;
    }

    // Status warna untuk monitoring
    public function getStatusWarnaAttribute()
    {
        if ($this->status !== 'aktif') {
            return 'gray';
        }
        
        if ($this->sudah_habis) {
            return 'red';
        }
        
        if ($this->akan_habis) {
            return 'yellow';
        }
        
        return 'green';
    }
}
