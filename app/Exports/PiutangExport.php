<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PiutangExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['data']);
    }

    public function headings(): array
    {
        return [
            'No',
            'Source',
            'No Invoice',
            'Tanggal',
            'Customer',
            'Outlet',
            'Jumlah Piutang',
            'Dibayar',
            'Sisa',
            'Jatuh Tempo',
            'Status',
            'Keterangan'
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $status = 'Belum Lunas';
        if ($row['status'] === 'lunas') {
            $status = 'Lunas';
        } elseif ($row['status'] === 'dibayar_sebagian') {
            $status = 'Dibayar Sebagian';
        }

        $keterangan = '';
        if ($row['is_overdue']) {
            $keterangan = 'Terlambat ' . $row['days_overdue'] . ' hari';
        }

        return [
            $no,
            strtoupper($row['source']),
            $row['invoice_number'],
            date('d/m/Y', strtotime($row['tanggal'])),
            $row['nama_customer'],
            $row['outlet'],
            $row['jumlah_piutang'],
            $row['jumlah_dibayar'],
            $row['sisa_piutang'],
            $row['tanggal_jatuh_tempo'] ? date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) : '-',
            $status,
            $keterangan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
