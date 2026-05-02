<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];
    protected $fillable = [
        'id_produk',
        'id_outlet',
        'id_kategori',
        'kode_produk',
        'nama_produk',
        'merk',
        'spesifikasi',
        'diskon',
        'harga_jual',
        'id_satuan',
        'tipe_produk',
        'track_inventory',
        'metode_hpp',
        'jenis_paket',
        'keberangkatan_template_id',
        'stok_minimum',
        'is_active' // Tambahkan ini
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stok_minimum' => 'integer'
    ];

    public function produkTipe()
    {
        return $this->hasMany(ProdukTipe::class, 'id_produk');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function tipe()
    {
        return $this->hasOne(ProdukTipe::class, 'id_produk', 'id_produk');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }

    public function hppProduk()
    {
        return $this->hasMany(HppProduk::class, 'id_produk', 'id_produk');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet');
    }

    public function getHppProdukSumStokAttribute()
    {
        return $this->hppProduk()->sum('stok');
    }

    public function getStokAttribute()
    {
        return $this->getHppProdukSumStokAttribute();
    }

    public function reduceStock($jumlah)
    {
        $sisaKurang = $jumlah;
        $hppProduks = $this->hppProduk()
            ->where('stok', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($hppProduks as $hppProduk) {
            if ($sisaKurang <= 0) break;

            if ($hppProduk->stok >= $sisaKurang) {
                $hppProduk->stok -= $sisaKurang;
                $hppProduk->save();
                $sisaKurang = 0;
            } else {
                $sisaKurang -= $hppProduk->stok;
                $hppProduk->stok = 0;
                $hppProduk->save();
            }
        }

        if ($sisaKurang > 0) {
            throw new \Exception("Stok tidak mencukupi untuk produk: {$this->nama_produk}. Stok tersedia: {$this->stok}, dibutuhkan: {$jumlah}");
        }

        return true;
    }

    // Method untuk menambah stok (saat retur atau pembelian)
    public function addStock($hpp, $jumlah)
    {
        return HppProduk::create([
            'id_produk' => $this->id_produk,
            'hpp' => $hpp,
            'stok' => $jumlah,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function rabTemplate()
    {
        return $this->belongsTo(RabTemplate::class, 'keberangkatan_template_id', 'id_rab');
    }

    // Scope untuk filter tipe produk
    public function scopeBarangDagang($query)
    {
        return $query->where('tipe_produk', 'barang_dagang');
    }

    public function scopePaketTravel($query)
    {
        return $query->where('tipe_produk', 'paket_travel');
    }

    // Hitung HPP berdasarkan tipe produk
    public function calculateHpp()
    {
        if ($this->tipe_produk === 'barang_dagang') {
            return $this->calculateHppBarangDagang();
        } elseif ($this->tipe_produk === 'paket_travel') {
            return $this->calculateHppPaketTravel();
        }
        return 0;
    }

    public function calculateHppBarangDagang()
    {
         $hppProduks = $this->hppProduk()->where('stok', '>', 0)->get();
    
        if ($hppProduks->isEmpty()) {
            return 0;
        }

        // Metode Rata-rata Tertimbang
        $totalNilai = 0;
        $totalStok = 0;

        foreach ($hppProduks as $hppProduk) {
            $totalNilai += $hppProduk->hpp * $hppProduk->stok;
            $totalStok += $hppProduk->stok;
        }

        if ($totalStok > 0) {
            return $totalNilai / $totalStok;
        }

        return 0;
    }


    public function calculateHppPaketTravel()
    {
        return $this->rabs->sum(function($rab) {
            return $rab->pivot->subtotal * $rab->pivot->qty;
        });
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'id_produk');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class, 'id_produk')->where('is_primary', true);
    }

    public function getGambarAttribute()
    {
        return $this->primaryImage ? $this->primaryImage->path : null;
    }

    public function rabs()
    {
        return $this->belongsToMany(RabTemplate::class, 'produk_rab', 'id_produk', 'id_rab')
            ->withTimestamps();
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function components()
    {
        return $this->hasMany(ProductComponent::class, 'product_id');
    }

    /**
     * Relationship ke CustomerPrice melalui pivot table
     */
    public function customerPrices()
    {
        return $this->belongsToMany(CustomerPrice::class, 'customer_price_produk', 'id_produk', 'id_customer_price')
                    ->withPivot('harga_khusus')
                    ->withTimestamps();
    }

    /**
     * Relationship langsung ke pivot table
     */
    public function customerPriceProduk()
    {
        return $this->hasMany(CustomerPriceProduk::class, 'id_produk', 'id_produk');
    }
}
