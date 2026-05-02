<?php

namespace App\Http\Controllers;

use App\Models\ProfitManagement;
use App\Models\Investor;
use App\Models\InvestorAccount;
use App\Models\AccountInvestment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProfitGroup;
use App\Models\Produk;
use Illuminate\Support\Facades\Validator;
use App\Models\ProfitGroupHistory;
use App\Models\ProfitGroupDistribution;
use App\Models\ProfitGroupInvestor;

class ProfitManagementController extends Controller
{
    public function index()
    {
        $activeTab = request('tab', 'group');
        
        $data = [
            'activeTab' => $activeTab,
            'products' => Produk::all(),
            'investors' => Investor::where('status', 'active')->get()
        ];
        
        if ($activeTab === 'category') {
            $data['profits'] = ProfitManagement::orderBy('created_at', 'desc')->paginate(10);
        } elseif ($activeTab === 'history') {
            $data['histories'] = ProfitGroupHistory::with(['group', 'distributions.investor', 'distributions.account'])
                ->orderBy('distribution_date', 'desc')
                ->paginate(10);
        } else {
            $data['groups'] = ProfitGroup::with(['investors.investor', 'product', 'investors.account.investments'])
                ->get()
                ->each(function($group) {
                    $group->unique_investors_count = $group->investors->unique('investor_id')->count();
                    $group->valid_accounts_count = $group->investors
                        ->filter(function($investor) {
                            return $investor->account !== null;
                        })
                        ->count();
                        
                    // Hitung total investasi dari semua account investor
                    $group->total_investment_fix = $group->investors->sum(function($investor) {
                        return $investor->account ? $investor->account->total_investment : 0;
                    });
                });
        }
        
        return view('irp.profit_management.index', $data);
    }


