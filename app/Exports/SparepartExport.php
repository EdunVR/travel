<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SparepartExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $spareparts;
    protected $filters;

    public function __construct($spareparts, $filters)
    {
        $this->spareparts = $spareparts;
        $this->filters = $filters;
    }

    public function collection()
    {
        $data = collect();
        
        foreach ($this->spareparts as $sparepart) {
            // Add sparepart main data
            $data->push([
                'type' => 'sparepart',
                'sparepart' => $sparepart,
                'log' => null
            ]);
            
            // Add logs if available
            if (isset($sparepart->filtered_logs)) {
                foreach ($sparepart->filtered_logs as $log) {
                    $data->push([
                        'type' => 'log',
                        'sparepart' => $sparepart,
                        'log' => $log
                    ]);
                }
            }
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'Tipe',
            'Kode Sparepart',
            'Nama Sparepart',
            'Merk',
            'Harga',
            'Stok',
            'Satuan',
            'Stok Minimum',
            'Status',
            'Outlet',
            'Kode Log',
            'Tanggal Log',
            'Tipe Log',
            'Kategori',
            'Nilai Lama',
            'Perubahan',
            'Nilai Baru',
            'Karyawan',
            'Keterangan',
            'User Log'
        ];
    }

    public function map($row): array
    {
        if ($row['type'] === 'sparepart') {
            $sparepart = $row['sparepart'];
            return [
                'SPAREPART',
                $sparepart->kode_sparepart,
                $sparepart->nama_sparepart,
                $sparepart->merk ?? '-',
                $sparepart->harga,
                $sparepart->stok,
                $sparepart->satuan,
                $sparepart->stok_minimum,
                $sparepart->is_active ? 'Aktif' : 'Nonaktif',
                $sparepart->outlet ? $sparepart->outlet->nama_outlet : '-',
                '', '', '', '', '', '', '', '', '', ''
            ];
        } else {
            $log = $row['log'];
            return [
                'LOG',
                '', '', '', '', '', '', '', '', '',
                'LOG-' . str_pad($log->id_log, 6, '0', STR_PAD_LEFT),
                $log->created_at->format('d/m/Y H:i:s'),
                ucfirst($log->tipe_perubahan),
                $log->kategori ? ucfirst($log->kategori) : '-',
                $log->nilai_lama,
                $log->selisih > 0 ? '+' . $log->selisih : $log->selisih,
                $log->nilai_baru,
                $log->karyawan ? $log->karyawan->name : '-',
                $log->keterangan,
                $log->user ? $log->user->name : '-'
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Data Sparepart';
    }
}