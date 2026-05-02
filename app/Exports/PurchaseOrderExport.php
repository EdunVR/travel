<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseOrderExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $status = $this->request->get('status', 'all');
        $startDate = $this->request->get('start_date');
        $endDate = $this->request->get('end_date');
        $supplierFilter = $this->request->get('supplier_filter', 'all');

        $query = PurchaseOrder::with(['supplier', 'outlet', 'items']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        if ($supplierFilter !== 'all') {
            $query->where('id_supplier', $supplierFilter);
        }

        return $query->orderBy('tanggal', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No PO',
            'Tanggal',
            'Supplier',
            'Outlet',
            'Jumlah Item',
            'Subtotal',
            'Diskon',
            'Total',
            'Status',
            'Jatuh Tempo',
            'Metode Pengiriman',
            'Keterangan'
        ];
    }

    public function map($po): array
    {
        return [
            $po->no_po,
            $po->tanggal->format('d/m/Y'),
            $po->supplier->nama_supplier ?? 'N/A',
            $po->outlet->nama_outlet ?? 'N/A',
            $po->items->count(),
            $po->subtotal,
            $po->total_diskon,
            $po->total,
            $this->getStatusText($po->status),
            $po->due_date ? $po->due_date->format('d/m/Y') : '-',
            $po->metode_pengiriman ?: '-',
            $po->keterangan ?: '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']]
            ],

            // Style the header row
            'A1:L1' => [
                'alignment' => ['horizontal' => 'center']
            ],

            // Set column widths
            'A' => ['width' => 15],
            'B' => ['width' => 12],
            'C' => ['width' => 20],
            'D' => ['width' => 15],
            'E' => ['width' => 12],
            'F' => ['width' => 15],
            'G' => ['width' => 15],
            'H' => ['width' => 15],
            'I' => ['width' => 12],
            'J' => ['width' => 12],
            'K' => ['width' => 15],
            'L' => ['width' => 25],
        ];
    }

    public function title(): string
    {
        return 'Purchase Orders';
    }

    private function getStatusText($status)
    {
        $statusMap = [
            'draft' => 'Draft',
            'diproses' => 'Diproses',
            'dikirim' => 'Dikirim',
            'diterima' => 'Diterima',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan'
        ];

        return $statusMap[$status] ?? $status;
    }
}