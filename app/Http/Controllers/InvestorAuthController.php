<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\Investor;
use App\Models\InvestorAccount;
use App\Models\InvestorDocument;
use App\Models\AccountInvestment;
use App\Models\InvestorWithdrawal;
use Log;
use PDF;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class InvestorAuthController extends Controller
{
    // ==================== AUTHENTICATION ====================
    
    /**
     * Menampilkan form login
     */
    public function showLoginForm()
    {
        return view('investor.auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);

        $investor = Investor::where('email', $request->email)
                          ->where('phone', $request->phone)
                          ->first();

        Log::info($investor);

        if ($investor) {
            Auth::guard('investor')->login($investor, $request->filled('remember-me'));
            return redirect()->route('investor.dashboard');
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Menampilkan form registrasi
     */
    public function showRegistrationForm()
    {
        return view('investor.auth.register');
    }

    /**
     * Proses registrasi
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:investors',
            'phone' => 'required|string|unique:investors',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $investor = Investor::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'pending',
            'join_date' => now(),
        ]);

        Auth::guard('investor')->login($investor);

        return redirect()->route('investor.dashboard');
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::guard('investor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/investor/login');
    }

    // ==================== PASSWORD RESET ====================

    public function showForgotPasswordForm()
    {
        return view('investor.auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('investors')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm($token)
    {
        return view('investor.auth.passwords.reset', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker('investors')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('investor.login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }

    // ==================== DASHBOARD & INVESTMENT ====================

    public function dashboard()
    {
        $investor = Auth::guard('investor')->user()->load(['accounts', 'customers']);
        
        // Calculate average monthly profit (last 12 months)
        $monthlyProfits = [];
        $monthlyWithdrawals = [];
        $monthlyLabels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthYear = $date->format('M Y');
            $monthlyLabels[] = $monthYear;
            
            // Calculate monthly profit
            $monthlyProfit = $investor->accounts->sum(function($account) use ($date) {
                return $account->investments()
                            ->where('type', 'deposit')
                            ->whereYear('created_at', $date->year)
                            ->whereMonth('created_at', $date->month)
                            ->sum('amount');
            });
            $monthlyProfits[] = $monthlyProfit;
            
            // Calculate monthly withdrawals
            $monthlyWithdrawal = $investor->accounts->sum(function($account) use ($date) {
                return $account->withdrawals()
                            ->where('status', 'approved')
                            ->whereYear('approved_at', $date->year)
                            ->whereMonth('approved_at', $date->month)
                            ->sum('amount');
            });
            $monthlyWithdrawals[] = $monthlyWithdrawal;
        }
        
        // Current month profit
        $currentMonthProfit = end($monthlyProfits);
        
        // Average monthly profit (last 12 months)
        $averageMonthlyProfit = array_sum($monthlyProfits) / 12;
        
        // Profit change percentage (current vs previous month)
        $previousMonthProfit = $monthlyProfits[count($monthlyProfits) - 2] ?? 0;
        $profitChangePercentage = $previousMonthProfit > 0 
            ? (($currentMonthProfit - $previousMonthProfit) / $previousMonthProfit) * 100 
            : 0;

        
        $totalProfit_semua = $investor->accounts->sum(function($account) {
            return $account->investments()
                        ->where('type', 'deposit')
                        ->sum('amount');
        });
        // Withdrawal status
         $totalWithdrawals = $investor->accounts->sum(function($account) {
            return $account->investments()
                        ->where('type', 'withdrawal')
                        ->sum('amount');
         });

         $totalSaldoTertahan = $investor->accounts->sum('saldo_tertahan');

         $totalSaldo_semua = $totalProfit_semua - $totalWithdrawals - $totalSaldoTertahan;
        // $totalWithdrawals = $investor->accounts->sum(function($account) {
        //     return $account->withdrawals()
        //                 ->where('status', 'approved')
        //                 ->sum('amount');
        // });
        $totalInvestmentRAW = $investor->accounts->sum(function($account) {
            return $account->investments()
                        ->where('type', 'investment')
                        ->sum('amount');
        });
        $totalWithdrawn = $investor->accounts->sum(function($account) {
            return $account->investments()
                        ->where('type', 'penarikan')
                        ->sum('amount');
        });
        $activeInvestment = $totalInvestmentRAW - $totalWithdrawn;
        
        $currentMonthWithdrawal = end($monthlyWithdrawals);
        $withdrawalStatus = $currentMonthWithdrawal > 0 ? 'active' : 'inactive';
        
        $totalProfit = array_sum($monthlyProfits);
        $totalInvestment = $investor->total_investment;
        $averagePercentage = $totalInvestment > 0 ? ($totalProfit / $totalInvestment) * 100 : 0;
        
        $investmentGrowthData = [];
        $investmentGrowthLabels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthYear = $date->format('M Y');
            $investmentGrowthLabels[] = $monthYear;
            
            $monthlyInvestment = $investor->accounts->sum(function($account) use ($date) {
                return $account->investments()
                            ->where('type', 'investment')
                            ->whereDate('created_at', '<=', $date->endOfMonth())
                            ->sum('amount');
            });
            $investmentGrowthData[] = $monthlyInvestment;
        }
        
        // Recent activities (combined investments and withdrawals)
        $recentActivities = collect();

        $investments = AccountInvestment::whereHas('account', function($q) use ($investor) {
            $q->where('investor_id', $investor->id);
        })->with('account')->orderBy('date', 'desc')->take(5)->get();

        $withdrawals = InvestorWithdrawal::where('investor_id', $investor->id)
            ->with('account')
            ->latest('requested_at')
            ->take(5)
            ->get();
        
        // Combine and sort
        $recentActivities = $investments->merge($withdrawals)
            ->sortByDesc(function($item) {
                // Gunakan date untuk investment, requested_at untuk withdrawal
                return $item instanceof \App\Models\AccountInvestment ? $item->date : $item->requested_at;
            })
            ->take(5);

        view()->share('totalSaldo_semua', $totalSaldo_semua);
        view()->share('totalSaldoTertahan', $totalSaldoTertahan);
        
        return view('investor.dashboard', [
            'investor' => $investor,
            'recentActivities' => $recentActivities,
            'averageMonthlyProfit' => $averageMonthlyProfit,
            'profitChangePercentage' => $profitChangePercentage,
            'currentMonthProfit' => $currentMonthProfit,
            'monthlyProfits' => $monthlyProfits,
            'monthlyWithdrawals' => $monthlyWithdrawals,
            'monthlyLabels' => $monthlyLabels,
            'totalWithdrawals' => $totalWithdrawals,
            'currentMonthWithdrawal' => $currentMonthWithdrawal,
            'withdrawalStatus' => $withdrawalStatus,
            'averagePercentage' => $averagePercentage,
            'investmentGrowthData' => $investmentGrowthData,
            'investmentGrowthLabels' => $investmentGrowthLabels,
            'totalInvestment' => $totalInvestment,
            'totalInvestmentRAW' => $totalInvestmentRAW,
            'activeInvestment' => $activeInvestment,
            'totalWithdrawn' => $totalWithdrawn,
        ]);
    }

    public function activities()
    {
        $investor = Auth::guard('investor')->user();
        
        // Get all activities (investments and withdrawals)
        $activities = collect();
        
        $investments = AccountInvestment::whereHas('account', function($q) use ($investor) {
            $q->where('investor_id', $investor->id);
        })->with('account')->orderBy('date', 'desc')->get();
        
        // Get withdrawals - Urutkan berdasarkan requested_at
        $withdrawals = InvestorWithdrawal::where('investor_id', $investor->id)
            ->with('account')
            ->latest('requested_at')
            ->get();
        
        // Combine and sort
        $allActivities = $investments->merge($withdrawals)
        ->sortByDesc(function($item) {
            // Gunakan date untuk investment, requested_at untuk withdrawal
            return $item instanceof \App\Models\AccountInvestment ? $item->date : $item->requested_at;
        });
        
        // Paginate
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $paginatedItems = $allActivities->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $activities = new LengthAwarePaginator(
            $paginatedItems,
            $allActivities->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        return view('investor.activities.index', [
            'activities' => $activities,
            'investor' => $investor
        ]);
    }

    public function investments()
    {
        $investor = Auth::guard('investor')->user();
        $accounts = $investor->accounts()->latest()->get();
        
        return view('investor.accounts.index', [
            'investor' => $investor,
            'accounts' => $accounts
        ]);
    }

    public function investmentDetail($id)
    {
        $account = InvestorAccount::where('investor_id', Auth::guard('investor')->id())
                                ->with(['investments', 'withdrawals'])
                                ->findOrFail($id);

        // Get filter parameters
        $type = request()->get('type', 'all');
        $period = request()->get('period', 'all');
        
        // Calculate date ranges
        $dateRange = $this->getDateRange($period);
        $startDate = $dateRange['start'] ?? null;
        $endDate = $dateRange['end'] ?? null;

        // Filter transactions
        $allTransactions = collect();
        
        // Handle investments based on type
        if ($type === 'all' || $type === 'investment' || $type === 'deposit' || $type === 'penarikan' || $type === 'withdrawal') {
            $investments = $account->investments();
            
            if ($type !== 'all') {
                $investments->where('type', $type);
            }
            
            if ($startDate && $endDate) {
                $investments->whereBetween('created_at', [$startDate, $endDate]);
            }
            
            $allTransactions = $allTransactions->merge($investments->get());
        }

        // Handle withdrawals
        if ($type === 'all' || $type === 'withdrawal_request') {
            $withdrawals = $account->withdrawals();
            if ($startDate && $endDate) {
                $withdrawals->whereBetween('requested_at', [$startDate, $endDate]);
            }
            $allTransactions = $allTransactions->merge($withdrawals->get());
        }

        // Sort transactions ascending for correct balance calculation
        $sortedForCalculation = $allTransactions->sortBy(function($item) {
            return $item->created_at ?? $item->requested_at;
        });

        // Calculate running balance (now correct order)
        $runningBalance = 0;
        $totalInvestasi = 0;
        $transactionsWithBalance = $sortedForCalculation->map(function($transaction) use (&$runningBalance, &$totalInvestasi) {
            if ($transaction instanceof \App\Models\AccountInvestment) {
                if ($transaction->type === 'investment') {
                    $totalInvestasi += $transaction->amount;
                } elseif ($transaction->type === 'penarikan') {
                    $totalInvestasi -= $transaction->amount;
                } elseif ($transaction->type === 'deposit') {
                    $runningBalance += $transaction->amount;
                } elseif ($transaction->type === 'withdrawal') {
                    $runningBalance -= $transaction->amount;
                }
            } elseif ($transaction instanceof \App\Models\InvestorWithdrawal && $transaction->status === 'approved') {
                $runningBalance -= $transaction->amount;
            }
            
            $transaction->total_investasi = $totalInvestasi;
            $transaction->calculated_balance = $runningBalance;
            return $transaction;
        });

        // Sort descending for display
        $sortedTransactions = $transactionsWithBalance->sortByDesc(function($item) {
            return $item->created_at ?? $item->requested_at;
        });

        return view('investor.accounts.show', [
            'account' => $account,
            'transactions' => new LengthAwarePaginator(
                $sortedTransactions->forPage(request()->get('page', 1), 10),
                $sortedTransactions->count(),
                10,
                request()->get('page', 1),
                ['path' => request()->url()]
            ),
            'current_balance' => $runningBalance,
            'total_investment' => $totalInvestasi,
            'filterType' => $type,
            'filterPeriod' => $period,
            'investor' => Auth::guard('investor')->user(),
        ]);
    }



    private function getDateRange($period)
    {
        switch ($period) {
            case 'today':
                return [
                    'start' => now()->startOfDay(),
                    'end' => now()->endOfDay()
                ];
            case 'week':
                return [
                    'start' => now()->startOfWeek(),
                    'end' => now()->endOfWeek()
                ];
            case 'month':
                return [
                    'start' => now()->startOfMonth(),
                    'end' => now()->endOfMonth()
                ];
            case 'year':
                return [
                    'start' => now()->startOfYear(),
                    'end' => now()->endOfYear()
                ];
            default:
                return [
                    'start' => null,
                    'end' => null
                ];
        }
    }

    public function showAddInvestmentForm()
    {
        return view('investor.accounts.create');
    }

    public function addInvestment(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'initial_balance' => 'required|numeric|min:0',
            'profit_percentage' => 'required|numeric|min:0|max:100',
        ]);

        InvestorAccount::create([
            'investor_id' => Auth::guard('investor')->id(),
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'initial_balance' => $request->initial_balance,
            'current_balance' => $request->initial_balance,
            'profit_percentage' => $request->profit_percentage,
            'status' => 'active',
        ]);

        return redirect()->route('investor.investments')->with('success', 'Akun investasi berhasil ditambahkan');
    }

    // ==================== PROFIT SHARING ====================

    public function profits()
    {
        $investor = Auth::guard('investor')->user();

        $totalProfit_semua = $investor->accounts->sum(function($account) {
            return $account->investments()
                        ->where('type', 'deposit')
                        ->sum('amount');
        });
        // Withdrawal status
         $totalWithdrawals = $investor->accounts->sum(function($account) {
            return $account->investments()
                        ->where('type', 'withdrawal')
                        ->sum('amount');
         });

         $totalSaldoTertahan = $investor->accounts->sum('saldo_tertahan');

         $totalProfit = $totalProfit_semua - $totalWithdrawals - $totalSaldoTertahan;

        // Bagi hasil tahun ini
        $yearlyProfit = $investor->accounts->sum(function($account) {
            return $account->investments()
                        ->where('type', 'deposit')
                        ->whereYear('date', date('Y'))
                        ->sum('amount');
        });

        // Bagi hasil bulan ini
        $monthlyProfit = $investor->accounts->sum(function($account) {
            return $account->investments()
                        ->where('type', 'deposit')
                        ->whereYear('date', date('Y'))
                        ->whereMonth('date', date('m'))
                        ->sum('amount');
        });

        // Daftar distribusi bagi hasil
        $profitDistributions = AccountInvestment::whereHas('account', function($query) use ($investor) {
                $query->where('investor_id', $investor->id);
            })
            ->where('type', 'deposit')
            ->with('account')
            ->latest()
            ->paginate(10);

        return view('investor.profits.index', [
            'totalProfit' => $totalProfit,
            'yearlyProfit' => $yearlyProfit,
            'monthlyProfit' => $monthlyProfit,
            'profitDistributions' => $profitDistributions,
            'investor' => $investor
        ]);
    }

    public function profitShareDetail($id)
    {
        $distribution = AccountInvestment::whereHas('account', function($q) {
            $q->where('investor_id', Auth::guard('investor')->id());
        })->findOrFail($id);

        return view('investor.profits.show', compact('distribution'));
    }

    // ==================== WITHDRAWAL ====================

    public function storeWithdrawal(Request $request)
    {
        $investor = Auth::guard('investor')->user();
        
        $request->validate([
            'account_id' => 'required|in:all,'.$investor->accounts()->pluck('id')->implode(','),
            'amount' => 'required|numeric',
            'notes' => 'nullable|string|max:500',
        ]);

        // Jika memilih semua rekening
        if ($request->account_id === 'all') {
            $accounts = $investor->accounts()->active()->get();
            $totalAvailable = $accounts->sum('profit_balance');
            
            if ($request->amount > $totalAvailable) {
                return back()->withErrors(['amount' => 'Jumlah pencairan melebihi total saldo semua rekening'])->withInput();
            }
            
            // Buat pencairan untuk setiap rekening
            foreach ($accounts as $account) {
                if ($account->profit_balance > 0) {
                    InvestorWithdrawal::create([
                        'investor_id' => $investor->id,
                        'account_id' => $account->id,
                        'amount' => min($request->amount, $account->profit_balance),
                        'notes' => $request->notes,
                        'status' => 'pending',
                        'requested_at' => now(),
                    ]);
                    
                    $request->amount -= min($request->amount, $account->profit_balance);
                    if ($request->amount <= 0) break;
                }
            }
        } else {
            // Pencairan untuk rekening tertentu
            $account = $investor->accounts()->findOrFail($request->account_id);
            
            if ($request->amount > $account->profit_balance) {
                return back()->withErrors(['amount' => 'Jumlah pencairan melebihi saldo rekening yang dipilih'])->withInput();
            }
            
            InvestorWithdrawal::create([
                'investor_id' => $investor->id,
                'account_id' => $account->id,
                'amount' => $request->amount,
                'notes' => $request->notes,
                'status' => 'pending',
                'requested_at' => now(),
            ]);
        }

        return redirect()->route('investor.withdrawals')
                    ->with('success', 'Pengajuan pencairan berhasil dikirim');
    }

    public function withdrawals()
    {
        $investor = Auth::guard('investor')->user();
        
        $withdrawals = InvestorWithdrawal::where('investor_id', $investor->id)
            ->with('account')
            ->latest()
            ->paginate(10);

        $totalApprovedWithdrawals = InvestorWithdrawal::where('investor_id', $investor->id)
            ->where('status', 'approved')
            ->sum('amount');

        return view('investor.withdrawals.index', [
            'withdrawals' => $withdrawals,
            'totalApprovedWithdrawals' => $totalApprovedWithdrawals,
            'investor' => $investor
        ]);
    }

    public function showWithdrawalForm()
    {
        $investor = Auth::guard('investor')->user();
        $accounts = $investor->accounts()->active()->get();
        
        // Hitung total saldo semua rekening
        $totalBalance = $accounts->sum('profit_balance');
        
        return view('investor.withdrawals.create', [
            'accounts' => $accounts,
            'totalBalance' => $totalBalance
        ]);
    }

    public function submitWithdrawal(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:investor_accounts,id,investor_id,'.Auth::guard('investor')->id(),
            'amount' => 'required|numeric|min:100000',
            'notes' => 'nullable|string|max:500',
        ]);

        $account = InvestorAccount::find($request->account_id);

        if ($account->current_balance < $request->amount) {
            return back()->withErrors(['amount' => 'Saldo tidak mencukupi'])->withInput();
        }

        AccountInvestment::create([
            'account_id' => $request->account_id,
            'type' => 'withdrawal',
            'amount' => $request->amount,
            'description' => 'Penarikan dana',
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('investor.withdrawals')->with('success', 'Permintaan penarikan berhasil diajukan');
    }

    public function withdrawalDetail($id)
    {
        $withdrawal = AccountInvestment::whereHas('account', function($q) {
            $q->where('investor_id', Auth::guard('investor')->id());
        })->findOrFail($id);

        return view('investor.withdrawals.show', compact('withdrawal'));
    }

    // ==================== DOCUMENTS ====================

    public function documents()
    {
        $documents = Auth::guard('investor')->user()->documents;
        $investor = Auth::guard('investor')->user()->load(['accounts', 'customers']);
        return view('investor.documents.index', compact('documents', 'investor'));
    }

    public function downloadDocument($id)
    {
        $document = InvestorDocument::where('investor_id', Auth::guard('investor')->id())
                                  ->findOrFail($id);

        return response()->download(storage_path('app/'.$document->file_path));
    }


    public function viewDocument($id)
    {
        $document = InvestorDocument::where('investor_id', Auth::guard('investor')->id())
                                    ->findOrFail($id);

        $path = storage_path('app/' . $document->file_path);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($path);
    }



    // ==================== PROFILE ====================

    public function profile()
    {
        $investor = Auth::guard('investor')->user()->load(['accounts', 'customers']);
        return view('investor.profile.show', ['investor' => $investor, 'layoutInvestor' => $investor]);
    }
    
    public function showEditProfileForm()
    {
        $investor = Auth::guard('investor')->user();
        return view('investor.profile.edit', compact('investor'));
    }

    public function updateProfile(Request $request)
    {
        $investor = Auth::guard('investor')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:investors,email,'.$investor->id,
            'phone' => 'required|string|unique:investors,phone,'.$investor->id,
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'address']);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('public/investor/photos');
            $data['photo'] = str_replace('public/', '', $path);
        }

        $investor->update($data);

        return redirect()->route('investor.profile')->with('success', 'Profil berhasil diperbarui');
    }

    public function showChangePasswordForm()
    {
        return view('investor.profile.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $investor = Auth::guard('investor')->user();

        if (!Hash::check($request->current_password, $investor->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        $investor->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('investor.profile')->with('success', 'Password berhasil diubah');
    }

    public function downloadHistory($id)
    {
        $investor = Auth::guard('investor')->user();
        $account = InvestorAccount::where('investor_id', $investor->id)
                                ->findOrFail($id);
        
        // Get all transactions sorted ascending for correct balance calculation
        $allTransactions = collect()
            ->merge($account->investments)
            ->merge($account->withdrawals)
            ->sortBy(function($item) {
                return $item->created_at ?? $item->requested_at;
            });

        // Calculate running balance (ascending order)
        $runningBalance = 0;
        $totalInvestasi = 0;
        $transactionsWithBalance = $allTransactions->map(function($transaction) use (&$runningBalance, &$totalInvestasi) {
            if ($transaction instanceof \App\Models\AccountInvestment) {
                if ($transaction->type === 'investment') {
                    $totalInvestasi += $transaction->amount;
                } elseif ($transaction->type === 'penarikan') {
                    $totalInvestasi -= $transaction->amount;
                } elseif ($transaction->type === 'deposit') {
                    $runningBalance += $transaction->amount;
                } elseif ($transaction->type === 'withdrawal') {
                    $runningBalance -= $transaction->amount;
                }
            } elseif ($transaction instanceof \App\Models\InvestorWithdrawal && $transaction->status === 'approved') {
                $runningBalance -= $transaction->amount;
            }
            
            return [
                'date' => ($transaction->created_at ?? $transaction->requested_at)->format('d/m/Y'),
                'type' => $this->getTransactionType($transaction),
                'description' => $transaction->description ?? 'Pencairan Dana',
                'debit' => ($transaction instanceof \App\Models\AccountInvestment && in_array($transaction->type, ['investment', 'deposit'])) || 
                        ($transaction instanceof \App\Models\InvestorWithdrawal && $transaction->status !== 'approved') ? $transaction->amount : 0,
                'credit' => ($transaction instanceof \App\Models\AccountInvestment && in_array($transaction->type, ['penarikan', 'withdrawal'])) || 
                        ($transaction instanceof \App\Models\InvestorWithdrawal && $transaction->status === 'approved') ? $transaction->amount : 0,
                'balance' => $runningBalance,
                'notes' => $transaction->notes ?? null,
                'status' => $transaction->status ?? null
            ];
        });

        // Sort descending for display but keep correct balance
        $sortedTransactions = array_reverse($transactionsWithBalance->toArray());

        $pdf = PDF::loadView('investor.accounts.history-pdf', [
            'investor' => $investor,
            'account' => $account,
            'transactions' => $sortedTransactions,
            'startDate' => $allTransactions->first() ? ($allTransactions->first()->created_at ?? $allTransactions->first()->requested_at)->format('d/m/Y') : '',
            'endDate' => $allTransactions->last() ? ($allTransactions->last()->created_at ?? $allTransactions->last()->requested_at)->format('d/m/Y') : '',
            'totalInvestment' => $totalInvestasi,
            'closingBalance' => $runningBalance
        ])->setPaper('a4', 'landscape');

        return $pdf->download('rekening-koran-'.$account->account_number.'.pdf');
    }

    private function getTransactionType($transaction)
    {
        if ($transaction instanceof \App\Models\AccountInvestment) {
            return [
                'investment' => 'Setoran Modal',
                'penarikan' => 'Penarikan Modal',
                'deposit' => 'Bagi Hasil',
                'withdrawal' => 'Pencairan'
            ][$transaction->type] ?? $transaction->type;
        }
        return 'Pencairan Dana';
    }
}