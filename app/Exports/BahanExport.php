<?php

namespace App\Exports;

use App\Models\Bahan;
use App\Models\Outlet;
use App\Models\Satuan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BahanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        
        $query = Bahan::with(['outlet', 'satuan'])
            ->withSum('hargaBahan', 'stok')
            ->when($userOutlets, function ($query) use ($userOutlets) {
                return $query->whereIn('id_outlet', $userOutlets);
            });

        // Pastikan $this->request adalah object sebelum menggunakan has()
        if ($this->request && is_object($this->request)) {
            if (method_exists($this->request, 'has') && $this->request->has('outlet') && $this->request->outlet !== 'ALL') {
                $query->where('id_outlet', $this->request->outlet);
            }

            if (method_exists($this->request, 'has') && $this->request->has('unit') && $this->request->unit !== 'ALL') {
                $query->whereHas('satuan', function($q) use ($request) {
                    $q->where('nama_satuan', $this->request->unit);
                });
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'KODE_BAHAN',
            'NAMA_BAHAN', 
            'OUTLET',
            'MERK',
            'STOK',
            'SATUAN',
            'CATATAN',
            'STATUS'
        ];
    }

    public function map($bahan): array
    {
        if ($this->isTemplate) {
            return [];
        }

        return [
            $bahan->kode_bahan,
            $bahan->nama_bahan,
            $bahan->outlet ? $bahan->outlet->nama_outlet : '-',
            $bahan->merk ?: '-',
            $bahan->harga_bahan_sum_stok ?? 0,
            $bahan->satuan ? $bahan->satuan->nama_satuan : '-',
            $bahan->catatan ?: '',
            $bahan->is_active ? 'AKTIF' : 'NONAKTIF'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}