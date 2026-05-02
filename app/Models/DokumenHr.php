<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class DokumenHr extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dokumen_hr';

    protected $fillable = [
        'recruitment_id',
        'outlet_id',
        'nomor_dokumen',
        'jenis_dokumen',
        'judul_dokumen',
        'deskripsi',
        'tanggal_terbit',
        'tanggal_berlaku',
        'tanggal_berakhir',
        'memiliki_masa_berlaku',
        'file_path',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_berlaku' => 'date',
        'tanggal_berakhir' => 'date',
        'memiliki_masa_berlaku' => 'boolean',
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

    // Cek apakah dokumen akan habis (30 hari ke depan)
    public function getAkanHabisAttribute()
    {
        if ($this->memiliki_masa_berlaku && $this->tanggal_berakhir && $this->status === 'aktif') {
            $sisaHari = Carbon::now()->diffInDays($this->tanggal_berakhir, false);
            return $sisaHari > 0 && $sisaHari <= 30;
        }
        return false;
    }

    // Cek apakah dokumen sudah habis
    public function getSudahHabisAttribute()
    {
        if ($this->memiliki_masa_berlaku && $this->tanggal_berakhir) {
            return Carbon::now()->isAfter($this->tanggal_berakhir) && $this->status === 'aktif';
        }
        return false;
    }

    // Status warna untuk monitoring
    public function getStatusWarnaAttribute()
    {
        if (!$this->memiliki_masa_berlaku) {
            return 'blue'; // Dokumen tanpa masa berlaku
        }

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
