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
    protected $fillable = [
        'nama', 
        'nama_perusahaan', 
        'telepon', 
        'alamat', 
        'id_tipe', 
        'id_outlet', 
        'kode_member',
        'pas_foto',
        'ktp_foto',
        'ktp_nik',
        'ktp_nama',
        'ktp_tempat_lahir',
        'ktp_tanggal_lahir',
        'ktp_alamat',
        'passport_foto',
        'passport_nomor',
        'passport_nama',
        'passport_tanggal_lahir',
        'passport_tanggal_kadaluarsa',
        'passport_kewarganegaraan',
        // Visa fields
        'visa_foto',
        'visa_nomor',
        'visa_tipe',
        'visa_tanggal_terbit',
        'visa_tanggal_kadaluarsa',
        'visa_negara',
        // Ticket fields
        'tiket_foto',
        'tiket_nomor',
        'tiket_maskapai',
        'tiket_rute',
        'tiket_tanggal_berangkat',
        'tiket_tanggal_pulang',
        // Insurance fields
        'asuransi_foto',
        'asuransi_nomor_polis',
        'asuransi_provider',
        'asuransi_tanggal_mulai',
        'asuransi_tanggal_akhir',
        // Health Certificate fields
        'sertifikat_kesehatan_foto',
        'sertifikat_kesehatan_nomor',
        'sertifikat_kesehatan_jenis',
        'sertifikat_kesehatan_tanggal_terbit',
        'sertifikat_kesehatan_tanggal_kadaluarsa',
        'sertifikat_kesehatan_penerbit',
        // Jamaah-specific fields
        'is_jamaah',
        'jamaah_type',
        'mahram_name',
        'mahram_relationship',
        'mahram_phone',
        'mahram_ktp_nik',
        'health_conditions',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'room_preference',
        'special_requests',
        'gender',
        'family_members'
    ];

    protected $casts = [
        'family_members' => 'array',
        'is_jamaah' => 'boolean',
    ];

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

    // Jamaah-specific relationships
    public function jamaahBookings()
    {
        return $this->hasMany(\App\Models\JamaahBooking::class, 'id_member');
    }

    /**
     * Validate jamaah-specific business rules
     */
    public function validateJamaahRules($departureDate = null)
    {
        $errors = [];

        if (!$this->is_jamaah) {
            return $errors;
        }

        // Validate passport expiry (6 months rule)
        if ($departureDate && $this->passport_tanggal_kadaluarsa) {
            $expiryDate = \Carbon\Carbon::parse($this->passport_tanggal_kadaluarsa);
            $minExpiryDate = \Carbon\Carbon::parse($departureDate)->addMonths(6);
            
            if ($expiryDate->lt($minExpiryDate)) {
                $errors['passport_tanggal_kadaluarsa'] = 'Passport must be valid for at least 6 months from departure date';
            }
        }

        // Validate KTP NIK format (16 digits)
        if ($this->ktp_nik && !preg_match('/^\d{16}$/', $this->ktp_nik)) {
            $errors['ktp_nik'] = 'KTP NIK must be exactly 16 digits';
        }

        // Validate mahram for female jamaah under 45
        if ($this->gender === 'female' && $this->ktp_tanggal_lahir) {
            $age = \Carbon\Carbon::parse($this->ktp_tanggal_lahir)->age;
            if ($age < 45 && empty($this->mahram_name)) {
                $errors['mahram_name'] = 'Female jamaah under 45 years must have a registered mahram';
            }
        }

        // Validate age requirements
        if ($this->ktp_tanggal_lahir && $this->jamaah_type) {
            $age = \Carbon\Carbon::parse($this->ktp_tanggal_lahir)->age;
            if ($this->jamaah_type === 'umrah' && $age < 12) {
                $errors['ktp_tanggal_lahir'] = 'Jamaah must be at least 12 years old for Umrah';
            }
            if ($this->jamaah_type === 'hajj' && $age < 18) {
                $errors['ktp_tanggal_lahir'] = 'Jamaah must be at least 18 years old for Hajj';
            }
        }

        return $errors;
    }

    /**
     * Check if jamaah has complete required documents
     */
    public function hasCompleteDocuments()
    {
        if (!$this->is_jamaah) {
            return true;
        }

        return !empty($this->ktp_nik) &&
               !empty($this->passport_nomor) &&
               !empty($this->passport_tanggal_kadaluarsa);
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
     * Accessor for full_name (alias for nama)
     */
    public function getFullNameAttribute()
    {
        return $this->nama ?? '';
    }

    /**
     * Accessor for phone_number (alias for telepon)
     */
    public function getPhoneNumberAttribute()
    {
        return $this->telepon ?? '';
    }

    /**
     * Scope untuk menambahkan total piutang dalam query
     */
    public function scopeWithTotalPiutang($query)
    {
        return $query->addSelect([
            'total_piutang' => Piutang::selectRaw('COALESCE(SUM(sisa_piutang), 0)')
                ->whereColumn('id_member', 'member.id_member')
                ->where('status', 'belum_lunas')
        ]);
    }

    /**
     * Boot method to add event listeners
     */
    protected static function boot()
    {
        parent::boot();
        
        // Clear customer cache when member is created, updated, or deleted
        static::created(function ($member) {
            \App\Services\CacheService::clearCustomerCache();
        });
        
        static::updated(function ($member) {
            \App\Services\CacheService::clearCustomerCache();
        });
        
        static::deleted(function ($member) {
            \App\Services\CacheService::clearCustomerCache();
        });
    }

}