    public function create()
    {
        return view('irp.profit_management.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period' => 'required|string|max:20',
            'total_profit' => 'required|numeric|min:0',
            'distribution_date' => 'required|date',
            'category' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        try {
            $profit = ProfitManagement::create([
                'period' => $validated['period'],
                'total_profit' => $validated['total_profit'],
                'distribution_date' => $validated['distribution_date'],
                'category' => $validated['category'] ?? null,
                'notes' => $validated['notes'],
                'status' => 'draft' // Status awal draft
            ]);

            return redirect()->route('irp.profit-management.show', $profit->id)
                ->with('success', 'Perhitungan bagi hasil berhasil disimpan sebagai draft');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan pembagian: '.$e->getMessage());
        }
    }

    public function show(ProfitManagement $profit)
    {
        // Dapatkan kategori filter dari request
        $categoryFilter = request('category', $profit->category);
        
        // Query investor dengan filter kategori
        $investors = Investor::where('status', 'active')
            ->when($categoryFilter, function($query) use ($categoryFilter) {
                return $query->where('category', $categoryFilter);
            })
            ->with(['accounts' => function($query) {
                $query->where('status', 'active')
                    ->with(['investments' => function($q) {
                        $q->where('type', 'investment');
                    }]);
            }])
            ->get();

        // Hitung total investasi
        $totalInvestment = $investors->sum(function($investor) {
            return $investor->accounts->sum(function($account) {
                return $account->investments->sum('amount');
            });
        });

        return view('irp.profit_management.show', [
            'profit' => $profit,
            'investors' => $investors,
            'totalInvestment' => $totalInvestment,
            'categories' => ['syirkah', 'investama', 'sukuk','internal', 'eksternal', 'aktif', 'pasif'], // Sesuaikan dengan kategori yang ada
            'selectedCategory' => $categoryFilter
        ]);
    }

    public function confirmPayment(Request $request, ProfitManagement $profit)
    {
        $request->validate([
            'proof_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        DB::beginTransaction();
        try {
            Log::info('Memulai proses konfirmasi pembayaran untuk profit ID: '.$profit->id);
            
            // Upload bukti transfer jika ada
            $proofPath = null;
            if ($request->hasFile('proof_file')) {
                $proofPath = $request->file('proof_file')->store('profit-proofs', 'public');
                Log::info('Bukti transfer diupload: '.$proofPath);
            }

            // Dapatkan kategori filter dari request
            $categoryFilter = $request->input('category');
            Log::info('Kategori filter yang digunakan: '.($categoryFilter ?? 'Semua kategori'));

            // Query investor dengan filter yang sama seperti di show()
            $investors = Investor::where('status', 'active')
                ->when($categoryFilter, function($query) use ($categoryFilter) {
                    return $query->where('category', $categoryFilter);
                })
                ->with(['accounts' => function($query) {
                    $query->where('status', 'active')
                        ->with(['investments' => function($q) {
                            $q->where('type', 'investment');
                        }]);
                }])
                ->get();

            Log::info('Jumlah investor yang akan menerima distribusi: '.$investors->count());

            // Hitung total investasi berdasarkan filter
            $totalInvestment = $investors->sum(function($investor) {
                return $investor->accounts->sum(function($account) {
                    return $account->investments->sum('amount');
                });
            });

            Log::info('Total investasi setelah filter: '.$totalInvestment);

            if ($totalInvestment <= 0) {
                throw new \Exception('Total investasi tidak valid atau nol');
            }

            // Hitung total profit share yang sudah didistribusikan
            $totalProfitShare = $investors->sum(function($investor) use ($profit) {
                return $investor->accounts->sum(function($account) use ($profit) {
                    return $account->investments()
                        ->where('type', 'deposit')
                        ->where('management_id', $profit->id)
                        ->sum('amount');
                });
            });
        
            $remainingProfit = $profit->total_profit - $totalProfitShare;
            Log::info('Sisa keuntungan yang akan didistribusikan: '.$remainingProfit);
            
            // Update status berdasarkan ada/tidaknya bukti transfer
            $status = $proofPath ? 'paid' : 'processed';
            $profit->update([
                'proof_file' => $proofPath,
                'remaining_profit' => $remainingProfit,
                'status' => $status,
                'category' => $categoryFilter
            ]);

            Log::info('Memulai distribusi bagi hasil ke masing-masing akun');
            
            // Distribusikan bagi hasil
            foreach ($investors as $investor) {
                foreach ($investor->accounts as $account) {
                    $accountInvestment = $account->investments->sum('amount');
                    $profitAmount = ($profit->total_profit * ($accountInvestment / $totalInvestment)) 
                                * ($account->profit_percentage / 100);

                    Log::debug('Distribusi ke akun ID '.$account->id.': '.$profitAmount);
                    
                    $account->investments()->create([
                        'date' => $profit->distribution_date,
                        'type' => 'deposit',
                        'amount' => round($profitAmount, 2),
                        'description' => 'Bagi hasil periode ' . $profit->period,
                        'management_id' => $profit->id
                    ]);
                }
            }

            DB::commit();
            Log::info('Proses konfirmasi pembayaran berhasil diselesaikan');
            
            return redirect()->route('irp.profit-management.index')
                ->with('success', 'Pembayaran berhasil dikonfirmasi dan bagi hasil telah didistribusikan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengkonfirmasi pembayaran: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->with('error', 'Gagal mengkonfirmasi pembayaran: ' . $e->getMessage());
        }
    }

    public function updateDistribution(Request $request, ProfitManagement $profit)
    {
        if ($profit->status != 'draft') {
            return back()->with('error', 'Hanya pembagian dengan status draft yang dapat diupdate');
        }

        $validated = $request->validate([
            'new_total_profit' => 'required|numeric|min:0',
            'use_custom_percentage' => 'nullable|boolean',
            'custom_percentage' => 'nullable|numeric|min:0|max:100'
        ]);

        DB::beginTransaction();
        try {
            // Update total profit dan persentase custom
            $profit->update([
                'total_profit' => $validated['new_total_profit'],
                'use_custom_percentage' => $validated['use_custom_percentage'] ?? false,
                'custom_percentage' => $validated['custom_percentage']
            ]);

            DB::commit();
            return back()->with('success', 'Pembagian keuntungan berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate pembagian: ' . $e->getMessage());
        }
    }

    public function storeForInvestor(Request $request, Investor $investor)
    {
        $validated = $request->validate([
            'period' => 'required|string|max:20',
            'total_profit' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'accounts' => 'required|array',
            'accounts.*' => 'exists:investor_accounts,id'
        ]);

        DB::beginTransaction();
        try {
            // Buat record profit management
            $profit = ProfitManagement::create([
                'period' => $validated['period'],
                'total_profit' => $validated['total_profit'],
                'distribution_date' => $validated['payment_date'],
                'status' => 'processed'
            ]);

            // Hitung total investasi dari akun yang dipilih
            $totalInvestment = $investor->accounts()
                ->whereIn('id', $validated['accounts'])
                ->withSum('investments', 'amount')
                ->get()
                ->sum('investments_sum_amount');

            // Distribusikan ke masing-masing akun
            foreach ($validated['accounts'] as $accountId) {
                $account = InvestorAccount::find($accountId);
                $accountInvestment = $account->investments()->sum('amount');
                
                if ($accountInvestment > 0) {
                    $profitAmount = ($validated['total_profit'] * ($accountInvestment / $totalInvestment)) 
                                * ($account->profit_percentage / 100);

                    $account->investments()->create([
                        'date' => $validated['payment_date'],
                        'type' => 'deposit',
                        'amount' => $profitAmount,
                        'description' => 'Bagi hasil periode ' . $validated['period'],
                        'management_id' => $profit->id
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('irp.investor.show', $investor->id)
                ->with('success', 'Pembagian keuntungan berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan pembagian keuntungan: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan pembagian keuntungan: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function updateCategory(Request $request, ProfitManagement $profit)
    {
        if ($profit->status != 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pembagian dengan status draft yang dapat diupdate'
            ], 403);
        }

        $validated = $request->validate([
            'category' => 'nullable|string|in:syirkah,investama,sukuk,internal,eksternal,aktif,pasif'
        ]);

        try {
            $profit->update([
                'category' => $validated['category'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate kategori: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kategori'
            ], 500);
        }
    }

    public function storeGroup(Request $request)
    {
        $cleanedRequest = $request->all();
    
        if ($request->has('total_quota')) {
            $cleanedRequest['total_quota'] = str_replace('.', '', $request->total_quota);
        }
        
        if ($request->has('amount')) {
            $cleanedRequest['amount'] = array_map(function($amount) {
                return str_replace('.', '', $amount);
            }, $request->amount);
        }
        
        $validator = Validator::make($cleanedRequest, [
            'name' => 'required|string|max:255|unique:profit_groups,name',
            'description' => 'nullable|string',
            'product_id' => 'nullable|exists:produk,id_produk',
            'total_quota' => 'nullable|numeric|min:0',
            'investor_id.*' => 'required|exists:investors,id',
            'account_id.*' => 'required|exists:investor_accounts,id',
            'amount.*' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $group = ProfitGroup::create([
                'name' => $request->name,
                'description' => $request->description,
                'product_id' => $request->product_id,
                'total_quota' => $cleanedRequest['total_quota'] ?: null
            ]);
    
            foreach ($request->investor_id as $index => $investorId) {
                $group->investors()->create([
                    'investor_id' => $investorId,
                    'account_id' => $request->account_id[$index],
                    'investment_amount' => $cleanedRequest['amount'][$index]
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'redirect' => route('irp.profit-management.index', ['tab' => 'group'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $group = ProfitGroup::with(['investors.investor', 'investors.account.investments', 'product'])->findOrFail($id);
        $products = Produk::select('id_produk', 'nama_produk', 'harga_jual')->get();
        $investors = Investor::where('status', 'active')->get();

        // Hitung total investasi
        $totalInvestment = $group->investors->sum(function($investor) {
            return $investor->account ? $investor->account->total_investment : 0;
        });

        $group->investors->each(function ($investor) {
            $investor->real_investment = $investor->account ? $investor->account->total_investment : 0;
        });

        return response()->json([
            'group' => $group,
            'products' => $products,
            'investors' => $investors,
            'total_investment' => $totalInvestment
        ]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $group = ProfitGroup::findOrFail($id);
            $group->investors()->delete();
            $group->delete();
            
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateGroup(Request $request, $id)
    {
        $cleanedRequest = $request->all();

        if ($request->has('total_quota')) {
            $cleanedRequest['total_quota'] = str_replace('.', '', $request->total_quota);
        }
        
        if ($request->has('amount')) {
            $cleanedRequest['amount'] = array_map(function($amount) {
                return str_replace('.', '', $amount);
            }, $request->amount);
        }
        
        $validator = Validator::make($cleanedRequest, [
            'name' => 'required|string|max:255|unique:profit_groups,name,'.$id,
            'description' => 'nullable|string',
            'product_id' => 'nullable|exists:produk,id_produk',
            'total_quota' => 'nullable|numeric|min:0',
            'investor_id.*' => 'required|exists:investors,id',
            'account_id.*' => 'required|exists:investor_accounts,id',
            'amount.*' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $group = ProfitGroup::findOrFail($id);
            $group->update([
                'name' => $request->name,
                'description' => $request->description,
                'product_id' => $request->product_id,
                'total_quota' => $cleanedRequest['total_quota'] ?: null
            ]);

            // Hapus semua investor lama
            $group->investors()->delete();

            // Tambahkan investor baru
            foreach ($request->investor_id as $index => $investorId) {
                $group->investors()->create([
                    'investor_id' => $investorId,
                    'account_id' => $request->account_id[$index],
                    'investment_amount' => $cleanedRequest['amount'][$index]
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kelompok berhasil diperbarui',
                'redirect' => route('irp.profit-management.index', ['tab' => 'group'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function showGroup($id)
    {
        $group = ProfitGroup::with(['investors.investor', 'investors.account.investments'])
                ->findOrFail($id);
        
        // Ambil history terbaru atau buat draft baru
        $history = ProfitGroupHistory::firstOrCreate(
            ['group_id' => $id, 'status' => 'draft'],
            [
                'total_profit' => 0,
                'distribution_date' => now(),
                'period' => 'Periode ' . now()->format('F Y')
            ]
        );
        
        $totalInvestment = $group->investors->sum(function($investor) {
            return $investor->account ? $investor->account->total_investment : 0;
        });

        $uniqueInvestorsCount = $group->investors->unique('investor_id')->count();
        $validAccountsCount = $group->investors->filter(function($investor) {
            return $investor->account !== null;
        })->count();
        
        return view('irp.profit_management.show_group', [
            'group' => $group,
            'history' => $history,
            'total_investment' => $totalInvestment,
            'uniqueInvestorsCount' => $uniqueInvestorsCount,
            'validAccountsCount' => $validAccountsCount,
        ]);
    }

    public function updateDistributionGroup(Request $request, $id)
    {
        $validated = $request->validate([
            'new_total_profit' => 'required|numeric|min:0',
            'use_custom_percentage' => 'nullable|boolean',
            'custom_percentage' => 'nullable|numeric|min:0|max:100',
            'period' => 'required|string|max:100',
            'distribution_date' => 'required|date'
        ]);

        $history = ProfitGroupHistory::where('group_id', $id)
                ->where('status', 'draft')
                ->firstOrFail();

        DB::beginTransaction();
        try {
            $history->update([
                'total_profit' => $validated['new_total_profit'],
                'use_custom_percentage' => $validated['use_custom_percentage'] ?? false,
                'custom_percentage' => $validated['custom_percentage'],
                'period' => $validated['period'],
                'distribution_date' => $validated['distribution_date']
            ]);

            DB::commit();
            return back()->with('success', 'Pembagian keuntungan berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate pembagian: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function confirmPaymentGroup(Request $request, $id)
    {
        $history = ProfitGroupHistory::where('group_id', $id)
                ->where('status', 'draft')
                ->firstOrFail();

        $request->validate([
            'proof_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        DB::beginTransaction();
        try {
            // Upload bukti transfer
            $proofPath = $request->hasFile('proof_file') 
                ? $request->file('proof_file')->store('profit-proofs', 'public')
                : null;

            $group = $history->group()->with('investors.account.investments')->first();
            
            // Hitung total investment dari semua account investor
            $totalInvestment = $group->investors->sum(function($investor) {
                return $investor->account ? $investor->account->total_investment : 0;
            });
            
            $totalProfitShare = 0;

            // Simpan detail distribusi
            $distributions = [];
            foreach ($group->investors as $investor) {
                $account = $investor->account;
                
                // Gunakan total_investment dari account jika ada, jika tidak gunakan investment_amount dari investor
                $investmentAmount = $account ? $account->total_investment : $investor->investment_amount;
                
                $effectivePercentage = $history->use_custom_percentage 
                    ? $history->custom_percentage 
                    : ($account ? $account->profit_percentage : 0);
                    
                $profitShare = ($history->total_profit * ($investmentAmount / $totalInvestment)) 
                            * ($effectivePercentage / 100);
                
                $distributions[] = [
                    'history_id' => $history->id,
                    'investor_id' => $investor->investor_id,
                    'account_id' => $investor->account_id,
                    'investment_amount' => $investmentAmount,
                    'profit_share' => round($profitShare, 2),
                    'profit_percentage' => $effectivePercentage,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $totalProfitShare += $profitShare;

                // Catat transaksi
                if ($account) {
                    $account->investments()->create([
                        'date' => $history->distribution_date,
                        'type' => 'deposit',
                        'amount' => round($profitShare, 2),
                        'description' => $history->period,
                        'reference_id' => $history->id,
                        'reference_type' => 'group_profit'
                    ]);

                    // Update balance account setelah distribusi profit
                    $account->updateBalance();
                }
            }

            // Insert bulk untuk efisiensi
            ProfitGroupDistribution::insert($distributions);

            // Update history
            $status = $proofPath ? 'paid' : 'processed';
            $history->update([
                'proof_file' => $proofPath,
                'remaining_profit' => $history->total_profit - $totalProfitShare,
                'status' => $status
            ]);

            // Buat draft baru untuk periode berikutnya
            ProfitGroupHistory::create([
                'group_id' => $group->id,
                'total_profit' => 0,
                'distribution_date' => now()->addMonth(),
                'period' => 'Periode '.now()->addMonth()->format('F Y'),
                'status' => 'draft'
            ]);

            DB::commit();
            return redirect()->route('irp.profit-management.index', ['tab' => 'group'])
                ->with('success', 'Pembagian berhasil dikonfirmasi');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Confirm payment failed: '.$e->getMessage());
            return back()->with('error', 'Gagal mengkonfirmasi: '.$e->getMessage());
        }
    }

    public function showGroupHistory($id)
    {
        $history = ProfitGroupHistory::with([
                    'group',
                    'group.investors.investor',
                    'group.investors.account',
                    'distributions.investor',
                    'distributions.account'
                ])->findOrFail($id);
        
        $total_investment = $history->group->investors->sum('investment_amount');
        $uniqueInvestorsCount = $history->group->investors->unique('investor_id')->count();
        $validAccountsCount = $history->group->investors->filter(function($investor) {
            return $investor->account !== null;
        })->count();
        
        return view('irp.profit_management.show_group', [
            'history' => $history,
            'group' => $history->group,
            'total_investment' => $total_investment,
            'uniqueInvestorsCount' => $uniqueInvestorsCount,
            'validAccountsCount' => $validAccountsCount,
        ]);
    }

    public function cancelPaymentGroup($id)
    {
        $history = ProfitGroupHistory::where('status', 'processed')
                    ->findOrFail($id);

        DB::beginTransaction();
        try {
            // Cari dan hapus transaksi terkait di AccountInvestment
            AccountInvestment::where('type', 'deposit')
                ->where('date', $history->distribution_date)
                ->where('description', $history->period)
                ->delete();
                
            // Hapus distribusi
            $history->distributions()->delete();
            
            // Update status history
            $history->update(['status' => 'draft']);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dibatalkan',
                'redirect' => route('irp.profit-management.index', ['tab' => 'history'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log error untuk debugging
            \Log::error('Gagal membatalkan pembayaran: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteHistory($id)
    {
        $history = ProfitGroupHistory::findOrFail($id);

        // Only allow deletion of draft or processed histories
        if ($history->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus history yang sudah dibayar'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Delete related distributions
            $history->distributions()->delete();
            
            // Delete the history
            $history->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'History berhasil dihapus',
                'redirect' => route('irp.profit-management.index', ['tab' => 'history'])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus history: ' . $e->getMessage()
            ], 500);
        }
    }
}