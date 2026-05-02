<?php

namespace App\Exports;

use App\Models\Kategori;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KategoriExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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

        $userOutlets = auth()->user()->akses_outlet ?? [];
        
        $query = Kategori::with('outlet')
            ->when($userOutlets, function ($query) use ($userOutlets) {
                return $query->whereIn('id_outlet', $userOutlets);
            });

        if ($this->request && is_object($this->request)) {
            if (method_exists($this->request, 'has') && $this->request->has('kelompok') && $this->request->kelompok !== 'ALL') {
                $query->where('kelompok', $this->request->kelompok);
            }

            if (method_exists($this->request, 'has') && $this->request->has('status') && $this->request->status !== 'ALL') {
                $query->where('is_active', $this->request->status === 'ACTIVE');
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'KODE_KATEGORI',
            'NAMA_KATEGORI', 
            'KELOMPOK',
            'OUTLET',
            'DESKRIPSI',
            'STATUS'
        ];
    }

    public function map($kategori): array
    {
        if ($this->isTemplate) {
            return [];
        }

        return [
            $kategori->kode_kategori,
            $kategori->nama_kategori,
            $kategori->kelompok,
            $kategori->outlet ? $kategori->outlet->nama_outlet : '-',
            $kategori->deskripsi,
            $kategori->is_active ? 'AKTIF' : 'NONAKTIF'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}