<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\Member;
use App\Models\InvestorCustomer;
use Illuminate\Http\Request;
use DB;

class InvestorCustomerController extends Controller
{
    public function index(Investor $investor)
    {
        $customers = $investor->customers()->with('member')->get();
        $availableMembers = Member::whereNotIn('id_member', $customers->pluck('id_member'))->get();
        
        return view('irp.investor.customers', compact('investor', 'customers', 'availableMembers'));
    }

    public function store(Request $request, Investor $investor)
    {

        if ($investor->customers()->count() >= $investor->kuota) {
            return back()->with('error', 'Kuota investor sudah penuh');
        }

        DB::beginTransaction();
        try {
            $investor->customers()->create([
                'id_member' => $request->id_member, // Pastikan menggunakan member_id bukan id_member
                'biaya' => $request->biaya,
                'status' => 'pending'
            ]);

            DB::commit();
            return back()->with('success', 'Customer berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan customer: ' . $e->getMessage())
                         ->withInput();
        }
    }

    public function verifyPayment(Investor $investor, $customerId)
    {
        $customer = InvestorCustomer::findOrFail($customerId);
        
        DB::transaction(function() use ($customer) {
            $customer->update([
                'status' => 'paid',
                'payment_date' => now()
            ]);
            
            $account = $customer->investor->accounts()->active()->first();
            if ($account) {
                $profit = $customer->investor->estimasi_keuntungan * ($account->profit_percentage / 100);
                
                $account->investments()->create([
                    'date' => now(),
                    'type' => 'deposit',
                    'amount' => $profit,
                    'description' => 'Bagi hasil dari customer: ' . $customer->member->nama
                ]);
            }
        });

        return back()->with('success', 'Pembayaran berhasil diverifikasi');
    }

    public function destroy(InvestorCustomer $customer)
    {
        $customer->delete();
        return back()->with('success', 'Customer berhasil dihapus');
    }
}