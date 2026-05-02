<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    use HasFactory;

    protected $table = 'satuan';
    protected $primaryKey = 'id_satuan';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    // Tambahkan fillable untuk mass assignment
    protected $fillable = [
        'kode_satuan',
        'nama_satuan',
        'simbol',
        'deskripsi',
        'is_active',
        'nilai_konversi',
        'satuan_utama_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'nilai_konversi' => 'decimal:6'
    ];

    // Relasi ke satuan utama (untuk konversi)
    public function satuanUtama()
    {
        return $this->belongsTo(Satuan::class, 'satuan_utama_id', 'id_satuan');
    }

    // Satuan turunan
    public function satuanTurunan()
    {
        return $this->hasMany(Satuan::class, 'satuan_utama_id', 'id_satuan');
    }

    // Scope untuk filter aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_satuan', 'like', "%{$search}%")
              ->orWhere('kode_satuan', 'like', "%{$search}%")
              ->orWhere('simbol', 'like', "%{$search}%");
        });
    }

    // Method untuk generate kode satuan otomatis
    public static function generateKodeSatuan()
    {
        $prefix = 'SAT';
        $lastSatuan = self::where('kode_satuan', 'like', $prefix . '-%')
            ->orderBy('kode_satuan', 'desc')
            ->first();

        if ($lastSatuan) {
            $lastNumber = (int) substr($lastSatuan->kode_satuan, strlen($prefix) + 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Method untuk konversi satuan
    public function konversiKe($satuanTarget, $nilai)
    {
        // Jika sama, tidak perlu konversi
        if ($this->id_satuan == $satuanTarget->id_satuan) {
            return $nilai;
        }

        // Jika ini adalah satuan utama, konversi ke turunan
        if ($satuanTarget->satuan_utama_id == $this->id_satuan) {
            return $nilai * $satuanTarget->nilai_konversi;
        }

        // Jika target adalah satuan utama, konversi dari turunan
        if ($this->satuan_utama_id == $satuanTarget->id_satuan) {
            return $nilai / $this->nilai_konversi;
        }

        // Jika keduanya adalah turunan dari satuan utama yang sama
        if ($this->satuan_utama_id && $this->satuan_utama_id == $satuanTarget->satuan_utama_id) {
            $nilaiDalamUtama = $nilai / $this->nilai_konversi;
            return $nilaiDalamUtama * $satuanTarget->nilai_konversi;
        }

        // Tidak bisa dikonversi
        return null;
    }
}