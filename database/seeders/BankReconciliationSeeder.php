<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BankReconciliation;
use App\Models\BankReconciliationItem;
use App\Models\Outlet;
use App\Models\CompanyBankAccount;
use Carbon\Carbon;

class BankReconciliationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first outlet and bank account (COA with type asset)
        $outlet = Outlet::first();
        $bankAccount = \App\Models\ChartOfAccount::where('type', 'asset')
            ->where('status', 'active')
            ->where(function($q) {
                $q->where('name', 'like', '%bank%')
                  ->orWhere('name', 'like', '%kas%');
            })
            ->whereDoesntHave('children') // Only leaf accounts
            ->first();

        if (!$outlet) {
            $this->command->warn('⚠️  Tidak ada outlet. Seeder dibatalkan.');
            $this->command->info('💡 Pastikan sudah ada data outlet terlebih dahulu.');
            return;
        }

        if (!$bankAccount) {
            $this->command->warn('⚠️  Tidak ada akun bank (COA type asset). Seeder dibatalkan.');
            $this->command->info('💡 Pastikan sudah ada Chart of Account dengan type asset (bank/kas) terlebih dahulu.');
            return;
        }

        $this->command->info('🏦 Membuat sample data rekonsiliasi bank...');

        // Sample 1: Draft Reconciliation (Current Month)
        $draft = BankReconciliation::create([
            'outlet_id' => $outlet->id_outlet,
            'account_id' => $bankAccount->id,
            'reconciliation_date' => Carbon::now(),
            'period_month' => Carbon::now()->format('Y-m'),
            'bank_statement_balance' => 50000000,
            'book_balance' => 49500000,
            'adjusted_balance' => 49500000,
            'difference' => 500000,
            'status' => 'draft',
            'notes' => 'Rekonsiliasi bulan ' . Carbon::now()->format('F Y') . ' - masih dalam proses review',
            'reconciled_by' => 'Admin System'
        ]);

        // Add items for draft
        BankReconciliationItem::create([
            'reconciliation_id' => $draft->id,
            'transaction_date' => Carbon::now()->subDays(5),
            'transaction_number' => 'TRX-001',
            'description' => 'Biaya admin bank bulan ini',
            'amount' => 150000,
            'type' => 'credit',
            'status' => 'unreconciled',
            'category' => 'bank_charge',
            'notes' => 'Belum tercatat di buku'
        ]);

        BankReconciliationItem::create([
            'reconciliation_id' => $draft->id,
            'transaction_date' => Carbon::now()->subDays(3),
            'transaction_number' => 'TRX-002',
            'description' => 'Bunga bank',
            'amount' => 50000,
            'type' => 'debit',
            'status' => 'unreconciled',
            'category' => 'bank_interest',
            'notes' => 'Belum tercatat di buku'
        ]);

        BankReconciliationItem::create([
            'reconciliation_id' => $draft->id,
            'transaction_date' => Carbon::now()->subDays(7),
            'transaction_number' => 'TRX-003',
            'description' => 'Cek yang belum dicairkan',
            'amount' => 300000,
            'type' => 'credit',
            'status' => 'pending',
            'category' => 'outstanding_check',
            'notes' => 'Cek nomor 12345'
        ]);

        $this->command->info('✅ Draft reconciliation created (ID: ' . $draft->id . ')');

        // Sample 2: Completed Reconciliation (Last Month)
        $completed = BankReconciliation::create([
            'outlet_id' => $outlet->id_outlet,
            'account_id' => $bankAccount->id,
            'reconciliation_date' => Carbon::now()->subMonth(),
            'period_month' => Carbon::now()->subMonth()->format('Y-m'),
            'bank_statement_balance' => 45000000,
            'book_balance' => 45000000,
            'adjusted_balance' => 45000000,
            'difference' => 0,
            'status' => 'completed',
            'notes' => 'Rekonsiliasi bulan ' . Carbon::now()->subMonth()->format('F Y') . ' - sudah selesai, menunggu approval',
            'reconciled_by' => 'Admin System'
        ]);

        $this->command->info('✅ Completed reconciliation created (ID: ' . $completed->id . ')');

        // Sample 3: Approved Reconciliation (2 Months Ago)
        $approved = BankReconciliation::create([
            'outlet_id' => $outlet->id_outlet,
            'account_id' => $bankAccount->id,
            'reconciliation_date' => Carbon::now()->subMonths(2),
            'period_month' => Carbon::now()->subMonths(2)->format('Y-m'),
            'bank_statement_balance' => 40000000,
            'book_balance' => 39950000,
            'adjusted_balance' => 39950000,
            'difference' => 50000,
            'status' => 'approved',
            'notes' => 'Rekonsiliasi bulan ' . Carbon::now()->subMonths(2)->format('F Y') . ' - sudah disetujui',
            'reconciled_by' => 'Admin System',
            'approved_by' => 'Manager Finance',
            'approved_at' => Carbon::now()->subMonths(2)->addDays(5)
        ]);

        // Add items for approved
        BankReconciliationItem::create([
            'reconciliation_id' => $approved->id,
            'transaction_date' => Carbon::now()->subMonths(2)->subDays(3),
            'transaction_number' => 'TRX-OLD-001',
            'description' => 'Biaya admin bank',
            'amount' => 50000,
            'type' => 'credit',
            'status' => 'reconciled',
            'category' => 'bank_charge',
            'notes' => 'Sudah direkonsiliasi'
        ]);

        $this->command->info('✅ Approved reconciliation created (ID: ' . $approved->id . ')');

        $this->command->info('');
        $this->command->info('🎉 Seeder selesai! Total: 3 rekonsiliasi bank');
        $this->command->info('   - 1 Draft (dengan 3 items)');
        $this->command->info('   - 1 Completed');
        $this->command->info('   - 1 Approved (dengan 1 item)');
        $this->command->info('');
        $this->command->info('💡 Akses di: /admin/finance/rekonsiliasi');
    }
}
