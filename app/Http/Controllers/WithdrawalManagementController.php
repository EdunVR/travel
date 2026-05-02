<?php

namespace App\Http\Controllers;

use App\Models\InvestorWithdrawal;
use App\Models\AccountInvestment;
use App\Models\InvestorAccount;
use App\Models\Investor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalManagementController extends Controller
{
    public function index()
    {
        // Mengambil data dari kedua tabel: InvestorWithdrawal dan AccountInvestment dengan type 'withdrawal'
        $withdrawalFromInvestor = InvestorWithdrawal::with(['investor', 'account'])
            ->select(
                'id',
                'investor_id',
                'account_id',
                'amount',
                'requested_at',
                'notes',
                'status',
                'approved_at',
                'approved_by',
                DB::raw("'investor_withdrawal' as source_table"),
                DB::raw("NULL as date"),
                DB::raw("NULL as description")
            )
            ->where('status', 'pending');

        $withdrawalFromInvestment = AccountInvestment::with(['account.investor'])
            ->select(
                'id',
                DB::raw("NULL as investor_id"),
                'account_id',
                'amount',
                DB::raw("NULL as requested_at"),
                DB::raw("NULL as notes"),
                DB::raw("'pending' as status"),
                DB::raw("NULL as approved_at"),
                DB::raw("NULL as approved_by"),
                DB::raw("'account_investment' as source_table"),
                'date',
                'description'
            )
            ->where('type', 'withdrawal')
            ->whereNotIn('id', function($query) {
                // Cek jika sudah ada di investor_withdrawals melalui kolom lain atau hubungan lain
                $query->select(DB::raw('1'))
                    ->from('investor_withdrawals')
                    ->whereColumn('investor_withdrawals.amount', 'account_investments.amount')
                    ->whereDate('investor_withdrawals.requested_at', DB::raw('DATE(account_investments.date)'));
            }); // Logika alternatif untuk menghindari duplikasi

        // Gabungkan kedua query
        $withdrawals = $withdrawalFromInvestor
            ->unionAll($withdrawalFromInvestment)
            ->orderBy('requested_at', 'desc')
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('irp.withdrawal_management.index', [
            'withdrawals' => $withdrawals
        ]);
    }

    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $withdrawal = InvestorWithdrawal::with('account')->findOrFail($id);
            
            // Validate withdrawal can be approved
            if ($withdrawal->status !== 'pending') {
                return back()->with('error', 'Hanya pencairan dengan status pending yang dapat disetujui');
            }

            if ($withdrawal->account->profit_balance < $withdrawal->amount) {
                return back()->with('error', 'Saldo profit tidak mencukupi untuk pencairan ini');
            }

            // Update withdrawal status
            $withdrawal->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id()
            ]);

            // Create withdrawal record in AccountInvestment (jika diperlukan)
            // Hapus atau modifikasi bagian ini sesuai kebutuhan
            AccountInvestment::create([
                'account_id' => $withdrawal->account_id,
                'date' => now(),
                'type' => 'withdrawal',
                'amount' => $withdrawal->amount,
                'description' => 'Pencairan dana - ' . ($withdrawal->notes ?? 'Tanpa catatan'),
                // Hapus reference_id dan reference_type jika kolom tidak ada
            ]);

            // Update account balance
            $withdrawal->account->update([
                'profit_balance' => DB::raw('profit_balance - ' . $withdrawal->amount)
            ]);

            DB::commit();

            return back()->with('success', 'Pencairan berhasil disetujui');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving withdrawal: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyetujui pencairan: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        DB::beginTransaction();
        try {
            $withdrawal = InvestorWithdrawal::findOrFail($id);
            
            if ($withdrawal->status !== 'pending') {
                return back()->with('error', 'Hanya pencairan dengan status pending yang dapat ditolak');
            }

            $withdrawal->update([
                'status' => 'rejected',
                'approved_at' => now(),
                'approved_by' => auth()->id()
            ]);

            DB::commit();

            return back()->with('success', 'Pencairan berhasil ditolak');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting withdrawal: ' . $e->getMessage());
            return back()->with('error', 'Gagal menolak pencairan: ' . $e->getMessage());
        }
    }

    // Tambahkan method baru untuk menangani approval dari account_investment
    public function approveInvestment($id)
    {
        DB::beginTransaction();
        try {
            $investment = AccountInvestment::with('account')->findOrFail($id);
            
            if ($investment->type !== 'withdrawal') {
                return back()->with('error', 'Hanya tipe withdrawal yang dapat disetujui');
            }

            if ($investment->account->profit_balance < $investment->amount) {
                return back()->with('error', 'Saldo profit tidak mencukupi untuk pencairan ini');
            }

            // Update account balance
            $investment->account->update([
                'profit_balance' => DB::raw('profit_balance - ' . $investment->amount)
            ]);

            // Optionally, Anda bisa menandai sebagai diproses dengan cara lain
            // Misalnya dengan menambah kolom status di account_investments

            DB::commit();

            return back()->with('success', 'Pencairan dari investment berhasil diproses');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving investment withdrawal: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses pencairan: ' . $e->getMessage());
        }
    }
}