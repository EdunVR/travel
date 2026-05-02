<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    protected $fillable = ['iata_code', 'name', 'city', 'country', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
