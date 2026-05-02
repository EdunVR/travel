<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPrice extends Model
{
    use HasFactory;

    protected $table = 'customer_price';
    protected $primaryKey = 'id_customer_price';
    
    protected $fillable = [
        'id_outlet',
        'customer_type',
        'customer_id',
        'id_ongkir'
    ];

    protected $casts = [
        'id_outlet' => 'integer',
        'customer_id' => 'integer'
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }

    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('id_outlet', $outletId);
    }

    public static function getByOutlet($outletId)
    {
        return static::byOutlet($outletId)->get();
    }

    public function customer()
    {
        if ($this->customer_type === 'member') {
            return $this->belongsTo(Member::class, 'customer_id', 'id_member');
        } else {
            return $this->belongsTo(Prospek::class, 'customer_id', 'id_prospek');
        }
    }

    public function ongkosKirim()
    {
        return $this->belongsTo(OngkosKirim::class, 'id_ongkir', 'id_ongkir');
    }

    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'customer_price_produk', 'id_customer_price', 'id_produk')
                    ->withPivot('harga_khusus')
                    ->withTimestamps();
    }

    // Accessor untuk nama customer yang lebih robust
    public function getNamaCustomerAttribute()
    {
        try {
            if ($this->customer_type === 'member') {
                $customer = Member::find($this->customer_id);
                return $customer ? $customer->nama : 'Member Tidak Ditemukan';
            } else {
                $customer = Prospek::find($this->customer_id);
                if ($customer) {
                    return $customer->nama ?? $customer->nama_perusahaan ?? 'Prospek Tanpa Nama';
                }
                return 'Prospek Tidak Ditemukan';
            }
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    // Accessor untuk info customer lengkap
    public function getCustomerInfoAttribute()
    {
        $nama = $this->nama_customer;
        $type = $this->customer_type === 'member' ? 'Member' : 'Prospek';
        return $nama . ' (' . $type . ')';
    }

    // Method untuk load customer dengan eager loading yang tepat
    public function loadCustomer()
    {
        if ($this->customer_type === 'member') {
            return $this->load(['customer' => function($query) {
                $query->select('id_member', 'nama', 'telepon', 'alamat');
            }]);
        } else {
            return $this->load(['customer' => function($query) {
                $query->select('id_prospek', 'nama', 'nama_perusahaan', 'telepon', 'alamat');
            }]);
        }
    }

    public function getCustomerNameAttribute()
    {
        if ($this->customer_type === 'member') {
            $customer = Member::where('id_member', $this->customer_id)
                ->where('id_outlet', $this->id_outlet)
                ->first();
            return $customer ? $customer->nama : 'Member Tidak Ditemukan';
        } else {
            $customer = Prospek::where('id_prospek', $this->customer_id)
                ->where('id_outlet', $this->id_outlet)
                ->first();
            return $customer ? ($customer->nama ?? $customer->nama_perusahaan) : 'Prospek Tidak Ditemukan';
        }
    }
}