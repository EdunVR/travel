<?php

namespace App\Imports;

use App\Models\Produk;
use App\Models\Outlet;
use App\Models\Kategori;
use App\Models\Satuan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProdukImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cari atau buat outlet
        $outlet = Outlet::where('nama_outlet', $row['outlet'])->first();
        if (!$outlet) {
            $outlet = Outlet::create(['nama_outlet' => $row['outlet']]);
        }

        // Cari kategori
        $kategori = Kategori::where('nama_kategori', $row['kategori'])->first();
        if (!$kategori) {
            $kategori = Kategori::create([
                'nama_kategori' => $row['kategori'],
                'id_outlet' => $outlet->id_outlet
            ]);
        }

        // Cari satuan
        $satuan = Satuan::where('nama_satuan', $row['satuan'])->first();
        if (!$satuan) {
            $satuan = Satuan::create(['nama_satuan' => $row['satuan']]);
        }

        // Generate kode produk
        $lastProduk = Produk::latest()->first();
        $kodeProduk = 'P' . str_pad(($lastProduk ? ((int) substr($lastProduk->kode_produk, 1)) + 1 : 1), 6, '0', STR_PAD_LEFT);

        return new Produk([
            'kode_produk' => $kodeProduk,
            'nama_produk' => $row['nama_produk'],
            'id_outlet' => $outlet->id_outlet,
            'id_kategori' => $kategori->id_kategori,
            'id_satuan' => $satuan->id_satuan,
            'tipe_produk' => $row['tipe_produk'] ?? 'barang_dagang',
            'merk' => $row['merk'] ?? '',
            'harga_jual' => $row['harga_jual'] ?? 0,
            'diskon' => $row['diskon'] ?? 0,
            'is_active' => strtoupper($row['status'] ?? 'AKTIF') === 'AKTIF',
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_produk' => 'required',
            'outlet' => 'required',
            'kategori' => 'required',
            'satuan' => 'required',
            'harga_jual' => 'required|numeric|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_produk.required' => 'Nama produk wajib diisi',
            'outlet.required' => 'Outlet wajib diisi',
            'kategori.required' => 'Kategori wajib diisi',
            'satuan.required' => 'Satuan wajib diisi',
            'harga_jual.required' => 'Harga jual wajib diisi',
            'harga_jual.numeric' => 'Harga jual harus berupa angka',
            'harga_jual.min' => 'Harga jual tidak boleh negatif',
        ];
    }
}