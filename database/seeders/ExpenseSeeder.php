<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first outlet and expense accounts
        $outlet = \App\Models\Outlet::first();
        if (!$outlet) {
            $this->command->error('No outlet found. Please create an outlet first.');
            return;
        }

        // Get expense accounts (type = expense)
        $expenseAccounts = \App\Models\ChartOfAccount::where('outlet_id', $outlet->id_outlet)
            ->where('type', 'expense')
            ->get();

        if ($expenseAccounts->isEmpty()) {
            $this->command->error('No expense accounts found. Please create expense accounts first.');
            return;
        }

        $categories = ['operational', 'administrative', 'marketing', 'maintenance'];
        $statuses = ['pending', 'approved', 'rejected'];
        
        $descriptions = [
            'operational' => [
                'Pembayaran listrik bulan ini',
                'Biaya air dan kebersihan',
                'Pembelian bahan bakar',
                'Biaya internet dan telepon'
            ],
            'administrative' => [
                'Pembelian alat tulis kantor',
                'Biaya fotokopi dan cetak',
                'Pembelian perlengkapan kantor',
                'Biaya administrasi bank'
            ],
            'marketing' => [
                'Iklan media sosial',
                'Biaya promosi produk',
                'Pembuatan brosur dan spanduk',
                'Event marketing'
            ],
            'maintenance' => [
                'Perbaikan AC kantor',
                'Service kendaraan operasional',
                'Pemeliharaan gedung',
                'Perbaikan peralatan'
            ]
        ];

        // Get first user for approved_by
        $user = \App\Models\User::first();
        $approvedBy = $user ? $user->id : null;

        // Create 20 sample expenses
        for ($i = 1; $i <= 20; $i++) {
            $category = $categories[array_rand($categories)];
            $status = $statuses[array_rand($statuses)];
            $account = $expenseAccounts->random();
            $description = $descriptions[$category][array_rand($descriptions[$category])];
            
            $expense = \App\Models\Expense::create([
                'outlet_id' => $outlet->id_outlet,
                'account_id' => $account->id,
                'reference_number' => 'EXP-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'expense_date' => now()->subDays(rand(0, 30)),
                'category' => $category,
                'description' => $description,
                'amount' => rand(500000, 10000000),
                'status' => $status,
                'notes' => $status === 'rejected' ? 'Ditolak karena tidak sesuai budget' : null,
                'approved_by' => ($status !== 'pending' && $approvedBy) ? $approvedBy : null,
                'approved_at' => $status !== 'pending' ? now()->subDays(rand(0, 5)) : null
            ]);
        }

        $this->command->info('20 sample expenses created successfully!');
    }
}
