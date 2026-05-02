<?php

namespace App\Http\Controllers;

use App\Models\PayrollCoaSetting;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\HasOutletFilter;

class PayrollCoaSettingController extends Controller
{
    use HasOutletFilter;

    public function index(Request $request)
    {
        $outlets = $this->getUserOutlets();

        return view('admin.sdm.payroll.coa-settings', compact('outlets'));
    }

    public function getAccounts(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $type = $request->get('type');
        
        if (!$outletId) {
            return response()->json([
                'success' => false,
                'message' => 'Outlet ID required'
            ], 422);
        }

        // Validate outlet access
        $this->validateOutletAccess($outletId);

        try {
            $query = ChartOfAccount::where('outlet_id', $outletId)
                ->where('status', 'active');

            if ($type) {
                $query->where('type', $type);
            }

            $allAccounts = $query->orderBy('code')->get();

            // Filter out parent accounts that have children
            $filteredAccounts = $allAccounts->filter(function ($account) use ($allAccounts) {
                // Check if this account has children
                $hasChildren = $allAccounts->where('parent_id', $account->id)->count() > 0;
                
                // Only include accounts that don't have children (leaf accounts)
                return !$hasChildren;
            });

            // Group by type for easier frontend handling
            $accountsByType = [
                'expense' => $filteredAccounts->where('type', 'expense')->values(),
                'liability' => $filteredAccounts->where('type', 'liability')->values(),
                'asset' => $filteredAccounts->where('type', 'asset')->values(),
            ];

            return response()->json([
                'success' => true,
                'data' => $accountsByType
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading accounts: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data akun'
            ], 500);
        }
    }

    public function getSettings(Request $request)
    {
        $outletId = $request->get('outlet_id');
        
        if (!$outletId) {
            return response()->json([
                'success' => false,
                'message' => 'Outlet ID required'
            ], 422);
        }

        // Validate outlet access
        $this->validateOutletAccess($outletId);

        $setting = PayrollCoaSetting::where('outlet_id', $outletId)->first();

        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'salary_expense_account_id' => 'required|exists:chart_of_accounts,id',
            'overtime_expense_account_id' => 'nullable|exists:chart_of_accounts,id',
            'bonus_expense_account_id' => 'nullable|exists:chart_of_accounts,id',
            'allowance_expense_account_id' => 'nullable|exists:chart_of_accounts,id',
            'tax_payable_account_id' => 'required|exists:chart_of_accounts,id',
            'loan_receivable_account_id' => 'nullable|exists:chart_of_accounts,id',
            'salary_payable_account_id' => 'required|exists:chart_of_accounts,id',
            'cash_account_id' => 'required|exists:chart_of_accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate outlet access
        $this->validateOutletAccess($request->outlet_id);

        try {
            DB::beginTransaction();

            $setting = PayrollCoaSetting::updateOrCreate(
                ['outlet_id' => $request->outlet_id],
                $request->only([
                    'salary_expense_account_id',
                    'overtime_expense_account_id',
                    'bonus_expense_account_id',
                    'allowance_expense_account_id',
                    'tax_payable_account_id',
                    'loan_receivable_account_id',
                    'salary_payable_account_id',
                    'cash_account_id',
                ])
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Setting COA berhasil disimpan',
                'data' => $setting
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving payroll COA settings: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }
}
