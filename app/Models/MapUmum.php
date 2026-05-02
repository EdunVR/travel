<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapUmum extends Model
{
    use HasFactory;

    protected $table = 'map_umum';
    protected $fillable = ['nama_lokasi', 'latitude', 'longitude', 'tipe'];
}