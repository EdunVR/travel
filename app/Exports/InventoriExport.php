<?php

namespace App\Exports;

use App\Models\Inventori;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoriExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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

        $query = Inventori::with(['outlet', 'kategori']);

        if ($this->request && is_object($this->request)) {
            if (method_exists($this->request, 'has') && $this->request->has('outlet') && $this->request->outlet !== 'ALL') {
                $query->where('id_outlet', $this->request->outlet);
            }

            if (method_exists($this->request, 'has') && $this->request->has('status') && $this->request->status !== 'ALL') {
                $query->where('status', $this->request->status);
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'KODE_INVENTORI',
            'NAMA_BARANG', 
            'KATEGORI',
            'OUTLET',
            'PENANGGUNG_JAWAB',
            'STOK',
            'LOKASI_PENYIMPANAN',
            'STATUS',
            'CATATAN'
        ];
    }

    public function map($inventori): array
    {
        if ($this->isTemplate) {
            return [];
        }

        return [
            $inventori->kode_inventori,
            $inventori->nama_barang,
            $inventori->kategori ? $inventori->kategori->nama_kategori : '',
            $inventori->outlet ? $inventori->outlet->nama_outlet : '',
            $inventori->penanggung_jawab,
            $inventori->stok,
            $inventori->lokasi_penyimpanan,
            $inventori->status,
            $inventori->catatan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}