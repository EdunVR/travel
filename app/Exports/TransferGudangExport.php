<?php

namespace App\Exports;

use App\Models\PermintaanPengiriman;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransferGudangExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = PermintaanPengiriman::with(['outletAsal', 'outletTujuan', 'produk', 'bahan', 'inventori']);

        if ($this->request && method_exists($this->request, 'has') && $this->request->has('status') && $this->request->status !== 'ALL') {
            $query->where('status', $this->request->status);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL',
            'OUTLET ASAL',
            'OUTLET TUJUAN',
            'JENIS ITEM',
            'NAMA ITEM',
            'JUMLAH',
            'STATUS',
            'KETERANGAN'
        ];
    }

    public function map($transfer): array
    {
        $itemName = '';
        $itemType = '';

        if ($transfer->id_produk) {
            $itemName = $transfer->produk->nama_produk ?? $transfer->nama_produk;
            $itemType = 'Produk';
        } elseif ($transfer->id_bahan) {
            $itemName = $transfer->bahan->nama_bahan ?? $transfer->nama_bahan;
            $itemType = 'Bahan';
        } elseif ($transfer->id_inventori) {
            $itemName = $transfer->inventori->nama_barang ?? $transfer->nama_barang;
            $itemType = 'Inventori';
        }

        return [
            $transfer->id_permintaan,
            $transfer->created_at->format('d/m/Y H:i'),
            $transfer->outletAsal->nama_outlet ?? '-',
            $transfer->outletTujuan->nama_outlet ?? '-',
            $itemType,
            $itemName,
            $transfer->jumlah,
            ucfirst($transfer->status),
            $this->getStatusDescription($transfer->status)
        ];
    }

    private function getStatusDescription($status)
    {
        $descriptions = [
            'menunggu' => 'Menunggu persetujuan',
            'disetujui' => 'Telah disetujui dan stok dipindahkan',
            'ditolak' => 'Permintaan ditolak'
        ];

        return $descriptions[$status] ?? '-';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:I' => ['alignment' => ['vertical' => 'center']],
        ];
    }
}