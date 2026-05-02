<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RegProvince;
use App\Models\RegRegency;
use App\Models\RegDistrict;
use App\Models\RegVillage;

class Prospek extends Model
{
    use HasFactory;

    protected $table = 'prospek';
    protected $primaryKey = 'id_prospek';
    protected $fillable = [
        'tanggal', 'nama', 'nama_perusahaan', 'jenis', 'telepon', 'email', 'alamat',
        'provinsi_id', 'kabupaten_id', 'kecamatan_id', 'desa_id',
        'pemilik_manager', 'kapasitas_produksi', 'sistem_produksi', 
        'bahan_bakar', 'informasi_perusahaan', 'latitude', 'longitude', 
        'recruitment_id', 'id_outlet', 'current_status', 'menggunakan_boiler'
    ];

    const STATUSES = [
        'prospek' => 'Prospek',
        'followup' => 'Follow Up',
        'negosiasi' => 'Negosiasi',
        'closing' => 'Closing',
        'deposit' => 'Deposit',
        'gagal' => 'Gagal'
    ];

    public function timeline()
    {
        return $this->hasMany(ProspekTimeline::class, 'prospek_id');
    }

    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class, 'recruitment_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function getLatestStatusAttribute()
    {
        return $this->timeline()->latest('tanggal')->first();
    }

    public function getTanggalProspekAttribute()
    {
        return $this->created_at;
    }

    public function getTanggalStatus($status)
    {
        $timeline = $this->timeline()->where('status', $status)->first();
        return $timeline ? $timeline->tanggal : null;
    }

    public function getDeskripsiStatus($status)
    {
        $timeline = $this->timeline()->where('status', $status)->first();
        return $timeline ? $timeline->deskripsi : null;
    }

    // Di dalam class Prospek
    public function province()
    {
        return $this->belongsTo(RegProvince::class, 'provinsi_id');
    }

    public function regency()
    {
        return $this->belongsTo(RegRegency::class, 'kabupaten_id');
    }

    public function district()
    {
        return $this->belongsTo(RegDistrict::class, 'kecamatan_id');
    }

    public function village()
    {
        return $this->belongsTo(RegVillage::class, 'desa_id');
    }
}
