<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServiceInvoiceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $invoices;
    
    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }
    
    public function collection()
    {
        return $this->invoices;
    }
    
    public function headings(): array
    {
        return [
            'No Invoice',
            'Tanggal',
            'Customer',
            'Jenis Service',
            'Subtotal',
            'Diskon',
            'Total',
            'Status',
            'Due Date',
            'Sisa Hari',
            'Teknisi',
            'Jam',
            'Biaya Teknisi',
            'Garansi'
        ];
    }
    
    public function map($invoice): array
    {
        $sisaHari = null;
        if ($invoice->due_date) {
            $sisaHari = now()->diffInDays($invoice->due_date, false);
        }
        
        return [
            $invoice->no_invoice,
            $invoice->tanggal->format('d/m/Y H:i'),
            $invoice->member->nama ?? '-',
            $invoice->jenis_service,
            $invoice->total_sebelum_diskon,
            $invoice->diskon,
            $invoice->total_setelah_diskon,
            strtoupper($invoice->status),
            $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-',
            $sisaHari !== null ? $sisaHari . ' hari' : '-',
            $invoice->jumlah_teknisi,
            $invoice->jumlah_jam,
            $invoice->biaya_teknisi,
            $invoice->is_garansi ? 'Ya' : 'Tidak'
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
