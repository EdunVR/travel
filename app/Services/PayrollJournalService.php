<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\PayrollCoaSetting;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollJournalService
{
    /**
     * Create journal entry when payroll is approved
     * 
     * Jurnal saat Approve:
     * Dr. Beban Gaji Pokok
     * Dr. Beban Lembur
     * Dr. Beban Bonus
     * Dr. Beban Tunjangan
     * Dr. Piutang Pinjaman Karyawan (potongan pinjaman)
     *     Cr. Hutang Pajak
     *     Cr. Hutang Gaji
     */
    public function createApprovalJournal(Payroll $payroll)
    {
        try {
            $coaSetting = PayrollCoaSetting::where('outlet_id', $payroll->outlet_id)->first();
            
            if (!$coaSetting) {
                throw new \Exception('COA Setting untuk payroll belum dikonfigurasi untuk outlet ini');
            }

            DB::beginTransaction();

            // Get default book (Buku Umum)
            $book = \App\Models\AccountingBook::where('code', 'BU')->first();
            if (!$book) {
                $book = \App\Models\AccountingBook::first();
            }

            // Create journal entry header
            $journal = JournalEntry::create([
                'book_id' => $book ? $book->id : null,
                'outlet_id' => $payroll->outlet_id,
                'transaction_number' => JournalEntry::generateTransactionNumber($book ? $book->id : 1),
                'transaction_date' => $payroll->payment_date,
                'reference_type' => 'payroll_approval',
                'reference_number' => 'PAYROLL-' . str_pad($payroll->id, 6, '0', STR_PAD_LEFT),
                'description' => "Approval Payroll - {$payroll->employee->name} - " . date('F Y', strtotime($payroll->period . '-01')),
                'status' => 'posted',
                'total_debit' => 0,
                'total_credit' => 0,
            ]);

            $details = [];

            // DEBIT: Beban Gaji Pokok
            if ($payroll->basic_salary > 0 && $coaSetting->salary_expense_account_id) {
                $details[] = [
                    'journal_entry_id' => $journal->id,
                    'account_id' => $coaSetting->salary_expense_account_id,
                    'description' => "Beban Gaji Pokok - {$payroll->employee->name}",
                    'debit' => $payroll->basic_salary,
                    'credit' => 0,
                ];
            }

            // DEBIT: Beban Lembur
            if ($payroll->overtime_pay > 0 && $coaSetting->overtime_expense_account_id) {
                $details[] = [
                    'journal_entry_id' => $journal->id,
                    'account_id' => $coaSetting->overtime_expense_account_id,
                    'description' => "Beban Lembur ({$payroll->overtime_hours} jam) - {$payroll->employee->name}",
                    'debit' => $payroll->overtime_pay,
                    'credit' => 0,
                ];
            }

            // DEBIT: Beban Bonus
            if ($payroll->bonus > 0 && $coaSetting->bonus_expense_account_id) {
                $details[] = [
                    'journal_entry_id' => $journal->id,
                    'account_id' => $coaSetting->bonus_expense_account_id,
                    'description' => "Beban Bonus - {$payroll->employee->name}",
                    'debit' => $payroll->bonus,
                    'credit' => 0,
                ];
            }

            // DEBIT: Beban Tunjangan
            if ($payroll->allowance > 0 && $coaSetting->allowance_expense_account_id) {
                $details[] = [
                    'journal_entry_id' => $journal->id,
                    'account_id' => $coaSetting->allowance_expense_account_id,
                    'description' => "Beban Tunjangan - {$payroll->employee->name}",
                    'debit' => $payroll->allowance,
                    'credit' => 0,
                ];
            }

            // DEBIT: Piutang Pinjaman Karyawan (potongan pinjaman)
            if ($payroll->loan_deduction > 0 && $coaSetting->loan_receivable_account_id) {
                $details[] = [
                    'journal_entry_id' => $journal->id,
                    'account_id' => $coaSetting->loan_receivable_account_id,
                    'description' => "Pelunasan Pinjaman - {$payroll->employee->name}",
                    'debit' => $payroll->loan_deduction,
                    'credit' => 0,
                ];
            }

            // CREDIT: Hutang Pajak
            if ($payroll->tax > 0 && $coaSetting->tax_payable_account_id) {
                $details[] = [
                    'journal_entry_id' => $journal->id,
                    'account_id' => $coaSetting->tax_payable_account_id,
                    'description' => "Hutang Pajak - {$payroll->employee->name}",
                    'debit' => 0,
                    'credit' => $payroll->tax,
                ];
            }

            // CREDIT: Hutang Gaji
            // Hutang gaji = Gaji Bersih + Pajak (karena pajak akan dibayar terpisah ke pemerintah)
            // Atau bisa juga: Hutang gaji = Net Salary (yang akan dibayarkan ke karyawan)
            if ($coaSetting->salary_payable_account_id) {
                // Net salary adalah yang akan dibayarkan ke karyawan
                // Sudah dikurangi semua potongan termasuk pajak, denda, dll
                $netPayable = $payroll->net_salary;
                
                $details[] = [
                    'journal_entry_id' => $journal->id,
                    'account_id' => $coaSetting->salary_payable_account_id,
                    'description' => "Hutang Gaji - {$payroll->employee->name}",
                    'debit' => 0,
                    'credit' => $netPayable,
                ];
            }

            // Insert all details
            foreach ($details as $detail) {
                JournalEntryDetail::create($detail);
            }

            // Update journal total
            $totalDebit = collect($details)->sum('debit');
            $totalCredit = collect($details)->sum('credit');
            
            $journal->update([
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ]);

            // Validate balanced
            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw new \Exception("Jurnal tidak balance. Debit: {$totalDebit}, Credit: {$totalCredit}");
            }

            DB::commit();

            Log::info("Payroll approval journal created", [
                'payroll_id' => $payroll->id,
                'journal_id' => $journal->id,
                'total' => $totalDebit
            ]);

            return $journal;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create payroll approval journal", [
                'payroll_id' => $payroll->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create journal entry when payroll is paid
     * 
     * Jurnal saat Pay:
     * Dr. Hutang Gaji
     *     Cr. Kas/Bank
     */
    public function createPaymentJournal(Payroll $payroll)
    {
        try {
            $coaSetting = PayrollCoaSetting::where('outlet_id', $payroll->outlet_id)->first();
            
            if (!$coaSetting) {
                throw new \Exception('COA Setting untuk payroll belum dikonfigurasi untuk outlet ini');
            }

            if (!$coaSetting->salary_payable_account_id || !$coaSetting->cash_account_id) {
                throw new \Exception('Akun Hutang Gaji atau Kas belum dikonfigurasi');
            }

            DB::beginTransaction();

            // Get default book (Buku Umum)
            $book = \App\Models\AccountingBook::where('code', 'BU')->first();
            if (!$book) {
                $book = \App\Models\AccountingBook::first();
            }

            // Create journal entry header
            $journal = JournalEntry::create([
                'book_id' => $book ? $book->id : null,
                'outlet_id' => $payroll->outlet_id,
                'transaction_number' => JournalEntry::generateTransactionNumber($book ? $book->id : 1),
                'transaction_date' => now(),
                'reference_type' => 'payroll_payment',
                'reference_number' => 'PAYROLL-PAY-' . str_pad($payroll->id, 6, '0', STR_PAD_LEFT),
                'description' => "Pembayaran Gaji - {$payroll->employee->name} - " . date('F Y', strtotime($payroll->period . '-01')),
                'status' => 'posted',
                'total_debit' => 0,
                'total_credit' => 0,
            ]);

            // DEBIT: Hutang Gaji
            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $coaSetting->salary_payable_account_id,
                'description' => "Pembayaran Gaji - {$payroll->employee->name}",
                'debit' => $payroll->net_salary,
                'credit' => 0,
            ]);

            // CREDIT: Kas/Bank
            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $coaSetting->cash_account_id,
                'description' => "Pembayaran Gaji - {$payroll->employee->name}",
                'debit' => 0,
                'credit' => $payroll->net_salary,
            ]);

            // Update journal total
            $journal->update([
                'total_debit' => $payroll->net_salary,
                'total_credit' => $payroll->net_salary,
            ]);

            DB::commit();

            Log::info("Payroll payment journal created", [
                'payroll_id' => $payroll->id,
                'journal_id' => $journal->id,
                'amount' => $payroll->net_salary
            ]);

            return $journal;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create payroll payment journal", [
                'payroll_id' => $payroll->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Reverse journal entries when payroll is deleted or status changed
     */
    public function reverseJournals(Payroll $payroll)
    {
        try {
            DB::beginTransaction();

            // Find all journals related to this payroll
            $referenceNumbers = [
                'PAYROLL-' . str_pad($payroll->id, 6, '0', STR_PAD_LEFT),
                'PAYROLL-PAY-' . str_pad($payroll->id, 6, '0', STR_PAD_LEFT)
            ];
            
            $journals = JournalEntry::where('reference_type', 'LIKE', 'payroll_%')
                ->whereIn('reference_number', $referenceNumbers)
                ->get();

            foreach ($journals as $journal) {
                // Soft delete or mark as reversed
                $journal->delete();
            }

            DB::commit();

            Log::info("Payroll journals reversed", [
                'payroll_id' => $payroll->id,
                'journals_count' => $journals->count()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to reverse payroll journals", [
                'payroll_id' => $payroll->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
