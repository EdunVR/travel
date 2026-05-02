<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LedgerPerAccountSheet implements FromCollection, WithTitle, WithHeadings
{
    protected $accountName;
    protected $transactions;

    public function __construct($accountName, $transactions)
    {
        $this->accountName = $accountName;
        $this->transactions = $transactions;
    }

    public function collection()
    {
        $data = collect($this->transactions)->map(function ($item) {
            return [
                $item['date'],
                $item['reference'],
                $item['description'],
                $item['debit'],
                $item['credit'],
                $item['balance']
            ];
        });
        
        // Tambahkan baris total
        $totalDebit = collect($this->transactions)->sum(fn($t) => str_replace(',', '', $t['debit']));
        $totalCredit = collect($this->transactions)->sum(fn($t) => str_replace(',', '', $t['credit']));
        $totalBalance = $totalDebit - $totalCredit;
        
        $data->push([
            'TOTAL',
            '',
            '',
            number_format($totalDebit, 2),
            number_format($totalCredit, 2),
            number_format($totalBalance, 2)
        ]);
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Reference',
            'Description',
            'Debit',
            'Credit',
            'Balance'
        ];
    }

    public function title(): string
    {
        // Hanya ambil 31 karakter pertama (batasan Excel)
        return substr($this->accountName, 0, 31);
    }
}