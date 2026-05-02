<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JemaahData extends Model
{
    protected $table = 'jemaah_data';
    protected $fillable = [
        'member_id', 'ktp_path', 'passport_path', 'visa_path', 'photo_path',
        'nama_lengkap', 'jenis_kelamin', 'status_pernikahan', 'tempat_lahir',
        'tanggal_lahir', 'no_ktp', 'no_telepon', 'alamat'
    ];
    
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
