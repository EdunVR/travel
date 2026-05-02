<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerpanjanganKontrak extends Model
{
    use HasFactory;

    protected $table = 'perpanjangan_kontrak';

    protected $fillable = [
        'kontrak_lama_id',
        'kontrak_baru_id',
        'tanggal_perpanjangan',
        'alasan',
        'file_path',
    ];

    protected $casts = [
        'tanggal_perpanjangan' => 'date',
    ];

    public function kontrakLama()
    {
        return $this->belongsTo(KontrakKerja::class, 'kontrak_lama_id');
    }

    public function kontrakBaru()
    {
        return $this->belongsTo(KontrakKerja::class, 'kontrak_baru_id');
    }
}
