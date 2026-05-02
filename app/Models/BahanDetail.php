<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BahanDetail extends Model
{
    use HasFactory;

    protected $table = 'harga_bahan';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function bahan()
    {
        return $this->hasOne(Bahan::class, 'id_bahan', 'id_bahan');
    }
}
