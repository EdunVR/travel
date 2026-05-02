<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    protected $fillable = ['name', 'iata_code', 'country', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
