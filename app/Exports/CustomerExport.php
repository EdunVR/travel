<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CustomerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $outletFilter;
    protected $tipeFilter;

    public function __construct($outletFilter = 'all', $tipeFilter = 'all')
    {
        $this->outletFilter = $outletFilter;
        $this->tipeFilter = $tipeFilter;
    }

    public function collection()
    {
        $query = Member::with(['tipe', 'outlet']);

        if ($this->outletFilter !== 'all') {
            $query->where('id_outlet', $this->outletFilter);
        }

        if ($this->tipeFilter !== 'all') {
            $query->where('id_tipe', $this->tipeFilter);
        }

        return $query->orderBy('nama', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'kode_member',
            'nama',
            'telepon',
            'alamat',
            'tipe_customer',
            'outlet',
        ];
    }

    public function map($member): array
    {
        return [
            $member->kode_member ?? '',
            $member->nama,
            $member->telepon,
            $member->alamat ?? '',
            $member->tipe ? $member->tipe->nama_tipe : '',
            $member->outlet ? $member->outlet->nama_outlet : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // kode_member
            'B' => 25,  // nama
            'C' => 15,  // telepon
            'D' => 35,  // alamat
            'E' => 20,  // tipe_customer
            'F' => 15,  // outlet
        ];
    }
}
