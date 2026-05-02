<?php

namespace App\Exports;

use App\Models\ChartOfAccount;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ChartOfAccountsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $outletId;

    public function __construct($outletId)
    {
        $this->outletId = $outletId;
    }

    public function collection()
    {
        return ChartOfAccount::with(['parent', 'outlet'])
            ->where('outlet_id', $this->outletId)
            ->orderBy('code')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Nama Akun',
            'Tipe',
            'Kategori',
            'Akun Induk',
            'Status',
            'Deskripsi',
            'Level'
        ];
    }

    public function map($account): array
    {
        return [
            $account->code,
            $account->name,
            $account->type,
            $account->category,
            $account->parent ? $account->parent->code : '',
            $account->status,
            $account->description,
            $account->level
        ];
    }
}