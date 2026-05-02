<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'setting';
    protected $primaryKey = 'id_setting';
    protected $guarded = [];
    
    protected $fillable = [
        'nama_perusahaan',
        'alamat',
        'telepon',
        'tipe_nota',
        'diskon',
        'path_logo',
        'path_kartu_member',
    ];
    
    protected $casts = [
        'tipe_nota' => 'integer',
        'diskon' => 'integer',
    ];
}
