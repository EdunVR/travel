<?php

namespace App\Http\Controllers;

use App\Models\SettingCOA;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingCOAController extends Controller
{
    protected $coaService;

    public function __construct()
    {
        // Gunakan service yang sudah ada atau buat helper
        $this->accountsConfig = config('accounts.accounts', []);
    }

    public function index()
    {
        $settings = SettingCOA::first();

        // Ambil semua akun dari config
        $allAccounts = collect($this->getAllAccountsFromConfig());
        
        $accountTypesLabels = [
            'asset' => 'Aset',
            'liability' => 'Kewajiban',
            'equity' => 'Ekuitas',
            'revenue' => 'Pendapatan',
            'expense' => 'Beban'
        ];

        // Hitung jumlah akun per tipe
        $accountCounts = [];
        foreach ($accountTypesLabels as $type => $label) {
            $accountCounts[$type] = $allAccounts->where('type', $type)->count();
        }

        return view('settings.coa.index', compact('settings', 'allAccounts', 'accountTypesLabels', 'accountCounts'));
    }

    // Di SettingCOAController.php
    public function poPenjualan()
    {
        $settings = SettingCOA::firstOrNew([]);
        
        // Ambil semua akun dari config
        $allAccounts = collect($this->getAllAccountsFromConfig());
        
        // Ambil accounting books aktif
        $accountingBooks = \App\Models\AccountingBook::where('status', 'active')->get();
        
        $accountTypesLabels = [
            'asset' => 'Aset',
            'liability' => 'Kewajiban',
            'equity' => 'Ekuitas',
            'revenue' => 'Pendapatan',
            'expense' => 'Beban'
        ];

        // Hitung jumlah akun per tipe
        $accountCounts = [];
        foreach ($accountTypesLabels as $type => $label) {
            $accountCounts[$type] = $allAccounts->where('type', $type)->count();
        }

        // Process account names for current settings
        $accountNames = [];
        if ($settings->exists) {
            $accountFields = [
                'akun_piutang_po',
                'akun_pendapatan_po',
                'akun_hpp_po',
                'akun_persediaan_po',
                'akun_ongkir_po',
                'akun_uang_muka_po',
                'akun_pendapatan_diterima_dimuka',
                'akun_diskon_penjualan'
            ];

            foreach ($accountFields as $field) {
                if ($settings->$field) {
                    $accountNames[$field] = $this->getAccountNameByCode($settings->$field);
                }
            }
        }

        return view('settings.coa.po_penjualan', compact(
            'settings', 
            'allAccounts', 
            'accountTypesLabels', 
            'accountCounts',
            'accountingBooks',
            'accountNames'
        ));
    }

    /**
     * Helper untuk mendapatkan nama akun dari kode
     */
    private function getAccountNameByCode($code)
    {
        $account = $this->findAccountByCode($code);
        return $account ? $account['name'] : 'Tidak ditemukan';
    }

    public function updatePoPenjualan(Request $request)
    {
        $request->validate([
            'akun_piutang_po' => 'required',
            'akun_pendapatan_po' => 'required',
            'akun_hpp_po' => 'required',
            'akun_persediaan_po' => 'required',
            'akun_ongkir_po' => 'required',
            'akun_uang_muka_po' => 'required',
            'akun_pendapatan_diterima_dimuka' => 'required',
            'akun_diskon_penjualan' => 'required',
            'accounting_book_id' => 'required|exists:accounting_books,id'
        ]);

        DB::beginTransaction();
        try {
            $settings = SettingCOA::first();
            
            // Jika belum ada setting, buat baru
            if (!$settings) {
                $settings = new SettingCOA();
            }

            $validAccounts = collect($this->getAllAccountsFromConfig())->pluck('code')->toArray();
            
            // Validasi semua akun
            $accountsToValidate = [
                'akun_piutang_po' => 'asset',
                'akun_pendapatan_po' => 'revenue',
                'akun_hpp_po' => 'expense',
                'akun_persediaan_po' => 'asset',
                'akun_ongkir_po' => 'revenue',
                'akun_uang_muka_po' => 'liability',
                'akun_pendapatan_diterima_dimuka' => 'liability',
                'akun_diskon_penjualan' => 'expense'
            ];

            foreach ($accountsToValidate as $field => $expectedType) {
                if (!in_array($request->$field, $validAccounts)) {
                    throw new \Exception("Kode akun {$field} tidak valid");
                }
                
                $account = $this->findAccountByCode($request->$field);
                if (!$account) {
                    throw new \Exception("Akun {$field} dengan kode {$request->$field} tidak ditemukan");
                }
                
                if ($account['type'] !== $expectedType) {
                    $typeLabels = [
                        'asset' => 'Aset',
                        'revenue' => 'Pendapatan', 
                        'expense' => 'Beban',
                        'liability' => 'Kewajiban'
                    ];
                    throw new \Exception("Akun {$field} harus bertipe " . ($typeLabels[$expectedType] ?? $expectedType));
                }
            }
            
            // Simpan setting - gunakan fill untuk mass assignment
            $settings->fill([
                'akun_piutang_po' => $request->akun_piutang_po,
                'akun_pendapatan_po' => $request->akun_pendapatan_po,
                'akun_hpp_po' => $request->akun_hpp_po,
                'akun_persediaan_po' => $request->akun_persediaan_po,
                'akun_ongkir_po' => $request->akun_ongkir_po,
                'akun_uang_muka_po' => $request->akun_uang_muka_po,
                'akun_pendapatan_diterima_dimuka' => $request->akun_pendapatan_diterima_dimuka,
                'akun_diskon_penjualan' => $request->akun_diskon_penjualan,
                'accounting_book_id' => $request->accounting_book_id
            ]);
            
            // Set created_by jika baru
            if (!$settings->exists) {
                $settings->created_by = auth()->id();
            }
            
            $settings->updated_by = auth()->id();
            $settings->save();

            DB::commit();

            return redirect()->back()->with('success', 'Setting akun PO Penjualan berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating PO Penjualan setting: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui setting: ' . $e->getMessage());
        }
    }

    /**
     * Get all accounts from config in flat array
     */
    private function getAllAccountsFromConfig()
    {
        $accounts = [];
        $this->flattenAccounts($this->accountsConfig, $accounts);
        return $accounts;
    }

    private function flattenAccounts($accountList, &$result, $level = 0)
    {
        foreach ($accountList as $account) {
            // Only include accounts that don't have children (leaf nodes)
            if (!isset($account['children']) || empty($account['children'])) {
                $result[] = [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'type' => $account['type'],
                    'is_active' => $account['is_active'] ?? true,
                    'level' => $level
                ];
            }
            
            // If has children, recursively process them
            if (isset($account['children']) && !empty($account['children'])) {
                $this->flattenAccounts($account['children'], $result, $level + 1);
            }
        }
    }

    private function getAccountsByType($type)
    {
        $allAccounts = collect($this->getAllAccountsFromConfig());
        return $allAccounts->where('type', $type)->where('is_active', true)->values();
    }

    private function findAccountByCode($code)
    {
        $allAccounts = collect($this->getAllAccountsFromConfig());
        return $allAccounts->firstWhere('code', $code);
    }

    public function pembelian()
    {
        $settings = SettingCOA::firstOrNew([]);
        $allAccounts = collect($this->getAllAccountsFromConfig());
        
        $accountTypesLabels = [
            'asset' => 'Aset',
            'liability' => 'Kewajiban',
            'equity' => 'Ekuitas',
            'revenue' => 'Pendapatan',
            'expense' => 'Beban'
        ];
        
        return view('settings.coa.pembelian', compact('settings', 'allAccounts', 'accountTypesLabels'));
    }

    public function updatePembelian(Request $request)
    {
        return redirect()->back()->with('info', 'Fitur setting akun pembelian akan segera tersedia');
    }

    public function produksi()
    {
        $settings = SettingCOA::firstOrNew([]);
        $allAccounts = collect($this->getAllAccountsFromConfig());
        
        $accountTypesLabels = [
            'asset' => 'Aset',
            'liability' => 'Kewajiban',
            'equity' => 'Ekuitas',
            'revenue' => 'Pendapatan',
            'expense' => 'Beban'
        ];
        
        return view('settings.coa.produksi', compact('settings', 'allAccounts', 'accountTypesLabels'));
    }

    public function updateProduksi(Request $request)
    {
        return redirect()->back()->with('info', 'Fitur setting akun produksi akan segera tersedia');
    }

    public function retur()
    {
        $settings = SettingCOA::firstOrNew([]);
        $allAccounts = collect($this->getAllAccountsFromConfig());
        
        $accountTypesLabels = [
            'asset' => 'Aset',
            'liability' => 'Kewajiban',
            'equity' => 'Ekuitas',
            'revenue' => 'Pendapatan',
            'expense' => 'Beban'
        ];
        
        return view('settings.coa.retur', compact('settings', 'allAccounts', 'accountTypesLabels'));
    }

    public function updateRetur(Request $request)
    {
        return redirect()->back()->with('info', 'Fitur setting akun retur akan segera tersedia');
    }

    public function getAccountOptions($type)
    {
        $accounts = $this->getAccountsByType($type);
        
        $options = [];
        foreach ($accounts as $account) {
            $options[] = [
                'id' => $account['code'],
                'text' => $account['code'] . ' - ' . $account['name'],
                'code' => $account['code'],
                'name' => $account['name'],
                'type' => $account['type']
            ];
        }

        return response()->json($options);
    }

    public function validateAccounts(Request $request)
    {
        $request->validate([
            'akun_piutang_po' => 'required',
            'akun_pendapatan_po' => 'required',
            'akun_hpp_po' => 'required'
        ]);

        // Validasi tipe akun berdasarkan config
        $errors = [];

        $piutangAccount = $this->findAccountByCode($request->akun_piutang_po);
        if (!$piutangAccount || $piutangAccount['type'] !== 'asset') {
            $errors[] = 'Akun piutang harus bertipe Aset';
        }

        $pendapatanAccount = $this->findAccountByCode($request->akun_pendapatan_po);
        if (!$pendapatanAccount || $pendapatanAccount['type'] !== 'revenue') {
            $errors[] = 'Akun pendapatan harus bertipe Pendapatan';
        }

        $hppAccount = $this->findAccountByCode($request->akun_hpp_po);
        if (!$hppAccount || $hppAccount['type'] !== 'expense') {
            $errors[] = 'Akun HPP harus bertipe Beban';
        }

        if (count($errors) > 0) {
            return response()->json([
                'success' => false,
                'errors' => $errors
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Semua akun valid'
        ]);
    }
}