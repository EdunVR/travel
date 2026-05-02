<?php

namespace App\Imports;

use App\Models\Kategori;
use App\Models\Outlet;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class KategoriImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $userOutlets = auth()->user()->akses_outlet ?? [];
        
        // Cari outlet berdasarkan nama
        $outlet = null;
        if (isset($row['outlet']) && $row['outlet']) {
            $outlet = Outlet::where('nama_outlet', $row['outlet'])->first();
        }

        // Jika tidak ada outlet yang cocok, gunakan outlet pertama user
        $idOutlet = $outlet ? $outlet->id_outlet : ($userOutlets ? $userOutlets[0] : null);

        return new Kategori([
            'kode_kategori' => $row['kode_kategori'] ?? Kategori::generateKodeKategori(),
            'nama_kategori' => $row['nama_kategori'] ?? $row['nama_kategori'],
            'kelompok' => $row['kelompok'] ?? $row['kelompok'],
            'id_outlet' => $idOutlet,
            'deskripsi' => $row['deskripsi'] ?? $row['deskripsi'],
            'is_active' => isset($row['status']) ? 
                (strtoupper($row['status']) === 'AKTIF' ? true : false) : true,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_kategori' => 'required',
            'kelompok' => 'required|in:Produk,Bahan,Aset,Lainnya',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi',
            'kelompok.required' => 'Kelompok wajib diisi',
            'kelompok.in' => 'Kelompok harus salah satu dari: Produk, Bahan, Aset, Lainnya',
        ];
    }
}