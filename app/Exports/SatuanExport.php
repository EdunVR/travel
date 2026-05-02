<?php

namespace App\Exports;

use App\Models\Satuan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SatuanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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

        $query = Satuan::with('satuanUtama');

        if ($this->request && is_object($this->request)) {
            if (method_exists($this->request, 'has') && $this->request->has('status') && $this->request->status !== 'ALL') {
                $query->where('is_active', $this->request->status === 'ACTIVE');
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'KODE_SATUAN',
            'NAMA_SATUAN', 
            'SIMBOL',
            'DESKRIPSI',
            'STATUS',
            'NILAI_KONVERSI',
            'SATUAN_UTAMA'
        ];
    }

    public function map($satuan): array
    {
        if ($this->isTemplate) {
            return [];
        }

        return [
            $satuan->kode_satuan,
            $satuan->nama_satuan,
            $satuan->simbol,
            $satuan->deskripsi,
            $satuan->is_active ? 'AKTIF' : 'NONAKTIF',
            $satuan->nilai_konversi,
            $satuan->satuanUtama ? $satuan->satuanUtama->nama_satuan : ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}