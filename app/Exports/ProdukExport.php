<?php

namespace App\Exports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProdukExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;
    protected $isTemplate;

    public function __construct($request = null, $isTemplate = false)
    {
        $this->isTemplate = $isTemplate;
        
        if ($isTemplate || $request === true) {
            $this->isTemplate = true;
            $this->request = null;
        } else {
            $this->request = $request;
            $this->isTemplate = false;
        }
    }

    public function collection()
    {
        if ($this->isTemplate) {
            return collect([]);
        }

        $query = Produk::with(['kategori', 'satuan', 'outlet'])
            ->withSum('hppProduk', 'stok');

        if ($this->request && is_object($this->request)) {
            if (method_exists($this->request, 'has') && $this->request->has('outlet') && $this->request->outlet !== 'ALL') {
                $query->whereHas('outlet', function($q) {
                    $q->where('nama_outlet', $this->request->outlet);
                });
            }

            if (method_exists($this->request, 'has') && $this->request->has('type') && $this->request->type !== 'ALL') {
                $query->where('tipe_produk', $this->request->type);
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'KODE_PRODUK',
            'NAMA_PRODUK',
            'OUTLET',
            'TIPE_PRODUK',
            'KATEGORI',
            'SATUAN',
            'MERK',
            'HARGA_JUAL',
            'DISKON',
            'STOK',
            'STATUS'
        ];
    }

    public function map($produk): array
    {
        if ($this->isTemplate) {
            return [];
        }

        return [
            $produk->kode_produk,
            $produk->nama_produk,
            $produk->outlet ? $produk->outlet->nama_outlet : '',
            $produk->tipe_produk,
            $produk->kategori ? $produk->kategori->nama_kategori : '',
            $produk->satuan ? $produk->satuan->nama_satuan : '',
            $produk->merk,
            $produk->harga_jual,
            $produk->diskon,
            $produk->hpp_produk_sum_stok ?? 0,
            $produk->is_active ? 'AKTIF' : 'NONAKTIF'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}