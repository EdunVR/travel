<?php

namespace App\Exports;

use App\Models\SalesInvoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesInvoiceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = SalesInvoice::with(['member', 'items', 'outlet']);

        if ($this->request) {
            $status = $this->request->get('status', 'all');
            $startDate = $this->request->get('start_date');
            $endDate = $this->request->get('end_date');
            $outletFilter = $this->request->get('outlet_filter', 'all');

            if ($status !== 'all') {
                $query->where('status', $status);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal', [$startDate, $endDate]);
            }

            if ($outletFilter !== 'all') {
                $query->where('id_outlet', $outletFilter);
            }
        }

        return $query->orderBy('tanggal', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'NO_INVOICE',
            'TANGGAL',
            'CUSTOMER',
            'OUTLET', 
            'TOTAL',
            'STATUS',
            'JATUH_TEMPO',
            'ITEM_DETAILS', // Kolom baru untuk rincian items
            'PETUGAS'
        ];
    }

    public function map($invoice): array
    {
        // Format rincian items
        $itemDetails = '';
        foreach ($invoice->items as $index => $item) {
            $itemDetails .= ($index + 1) . '. ' . $item->deskripsi . 
                           ' (Qty: ' . $item->kuantitas . ' ' . ($item->satuan ?? 'Unit') . 
                           ', Harga: Rp ' . number_format($item->harga, 0, ',', '.') . 
                           ', Subtotal: Rp ' . number_format($item->subtotal, 0, ',', '.') . ")\n";
        }

        return [
            $invoice->no_invoice,
            $invoice->tanggal->format('d/m/Y'),
            $invoice->member ? $invoice->member->nama : 'Customer Tidak Ditemukan',
            $invoice->outlet ? $invoice->outlet->nama_outlet : '-',
            $invoice->total,
            $invoice->status,
            $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-',
            $itemDetails, // Masukkan rincian items
            $invoice->user ? $invoice->user->name : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'H' => ['alignment' => ['wrapText' => true]], // Wrap text untuk kolom item details
        ];
    }
}