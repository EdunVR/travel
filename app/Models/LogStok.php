<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogStok extends Model
{
    use HasFactory;

    protected $table = 'log_stok';
    protected $primaryKey = 'id_log';
    protected $guarded = [];

}
