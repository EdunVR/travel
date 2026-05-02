<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesInvoiceTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                'INV/001/2024',
                '2024-01-15',
                'Customer Contoh',
                'OUT-001',
                '1000000',
                'menunggu',
                '2024-02-14',
                '2',
                'admin'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'NO_INVOICE*',
            'TANGGAL*',
            'CUSTOMER*',
            'OUTLET',
            'TOTAL*',
            'STATUS',
            'JATUH_TEMPO',
            'ITEM_COUNT',
            'PETUGAS'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:I1' => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E6F3FF']]]
        ];
    }
}