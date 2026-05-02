<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyBankAccount;

class CompanyBankAccountSeeder extends Seeder
{
    public function run()
    {
        $bankAccounts = [
            [
                'id_outlet' => 1,
                'bank_name' => 'Bank Central Asia (BCA)',
                'account_number' => '1234567890',
                'account_holder_name' => 'PT. Contoh Perusahaan',
                'branch_name' => 'KCU Jakarta Pusat',
                'currency' => 'IDR',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'id_outlet' => 3,
                'bank_name' => 'Bank Mandiri',
                'account_number' => '0987654321',
                'account_holder_name' => 'PT. Contoh Perusahaan',
                'branch_name' => 'KCU Jakarta Selatan',
                'currency' => 'IDR',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'id_outlet' => 8,
                'bank_name' => 'Bank Negara Indonesia (BNI)',
                'account_number' => '1122334455',
                'account_holder_name' => 'PT. Contoh Perusahaan',
                'branch_name' => 'KCU Jakarta Barat',
                'currency' => 'IDR',
                'is_active' => true,
                'sort_order' => 3
            ]
        ];

        foreach ($bankAccounts as $account) {
            CompanyBankAccount::create($account);
        }
    }
}