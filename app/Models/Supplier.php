<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';
    protected $primaryKey = 'id_supplier';
    
    protected $fillable = [
        'nama',
        'telepon', 
        'alamat',
        'email',
        'id_outlet',
        'is_active',
        // Tambahan field baru
        'bank',
        'no_rekening',
        'atas_nama'
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'id_supplier');
    }

    /**
     * Get informasi bank lengkap
     */
    public function getInfoBankAttribute()
    {
        if ($this->bank && $this->no_rekening && $this->atas_nama) {
            return $this->bank . ' - ' . $this->no_rekening . ' a/n ' . $this->atas_nama;
        }
        return null;
    }
}