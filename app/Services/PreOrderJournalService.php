<?php

namespace App\Services;

use App\Models\PreOrder;
use App\Models\PreOrderSetting;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Illuminate\Support\Facades\DB;

class PreOrderJournalService
{
    public function createJournalEntry(PreOrder $preOrder, string $oldStatus, string $newStatus)
    {
        $settings = PreOrderSetting::getSettings();
        
        if (!$settings->id) {
            throw new \Exception('Pengaturan COA Pre Order belum dikonfigurasi');
        }

        // Only create journal entries for specific status transitions
        if ($oldStatus === 'penawaran' && $newStatus === 'invoice') {
            $this->createInvoiceJournal($preOrder, $settings);
        } elseif ($oldStatus === 'invoice' && $newStatus === 'lunas') {
            $this->createPaymentJournal($preOrder, $settings);
        }
    }

    private function createInvoiceJournal(PreOrder $preOrder, PreOrderSetting $settings)
    {
        DB::beginTransaction();
        
        try {
            $journalEntry = JournalEntry::create([
                'tanggal' => now(),
                'keterangan' => "Invoice Pre Order {$preOrder->kode_preorder}",
                'reference_type' => 'pre_order',
                'reference_id' => $preOrder->id,
                'total_debit' => $preOrder->total,
                'total_credit' => $preOrder->total
            ]);

            // Debit: Piutang
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'chart_of_account_id' => $settings->coa_piutang,
                'debit' => $preOrder->total,
                'credit' => 0,
                'keterangan' => "Piutang Pre Order {$preOrder->kode_preorder}"
            ]);

            // Credit: Penjualan
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'chart_of_account_id' => $settings->coa_penjualan,
                'debit' => 0,
                'credit' => $preOrder->total,
                'keterangan' => "Penjualan Pre Order {$preOrder->kode_preorder}"
            ]);

            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function createPaymentJournal(PreOrder $preOrder, PreOrderSetting $settings)
    {
        DB::beginTransaction();
        
        try {
            $remainingAmount = $preOrder->remaining_payment;
            
            $journalEntry = JournalEntry::create([
                'tanggal' => now(),
                'keterangan' => "Pelunasan Pre Order {$preOrder->kode_preorder}",
                'reference_type' => 'pre_order',
                'reference_id' => $preOrder->id,
                'total_debit' => $remainingAmount,
                'total_credit' => $remainingAmount
            ]);

            // Debit: Kas/Bank
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'chart_of_account_id' => $settings->coa_kas_bank,
                'debit' => $remainingAmount,
                'credit' => 0,
                'keterangan' => "Pelunasan Pre Order {$preOrder->kode_preorder}"
            ]);

            // Credit: Piutang
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'chart_of_account_id' => $settings->coa_piutang,
                'debit' => 0,
                'credit' => $remainingAmount,
                'keterangan' => "Pelunasan Pre Order {$preOrder->kode_preorder}"
            ]);

            // If there was a down payment, create entry for that too
            if ($preOrder->dp_amount > 0) {
                $dpJournalEntry = JournalEntry::create([
                    'tanggal' => $preOrder->updated_at,
                    'keterangan' => "DP Pre Order {$preOrder->kode_preorder}",
                    'reference_type' => 'pre_order',
                    'reference_id' => $preOrder->id,
                    'total_debit' => $preOrder->dp_amount,
                    'total_credit' => $preOrder->dp_amount
                ]);

                // Debit: Kas/Bank
                JournalEntryDetail::create([
                    'journal_entry_id' => $dpJournalEntry->id,
                    'chart_of_account_id' => $settings->coa_kas_bank,
                    'debit' => $preOrder->dp_amount,
                    'credit' => 0,
                    'keterangan' => "DP Pre Order {$preOrder->kode_preorder}"
                ]);

                // Credit: Uang Muka
                JournalEntryDetail::create([
                    'journal_entry_id' => $dpJournalEntry->id,
                    'chart_of_account_id' => $settings->coa_uang_muka,
                    'debit' => 0,
                    'credit' => $preOrder->dp_amount,
                    'keterangan' => "DP Pre Order {$preOrder->kode_preorder}"
                ]);
            }

            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}