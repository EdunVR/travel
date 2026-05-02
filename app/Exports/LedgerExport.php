<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LedgerExport implements WithMultipleSheets
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        foreach ($this->data as $accountName => $transactions) {
            $sheets[] = new LedgerSheet($accountName, $transactions);
        }
        
        return $sheets;
    }
}

class LedgerSheet implements FromArray
{
    protected $accountName;
    protected $transactions;

    public function __construct($accountName, $transactions)
    {
        $this->accountName = $accountName;
        $this->transactions = $transactions;
    }

    public function array(): array
    {
        // Header
        $result = [
            ['Buku Besar - ' . $this->accountName],
            [],
            ['Tanggal', 'No. Referensi', 'Keterangan', 'Debit', 'Kredit', 'Saldo']
        ];
        
        // Data
        foreach ($this->transactions as $transaction) {
            $result[] = [
                $transaction['Tanggal'],
                $transaction['No. Referensi'],
                $transaction['Keterangan'],
                $transaction['Debit'],
                $transaction['Kredit'],
                $transaction['Saldo']
            ];
        }
        
        // Total
        $totalDebit = array_sum(array_column($this->transactions, '_debit'));
        $totalCredit = array_sum(array_column($this->transactions, '_credit'));
        
        $result[] = [];
        $result[] = [
            'TOTAL',
            '',
            '',
            number_format($totalDebit, 2),
            number_format($totalCredit, 2),
            number_format($totalDebit - $totalCredit, 2)
        ];
        
        return $result;
    }

    public function title(): string
    {
        // Singkatkan nama sheet jika terlalu panjang
        return substr(str_replace(['/', '\\', '?', '*', '[', ']'], '', $this->accountName), 0, 31);
    }
}