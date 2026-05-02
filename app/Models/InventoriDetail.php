<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoriDetail extends Model
{
    use HasFactory;

    protected $table = 'inventori_detail';
    protected $primaryKey = 'id_inventori_detail';
    protected $guarded = [];

    public function inventori()
    {
        return $this->belongsTo(Inventori::class, 'id_inventori');
    }
}
