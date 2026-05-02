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

class FixedAssetsTemplateExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct($data = null)
    {
        // If no data provided, use default sample data
        $this->data = $data ?? [
            (object)[
                'kode_aset' => 'FA-001',
                'nama_aset' => 'Komputer Dell Latitude',
                'kategori' => 'computer',
                'lokasi' => 'Kantor Pusat',
                'tanggal_perolehan' => '2024-01-10',
                'harga_perolehan' => 15000000,
                'nilai_residu' => 1000000,
                'umur_ekonomis' => 5,
                'metode_penyusutan' => 'straight_line',
                'deskripsi' => 'Komputer untuk staff accounting',
                'kode_akun_aset' => '1310',
                'kode_akun_beban' => '5120',
                'kode_akun_akumulasi' => '1320',
                'kode_akun_pembayaran' => '1010',
            ],
            (object)[
                'kode_aset' => 'FA-002',
                'nama_aset' => 'Meja Kantor Kayu Jati',
                'kategori' => 'furniture',
                'lokasi' => 'Ruang Meeting',
                'tanggal_perolehan' => '2024-01-15',
                'harga_perolehan' => 5000000,
                'nilai_residu' => 500000,
                'umur_ekonomis' => 10,
                'metode_penyusutan' => 'straight_line',
                'deskripsi' => 'Meja meeting untuk 8 orang',
                'kode_akun_aset' => '1310',
                'kode_akun_beban' => '5120',
                'kode_akun_akumulasi' => '1320',
                'kode_akun_pembayaran' => '1010',
            ],
            (object)[
                'kode_aset' => 'FA-003',
                'nama_aset' => 'Toyota Avanza 2024',
                'kategori' => 'vehicle',
                'lokasi' => 'Pool Kendaraan',
                'tanggal_perolehan' => '2024-02-01',
                'harga_perolehan' => 250000000,
                'nilai_residu' => 50000000,
                'umur_ekonomis' => 8,
                'metode_penyusutan' => 'declining_balance',
                'deskripsi' => 'Kendaraan operasional',
                'kode_akun_aset' => '1310',
                'kode_akun_beban' => '5120',
                'kode_akun_akumulasi' => '1320',
                'kode_akun_pembayaran' => '1010',
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
            $row->kode_aset ?? '',
            $row->nama_aset ?? '',
            $row->kategori ?? '',
            $row->lokasi ?? '',
            $row->tanggal_perolehan ?? '',
            $row->harga_perolehan ?? 0,
            $row->nilai_residu ?? 0,
            $row->umur_ekonomis ?? 0,
            $row->metode_penyusutan ?? '',
            $row->deskripsi ?? '',
            $row->kode_akun_aset ?? '',
            $row->kode_akun_beban ?? '',
            $row->kode_akun_akumulasi ?? '',
            $row->kode_akun_pembayaran ?? '',
        ];
    }

    /**
     * Return column headings
     */
    public function headings(): array
    {
        return [
            'kode_aset',
            'nama_aset',
            'kategori',
            'lokasi',
            'tanggal_perolehan',
            'harga_perolehan',
            'nilai_residu',
            'umur_ekonomis',
            'metode_penyusutan',
            'deskripsi',
            'kode_akun_aset',
            'kode_akun_beban',
            'kode_akun_akumulasi',
            'kode_akun_pembayaran',
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
            'A2:N100' => [
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
            'A' => 12,  // kode_aset
            'B' => 30,  // nama_aset
            'C' => 15,  // kategori
            'D' => 20,  // lokasi
            'E' => 18,  // tanggal_perolehan
            'F' => 18,  // harga_perolehan
            'G' => 15,  // nilai_residu
            'H' => 15,  // umur_ekonomis
            'I' => 20,  // metode_penyusutan
            'J' => 40,  // deskripsi
            'K' => 18,  // kode_akun_aset
            'L' => 18,  // kode_akun_beban
            'M' => 20,  // kode_akun_akumulasi
            'N' => 22,  // kode_akun_pembayaran
        ];
    }
}
