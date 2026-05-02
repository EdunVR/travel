<?php

namespace App\Exports;

use App\Models\Outlet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OutletExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;
    protected $isTemplate;

    public function __construct($request = null, $isTemplate = false)
    {
        $this->isTemplate = $isTemplate;
        
        // Handle jika $request adalah boolean (untuk template)
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

        $query = Outlet::query();

        // Pastikan $this->request adalah object sebelum menggunakan has()
        if ($this->request && is_object($this->request)) {
            if (method_exists($this->request, 'has') && $this->request->has('kota') && $this->request->kota !== 'ALL') {
                $query->where('kota', $this->request->kota);
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
            'KODE_OUTLET',
            'NAMA_OUTLET', 
            'KOTA',
            'ALAMAT',
            'TELEPON',
            'STATUS',
            'CATATAN'
        ];
    }

    public function map($outlet): array
    {
        if ($this->isTemplate) {
            return [];
        }

        return [
            $outlet->kode_outlet,
            $outlet->nama_outlet,
            $outlet->kota,
            $outlet->alamat,
            $outlet->telepon,
            $outlet->is_active ? 'AKTIF' : 'NONAKTIF',
            $outlet->catatan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}