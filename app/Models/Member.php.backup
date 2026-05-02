<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected $table = 'member';
    protected $primaryKey = 'id_member';
    protected $guarded = [];
    protected $fillable = ['nama', 'telepon', 'alamat', 'id_tipe', 'id_outlet', 'kode_member'];

    public function tipe()
    {
        return $this->belongsTo(Tipe::class, 'id_tipe', 'id_tipe');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'id_outlet', 'id_outlet');
    }


    public function tipeX()
    {
        return $this->belongsTo(Tipe::class, 'id_tipe');
    }

    public function jemaahData()
    {
        return $this->hasMany(JemaahData::class, 'id_member');
    }

    public function gerobak()
    {
        return $this->hasMany(Gerobak::class, 'id_agen', 'id_member');
    }

    // Tambahkan relationships
    public function produkStok()
    {
        return $this->hasMany(AgenProduk::class, 'id_agen', 'id_member');
    }

    public function stokHistory()
    {
        return $this->hasMany(AgenStokHistory::class, 'id_agen', 'id_member');
    }

    public function getStokProduk($id_produk)
    {
        return $this->produkStok()->where('id_produk', $id_produk)->first();
    }

    public function mesinCustomers()
    {
        return $this->hasMany(MesinCustomer::class, 'id_member');
    }

    public function customerPrices()
    {
        return $this->hasMany(CustomerPrice::class, 'id_member');
    }

    // Relationship dengan SalesInvoice
    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'id_member');
    }

    public function getMemberCodeWithPrefix()
    {
        if (!$this->kode_member) {
            return null;
        }

        $closingTypes = $this->getClosingTypes();
        
        // Determine prefix
        $prefix = 'J'; // Default Jual Putus
        
        if (in_array('deposit', $closingTypes) && in_array('jual_putus', $closingTypes)) {
            $prefix = 'JD';
        } elseif (in_array('deposit', $closingTypes)) {
            $prefix = 'D';
        }

        return $prefix . '-' . $this->kode_member;
    }

    /**
     * Get all closing types from mesin customers
     */
    public function getClosingTypes()
    {
        $closingTypes = [];
        
        foreach ($this->mesinCustomers as $mesinCustomer) {
            foreach ($mesinCustomer->produk as $produk) {
                $closingType = $produk->pivot->closing_type ?? 'jual_putus';
                if (!in_array($closingType, $closingTypes)) {
                    $closingTypes[] = $closingType;
                }
            }
        }
        
        return $closingTypes;
    }

    /**
     * Get closing type display
     */
    public function getClosingTypeDisplay()
    {
        $closingTypes = $this->getClosingTypes();
        
        if (in_array('deposit', $closingTypes) && in_array('jual_putus', $closingTypes)) {
            return 'Mixed';
        } elseif (in_array('deposit', $closingTypes)) {
            return 'Deposit';
        }
        
        return 'Jual Putus';
    }

    public function piutangs()
    {
        return $this->hasMany(Piutang::class, 'id_member');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'id_member');
    }

    /**
     * Relationship dengan piutang yang belum lunas
     */
    public function piutangBelumLunas()
    {
        return $this->hasMany(Piutang::class, 'id_member')
                    ->where('status', 'belum_lunas');
    }

    /**
     * Accessor untuk mendapatkan total piutang dari tabel piutang
     */
    public function getTotalPiutangAttribute()
    {
        return $this->piutangBelumLunas()->sum('piutang');
    }

    /**
     * Scope untuk menambahkan total piutang dalam query
     */
    public function scopeWithTotalPiutang($query)
    {
        return $query->addSelect([
            'total_piutang' => Piutang::selectRaw('COALESCE(SUM(piutang), 0)')
                ->whereColumn('id_member', 'member.id_member')
                ->where('status', 'belum_lunas')
        ]);
    }
}
