<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id',
        'material_id',
        'material_type',
        'quantity_required',
        'quantity_used',
        'unit',
    ];

    protected $casts = [
        'quantity_required' => 'decimal:2',
        'quantity_used' => 'decimal:2',
    ];

    // Relationships
    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function material()
    {
        if ($this->material_type === 'bahan') {
            return $this->belongsTo(Bahan::class, 'material_id', 'id_bahan');
        } else {
            return $this->belongsTo(Produk::class, 'material_id', 'id_produk');
        }
    }

    // Accessors
    public function getMaterialNameAttribute()
    {
        $material = $this->material;
        if ($this->material_type === 'bahan') {
            return $material->nama_bahan ?? 'Unknown';
        } else {
            return $material->nama_produk ?? 'Unknown';
        }
    }

    public function getUsagePercentageAttribute()
    {
        if ($this->quantity_required == 0) {
            return 0;
        }
        return round(($this->quantity_used / $this->quantity_required) * 100, 2);
    }
}
