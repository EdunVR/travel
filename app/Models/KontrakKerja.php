<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class KontrakKerja extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kontrak_kerja';

    protected $fillable = [
        'recruitment_id',
        'outlet_id',
        'nomor_kontrak',
        'jenis_kontrak',
        'jabatan',
        'unit_kerja',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_bulan',
        'gaji_pokok',
        'deskripsi',
        'file_path',
        'status',
        'perpanjangan_dari',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'gaji_pokok' => 'decimal:2',
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

    public function kontrakSebelumnya()
    {
        return $this->belongsTo(KontrakKerja::class, 'perpanjangan_dari');
    }

    public function perpanjangan()
    {
        return $this->hasMany(PerpanjanganKontrak::class, 'kontrak_lama_id');
    }

    // Hitung durasi kontrak
    public function getDurasiAttribute()
    {
        if ($this->tanggal_mulai && $this->tanggal_selesai) {
            return $this->tanggal_mulai->diffInMonths($this->tanggal_selesai);
        }
        return $this->durasi_bulan;
    }

    // Cek apakah kontrak akan habis (30 hari ke depan)
    public function getAkanHabisAttribute()
    {
        if ($this->tanggal_selesai && $this->status === 'aktif') {
            $sisaHari = Carbon::now()->diffInDays($this->tanggal_selesai, false);
            return $sisaHari > 0 && $sisaHari <= 30;
        }
        return false;
    }

    // Cek apakah kontrak sudah habis
    public function getSudahHabisAttribute()
    {
        if ($this->tanggal_selesai) {
            return Carbon::now()->isAfter($this->tanggal_selesai) && $this->status === 'aktif';
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
