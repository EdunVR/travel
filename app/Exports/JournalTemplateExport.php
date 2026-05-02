<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class JournalTemplateExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct($data = null)
    {
        // If no data provided, use default sample data
        $this->data = $data ?? [
            (object)[
                'tanggal' => '2024-01-15',
                'no_transaksi' => 'JRN-001',
                'kode_akun' => '1010',
                'nama_akun' => 'Kas',
                'deskripsi' => 'Penerimaan kas dari penjualan',
                'debit' => 5000000,
                'kredit' => 0,
                'keterangan' => 'Contoh keterangan entri'
            ],
            (object)[
                'tanggal' => '2024-01-15',
                'no_transaksi' => 'JRN-001',
                'kode_akun' => '4010',
                'nama_akun' => 'Pendapatan Penjualan',
                'deskripsi' => 'Penerimaan kas dari penjualan',
                'debit' => 0,
                'kredit' => 5000000,
                'keterangan' => 'Contoh keterangan entri'
            ],
            (object)[
                'tanggal' => '2024-01-16',
                'no_transaksi' => 'JRN-002',
                'kode_akun' => '5010',
                'nama_akun' => 'Beban Gaji',
                'deskripsi' => 'Pembayaran gaji karyawan',
                'debit' => 3000000,
                'kredit' => 0,
                'keterangan' => 'Contoh keterangan entri'
            ],
            (object)[
                'tanggal' => '2024-01-16',
                'no_transaksi' => 'JRN-002',
                'kode_akun' => '1010',
                'nama_akun' => 'Kas',
                'deskripsi' => 'Pembayaran gaji karyawan',
                'debit' => 0,
                'kredit' => 3000000,
                'keterangan' => 'Contoh keterangan entri'
            ],
        ];
    }

    /**
     * Return collection of data
     */
    public function collection()
    {
        return collect($this->data);
    }

    /**
     * Map data to array format
     */
    public function map($row): array
    {
        return [
            $row->tanggal ?? '',
            $row->no_transaksi ?? '',
            $row->kode_akun ?? '',
            $row->nama_akun ?? '',
            $row->keterangan ?? '',
            $row->debit ?? 0,
            $row->kredit ?? 0,
        ];
    }

    /**
     * Return column headings
     */
    public function headings(): array
    {
        return [
            'tanggal',
            'no_transaksi',
            'kode_akun',
            'nama_akun',
            'keterangan',
            'debit',
            'kredit',
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
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
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Style data rows
            'A2:G100' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // tanggal
            'B' => 15,  // no_transaksi
            'C' => 12,  // kode_akun
            'D' => 30,  // nama_akun
            'E' => 40,  // keterangan
            'F' => 15,  // debit
            'G' => 15,  // kredit
        ];
    }
}
