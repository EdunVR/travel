<?php

namespace App\Imports;

use App\Models\Inventori;
use App\Models\Kategori;
use App\Models\Outlet;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class InventoriImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cari atau generate kode inventori
        $kodeInventori = $row['kode_inventori'] ?? Inventori::generateKodeInventori();

        // Cari kategori berdasarkan nama
        $kategori = Kategori::where('nama_kategori', $row['kategori'])->first();
        $idKategori = $kategori ? $kategori->id_kategori : null;

        // Cari outlet berdasarkan nama
        $outlet = Outlet::where('nama_outlet', $row['outlet'])->first();
        $idOutlet = $outlet ? $outlet->id_outlet : null;

        return new Inventori([
            'kode_inventori' => $kodeInventori,
            'nama_barang' => $row['nama_barang'],
            'id_kategori' => $idKategori,
            'id_outlet' => $idOutlet,
            'penanggung_jawab' => $row['penanggung_jawab'],
            'stok' => $row['stok'] ?? 0,
            'lokasi_penyimpanan' => $row['lokasi_penyimpanan'],
            'status' => isset($row['status']) ? 
                (strtolower($row['status']) === 'tersedia' ? 'tersedia' : 'tidak tersedia') : 'tersedia',
            'catatan' => $row['catatan'] ?? '',
            'is_active' => true
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_barang' => 'required',
            'kategori' => 'required|exists:kategori,nama_kategori',
            'outlet' => 'required|exists:outlets,nama_outlet',
            'penanggung_jawab' => 'required',
            'stok' => 'required|integer|min:0',
            'lokasi_penyimpanan' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_barang.required' => 'Nama barang wajib diisi',
            'kategori.required' => 'Kategori wajib diisi',
            'kategori.exists' => 'Kategori tidak ditemukan',
            'outlet.required' => 'Outlet wajib diisi',
            'outlet.exists' => 'Outlet tidak ditemukan',
            'penanggung_jawab.required' => 'Penanggung jawab wajib diisi',
            'stok.required' => 'Stok wajib diisi',
            'stok.integer' => 'Stok harus berupa angka',
            'stok.min' => 'Stok minimal 0',
            'lokasi_penyimpanan.required' => 'Lokasi penyimpanan wajib diisi',
        ];
    }
}