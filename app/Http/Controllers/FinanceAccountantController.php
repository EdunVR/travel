<?php

namespace App\Http\Controllers;

use App\Traits\HasOutletFilter;
use App\Traits\HasCompanySettings;

use App\Models\ChartOfAccount;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ChartOfAccountsExport;
use App\Imports\ChartOfAccountsImport;
use App\Models\AccountingBook;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\OpeningBalance;
use App\Models\FixedAsset;
use App\Models\FixedAssetDepreciation;
use App\Models\Expense;
use App\Services\FinanceExportService;
use App\Services\ChartOfAccountService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class FinanceAccountantController extends Controller
{
    use HasOutletFilter;

    use \App\Traits\HasOutletFilter;

    protected $coaService;

    public function __construct(ChartOfAccountService $coaService)
    {
        $this->coaService = $coaService;
    }
    use HasCompanySettings;


    public function jurnalIndex(Request $request)
    {
        $journalId = $request->get('journal_id');
        $outletId = $request->get('outlet_id');
        
        // Pass parameters to view if needed
        return view('admin.finance.jurnal.index', [
            'journal_id' => $journalId,
            'outlet_id' => $outletId
        ]);
    }

    public function storeAccount(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'code' => 'required|string|max:50',
                'name' => 'required|string|max:255',
                'type' => 'required|in:asset,liability,equity,revenue,expense,otherrevenue,otherexpense',
                'category' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:chart_of_accounts,id',
                'status' => 'required|in:active,inactive'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek duplikasi kode akun di outlet yang sama
            $existingAccount = ChartOfAccount::where('outlet_id', $request->outlet_id)
                ->where('code', $request->code)
                ->first();

            if ($existingAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode akun sudah digunakan di outlet ini'
                ], 422);
            }

            // Tentukan level berdasarkan parent_id
            $level = 1;
            if ($request->parent_id) {
                $parent = ChartOfAccount::find($request->parent_id);
                $level = $parent->level + 1;
                
                // Validasi: parent harus memiliki type yang sama
                if ($parent->type !== $request->type) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tipe akun anak harus sama dengan tipe akun induk'
                    ], 422);
                }
            }

            $account = ChartOfAccount::create([
                'outlet_id' => $request->outlet_id,
                'code' => $request->code,
                'name' => $request->name,
                'type' => $request->type,
                'category' => $request->category,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'level' => $level,
                'status' => $request->status,
                'balance' => 0
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $account,
                'message' => 'Akun berhasil dibuat'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating account: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat akun: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateAccount(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $account = ChartOfAccount::find($id);
            
            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:50',
                'name' => 'required|string|max:255',
                'type' => 'required|in:asset,liability,equity,revenue,expense,otherrevenue,otherexpense',
                'category' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:chart_of_accounts,id',
                'status' => 'required|in:active,inactive'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek duplikasi kode akun (kecuali untuk akun ini)
            $existingAccount = ChartOfAccount::where('outlet_id', $account->outlet_id)
                ->where('code', $request->code)
                ->where('id', '!=', $id)
                ->first();

            if ($existingAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode akun sudah digunakan di outlet ini'
                ], 422);
            }

            // Update level jika parent_id berubah
            $level = 1;
            if ($request->parent_id) {
                $parent = ChartOfAccount::find($request->parent_id);
                $level = $parent->level + 1;
            }

            $account->update([
                'code' => $request->code,
                'name' => $request->name,
                'type' => $request->type,
                'category' => $request->category,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'level' => $level,
                'status' => $request->status
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $account,
                'message' => 'Akun berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui akun: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleAccount($id): JsonResponse
    {
        try {
            $account = ChartOfAccount::find($id);
            
            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }

            $newStatus = $account->status === 'active' ? 'inactive' : 'active';
            $account->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'data' => $account,
                'message' => 'Status akun berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status akun: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getParentAccounts(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $type = $request->get('type', 'all');

            $parents = ChartOfAccount::byOutlet($outletId)
                ->byType($type)
                ->parentAccounts()
                ->active()
                ->orderBy('code')
                ->get(['id', 'code', 'name', 'type']);

            return response()->json([
                'success' => true,
                'data' => $parents,
                'message' => 'Data akun parent berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data akun parent: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateAccountCode(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id');
            $parentId = $request->get('parent_id');
            $type = $request->get('type', 'asset');
            $category = $request->get('category', '');

            \Log::info('Generating account code', [
                'outlet_id' => $outletId,
                'parent_id' => $parentId,
                'type' => $type,
                'category' => $category
            ]);

            if (!$outletId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlet ID diperlukan'
                ], 422);
            }

            // Validate outlet access
            $this->validateOutletAccess($outletId);

            // Use database transaction with locking to prevent race conditions
            $code = DB::transaction(function () use ($outletId, $parentId, $type, $category) {
                return $this->generateAccountCodeForOutlet($outletId, $parentId, $type, $category);
            });

            \Log::info('Generated account code', [
                'outlet_id' => $outletId,
                'generated_code' => $code
            ]);

            return response()->json([
                'success' => true,
                'data' => ['code' => $code],
                'message' => 'Kode akun berhasil digenerate'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error generating account code: ' . $e->getMessage(), [
                'outlet_id' => $request->get('outlet_id'),
                'parent_id' => $request->get('parent_id'),
                'type' => $request->get('type'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate kode akun: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate account code for specific outlet
     */
    private function generateAccountCodeForOutlet($outletId, $parentId = null, $type = 'asset', $category = '')
    {
        if ($parentId) {
            // Generate child account code
            $parentAccount = ChartOfAccount::where('id', $parentId)
                ->where('outlet_id', $outletId)
                ->lockForUpdate()
                ->first();

            if (!$parentAccount) {
                throw new \Exception("Parent account tidak ditemukan");
            }

            \Log::info('Generating child account code', [
                'parent_id' => $parentId,
                'parent_code' => $parentAccount->code,
                'outlet_id' => $outletId
            ]);

            // Find all children under this parent with locking
            $children = ChartOfAccount::where('parent_id', $parentId)
                ->where('outlet_id', $outletId)
                ->lockForUpdate()
                ->get();

            \Log::info('Found children accounts', [
                'count' => $children->count(),
                'children_codes' => $children->pluck('code')->toArray()
            ]);

            $maxChildNumber = 0;
            $parentCode = $parentAccount->code;

            foreach ($children as $child) {
                // Extract child number from code like "1001.001" -> "001"
                if (strpos($child->code, $parentCode . '.') === 0) {
                    $childPart = substr($child->code, strlen($parentCode . '.'));
                    // Handle multi-level children like "1001.001.001"
                    $firstLevel = explode('.', $childPart)[0];
                    $childNumber = (int)$firstLevel;
                    if ($childNumber > $maxChildNumber) {
                        $maxChildNumber = $childNumber;
                    }
                }
            }

            $nextNumber = $maxChildNumber + 1;
            $generatedCode = $parentCode . '.' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            \Log::info('Generated child code', [
                'max_child_number' => $maxChildNumber,
                'next_number' => $nextNumber,
                'generated_code' => $generatedCode
            ]);

            // Verify the generated code doesn't already exist
            $existingCode = ChartOfAccount::where('code', $generatedCode)
                ->where('outlet_id', $outletId)
                ->first();
            
            if ($existingCode) {
                \Log::warning('Generated code already exists, retrying...', [
                    'generated_code' => $generatedCode,
                    'existing_id' => $existingCode->id
                ]);
                
                // Retry with next number
                $nextNumber++;
                $generatedCode = $parentCode . '.' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }

            return $generatedCode;
        } else {
            // Generate parent account code based on type
            $typePrefix = $this->getTypePrefixForCode($type);
            
            \Log::info('Generating parent account code', [
                'type' => $type,
                'type_prefix' => $typePrefix,
                'outlet_id' => $outletId
            ]);
            
            // Find all accounts of this type and outlet with locking
            $accounts = ChartOfAccount::where('outlet_id', $outletId)
                ->where('type', $type)
                ->whereNull('parent_id')
                ->lockForUpdate()
                ->get();

            \Log::info('Found existing accounts', [
                'count' => $accounts->count(),
                'existing_codes' => $accounts->pluck('code')->toArray()
            ]);

            $maxNumber = 0;

            foreach ($accounts as $account) {
                // Extract number from codes like "1001", "1002", etc.
                if (strpos($account->code, $typePrefix) === 0) {
                    $numberPart = substr($account->code, strlen($typePrefix));
                    // Handle codes that might have dots (get only the first part)
                    $firstPart = explode('.', $numberPart)[0];
                    $number = (int)$firstPart;
                    if ($number > $maxNumber) {
                        $maxNumber = $number;
                    }
                }
            }

            $nextNumber = $maxNumber + 1;
            $generatedCode = $typePrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            \Log::info('Generated parent code', [
                'max_number' => $maxNumber,
                'next_number' => $nextNumber,
                'generated_code' => $generatedCode
            ]);

            // Verify the generated code doesn't already exist
            $existingCode = ChartOfAccount::where('code', $generatedCode)
                ->where('outlet_id', $outletId)
                ->first();
            
            if ($existingCode) {
                \Log::warning('Generated code already exists, retrying...', [
                    'generated_code' => $generatedCode,
                    'existing_id' => $existingCode->id
                ]);
                
                // Retry with next number
                $nextNumber++;
                $generatedCode = $typePrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }

            return $generatedCode;
        }
    }

    /**
     * Get type prefix for account code generation
     */
    private function getTypePrefixForCode($type)
    {
        $prefixes = [
            'asset' => '1',
            'liability' => '2', 
            'equity' => '3',
            'revenue' => '4',
            'expense' => '5',
            'other_income' => '6',
            'other_expense' => '7'
        ];

        return $prefixes[$type] ?? '1';
    }

    private function calculateAccountStats($outletId): array
    {
        // Hitung total accounts (semua level)
        $totalAccounts = ChartOfAccount::byOutlet($outletId)->count();
        $activeAccounts = ChartOfAccount::byOutlet($outletId)->active()->count();

        // Hitung saldo akumulasi per tipe (termasuk semua child accounts)
        $types = ['asset', 'liability', 'equity', 'revenue', 'expense', 'otherrevenue', 'otherexpense'];
        $typeBalances = [];
        $typeCounts = [];

        foreach ($types as $type) {
            // Dapatkan semua parent accounts untuk tipe ini
            $parentAccounts = ChartOfAccount::byOutlet($outletId)
                ->active()
                ->where('type', $type)
                ->whereNull('parent_id')
                ->get();

            $totalBalance = 0;
            $accountCount = 0;

            foreach ($parentAccounts as $parentAccount) {
                // Hitung saldo akumulasi termasuk children
                $accumulatedBalance = $this->calculateAccumulatedBalanceWithChildren($parentAccount, $outletId);
                $totalBalance += $accumulatedBalance;
                $accountCount += $this->countChildAccounts($parentAccount) + 1; // +1 untuk parent sendiri
            }

            $typeBalances[$type] = $totalBalance;
            $typeCounts[$type] = $accountCount;
        }

        // Hitung parent accounts balance untuk chart
        $parentAccountsBalance = ChartOfAccount::byOutlet($outletId)
            ->active()
            ->whereNull('parent_id')
            ->with(['children'])
            ->get()
            ->map(function($parentAccount) use ($outletId) {
                return [
                    'id' => $parentAccount->id,
                    'name' => $parentAccount->name,
                    'code' => $parentAccount->code,
                    'type' => $parentAccount->type,
                    'balance' => $this->calculateAccumulatedBalanceWithChildren($parentAccount, $outletId)
                ];
            });

        return [
            'totalAccounts' => $totalAccounts,
            'activeAccounts' => $activeAccounts,
            'assetBalance' => $typeBalances['asset'] ?? 0,
            'liabilityBalance' => $typeBalances['liability'] ?? 0,
            'equityBalance' => $typeBalances['equity'] ?? 0,
            'revenueBalance' => $typeBalances['revenue'] ?? 0,
            'expenseBalance' => $typeBalances['expense'] ?? 0,
            'assetAccounts' => $typeCounts['asset'] ?? 0,
            'liabilityAccounts' => $typeCounts['liability'] ?? 0,
            'equityAccounts' => $typeCounts['equity'] ?? 0,
            'revenueAccounts' => $typeCounts['revenue'] ?? 0,
            'expenseAccounts' => $typeCounts['expense'] ?? 0,
            'otherrevenueBalance' => $typeBalances['otherrevenue'] ?? 0,
            'otherexpenseBalance' => $typeBalances['otherexpense'] ?? 0,
            'otherrevenueAccounts' => $typeCounts['otherrevenue'] ?? 0,
            'otherexpenseAccounts' => $typeCounts['otherexpense'] ?? 0,
            'totalBalance' => array_sum($typeBalances),
            'parentAccountsBalance' => $parentAccountsBalance
        ];
    }

    /**
     * Hitung jumlah child accounts (recursive)
     */
    private function countChildAccounts($account): int
    {
        $count = 0;
        if ($account->children && $account->children->count() > 0) {
            foreach ($account->children as $child) {
                $count += 1 + $this->countChildAccounts($child); // child sendiri + grandchildren
            }
        }
        return $count;
    }

    public function chartOfAccountsData(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $type = $request->get('type', 'all');
            $status = $request->get('status', 'all');
            $search = $request->get('search', '');
            $includeBalance = $request->get('include_balance', false);

            $query = ChartOfAccount::query();
            
            // Only load relationships if not searching (for performance)
            if (!$search) {
                $query->with(['parent', 'children']);
            }
            
            $accounts = $query
                ->byOutlet($outletId)
                ->byType($type)
                ->when($status !== 'all', function($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->when($search, function($query) use ($search) {
                    return $query->search($search);
                })
                ->orderBy('code')
                ->get();

            // Only calculate balances if explicitly requested or not searching
            if ($includeBalance || !$search) {
                $accounts = $accounts->map(function($account) use ($outletId) {
                    try {
                        // Hitung saldo akumulasi dari jurnal
                        $account->accumulated_balance = $this->calculateAccumulatedBalanceWithChildren($account, $outletId);
                    } catch (\Exception $e) {
                        // If balance calculation fails, set to 0
                        $account->accumulated_balance = 0;
                    }
                    return $account;
                });
            } else {
                // For search results, just add basic info without balance calculation
                $accounts = $accounts->map(function($account) {
                    $account->accumulated_balance = 0;
                    $account->type_name = $this->getAccountTypeName($account->type);
                    return $account;
                });
            }

            $stats = null;
            if (!$search) {
                try {
                    $stats = $this->calculateAccountStats($outletId);
                } catch (\Exception $e) {
                    $stats = null;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $accounts,
                'stats' => $stats,
                'message' => 'Data akun berhasil diambil'
            ]);
        } catch (\Exception $e) {
            \Log::error('Chart of Accounts Data Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data akun: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOutlets(Request $request): JsonResponse
    {
        try {
            $outlets = $this->getAccessibleOutlets();

            return response()->json([
                'success' => true,
                'data' => $outlets,
                'message' => 'Data outlet berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data outlet: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteAccount($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $account = ChartOfAccount::with('children')->find($id);
            
            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }

            // Validasi: akun yang memiliki children tidak bisa dihapus
            if ($account->children->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun yang memiliki akun anak'
                ], 422);
            }

            // Validasi: akun dengan saldo tidak nol tidak bisa dihapus
            if (floatval($account->balance) !== 0.0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun yang memiliki saldo tidak nol'
                ], 422);
            }

            // Validasi: akun sistem tidak bisa dihapus
            if ($account->is_system_account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun sistem'
                ], 422);
            }

            $accountName = $account->name;
            $accountCode = $account->code;
            
            $account->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Akun {$accountCode} - {$accountName} berhasil dihapus"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting account: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus akun: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportAccounts(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            
            if (!$outletId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlet ID diperlukan'
                ], 422);
            }

            $outlet = Outlet::find($outletId);
            if (!$outlet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlet tidak ditemukan'
                ], 404);
            }

            $filename = 'daftar_akun_' . str_replace(' ', '_', $outlet->nama_outlet) . '_' . date('Y-m-d') . '.xlsx';

            return Excel::download(new ChartOfAccountsExport($outletId), $filename);
        } catch (\Exception $e) {
            \Log::error('Export error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal export data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importAccounts(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ]
            ]);

            $outletId = $request->outlet_id;
            $file = $request->file('file');

            $import = new ChartOfAccountsImport($outletId);
            Excel::import($import, $file);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Import berhasil: ' . $import->getResultMessage(),
                'data' => [
                    'total' => $import->getTotal(),
                    'created' => $import->getCreated(),
                    'updated' => $import->getUpdated(),
                    'errors' => $import->getErrors()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Import error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal import data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function accountingBooksData(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);
            $search = $request->get('search', '');
            $outletId = $request->get('outlet_id');
            $type = $request->get('type', 'all');
            $status = $request->get('status', 'all');

            $query = AccountingBook::with(['outlet'])
                ->when($outletId, function($q) use ($outletId) {
                    $q->where('outlet_id', $outletId);
                })
                ->when($type !== 'all', function($q) use ($type) {
                    $q->where('type', $type);
                })
                ->when($status !== 'all', function($q) use ($status) {
                    $q->where('status', $status);
                })
                ->when($search, function($q) use ($search) {
                    $q->where(function($query) use ($search) {
                        $query->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                })
                ->orderBy('created_at', 'desc');

            $accountingBooks = $query->paginate($perPage, ['*'], 'page', $page);

            // Hitung statistik
            $stats = $this->calculateBookStats($outletId);

            return response()->json([
                'success' => true,
                'data' => $accountingBooks->items(),
                'stats' => $stats,
                'meta' => [
                    'current_page' => $accountingBooks->currentPage(),
                    'per_page' => $accountingBooks->perPage(),
                    'total' => $accountingBooks->total(),
                    'last_page' => $accountingBooks->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching accounting books data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch accounting books data'
            ], 500);
        }
    }

    public function storeBook(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'code' => 'required|string|max:50',
                'name' => 'required|string|max:255',
                'type' => 'required|in:general,cash,bank,sales,purchase,inventory,payroll',
                'description' => 'nullable|string',
                'currency' => 'required|in:IDR,USD,EUR,SGD',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'opening_balance' => 'nullable|numeric',
                'status' => 'required|in:active,inactive,draft,closed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek duplikasi kode buku di outlet yang sama
            $existingBook = AccountingBook::where('outlet_id', $request->outlet_id)
                ->where('code', $request->code)
                ->first();

            if ($existingBook) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode buku sudah digunakan di outlet ini'
                ], 422);
            }

            // Validasi periode tidak overlap dengan buku lain
            $overlapBook = AccountingBook::where('outlet_id', $request->outlet_id)
                ->where('type', $request->type)
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                        ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                        ->orWhere(function($q) use ($request) {
                            $q->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                        });
                })
                ->first();

            if ($overlapBook) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode buku bertabrakan dengan buku lain dengan tipe yang sama'
                ], 422);
            }

            $book = AccountingBook::create([
                'outlet_id' => $request->outlet_id,
                'code' => $request->code,
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'currency' => $request->currency,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'opening_balance' => $request->opening_balance ?? 0,
                'closing_balance' => $request->opening_balance ?? 0,
                'status' => $request->status,
                'is_locked' => false
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $book,
                'message' => 'Buku akuntansi berhasil dibuat'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating accounting book: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat buku akuntansi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateBook(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $book = AccountingBook::find($id);
            
            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku akuntansi tidak ditemukan'
                ], 404);
            }

            // Validasi: buku yang dikunci tidak bisa di-edit
            if ($book->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku sedang dikunci dan tidak dapat diubah'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:50',
                'name' => 'required|string|max:255',
                'type' => 'required|in:general,cash,bank,sales,purchase,inventory,payroll',
                'description' => 'nullable|string',
                'currency' => 'required|in:IDR,USD,EUR,SGD',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'opening_balance' => 'nullable|numeric',
                'status' => 'required|in:active,inactive,draft,closed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek duplikasi kode buku (kecuali untuk buku ini)
            $existingBook = AccountingBook::where('outlet_id', $book->outlet_id)
                ->where('code', $request->code)
                ->where('id', '!=', $id)
                ->first();

            if ($existingBook) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode buku sudah digunakan di outlet ini'
                ], 422);
            }

            // Validasi periode tidak overlap dengan buku lain (kecuali buku ini)
            $overlapBook = AccountingBook::where('outlet_id', $book->outlet_id)
                ->where('type', $request->type)
                ->where('id', '!=', $id)
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                        ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                        ->orWhere(function($q) use ($request) {
                            $q->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                        });
                })
                ->first();

            if ($overlapBook) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode buku bertabrakan dengan buku lain dengan tipe yang sama'
                ], 422);
            }

            $book->update([
                'code' => $request->code,
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'currency' => $request->currency,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'opening_balance' => $request->opening_balance ?? $book->opening_balance,
                'status' => $request->status
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $book,
                'message' => 'Buku akuntansi berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating accounting book: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui buku akuntansi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleBook($id): JsonResponse
    {
        try {
            $book = AccountingBook::find($id);
            
            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku akuntansi tidak ditemukan'
                ], 404);
            }

            // Validasi: buku yang dikunci tidak bisa diubah status
            if ($book->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku sedang dikunci dan tidak dapat diubah statusnya'
                ], 422);
            }

            $newStatus = $book->status === 'active' ? 'inactive' : 'active';
            $book->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'data' => $book,
                'message' => 'Status buku berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status buku: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateBookCode(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id');
            $type = $request->get('type', 'general');

            if (!$outletId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlet ID diperlukan'
                ], 422);
            }

            // Validasi outlet exists
            $outletExists = Outlet::where('id_outlet', $outletId)->exists();
            if (!$outletExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlet tidak ditemukan'
                ], 422);
            }

            $code = AccountingBook::generateBookCode($outletId, $type);

            return response()->json([
                'success' => true,
                'data' => ['code' => $code],
                'message' => 'Kode buku berhasil digenerate'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating book code: ' . $e->getMessage());
            \Log::error('Outlet ID: ' . $outletId . ', Type: ' . $type);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate kode buku: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateBookStats($outletId): array
    {
        $totalBooks = AccountingBook::byOutlet($outletId)->count();
        $activeBooks = AccountingBook::byOutlet($outletId)->active()->count();
        $inactiveBooks = AccountingBook::byOutlet($outletId)->inactive()->count();
        $draftBooks = AccountingBook::byOutlet($outletId)->draft()->count();
        $closedBooks = AccountingBook::byOutlet($outletId)->closed()->count();
        
        $totalEntries = AccountingBook::byOutlet($outletId)->sum('total_entries');
        $totalBalance = AccountingBook::byOutlet($outletId)->sum('closing_balance');
        $avgEntries = $totalBooks > 0 ? round($totalEntries / $totalBooks) : 0;
        
        // Entri bulan ini (simulasi - nanti bisa dihitung dari journal entries)
        $entriesThisMonth = rand(10, 100);

        return [
            'totalBooks' => $totalBooks,
            'activeBooks' => $activeBooks,
            'inactiveBooks' => $inactiveBooks, // Tambahkan ini
            'draftBooks' => $draftBooks,
            'closedBooks' => $closedBooks,
            'totalEntries' => $totalEntries,
            'entriesThisMonth' => $entriesThisMonth,
            'totalBalance' => $totalBalance,
            'avgEntries' => $avgEntries
        ];
    }

    public function deleteBook($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $book = AccountingBook::find($id);
            
            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku akuntansi tidak ditemukan'
                ], 404);
            }

            // Validasi: buku yang dikunci tidak bisa dihapus
            if ($book->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku sedang dikunci dan tidak dapat dihapus'
                ], 422);
            }

            // Validasi: hanya buku dengan status draft dan tanpa entri yang bisa dihapus
            if ($book->status !== 'draft' || $book->total_entries > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya buku dengan status draft dan tanpa entri yang dapat dihapus'
                ], 422);
            }

            $bookName = $book->name;
            $bookCode = $book->code;
            
            $book->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Buku {$bookCode} - {$bookName} berhasil dihapus"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting accounting book: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus buku akuntansi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showBook($id): JsonResponse
    {
        try {
            $book = AccountingBook::with([
                'outlet',
                'journalEntries.journalEntryDetails.account' // Include details dengan account
            ])->find($id);
            
            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku akuntansi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $book,
                'message' => 'Data buku berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data buku: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bookDetailPage($id)
    {
        // Return view untuk halaman detail
        return view('admin.finance.buku.detail', ['id' => $id]);
    }

    public function storeJournalEntry(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'book_id' => 'required|exists:accounting_books,id',
                'transaction_date' => 'required|date',
                'description' => 'required|string|max:500',
                'entries' => 'required|array|min:2',
                'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
                'entries.*.debit' => 'required_without:entries.*.credit|numeric|min:0',
                'entries.*.credit' => 'required_without:entries.*.debit|numeric|min:0',
                'entries.*.description' => 'nullable|string|max:255',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $book = AccountingBook::find($request->book_id);
            
            // Validasi: buku harus aktif dan tidak terkunci
            if ($book->status !== 'active' || $book->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku tidak aktif atau terkunci'
                ], 422);
            }

            // Generate transaction number
            $transactionNumber = JournalEntry::generateTransactionNumber($request->book_id);

            // Hitung total debit dan credit
            $totalDebit = 0;
            $totalCredit = 0;
            
            foreach ($request->entries as $entry) {
                $totalDebit += $entry['debit'] ?? 0;
                $totalCredit += $entry['credit'] ?? 0;
            }

            // Validasi: debit harus sama dengan credit
            if ($totalDebit !== $totalCredit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total debit dan credit tidak balance'
                ], 422);
            }

            // Buat journal entry
            $journalEntry = JournalEntry::create([
                'book_id' => $request->book_id,
                'outlet_id' => $book->outlet_id,
                'transaction_number' => $transactionNumber,
                'transaction_date' => $request->transaction_date,
                'description' => $request->description,
                'status' => 'posted', // Auto post
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'notes' => $request->notes,
                'posted_at' => now()
            ]);

            // Buat journal entry details
            foreach ($request->entries as $entry) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                    'description' => $entry['description'] ?? null
                ]);

                // Update account balance
                $account = ChartOfAccount::find($entry['account_id']);
                if ($account) {
                    $balanceChange = ($entry['debit'] ?? 0) - ($entry['credit'] ?? 0);
                    $account->updateBalance($balanceChange);
                }
            }

            // Update book total entries
            $book->incrementEntries();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $journalEntry,
                'message' => 'Entri jurnal berhasil dibuat'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating journal entry: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat entri jurnal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteJournalEntry($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $journalEntry = JournalEntry::with('journalEntryDetails')->find($id);
            
            if (!$journalEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Entri jurnal tidak ditemukan'
                ], 404);
            }

            // Validasi: hanya entry dengan status draft yang bisa dihapus
            if ($journalEntry->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya entri jurnal dengan status draft yang dapat dihapus'
                ], 422);
            }

            // Reverse account balances
            foreach ($journalEntry->journalEntryDetails as $detail) {
                $account = $detail->account;
                if ($account) {
                    $balanceChange = -($detail->debit - $detail->credit);
                    $account->updateBalance($balanceChange);
                }
            }

            $transactionNumber = $journalEntry->transaction_number;
            $journalEntry->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Entri jurnal {$transactionNumber} berhasil dihapus"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting journal entry: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus entri jurnal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showJournalEntry($id): JsonResponse
    {
        try {
            $journalEntry = JournalEntry::with(['journalEntryDetails.account', 'book'])
                ->find($id);
            
            if (!$journalEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Entri jurnal tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $journalEntry,
                'message' => 'Data entri jurnal berhasil diambil'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error showing journal entry: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data entri jurnal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function editJournalEntry($id): JsonResponse
    {
        try {
            $journalEntry = JournalEntry::with(['journalEntryDetails.account', 'book'])
                ->find($id);
            
            if (!$journalEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Entri jurnal tidak ditemukan'
                ], 404);
            }

            // Validasi: hanya entry dengan status draft yang bisa diedit
            if ($journalEntry->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya entri jurnal dengan status draft yang dapat diedit'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => $journalEntry,
                'message' => 'Data entri jurnal untuk edit berhasil diambil'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting journal entry for edit: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data entri jurnal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateJournalEntry(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $journalEntry = JournalEntry::with('journalEntryDetails')->find($id);
            
            if (!$journalEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Entri jurnal tidak ditemukan'
                ], 404);
            }

            // Validasi: hanya entry dengan status draft yang bisa diedit
            if ($journalEntry->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya entri jurnal dengan status draft yang dapat diedit'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'transaction_date' => 'required|date',
                'description' => 'required|string|max:500',
                'entries' => 'required|array|min:2',
                'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
                'entries.*.debit' => 'required_without:entries.*.credit|numeric|min:0',
                'entries.*.credit' => 'required_without:entries.*.debit|numeric|min:0',
                'entries.*.description' => 'nullable|string|max:255',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Hitung total debit dan credit
            $totalDebit = 0;
            $totalCredit = 0;
            
            foreach ($request->entries as $entry) {
                $totalDebit += $entry['debit'] ?? 0;
                $totalCredit += $entry['credit'] ?? 0;
            }

            // Validasi: debit harus sama dengan credit
            if ($totalDebit !== $totalCredit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total debit dan credit tidak balance'
                ], 422);
            }

            // Reverse old account balances
            foreach ($journalEntry->journalEntryDetails as $detail) {
                $account = $detail->account;
                if ($account) {
                    $balanceChange = -($detail->debit - $detail->credit);
                    $account->updateBalance($balanceChange);
                }
            }

            // Delete old details
            $journalEntry->journalEntryDetails()->delete();

            // Update journal entry
            $journalEntry->update([
                'transaction_date' => $request->transaction_date,
                'description' => $request->description,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'notes' => $request->notes
            ]);

            // Create new journal entry details
            foreach ($request->entries as $entry) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                    'description' => $entry['description'] ?? null
                ]);

                // Update account balance
                $account = ChartOfAccount::find($entry['account_id']);
                if ($account) {
                    $balanceChange = ($entry['debit'] ?? 0) - ($entry['credit'] ?? 0);
                    $account->updateBalance($balanceChange);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $journalEntry->fresh(['journalEntryDetails.account']),
                'message' => 'Entri jurnal berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating journal entry: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui entri jurnal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBookActivityData(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $period = $request->get('period', 'monthly');
            
            $activityData = [];
            $labels = [];
            
            // Tentukan jumlah periode berdasarkan pilihan
            $periodCount = 6; // Default 6 bulan
            $dateFormat = 'M';
            
            if ($period === 'quarterly') {
                $periodCount = 4; // 4 quarter
                $dateFormat = 'Q Y';
            } elseif ($period === 'yearly') {
                $periodCount = 5; // 5 tahun
                $dateFormat = 'Y';
            }
            
            for ($i = $periodCount - 1; $i >= 0; $i--) {
                if ($period === 'monthly') {
                    $currentPeriod = now()->subMonths($i);
                    $startDate = $currentPeriod->copy()->startOfMonth();
                    $endDate = $currentPeriod->copy()->endOfMonth();
                } elseif ($period === 'quarterly') {
                    $currentPeriod = now()->subQuarters($i);
                    $startDate = $currentPeriod->copy()->startOfQuarter();
                    $endDate = $currentPeriod->copy()->endOfQuarter();
                } else { // yearly
                    $currentPeriod = now()->subYears($i);
                    $startDate = $currentPeriod->copy()->startOfYear();
                    $endDate = $currentPeriod->copy()->endOfYear();
                }
                
                $entriesCount = JournalEntry::whereHas('book', function($query) use ($outletId) {
                        $query->where('outlet_id', $outletId);
                    })
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->where('status', 'posted')
                    ->count();
                
                $activityData[] = $entriesCount;
                
                // Format label berdasarkan periode
                if ($period === 'monthly') {
                    $labels[] = $currentPeriod->translatedFormat('M');
                } elseif ($period === 'quarterly') {
                    $quarter = ceil($currentPeriod->month / 3);
                    $labels[] = 'Q' . $quarter . ' ' . $currentPeriod->format('y');
                } else {
                    $labels[] = $currentPeriod->format('Y');
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'Entri Jurnal',
                            'data' => $activityData,
                            'borderColor' => '#3b82f6',
                            'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                            'tension' => 0.4,
                            'fill' => true
                        ]
                    ]
                ],
                'message' => 'Data aktivitas berhasil diambil'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting book activity data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data aktivitas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function journalsData(Request $request): JsonResponse
    {
        try {
            // Support both single outlet_id and multiple outlet_ids[]
            $outletIds = $request->get('outlet_ids', []);
            if (empty($outletIds)) {
                $singleOutletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
                $outletIds = [$singleOutletId];
            }
            
            // Ensure outlet_ids is an array
            if (!is_array($outletIds)) {
                $outletIds = [$outletIds];
            }
            
            // Validate outlet access
            foreach ($outletIds as $outletId) {
                $this->validateOutletAccess($outletId);
            }

            $bookId = $request->get('book_id', 'all');
            $status = $request->get('status', 'all');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $search = $request->get('search', '');

            $journals = JournalEntry::with(['book', 'journalEntryDetails.account'])
                ->whereHas('book', function($query) use ($outletIds) {
                    $query->whereIn('outlet_id', $outletIds);
                })
                ->when($bookId !== 'all', function($query) use ($bookId) {
                    $query->where('book_id', $bookId);
                })
                ->when($status !== 'all', function($query) use ($status) {
                    $query->where('status', $status);
                })
                ->when($dateFrom, function($query) use ($dateFrom) {
                    $query->where('transaction_date', '>=', $dateFrom);
                })
                ->when($dateTo, function($query) use ($dateTo) {
                    $query->where('transaction_date', '<=', $dateTo);
                })
                ->when($search, function($query) use ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('transaction_number', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%");
                    });
                })
                ->orderBy('transaction_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($journal) {
                    $entries = $journal->journalEntryDetails->map(function($detail) {
                        return [
                            'id' => $detail->id,
                            'account_id' => $detail->account_id,
                            'account_code' => $detail->account->code ?? '-',
                            'account_name' => $detail->account->name ?? '-',
                            'debit' => floatval($detail->debit),
                            'credit' => floatval($detail->credit),
                            'description' => $detail->description
                        ];
                    });

                    return [
                        'id' => $journal->id,
                        'date_formatted' => $journal->transaction_date->translatedFormat('d M Y'),
                        'transaction_date' => $journal->transaction_date->format('Y-m-d'),
                        'reference' => $journal->transaction_number,
                        'description' => $journal->description,
                        'book_name' => $journal->book->name,
                        'book_id' => $journal->book_id,
                        'total_debit' => floatval($journal->total_debit),
                        'total_credit' => floatval($journal->total_credit),
                        'balance' => floatval($journal->total_debit) - floatval($journal->total_credit),
                        'entries_count' => $journal->journalEntryDetails->count(),
                        'entries' => $entries,
                        'status' => $journal->status,
                        'notes' => $journal->notes,
                        'posted_at' => $journal->posted_at,
                        'showDetails' => false // Frontend state
                    ];
                });

            $unbalancedJournals = $journals->where('balance', '!=', 0)
                ->where('status', 'draft')
                ->values();

            return response()->json([
                'success' => true,
                'data' => $journals,
                'unbalanced_journals' => $unbalancedJournals,
                'message' => 'Data jurnal berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jurnal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function journalStats(Request $request): JsonResponse
    {
        try {
            // Support both single outlet_id and multiple outlet_ids[]
            $outletIds = $request->get('outlet_ids', []);
            if (empty($outletIds)) {
                $singleOutletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
                $outletIds = [$singleOutletId];
            }
            
            // Ensure outlet_ids is an array
            if (!is_array($outletIds)) {
                $outletIds = [$outletIds];
            }
            
            // Validate outlet access
            foreach ($outletIds as $outletId) {
                $this->validateOutletAccess($outletId);
            }

            $totalJournals = JournalEntry::whereHas('book', function($query) use ($outletIds) {
                $query->whereIn('outlet_id', $outletIds);
            })->count();

            $thisMonth = JournalEntry::whereHas('book', function($query) use ($outletIds) {
                $query->whereIn('outlet_id', $outletIds);
            })->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->count();

            $totals = JournalEntry::whereHas('book', function($query) use ($outletIds) {
                $query->whereIn('outlet_id', $outletIds);
            })->selectRaw('SUM(total_debit) as total_debit, SUM(total_credit) as total_credit')
            ->first();

            $balancedJournals = JournalEntry::whereHas('book', function($query) use ($outletIds) {
                $query->whereIn('outlet_id', $outletIds);
            })->where('status', 'posted')
            ->whereRaw('ABS(total_debit - total_credit) < 0.01') // Tolerance for float comparison
            ->count();

            $unbalancedJournals = JournalEntry::whereHas('book', function($query) use ($outletIds) {
                $query->whereIn('outlet_id', $outletIds);
            })->where('status', 'draft')
            ->whereRaw('ABS(total_debit - total_credit) >= 0.01')
            ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'totalJournals' => $totalJournals,
                    'thisMonth' => $thisMonth,
                    'totalDebit' => floatval($totals->total_debit ?? 0),
                    'totalCredit' => floatval($totals->total_credit ?? 0),
                    'balancedJournals' => $balancedJournals,
                    'unbalancedJournals' => $unbalancedJournals
                ],
                'message' => 'Statistik jurnal berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik jurnal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showJournal($id): JsonResponse
    {
        try {
            $journal = JournalEntry::with([
                'book',
                'journalEntryDetails.account',
                'outlet'
            ])->find($id);

            if (!$journal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurnal tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $journal,
                'message' => 'Data jurnal berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jurnal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeJournal(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'book_id' => 'required|exists:accounting_books,id',
                'transaction_date' => 'required|date',
                'description' => 'required|string|max:500',
                'entries' => 'required|array|min:2',
                'entries.*.account_id' => 'required|exists:chart_of_accounts,id',
                'entries.*.debit' => 'required_without:entries.*.credit|numeric|min:0',
                'entries.*.credit' => 'required_without:entries.*.debit|numeric|min:0',
                'entries.*.description' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'status' => 'required|in:draft,posted'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $book = AccountingBook::find($request->book_id);
            
            // Validasi: buku harus aktif dan tidak terkunci
            if ($book->status !== 'active' || $book->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku tidak aktif atau terkunci'
                ], 422);
            }

            // Generate transaction number
            $transactionNumber = JournalEntry::generateTransactionNumber($request->book_id);

            // Hitung total debit dan credit
            $totalDebit = 0;
            $totalCredit = 0;
            
            foreach ($request->entries as $entry) {
                $totalDebit += $entry['debit'] ?? 0;
                $totalCredit += $entry['credit'] ?? 0;
            }

            // Validasi: debit harus sama dengan credit untuk status posted
            if ($request->status === 'posted' && $totalDebit !== $totalCredit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total debit dan credit tidak balance'
                ], 422);
            }

            // Buat journal entry
            $journalEntry = JournalEntry::create([
                'book_id' => $request->book_id,
                'outlet_id' => $book->outlet_id,
                'transaction_number' => $transactionNumber,
                'transaction_date' => $request->transaction_date,
                'description' => $request->description,
                'status' => $request->status,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'notes' => $request->notes,
                'posted_at' => $request->status === 'posted' ? now() : null
            ]);

            // Buat journal entry details
            foreach ($request->entries as $entry) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                    'description' => $entry['description'] ?? null
                ]);

                // Update account balance jika status posted
                if ($request->status === 'posted') {
                    $account = ChartOfAccount::find($entry['account_id']);
                    if ($account) {
                        $balanceChange = ($entry['debit'] ?? 0) - ($entry['credit'] ?? 0);
                        $account->updateBalance($balanceChange);
                    }
                }
            }

            // Update book total entries jika status posted
            if ($request->status === 'posted') {
                $book->incrementEntries();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $journalEntry,
                'message' => 'Jurnal berhasil dibuat'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating journal: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat jurnal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function postJournal($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $journalEntry = JournalEntry::with('journalEntryDetails')->find($id);
            
            if (!$journalEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurnal tidak ditemukan'
                ], 404);
            }

            if ($journalEntry->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya jurnal dengan status draft yang dapat diposting'
                ], 422);
            }

            if ($journalEntry->total_debit !== $journalEntry->total_credit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurnal tidak balance, tidak dapat diposting'
                ], 422);
            }

            // Update account balances
            foreach ($journalEntry->journalEntryDetails as $detail) {
                $account = $detail->account;
                if ($account) {
                    $balanceChange = ($detail->debit ?? 0) - ($detail->credit ?? 0);
                    $account->updateBalance($balanceChange);
                }
            }

            // Update journal status
            $journalEntry->update([
                'status' => 'posted',
                'posted_at' => now()
            ]);

            // Update book total entries
            $journalEntry->book->incrementEntries();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $journalEntry,
                'message' => 'Jurnal berhasil diposting'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error posting journal: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memposting jurnal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteJournal($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $journalEntry = JournalEntry::with('journalEntryDetails')->find($id);
            
            if (!$journalEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurnal tidak ditemukan'
                ], 404);
            }

            // Validasi: hanya entry dengan status draft yang bisa dihapus
            if ($journalEntry->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya jurnal dengan status draft yang dapat dihapus'
                ], 422);
            }

            $transactionNumber = $journalEntry->transaction_number;
            $journalEntry->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Jurnal {$transactionNumber} berhasil dihapus"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting journal: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jurnal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete journal entry for superadmin (can delete posted journals and related opening balances)
     */
    public function deleteSuperadminJournal($id): JsonResponse
    {
        // Validasi: hanya superadmin yang bisa menggunakan method ini
        if (auth()->user()->role->name !== 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya superadmin yang dapat menghapus jurnal yang sudah diposting.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $journalEntry = JournalEntry::with('journalEntryDetails.account')->find($id);
            
            if (!$journalEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jurnal tidak ditemukan'
                ], 404);
            }

            $transactionNumber = $journalEntry->transaction_number;
            $description = $journalEntry->description;
            
            // Cek apakah jurnal ini berasal dari saldo awal
            $isOpeningBalance = false;
            if (stripos($description, 'saldo awal') !== false || 
                stripos($description, 'opening balance') !== false ||
                stripos($journalEntry->reference_type, 'opening_balance') !== false) {
                $isOpeningBalance = true;
            }

            // Jika jurnal berasal dari saldo awal, hapus juga data saldo awal terkait
            if ($isOpeningBalance) {
                // Hapus saldo awal berdasarkan akun-akun yang ada di jurnal entry details
                foreach ($journalEntry->journalEntryDetails as $detail) {
                    if ($detail->account_id) {
                        // Hapus saldo awal untuk akun ini di buku yang sama
                        $deletedCount = DB::table('opening_balances')
                            ->where('book_id', $journalEntry->book_id)
                            ->where('account_id', $detail->account_id)
                            ->delete();
                        
                        if ($deletedCount > 0) {
                            \Log::info("Deleted opening balance for account ID: {$detail->account_id}");
                        }
                    }
                }
                
                \Log::info("Deleted opening balances for journal: {$transactionNumber}");
            }

            // Hapus jurnal entry
            $journalEntry->delete();

            DB::commit();

            $message = "Jurnal {$transactionNumber} berhasil dihapus";
            if ($isOpeningBalance) {
                $message .= " beserta data saldo awal terkait";
            }

            \Log::info("Superadmin deleted journal: {$transactionNumber} by user: " . auth()->user()->name);

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting superadmin journal: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jurnal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getActiveBooks(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            
            $books = AccountingBook::where('outlet_id', $outletId)
                ->where('status', 'active')
                ->where('is_locked', false)
                ->orderBy('name')
                ->get(['id', 'code', 'name', 'type']);

            return response()->json([
                'success' => true,
                'data' => $books,
                'message' => 'Data buku aktif berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data buku aktif: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchAccounts(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $search = $request->get('search', '');

            $accounts = ChartOfAccount::where('outlet_id', $outletId)
                ->where('status', 'active')
                ->where(function($query) use ($search) {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                })
                ->orderBy('code')
                ->limit(10)
                ->get(['id', 'code', 'name', 'type']);

            return response()->json([
                'success' => true,
                'data' => $accounts,
                'message' => 'Data akun berhasil dicari'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari akun: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export journal data to XLSX format
     */
    public function exportJournalsXLSX(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id', 'all');
            $status = $request->get('status', 'all');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $search = $request->get('search', '');

            // Get journal data with details
            $journals = $this->getJournalExportData($outletId, $bookId, $status, $dateFrom, $dateTo, $search);

            // Get outlet info for filters
            $outlet = Outlet::find($outletId);
            
            // Prepare filters for export
            $filters = [
                'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'status' => $status,
                'book_id' => $bookId
            ];

            // Use FinanceExportService
            $exportService = new FinanceExportService();
            return $exportService->exportToXLSX('journal', $journals, $filters);

        } catch (\Exception $e) {
            \Log::error('Error exporting journal to XLSX: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export journal data to PDF format
     */
    public function exportJournalsPDF(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id', 'all');
            $status = $request->get('status', 'all');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $search = $request->get('search', '');

            // Get journal data with details
            $journals = $this->getJournalExportData($outletId, $bookId, $status, $dateFrom, $dateTo, $search);

            // Get outlet and company info for PDF header
            $outlet = Outlet::find($outletId);
            
            // Get book name if specific book is selected
            $bookName = null;
            if ($bookId !== 'all') {
                $book = AccountingBook::find($bookId);
                $bookName = $book->name ?? null;
            }
            
            // Prepare filters for PDF
            $filters = [
                'company_name' => config('app.name', 'Nama Perusahaan'),
                'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'status' => $status,
                'book_name' => $bookName
            ];

            // Use FinanceExportService
            $exportService = new FinanceExportService();
            return $exportService->exportToPDF('journal', $journals, $filters);

        } catch (\Exception $e) {
            \Log::error('Error exporting journal to PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get journal data for export (shared method for XLSX and PDF)
     */
    private function getJournalExportData($outletId, $bookId, $status, $dateFrom, $dateTo, $search)
    {
        $query = JournalEntry::with(['book', 'journalEntryDetails.account', 'outlet'])
            ->whereHas('book', function($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            })
            ->when($bookId !== 'all', function($q) use ($bookId) {
                $q->where('book_id', $bookId);
            })
            ->when($status !== 'all', function($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($dateFrom, function($q) use ($dateFrom) {
                $q->where('transaction_date', '>=', $dateFrom);
            })
            ->when($dateTo, function($q) use ($dateTo) {
                $q->where('transaction_date', '<=', $dateTo);
            })
            ->when($search, function($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('transaction_number', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('transaction_date', 'asc')
            ->orderBy('transaction_number', 'asc');

        // Get all journal entries with their details
        $journals = $query->get();

        // Flatten the data for export (one row per journal entry detail)
        $exportData = [];
        foreach ($journals as $journal) {
            foreach ($journal->journalEntryDetails as $detail) {
                $exportData[] = (object)[
                    'transaction_date' => $journal->transaction_date,
                    'transaction_number' => $journal->transaction_number,
                    'account_code' => $detail->account->code ?? '-',
                    'account_name' => $detail->account->name ?? '-',
                    'description' => $detail->description ?? $journal->description,
                    'debit' => floatval($detail->debit),
                    'credit' => floatval($detail->credit),
                    'outlet_name' => $journal->outlet->nama_outlet ?? '-',
                    'book_name' => $journal->book->name ?? '-',
                    'status' => $journal->status
                ];
            }
        }

        return $exportData;
    }

    /**
     * Import journal data from Excel file
     */
    public function importJournals(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:xlsx,xls|max:5120',
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()->all()
                ], 422);
            }

            $outletId = $request->outlet_id;
            $file = $request->file('file');

            // Get default book for this outlet
            $book = AccountingBook::where('outlet_id', $outletId)
                ->where('type', 'general')
                ->where('status', 'active')
                ->first();

            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada buku akuntansi aktif untuk outlet ini'
                ], 422);
            }

            // Use FinanceImportService
            $importService = new \App\Services\FinanceImportService();
            $result = $importService->import('journal', $file, [
                'outlet_id' => $outletId,
                'book_id' => $book->id
            ]);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Import berhasil',
                    'imported_count' => $result['imported_count'] ?? 0,
                    'skipped_count' => $result['skipped_count'] ?? 0,
                    'errors' => $result['errors'] ?? []
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Import gagal',
                    'errors' => $result['errors'] ?? []
                ], 422);
            }

        } catch (\Exception $e) {
            \Log::error('Error importing journals: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor data: ' . $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Download journal import template
     */
    public function downloadJournalsTemplate()
    {
        try {
            // Create a simple template with headers and sample data
            $templateData = [
                (object)[
                    'tanggal' => '2024-01-01',
                    'no_transaksi' => 'JNL-001',
                    'kode_akun' => '1000',
                    'deskripsi' => 'Contoh transaksi jurnal',
                    'debit' => 1000000,
                    'kredit' => 0,
                    'keterangan' => 'Contoh keterangan entri'
                ],
                (object)[
                    'tanggal' => '2024-01-01',
                    'no_transaksi' => 'JNL-001',
                    'kode_akun' => '2000',
                    'deskripsi' => 'Contoh transaksi jurnal',
                    'debit' => 0,
                    'kredit' => 1000000,
                    'keterangan' => 'Contoh keterangan entri'
                ]
            ];

            $export = new \App\Exports\JournalTemplateExport($templateData);
            return Excel::download($export, 'template_import_jurnal.xlsx');

        } catch (\Exception $e) {
            \Log::error('Error downloading journal template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export accounting books to XLSX format
     */
    public function exportAccountingBooksXLSX(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $type = $request->get('type', 'all');
            $status = $request->get('status', 'all');
            $search = $request->get('search', '');

            // Get accounting books data
            $books = $this->getAccountingBooksExportData($outletId, $type, $status, $search);

            // Get outlet info for filters
            $outlet = Outlet::find($outletId);
            
            // Prepare filters for export
            $filters = [
                'outlet' => $outlet->nama_outlet ?? 'Semua Outlet',
                'type' => $type,
                'status' => $status
            ];

            // Use FinanceExportService
            $exportService = new FinanceExportService();
            return $exportService->exportToXLSX('accounting-book', $books, $filters);

        } catch (\Exception $e) {
            \Log::error('Error exporting accounting books to XLSX: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export accounting books to PDF format
     */
    public function exportAccountingBooksPDF(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $type = $request->get('type', 'all');
            $status = $request->get('status', 'all');
            $search = $request->get('search', '');

            // Get accounting books data
            $books = $this->getAccountingBooksExportData($outletId, $type, $status, $search);

            // Get outlet info for PDF header
            $outlet = Outlet::find($outletId);
            
            // Calculate summary
            $summary = [
                'total_books' => $books->count(),
                'active_books' => $books->where('status', 'active')->count(),
                'total_entries' => $books->sum('total_entries'),
                'total_opening_balance' => $books->sum('opening_balance'),
                'total_closing_balance' => $books->sum('closing_balance')
            ];
            
            // Prepare data for PDF
            $data = $books;
            $filters = [
                'outlet' => $outlet->nama_outlet ?? 'Semua Outlet',
                'type' => $type !== 'all' ? $this->getTypeName($type) : 'Semua Tipe',
                'status' => $status !== 'all' ? $this->getStatusName($status) : 'Semua Status'
            ];

            // Company info
            $companyName = config('app.name', 'PT. NAMA PERUSAHAAN');
            $companyAddress = config('app.address', 'Alamat Perusahaan');
            $companyPhone = config('app.phone', '-');
            $companyEmail = config('app.email', '-');

            // Use FinanceExportService with additional data
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.finance.buku.pdf', compact(
                'data', 
                'filters', 
                'summary',
                'companyName',
                'companyAddress',
                'companyPhone',
                'companyEmail'
            ))->setPaper('a4', 'landscape');
            
            $filename = 'accounting_books_export_' . now()->format('Y-m-d_His') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Error exporting accounting books to PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get accounting books data for export (shared method for XLSX and PDF)
     */
    private function getAccountingBooksExportData($outletId, $type, $status, $search)
    {
        $query = AccountingBook::with(['outlet'])
            ->where('outlet_id', $outletId)
            ->when($type !== 'all', function($q) use ($type) {
                $q->where('type', $type);
            })
            ->when($status !== 'all', function($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($search, function($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('code', 'asc');

        return $query->get();
    }

    /**
     * Get type name in Indonesian
     */
    private function getTypeName($type): string
    {
        $types = [
            'general' => 'Umum',
            'cash' => 'Kas',
            'bank' => 'Bank',
            'sales' => 'Penjualan',
            'purchase' => 'Pembelian',
            'inventory' => 'Persediaan',
            'payroll' => 'Penggajian'
        ];

        return $types[$type] ?? $type;
    }

    /**
     * Get status name in Indonesian
     */
    private function getStatusName($status): string
    {
        $statuses = [
            'active' => 'Aktif',
            'inactive' => 'Nonaktif',
            'draft' => 'Draft',
            'closed' => 'Ditutup'
        ];

        return $statuses[$status] ?? $status;
    }

    private function calculateAccumulatedBalance($accountId, $outletId): float
    {
        $balance = JournalEntryDetail::whereHas('journalEntry', function($query) use ($outletId) {
                $query->where('outlet_id', $outletId)
                    ->where('status', 'posted');
            })
            ->where('account_id', $accountId)
            ->selectRaw('SUM(debit - credit) as balance')
            ->value('balance');

        return floatval($balance ?? 0);
    }

    private function getAccountTypeName($type): string
    {
        $typeNames = [
            'asset' => 'Aset',
            'liability' => 'Kewajiban',
            'equity' => 'Ekuitas',
            'revenue' => 'Pendapatan',
            'expense' => 'Beban',
            'otherrevenue' => 'Pendapatan Lain',
            'otherexpense' => 'Beban Lain'
        ];
        
        return $typeNames[$type] ?? ucfirst($type);
    }

    private function calculateAccumulatedBalanceWithChildren($account, $outletId): float
    {
        // Hitung saldo akun ini sendiri
        $balance = $this->calculateAccumulatedBalance($account->id, $outletId);
        
        // Normalisasi saldo berdasarkan tipe akun
        $normalizedBalance = $this->normalizeBalanceByType($balance, $account->type);
        
        // Jika akun memiliki children, tambahkan saldo children
        if ($account->children && $account->children->count() > 0) {
            foreach ($account->children as $child) {
                $childBalance = $this->calculateAccumulatedBalanceWithChildren($child, $outletId);
                $normalizedBalance += $childBalance;
            }
        }
        
        return $normalizedBalance;
    }

    /**
     * Normalisasi saldo berdasarkan tipe akun sesuai prinsip akuntansi
     */
    private function normalizeBalanceByType(float $balance, string $type): float
    {
        switch ($type) {
            case 'asset':
            case 'expense':
            case 'otherexpense':
                // Aset dan Beban: Debit positif, Kredit negatif
                // Saldo normal seharusnya positif (Debit balance)
                return $balance;
                
            case 'liability':
            case 'equity':
            case 'revenue':
            case 'otherrevenue':
                // Kewajiban, Ekuitas, Pendapatan: Debit negatif, Kredit positif  
                // Saldo normal seharusnya positif (Credit balance)
                // Untuk konsistensi display, balik nilainya
                return -$balance;
                
            default:
                return $balance;
        }
    }

    /**
     * Get account transaction details for modal (used by Trial Balance, etc)
     */
    public function getAccountTransactionDetailsForModal(Request $request): JsonResponse
    {
        try {
            $accountId = $request->get('account_id');
            $outletId = $request->get('outlet_id');
            $bookId = $request->get('book_id');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            if (!$accountId || !$outletId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account ID dan Outlet ID diperlukan'
                ], 422);
            }
            
            $account = ChartOfAccount::find($accountId);
            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }
            
            // Get transactions
            $query = JournalEntryDetail::with(['journalEntry.book'])
                ->whereHas('journalEntry', function($q) use ($outletId, $bookId, $startDate, $endDate) {
                    $q->where('outlet_id', $outletId)
                      ->where('status', 'posted');
                    
                    if ($bookId) {
                        $q->where('book_id', $bookId);
                    }
                    
                    if ($startDate && $endDate) {
                        $q->whereBetween('transaction_date', [$startDate, $endDate]);
                    }
                })
                ->where('account_id', $accountId)
                ->orderBy('created_at', 'desc');
            
            $transactions = $query->get()->map(function($detail) {
                return [
                    'id' => $detail->id,
                    'transaction_date' => $detail->journalEntry->transaction_date->format('Y-m-d'),
                    'transaction_number' => $detail->journalEntry->transaction_number,
                    'description' => $detail->description ?: $detail->journalEntry->description,
                    'book_name' => $detail->journalEntry->book->name ?? '-',
                    'debit' => floatval($detail->debit),
                    'credit' => floatval($detail->credit)
                ];
            });
            
            // Calculate summary
            $totalDebit = $transactions->sum('debit');
            $totalCredit = $transactions->sum('credit');
            $currentBalance = $totalDebit - $totalCredit;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'account' => [
                        'id' => $account->id,
                        'code' => $account->code,
                        'name' => $account->name,
                        'type' => $account->type
                    ],
                    'transactions' => $transactions,
                    'summary' => [
                        'transaction_count' => $transactions->count(),
                        'total_debit' => $totalDebit,
                        'total_credit' => $totalCredit,
                        'current_balance' => $currentBalance
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting account transaction details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get account balance details for modal view
     */
    public function getAccountBalanceDetails($id): JsonResponse
    {
        try {
            $account = ChartOfAccount::with(['children', 'outlet'])->find($id);
            
            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }

            $outletId = $account->outlet_id;
            
            // Get journal entries for this account
            $journalEntries = JournalEntryDetail::with(['journalEntry.book'])
                ->whereHas('journalEntry', function($query) use ($outletId) {
                    $query->where('outlet_id', $outletId)
                        ->where('status', 'posted');
                })
                ->where('account_id', $id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($detail) {
                    return [
                        'date' => $detail->journalEntry->transaction_date->format('d/m/Y'),
                        'transaction_number' => $detail->journalEntry->transaction_number,
                        'description' => $detail->description ?: $detail->journalEntry->description,
                        'debit' => floatval($detail->debit),
                        'credit' => floatval($detail->credit),
                        'book_name' => $detail->journalEntry->book->name,
                        'balance_change' => floatval($detail->debit) - floatval($detail->credit)
                    ];
                });

            // Calculate current balance from journal entries
            $currentBalance = $this->calculateAccumulatedBalance($id, $outletId);
            
            // Calculate opening balance (saldo awal sebelum periode berjalan)
            $openingBalance = $account->balance; // Anda bisa menyesuaikan ini dengan logika saldo awal
            
            // Jika akun parent, hitung saldo termasuk children
            $accumulatedBalance = $this->calculateAccumulatedBalanceWithChildren($account, $outletId);

            return response()->json([
                'success' => true,
                'data' => [
                    'account' => [
                        'id' => $account->id,
                        'code' => $account->code,
                        'name' => $account->name,
                        'type' => $account->type,
                        'level' => $account->level,
                        'has_children' => $account->children && $account->children->count() > 0
                    ],
                    'balances' => [
                        'opening_balance' => $openingBalance,
                        'current_balance' => $currentBalance,
                        'accumulated_balance' => $accumulatedBalance,
                        'final_balance' => $accumulatedBalance
                    ],
                    'journal_entries' => $journalEntries,
                    'summary' => [
                        'total_debit' => $journalEntries->sum('debit'),
                        'total_credit' => $journalEntries->sum('credit'),
                        'entry_count' => $journalEntries->count()
                    ]
                ],
                'message' => 'Detail saldo akun berhasil diambil'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting account balance details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail saldo akun: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
 * Get account transactions for a period - FIXED WITH RELATIONSHIPS
 */
private function getAccountPeriodTransactions($accountId, $outletId, $startDate, $endDate)
{
    return JournalEntryDetail::with([
            'journalEntry.book', 
            'account'
        ])
        ->whereHas('journalEntry', function($query) use ($outletId, $startDate, $endDate) {
            $query->where('outlet_id', $outletId)
                ->where('status', 'posted')
                ->whereBetween('transaction_date', [$startDate, $endDate]);
        })
        ->where('account_id', $accountId)
        ->orderBy('created_at')
        ->get();
}


/**
 * Calculate children balances for parent account
 */
private function calculateChildrenBalances($parentAccount, $outletId, $startDate, $endDate, $level): array
{
    $openingBalance = 0;
    $totalDebit = 0;
    $totalCredit = 0;
    $endingBalance = 0;

    foreach ($parentAccount->children as $child) {
        $childLedger = $this->calculateAccountLedger($child, $outletId, $startDate, $endDate, $level);
        if ($childLedger) {
            $openingBalance += $childLedger['opening_balance'];
            $totalDebit += $childLedger['total_debit'];
            $totalCredit += $childLedger['total_credit'];
            $endingBalance += $childLedger['ending_balance'];
        }
    }

    return [
        'opening_balance' => $openingBalance,
        'total_debit' => $totalDebit,
        'total_credit' => $totalCredit,
        'ending_balance' => $endingBalance
    ];
}


/**
 * Get account transactions in period
 */
private function getAccountTransactionsInPeriod($accountId, $outletId, $startDate, $endDate)
{
    return JournalEntryDetail::with(['journalEntry.book'])
        ->whereHas('journalEntry', function($query) use ($outletId, $startDate, $endDate) {
            $query->where('outlet_id', $outletId)
                  ->where('status', 'posted')
                  ->whereBetween('transaction_date', [$startDate, $endDate]);
        })
        ->where('account_id', $accountId)
        ->select('debit', 'credit', 'description', 'journal_entry_id')
        ->get();
}

/**
 * Get detailed transaction list for an account
 */
private function getAccountTransactionDetails($accountId, $outletId, $startDate, $endDate): array
{
    $transactions = JournalEntryDetail::with([
        'journalEntry.book',
        'journalEntry.journalEntryDetails.account'
    ])
    ->whereHas('journalEntry', function($query) use ($outletId, $startDate, $endDate) {
        $query->where('outlet_id', $outletId)
              ->where('status', 'posted')
              ->whereBetween('transaction_date', [$startDate, $endDate]);
    })
    ->where('account_id', $accountId)
    ->orderBy('journal_entries.transaction_date', 'asc')
    ->orderBy('journal_entries.created_at', 'asc')
    ->get();

    $runningBalance = $this->calculateAccountBalanceUntilDate($accountId, $outletId, $startDate);
    $transactionDetails = [];

    // Add opening balance as first entry
    $transactionDetails[] = [
        'type' => 'opening_balance',
        'date' => $startDate,
        'reference' => 'SALDO-AWAL',
        'description' => 'Saldo Awal Periode',
        'debit' => $runningBalance > 0 ? $runningBalance : 0,
        'credit' => $runningBalance < 0 ? abs($runningBalance) : 0,
        'balance' => $runningBalance,
        'journal_id' => null,
        'journal_type' => 'Saldo Awal'
    ];

    foreach ($transactions as $transaction) {
        $balanceChange = $transaction->debit - $transaction->credit;
        $runningBalance += $balanceChange;

        // Get other accounts in this journal entry for description
        $otherEntries = $transaction->journalEntry->journalEntryDetails
            ->where('account_id', '!=', $accountId)
            ->take(2); // Limit to 2 other accounts for brevity

        $description = $transaction->description ?: $transaction->journalEntry->description;
        if ($otherEntries->count() > 0) {
            $otherAccounts = $otherEntries->map(function($entry) {
                return $entry->account->code . ' ' . $entry->account->name;
            })->implode(', ');
            
            $description .= ' (' . $otherAccounts . ')';
        }

        $transactionDetails[] = [
            'type' => 'transaction',
            'date' => $transaction->journalEntry->transaction_date->format('Y-m-d'),
            'date_formatted' => $transaction->journalEntry->transaction_date->translatedFormat('d M Y'),
            'reference' => $transaction->journalEntry->transaction_number,
            'description' => $description,
            'debit' => floatval($transaction->debit),
            'credit' => floatval($transaction->credit),
            'balance' => $runningBalance,
            'journal_id' => $transaction->journalEntry->id,
            'journal_type' => $transaction->journalEntry->book->name,
            'book_id' => $transaction->journalEntry->book_id
        ];
    }

    return $transactionDetails;
}

/**
 * Calculate date range based on period
 */
private function calculateDateRange($period): array
{
    $today = now();
    
    switch ($period) {
        case 'quarterly':
            $startDate = $today->copy()->startOfQuarter()->format('Y-m-d');
            $endDate = $today->copy()->endOfQuarter()->format('Y-m-d');
            break;
        case 'yearly':
            $startDate = $today->copy()->startOfYear()->format('Y-m-d');
            $endDate = $today->copy()->endOfYear()->format('Y-m-d');
            break;
        case 'monthly':
        default:
            $startDate = $today->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $today->copy()->endOfMonth()->format('Y-m-d');
            break;
    }

    return [
        'start_date' => $startDate,
        'end_date' => $endDate,
        'period' => $period
    ];
}

/**
 * Calculate ledger statistics
 */
private function calculateLedgerStats($outletId, $startDate, $endDate): array
{
    // Total transactions in period
    $totalTransactions = JournalEntry::where('outlet_id', $outletId)
        ->where('status', 'posted')
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->count();

    // Total debit and credit
    $totals = JournalEntry::where('outlet_id', $outletId)
        ->where('status', 'posted')
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->selectRaw('SUM(total_debit) as total_debit, SUM(total_credit) as total_credit')
        ->first();

    // Active accounts (with transactions in period)
    $activeAccounts = JournalEntryDetail::whereHas('journalEntry', function($query) use ($outletId, $startDate, $endDate) {
            $query->where('outlet_id', $outletId)
                  ->where('status', 'posted')
                  ->whereBetween('transaction_date', [$startDate, $endDate]);
        })
        ->distinct('account_id')
        ->count('account_id');

    // Top accounts by transaction count
    $topAccounts = JournalEntryDetail::whereHas('journalEntry', function($query) use ($outletId, $startDate, $endDate) {
            $query->where('outlet_id', $outletId)
                  ->where('status', 'posted')
                  ->whereBetween('transaction_date', [$startDate, $endDate]);
        })
        ->with('account')
        ->select('account_id')
        ->selectRaw('COUNT(*) as transaction_count, SUM(debit + credit) as total_amount')
        ->groupBy('account_id')
        ->orderByDesc('transaction_count')
        ->limit(5)
        ->get()
        ->map(function($item) {
            return [
                'id' => $item->account_id,
                'code' => $item->account->code ?? '-',
                'name' => $item->account->name ?? 'Unknown Account',
                'transaction_count' => $item->transaction_count,
                'total_amount' => floatval($item->total_amount)
            ];
        });

    return [
        'total_transactions' => $totalTransactions,
        'total_debit' => floatval($totals->total_debit ?? 0),
        'total_credit' => floatval($totals->total_credit ?? 0),
        'active_accounts' => $activeAccounts,
        'top_accounts' => $topAccounts,
        'period' => [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]
    ];
}

public function getActiveAccounts(Request $request): JsonResponse
{
    try {
        $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
        $type = $request->get('type'); // asset, liability, equity, revenue, expense
        $excludeContra = $request->get('exclude_contra', false);
        $contraOnly = $request->get('contra', false);
        $category = $request->get('category'); // current, non_current, etc.
        
        $query = ChartOfAccount::where('outlet_id', $outletId)
            ->where('status', 'active');
        
        // Filter by type
        if ($type) {
            $query->where('type', $type);
        }
        
        // Filter by category
        if ($category) {
            $query->where('category', $category);
        }
        
        // Get all accounts
        $allAccounts = $query->orderBy('code')->get(['id', 'code', 'name', 'type', 'parent_id', 'category']);
        
        // Detect contra accounts by name pattern (akumulasi, penyisihan, cadangan, etc.)
        $contraKeywords = ['akumulasi', 'penyisihan', 'cadangan', 'allowance', 'accumulated', 'provision'];
        
        $allAccounts = $allAccounts->map(function($account) use ($contraKeywords) {
            $nameLower = strtolower($account->name);
            $isContra = false;
            
            foreach ($contraKeywords as $keyword) {
                if (strpos($nameLower, $keyword) !== false) {
                    $isContra = true;
                    break;
                }
            }
            
            $account->is_contra = $isContra;
            return $account;
        });
        
        // Filter contra accounts if requested
        if ($excludeContra) {
            $allAccounts = $allAccounts->filter(function($account) {
                return !$account->is_contra;
            });
        }
        
        if ($contraOnly) {
            $allAccounts = $allAccounts->filter(function($account) {
                return $account->is_contra;
            });
        }
        
        // Get all parent IDs that have children
        $parentIds = $allAccounts->pluck('parent_id')->filter()->unique()->toArray();
        
        // Filter to only leaf accounts (accounts that are not parents)
        $leafAccounts = $allAccounts->filter(function($account) use ($parentIds) {
            return !in_array($account->id, $parentIds);
        });
        
        $accounts = $leafAccounts->map(function($account) {
            return [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'is_contra' => $account->is_contra,
                'category' => $account->category,
                'display_name' => $account->code . ' - ' . $account->name
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $accounts,
            'message' => 'Data akun aktif berhasil diambil'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error getting active accounts: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data akun aktif: ' . $e->getMessage()
        ], 500);
    }
}

public function generalLedgerData(Request $request): JsonResponse
{
    try {
        $validator = Validator::make($request->all(), [
            'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'account_id' => 'nullable|exists:chart_of_accounts,id',
            'level' => 'nullable|in:summary,detail,odoo-style'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $outletId = $request->outlet_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $accountId = $request->account_id;
        $level = $request->level ?? 'odoo-style';

        // Get ledger data in Odoo style format
        $ledgerData = $this->calculateOdooStyleLedger($outletId, $startDate, $endDate, $accountId);

        return response()->json([
            'success' => true,
            'data' => $ledgerData,
            'message' => 'Data buku besar berhasil diambil'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error getting general ledger data: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data buku besar: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Calculate Odoo-style General Ledger
 */
private function calculateOdooStyleLedger($outletId, $startDate, $endDate, $accountId = null): array
{
    // Get accounts with their opening balances
    $accountsQuery = ChartOfAccount::with(['children'])
        ->where('outlet_id', $outletId)
        ->where('status', 'active')
        ->orderBy('code');

    if ($accountId) {
        $accountsQuery->where('id', $accountId);
    }

    $accounts = $accountsQuery->get();
    $ledgerEntries = [];
    $totalDebit = 0;
    $totalCredit = 0;

    foreach ($accounts as $account) {
        $accountEntries = $this->getAccountOdooStyleEntries($account, $outletId, $startDate, $endDate);
        
        if (!empty($accountEntries['transactions']) || $accountEntries['opening_balance'] != 0) {
            $ledgerEntries[] = $accountEntries;
            $totalDebit += $accountEntries['total_debit'];
            $totalCredit += $accountEntries['total_credit'];
        }
    }

    return [
        'ledger_entries' => $ledgerEntries,
        'summary' => [
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'balance' => $totalDebit - $totalCredit,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]
    ];
}

private function getAccountOdooStyleEntries($account, $outletId, $startDate, $endDate): array
{
    // Calculate opening balance
    $openingBalance = $this->calculateAccountBalanceUntilDate($account->id, $outletId, $startDate);
    
    // Get transactions in period - FIXED QUERY
    $transactions = JournalEntryDetail::with([
        'journalEntry.book',
        'journalEntry.journalEntryDetails.account'
    ])
    ->whereHas('journalEntry', function($query) use ($outletId, $startDate, $endDate) {
        $query->where('outlet_id', $outletId)
              ->where('status', 'posted')
              ->whereBetween('transaction_date', [$startDate, $endDate]);
    })
    ->where('account_id', $account->id)
    ->join('journal_entries', 'journal_entry_details.journal_entry_id', '=', 'journal_entries.id')
    ->orderBy('journal_entries.transaction_date', 'asc')
    ->orderBy('journal_entries.created_at', 'asc')
    ->select('journal_entry_details.*') // Select all columns from journal_entry_details
    ->get();

    $runningBalance = $openingBalance;
    $accountTotalDebit = 0;
    $accountTotalCredit = 0;

    $formattedTransactions = [];

    foreach ($transactions as $transaction) {
        $balanceChange = $transaction->debit - $transaction->credit;
        $runningBalance += $balanceChange;
        
        $accountTotalDebit += $transaction->debit;
        $accountTotalCredit += $transaction->credit;

        // Get counterpart accounts for description
        $counterpartAccounts = $transaction->journalEntry->journalEntryDetails
            ->where('account_id', '!=', $account->id)
            ->map(function($entry) {
                return $entry->account->code . ' - ' . $entry->account->name;
            })
            ->implode(', ');

        $description = $transaction->description ?: $transaction->journalEntry->description;
        if ($counterpartAccounts) {
            $description .= ' (' . $counterpartAccounts . ')';
        }

        $formattedTransactions[] = [
            'type' => 'transaction',
            'date' => $transaction->journalEntry->transaction_date->format('Y-m-d'),
            'date_formatted' => $transaction->journalEntry->transaction_date->translatedFormat('d/m/Y'),
            'reference' => $transaction->journalEntry->transaction_number,
            'description' => $description,
            'debit' => floatval($transaction->debit),
            'credit' => floatval($transaction->credit),
            'balance' => $runningBalance,
            'journal_id' => $transaction->journalEntry->id,
            'journal_entry_id' => $transaction->id,
            'book_name' => $transaction->journalEntry->book->name,
            'has_children' => false
        ];
    }

    return [
        'account_id' => $account->id,
        'account_code' => $account->code,
        'account_name' => $account->name,
        'account_type' => $account->type,
        'level' => $account->level,
        'has_children' => $account->children && $account->children->count() > 0,
        'opening_balance' => $openingBalance,
        'total_debit' => $accountTotalDebit,
        'total_credit' => $accountTotalCredit,
        'ending_balance' => $runningBalance,
        'transactions' => $formattedTransactions,
        'transaction_count' => count($formattedTransactions)
    ];
}

/**
 * Calculate account balance until specific date - FIXED VERSION
 */
private function calculateAccountBalanceUntilDate($accountId, $outletId, $untilDate): float
{
    $balance = JournalEntryDetail::whereHas('journalEntry', function($query) use ($outletId, $untilDate) {
            $query->where('outlet_id', $outletId)
                  ->where('status', 'posted')
                  ->where('transaction_date', '<', $untilDate);
        })
        ->where('account_id', $accountId)
        ->selectRaw('SUM(debit - credit) as balance')
        ->value('balance');

    return floatval($balance ?? 0);
}



/**
 * Get account balance for ledger display (normalized by type)
 */
private function getAccountBalanceForLedger($balance, $accountType): array
{
    $normalizedBalance = $this->normalizeBalanceByType($balance, $accountType);
    
    return [
        'raw' => $balance,
        'normalized' => $normalizedBalance,
        'display' => $this->formatBalanceForDisplay($normalizedBalance),
        'is_positive' => $normalizedBalance >= 0
    ];
}

/**
 * Format balance for display
 */
private function formatBalanceForDisplay($balance): string
{
    return 'Rp ' . number_format(abs($balance), 2, ',', '.');
}

public function openingBalanceData(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);
            $search = $request->get('search', '');
            $outletId = $request->get('outlet_id');
            $bookId = $request->get('book_id');
            $type = $request->get('type', 'all');
            $status = $request->get('status', 'all');
            $effectiveDate = $request->get('effective_date');

            $query = OpeningBalance::with(['account', 'outlet', 'accountingBook'])
                ->select('opening_balances.*')
                ->join('chart_of_accounts', 'opening_balances.account_id', '=', 'chart_of_accounts.id')
                ->when($outletId, function($q) use ($outletId) {
                    $q->where('opening_balances.outlet_id', $outletId);
                })
                ->when($bookId, function($q) use ($bookId) {
                    $q->where('opening_balances.book_id', $bookId);
                })
                ->when($type !== 'all', function($q) use ($type) {
                    $q->where('chart_of_accounts.type', $type);
                })
                ->when($status !== 'all', function($q) use ($status) {
                    if ($status === 'balanced') {
                        $q->whereRaw('opening_balances.debit = opening_balances.credit');
                    } elseif ($status === 'unbalanced') {
                        $q->whereRaw('opening_balances.debit != opening_balances.credit');
                    } else {
                        $q->where('opening_balances.status', $status);
                    }
                })
                ->when($effectiveDate, function($q) use ($effectiveDate) {
                    $q->where('opening_balances.effective_date', $effectiveDate);
                })
                ->when($search, function($q) use ($search) {
                    $q->where(function($query) use ($search) {
                        $query->where('chart_of_accounts.code', 'like', "%{$search}%")
                            ->orWhere('chart_of_accounts.name', 'like', "%{$search}%")
                            ->orWhere('opening_balances.description', 'like', "%{$search}%");
                    });
                })
                ->orderBy('chart_of_accounts.code')
                ->orderBy('opening_balances.effective_date', 'desc');

            $openingBalances = $query->paginate($perPage, ['*'], 'page', $page);

            // Hitung statistik
            $stats = $this->getOpeningBalanceStats($outletId, $bookId, $effectiveDate);

            return response()->json([
                'success' => true,
                'data' => $openingBalances->items(),
                'meta' => [
                    'current_page' => $openingBalances->currentPage(),
                    'per_page' => $openingBalances->perPage(),
                    'total' => $openingBalances->total(),
                    'last_page' => $openingBalances->lastPage(),
                ],
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching opening balance data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch opening balance data'
            ], 500);
        }
    }

    /**
     * Get opening balance statistics
     */
    private function getOpeningBalanceStats($outletId = null, $bookId = null, $effectiveDate = null): array
    {
        // ✅ Remove status filter to show all balances
        $query = OpeningBalance::query();

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }

        if ($bookId) {
            $query->where('book_id', $bookId);
        }

        if ($effectiveDate) {
            $query->where('effective_date', $effectiveDate);
        }

        $totalDebit = $query->sum('debit');
        $totalCredit = $query->sum('credit');
        $balance = $totalDebit - $totalCredit;

        $accountsWithBalance = $query->distinct('account_id')->count('account_id');
        $totalAccounts = ChartOfAccount::active()->when($outletId, function($q) use ($outletId) {
            $q->where('outlet_id', $outletId);
        })->count();

        return [
            'total_debit' => (float) $totalDebit,
            'total_credit' => (float) $totalCredit,
            'balance' => (float) $balance,
            'total_accounts' => $totalAccounts,
            'accounts_with_balance' => $accountsWithBalance,
            'balanced_accounts' => (clone $query)->whereRaw('debit = credit')->count(),
            'unbalanced_accounts' => (clone $query)->whereRaw('debit != credit')->count(),
            'period' => date('Y')
        ];
    }

    /**
     * Store new opening balance
     */
    public function storeOpeningBalance(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'book_id' => 'required|exists:accounting_books,id',
                'account_id' => 'required|exists:chart_of_accounts,id',
                'debit' => 'required_without:credit|numeric|min:0',
                'credit' => 'required_without:debit|numeric|min:0',
                'effective_date' => 'required|date',
                'description' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek apakah sudah ada saldo untuk akun, outlet, book, dan tanggal yang sama
            $existingBalance = OpeningBalance::where('outlet_id', $request->outlet_id)
                ->where('book_id', $request->book_id)
                ->where('account_id', $request->account_id)
                ->where('effective_date', $request->effective_date)
                ->first();

            if ($existingBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo awal untuk akun ini sudah ada pada tanggal yang sama'
                ], 409);
            }

            // Extract year and month from effective_date
            $effectiveDate = \Carbon\Carbon::parse($request->effective_date);
            
            $openingBalance = new OpeningBalance();
            $openingBalance->outlet_id = $request->outlet_id;
            $openingBalance->book_id = $request->book_id;
            $openingBalance->account_id = $request->account_id;
            $openingBalance->period_year = $effectiveDate->year;
            $openingBalance->period_month = $effectiveDate->month;
            $openingBalance->debit = $request->debit ?? 0;
            $openingBalance->credit = $request->credit ?? 0;
            $openingBalance->balance = ($request->debit ?? 0) - ($request->credit ?? 0);
            $openingBalance->effective_date = $request->effective_date;
            $openingBalance->description = $request->description;
            $openingBalance->status = 'active';
            $openingBalance->created_by = auth()->id();
            $openingBalance->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Saldo awal berhasil disimpan',
                'data' => $openingBalance->load(['account', 'outlet', 'accountingBook'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing opening balance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to store opening balance'
            ], 500);
        }
    }

    /**
     * Update opening balance
     */
    public function updateOpeningBalance(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $openingBalance = OpeningBalance::find($id);
            
            if (!$openingBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo awal tidak ditemukan'
                ], 404);
            }

            // Jika sudah diposting, tidak bisa diubah
            if ($openingBalance->status === 'posted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo awal yang sudah diposting tidak dapat diubah'
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'debit' => 'required_without:credit|numeric|min:0',
                'credit' => 'required_without:debit|numeric|min:0',
                'effective_date' => 'required|date',
                'description' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Extract year and month from effective_date if changed
            $effectiveDate = \Carbon\Carbon::parse($request->effective_date);
            
            $openingBalance->period_year = $effectiveDate->year;
            $openingBalance->period_month = $effectiveDate->month;
            $openingBalance->debit = $request->debit ?? 0;
            $openingBalance->credit = $request->credit ?? 0;
            $openingBalance->balance = ($request->debit ?? 0) - ($request->credit ?? 0);
            $openingBalance->effective_date = $request->effective_date;
            $openingBalance->description = $request->description;
            $openingBalance->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Saldo awal berhasil diupdate',
                'data' => $openingBalance->load(['account', 'outlet', 'accountingBook'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating opening balance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update opening balance'
            ], 500);
        }
    }

    /**
     * Delete opening balance
     */
    public function deleteOpeningBalance($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $openingBalance = OpeningBalance::find($id);
            
            if (!$openingBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo awal tidak ditemukan'
                ], 404);
            }

            // Jika sudah diposting, tidak bisa dihapus
            if ($openingBalance->status === 'posted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo awal yang sudah diposting tidak dapat dihapus'
                ], 400);
            }

            $openingBalance->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Saldo awal berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting opening balance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete opening balance'
            ], 500);
        }
    }

    /**
     * Validate opening balances
     */
    public function validateOpeningBalances(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id');
            $bookId = $request->get('book_id');
            $effectiveDate = $request->get('effective_date', date('Y-m-d'));

            $validation = OpeningBalance::validateTotalBalance($outletId, $bookId, $effectiveDate);

            return response()->json([
                'success' => true,
                'data' => $validation,
                'message' => $validation['is_balanced'] ? 
                    'Saldo awal seimbang' : 
                    'Saldo awal tidak seimbang. Selisih: ' . number_format($validation['difference'], 2)
            ]);

        } catch (\Exception $e) {
            \Log::error('Error validating opening balances: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate opening balances'
            ], 500);
        }
    }

    /**
     * Post opening balances to journal
     */
    public function postOpeningBalances(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $outletId = $request->get('outlet_id');
            $bookId = $request->get('book_id');
            $effectiveDate = $request->get('effective_date', date('Y-m-d'));

            // Validasi saldo terlebih dahulu
            $validation = OpeningBalance::validateTotalBalance($outletId, $bookId, $effectiveDate);
            
            if (!$validation['is_balanced']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo awal tidak seimbang. Tidak dapat memposting ke jurnal. Selisih: ' . number_format($validation['difference'], 2)
                ], 400);
            }

            // Ambil semua saldo awal yang aktif
            $openingBalances = OpeningBalance::where('outlet_id', $outletId)
                ->where('book_id', $bookId)
                ->where('status', 'active')
                ->get();

            \Log::info('Posting opening balances', [
                'outlet_id' => $outletId,
                'book_id' => $bookId,
                'effective_date' => $effectiveDate,
                'found_balances' => $openingBalances->count()
            ]);

            if ($openingBalances->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada saldo awal yang perlu diposting'
                ], 400);
            }

            $postedCount = 0;
            $skippedCount = 0;
            $errors = [];

            foreach ($openingBalances as $balance) {
                // Skip if both debit and credit are zero
                if ($balance->debit == 0 && $balance->credit == 0) {
                    $skippedCount++;
                    continue;
                }

                if ($balance->postToJournal()) {
                    $postedCount++;
                } else {
                    $errors[] = 'Gagal memposting saldo untuk akun: ' . $balance->account->code . ' - ' . $balance->account->name;
                }
            }

            if (!empty($errors)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa saldo gagal diposting',
                    'errors' => $errors
                ], 400);
            }

            DB::commit();

            $message = 'Berhasil memposting ' . $postedCount . ' saldo awal ke jurnal';
            if ($skippedCount > 0) {
                $message .= ' (' . $skippedCount . ' saldo dengan nilai 0 dilewati)';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'posted_count' => $postedCount,
                'skipped_count' => $skippedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error posting opening balances: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to post opening balances'
            ], 500);
        }
    }

    /**
     * Get available accounts for opening balance
     */
    public function getAccountsForOpeningBalance(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id');
            $search = $request->get('search', '');

            $query = ChartOfAccount::active()
                ->when($outletId, function($q) use ($outletId) {
                    $q->where('outlet_id', $outletId);
                })
                ->when($search, function($q) use ($search) {
                    $q->where(function($query) use ($search) {
                        $query->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
                })
                ->orderBy('code')
                ->limit(50);

            $accounts = $query->get(['id', 'code', 'name', 'type']);

            return response()->json([
                'success' => true,
                'data' => $accounts
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching accounts for opening balance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch accounts'
            ], 500);
        }
    }

    // ==================== FIXED ASSETS MANAGEMENT ====================

    /**
     * Get fixed assets data with pagination and filters
     */
    public function fixedAssetsData(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id', '');
            $category = $request->get('category', 'all');
            $status = $request->get('status', 'all');
            $search = $request->get('search', '');

            $query = \App\Models\FixedAsset::with([
                'outlet',
                'book',
                'assetAccount',
                'depreciationExpenseAccount',
                'accumulatedDepreciationAccount',
                'paymentAccount'
            ])
            ->byOutlet($outletId)
            ->when($bookId, function($q) use ($bookId) {
                $q->where('book_id', $bookId);
            })
            ->when($category !== 'all', function($q) use ($category) {
                $q->where('category', $category);
            })
            ->when($status !== 'all', function($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($search, function($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc');

            $assets = $query->paginate($perPage, ['*'], 'page', $page);

            // Calculate statistics
            $stats = $this->calculateFixedAssetStats($outletId);

            // Format data
            $formattedAssets = $assets->getCollection()->map(function($asset) {
                // Get real-time accumulated depreciation and book value including draft entries
                $realTimeAccumulatedDepreciation = $asset->getRealTimeAccumulatedDepreciation();
                $realTimeBookValue = $asset->getRealTimeBookValue();
                
                return [
                    'id' => $asset->id,
                    'code' => $asset->code,
                    'name' => $asset->name,
                    'category' => $asset->category,
                    'location' => $asset->location,
                    'acquisition_date' => $asset->acquisition_date->format('Y-m-d'),
                    'acquisition_date_formatted' => $asset->acquisition_date->translatedFormat('d M Y'),
                    'acquisition_cost' => floatval($asset->acquisition_cost),
                    'salvage_value' => floatval($asset->salvage_value),
                    'useful_life' => $asset->useful_life,
                    'depreciation_method' => $asset->depreciation_method,
                    'accumulated_depreciation' => floatval($realTimeAccumulatedDepreciation),
                    'book_value' => floatval($realTimeBookValue),
                    'status' => $asset->status,
                    'monthly_depreciation' => $asset->calculateMonthlyDepreciation(),
                    'remaining_life' => round($asset->calculateRemainingLife(), 2),
                    'depreciation_progress' => $asset->acquisition_cost > 0 
                        ? round(($realTimeAccumulatedDepreciation / $asset->acquisition_cost) * 100, 2) 
                        : 0,
                    'outlet_name' => $asset->outlet->nama_outlet ?? '-',
                    'asset_account_name' => $asset->assetAccount->name ?? '-',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedAssets,
                'stats' => $stats,
                'meta' => [
                    'current_page' => $assets->currentPage(),
                    'per_page' => $assets->perPage(),
                    'total' => $assets->total(),
                    'last_page' => $assets->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching fixed assets data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data aktiva tetap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate fixed asset statistics
     */
    private function calculateFixedAssetStats($outletId): array
    {
        $totalAssets = \App\Models\FixedAsset::byOutlet($outletId)->count();
        $activeAssets = \App\Models\FixedAsset::byOutlet($outletId)->active()->count();
        
        // Get all assets for this outlet to calculate real-time totals
        $assets = \App\Models\FixedAsset::byOutlet($outletId)->get();
        
        $totalAcquisitionCost = 0;
        $totalDepreciation = 0;
        $totalBookValue = 0;
        
        foreach ($assets as $asset) {
            $totalAcquisitionCost += $asset->acquisition_cost;
            $realTimeAccumulatedDepreciation = $asset->getRealTimeAccumulatedDepreciation();
            $totalDepreciation += $realTimeAccumulatedDepreciation;
            $totalBookValue += ($asset->acquisition_cost - $realTimeAccumulatedDepreciation);
        }
        
        $depreciationRate = $totalAcquisitionCost > 0 
            ? round(($totalDepreciation / $totalAcquisitionCost) * 100, 2) 
            : 0;

        return [
            'totalAssets' => $totalAssets,
            'activeAssets' => $activeAssets,
            'totalAcquisitionCost' => floatval($totalAcquisitionCost),
            'totalDepreciation' => floatval($totalDepreciation),
            'totalBookValue' => floatval($totalBookValue),
            'depreciationRate' => $depreciationRate
        ];
    }

    /**
     * Store new fixed asset with automatic journal entry
     */
    public function storeFixedAsset(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'book_id' => 'nullable|exists:accounting_books,id',
                'code' => 'required|string|max:50|unique:fixed_assets,code',
                'name' => 'required|string|max:255',
                'category' => 'required|in:land,building,vehicle,equipment,furniture,computer,mesin',
                'location' => 'nullable|string|max:255',
                'acquisition_date' => 'required|date|before_or_equal:today',
                'acquisition_cost' => 'required|numeric|min:0',
                'salvage_value' => 'required|numeric|min:0',
                'useful_life' => 'required|integer|min:1',
                'depreciation_method' => 'required|in:straight_line,declining_balance,double_declining,units_of_production',
                'asset_account_id' => 'required|exists:chart_of_accounts,id',
                'depreciation_expense_account_id' => 'required|exists:chart_of_accounts,id',
                'accumulated_depreciation_account_id' => 'required|exists:chart_of_accounts,id',
                'payment_account_id' => 'required|exists:chart_of_accounts,id',
                'description' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validate salvage value < acquisition cost
            if ($request->salvage_value >= $request->acquisition_cost) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nilai residu harus lebih kecil dari nilai perolehan'
                ], 422);
            }

            // Validate account types
            $assetAccount = ChartOfAccount::find($request->asset_account_id);
            $depreciationExpenseAccount = ChartOfAccount::find($request->depreciation_expense_account_id);
            $accumulatedDepreciationAccount = ChartOfAccount::find($request->accumulated_depreciation_account_id);
            $paymentAccount = ChartOfAccount::find($request->payment_account_id);

            if ($assetAccount->type !== 'asset') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun aset harus memiliki tipe "asset"'
                ], 422);
            }

            if ($depreciationExpenseAccount->type !== 'expense') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun beban penyusutan harus memiliki tipe "expense"'
                ], 422);
            }

            if ($accumulatedDepreciationAccount->type !== 'asset') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun akumulasi penyusutan harus memiliki tipe "asset"'
                ], 422);
            }

            // Validate outlet_id matches accounts
            if ($assetAccount->outlet_id != $request->outlet_id ||
                $depreciationExpenseAccount->outlet_id != $request->outlet_id ||
                $accumulatedDepreciationAccount->outlet_id != $request->outlet_id ||
                $paymentAccount->outlet_id != $request->outlet_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semua akun harus berada di outlet yang sama'
                ], 422);
            }

            // Create fixed asset with draft status
            $fixedAsset = \App\Models\FixedAsset::create([
                'outlet_id' => $request->outlet_id,
                'book_id' => $request->book_id, // Save book_id from request
                'code' => $request->code,
                'name' => $request->name,
                'category' => $request->category,
                'location' => $request->location,
                'acquisition_date' => $request->acquisition_date,
                'acquisition_cost' => $request->acquisition_cost,
                'salvage_value' => $request->salvage_value,
                'useful_life' => $request->useful_life,
                'depreciation_method' => $request->depreciation_method,
                'asset_account_id' => $request->asset_account_id,
                'depreciation_expense_account_id' => $request->depreciation_expense_account_id,
                'accumulated_depreciation_account_id' => $request->accumulated_depreciation_account_id,
                'payment_account_id' => $request->payment_account_id,
                'accumulated_depreciation' => 0,
                'book_value' => $request->acquisition_cost,
                'status' => 'draft', // Set as draft initially
                'description' => $request->description,
                'created_by' => auth()->id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $fixedAsset->load(['outlet', 'book', 'assetAccount', 'paymentAccount']),
                'message' => 'Aktiva tetap berhasil dibuat dengan status draft. Silakan approve untuk posting jurnal.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating fixed asset: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat aktiva tetap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update fixed asset data
     */
    public function updateFixedAsset(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $fixedAsset = \App\Models\FixedAsset::find($id);
            
            if (!$fixedAsset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktiva tetap tidak ditemukan'
                ], 404);
            }

            // Validate: cannot update if has posted depreciation (except for draft status)
            $hasPostedDepreciation = $fixedAsset->depreciations()
                ->where('status', 'posted')
                ->exists();

            if ($hasPostedDepreciation && $fixedAsset->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengubah aset yang sudah memiliki penyusutan terposting'
                ], 422);
            }

            // Different validation rules based on asset status
            $validationRules = [
                'book_id' => 'nullable|exists:accounting_books,id',
                'name' => 'required|string|max:255',
                'category' => 'required|in:land,building,vehicle,equipment,furniture,computer,mesin',
                'location' => 'nullable|string|max:255',
                'salvage_value' => 'required|numeric|min:0',
                'useful_life' => 'required|integer|min:1',
                'depreciation_method' => 'required|in:straight_line,declining_balance,double_declining,units_of_production',
                'description' => 'nullable|string'
            ];

            // For draft assets, allow updating acquisition cost and accounts
            if ($fixedAsset->status === 'draft') {
                $validationRules['acquisition_cost'] = 'required|numeric|min:0';
                $validationRules['asset_account_id'] = 'nullable|exists:chart_of_accounts,id';
                $validationRules['depreciation_expense_account_id'] = 'nullable|exists:chart_of_accounts,id';
                $validationRules['accumulated_depreciation_account_id'] = 'nullable|exists:chart_of_accounts,id';
                $validationRules['payment_account_id'] = 'nullable|exists:chart_of_accounts,id';
            }

            $validator = Validator::make($request->all(), $validationRules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validate salvage value < acquisition cost
            $acquisitionCost = $request->has('acquisition_cost') ? $request->acquisition_cost : $fixedAsset->acquisition_cost;
            if ($request->salvage_value >= $acquisitionCost) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nilai residu harus lebih kecil dari nilai perolehan'
                ], 422);
            }

            // Prepare update data based on asset status
            $updateData = [
                'book_id' => $request->book_id,
                'name' => $request->name,
                'category' => $request->category,
                'location' => $request->location,
                'salvage_value' => $request->salvage_value,
                'useful_life' => $request->useful_life,
                'depreciation_method' => $request->depreciation_method,
                'description' => $request->description
            ];

            // For draft assets, allow updating acquisition cost and accounts
            if ($fixedAsset->status === 'draft') {
                if ($request->has('acquisition_cost')) {
                    $updateData['acquisition_cost'] = $request->acquisition_cost;
                }
                if ($request->has('asset_account_id')) {
                    $updateData['asset_account_id'] = $request->asset_account_id;
                }
                if ($request->has('depreciation_expense_account_id')) {
                    $updateData['depreciation_expense_account_id'] = $request->depreciation_expense_account_id;
                }
                if ($request->has('accumulated_depreciation_account_id')) {
                    $updateData['accumulated_depreciation_account_id'] = $request->accumulated_depreciation_account_id;
                }
                if ($request->has('payment_account_id')) {
                    $updateData['payment_account_id'] = $request->payment_account_id;
                }
            }

            // Update fixed asset
            $fixedAsset->update($updateData);

            // Recalculate book value if acquisition cost or salvage value changed
            if ($request->has('acquisition_cost') || $request->has('salvage_value')) {
                $fixedAsset->book_value = $fixedAsset->acquisition_cost - $fixedAsset->accumulated_depreciation;
                $fixedAsset->save();
            }

            DB::commit();

            // Reload with relationships for complete data
            $fixedAsset->load(['outlet', 'book', 'assetAccount', 'depreciationExpenseAccount', 'accumulatedDepreciationAccount', 'paymentAccount']);

            return response()->json([
                'success' => true,
                'data' => $fixedAsset,
                'message' => 'Aktiva tetap berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating fixed asset: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui aktiva tetap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete fixed asset
     */
    public function deleteFixedAsset($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $fixedAsset = \App\Models\FixedAsset::with('depreciations')->find($id);
            
            if (!$fixedAsset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktiva tetap tidak ditemukan'
                ], 404);
            }

            // Validate: cannot delete if has posted journal entries
            if (!$fixedAsset->canBeDeleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus aset yang sudah memiliki jurnal terposting'
                ], 422);
            }

            // Validate: cannot delete if has depreciation records
            if ($fixedAsset->depreciations()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus aset yang sudah memiliki catatan penyusutan'
                ], 422);
            }

            $assetCode = $fixedAsset->code;
            $assetName = $fixedAsset->name;
            
            $fixedAsset->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Aktiva tetap {$assetCode} - {$assetName} berhasil dihapus"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting fixed asset: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus aktiva tetap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle fixed asset status (active/inactive)
     */
    public function toggleFixedAsset($id): JsonResponse
    {
        try {
            $fixedAsset = \App\Models\FixedAsset::find($id);
            
            if (!$fixedAsset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktiva tetap tidak ditemukan'
                ], 404);
            }

            $newStatus = $fixedAsset->status === 'active' ? 'inactive' : 'active';
            $fixedAsset->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'data' => $fixedAsset,
                'message' => 'Status aktiva tetap berhasil diubah'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error toggling fixed asset: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status aktiva tetap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show fixed asset detail
     */
    public function showFixedAsset($id): JsonResponse
    {
        try {
            $fixedAsset = \App\Models\FixedAsset::with([
                'outlet',
                'assetAccount',
                'depreciationExpenseAccount',
                'accumulatedDepreciationAccount',
                'paymentAccount',
                'depreciations.journalEntry',
                'creator'
            ])->find($id);
            
            if (!$fixedAsset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktiva tetap tidak ditemukan'
                ], 404);
            }

            // Get related journal entries
            $journalEntries = JournalEntry::where('reference_type', 'fixed_asset_acquisition')
                ->where('reference_number', $fixedAsset->code)
                ->orWhere('reference_type', 'fixed_asset_depreciation')
                ->where('reference_number', 'like', $fixedAsset->code . '%')
                ->orWhere('reference_type', 'fixed_asset_disposal')
                ->where('reference_number', $fixedAsset->code)
                ->with('journalEntryDetails.account')
                ->orderBy('transaction_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'asset' => $fixedAsset,
                    'journal_entries' => $journalEntries,
                    'depreciation_history' => $fixedAsset->depreciations
                ],
                'message' => 'Data aktiva tetap berhasil diambil'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error showing fixed asset: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data aktiva tetap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique asset code
     */
    public function generateAssetCode(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id');

            if (!$outletId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlet ID diperlukan'
                ], 422);
            }

            $code = \App\Models\FixedAsset::generateCode($outletId);

            return response()->json([
                'success' => true,
                'data' => ['code' => $code],
                'message' => 'Kode aset berhasil digenerate'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating asset code: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate kode aset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate depreciation for a specific period
     */
    public function calculateDepreciation(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'period_month' => 'required|integer|min:1|max:12',
                'period_year' => 'required|integer|min:2000|max:2100',
                'asset_ids' => 'nullable|array',
                'asset_ids.*' => 'exists:fixed_assets,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $outletId = $request->outlet_id;
            $periodMonth = $request->period_month;
            $periodYear = $request->period_year;
            $assetIds = $request->asset_ids;

            // Create target period date (last day of the month)
            $targetPeriodDate = \Carbon\Carbon::create($periodYear, $periodMonth, 1)->endOfMonth();

            // Get all active fixed assets for outlet
            $query = FixedAsset::byOutlet($outletId)
                ->active()
                ->where('book_value', '>', DB::raw('salvage_value'));

            // Filter by specific asset IDs if provided
            if ($assetIds && count($assetIds) > 0) {
                $query->whereIn('id', $assetIds);
            }

            $assets = $query->get();

            $processedCount = 0;
            $totalDepreciationAmount = 0;
            $createdDepreciations = [];

            foreach ($assets as $asset) {
                // Get acquisition date
                $acquisitionDate = \Carbon\Carbon::parse($asset->acquisition_date);
                
                // Skip if acquisition date is after target period
                if ($acquisitionDate->year > $periodYear || 
                    ($acquisitionDate->year == $periodYear && $acquisitionDate->month > $periodMonth)) {
                    continue;
                }

                // Calculate monthly depreciation amount
                $monthlyDepreciationAmount = $asset->calculateMonthlyDepreciation();

                if ($monthlyDepreciationAmount <= 0) {
                    continue;
                }

                // Get existing depreciation records for this asset
                $existingDepreciations = FixedAssetDepreciation::where('fixed_asset_id', $asset->id)
                    ->orderBy('depreciation_date', 'asc')
                    ->get();

                // Create a set of existing depreciation dates for quick lookup
                $existingDates = $existingDepreciations->pluck('depreciation_date')
                    ->map(function($date) {
                        return \Carbon\Carbon::parse($date)->format('Y-m');
                    })->toArray();

                // Calculate all missing periods from acquisition date to target period
                $currentDate = $acquisitionDate->copy()->addMonth()->startOfMonth(); // Start from month after acquisition
                $period = 1;
                $assetProcessed = false;

                // Get the last period number from existing depreciations
                $lastPeriod = $existingDepreciations->max('period') ?? 0;

                while ($currentDate->lte($targetPeriodDate)) {
                    $currentPeriodKey = $currentDate->format('Y-m');
                    
                    // Check if depreciation for this month already exists
                    if (in_array($currentPeriodKey, $existingDates)) {
                        $currentDate->addMonth();
                        continue;
                    }

                    // Calculate depreciation for this period
                    $depreciationDate = $currentDate->copy()->endOfMonth();
                    
                    // Get current accumulated depreciation
                    $currentAccumulatedDepreciation = $asset->accumulated_depreciation;
                    
                    // If there are existing depreciations, use the latest accumulated value
                    if ($existingDepreciations->isNotEmpty()) {
                        $latestDepreciation = $existingDepreciations->sortByDesc('depreciation_date')->first();
                        $currentAccumulatedDepreciation = $latestDepreciation->accumulated_depreciation ?? $asset->accumulated_depreciation;
                    }

                    // Calculate new values
                    $depreciationAmount = $monthlyDepreciationAmount;
                    $newAccumulatedDepreciation = $currentAccumulatedDepreciation + $depreciationAmount;
                    $newBookValue = $asset->acquisition_cost - $newAccumulatedDepreciation;

                    // Ensure book value doesn't go below salvage value
                    if ($newBookValue < $asset->salvage_value) {
                        $depreciationAmount = ($asset->acquisition_cost - $currentAccumulatedDepreciation) - $asset->salvage_value;
                        $newAccumulatedDepreciation = $asset->acquisition_cost - $asset->salvage_value;
                        $newBookValue = $asset->salvage_value;
                        
                        // If depreciation amount is zero or negative, skip
                        if ($depreciationAmount <= 0) {
                            break;
                        }
                    }

                    // Increment period number
                    $lastPeriod++;

                    // Create depreciation record with status 'draft'
                    $depreciation = FixedAssetDepreciation::create([
                        'fixed_asset_id' => $asset->id,
                        'period' => $lastPeriod,
                        'depreciation_date' => $depreciationDate,
                        'amount' => $depreciationAmount,
                        'accumulated_depreciation' => $newAccumulatedDepreciation,
                        'book_value' => $newBookValue,
                        'status' => 'draft',
                        'notes' => 'Penyusutan periode ' . $currentDate->format('M Y'),
                        'created_by' => auth()->id()
                    ]);

                    $totalDepreciationAmount += $depreciationAmount;
                    $createdDepreciations[] = [
                        'id' => $depreciation->id,
                        'asset_code' => $asset->code,
                        'asset_name' => $asset->name,
                        'amount' => $depreciationAmount,
                        'period' => $lastPeriod,
                        'depreciation_date' => $depreciationDate->format('Y-m-d'),
                        'period_display' => $currentDate->format('M Y')
                    ];

                    $assetProcessed = true;

                    // Stop if book value reached salvage value
                    if ($newBookValue <= $asset->salvage_value) {
                        break;
                    }

                    $currentDate->addMonth();
                }

                if ($assetProcessed) {
                    $processedCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'processed_count' => $processedCount,
                    'total_depreciation_amount' => $totalDepreciationAmount,
                    'total_depreciation_entries' => count($createdDepreciations),
                    'depreciations' => $createdDepreciations
                ],
                'message' => "Berhasil menghitung penyusutan untuk {$processedCount} aset dengan " . count($createdDepreciations) . " entri penyusutan"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error calculating depreciation: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung penyusutan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch process depreciation for all active assets
     */
    public function batchDepreciation(Request $request): JsonResponse
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'period_month' => 'required|integer|min:1|max:12',
                'period_year' => 'required|integer|min:2000|max:2100',
                'auto_post' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $outletId = $request->outlet_id;
            $periodMonth = $request->period_month;
            $periodYear = $request->period_year;
            $autoPost = $request->get('auto_post', false);

            // Call calculateDepreciation for all active assets
            $calculateRequest = new Request([
                'outlet_id' => $outletId,
                'period_month' => $periodMonth,
                'period_year' => $periodYear
            ]);

            $calculateResponse = $this->calculateDepreciation($calculateRequest);
            $calculateData = json_decode($calculateResponse->getContent(), true);

            if (!$calculateData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghitung penyusutan: ' . $calculateData['message']
                ], 500);
            }

            $totalAssetsProcessed = $calculateData['data']['processed_count'];
            $totalDepreciationAmount = $calculateData['data']['total_depreciation_amount'];
            $totalDepreciationEntries = $calculateData['data']['total_depreciation_entries'];
            $totalJournalsCreated = 0;
            $errors = [];

            // If auto_post is true, post each depreciation
            if ($autoPost) {
                $depreciations = $calculateData['data']['depreciations'];

                foreach ($depreciations as $depreciation) {
                    try {
                        $postResponse = $this->postDepreciation($depreciation['id']);
                        $postData = json_decode($postResponse->getContent(), true);
                        if ($postData['success']) {
                            $totalJournalsCreated++;
                        } else {
                            $errors[] = [
                                'asset_code' => $depreciation['asset_code'],
                                'period' => $depreciation['period_display'] ?? $depreciation['depreciation_date'],
                                'error' => $postData['message']
                            ];
                        }
                    } catch (\Exception $e) {
                        $errors[] = [
                            'asset_code' => $depreciation['asset_code'],
                            'period' => $depreciation['period_display'] ?? $depreciation['depreciation_date'],
                            'error' => $e->getMessage()
                        ];
                    }
                }
            }

            // Create detailed message
            $message = "Batch processing selesai: {$totalAssetsProcessed} aset diproses";
            if ($totalDepreciationEntries > 0) {
                $message .= ", {$totalDepreciationEntries} entri penyusutan dibuat";
            }
            if ($autoPost && $totalJournalsCreated > 0) {
                $message .= ", {$totalJournalsCreated} jurnal diposting";
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_assets_processed' => $totalAssetsProcessed,
                    'total_depreciation_entries' => $totalDepreciationEntries,
                    'total_journals_created' => $totalJournalsCreated,
                    'total_depreciation_amount' => $totalDepreciationAmount,
                    'errors' => $errors,
                    'auto_post_enabled' => $autoPost
                ],
                'message' => $message
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in batch depreciation: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan batch processing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Post depreciation and create journal entry
     */
    public function postDepreciation(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'book_id' => 'required|exists:accounting_books,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find FixedAssetDepreciation with relationship
            $depreciation = FixedAssetDepreciation::with('fixedAsset')->find($id);
            
            if (!$depreciation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyusutan tidak ditemukan'
                ], 404);
            }

            // Validate status is 'draft'
            if ($depreciation->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya penyusutan dengan status draft yang dapat diposting'
                ], 422);
            }

            // Validate no journal_entry_id
            if ($depreciation->journal_entry_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyusutan sudah memiliki jurnal'
                ], 422);
            }

            $fixedAsset = $depreciation->fixedAsset;

            // Get selected accounting book
            $book = AccountingBook::where('id', $request->book_id)
                ->where('outlet_id', $fixedAsset->outlet_id)
                ->where('status', 'active')
                ->first();

            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku akuntansi tidak ditemukan atau tidak aktif untuk outlet ini'
                ], 422);
            }

            // Validate user has access to this book
            $this->validateOutletAccess($fixedAsset->outlet_id);

            // Generate transaction number
            $transactionNumber = 'FA-DEP-' . $fixedAsset->code . '-' . $depreciation->period;

            // Create journal entry for depreciation
            $journalEntry = JournalEntry::create([
                'book_id' => $book->id,
                'outlet_id' => $fixedAsset->outlet_id,
                'transaction_number' => $transactionNumber,
                'transaction_date' => $depreciation->depreciation_date,
                'description' => 'Penyusutan Aktiva Tetap - ' . $fixedAsset->name . ' - Periode ' . $depreciation->period,
                'reference_type' => 'fixed_asset_depreciation',
                'reference_number' => $fixedAsset->code . '-' . $depreciation->period,
                'status' => 'posted',
                'total_debit' => $depreciation->amount,
                'total_credit' => $depreciation->amount,
                'posted_at' => now()
            ]);

            // Create journal entry details
            // Debit: Depreciation Expense Account
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $fixedAsset->depreciation_expense_account_id,
                'debit' => $depreciation->amount,
                'credit' => 0,
                'description' => 'Beban penyusutan ' . $fixedAsset->name . ' periode ' . $depreciation->period
            ]);

            // Credit: Accumulated Depreciation Account
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $fixedAsset->accumulated_depreciation_account_id,
                'debit' => 0,
                'credit' => $depreciation->amount,
                'description' => 'Akumulasi penyusutan ' . $fixedAsset->name . ' periode ' . $depreciation->period
            ]);

            // Update FixedAssetDepreciation
            $depreciation->update([
                'journal_entry_id' => $journalEntry->id,
                'status' => 'posted'
            ]);

            // Update FixedAsset
            $fixedAsset->increment('accumulated_depreciation', $depreciation->amount);
            $fixedAsset->decrement('book_value', $depreciation->amount);

            // Update account balances
            $depreciationExpenseAccount = ChartOfAccount::find($fixedAsset->depreciation_expense_account_id);
            $accumulatedDepreciationAccount = ChartOfAccount::find($fixedAsset->accumulated_depreciation_account_id);

            $depreciationExpenseAccount->updateBalance($depreciation->amount);
            $accumulatedDepreciationAccount->updateBalance($depreciation->amount);

            // Update book total entries
            $book->incrementEntries();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'depreciation' => $depreciation->load('journalEntry'),
                    'fixed_asset' => $fixedAsset->fresh(),
                    'journal_entry' => $journalEntry->load('journalEntryDetails.account')
                ],
                'message' => 'Penyusutan berhasil diposting dan jurnal telah dibuat'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error posting depreciation: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memposting penyusutan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reverse posted depreciation
     */
    public function reverseDepreciation($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Find FixedAssetDepreciation
            $depreciation = FixedAssetDepreciation::with('fixedAsset', 'journalEntry')->find($id);
            
            if (!$depreciation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyusutan tidak ditemukan'
                ], 404);
            }

            // Validate status is 'posted'
            if ($depreciation->status !== 'posted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya penyusutan yang sudah diposting yang dapat di-reverse'
                ], 422);
            }

            // Validate has journal_entry_id
            if (!$depreciation->journal_entry_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyusutan tidak memiliki jurnal'
                ], 422);
            }

            $fixedAsset = $depreciation->fixedAsset;
            $originalJournal = $depreciation->journalEntry;

            // Get accounting book
            $book = AccountingBook::where('outlet_id', $fixedAsset->outlet_id)
                ->where('type', 'general')
                ->where('status', 'active')
                ->first();

            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada buku akuntansi aktif untuk outlet ini'
                ], 422);
            }

            // Generate transaction number for reversal
            $transactionNumber = 'FA-DEP-REV-' . $fixedAsset->code . '-' . $depreciation->period;

            // Create reversal journal entry (reverse debit-credit)
            $reversalJournal = JournalEntry::create([
                'book_id' => $book->id,
                'outlet_id' => $fixedAsset->outlet_id,
                'transaction_number' => $transactionNumber,
                'transaction_date' => now()->format('Y-m-d'),
                'description' => 'Pembalikan Penyusutan Aktiva Tetap - ' . $fixedAsset->name . ' - Periode ' . $depreciation->period,
                'reference_type' => 'fixed_asset_depreciation_reversal',
                'reference_number' => $fixedAsset->code . '-' . $depreciation->period,
                'status' => 'posted',
                'total_debit' => $depreciation->amount,
                'total_credit' => $depreciation->amount,
                'posted_at' => now()
            ]);

            // Create reversal journal entry details (reverse of original)
            // Debit: Accumulated Depreciation Account (was credit)
            JournalEntryDetail::create([
                'journal_entry_id' => $reversalJournal->id,
                'account_id' => $fixedAsset->accumulated_depreciation_account_id,
                'debit' => $depreciation->amount,
                'credit' => 0,
                'description' => 'Pembalikan akumulasi penyusutan ' . $fixedAsset->name . ' periode ' . $depreciation->period
            ]);

            // Credit: Depreciation Expense Account (was debit)
            JournalEntryDetail::create([
                'journal_entry_id' => $reversalJournal->id,
                'account_id' => $fixedAsset->depreciation_expense_account_id,
                'debit' => 0,
                'credit' => $depreciation->amount,
                'description' => 'Pembalikan beban penyusutan ' . $fixedAsset->name . ' periode ' . $depreciation->period
            ]);

            // Update FixedAssetDepreciation status
            $depreciation->update([
                'status' => 'reversed'
            ]);

            // Update FixedAsset (reverse the depreciation)
            $fixedAsset->decrement('accumulated_depreciation', $depreciation->amount);
            $fixedAsset->increment('book_value', $depreciation->amount);

            // Update account balances (reverse)
            $depreciationExpenseAccount = ChartOfAccount::find($fixedAsset->depreciation_expense_account_id);
            $accumulatedDepreciationAccount = ChartOfAccount::find($fixedAsset->accumulated_depreciation_account_id);

            $depreciationExpenseAccount->updateBalance(-$depreciation->amount);
            $accumulatedDepreciationAccount->updateBalance(-$depreciation->amount);

            // Update book total entries
            $book->incrementEntries();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'depreciation' => $depreciation->fresh(),
                    'fixed_asset' => $fixedAsset->fresh(),
                    'reversal_journal' => $reversalJournal->load('journalEntryDetails.account')
                ],
                'message' => 'Penyusutan berhasil di-reverse dan jurnal pembalik telah dibuat'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error reversing depreciation: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal me-reverse penyusutan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get depreciation history data with filters
     */
    public function depreciationHistoryData(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 100); // Increased default from 10 to 100
            $page = $request->get('page', 1);
            $assetId = $request->get('asset_id');
            $month = $request->get('month');
            $year = $request->get('year');
            $status = $request->get('status', 'all');
            $outletId = $request->get('outlet_id');

            // If no outlet_id provided, use auth user's outlet or get all
            if (!$outletId) {
                $outletId = auth()->user()->outlet_id ?? null;
            }

            $query = FixedAssetDepreciation::with([
                'fixedAsset.outlet',
                'journalEntry'
            ])
            ->when($outletId, function($q) use ($outletId) {
                $q->whereHas('fixedAsset', function($subQ) use ($outletId) {
                    $subQ->where('outlet_id', $outletId);
                });
            })
            ->when($assetId, function($q) use ($assetId) {
                $q->where('fixed_asset_id', $assetId);
            })
            ->when($month, function($q) use ($month) {
                $q->whereMonth('depreciation_date', $month);
            })
            ->when($year, function($q) use ($year) {
                $q->whereYear('depreciation_date', $year);
            })
            ->when($status !== 'all', function($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderBy('depreciation_date', 'asc') // Changed to ASC for cumulative calculation
            ->orderBy('created_at', 'asc');

            // Get total count for logging
            $totalCount = $query->count();
            \Log::info("Depreciation history query - Total records: {$totalCount}, Outlet: {$outletId}, Asset: {$assetId}, Month: {$month}, Year: {$year}");

            $depreciations = $query->paginate($perPage, ['*'], 'page', $page);

            // Calculate cumulative accumulated depreciation for each asset
            $assetAccumulations = [];
            
            // Format data for frontend with cumulative calculation
            $formattedData = $depreciations->getCollection()->map(function($depreciation) use (&$assetAccumulations) {
                $assetId = $depreciation->fixed_asset_id;
                
                // Initialize accumulation for this asset if not exists
                if (!isset($assetAccumulations[$assetId])) {
                    $assetAccumulations[$assetId] = 0;
                }
                
                // Add current depreciation to cumulative total
                $assetAccumulations[$assetId] += $depreciation->amount;
                
                return [
                    'id' => $depreciation->id,
                    'date' => $depreciation->depreciation_date->format('Y-m-d'),
                    'date_formatted' => $depreciation->depreciation_date->translatedFormat('d M Y'),
                    'asset_id' => $depreciation->fixed_asset_id,
                    'asset_code' => $depreciation->fixedAsset->code ?? '-',
                    'asset_name' => $depreciation->fixedAsset->name ?? '-',
                    'period' => $depreciation->period,
                    'amount' => floatval($depreciation->amount),
                    'accumulated' => floatval($assetAccumulations[$assetId]), // Cumulative calculation
                    'book_value' => floatval(($depreciation->fixedAsset->acquisition_cost ?? 0) - $assetAccumulations[$assetId]),
                    'status' => $depreciation->status,
                    'status_label' => $this->getDepreciationStatusLabel($depreciation->status),
                    'journal_number' => $depreciation->journalEntry->transaction_number ?? '-',
                    'journal_id' => $depreciation->journal_entry_id,
                    'can_post' => $depreciation->canBePosted(),
                    'can_reverse' => $depreciation->canBeReversed()
                ];
            });

            // Reverse the order for display (newest first) but keep cumulative calculation correct
            $formattedData = $formattedData->reverse()->values();

            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'meta' => [
                    'current_page' => $depreciations->currentPage(),
                    'per_page' => $depreciations->perPage(),
                    'total' => $depreciations->total(),
                    'last_page' => $depreciations->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching depreciation history: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat penyusutan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get depreciation status label
     */
    private function getDepreciationStatusLabel($status): string
    {
        $labels = [
            'draft' => 'Draft',
            'posted' => 'Posted',
            'reversed' => 'Reversed'
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get all fixed assets for dropdown filter (not paginated)
     */
    public function getAllFixedAssets(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            
            $assets = \App\Models\FixedAsset::byOutlet($outletId)
                ->select('id', 'code', 'name', 'status')
                ->orderBy('name', 'asc')
                ->get();

            $formattedAssets = $assets->map(function($asset) {
                return [
                    'id' => $asset->id,
                    'code' => $asset->code,
                    'name' => $asset->name,
                    'display_name' => "{$asset->code} - {$asset->name}",
                    'status' => $asset->status
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedAssets
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching all fixed assets: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar aktiva tetap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate disposal gain or loss
     * Helper method for asset disposal
     */
    private function calculateDisposalGainLoss($disposalValue, $bookValue): array
    {
        $gainLoss = $disposalValue - $bookValue;
        
        return [
            'gain_loss' => $gainLoss,
            'type' => $gainLoss >= 0 ? 'gain' : 'loss',
            'amount' => abs($gainLoss)
        ];
    }

    /**
     * Dispose fixed asset and create journal entry
     */
    public function disposeAsset(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'disposal_date' => 'required|date|before_or_equal:today',
                'disposal_value' => 'required|numeric|min:0',
                'disposal_notes' => 'nullable|string',
                'disposal_type' => 'required|in:sold,disposed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find FixedAsset by ID
            $fixedAsset = FixedAsset::with([
                'outlet',
                'assetAccount',
                'accumulatedDepreciationAccount',
                'paymentAccount'
            ])->find($id);

            if (!$fixedAsset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktiva tetap tidak ditemukan'
                ], 404);
            }

            // Validate status is 'active'
            if ($fixedAsset->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya aktiva tetap dengan status active yang dapat dilepas'
                ], 422);
            }

            // Calculate gain/loss
            $disposalCalculation = $this->calculateDisposalGainLoss(
                $request->disposal_value,
                $fixedAsset->book_value
            );

            // Get accounting book - prioritize the book used by the fixed asset
            $book = null;
            
            // First try to use the same book as the fixed asset
            if ($fixedAsset->book_id) {
                $book = AccountingBook::where('id', $fixedAsset->book_id)
                    ->where('status', 'active')
                    ->first();
            }
            
            // If not found, try to find any active book for the outlet
            if (!$book) {
                $book = AccountingBook::where('outlet_id', $fixedAsset->outlet_id)
                    ->where('status', 'active')
                    ->first();
            }

            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada buku akuntansi aktif untuk outlet ini'
                ], 422);
            }

            // Generate transaction number
            $transactionNumber = 'FA-DSP-' . $fixedAsset->code;

            // Create journal entry for disposal
            $journalEntry = JournalEntry::create([
                'book_id' => $book->id,
                'outlet_id' => $fixedAsset->outlet_id,
                'transaction_number' => $transactionNumber,
                'transaction_date' => $request->disposal_date,
                'description' => 'Pelepasan Aktiva Tetap - ' . $fixedAsset->name,
                'reference_type' => 'fixed_asset_disposal',
                'reference_number' => $fixedAsset->code,
                'status' => 'posted',
                'total_debit' => $request->disposal_value + $fixedAsset->accumulated_depreciation + ($disposalCalculation['type'] === 'loss' ? $disposalCalculation['amount'] : 0),
                'total_credit' => $fixedAsset->acquisition_cost + ($disposalCalculation['type'] === 'gain' ? $disposalCalculation['amount'] : 0)
            ]);

            // Journal Entry Details:
            
            // 1. Debit: Payment Account (disposal_value)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $fixedAsset->payment_account_id,
                'debit' => $request->disposal_value,
                'credit' => 0,
                'description' => 'Penerimaan dari pelepasan ' . $fixedAsset->name
            ]);

            // 2. Debit: Accumulated Depreciation Account (accumulated_depreciation)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $fixedAsset->accumulated_depreciation_account_id,
                'debit' => $fixedAsset->accumulated_depreciation,
                'credit' => 0,
                'description' => 'Eliminasi akumulasi penyusutan ' . $fixedAsset->name
            ]);

            // 3. Gain or Loss on Disposal
            if ($disposalCalculation['type'] === 'loss') {
                // Debit: Loss on Disposal
                // Find or use a default loss account (you may need to configure this)
                $lossAccount = ChartOfAccount::where('outlet_id', $fixedAsset->outlet_id)
                    ->where('type', 'otherexpense')
                    ->where('name', 'LIKE', '%Loss on Disposal%')
                    ->first();

                if (!$lossAccount) {
                    // Use depreciation expense account as fallback
                    $lossAccount = $fixedAsset->depreciationExpenseAccount;
                }

                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $lossAccount->id,
                    'debit' => $disposalCalculation['amount'],
                    'credit' => 0,
                    'description' => 'Kerugian pelepasan ' . $fixedAsset->name
                ]);
            } else if ($disposalCalculation['gain_loss'] > 0) {
                // Credit: Gain on Disposal
                // Find or use a default gain account
                $gainAccount = ChartOfAccount::where('outlet_id', $fixedAsset->outlet_id)
                    ->where('type', 'otherrevenue')
                    ->where('name', 'LIKE', '%Gain on Disposal%')
                    ->first();

                if (!$gainAccount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun Gain on Disposal tidak ditemukan. Silakan buat akun dengan tipe Other Revenue terlebih dahulu.'
                    ], 422);
                }

                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $gainAccount->id,
                    'debit' => 0,
                    'credit' => $disposalCalculation['amount'],
                    'description' => 'Keuntungan pelepasan ' . $fixedAsset->name
                ]);
            }

            // 4. Credit: Asset Account (acquisition_cost)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $fixedAsset->asset_account_id,
                'debit' => 0,
                'credit' => $fixedAsset->acquisition_cost,
                'description' => 'Eliminasi nilai perolehan ' . $fixedAsset->name
            ]);

            // Update FixedAsset
            $fixedAsset->update([
                'status' => $request->disposal_type, // 'sold' or 'disposed'
                'disposal_date' => $request->disposal_date,
                'disposal_value' => $request->disposal_value,
                'disposal_notes' => $request->disposal_notes
            ]);

            // Update account balances
            $paymentAccount = ChartOfAccount::find($fixedAsset->payment_account_id);
            $accumulatedDepreciationAccount = ChartOfAccount::find($fixedAsset->accumulated_depreciation_account_id);
            $assetAccount = ChartOfAccount::find($fixedAsset->asset_account_id);

            // Payment account increases (debit)
            $paymentAccount->updateBalance($request->disposal_value);
            
            // Accumulated depreciation account decreases (debit to contra asset)
            $accumulatedDepreciationAccount->updateBalance($fixedAsset->accumulated_depreciation);
            
            // Asset account decreases (credit)
            $assetAccount->updateBalance(-$fixedAsset->acquisition_cost);

            // Update gain/loss account balance
            if ($disposalCalculation['type'] === 'loss') {
                $lossAccount = ChartOfAccount::where('outlet_id', $fixedAsset->outlet_id)
                    ->where('type', 'otherexpense')
                    ->where('name', 'LIKE', '%Loss on Disposal%')
                    ->first();
                if (!$lossAccount) {
                    $lossAccount = $fixedAsset->depreciationExpenseAccount;
                }
                $lossAccount->updateBalance($disposalCalculation['amount']);
            } else if ($disposalCalculation['gain_loss'] > 0) {
                $gainAccount = ChartOfAccount::where('outlet_id', $fixedAsset->outlet_id)
                    ->where('type', 'otherrevenue')
                    ->where('name', 'LIKE', '%Gain on Disposal%')
                    ->first();
                if ($gainAccount) {
                    $gainAccount->updateBalance($disposalCalculation['amount']);
                }
            }

            // Update book total entries
            $book->incrementEntries();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'fixed_asset' => $fixedAsset->fresh(),
                    'journal_entry' => $journalEntry->load('journalEntryDetails.account'),
                    'disposal_calculation' => $disposalCalculation
                ],
                'message' => 'Aktiva tetap berhasil dilepas dan jurnal telah diposting'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error disposing asset: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal melepas aktiva tetap: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== FIXED ASSETS STATISTICS AND REPORTING ====================

    /**
     * Get fixed assets statistics for dashboard
     */
    public function fixedAssetsStats(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);

            // Calculate total assets and active assets per outlet
            $totalAssets = FixedAsset::byOutlet($outletId)->count();
            $activeAssets = FixedAsset::byOutlet($outletId)->active()->count();

            // Calculate total acquisition cost, total depreciation, total book value
            $totals = FixedAsset::byOutlet($outletId)
                ->selectRaw('
                    SUM(acquisition_cost) as total_acquisition_cost,
                    SUM(accumulated_depreciation) as total_depreciation,
                    SUM(book_value) as total_book_value
                ')
                ->first();

            $totalAcquisitionCost = floatval($totals->total_acquisition_cost ?? 0);
            $totalDepreciation = floatval($totals->total_depreciation ?? 0);
            $totalBookValue = floatval($totals->total_book_value ?? 0);

            // Calculate depreciation rate
            $depreciationRate = $totalAcquisitionCost > 0 
                ? round(($totalDepreciation / $totalAcquisitionCost) * 100, 2) 
                : 0;

            // Group by category for distribution
            $categoryDistribution = FixedAsset::byOutlet($outletId)
                ->selectRaw('
                    category,
                    COUNT(*) as count,
                    SUM(acquisition_cost) as total_acquisition_cost,
                    SUM(accumulated_depreciation) as total_depreciation,
                    SUM(book_value) as total_book_value
                ')
                ->groupBy('category')
                ->get()
                ->map(function($item) {
                    return [
                        'category' => $item->category,
                        'count' => $item->count,
                        'total_acquisition_cost' => floatval($item->total_acquisition_cost),
                        'total_depreciation' => floatval($item->total_depreciation),
                        'total_book_value' => floatval($item->total_book_value)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'totalAssets' => $totalAssets,
                    'activeAssets' => $activeAssets,
                    'totalAcquisitionCost' => $totalAcquisitionCost,
                    'totalDepreciation' => $totalDepreciation,
                    'totalBookValue' => $totalBookValue,
                    'depreciationRate' => $depreciationRate,
                    'categoryDistribution' => $categoryDistribution
                ],
                'message' => 'Statistik aktiva tetap berhasil diambil'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching fixed assets stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik aktiva tetap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get asset value chart data (acquisition cost and book value per year)
     */
    public function assetValueChartData(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);

            // Query fixed assets with group by acquisition year
            $chartData = FixedAsset::byOutlet($outletId)
                ->selectRaw('
                    YEAR(acquisition_date) as year,
                    SUM(acquisition_cost) as total_acquisition_cost,
                    SUM(book_value) as total_book_value
                ')
                ->groupBy(DB::raw('YEAR(acquisition_date)'))
                ->orderBy('year', 'asc')
                ->get();

            // Format data for Chart.js
            $labels = [];
            $acquisitionCostData = [];
            $bookValueData = [];

            foreach ($chartData as $data) {
                $labels[] = (string) $data->year;
                $acquisitionCostData[] = floatval($data->total_acquisition_cost);
                $bookValueData[] = floatval($data->total_book_value);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'Nilai Perolehan',
                            'data' => $acquisitionCostData,
                            'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                            'borderColor' => 'rgba(54, 162, 235, 1)',
                            'borderWidth' => 2
                        ],
                        [
                            'label' => 'Nilai Buku',
                            'data' => $bookValueData,
                            'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                            'borderColor' => 'rgba(75, 192, 192, 1)',
                            'borderWidth' => 2
                        ]
                    ]
                ],
                'message' => 'Data chart nilai aset berhasil diambil'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching asset value chart data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data chart nilai aset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get asset distribution chart data (count and value per category)
     */
    public function assetDistributionData(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);

            // Query fixed assets with group by category
            $distributionData = FixedAsset::byOutlet($outletId)
                ->selectRaw('
                    category,
                    COUNT(*) as count,
                    SUM(book_value) as total_value
                ')
                ->groupBy('category')
                ->get();

            // Format data for Chart.js pie chart
            $labels = [];
            $countData = [];
            $valueData = [];
            $backgroundColors = [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)'
            ];

            $categoryLabels = [
                'land' => 'Tanah',
                'building' => 'Gedung',
                'vehicle' => 'Kendaraan',
                'equipment' => 'Peralatan',
                'furniture' => 'Furniture',
                'computer' => 'Komputer'
            ];

            $colorIndex = 0;
            foreach ($distributionData as $data) {
                $labels[] = $categoryLabels[$data->category] ?? ucfirst($data->category);
                $countData[] = $data->count;
                $valueData[] = floatval($data->total_value);
                $colorIndex++;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'count_chart' => [
                        'labels' => $labels,
                        'datasets' => [
                            [
                                'label' => 'Jumlah Aset',
                                'data' => $countData,
                                'backgroundColor' => array_slice($backgroundColors, 0, count($labels)),
                                'borderWidth' => 1
                            ]
                        ]
                    ],
                    'value_chart' => [
                        'labels' => $labels,
                        'datasets' => [
                            [
                                'label' => 'Nilai Buku',
                                'data' => $valueData,
                                'backgroundColor' => array_slice($backgroundColors, 0, count($labels)),
                                'borderWidth' => 1
                            ]
                        ]
                    ]
                ],
                'message' => 'Data chart distribusi aset berhasil diambil'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching asset distribution data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data chart distribusi aset: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== FIXED ASSETS EXPORT/IMPORT ====================

    /**
     * Export fixed assets to XLSX format
     */
    public function exportFixedAssetsXLSX(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $category = $request->get('category', 'all');
            $status = $request->get('status', 'all');

            // Query fixed assets with filters
            $query = FixedAsset::with(['outlet'])
                ->byOutlet($outletId)
                ->when($category !== 'all', function($q) use ($category) {
                    $q->where('category', $category);
                })
                ->when($status !== 'all', function($q) use ($status) {
                    $q->where('status', $status);
                })
                ->orderBy('acquisition_date', 'desc');

            $assets = $query->get();

            // Get outlet info for filters
            $outlet = Outlet::find($outletId);
            
            // Prepare filters for export
            $filters = [
                'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
                'category' => $category,
                'status' => $status
            ];

            // Use FixedAssetsExport class
            $export = new \App\Exports\FixedAssetsExport($assets, $filters);
            $filename = 'aktiva_tetap_' . date('Y-m-d_His') . '.xlsx';

            return Excel::download($export, $filename);

        } catch (\Exception $e) {
            \Log::error('Error exporting fixed assets to XLSX: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal export data aktiva tetap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export fixed assets to PDF format
     */
    public function exportFixedAssetsPDF(Request $request)
    {
        try {
            $companySettings = $this->getCompanySettingsForPrint();
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $category = $request->get('category', 'all');
            $status = $request->get('status', 'all');
            $groupByCategory = $request->get('group_by_category', false);

            // Query fixed assets with filters
            $query = FixedAsset::with(['outlet'])
                ->byOutlet($outletId)
                ->when($category !== 'all', function($q) use ($category) {
                    $q->where('category', $category);
                })
                ->when($status !== 'all', function($q) use ($status) {
                    $q->where('status', $status);
                })
                ->orderBy('category', 'asc')
                ->orderBy('acquisition_date', 'desc');

            $assets = $query->get();

            // Get outlet info for filters
            $outlet = Outlet::find($outletId);
            
            // Prepare filters for PDF
            $filters = [
                'company_name' => $companySettings['company_name'] ?? 'Nama Perusahaan',
                'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
                'category' => $category,
                'status' => $status,
                'group_by_category' => $groupByCategory
            ];

            // Generate PDF
            $pdf = Pdf::loadView('admin.finance.aktiva-tetap.pdf', [
                'data' => $assets,
                'filters' => $filters,
                'companySettings' => $companySettings
            ]);

            $filename = 'aktiva_tetap_' . date('Y-m-d_His') . '.pdf';
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Error exporting fixed assets to PDF: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal export data aktiva tetap ke PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import fixed assets from Excel file
     */
    public function importFixedAssets(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:xlsx,xls|max:5120',
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'book_id' => 'required|exists:accounting_books,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()->all()
                ], 422);
            }

            $outletId = $request->outlet_id;
            $bookId = $request->book_id;
            $file = $request->file('file');

            // Validate book belongs to outlet
            $book = AccountingBook::where('id', $bookId)
                ->where('outlet_id', $outletId)
                ->where('status', 'active')
                ->first();

            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku akuntansi tidak ditemukan atau tidak aktif untuk outlet ini'
                ], 422);
            }

            // Use FinanceImportService
            $importService = new \App\Services\FinanceImportService();
            $result = $importService->import('fixed-assets', $file, [
                'outlet_id' => $outletId,
                'book_id' => $bookId
            ]);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Import berhasil dengan status draft. Silakan approve untuk posting jurnal.',
                    'imported_count' => $result['imported_count'] ?? 0,
                    'skipped_count' => $result['skipped_count'] ?? 0,
                    'errors' => $result['errors'] ?? []
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Import gagal',
                    'errors' => $result['errors'] ?? []
                ], 422);
            }

        } catch (\Exception $e) {
            \Log::error('Error importing fixed assets: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor data: ' . $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Download fixed assets import template
     */
    public function downloadFixedAssetsTemplate()
    {
        try {
            // Create a simple template with headers and sample data
            $templateData = [
                (object)[
                    'kode_aset' => 'AST-202401-001',
                    'nama_aset' => 'Contoh Komputer',
                    'kategori' => 'computer',
                    'lokasi' => 'Kantor Pusat',
                    'tanggal_perolehan' => '2024-01-01',
                    'harga_perolehan' => 10000000,
                    'nilai_residu' => 1000000,
                    'umur_ekonomis' => 4,
                    'metode_penyusutan' => 'straight_line',
                    'akumulasi_penyusutan' => 0,
                    'kode_akun_aset' => '1300',
                    'kode_akun_beban' => '6100',
                    'kode_akun_akumulasi' => '1310',
                    'kode_akun_pembayaran' => '1100',
                    'deskripsi' => 'Contoh deskripsi aset'
                ],
                (object)[
                    'kode_aset' => 'AST-202401-002',
                    'nama_aset' => 'Contoh Kendaraan',
                    'kategori' => 'vehicle',
                    'lokasi' => 'Kantor Cabang',
                    'tanggal_perolehan' => '2024-01-15',
                    'harga_perolehan' => 150000000,
                    'nilai_residu' => 30000000,
                    'umur_ekonomis' => 8,
                    'metode_penyusutan' => 'declining_balance',
                    'akumulasi_penyusutan' => 0,
                    'kode_akun_aset' => '1300',
                    'kode_akun_beban' => '6100',
                    'kode_akun_akumulasi' => '1310',
                    'kode_akun_pembayaran' => '1100',
                    'deskripsi' => 'Contoh kendaraan operasional'
                ]
            ];

            // Use FixedAssetsTemplateExport class
            $export = new \App\Exports\FixedAssetsTemplateExport();
            return Excel::download($export, 'template_import_aktiva_tetap.xlsx');

        } catch (\Exception $e) {
            \Log::error('Error downloading fixed assets template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export fixed assets to Excel
     */
    public function exportFixedAssets(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $category = $request->get('category', 'all');
            $status = $request->get('status', 'all');

            // Query fixed assets with filters
            $query = FixedAsset::with([
                'outlet',
                'assetAccount',
                'depreciationExpenseAccount',
                'accumulatedDepreciationAccount',
                'paymentAccount'
            ])
            ->byOutlet($outletId)
            ->when($category !== 'all', function($q) use ($category) {
                $q->where('category', $category);
            })
            ->when($status !== 'all', function($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderBy('acquisition_date', 'desc');

            $assets = $query->get();

            // Format data for export
            $exportData = [];
            $exportData[] = ['LAPORAN AKTIVA TETAP'];
            $exportData[] = ['Outlet: ' . ($assets->first()->outlet->nama_outlet ?? '-')];
            $exportData[] = ['Tanggal Export: ' . now()->translatedFormat('d F Y H:i')];
            $exportData[] = []; // Empty row

            // Header row
            $exportData[] = [
                'Kode',
                'Nama Aset',
                'Kategori',
                'Lokasi',
                'Tanggal Perolehan',
                'Nilai Perolehan',
                'Nilai Residu',
                'Masa Manfaat (Tahun)',
                'Metode Penyusutan',
                'Akumulasi Penyusutan',
                'Nilai Buku',
                'Status',
                'Akun Aset',
                'Akun Beban Penyusutan',
                'Akun Akumulasi Penyusutan'
            ];

            // Data rows
            $totalAcquisitionCost = 0;
            $totalDepreciation = 0;
            $totalBookValue = 0;

            foreach ($assets as $asset) {
                $categoryLabels = [
                    'land' => 'Tanah',
                    'building' => 'Gedung',
                    'vehicle' => 'Kendaraan',
                    'equipment' => 'Peralatan',
                    'furniture' => 'Furniture',
                    'computer' => 'Komputer'
                ];

                $methodLabels = [
                    'straight_line' => 'Garis Lurus',
                    'declining_balance' => 'Saldo Menurun',
                    'double_declining' => 'Saldo Menurun Ganda',
                    'units_of_production' => 'Unit Produksi'
                ];

                $statusLabels = [
                    'active' => 'Aktif',
                    'inactive' => 'Tidak Aktif',
                    'sold' => 'Terjual',
                    'disposed' => 'Dilepas'
                ];

                $exportData[] = [
                    $asset->code,
                    $asset->name,
                    $categoryLabels[$asset->category] ?? $asset->category,
                    $asset->location ?? '-',
                    $asset->acquisition_date->format('d/m/Y'),
                    floatval($asset->acquisition_cost),
                    floatval($asset->salvage_value),
                    $asset->useful_life,
                    $methodLabels[$asset->depreciation_method] ?? $asset->depreciation_method,
                    floatval($asset->accumulated_depreciation),
                    floatval($asset->book_value),
                    $statusLabels[$asset->status] ?? $asset->status,
                    $asset->assetAccount->code . ' - ' . $asset->assetAccount->name,
                    $asset->depreciationExpenseAccount->code . ' - ' . $asset->depreciationExpenseAccount->name,
                    $asset->accumulatedDepreciationAccount->code . ' - ' . $asset->accumulatedDepreciationAccount->name
                ];

                $totalAcquisitionCost += floatval($asset->acquisition_cost);
                $totalDepreciation += floatval($asset->accumulated_depreciation);
                $totalBookValue += floatval($asset->book_value);
            }

            // Summary row
            $exportData[] = []; // Empty row
            $exportData[] = [
                '',
                '',
                '',
                '',
                'TOTAL',
                $totalAcquisitionCost,
                '',
                '',
                '',
                $totalDepreciation,
                $totalBookValue,
                '',
                '',
                '',
                ''
            ];

            // Create Excel file using Maatwebsite\Excel
            $export = new class($exportData) implements \Maatwebsite\Excel\Concerns\FromArray {
                protected $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function array(): array
                {
                    return $this->data;
                }
            };

            $outlet = Outlet::find($outletId);
            $filename = 'aktiva_tetap_' . str_replace(' ', '_', $outlet->nama_outlet ?? 'outlet') . '_' . date('Y-m-d') . '.xlsx';

            return Excel::download($export, $filename);

        } catch (\Exception $e) {
            \Log::error('Error exporting fixed assets: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal export data aktiva tetap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export general ledger to XLSX format
     */
    public function exportGeneralLedgerXLSX(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $accountId = $request->get('account_id');

            if (!$outletId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter outlet_id, start_date, dan end_date wajib diisi'
                ], 422);
            }

            // Get ledger data
            $ledgerData = $this->calculateOdooStyleLedger($outletId, $startDate, $endDate, $accountId);

            // Get outlet info for filters
            $outlet = Outlet::find($outletId);
            
            // Get account name if specific account is selected
            $accountName = null;
            if ($accountId && $accountId !== 'all') {
                $account = ChartOfAccount::find($accountId);
                $accountName = $account ? $account->code . ' - ' . $account->name : null;
            }
            
            // Prepare filters for export
            $filters = [
                'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'account_name' => $accountName
            ];

            // Use FinanceExportService
            $exportService = new FinanceExportService();
            return $exportService->exportToXLSX('general-ledger', $ledgerData, $filters);

        } catch (\Exception $e) {
            \Log::error('Error exporting general ledger to XLSX: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export general ledger to PDF format
     */
    public function exportGeneralLedgerPDF(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $accountId = $request->get('account_id');

            if (!$outletId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter outlet_id, start_date, dan end_date wajib diisi'
                ], 422);
            }

            // Get ledger data
            $ledgerData = $this->calculateOdooStyleLedger($outletId, $startDate, $endDate, $accountId);

            // Get outlet and company info for PDF header
            $outlet = Outlet::find($outletId);
            
            // Get account name if specific account is selected
            $accountName = null;
            if ($accountId && $accountId !== 'all') {
                $account = ChartOfAccount::find($accountId);
                $accountName = $account ? $account->code . ' - ' . $account->name : null;
            }
            
            // Prepare filters for PDF
            $filters = [
                'company_name' => config('app.name', 'Nama Perusahaan'),
                'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'account_name' => $accountName
            ];

            // Use FinanceExportService
            $exportService = new FinanceExportService();
            return $exportService->exportToPDF('general-ledger', $ledgerData, $filters);

        } catch (\Exception $e) {
            \Log::error('Error exporting general ledger to PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the profit & loss report page
     */
    public function profitLossIndex(Request $request)
    {
        return view('admin.finance.labarugi.index');
    }

    /**
     * Get profit & loss data for a specific period
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function profitLossData(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'comparison' => 'nullable|boolean',
                'comparison_start_date' => 'nullable|required_if:comparison,true|date',
                'comparison_end_date' => 'nullable|required_if:comparison,true|date|after_or_equal:comparison_start_date',
            ], [
                'outlet_id.required' => 'Outlet wajib dipilih',
                'outlet_id.exists' => 'Outlet tidak ditemukan',
                'start_date.required' => 'Tanggal mulai wajib diisi',
                'start_date.date' => 'Format tanggal mulai tidak valid',
                'end_date.required' => 'Tanggal akhir wajib diisi',
                'end_date.date' => 'Format tanggal akhir tidak valid',
                'end_date.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai',
                'comparison_start_date.required_if' => 'Tanggal mulai pembanding wajib diisi saat mode perbandingan aktif',
                'comparison_start_date.date' => 'Format tanggal mulai pembanding tidak valid',
                'comparison_end_date.required_if' => 'Tanggal akhir pembanding wajib diisi saat mode perbandingan aktif',
                'comparison_end_date.date' => 'Format tanggal akhir pembanding tidak valid',
                'comparison_end_date.after_or_equal' => 'Tanggal akhir pembanding harus sama atau setelah tanggal mulai pembanding',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $outletId = $request->outlet_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            // Get outlet info
            $outlet = Outlet::find($outletId);
            
            // Calculate current period data
            $currentData = $this->calculateProfitLossForPeriod($outletId, $startDate, $endDate);
            
            // Prepare response data
            $data = [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'outlet_name' => $outlet->nama_outlet ?? 'Unknown'
                ],
                'revenue' => $currentData['revenue'],
                'other_revenue' => $currentData['other_revenue'],
                'expense' => $currentData['expense'],
                'other_expense' => $currentData['other_expense'],
                'summary' => $currentData['summary'],
                'comparison' => [
                    'enabled' => false,
                    'period' => null,
                    'revenue' => null,
                    'expense' => null,
                    'summary' => null,
                    'changes' => null
                ]
            ];
            
            // Handle comparison mode
            if ($request->comparison && $request->comparison_start_date && $request->comparison_end_date) {
                $comparisonData = $this->calculateProfitLossForPeriod(
                    $outletId, 
                    $request->comparison_start_date, 
                    $request->comparison_end_date
                );
                
                $data['comparison'] = [
                    'enabled' => true,
                    'period' => [
                        'start_date' => $request->comparison_start_date,
                        'end_date' => $request->comparison_end_date
                    ],
                    'revenue' => $comparisonData['revenue'],
                    'other_revenue' => $comparisonData['other_revenue'],
                    'expense' => $comparisonData['expense'],
                    'other_expense' => $comparisonData['other_expense'],
                    'summary' => $comparisonData['summary'],
                    'changes' => $this->calculateChanges($currentData['summary'], $comparisonData['summary'])
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching profit & loss data: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get profit & loss statistics for dashboard
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function profitLossStats(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'period' => 'nullable|in:monthly,quarterly,yearly'
            ], [
                'outlet_id.required' => 'Outlet wajib dipilih',
                'outlet_id.exists' => 'Outlet tidak ditemukan',
                'period.in' => 'Periode tidak valid. Pilih: monthly, quarterly, atau yearly'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $outletId = $request->outlet_id;
            
            // Calculate current month
            $currentMonthStart = now()->startOfMonth()->format('Y-m-d');
            $currentMonthEnd = now()->endOfMonth()->format('Y-m-d');
            $currentMonth = $this->calculateProfitLossForPeriod($outletId, $currentMonthStart, $currentMonthEnd);
            
            // Calculate last month
            $lastMonthStart = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $lastMonthEnd = now()->subMonth()->endOfMonth()->format('Y-m-d');
            $lastMonth = $this->calculateProfitLossForPeriod($outletId, $lastMonthStart, $lastMonthEnd);
            
            // Calculate YTD (Year to Date)
            $ytdStart = now()->startOfYear()->format('Y-m-d');
            $ytdEnd = now()->format('Y-m-d');
            $ytd = $this->calculateProfitLossForPeriod($outletId, $ytdStart, $ytdEnd);
            
            // Calculate trends for last 6 months
            $trends = $this->calculateProfitLossTrends($outletId, 6);
            
            $data = [
                'current_month' => [
                    'revenue' => $currentMonth['summary']['total_revenue'],
                    'expense' => $currentMonth['summary']['total_expense'],
                    'net_income' => $currentMonth['summary']['net_income']
                ],
                'last_month' => [
                    'revenue' => $lastMonth['summary']['total_revenue'],
                    'expense' => $lastMonth['summary']['total_expense'],
                    'net_income' => $lastMonth['summary']['net_income']
                ],
                'ytd' => [
                    'revenue' => $ytd['summary']['total_revenue'],
                    'expense' => $ytd['summary']['total_expense'],
                    'net_income' => $ytd['summary']['net_income']
                ],
                'trends' => $trends
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching profit & loss stats: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export profit & loss report to XLSX
     * 
     * @param Request $request
     * @return mixed
     */
    public function exportProfitLossXLSX(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'comparison' => 'nullable|boolean',
                'comparison_start_date' => 'nullable|required_if:comparison,true|date',
                'comparison_end_date' => 'nullable|required_if:comparison,true|date|after_or_equal:comparison_start_date',
            ], [
                'outlet_id.required' => 'Outlet wajib dipilih',
                'outlet_id.exists' => 'Outlet tidak ditemukan',
                'start_date.required' => 'Tanggal mulai wajib diisi',
                'start_date.date' => 'Format tanggal mulai tidak valid',
                'end_date.required' => 'Tanggal akhir wajib diisi',
                'end_date.date' => 'Format tanggal akhir tidak valid',
                'end_date.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai',
                'comparison_start_date.required_if' => 'Tanggal mulai pembanding wajib diisi saat mode perbandingan aktif',
                'comparison_start_date.date' => 'Format tanggal mulai pembanding tidak valid',
                'comparison_end_date.required_if' => 'Tanggal akhir pembanding wajib diisi saat mode perbandingan aktif',
                'comparison_end_date.date' => 'Format tanggal akhir pembanding tidak valid',
                'comparison_end_date.after_or_equal' => 'Tanggal akhir pembanding harus sama atau setelah tanggal mulai pembanding',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $outletId = $request->outlet_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            // Get outlet info
            $outlet = Outlet::find($outletId);
            
            // Calculate current period data using existing logic
            $currentData = $this->calculateProfitLossForPeriod($outletId, $startDate, $endDate);
            
            // Prepare filters for export
            $filters = [
                'outlet_name' => $outlet->nama_outlet ?? 'Unknown',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'comparison_enabled' => false
            ];
            
            // Handle comparison mode
            if ($request->comparison && $request->comparison_start_date && $request->comparison_end_date) {
                $comparisonData = $this->calculateProfitLossForPeriod(
                    $outletId, 
                    $request->comparison_start_date, 
                    $request->comparison_end_date
                );
                
                $filters['comparison_enabled'] = true;
                $filters['comparison_start_date'] = $request->comparison_start_date;
                $filters['comparison_end_date'] = $request->comparison_end_date;
                
                // Merge comparison data into current data for export
                $currentData['comparison'] = $comparisonData;
            }
            
            // Use ProfitLossExport class
            $export = new \App\Exports\ProfitLossExport($currentData, $filters);
            $filename = 'laporan_laba_rugi_' . str_replace(' ', '_', $outlet->nama_outlet) . '_' . date('Y-m-d') . '.xlsx';

            return Excel::download($export, $filename);

        } catch (\Exception $e) {
            \Log::error('Error exporting profit & loss to XLSX: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export profit & loss report to PDF
     * 
     * @param Request $request
     * @return mixed
     */
    public function exportProfitLossPDF(Request $request)
    {
        try {
            $companySettings = $this->getCompanySettingsForPrint();
            
            // Validate request
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'comparison' => 'nullable|boolean',
                'comparison_start_date' => 'nullable|required_if:comparison,true|date',
                'comparison_end_date' => 'nullable|required_if:comparison,true|date|after_or_equal:comparison_start_date',
            ], [
                'outlet_id.required' => 'Outlet wajib dipilih',
                'outlet_id.exists' => 'Outlet tidak ditemukan',
                'start_date.required' => 'Tanggal mulai wajib diisi',
                'start_date.date' => 'Format tanggal mulai tidak valid',
                'end_date.required' => 'Tanggal akhir wajib diisi',
                'end_date.date' => 'Format tanggal akhir tidak valid',
                'end_date.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai',
                'comparison_start_date.required_if' => 'Tanggal mulai pembanding wajib diisi saat mode perbandingan aktif',
                'comparison_start_date.date' => 'Format tanggal mulai pembanding tidak valid',
                'comparison_end_date.required_if' => 'Tanggal akhir pembanding wajib diisi saat mode perbandingan aktif',
                'comparison_end_date.date' => 'Format tanggal akhir pembanding tidak valid',
                'comparison_end_date.after_or_equal' => 'Tanggal akhir pembanding harus sama atau setelah tanggal mulai pembanding',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $outletId = $request->outlet_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            // Get outlet info
            $outlet = Outlet::find($outletId);
            
            // Calculate current period data using existing logic
            $currentData = $this->calculateProfitLossForPeriod($outletId, $startDate, $endDate);
            
            // Prepare filters for PDF
            $filters = [
                'company_name' => $companySettings['company_name'] ?? 'Nama Perusahaan',
                'outlet_name' => $outlet->nama_outlet ?? 'Unknown',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'comparison_enabled' => false
            ];
            
            // Handle comparison mode
            if ($request->comparison && $request->comparison_start_date && $request->comparison_end_date) {
                $comparisonData = $this->calculateProfitLossForPeriod(
                    $outletId, 
                    $request->comparison_start_date, 
                    $request->comparison_end_date
                );
                
                $filters['comparison_enabled'] = true;
                $filters['comparison_start_date'] = $request->comparison_start_date;
                $filters['comparison_end_date'] = $request->comparison_end_date;
                
                // Merge comparison data into current data for export
                $currentData['comparison'] = $comparisonData;
            }
            
            // Generate PDF using DomPDF
            $pdf = Pdf::loadView('admin.finance.labarugi.pdf', [
                'data' => $currentData,
                'filters' => $filters,
                'companySettings' => $companySettings
            ]);

            // Set paper size, orientation, and margins for A4
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption('margin-top', '15mm');
            $pdf->setOption('margin-right', '15mm');
            $pdf->setOption('margin-bottom', '15mm');
            $pdf->setOption('margin-left', '15mm');

            $filename = 'laporan_laba_rugi_' . str_replace(' ', '_', $outlet->nama_outlet) . '_' . date('Y-m-d') . '.pdf';
            
            // Default: stream (preview) instead of download
            return $pdf->stream($filename);

        } catch (\Exception $e) {
            \Log::error('Error exporting profit & loss to PDF: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate profit & loss for a specific period
     * 
     * @param int $outletId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function calculateProfitLossForPeriod($outletId, $startDate, $endDate): array
    {
        // Query revenue accounts (type: 'revenue')
        $revenueAccounts = ChartOfAccount::where('outlet_id', $outletId)
            ->where('type', 'revenue')
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('code')
            ->get();
        
        // Query other revenue accounts (type: 'otherrevenue')
        $otherRevenueAccounts = ChartOfAccount::where('outlet_id', $outletId)
            ->where('type', 'otherrevenue')
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('code')
            ->get();
        
        // Query expense accounts (type: 'expense')
        $expenseAccounts = ChartOfAccount::where('outlet_id', $outletId)
            ->where('type', 'expense')
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('code')
            ->get();
        
        // Query other expense accounts (type: 'otherexpense')
        $otherExpenseAccounts = ChartOfAccount::where('outlet_id', $outletId)
            ->where('type', 'otherexpense')
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('code')
            ->get();
        
        // Calculate amounts for each account
        $revenueData = $this->calculateAccountsAmount($revenueAccounts, $outletId, $startDate, $endDate);
        $otherRevenueData = $this->calculateAccountsAmount($otherRevenueAccounts, $outletId, $startDate, $endDate);
        $expenseData = $this->calculateAccountsAmount($expenseAccounts, $outletId, $startDate, $endDate);
        $otherExpenseData = $this->calculateAccountsAmount($otherExpenseAccounts, $outletId, $startDate, $endDate);
        
        // Calculate totals
        $totalRevenue = $revenueData['total'] + $otherRevenueData['total'];
        $totalExpense = $expenseData['total'] + $otherExpenseData['total'];
        $grossProfit = $revenueData['total'];
        $operatingProfit = $revenueData['total'] - $expenseData['total'];
        $netIncome = $totalRevenue - $totalExpense;
        
        // Calculate financial ratios
        $ratios = $this->calculateFinancialRatios($totalRevenue, $totalExpense, $grossProfit, $operatingProfit);
        
        return [
            'revenue' => [
                'accounts' => $revenueData['accounts'],
                'total' => $revenueData['total']
            ],
            'other_revenue' => [
                'accounts' => $otherRevenueData['accounts'],
                'total' => $otherRevenueData['total']
            ],
            'expense' => [
                'accounts' => $expenseData['accounts'],
                'total' => $expenseData['total']
            ],
            'other_expense' => [
                'accounts' => $otherExpenseData['accounts'],
                'total' => $otherExpenseData['total']
            ],
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_expense' => $totalExpense,
                'gross_profit' => $grossProfit,
                'operating_profit' => $operatingProfit,
                'net_income' => $netIncome,
                'gross_profit_margin' => $ratios['gross_profit_margin'],
                'net_profit_margin' => $ratios['net_profit_margin'],
                'operating_expense_ratio' => $ratios['operating_expense_ratio']
            ]
        ];
    }

    /**
     * Calculate amounts for a collection of accounts
     * 
     * @param \Illuminate\Database\Eloquent\Collection $accounts
     * @param int $outletId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function calculateAccountsAmount($accounts, $outletId, $startDate, $endDate): array
    {
        $accountsData = [];
        $total = 0;
        
        foreach ($accounts as $account) {
            $amount = $this->calculateAccountAmountRecursive($account, $outletId, $startDate, $endDate);
            
            // Calculate children amounts first
            $childrenData = [];
            $hasNonZeroChildren = false;
            
            if ($account->children && $account->children->count() > 0) {
                foreach ($account->children as $child) {
                    $childAmount = $this->calculateAccountAmountRecursive($child, $outletId, $startDate, $endDate);
                    if ($childAmount != 0) {
                        $hasNonZeroChildren = true;
                        $childrenData[] = [
                            'id' => $child->id,
                            'code' => $child->code ?? '',
                            'name' => $child->name ?? 'Unnamed Account',
                            'amount' => abs($childAmount),
                            'is_child' => true
                        ];
                    }
                }
            }
            
            // Include parent account if it has amount OR has children with amounts
            if ($amount != 0 || $hasNonZeroChildren) {
                $accountData = [
                    'id' => $account->id,
                    'code' => $account->code ?? '',
                    'name' => $account->name ?? 'Unnamed Account',
                    'amount' => abs($amount),
                    'children' => $childrenData,
                    'is_parent' => count($childrenData) > 0
                ];
                
                $accountsData[] = $accountData;
                $total += abs($amount);
            }
        }
        
        return [
            'accounts' => $accountsData,
            'total' => $total
        ];
    }

    /**
     * Calculate account amount recursively including children
     * 
     * @param ChartOfAccount $account
     * @param int $outletId
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    private function calculateAccountAmountRecursive($account, $outletId, $startDate, $endDate): float
    {
        // Calculate direct transactions for this account
        $directAmount = JournalEntryDetail::whereHas('journalEntry', function($query) use ($outletId, $startDate, $endDate) {
                $query->where('outlet_id', $outletId)
                    ->where('status', 'posted')
                    ->whereBetween('transaction_date', [$startDate, $endDate]);
            })
            ->where('account_id', $account->id)
            ->selectRaw('SUM(credit - debit) as total')
            ->value('total');
        
        $amount = floatval($directAmount ?? 0);
        
        // Add children amounts recursively
        if ($account->children && $account->children->count() > 0) {
            foreach ($account->children as $child) {
                $amount += $this->calculateAccountAmountRecursive($child, $outletId, $startDate, $endDate);
            }
        }
        
        return $amount;
    }

    /**
     * Calculate financial ratios
     * 
     * @param float $totalRevenue
     * @param float $totalExpense
     * @param float $grossProfit
     * @param float $operatingProfit
     * @return array
     */
    private function calculateFinancialRatios($totalRevenue, $totalExpense, $grossProfit, $operatingProfit): array
    {
        // Handle division by zero
        if ($totalRevenue == 0) {
            return [
                'gross_profit_margin' => null,
                'net_profit_margin' => null,
                'operating_expense_ratio' => null
            ];
        }
        
        $grossProfitMargin = ($grossProfit / $totalRevenue) * 100;
        $netProfitMargin = (($totalRevenue - $totalExpense) / $totalRevenue) * 100;
        $operatingExpenseRatio = ($totalExpense / $totalRevenue) * 100;
        
        return [
            'gross_profit_margin' => round($grossProfitMargin, 2),
            'net_profit_margin' => round($netProfitMargin, 2),
            'operating_expense_ratio' => round($operatingExpenseRatio, 2)
        ];
    }

    /**
     * Calculate changes between current and comparison periods
     * 
     * @param array $current
     * @param array $comparison
     * @return array
     */
    private function calculateChanges($current, $comparison): array
    {
        $changes = [];
        
        foreach ($current as $key => $value) {
            if (is_numeric($value) && isset($comparison[$key])) {
                $comparisonValue = $comparison[$key];
                $delta = $value - $comparisonValue;
                
                // Calculate percentage change
                if ($comparisonValue != 0) {
                    $percentage = ($delta / abs($comparisonValue)) * 100;
                } else {
                    $percentage = $value != 0 ? 100 : 0;
                }
                
                $changes[$key] = [
                    'delta' => $delta,
                    'percentage' => round($percentage, 2),
                    'direction' => $delta > 0 ? 'increase' : ($delta < 0 ? 'decrease' : 'stable')
                ];
            }
        }
        
        return $changes;
    }

    /**
     * Calculate profit & loss trends for Chart.js
     * 
     * @param int $outletId
     * @param int $months
     * @return array
     */
    private function calculateProfitLossTrends($outletId, $months = 6): array
    {
        $labels = [];
        $revenueData = [];
        $expenseData = [];
        $netIncomeData = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startDate = $date->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $date->copy()->endOfMonth()->format('Y-m-d');
            
            $periodData = $this->calculateProfitLossForPeriod($outletId, $startDate, $endDate);
            
            $labels[] = $date->translatedFormat('M Y');
            $revenueData[] = $periodData['summary']['total_revenue'];
            $expenseData[] = $periodData['summary']['total_expense'];
            $netIncomeData[] = $periodData['summary']['net_income'];
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenueData,
            'expense' => $expenseData,
            'net_income' => $netIncomeData
        ];
    }

    /**
     * Get account transaction details for profit & loss report
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function profitLossAccountDetails(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'account_id' => 'required|exists:chart_of_accounts,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ], [
                'outlet_id.required' => 'Outlet wajib dipilih',
                'outlet_id.exists' => 'Outlet tidak ditemukan',
                'account_id.required' => 'Akun wajib dipilih',
                'account_id.exists' => 'Akun tidak ditemukan',
                'start_date.required' => 'Tanggal mulai wajib diisi',
                'start_date.date' => 'Format tanggal mulai tidak valid',
                'end_date.required' => 'Tanggal akhir wajib diisi',
                'end_date.date' => 'Format tanggal akhir tidak valid',
                'end_date.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $outletId = $request->outlet_id;
            $accountId = $request->account_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            // Get account info
            $account = ChartOfAccount::find($accountId);
            
            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }

            // Get journal entries that affect this account
            $transactions = JournalEntry::where('outlet_id', $outletId)
                ->where('status', 'posted')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->whereHas('journalEntryDetails', function($query) use ($accountId) {
                    $query->where('account_id', $accountId);
                })
                ->with(['journalEntryDetails' => function($query) use ($accountId) {
                    $query->where('account_id', $accountId);
                }, 'book'])
                ->orderBy('transaction_date', 'asc')
                ->orderBy('id', 'asc')
                ->get()
                ->map(function($entry) use ($accountId) {
                    $detail = $entry->journalEntryDetails->first();
                    
                    return [
                        'id' => $entry->id,
                        'transaction_date' => $entry->transaction_date,
                        'transaction_number' => $entry->transaction_number,
                        'description' => $entry->description,
                        'debit' => $detail->debit ?? 0,
                        'credit' => $detail->credit ?? 0,
                        'amount' => ($detail->debit ?? 0) - ($detail->credit ?? 0),
                        'book_name' => $entry->book->name ?? '-',
                    ];
                });

            // Calculate total
            $totalDebit = $transactions->sum('debit');
            $totalCredit = $transactions->sum('credit');
            $totalAmount = $totalDebit - $totalCredit;

            return response()->json([
                'success' => true,
                'data' => [
                    'account' => [
                        'id' => $account->id,
                        'code' => $account->code,
                        'name' => $account->name,
                        'type' => $account->type,
                    ],
                    'period' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ],
                    'transactions' => $transactions,
                    'summary' => [
                        'total_debit' => $totalDebit,
                        'total_credit' => $totalCredit,
                        'total_amount' => $totalAmount,
                        'transaction_count' => $transactions->count(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting profit loss account details: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail transaksi akun'
            ], 500);
        }
    }

    /**
     * Halaman Neraca (Balance Sheet)
     */
    public function neracaIndex(Request $request)
    {
        $outletId = $request->get('outlet_id');
        
        return view('admin.finance.neraca.index', [
            'outlet_id' => $outletId
        ]);
    }

    /**
     * Get Neraca (Balance Sheet) Data
     */
    public function neracaData(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id', null);
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            // Get all accounts with their balances
            $assets = $this->getAccountsByType($outletId, 'asset', $endDate, $bookId);
            $liabilities = $this->getAccountsByType($outletId, 'liability', $endDate, $bookId);
            $equity = $this->getAccountsByType($outletId, 'equity', $endDate, $bookId);
            
            // Calculate totals
            $totalAssets = $this->calculateTotal($assets);
            $totalLiabilities = $this->calculateTotal($liabilities);
            $totalEquity = $this->calculateTotal($equity);
            
            // Calculate retained earnings (laba ditahan) dari laporan laba rugi
            $retainedEarnings = $this->calculateRetainedEarnings($outletId, $endDate, $bookId);
            $totalEquity += $retainedEarnings;
            
            $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
            
            // Check if balanced
            $isBalanced = abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01;
            $difference = $totalAssets - $totalLiabilitiesAndEquity;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'assets' => $assets,
                    'liabilities' => $liabilities,
                    'equity' => $equity,
                    'retained_earnings' => $retainedEarnings,
                    'totals' => [
                        'total_assets' => $totalAssets,
                        'total_liabilities' => $totalLiabilities,
                        'total_equity' => $totalEquity,
                        'total_liabilities_and_equity' => $totalLiabilitiesAndEquity,
                        'is_balanced' => $isBalanced,
                        'difference' => $difference
                    ]
                ],
                'message' => 'Data neraca berhasil diambil'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting neraca data: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data neraca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get accounts by type with hierarchical structure
     */
    private function getAccountsByType($outletId, $type, $endDate, $bookId = null)
    {
        $accounts = ChartOfAccount::with(['children'])
            ->where('outlet_id', $outletId)
            ->where('type', $type)
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->orderBy('code')
            ->get();
        
        return $accounts->map(function($account) use ($outletId, $endDate, $bookId) {
            return $this->buildAccountHierarchy($account, $outletId, $endDate, $bookId);
        })->toArray();
    }

    /**
     * Build account hierarchy with balances
     */
    private function buildAccountHierarchy($account, $outletId, $endDate, $bookId = null, $level = 1)
    {
        // Calculate balance up to end date
        $balance = $this->calculateAccountBalanceUpToDate($account->id, $outletId, $endDate, $bookId);
        
        $data = [
            'id' => $account->id,
            'code' => $account->code,
            'name' => $account->name,
            'type' => $account->type,
            'level' => $level,
            'balance' => $balance,
            'has_children' => $account->children && $account->children->count() > 0,
            'children' => []
        ];
        
        // Process children recursively
        if ($account->children && $account->children->count() > 0) {
            $childrenBalance = 0;
            foreach ($account->children as $child) {
                $childData = $this->buildAccountHierarchy($child, $outletId, $endDate, $bookId, $level + 1);
                $data['children'][] = $childData;
                $childrenBalance += $childData['balance'];
            }
            // Parent balance is sum of children
            $data['balance'] = $childrenBalance;
        }
        
        return $data;
    }

    /**
     * Calculate account balance up to specific date
     */
    private function calculateAccountBalanceUpToDate($accountId, $outletId, $endDate, $bookId = null)
    {
        // Get opening balance (debit - credit)
        $openingBalanceRecord = OpeningBalance::where('account_id', $accountId)
            ->where('outlet_id', $outletId)
            ->where('effective_date', '<=', $endDate)
            ->when($bookId, function($query) use ($bookId) {
                $query->where('book_id', $bookId);
            })
            ->orderBy('effective_date', 'desc')
            ->first();
        
        $openingBalance = 0;
        if ($openingBalanceRecord) {
            $openingBalance = floatval($openingBalanceRecord->debit) - floatval($openingBalanceRecord->credit);
        }
        
        // Get journal entries balance up to end date
        $journalBalance = JournalEntryDetail::whereHas('journalEntry', function($query) use ($outletId, $endDate, $bookId) {
                $query->where('outlet_id', $outletId)
                    ->where('status', 'posted')
                    ->where('transaction_date', '<=', $endDate);
                
                if ($bookId) {
                    $query->where('book_id', $bookId);
                }
            })
            ->where('account_id', $accountId)
            ->selectRaw('SUM(debit - credit) as balance')
            ->value('balance') ?? 0;
        
        return floatval($openingBalance) + floatval($journalBalance);
    }

    /**
     * Calculate total from account array
     */
    private function calculateTotal($accounts)
    {
        $total = 0;
        foreach ($accounts as $account) {
            $total += $account['balance'];
        }
        return $total;
    }

    /**
     * Calculate retained earnings (laba ditahan)
     */
    private function calculateRetainedEarnings($outletId, $endDate, $bookId = null)
    {
        // Calculate revenue
        $revenue = $this->calculateAccountTypeBalance($outletId, 'revenue', $endDate, $bookId);
        $otherRevenue = $this->calculateAccountTypeBalance($outletId, 'otherrevenue', $endDate, $bookId);
        
        // Calculate expenses
        $expense = $this->calculateAccountTypeBalance($outletId, 'expense', $endDate, $bookId);
        $otherExpense = $this->calculateAccountTypeBalance($outletId, 'otherexpense', $endDate, $bookId);
        
        // Net income = Revenue - Expense
        $netIncome = ($revenue + $otherRevenue) - ($expense + $otherExpense);
        
        return $netIncome;
    }

    /**
     * Calculate balance for account type
     */
    private function calculateAccountTypeBalance($outletId, $type, $endDate, $bookId = null)
    {
        $accounts = ChartOfAccount::where('outlet_id', $outletId)
            ->where('type', $type)
            ->where('status', 'active')
            ->get();
        
        $total = 0;
        foreach ($accounts as $account) {
            $balance = $this->calculateAccountBalanceUpToDate($account->id, $outletId, $endDate, $bookId);
            
            // For revenue accounts, credit increases balance (so we negate)
            // For expense accounts, debit increases balance
            if (in_array($type, ['revenue', 'otherrevenue'])) {
                $total += -$balance; // Revenue is credit balance
            } else {
                $total += $balance; // Expense is debit balance
            }
        }
        
        return $total;
    }

    /**
     * Get account transactions for neraca detail modal
     */
    public function getNeracaAccountDetails($id, Request $request): JsonResponse
    {
        try {
            $account = ChartOfAccount::with(['children', 'outlet'])->find($id);
            
            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }

            $outletId = $account->outlet_id;
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            // Get journal entries for this account up to end date
            $journalEntries = JournalEntryDetail::with(['journalEntry.book'])
                ->whereHas('journalEntry', function($query) use ($outletId, $endDate) {
                    $query->where('outlet_id', $outletId)
                        ->where('status', 'posted')
                        ->where('transaction_date', '<=', $endDate);
                })
                ->where('account_id', $id)
                ->orderBy('created_at', 'desc')
                ->limit(100) // Limit to last 100 transactions
                ->get()
                ->map(function($detail) {
                    return [
                        'id' => $detail->id,
                        'transaction_date' => $detail->journalEntry->transaction_date->format('Y-m-d'),
                        'transaction_number' => $detail->journalEntry->transaction_number,
                        'description' => $detail->description ?: $detail->journalEntry->description,
                        'debit' => floatval($detail->debit),
                        'credit' => floatval($detail->credit),
                        'book_name' => $detail->journalEntry->book->name ?? '-',
                        'reference_type' => $detail->journalEntry->reference_type,
                        'reference_number' => $detail->journalEntry->reference_number
                    ];
                });

            $bookId = $request->get('book_id', null);
            
            // Calculate balance
            $balance = $this->calculateAccountBalanceUpToDate($id, $outletId, $endDate, $bookId);
            
            // Get opening balance
            $openingBalanceRecord = OpeningBalance::where('account_id', $id)
                ->where('outlet_id', $outletId)
                ->where('effective_date', '<=', $endDate)
                ->when($bookId, function($query) use ($bookId) {
                    $query->where('book_id', $bookId);
                })
                ->orderBy('effective_date', 'desc')
                ->first();
            
            $openingBalance = 0;
            if ($openingBalanceRecord) {
                $openingBalance = floatval($openingBalanceRecord->debit) - floatval($openingBalanceRecord->credit);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'account' => [
                        'id' => $account->id,
                        'code' => $account->code,
                        'name' => $account->name,
                        'type' => $account->type,
                        'level' => $account->level,
                        'has_children' => $account->children && $account->children->count() > 0
                    ],
                    'transactions' => $journalEntries,
                    'summary' => [
                        'opening_balance' => floatval($openingBalance),
                        'total_debit' => $journalEntries->sum('debit'),
                        'total_credit' => $journalEntries->sum('credit'),
                        'total_amount' => $journalEntries->sum('debit') - $journalEntries->sum('credit'),
                        'current_balance' => $balance,
                        'transaction_count' => $journalEntries->count()
                    ]
                ],
                'message' => 'Detail transaksi akun berhasil diambil'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting neraca account details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail transaksi akun: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Neraca to PDF
     */
    public function exportNeracaPDF(Request $request)
    {
        try {
            $companySettings = $this->getCompanySettingsForPrint();
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id', null);
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            // Get neraca data
            $assets = $this->getAccountsByType($outletId, 'asset', $endDate, $bookId);
            $liabilities = $this->getAccountsByType($outletId, 'liability', $endDate, $bookId);
            $equity = $this->getAccountsByType($outletId, 'equity', $endDate, $bookId);
            
            $totalAssets = $this->calculateTotal($assets);
            $totalLiabilities = $this->calculateTotal($liabilities);
            $totalEquity = $this->calculateTotal($equity);
            
            $retainedEarnings = $this->calculateRetainedEarnings($outletId, $endDate, $bookId);
            $totalEquity += $retainedEarnings;
            
            $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
            
            // Check if balanced
            $isBalanced = abs($totalAssets - $totalLiabilitiesAndEquity) < 0.01;
            $difference = $totalAssets - $totalLiabilitiesAndEquity;
            
            // Get outlet info
            $outlet = Outlet::find($outletId);
            
            $data = [
                'assets' => $assets,
                'liabilities' => $liabilities,
                'equity' => $equity,
                'retained_earnings' => $retainedEarnings,
                'totals' => [
                    'total_assets' => $totalAssets,
                    'total_liabilities' => $totalLiabilities,
                    'total_equity' => $totalEquity,
                    'total_liabilities_and_equity' => $totalLiabilitiesAndEquity,
                    'is_balanced' => $isBalanced,
                    'difference' => $difference
                ],
                'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
                'end_date' => $endDate,
                'companySettings' => $companySettings,
                'print_date' => now()->format('d/m/Y H:i')
            ];
            
            $pdf = Pdf::loadView('admin.finance.neraca.pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('margin-top', '10mm')
                ->setOption('margin-right', '10mm')
                ->setOption('margin-bottom', '10mm')
                ->setOption('margin-left', '10mm');
            
            $filename = 'neraca_' . str_replace(' ', '_', $outlet->nama_outlet ?? 'outlet') . '_' . $endDate . '.pdf';
            return $pdf->stream($filename);
            
        } catch (\Exception $e) {
            \Log::error('Error exporting neraca to PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor neraca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Neraca to XLSX
     */
    public function exportNeracaXLSX(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id', null);
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            // Get neraca data
            $assets = $this->getAccountsByType($outletId, 'asset', $endDate, $bookId);
            $liabilities = $this->getAccountsByType($outletId, 'liability', $endDate, $bookId);
            $equity = $this->getAccountsByType($outletId, 'equity', $endDate, $bookId);
            
            $totalAssets = $this->calculateTotal($assets);
            $totalLiabilities = $this->calculateTotal($liabilities);
            $totalEquity = $this->calculateTotal($equity);
            
            $retainedEarnings = $this->calculateRetainedEarnings($outletId, $endDate, $bookId);
            
            // Flatten data for Excel export
            $exportData = [];
            
            // Assets
            $exportData[] = (object)['section' => 'ASET', 'code' => '', 'name' => '', 'balance' => ''];
            foreach ($assets as $asset) {
                $this->flattenAccountForExport($asset, $exportData);
            }
            $exportData[] = (object)['section' => '', 'code' => '', 'name' => 'TOTAL ASET', 'balance' => $totalAssets];
            $exportData[] = (object)['section' => '', 'code' => '', 'name' => '', 'balance' => ''];
            
            // Liabilities
            $exportData[] = (object)['section' => 'KEWAJIBAN', 'code' => '', 'name' => '', 'balance' => ''];
            foreach ($liabilities as $liability) {
                $this->flattenAccountForExport($liability, $exportData);
            }
            $exportData[] = (object)['section' => '', 'code' => '', 'name' => 'TOTAL KEWAJIBAN', 'balance' => $totalLiabilities];
            $exportData[] = (object)['section' => '', 'code' => '', 'name' => '', 'balance' => ''];
            
            // Equity
            $exportData[] = (object)['section' => 'EKUITAS', 'code' => '', 'name' => '', 'balance' => ''];
            foreach ($equity as $eq) {
                $this->flattenAccountForExport($eq, $exportData);
            }
            $exportData[] = (object)['section' => '', 'code' => '', 'name' => 'Laba Ditahan', 'balance' => $retainedEarnings];
            $exportData[] = (object)['section' => '', 'code' => '', 'name' => 'TOTAL EKUITAS', 'balance' => $totalEquity + $retainedEarnings];
            
            $outlet = Outlet::find($outletId);
            $filters = [
                'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
                'end_date' => $endDate
            ];
            
            $exportService = new FinanceExportService();
            return $exportService->exportToXLSX('neraca', $exportData, $filters);
            
        } catch (\Exception $e) {
            \Log::error('Error exporting neraca to XLSX: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor neraca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Flatten account hierarchy for export
     */
    private function flattenAccountForExport($account, &$exportData, $indent = 0)
    {
        $name = str_repeat('  ', $indent) . $account['name'];
        $exportData[] = (object)[
            'section' => '',
            'code' => $account['code'],
            'name' => $name,
            'balance' => $account['balance']
        ];
        
        if (!empty($account['children'])) {
            foreach ($account['children'] as $child) {
                $this->flattenAccountForExport($child, $exportData, $indent + 1);
            }
        }
    }

    /**
     * Get Trial Balance Data (Neraca Saldo)
     */
    public function trialBalanceData(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id', null);
            $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            // Get all active accounts
            $accountsQuery = ChartOfAccount::with(['parent', 'children'])
                ->byOutlet($outletId)
                ->active()
                ->orderBy('code');
            
            $accounts = $accountsQuery->get();
            
            $trialBalanceData = [];
            $totalDebit = 0;
            $totalCredit = 0;
            
            foreach ($accounts as $account) {
                // Calculate debit and credit for this account
                $balances = $this->calculateTrialBalanceForAccount($account->id, $outletId, $startDate, $endDate, $bookId);
                
                // Only include accounts with transactions
                if ($balances['debit'] > 0 || $balances['credit'] > 0 || $balances['opening_balance'] != 0) {
                    $trialBalanceData[] = [
                        'id' => $account->id,
                        'code' => $account->code,
                        'name' => $account->name,
                        'type' => $account->type,
                        'level' => $account->level,
                        'opening_balance' => $balances['opening_balance'],
                        'debit' => $balances['debit'],
                        'credit' => $balances['credit'],
                        'ending_balance' => $balances['ending_balance'],
                        'normal_balance' => $this->getNormalBalance($account->type)
                    ];
                    
                    $totalDebit += $balances['debit'];
                    $totalCredit += $balances['credit'];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $trialBalanceData,
                'summary' => [
                    'total_debit' => $totalDebit,
                    'total_credit' => $totalCredit,
                    'difference' => abs($totalDebit - $totalCredit),
                    'is_balanced' => abs($totalDebit - $totalCredit) < 0.01
                ],
                'filters' => [
                    'outlet_id' => $outletId,
                    'book_id' => $bookId,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching trial balance data: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data neraca saldo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calculate trial balance for a specific account
     */
    private function calculateTrialBalanceForAccount($accountId, $outletId, $startDate, $endDate, $bookId = null)
    {
        // Get opening balance (before start date)
        $openingQuery = JournalEntryDetail::whereHas('journalEntry', function($q) use ($outletId, $startDate, $bookId) {
            $q->where('outlet_id', $outletId)
              ->where('transaction_date', '<', $startDate)
              ->where('status', 'posted');
            if ($bookId) {
                $q->where('book_id', $bookId);
            }
        })->where('account_id', $accountId);
        
        $openingDebit = $openingQuery->sum('debit');
        $openingCredit = $openingQuery->sum('credit');
        $openingBalance = $openingDebit - $openingCredit;
        
        // Get period transactions
        $periodQuery = JournalEntryDetail::whereHas('journalEntry', function($q) use ($outletId, $startDate, $endDate, $bookId) {
            $q->where('outlet_id', $outletId)
              ->whereBetween('transaction_date', [$startDate, $endDate])
              ->where('status', 'posted');
            if ($bookId) {
                $q->where('book_id', $bookId);
            }
        })->where('account_id', $accountId);
        
        $periodDebit = $periodQuery->sum('debit');
        $periodCredit = $periodQuery->sum('credit');
        
        // Calculate ending balance
        $endingBalance = $openingBalance + $periodDebit - $periodCredit;
        
        return [
            'opening_balance' => $openingBalance,
            'debit' => $periodDebit,
            'credit' => $periodCredit,
            'ending_balance' => $endingBalance
        ];
    }
    
    /**
     * Get normal balance side for account type
     */
    private function getNormalBalance($type)
    {
        $normalBalances = [
            'asset' => 'debit',
            'expense' => 'debit',
            'otherexpense' => 'debit',
            'liability' => 'credit',
            'equity' => 'credit',
            'revenue' => 'credit',
            'otherrevenue' => 'credit'
        ];
        
        return $normalBalances[$type] ?? 'debit';
    }
    
    /**
     * Export Trial Balance to PDF
     */
    public function exportTrialBalancePDF(Request $request)
    {
        try {
            $companySettings = $this->getCompanySettingsForPrint();
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id', null);
            $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            // Get trial balance data
            $response = $this->trialBalanceData($request);
            $responseData = json_decode($response->getContent(), true);
            
            if (!$responseData['success']) {
                throw new \Exception($responseData['message']);
            }
            
            $outlet = Outlet::find($outletId);
            $book = $bookId ? AccountingBook::find($bookId) : null;
            
            $data = [
                'trialBalanceData' => $responseData['data'],
                'summary' => $responseData['summary'],
                'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
                'book_name' => $book->name ?? 'Semua Buku',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'companySettings' => $companySettings,
                'print_date' => now()->format('d/m/Y H:i')
            ];
            
            $pdf = Pdf::loadView('admin.finance.neraca-saldo.pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('margin-top', '10mm')
                ->setOption('margin-right', '10mm')
                ->setOption('margin-bottom', '10mm')
                ->setOption('margin-left', '10mm');
            
            $filename = 'neraca_saldo_' . str_replace(' ', '_', $outlet->nama_outlet ?? 'outlet') . '_' . $endDate . '.pdf';
            return $pdf->stream($filename);
            
        } catch (\Exception $e) {
            \Log::error('Error exporting trial balance to PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor neraca saldo: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export Trial Balance to XLSX
     */
    public function exportTrialBalanceXLSX(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id', null);
            $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            // Get trial balance data
            $response = $this->trialBalanceData($request);
            $responseData = json_decode($response->getContent(), true);
            
            if (!$responseData['success']) {
                throw new \Exception($responseData['message']);
            }
            
            $outlet = Outlet::find($outletId);
            $book = $bookId ? AccountingBook::find($bookId) : null;
            
            $filters = [
                'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
                'book_name' => $book->name ?? 'Semua Buku',
                'start_date' => $startDate,
                'end_date' => $endDate
            ];
            
            $exportService = new FinanceExportService();
            return $exportService->exportToXLSX('neraca-saldo', $responseData['data'], $filters, $responseData['summary']);
            
        } catch (\Exception $e) {
            \Log::error('Error exporting trial balance to XLSX: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor neraca saldo: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== PIUTANG METHODS ====================

    /**
     * Display piutang index page
     */
    public function piutangIndex()
    {
        return view('admin.finance.piutang.index');
    }

    /**
     * Get piutang data with filters (REALTIME from sales_invoice & invoice_payment_history)
     */
    public function getPiutangData(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $status = $request->get('status', 'all');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $search = $request->get('search');

            // Query dari tabel piutang dengan source_type = 'travel'
            $query = DB::table('piutang as p')
                ->leftJoin('member as m', 'p.id_member', '=', 'm.id_member')
                ->leftJoin('outlets as o', 'p.id_outlet', '=', 'o.id_outlet')
                ->leftJoin('jamaah_bookings as jb', 'p.id_jamaah_booking', '=', 'jb.id')
                ->select(
                    'p.id_piutang',
                    'p.tanggal_piutang as tanggal',
                    'p.tanggal_jatuh_tempo',
                    'p.jumlah_piutang',
                    'p.jumlah_dibayar',
                    'p.sisa_piutang',
                    'p.status',
                    'p.id_outlet',
                    'p.keterangan',
                    'p.id_jamaah_booking',
                    'p.source_type',
                    DB::raw('COALESCE(m.nama, "Customer") as nama_customer'),
                    'o.nama_outlet as outlet',
                    'jb.booking_code as invoice_number'
                )
                ->where('p.source_type', 'travel'); // HANYA piutang travel

            // Filter by outlet
            if ($outletId) {
                $query->where('p.id_outlet', $outletId);
            }

            // Filter by status
            if ($status !== 'all') {
                if ($status === 'belum_lunas') {
                    $query->where('p.status', 'belum_lunas');
                } elseif ($status === 'lunas') {
                    $query->where('p.status', 'lunas');
                }
            }

            // Filter by date range (inclusive)
            if ($startDate && $endDate) {
                $query->whereDate('p.tanggal_piutang', '>=', $startDate)
                      ->whereDate('p.tanggal_piutang', '<=', $endDate);
            }

            // Filter by search
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('jb.booking_code', 'like', "%{$search}%")
                      ->orWhere('m.nama', 'like', "%{$search}%");
                });
            }

            $piutangData = $query->orderBy('p.tanggal_piutang', 'desc')->get();

            // Calculate summary
            $totalPiutang = 0;
            $totalDibayar = 0;
            $totalSisa = 0;
            $countBelumLunas = 0;
            $countLunas = 0;
            $countOverdue = 0;

            $formattedData = [];

            // Process piutang data
            foreach ($piutangData as $piutang) {
                // Check if overdue
                $isOverdue = false;
                $daysOverdue = 0;
                if ($piutang->tanggal_jatuh_tempo && $piutang->status !== 'lunas') {
                    $dueDate = \Carbon\Carbon::parse($piutang->tanggal_jatuh_tempo);
                    $today = \Carbon\Carbon::today();
                    if ($today->gt($dueDate)) {
                        $isOverdue = true;
                        $daysOverdue = $today->diffInDays($dueDate);
                    }
                }

                // Update summary
                $totalPiutang += $piutang->jumlah_piutang;
                $totalDibayar += $piutang->jumlah_dibayar;
                $totalSisa += $piutang->sisa_piutang;
                
                if ($piutang->status === 'lunas') {
                    $countLunas++;
                } else {
                    $countBelumLunas++;
                }
                
                if ($isOverdue) {
                    $countOverdue++;
                }

                $formattedData[] = [
                    'id_piutang' => $piutang->id_piutang,
                    'id_penjualan' => null, // Travel tidak punya id_penjualan
                    'tanggal' => $piutang->tanggal,
                    'tanggal_jatuh_tempo' => $piutang->tanggal_jatuh_tempo,
                    'nama_customer' => $piutang->nama_customer ?: '-',
                    'outlet' => $piutang->outlet ?: '-',
                    'jumlah_piutang' => (float) $piutang->jumlah_piutang,
                    'jumlah_dibayar' => (float) $piutang->jumlah_dibayar,
                    'sisa_piutang' => (float) $piutang->sisa_piutang,
                    'status' => $piutang->status,
                    'is_overdue' => $isOverdue,
                    'days_overdue' => $daysOverdue,
                    'invoice_number' => $piutang->invoice_number ?: '-',
                    'source' => 'travel', // Mark as travel
                    'source_id' => $piutang->id_jamaah_booking // ID booking untuk redirect
                ];
            }

            $summary = [
                'total_piutang' => $totalPiutang,
                'total_dibayar' => $totalDibayar,
                'total_sisa' => $totalSisa,
                'count_belum_lunas' => $countBelumLunas,
                'count_lunas' => $countLunas,
                'count_overdue' => $countOverdue
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting piutang data: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data piutang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales_invoice ID from penjualan ID
     */
    public function getSalesInvoiceId($penjualanId)
    {
        try {
            $salesInvoice = DB::table('sales_invoice')
                ->where('id_penjualan', $penjualanId)
                ->first();
            
            if ($salesInvoice) {
                return response()->json([
                    'success' => true,
                    'sales_invoice_id' => $salesInvoice->id_sales_invoice,
                    'penjualan_id' => $penjualanId
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Sales invoice tidak ditemukan'
            ], 404);
            
        } catch (\Exception $e) {
            \Log::error('Error getting sales invoice ID: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data invoice'
            ], 500);
        }
    }

    /**
     * Mark piutang as paid (lunas)
     */
    public function markPiutangAsPaid(Request $request, $id)
    {
        try {
            $piutang = \App\Models\Piutang::findOrFail($id);
            
            // Validate
            if ($piutang->status === 'lunas') {
                return response()->json([
                    'success' => false,
                    'message' => 'Piutang sudah lunas'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'jumlah_pembayaran' => 'required|numeric|min:0|max:' . $piutang->sisa_piutang,
                'tanggal_pembayaran' => 'required|date',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $jumlahPembayaran = $request->jumlah_pembayaran;
            $piutang->jumlah_dibayar += $jumlahPembayaran;
            $piutang->sisa_piutang -= $jumlahPembayaran;

            // Update status jika sudah lunas
            if ($piutang->sisa_piutang <= 0) {
                $piutang->status = 'lunas';
                $piutang->sisa_piutang = 0;
            }

            $piutang->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran piutang berhasil dicatat',
                'data' => [
                    'jumlah_dibayar' => (float) $piutang->jumlah_dibayar,
                    'sisa_piutang' => (float) $piutang->sisa_piutang,
                    'status' => $piutang->status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error marking piutang as paid: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process POS piutang payment
     */
    public function payPosPiutang(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'jumlah_bayar' => 'required|numeric|min:0',
                'metode_bayar' => 'required|in:cash,transfer,qris',
                'tanggal_bayar' => 'required|date',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Get POS sale
            $posSale = \App\Models\PosSale::findOrFail($id);
            
            // Get piutang record
            $piutang = \App\Models\Piutang::where('id_penjualan', $posSale->id_penjualan)->first();
            
            if (!$piutang) {
                throw new \Exception('Data piutang tidak ditemukan');
            }
            
            $jumlahBayar = $request->jumlah_bayar;
            
            // Validate payment amount
            if ($jumlahBayar > $piutang->sisa_piutang) {
                throw new \Exception('Jumlah bayar melebihi sisa piutang');
            }
            
            // Update piutang
            $piutang->jumlah_dibayar += $jumlahBayar;
            $piutang->sisa_piutang -= $jumlahBayar;
            
            if ($piutang->sisa_piutang <= 0) {
                $piutang->status = 'lunas';
                $posSale->status = 'lunas';
            }
            
            $piutang->save();
            $posSale->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran piutang POS berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error paying POS piutang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get piutang detail with transaction and journal info (REALTIME from sales_invoice)
     */
    public function getPiutangDetail(Request $request, $id)
    {
        try {
            // Get sales invoice with relations
            $invoice = DB::table('sales_invoice as si')
                ->leftJoin('member as m', function($join) {
                    $join->on('si.id_member', '=', 'm.id_member')
                         ->whereNotNull('si.id_member');
                })
                ->leftJoin('prospek as p', function($join) {
                    $join->on('si.id_prospek', '=', 'p.id_prospek')
                         ->whereNotNull('si.id_prospek');
                })
                ->leftJoin('outlets as o', 'si.id_outlet', '=', 'o.id_outlet')
                ->leftJoin('penjualan as pj', 'si.id_penjualan', '=', 'pj.id_penjualan')
                ->select(
                    'si.*',
                    DB::raw('COALESCE(m.nama, p.nama, "Customer") as nama_customer'),
                    DB::raw('COALESCE(m.telepon, p.telepon, "-") as telepon'),
                    DB::raw('COALESCE(m.alamat, p.alamat, "-") as alamat'),
                    'o.nama_outlet as outlet'
                )
                ->where('si.id_sales_invoice', $id)
                ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice tidak ditemukan'
                ], 404);
            }

            // Get payment history
            $paymentHistory = DB::table('invoice_payment_history')
                ->where('id_sales_invoice', $id)
                ->orderBy('tanggal_bayar', 'desc')
                ->get();

            $totalPayments = $paymentHistory->sum('jumlah_bayar');
            $sisaTagihan = $invoice->total - $totalPayments;

            // Determine status
            $invoiceStatus = 'belum_lunas';
            if ($totalPayments >= $invoice->total) {
                $invoiceStatus = 'lunas';
            } elseif ($totalPayments > 0) {
                $invoiceStatus = 'dibayar_sebagian';
            }

            // Check if overdue
            $isOverdue = false;
            $daysOverdue = 0;
            if ($invoice->due_date && $invoiceStatus !== 'lunas') {
                $dueDate = \Carbon\Carbon::parse($invoice->due_date);
                $today = \Carbon\Carbon::today();
                if ($today->gt($dueDate)) {
                    $isOverdue = true;
                    $daysOverdue = $today->diffInDays($dueDate);
                }
            }

            // Get penjualan details if exists
            $penjualanDetails = null;
            if ($invoice->id_penjualan) {
                $penjualan = DB::table('penjualan')
                    ->where('id_penjualan', $invoice->id_penjualan)
                    ->first();

                if ($penjualan) {
                    $penjualanItems = DB::table('penjualan_detail as pd')
                        ->leftJoin('produk as pr', 'pd.id_produk', '=', 'pr.id_produk')
                        ->where('pd.id_penjualan', $invoice->id_penjualan)
                        ->select(
                            'pr.nama_produk',
                            'pd.jumlah',
                            'pd.harga',
                            'pd.diskon',
                            'pd.sub_total'
                        )
                        ->get();

                    $penjualanDetails = [
                        'id_penjualan' => $penjualan->id_penjualan,
                        'invoice_number' => $invoice->no_invoice,
                        'tanggal' => $invoice->tanggal,
                        'total_item' => $penjualanItems->sum('jumlah'),
                        'total_harga' => (float) $invoice->total,
                        'diskon' => (float) ($invoice->total_diskon ?? 0),
                        'bayar' => (float) $totalPayments,
                        'items' => $penjualanItems->map(function($item) {
                            return [
                                'nama_produk' => $item->nama_produk ?: '-',
                                'jumlah' => $item->jumlah,
                                'harga' => (float) $item->harga,
                                'diskon' => (float) ($item->diskon ?? 0),
                                'subtotal' => (float) $item->sub_total
                            ];
                        })->toArray()
                    ];
                }
            }

            // Get related journal entries (if any)
            $journalEntries = DB::table('journal_entries as je')
                ->where('reference_type', 'sales_invoice')
                ->where('reference_id', $id)
                ->get();

            $formattedJournals = [];
            foreach ($journalEntries as $journal) {
                $details = DB::table('journal_entry_details as jed')
                    ->leftJoin('chart_of_accounts as coa', 'jed.account_id', '=', 'coa.id')
                    ->where('jed.journal_entry_id', $journal->id)
                    ->select(
                        'coa.code as account_code',
                        'coa.name as account_name',
                        'jed.description',
                        'jed.debit',
                        'jed.credit'
                    )
                    ->get();

                $formattedJournals[] = [
                    'id' => $journal->id,
                    'transaction_number' => $journal->transaction_number,
                    'transaction_date' => $journal->transaction_date,
                    'description' => $journal->description,
                    'status' => $journal->status,
                    'total_debit' => (float) $journal->total_debit,
                    'total_credit' => (float) $journal->total_credit,
                    'details' => $details->map(function($detail) {
                        return [
                            'account_code' => $detail->account_code ?: '-',
                            'account_name' => $detail->account_name ?: '-',
                            'description' => $detail->description,
                            'debit' => (float) $detail->debit,
                            'credit' => (float) $detail->credit
                        ];
                    })->toArray()
                ];
            }

            // Format payment history
            $formattedPaymentHistory = $paymentHistory->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'tanggal_bayar' => $payment->tanggal_bayar,
                    'jumlah_bayar' => (float) $payment->jumlah_bayar,
                    'jenis_pembayaran' => $payment->jenis_pembayaran,
                    'nama_bank' => $payment->nama_bank,
                    'nama_pengirim' => $payment->nama_pengirim,
                    'penerima' => $payment->penerima,
                    'keterangan' => $payment->keterangan,
                    'bukti_pembayaran' => $payment->bukti_pembayaran ? asset('storage/' . $payment->bukti_pembayaran) : null
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'piutang' => [
                        'id_piutang' => $invoice->id_sales_invoice,
                        'tanggal' => $invoice->tanggal,
                        'tanggal_jatuh_tempo' => $invoice->due_date,
                        'nama_customer' => $invoice->nama_customer ?: '-',
                        'telepon' => $invoice->telepon ?: '-',
                        'alamat' => $invoice->alamat ?: '-',
                        'outlet' => $invoice->outlet ?: '-',
                        'jumlah_piutang' => (float) $invoice->total,
                        'jumlah_dibayar' => (float) $totalPayments,
                        'sisa_piutang' => (float) $sisaTagihan,
                        'status' => $invoiceStatus,
                        'is_overdue' => $isOverdue,
                        'days_overdue' => $daysOverdue,
                        'invoice_number' => $invoice->no_invoice
                    ],
                    'penjualan' => $penjualanDetails,
                    'journals' => $formattedJournals,
                    'payment_history' => $formattedPaymentHistory
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting piutang detail: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail piutang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hutang Index Page
     */
    public function hutangIndex()
    {
        return view('admin.finance.hutang.index');
    }

    /**
     * Get hutang data with filters (REALTIME from purchase_order & po_payment_history)
     */
    public function getHutangData(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $status = $request->get('status', 'all');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $search = $request->get('search');

            // Query purchase_order dengan payment history
            $query = DB::table('purchase_order as po')
                ->leftJoin('supplier as s', 'po.id_supplier', '=', 's.id_supplier')
                ->leftJoin('outlets as o', 'po.id_outlet', '=', 'o.id_outlet')
                ->select(
                    'po.id_purchase_order',
                    'po.no_po',
                    'po.tanggal',
                    'po.due_date',
                    'po.total',
                    'po.total_dibayar',
                    'po.sisa_pembayaran',
                    'po.status',
                    'po.id_outlet',
                    's.nama as nama_supplier',
                    'o.nama_outlet as outlet'
                )
                ->whereNotIn('po.status', ['permintaan_pembelian', 'request_quotation', 'dibatalkan']); // Exclude draft & cancelled

            // Filter by outlet
            if ($outletId) {
                $query->where('po.id_outlet', $outletId);
            }

            // Filter by status
            if ($status !== 'all') {
                if ($status === 'belum_lunas') {
                    $query->where('po.sisa_pembayaran', '>', 0);
                } elseif ($status === 'lunas') {
                    $query->where('po.sisa_pembayaran', '<=', 0);
                }
            }

            // Filter by date range
            if ($startDate && $endDate) {
                $query->whereBetween('po.tanggal', [$startDate, $endDate]);
            }

            // Filter by search
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('po.no_po', 'like', "%{$search}%")
                      ->orWhere('s.nama', 'like', "%{$search}%");
                });
            }

            $purchaseOrders = $query->orderBy('po.tanggal', 'desc')->get();

            // Calculate realtime summary from actual data
            $totalHutang = 0;
            $totalDibayar = 0;
            $totalSisa = 0;
            $countBelumLunas = 0;
            $countLunas = 0;
            $countOverdue = 0;

            $formattedData = [];

            foreach ($purchaseOrders as $po) {
                // Calculate realtime payment from po_payment_history
                $totalPayments = DB::table('po_payment_history')
                    ->where('id_purchase_order', $po->id_purchase_order)
                    ->sum('jumlah_pembayaran');

                $sisaPembayaran = $po->total - $totalPayments;
                
                // Determine status based on payments
                $poStatus = 'belum_lunas';
                if ($totalPayments >= $po->total) {
                    $poStatus = 'lunas';
                } elseif ($totalPayments > 0) {
                    $poStatus = 'dibayar_sebagian';
                }

                // Check if overdue
                $isOverdue = false;
                $daysOverdue = 0;
                if ($po->due_date && $poStatus !== 'lunas') {
                    $dueDate = \Carbon\Carbon::parse($po->due_date);
                    $today = \Carbon\Carbon::today();
                    if ($today->gt($dueDate)) {
                        $isOverdue = true;
                        $daysOverdue = $today->diffInDays($dueDate);
                    }
                }

                // Update summary
                $totalHutang += $po->total;
                $totalDibayar += $totalPayments;
                $totalSisa += $sisaPembayaran;
                
                if ($poStatus === 'lunas') {
                    $countLunas++;
                } else {
                    $countBelumLunas++;
                }
                
                if ($isOverdue) {
                    $countOverdue++;
                }

                $formattedData[] = [
                    'id_hutang' => $po->id_purchase_order,
                    'id_purchase_order' => $po->id_purchase_order,
                    'tanggal' => $po->tanggal,
                    'tanggal_jatuh_tempo' => $po->due_date,
                    'nama_supplier' => $po->nama_supplier ?: '-',
                    'outlet' => $po->outlet ?: '-',
                    'jumlah_hutang' => (float) $po->total,
                    'jumlah_dibayar' => (float) $totalPayments,
                    'sisa_hutang' => (float) $sisaPembayaran,
                    'status' => $poStatus,
                    'is_overdue' => $isOverdue,
                    'days_overdue' => $daysOverdue,
                    'po_number' => $po->no_po ?: '-'
                ];
            }

            $summary = [
                'total_hutang' => $totalHutang,
                'total_dibayar' => $totalDibayar,
                'total_sisa' => $totalSisa,
                'count_belum_lunas' => $countBelumLunas,
                'count_lunas' => $countLunas,
                'count_overdue' => $countOverdue
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting hutang data: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data hutang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get hutang detail with transaction and payment history (REALTIME from purchase_order)
     */
    public function getHutangDetail(Request $request, $id)
    {
        try {
            // Get purchase order with relations
            $po = DB::table('purchase_order as po')
                ->leftJoin('supplier as s', 'po.id_supplier', '=', 's.id_supplier')
                ->leftJoin('outlets as o', 'po.id_outlet', '=', 'o.id_outlet')
                ->select(
                    'po.*',
                    's.nama as nama_supplier',
                    's.telepon as telepon',
                    's.alamat as alamat',
                    'o.nama_outlet as outlet'
                )
                ->where('po.id_purchase_order', $id)
                ->first();

            if (!$po) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase Order tidak ditemukan'
                ], 404);
            }

            // Get payment history
            $paymentHistory = DB::table('po_payment_history')
                ->where('id_purchase_order', $id)
                ->orderBy('tanggal_pembayaran', 'desc')
                ->get();

            $totalPayments = $paymentHistory->sum('jumlah_pembayaran');
            $sisaPembayaran = $po->total - $totalPayments;

            // Determine status
            $poStatus = 'belum_lunas';
            if ($totalPayments >= $po->total) {
                $poStatus = 'lunas';
            } elseif ($totalPayments > 0) {
                $poStatus = 'dibayar_sebagian';
            }

            // Check if overdue
            $isOverdue = false;
            $daysOverdue = 0;
            if ($po->due_date && $poStatus !== 'lunas') {
                $dueDate = \Carbon\Carbon::parse($po->due_date);
                $today = \Carbon\Carbon::today();
                if ($today->gt($dueDate)) {
                    $isOverdue = true;
                    $daysOverdue = $today->diffInDays($dueDate);
                }
            }

            // Get PO items
            $poItems = DB::table('purchase_order_items as poi')
                ->leftJoin('produk as pr', 'poi.id_produk', '=', 'pr.id_produk')
                ->leftJoin('bahan as b', 'poi.id_bahan', '=', 'b.id_bahan')
                ->where('poi.id_purchase_order', $id)
                ->select(
                    DB::raw('COALESCE(pr.nama_produk, b.nama_bahan, "Item") as nama_item'),
                    'poi.jumlah',
                    'poi.harga',
                    'poi.diskon',
                    'poi.subtotal'
                )
                ->get();

            $poDetails = [
                'id_purchase_order' => $po->id_purchase_order,
                'po_number' => $po->no_po,
                'tanggal' => $po->tanggal,
                'total_item' => $poItems->sum('jumlah'),
                'total_harga' => (float) $po->total,
                'diskon' => (float) ($po->total_diskon ?? 0),
                'bayar' => (float) $totalPayments,
                'items' => $poItems->map(function($item) {
                    return [
                        'nama_item' => $item->nama_item ?: '-',
                        'jumlah' => $item->jumlah,
                        'harga' => (float) $item->harga,
                        'diskon' => (float) ($item->diskon ?? 0),
                        'subtotal' => (float) $item->subtotal
                    ];
                })->toArray()
            ];

            // Get related journal entries (if any)
            $journalEntries = DB::table('journal_entries as je')
                ->where('reference_type', 'purchase_order')
                ->where('reference_id', $id)
                ->get();

            $formattedJournals = [];
            foreach ($journalEntries as $journal) {
                $details = DB::table('journal_entry_details as jed')
                    ->leftJoin('chart_of_accounts as coa', 'jed.account_id', '=', 'coa.id')
                    ->where('jed.journal_entry_id', $journal->id)
                    ->select(
                        'coa.code as account_code',
                        'coa.name as account_name',
                        'jed.description',
                        'jed.debit',
                        'jed.credit'
                    )
                    ->get();

                $formattedJournals[] = [
                    'id' => $journal->id,
                    'transaction_number' => $journal->transaction_number,
                    'transaction_date' => $journal->transaction_date,
                    'description' => $journal->description,
                    'status' => $journal->status,
                    'total_debit' => (float) $journal->total_debit,
                    'total_credit' => (float) $journal->total_credit,
                    'details' => $details->map(function($detail) {
                        return [
                            'account_code' => $detail->account_code ?: '-',
                            'account_name' => $detail->account_name ?: '-',
                            'description' => $detail->description,
                            'debit' => (float) $detail->debit,
                            'credit' => (float) $detail->credit
                        ];
                    })->toArray()
                ];
            }

            // Format payment history
            $formattedPaymentHistory = $paymentHistory->map(function($payment) {
                return [
                    'id' => $payment->id_payment,
                    'tanggal_bayar' => $payment->tanggal_pembayaran,
                    'jumlah_bayar' => (float) $payment->jumlah_pembayaran,
                    'jenis_pembayaran' => $payment->jenis_pembayaran,
                    'penerima' => $payment->penerima,
                    'catatan' => $payment->catatan,
                    'bukti_pembayaran' => $payment->bukti_pembayaran ? asset('storage/' . $payment->bukti_pembayaran) : null
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'hutang' => [
                        'id_hutang' => $po->id_purchase_order,
                        'tanggal' => $po->tanggal,
                        'tanggal_jatuh_tempo' => $po->due_date,
                        'nama_supplier' => $po->nama_supplier ?: '-',
                        'telepon' => $po->telepon ?: '-',
                        'alamat' => $po->alamat ?: '-',
                        'outlet' => $po->outlet ?: '-',
                        'jumlah_hutang' => (float) $po->total,
                        'jumlah_dibayar' => (float) $totalPayments,
                        'sisa_hutang' => (float) $sisaPembayaran,
                        'status' => $poStatus,
                        'is_overdue' => $isOverdue,
                        'days_overdue' => $daysOverdue,
                        'po_number' => $po->no_po
                    ],
                    'purchase_order' => $poDetails,
                    'journals' => $formattedJournals,
                    'payment_history' => $formattedPaymentHistory
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting hutang detail: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail hutang: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== RAB Management ====================

    public function rabIndex()
    {
        return view('admin.finance.rab.index');
    }

    public function rabData(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id');
            $bookId = $request->get('book_id');
            $search = $request->get('search', '');
            $status = $request->get('status', 'all');
            $hasProduct = $request->get('has_product', 'all');

            $query = \App\Models\RabTemplate::with(['details', 'products'])
                ->when($outletId, function($q) use ($outletId) {
                    $q->where('outlet_id', $outletId);
                })
                ->when($bookId, function($q) use ($bookId) {
                    $q->where('book_id', $bookId);
                })
                ->when($search, function($q) use ($search) {
                    $q->where(function($query) use ($search) {
                        $query->where('nama_template', 'like', "%{$search}%")
                            ->orWhere('deskripsi', 'like', "%{$search}%");
                    });
                })
                ->orderBy('created_at', 'desc');

            $rabs = $query->get()->map(function($rab) {
                $totalBudget = $rab->details->sum('budget');
                $totalApproved = $rab->details->sum('nilai_disetujui');
                $totalSpent = $rab->details->sum('realisasi_pemakaian');
                
                // Calculate status
                $status = $this->calculateRabStatus($rab, $totalApproved);
                
                return [
                    'id' => $rab->id_rab,
                    'created_at' => $rab->created_at->format('Y-m-d'),
                    'name' => $rab->nama_template,
                    'description' => $rab->deskripsi ?? '',
                    'components' => $rab->details->map(function($detail) {
                        return [
                            'uraian' => $detail->nama_komponen ?? $detail->item,
                            'qty' => (float) ($detail->qty ?? $detail->jumlah ?? 1),
                            'satuan' => $detail->satuan ?? 'pcs',
                            'harga_satuan' => (float) ($detail->harga_satuan ?? $detail->harga ?? 0),
                            'biaya' => (float) ($detail->biaya ?? $detail->budget ?? 0)
                        ];
                    })->toArray(),
                    'budget_total' => (float) $totalBudget,
                    'approved_value' => (float) $totalApproved,
                    'spends' => $rab->details->map(function($detail) {
                        return [
                            'desc' => $detail->nama_komponen,
                            'amount' => (float) ($detail->realisasi_pemakaian ?? 0)
                        ];
                    })->filter(function($spend) {
                        return $spend['amount'] > 0;
                    })->values()->toArray(),
                    'status' => $status,
                    'has_product' => $rab->products->count() > 0,
                    'outlet_id' => $rab->outlet_id,
                    'book_id' => $rab->book_id,
                    'details' => $rab->details->map(function($detail) {
                        return [
                            'id' => $detail->id ?? $detail->id_rab_detail,
                            'nama_komponen' => $detail->nama_komponen ?? $detail->item,
                            'jumlah' => (float) ($detail->jumlah ?? $detail->qty ?? 1),
                            'satuan' => $detail->satuan ?? 'pcs',
                            'harga_satuan' => (float) ($detail->harga_satuan ?? $detail->harga ?? 0),
                            'budget' => (float) ($detail->budget ?? $detail->subtotal ?? 0),
                            'nilai_disetujui' => (float) ($detail->nilai_disetujui ?? 0),
                            'realisasi_pemakaian' => (float) ($detail->realisasi_pemakaian ?? 0),
                            'disetujui' => (bool) ($detail->disetujui ?? false),
                            'deskripsi' => $detail->deskripsi ?? ''
                        ];
                    })->toArray()
                ];
            });

            // Apply frontend filters
            if ($status !== 'all') {
                $rabs = $rabs->filter(function($rab) use ($status) {
                    return $rab['status'] === $status;
                });
            }

            if ($hasProduct !== 'all') {
                $rabs = $rabs->filter(function($rab) use ($hasProduct) {
                    return $hasProduct === 'YES' ? $rab['has_product'] : !$rab['has_product'];
                });
            }

            return response()->json([
                'success' => true,
                'data' => array_values($rabs->toArray())
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching RAB data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data RAB: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeRab(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Log incoming request
            \Log::info('=== STORE RAB REQUEST ===');
            \Log::info('Request data:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'book_id' => 'required|exists:accounting_books,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'created_at' => 'required|date',
                'components' => 'nullable|array',
                'budget_total' => 'required|numeric|min:0',
                'approved_value' => 'nullable|numeric|min:0',
                'status' => 'required|in:DRAFT,PENDING_APPROVAL,APPROVED,APPROVED_WITH_REV,TRANSFERRED,REJECTED',
                'has_product' => 'nullable|boolean',
                'spends' => 'nullable|array',
                'details' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create RAB template
            $rab = \App\Models\RabTemplate::create([
                'outlet_id' => $request->outlet_id,
                'book_id' => $request->book_id,
                'nama_template' => $request->name,
                'deskripsi' => $request->description,
                'total_biaya' => $request->budget_total,
                'is_active' => true,
                'created_at' => $request->created_at
            ]);
            
            \Log::info('RAB Template created:', ['id_rab' => $rab->id_rab]);
            
            // Debug: Check what we have
            \Log::info('Checking data:', [
                'has_details' => $request->has('details'),
                'details_is_array' => is_array($request->details),
                'details_count' => is_array($request->details) ? count($request->details) : 0,
                'has_components' => $request->has('components'),
                'components_is_array' => is_array($request->components),
                'components_count' => is_array($request->components) ? count($request->components) : 0
            ]);

            // Create details from components or details array
            if ($request->has('details') && is_array($request->details) && count($request->details) > 0) {
                \Log::info('Creating details from details array:', ['count' => count($request->details)]);
                
                foreach ($request->details as $detail) {
                    $namaKomponen = $detail['nama_komponen'] ?? '';
                    
                    \App\Models\RabDetail::create([
                        'id_rab' => $rab->id_rab,
                        'item' => $namaKomponen,
                        'nama_komponen' => $namaKomponen,
                        'deskripsi' => $detail['deskripsi'] ?? '',
                        'qty' => $detail['jumlah'] ?? 1,
                        'jumlah' => $detail['jumlah'] ?? 1,
                        'satuan' => $detail['satuan'] ?? 'pcs',
                        'harga' => $detail['harga_satuan'] ?? 0,
                        'harga_satuan' => $detail['harga_satuan'] ?? 0,
                        'subtotal' => $detail['budget'] ?? 0,
                        'budget' => $detail['budget'] ?? 0,
                        'biaya' => $detail['budget'] ?? 0,
                        'nilai_disetujui' => $detail['nilai_disetujui'] ?? 0,
                        'realisasi_pemakaian' => $detail['realisasi_pemakaian'] ?? 0,
                        'disetujui' => $detail['disetujui'] ?? false
                    ]);
                }
            } elseif ($request->has('components') && is_array($request->components) && count($request->components) > 0) {
                // Create details from components (support both old string format and new object format)
                foreach ($request->components as $component) {
                    // Support new format: {uraian, qty, satuan, harga_satuan, biaya}
                    if (is_array($component) && isset($component['uraian'])) {
                        $uraian = $component['uraian'];
                        $qty = $component['qty'] ?? 1;
                        $satuan = $component['satuan'] ?? 'pcs';
                        $harga_satuan = $component['harga_satuan'] ?? 0;
                        $biaya = $component['biaya'] ?? ($qty * $harga_satuan);
                        
                        if (!empty($uraian)) {
                            \App\Models\RabDetail::create([
                                'id_rab' => $rab->id_rab,
                                'item' => $uraian,
                                'nama_komponen' => $uraian,
                                'deskripsi' => $component['deskripsi'] ?? '',
                                'qty' => $qty,
                                'jumlah' => $qty,
                                'satuan' => $satuan,
                                'harga' => $harga_satuan,
                                'harga_satuan' => $harga_satuan,
                                'subtotal' => $biaya,
                                'budget' => $biaya,
                                'biaya' => $biaya,
                                'nilai_disetujui' => 0,
                                'realisasi_pemakaian' => 0,
                                'disetujui' => false
                            ]);
                        }
                    }
                    // Support old format: string
                    elseif (is_string($component) && !empty($component)) {
                        $budgetPerComponent = count($request->components) > 0 
                            ? $request->budget_total / count($request->components) 
                            : 0;
                        
                        \App\Models\RabDetail::create([
                            'id_rab' => $rab->id_rab,
                            'item' => $component,
                            'nama_komponen' => $component,
                            'deskripsi' => '',
                            'qty' => 1,
                            'jumlah' => 1,
                            'satuan' => 'pcs',
                            'harga' => $budgetPerComponent,
                            'harga_satuan' => $budgetPerComponent,
                            'subtotal' => $budgetPerComponent,
                            'budget' => $budgetPerComponent,
                            'biaya' => $budgetPerComponent,
                            'nilai_disetujui' => 0,
                            'realisasi_pemakaian' => 0,
                            'disetujui' => false
                        ]);
                    }
                }
            }

            // Handle spends (realisasi)
            if ($request->has('spends') && is_array($request->spends)) {
                foreach ($request->spends as $spend) {
                    if (!empty($spend['desc']) && $spend['amount'] > 0) {
                        // Find matching detail or create new one
                        $detail = \App\Models\RabDetail::where('id_rab', $rab->id_rab)
                            ->where('nama_komponen', $spend['desc'])
                            ->first();
                        
                        if ($detail) {
                            $detail->update([
                                'realisasi_pemakaian' => $spend['amount']
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            
            \Log::info('RAB created successfully:', [
                'id_rab' => $rab->id_rab,
                'details_count' => \App\Models\RabDetail::where('id_rab', $rab->id_rab)->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => $rab,
                'message' => 'RAB berhasil dibuat'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating RAB: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat RAB: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateRab(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $rab = \App\Models\RabTemplate::find($id);
            
            if (!$rab) {
                return response()->json([
                    'success' => false,
                    'message' => 'RAB tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'book_id' => 'required|exists:accounting_books,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'created_at' => 'required|date',
                'budget_total' => 'required|numeric|min:0',
                'approved_value' => 'nullable|numeric|min:0',
                'status' => 'required|in:DRAFT,APPROVED_ALL,APPROVED_WITH_REV,TRANSFERRED,REJECTED',
                'has_product' => 'nullable|boolean',
                'components' => 'nullable|array',
                'spends' => 'nullable|array',
                'details' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update RAB template
            $rab->update([
                'outlet_id' => $request->outlet_id,
                'book_id' => $request->book_id,
                'nama_template' => $request->name,
                'deskripsi' => $request->description,
                'total_biaya' => $request->budget_total,
                'created_at' => $request->created_at
            ]);

            // Delete old details and create new ones
            \App\Models\RabDetail::where('id_rab', $rab->id_rab)->delete();

            // Create new details
            if ($request->has('details') && is_array($request->details) && count($request->details) > 0) {
                \Log::info('Updating details from details array:', ['count' => count($request->details)]);
                
                foreach ($request->details as $detail) {
                    $namaKomponen = $detail['nama_komponen'] ?? '';
                    
                    \App\Models\RabDetail::create([
                        'id_rab' => $rab->id_rab,
                        'item' => $namaKomponen,
                        'nama_komponen' => $namaKomponen,
                        'deskripsi' => $detail['deskripsi'] ?? '',
                        'qty' => $detail['jumlah'] ?? 1,
                        'jumlah' => $detail['jumlah'] ?? 1,
                        'satuan' => $detail['satuan'] ?? 'pcs',
                        'harga' => $detail['harga_satuan'] ?? 0,
                        'harga_satuan' => $detail['harga_satuan'] ?? 0,
                        'subtotal' => $detail['budget'] ?? 0,
                        'budget' => $detail['budget'] ?? 0,
                        'biaya' => $detail['budget'] ?? 0,
                        'nilai_disetujui' => $detail['nilai_disetujui'] ?? 0,
                        'realisasi_pemakaian' => $detail['realisasi_pemakaian'] ?? 0,
                        'disetujui' => $detail['disetujui'] ?? false
                    ]);
                }
            } elseif ($request->has('components') && is_array($request->components) && count($request->components) > 0) {
                // Create details from components (support both old string format and new object format)
                foreach ($request->components as $component) {
                    // Support new format: {uraian, qty, satuan, harga_satuan, biaya}
                    if (is_array($component) && isset($component['uraian'])) {
                        $uraian = $component['uraian'];
                        $qty = $component['qty'] ?? 1;
                        $satuan = $component['satuan'] ?? 'pcs';
                        $harga_satuan = $component['harga_satuan'] ?? 0;
                        $biaya = $component['biaya'] ?? ($qty * $harga_satuan);
                        
                        if (!empty($uraian)) {
                            \App\Models\RabDetail::create([
                                'id_rab' => $rab->id_rab,
                                'item' => $uraian,
                                'nama_komponen' => $uraian,
                                'deskripsi' => $component['deskripsi'] ?? '',
                                'qty' => $qty,
                                'jumlah' => $qty,
                                'satuan' => $satuan,
                                'harga' => $harga_satuan,
                                'harga_satuan' => $harga_satuan,
                                'subtotal' => $biaya,
                                'budget' => $biaya,
                                'biaya' => $biaya,
                                'nilai_disetujui' => 0,
                                'realisasi_pemakaian' => 0,
                                'disetujui' => false
                            ]);
                        }
                    }
                    // Support old format: string
                    elseif (is_string($component) && !empty($component)) {
                        $budgetPerComponent = count($request->components) > 0 
                            ? $request->budget_total / count($request->components) 
                            : 0;
                        
                        \App\Models\RabDetail::create([
                            'id_rab' => $rab->id_rab,
                            'item' => $component,
                            'nama_komponen' => $component,
                            'deskripsi' => '',
                            'qty' => 1,
                            'jumlah' => 1,
                            'satuan' => 'pcs',
                            'harga' => $budgetPerComponent,
                            'harga_satuan' => $budgetPerComponent,
                            'subtotal' => $budgetPerComponent,
                            'budget' => $budgetPerComponent,
                            'biaya' => $budgetPerComponent,
                            'nilai_disetujui' => 0,
                            'realisasi_pemakaian' => 0,
                            'disetujui' => false
                        ]);
                    }
                }
            }

            // Handle spends (realisasi)
            if ($request->has('spends') && is_array($request->spends)) {
                \Log::info('Processing spends:', ['count' => count($request->spends)]);
                
                foreach ($request->spends as $spend) {
                    if (!empty($spend['desc']) && $spend['amount'] > 0) {
                        $detail = \App\Models\RabDetail::where('id_rab', $rab->id_rab)
                            ->where('nama_komponen', $spend['desc'])
                            ->first();
                        
                        if ($detail) {
                            $oldRealisasi = $detail->realisasi_pemakaian ?? 0;
                            $newRealisasi = $spend['amount'];
                            
                            \Log::info('Updating realisasi:', [
                                'detail_id' => $detail->id,
                                'old' => $oldRealisasi,
                                'new' => $newRealisasi
                            ]);
                            
                            $detail->update([
                                'realisasi_pemakaian' => $newRealisasi
                            ]);
                            
                            // Save to history if changed
                            if ($newRealisasi != $oldRealisasi) {
                                $tambahan = $newRealisasi - $oldRealisasi;
                                
                                \Log::info('Saving to history:', [
                                    'tambahan' => $tambahan,
                                    'keterangan' => $spend['desc']
                                ]);
                                
                                if ($tambahan != 0) {
                                    DB::table('rab_realisasi_history')->insert([
                                        'id_rab_detail' => $detail->id,
                                        'jumlah' => abs($tambahan),
                                        'keterangan' => $spend['desc'] . ($tambahan > 0 ? ' (Penambahan)' : ' (Pengurangan)'),
                                        'user_id' => auth()->id(),
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ]);
                                    
                                    \Log::info('History saved successfully');
                                }
                            }
                        } else {
                            \Log::warning('Detail not found for spend:', ['desc' => $spend['desc']]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $rab,
                'message' => 'RAB berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating RAB: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui RAB: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteRab($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $rab = \App\Models\RabTemplate::find($id);
            
            if (!$rab) {
                return response()->json([
                    'success' => false,
                    'message' => 'RAB tidak ditemukan'
                ], 404);
            }

            // Set id_rab to NULL in keberangkatan table if this RAB is linked
            \App\Models\Keberangkatan::where('id_rab', $rab->id_rab)
                ->update(['id_rab' => null]);

            // Delete related details
            \App\Models\RabDetail::where('id_rab', $rab->id_rab)->delete();
            
            // Detach products
            $rab->products()->detach();
            
            // Delete RAB
            $rab->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'RAB berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting RAB: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus RAB: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRealisasiHistory($id): JsonResponse
    {
        try {
            $rab = \App\Models\RabTemplate::find($id);
            
            if (!$rab) {
                return response()->json([
                    'success' => false,
                    'message' => 'RAB tidak ditemukan'
                ], 404);
            }

            // Get all details for this RAB
            $detailIds = \App\Models\RabDetail::where('id_rab', $rab->id_rab)
                ->pluck('id');

            // Get history
            $history = DB::table('rab_realisasi_history')
                ->whereIn('id_rab_detail', $detailIds)
                ->leftJoin('users', 'rab_realisasi_history.user_id', '=', 'users.id')
                ->leftJoin('rab_detail', 'rab_realisasi_history.id_rab_detail', '=', 'rab_detail.id')
                ->select(
                    'rab_realisasi_history.*',
                    'users.name as user_name',
                    'rab_detail.nama_komponen'
                )
                ->orderBy('rab_realisasi_history.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting realisasi history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil history: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveRealisasiSimple(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            \Log::info('=== SAVE REALISASI SIMPLE REQUEST ===');
            \Log::info('RAB ID:', ['id' => $id]);
            \Log::info('Request data:', $request->all());
            
            $rab = \App\Models\RabTemplate::find($id);
            
            if (!$rab) {
                return response()->json([
                    'success' => false,
                    'message' => 'RAB tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'realisasi' => 'required|array',
                'realisasi.*.keterangan' => 'required|string',
                'realisasi.*.jumlah' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get first detail as default (or create one if not exists)
            $detail = \App\Models\RabDetail::where('id_rab', $rab->id_rab)->first();
            
            if (!$detail) {
                // Create default detail if not exists
                $detail = \App\Models\RabDetail::create([
                    'id_rab' => $rab->id_rab,
                    'item' => 'Realisasi Umum',
                    'nama_komponen' => 'Realisasi Umum',
                    'deskripsi' => 'Komponen untuk realisasi umum',
                    'qty' => 1,
                    'jumlah' => 1,
                    'satuan' => 'pcs',
                    'harga' => 0,
                    'harga_satuan' => 0,
                    'subtotal' => 0,
                    'budget' => 0,
                    'biaya' => 0,
                    'nilai_disetujui' => 0,
                    'realisasi_pemakaian' => 0,
                    'disetujui' => false
                ]);
            }

            $totalSaved = 0;
            $totalJumlah = 0;
            $realisasiIds = [];
            
            foreach ($request->realisasi as $item) {
                $jumlah = $item['jumlah'];
                $keterangan = $item['keterangan'];
                
                \Log::info('Processing realisasi:', [
                    'keterangan' => $keterangan,
                    'jumlah' => $jumlah
                ]);
                
                // Update total realisasi_pemakaian
                $detail->increment('realisasi_pemakaian', $jumlah);
                
                // Save to history and get ID
                $historyId = DB::table('rab_realisasi_history')->insertGetId([
                    'id_rab_detail' => $detail->id,
                    'jumlah' => $jumlah,
                    'keterangan' => $keterangan,
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $realisasiIds[] = $historyId;
                
                \Log::info('History saved:', [
                    'id' => $historyId,
                    'id_rab_detail' => $detail->id,
                    'jumlah' => $jumlah
                ]);
                
                $totalSaved++;
                $totalJumlah += $jumlah;
            }

            DB::commit();
            
            \Log::info('Realisasi saved successfully:', [
                'total_records' => $totalSaved,
                'total_jumlah' => $totalJumlah,
                'realisasi_ids' => $realisasiIds
            ]);

            return response()->json([
                'success' => true,
                'message' => "Realisasi berhasil disimpan ({$totalSaved} item, total: Rp " . number_format($totalJumlah, 0, ',', '.') . ")",
                'realisasi_ids' => $realisasiIds
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving realisasi simple: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan realisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveRealisasiPerItem(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            \Log::info('=== SAVE REALISASI PER ITEM REQUEST ===');
            \Log::info('RAB ID:', ['id' => $id]);
            \Log::info('Request data:', $request->all());
            
            $rab = \App\Models\RabTemplate::find($id);
            
            if (!$rab) {
                return response()->json([
                    'success' => false,
                    'message' => 'RAB tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'realisasi' => 'required|array',
                'realisasi.*.id_rab_detail' => 'required|exists:rab_detail,id',
                'realisasi.*.nama_komponen' => 'required|string',
                'realisasi.*.keterangan' => 'required|string',
                'realisasi.*.jumlah' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $totalSaved = 0;
            $totalJumlah = 0;
            $realisasiIds = [];
            
            foreach ($request->realisasi as $item) {
                $detailId = $item['id_rab_detail'];
                $jumlah = $item['jumlah'];
                $keterangan = $item['keterangan'];
                $namaKomponen = $item['nama_komponen'];
                
                // Find the detail
                $detail = \App\Models\RabDetail::find($detailId);
                
                if (!$detail) {
                    \Log::warning('Detail not found:', ['id' => $detailId]);
                    continue;
                }
                
                // Verify detail belongs to this RAB
                if ($detail->id_rab != $rab->id_rab) {
                    \Log::warning('Detail does not belong to this RAB:', [
                        'detail_id' => $detailId,
                        'detail_rab_id' => $detail->id_rab,
                        'expected_rab_id' => $rab->id_rab
                    ]);
                    continue;
                }
                
                \Log::info('Processing realisasi for detail:', [
                    'detail_id' => $detailId,
                    'nama_komponen' => $namaKomponen,
                    'keterangan' => $keterangan,
                    'jumlah' => $jumlah
                ]);
                
                // Update total realisasi_pemakaian
                $detail->increment('realisasi_pemakaian', $jumlah);
                
                // Save to history and get ID
                $historyId = DB::table('rab_realisasi_history')->insertGetId([
                    'id_rab_detail' => $detail->id,
                    'jumlah' => $jumlah,
                    'keterangan' => $keterangan,
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $realisasiIds[] = $historyId;
                
                \Log::info('History saved:', [
                    'id' => $historyId,
                    'id_rab_detail' => $detail->id,
                    'nama_komponen' => $namaKomponen,
                    'jumlah' => $jumlah
                ]);
                
                $totalSaved++;
                $totalJumlah += $jumlah;
            }

            DB::commit();
            
            \Log::info('Realisasi per item saved successfully:', [
                'total_records' => $totalSaved,
                'total_jumlah' => $totalJumlah,
                'realisasi_ids' => $realisasiIds
            ]);

            return response()->json([
                'success' => true,
                'message' => "Realisasi berhasil disimpan ({$totalSaved} item, total: Rp " . number_format($totalJumlah, 0, ',', '.') . ")",
                'realisasi_ids' => $realisasiIds
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving realisasi per item: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan realisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateRabStatus($rab, $totalApproved): string
    {
        // Check if transferred
        if ($rab->details->whereNotNull('bukti_transfer')->count() > 0) {
            return 'TRANSFERRED';
        }

        // Check if has approvals
        $hasApprovals = $rab->details->where('disetujui', true)->count() > 0 || $totalApproved > 0;

        if (!$hasApprovals) {
            return 'DRAFT';
        }

        // Check approval status
        $allApproved = $rab->details->where('disetujui', false)->count() === 0;
        $totalBudget = $rab->details->sum('budget');
        $budgetEqualsApproved = abs($totalBudget - $totalApproved) < 0.01;
        
        if ($allApproved) {
            return $budgetEqualsApproved ? 'APPROVED_ALL' : 'APPROVED_WITH_REV';
        }
        
        return 'DRAFT';
    }

    public function saveRealisasi(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            \Log::info('=== SAVE REALISASI REQUEST ===');
            \Log::info('RAB ID:', ['id' => $id]);
            \Log::info('Request data:', $request->all());
            
            $rab = \App\Models\RabTemplate::find($id);
            
            if (!$rab) {
                return response()->json([
                    'success' => false,
                    'message' => 'RAB tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'details' => 'required|array',
                'details.*.id' => 'required',
                'details.*.tambahan_realisasi' => 'required|numeric|min:0',
                'details.*.keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $totalSaved = 0;
            
            foreach ($request->details as $detailData) {
                $detail = \App\Models\RabDetail::where('id_rab', $rab->id_rab)
                    ->where('id', $detailData['id'])
                    ->first();
                    
                if ($detail) {
                    $oldRealisasi = $detail->realisasi_pemakaian ?? 0;
                    $tambahan = $detailData['tambahan_realisasi'];
                    $newRealisasi = $oldRealisasi + $tambahan;
                    
                    \Log::info('Processing detail:', [
                        'detail_id' => $detail->id,
                        'nama_komponen' => $detail->nama_komponen,
                        'old_realisasi' => $oldRealisasi,
                        'tambahan' => $tambahan,
                        'new_realisasi' => $newRealisasi
                    ]);
                    
                    $detail->update([
                        'realisasi_pemakaian' => $newRealisasi
                    ]);
                    
                    // Save to history
                    if ($tambahan > 0) {
                        DB::table('rab_realisasi_history')->insert([
                            'id_rab_detail' => $detail->id,
                            'jumlah' => $tambahan,
                            'keterangan' => $detailData['keterangan'] ?? $detail->nama_komponen,
                            'user_id' => auth()->id(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        \Log::info('History saved:', [
                            'id_rab_detail' => $detail->id,
                            'jumlah' => $tambahan
                        ]);
                        
                        $totalSaved++;
                    }
                } else {
                    \Log::warning('Detail not found:', ['id' => $detailData['id']]);
                }
            }

            DB::commit();
            
            \Log::info('Realisasi saved successfully:', ['total_records' => $totalSaved]);

            return response()->json([
                'success' => true,
                'message' => 'Realisasi berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving realisasi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan realisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== EXPENSE MANAGEMENT ====================

    /**
     * Display expense management page
     */
    public function biayaIndex()
    {
        return view('admin.finance.biaya.index');
    }

    /**
     * Get expenses data with filters
     */
    public function expensesData(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id');
            $rabId = $request->get('rab_id', 'all');
            $category = $request->get('category', 'all');
            $status = $request->get('status', 'all');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $search = $request->get('search', '');

            // Debug: Log request parameters
            Log::info('Expenses Data Request', [
                'outlet_id' => $outletId,
                'book_id' => $bookId,
                'rab_id' => $rabId,
                'category' => $category,
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'search' => $search
            ]);

            $query = Expense::with(['outlet', 'book', 'account', 'approver', 'rab'])
                ->where('outlet_id', $outletId)
                ->when($bookId, function($q) use ($bookId) {
                    $q->where('book_id', $bookId);
                })
                ->when($rabId === 'no_budget', function($q) {
                    $q->whereNull('rab_id');
                })
                ->when($rabId !== 'all' && $rabId !== 'no_budget' && !empty($rabId), function($q) use ($rabId) {
                    $q->where('rab_id', $rabId);
                })
                ->when($category !== 'all' && !empty($category), function($q) use ($category) {
                    $q->where('category', $category);
                })
                ->when($status !== 'all' && !empty($status), function($q) use ($status) {
                    $q->where('status', $status);
                })
                ->when($dateFrom, function($q) use ($dateFrom) {
                    $q->where('expense_date', '>=', $dateFrom);
                })
                ->when($dateTo, function($q) use ($dateTo) {
                    $q->where('expense_date', '<=', $dateTo);
                })
                ->when($search, function($q) use ($search) {
                    $q->where(function($query) use ($search) {
                        $query->where('reference_number', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                })
                ->orderBy('expense_date', 'desc')
                ->orderBy('created_at', 'desc');

            $expenses = $query->get();

            // Debug: Log query result
            Log::info('Expenses Query Result', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
                'count' => $expenses->count()
            ]);

            // Format data for frontend
            $formattedExpenses = $expenses->map(function($expense) {
                return [
                    'id' => $expense->id,
                    'reference' => $expense->reference_number,
                    'date_formatted' => $expense->expense_date->format('d M Y'),
                    'expense_date' => $expense->expense_date->format('Y-m-d'),
                    'category' => $expense->category,
                    'category_badge' => $this->getCategoryBadge($expense->category),
                    'description' => $expense->description,
                    'account_code' => $expense->account->code ?? '-',
                    'account_name' => $expense->account->name ?? '-',
                    'account_id' => $expense->account_id,
                    'cash_account_id' => $expense->cash_account_id,
                    'rab_id' => $expense->rab_id,
                    'rab_name' => $expense->rab->nama_template ?? null,
                    'realisasi_id' => $expense->realisasi_id,
                    'is_auto_generated' => $expense->is_auto_generated,
                    'amount' => floatval($expense->amount),
                    'amount_formatted' => 'Rp ' . number_format($expense->amount, 0, ',', '.'),
                    'status' => $expense->status,
                    'status_badge' => $this->getStatusBadge($expense->status),
                    'approved_by' => $expense->approver->name ?? null,
                    'approved_at' => $expense->approved_at ? $expense->approved_at->format('d M Y H:i') : null,
                    'notes' => $expense->notes,
                    'attachment' => $expense->attachment
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedExpenses
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading expenses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data biaya: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get expense statistics
     */
    public function expensesStats(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id');
            $rabId = $request->get('rab_id', 'all');

            $query = Expense::where('outlet_id', $outletId)
                ->when($bookId, function($q) use ($bookId) {
                    $q->where('book_id', $bookId);
                });
            
            // Apply RAB filter
            if ($rabId === 'no_budget') {
                $query->whereNull('rab_id');
            } elseif ($rabId !== 'all') {
                $query->where('rab_id', $rabId);
            }

            // Total this month
            $totalMonthly = (clone $query)
                ->whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->where('status', 'approved')
                ->sum('amount');

            // Total this period (based on filter or default to this month)
            $totalThisPeriod = $totalMonthly;

            // Categories count
            $categoriesCount = (clone $query)
                ->distinct('category')
                ->count('category');

            // Top category
            $topCategory = (clone $query)
                ->where('status', 'approved')
                ->select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->orderBy('total', 'desc')
                ->first();

            // Approved and pending count
            $approvedCount = (clone $query)
                ->where('status', 'approved')
                ->count();

            $pendingCount = (clone $query)
                ->where('status', 'pending')
                ->count();

            // Calculate budget from RAB if specific RAB selected
            $budgetTotal = 0;
            $budgetRemaining = 0;
            
            if ($rabId !== 'all' && $rabId !== 'no_budget') {
                $rab = \App\Models\RabTemplate::find($rabId);
                if ($rab) {
                    $budgetTotal = $rab->details->sum('nilai_disetujui') ?: $rab->details->sum('budget');
                    $budgetRemaining = $budgetTotal - $totalThisPeriod;
                }
            } else {
                // For 'all' or 'no_budget', use dummy value or sum all RABs
                $budgetTotal = 100000000;
                $budgetRemaining = $budgetTotal - $totalThisPeriod;
            }

            // Utilization
            $utilization = $budgetTotal > 0 ? round(($totalThisPeriod / $budgetTotal) * 100) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'totalThisPeriod' => floatval($totalThisPeriod),
                    'budgetRemaining' => floatval($budgetRemaining),
                    'totalMonthly' => floatval($totalMonthly),
                    'categoriesCount' => $categoriesCount,
                    'topCategory' => $topCategory->category ?? 'Operasional',
                    'approvedCount' => $approvedCount,
                    'pendingCount' => $pendingCount,
                    'utilization' => $utilization
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading expense stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik biaya'
            ], 500);
        }
    }

    /**
     * Store new expense
     */
    public function storeExpense(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'book_id' => 'required|exists:accounting_books,id',
                'account_id' => 'required|exists:chart_of_accounts,id',
                'cash_account_id' => 'required|exists:chart_of_accounts,id',
                'expense_date' => 'required|date',
                'category' => 'required|string',
                'description' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'rab_id' => 'nullable|exists:rab_templates,id_rab',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $expense = Expense::create([
                'outlet_id' => $request->outlet_id,
                'book_id' => $request->book_id,
                'rab_id' => $request->rab_id,
                'account_id' => $request->account_id,
                'cash_account_id' => $request->cash_account_id,
                'reference_number' => Expense::generateReferenceNumber(),
                'expense_date' => $request->expense_date,
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->amount,
                'status' => 'pending',
                'notes' => $request->notes
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Biaya berhasil ditambahkan',
                'data' => $expense
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing expense: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan biaya: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update expense
     */
    public function updateExpense(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $expense = Expense::findOrFail($id);

            // Only allow update if status is pending
            if ($expense->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya biaya dengan status pending yang dapat diubah'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'book_id' => 'required|exists:accounting_books,id',
                'account_id' => 'required|exists:chart_of_accounts,id',
                'cash_account_id' => 'required|exists:chart_of_accounts,id',
                'expense_date' => 'required|date',
                'category' => 'required|string',
                'description' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'rab_id' => 'nullable|exists:rab_templates,id_rab',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $expense->update([
                'book_id' => $request->book_id,
                'rab_id' => $request->rab_id,
                'account_id' => $request->account_id,
                'cash_account_id' => $request->cash_account_id,
                'expense_date' => $request->expense_date,
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->amount,
                'notes' => $request->notes
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Biaya berhasil diperbarui',
                'data' => $expense
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating expense: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui biaya: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete expense
     */
    public function deleteExpense($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $expense = Expense::findOrFail($id);

            // Only allow delete if status is pending
            if ($expense->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya biaya dengan status pending yang dapat dihapus'
                ], 422);
            }

            $expense->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Biaya berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting expense: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus biaya: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve expense and create journal entry
     */
    public function approveExpense($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $expense = Expense::with(['account', 'outlet'])->findOrFail($id);

            if ($expense->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Biaya sudah diproses sebelumnya'
                ], 422);
            }

            // Update expense status
            $expense->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            // Get general journal book for this outlet
            $book = AccountingBook::where('outlet_id', $expense->outlet_id)
                ->where('type', 'general')
                ->where('status', 'active')
                ->first();

            if (!$book) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Buku jurnal umum tidak ditemukan untuk outlet ini'
                ], 422);
            }

            // Validate cash account exists
            if (!$expense->cash_account_id) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun kas/bank belum ditentukan untuk biaya ini'
                ], 422);
            }

            $cashAccount = ChartOfAccount::find($expense->cash_account_id);
            
            if (!$cashAccount) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun kas/bank tidak ditemukan'
                ], 422);
            }

            // Generate transaction number
            $lastJournal = JournalEntry::whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->first();
            $number = $lastJournal ? (int) substr($lastJournal->transaction_number, -4) + 1 : 1;
            $transactionNumber = 'JNL-' . date('Ymd') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

            // Create journal entry
            $journal = JournalEntry::create([
                'outlet_id' => $expense->outlet_id,
                'book_id' => $book->id,
                'transaction_number' => $transactionNumber,
                'transaction_date' => $expense->expense_date,
                'description' => 'Pembayaran Biaya: ' . $expense->description,
                'reference_number' => $expense->reference_number,
                'total_debit' => $expense->amount,
                'total_credit' => $expense->amount,
                'status' => 'posted',
                'posted_at' => now(),
                'notes' => 'Auto-generated from expense #' . $expense->id
            ]);

            // Create journal entry details
            // Debit: Expense Account
            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $expense->account_id,
                'description' => $expense->description,
                'debit' => $expense->amount,
                'credit' => 0
            ]);

            // Credit: Cash/Bank Account
            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $cashAccount->id,
                'description' => 'Pembayaran ' . $expense->description,
                'debit' => 0,
                'credit' => $expense->amount
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Biaya berhasil disetujui dan jurnal entry telah dibuat',
                'journal_id' => $journal->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving expense: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui biaya: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject expense
     */
    public function rejectExpense($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $expense = Expense::findOrFail($id);

            if ($expense->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Biaya sudah diproses sebelumnya'
                ], 422);
            }

            $expense->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Biaya berhasil ditolak'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error rejecting expense: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak biaya: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export expenses to XLSX
     */
    public function exportExpensesXLSX(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id');
            $category = $request->get('category', 'all');
            $status = $request->get('status', 'all');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $search = $request->get('search', '');

            $expenses = $this->getExpenseExportData($outletId, $bookId, $category, $status, $dateFrom, $dateTo, $search);

            $outlet = Outlet::find($outletId);
            
            $filters = [
                'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'status' => $status,
                'category' => $category
            ];

            $exportService = new FinanceExportService();
            return $exportService->exportToXLSX('expenses', $expenses, $filters);

        } catch (\Exception $e) {
            \Log::error('Error exporting expenses to XLSX: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export expenses to PDF
     */
    public function exportExpensesPDF(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id');
            $category = $request->get('category', 'all');
            $status = $request->get('status', 'all');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $search = $request->get('search', '');

            $expenses = $this->getExpenseExportData($outletId, $bookId, $category, $status, $dateFrom, $dateTo, $search);

            $outlet = Outlet::find($outletId);
            
            $filters = [
                'company_name' => config('app.name', 'Nama Perusahaan'),
                'outlet_name' => $outlet->nama_outlet ?? 'Semua Outlet',
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'status' => $status,
                'category' => $category
            ];

            $exportService = new FinanceExportService();
            return $exportService->exportToPDF('expenses', $expenses, $filters);

        } catch (\Exception $e) {
            \Log::error('Error exporting expenses to PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get expense data for export
     */
    private function getExpenseExportData($outletId, $bookId, $category, $status, $dateFrom, $dateTo, $search)
    {
        $query = Expense::with(['outlet', 'book', 'account', 'approver'])
            ->where('outlet_id', $outletId)
            ->when($bookId, function($q) use ($bookId) {
                $q->where('book_id', $bookId);
            })
            ->when($category !== 'all', function($q) use ($category) {
                $q->where('category', $category);
            })
            ->when($status !== 'all', function($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($dateFrom, function($q) use ($dateFrom) {
                $q->where('expense_date', '>=', $dateFrom);
            })
            ->when($dateTo, function($q) use ($dateTo) {
                $q->where('expense_date', '<=', $dateTo);
            })
            ->when($search, function($q) use ($search) {
                $q->where(function($query) use ($search) {
                    $query->where('reference_number', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('expense_date', 'asc')
            ->orderBy('reference_number', 'asc');

        $expenses = $query->get();

        $exportData = [];
        foreach ($expenses as $expense) {
            $exportData[] = (object)[
                'expense_date' => $expense->expense_date->format('Y-m-d'),
                'reference_number' => $expense->reference_number,
                'category' => $expense->category,
                'account_code' => $expense->account->code ?? '-',
                'account_name' => $expense->account->name ?? '-',
                'description' => $expense->description,
                'amount' => floatval($expense->amount),
                'status' => $expense->status,
                'approved_by' => $expense->approver->name ?? '-',
                'approved_at' => $expense->approved_at ? $expense->approved_at->format('Y-m-d H:i') : '-'
            ];
        }

        return $exportData;
    }

    /**
     * Helper: Get category badge HTML
     */
    private function getCategoryBadge($category): string
    {
        $badges = [
            'operational' => '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">Operasional</span>',
            'administrative' => '<span class="px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">Administratif</span>',
            'marketing' => '<span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">Pemasaran</span>',
            'maintenance' => '<span class="px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-800">Pemeliharaan</span>',
        ];

        return $badges[$category] ?? '<span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">' . ucfirst($category) . '</span>';
    }

    /**
     * Get expense chart data
     */
    public function expensesChartData(Request $request): JsonResponse
    {
        try {
            $outletId = $request->get('outlet_id', auth()->user()->outlet_id ?? 1);
            $bookId = $request->get('book_id');
            $rabId = $request->get('rab_id', 'all');
            $period = $request->get('period', 'monthly'); // monthly, quarterly, yearly

            // Get trend data (last 6 months)
            $trendData = [];
            $trendLabels = [];
            
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $trendLabels[] = $date->format('M');
                
                $query = Expense::where('outlet_id', $outletId)
                    ->where('status', 'approved')
                    ->whereYear('expense_date', $date->year)
                    ->whereMonth('expense_date', $date->month)
                    ->when($bookId, function($q) use ($bookId) {
                        $q->where('book_id', $bookId);
                    });
                
                // Apply RAB filter
                if ($rabId === 'no_budget') {
                    $query->whereNull('rab_id');
                } elseif ($rabId !== 'all') {
                    $query->where('rab_id', $rabId);
                }
                
                $total = $query->sum('amount');
                $trendData[] = floatval($total);
            }

            // Get category data
            $query = Expense::where('outlet_id', $outletId)
                ->where('status', 'approved')
                ->when($bookId, function($q) use ($bookId) {
                    $q->where('book_id', $bookId);
                });
            
            // Apply RAB filter
            if ($rabId === 'no_budget') {
                $query->whereNull('rab_id');
            } elseif ($rabId !== 'all') {
                $query->where('rab_id', $rabId);
            }
            
            $categoryData = $query->select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->get();

            $categoryLabels = [];
            $categoryValues = [];
            $categoryColors = [
                'operational' => '#ef4444',
                'administrative' => '#8b5cf6',
                'marketing' => '#3b82f6',
                'maintenance' => '#f59e0b',
            ];

            foreach ($categoryData as $cat) {
                $categoryLabels[] = $this->getCategoryName($cat->category);
                $categoryValues[] = floatval($cat->total);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'trend' => [
                        'labels' => $trendLabels,
                        'data' => $trendData
                    ],
                    'category' => [
                        'labels' => $categoryLabels,
                        'data' => $categoryValues,
                        'colors' => array_values($categoryColors)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading expense chart data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data chart'
            ], 500);
        }
    }

    /**
     * Helper: Get category name in Indonesian
     */
    private function getCategoryName($category): string
    {
        return match($category) {
            'operational' => 'Operasional',
            'administrative' => 'Administratif',
            'marketing' => 'Pemasaran',
            'maintenance' => 'Pemeliharaan',
            default => ucfirst($category)
        };
    }

    /**
     * Helper: Get status badge HTML
     */
    private function getStatusBadge($status): string
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">Menunggu</span>',
            'approved' => '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Disetujui</span>',
            'rejected' => '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">Ditolak</span>',
        ];

        return $badges[$status] ?? '<span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">' . ucfirst($status) . '</span>';
    }

    /**
     * Create expense from RAB realisasi
     */
    public function createExpenseFromRealisasi(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'realisasi_id' => 'required|exists:rab_realisasi_history,id',
                'outlet_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
                'book_id' => 'nullable|exists:accounting_books,id',
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string',
                'expense_date' => 'required|date',
                'rab_id' => 'nullable|exists:rab_template,id_rab'
            ]);

            // Auto-detect expense account (search for "Biaya" account)
            $expenseAccount = ChartOfAccount::where('outlet_id', $validated['outlet_id'])
                ->where('name', 'LIKE', '%Biaya%')
                ->where('status', 'active')
                ->first();

            if (!$expenseAccount) {
                // Fallback to any expense type account
                $expenseAccount = ChartOfAccount::where('outlet_id', $validated['outlet_id'])
                    ->where('type', 'expense')
                    ->where('status', 'active')
                    ->first();
            }

            // Auto-detect cash account
            $cashAccount = ChartOfAccount::where('outlet_id', $validated['outlet_id'])
                ->where(function($q) {
                    $q->where('name', 'LIKE', '%Kas%')
                      ->orWhere('name', 'LIKE', '%Bank%');
                })
                ->where('status', 'active')
                ->first();

            if (!$expenseAccount || !$cashAccount) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun biaya atau kas/bank tidak ditemukan. Silakan buat akun terlebih dahulu.'
                ], 422);
            }

            // Generate reference number
            $lastExpense = Expense::whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->first();
            $number = $lastExpense ? (int) substr($lastExpense->reference_number, -4) + 1 : 1;
            $referenceNumber = 'EXP-' . date('Ymd') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

            // Create expense
            $expense = Expense::create([
                'outlet_id' => $validated['outlet_id'],
                'book_id' => $validated['book_id'] ?? null,
                'account_id' => $expenseAccount->id,
                'cash_account_id' => $cashAccount->id,
                'reference_number' => $referenceNumber,
                'expense_date' => $validated['expense_date'],
                'category' => 'operational',
                'description' => $validated['description'],
                'amount' => $validated['amount'],
                'status' => 'pending',
                'notes' => 'Auto-generated from RAB realisasi',
                'rab_id' => $validated['rab_id'] ?? null,
                'realisasi_id' => $validated['realisasi_id'],
                'is_auto_generated' => true
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Biaya berhasil dibuat dari realisasi RAB',
                'data' => $expense
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating expense from realisasi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat biaya: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Close accounting book
     */
    public function closeBook($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $book = AccountingBook::find($id);
            
            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku akuntansi tidak ditemukan'
                ], 404);
            }

            // Validasi: buku harus aktif
            if ($book->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya buku dengan status aktif yang dapat ditutup'
                ], 422);
            }

            // Validasi: buku belum terkunci
            if ($book->is_locked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku sudah terkunci'
                ], 422);
            }

            // Validasi: semua jurnal harus sudah diposting
            $draftEntries = JournalEntry::where('book_id', $book->id)
                ->where('status', 'draft')
                ->count();

            if ($draftEntries > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Masih ada {$draftEntries} entri jurnal dengan status draft. Posting semua entri terlebih dahulu."
                ], 422);
            }

            // Update status buku menjadi closed dan locked
            $book->update([
                'status' => 'closed',
                'is_locked' => true,
                'closed_at' => now(),
                'closed_by' => auth()->id()
            ]);

            // Log aktivitas
            \Log::info("Buku akuntansi ditutup", [
                'book_id' => $book->id,
                'book_name' => $book->name,
                'closed_by' => auth()->id(),
                'closed_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Buku \"{$book->name}\" berhasil ditutup dan dikunci"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error closing book: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup buku: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate book report
     */
    public function generateBookReport($id)
    {
        try {
            $book = AccountingBook::with(['outlet', 'journalEntries.journalEntryDetails.account'])
                ->find($id);
            
            if (!$book) {
                abort(404, 'Buku akuntansi tidak ditemukan');
            }

            // Hitung ringkasan buku
            $totalEntries = $book->journalEntries->count();
            $totalDebit = $book->journalEntries->sum('total_debit');
            $totalCredit = $book->journalEntries->sum('total_credit');
            
            // Hitung saldo per akun
            $accountBalances = [];
            foreach ($book->journalEntries as $entry) {
                foreach ($entry->journalEntryDetails as $detail) {
                    $accountId = $detail->account_id;
                    if (!isset($accountBalances[$accountId])) {
                        $accountBalances[$accountId] = [
                            'account' => $detail->account,
                            'debit' => 0,
                            'credit' => 0,
                            'balance' => 0
                        ];
                    }
                    $accountBalances[$accountId]['debit'] += $detail->debit;
                    $accountBalances[$accountId]['credit'] += $detail->credit;
                    $accountBalances[$accountId]['balance'] = $accountBalances[$accountId]['debit'] - $accountBalances[$accountId]['credit'];
                }
            }

            // Sort by account code
            uasort($accountBalances, function($a, $b) {
                return strcmp($a['account']->code, $b['account']->code);
            });

            // Get company settings dengan outlet ID yang benar
            $companySettings = $this->getCompanySettingsForOutlet((int) $book->outlet_id);

            $data = [
                'book' => $book,
                'totalEntries' => $totalEntries,
                'totalDebit' => $totalDebit,
                'totalCredit' => $totalCredit,
                'accountBalances' => $accountBalances,
                'generatedAt' => now(),
                'companySettings' => $companySettings
            ];

            $pdf = Pdf::loadView('admin.finance.buku.report-pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'Laporan_Buku_' . str_replace(' ', '_', $book->name) . '_' . date('Y-m-d') . '.pdf';
            
            return $pdf->stream($filename);

        } catch (\Exception $e) {
            \Log::error('Error generating book report: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            abort(500, 'Gagal generate laporan buku: ' . $e->getMessage());
        }
    }

    /**
     * Get company settings for specific outlet
     */
    private function getCompanySettingsForOutlet(int $outletId): array
    {
        try {
            $settings = \App\Models\CompanySetting::getOrCreateForOutlet($outletId);
            
            return [
                'company_name' => $settings->company_name ?? config('app.name'),
                'company_code' => $settings->company_code ?? '',
                'company_address' => $settings->company_address ?? '',
                'company_phone' => $settings->company_phone ?? '',
                'company_email' => $settings->company_email ?? '',
                'company_website' => $settings->company_website ?? '',
                'logo_url' => $settings->logo_url ?? null,
                'favicon_url' => $settings->favicon_url ?? null,
                'npwp' => $settings->npwp ?? '',
                'nib' => $settings->nib ?? '',
                'siup' => $settings->siup ?? '',
                'tdp' => $settings->tdp ?? '',
                'bank_name' => $settings->bank_name ?? '',
                'bank_account_number' => $settings->bank_account_number ?? '',
                'bank_account_name' => $settings->bank_account_name ?? '',
                'currency' => $settings->currency ?? 'IDR',
                'timezone' => $settings->timezone ?? 'Asia/Jakarta',
                'date_format' => $settings->date_format ?? 'd/m/Y',
                'time_format' => $settings->time_format ?? 'H:i',
                'tax_rate' => $settings->tax_rate ?? 11.00,
                'formatted_address' => $settings->formatted_address ?? '',
                'formatted_phone' => $settings->formatted_phone ?? '',
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting company settings: ' . $e->getMessage());
            
            // Return default settings if error
            return [
                'company_name' => config('app.name'),
                'company_code' => '',
                'company_address' => '',
                'company_phone' => '',
                'company_email' => '',
                'company_website' => '',
                'logo_url' => null,
                'favicon_url' => null,
                'npwp' => '',
                'nib' => '',
                'siup' => '',
                'tdp' => '',
                'bank_name' => '',
                'bank_account_number' => '',
                'bank_account_name' => '',
                'currency' => 'IDR',
                'timezone' => 'Asia/Jakarta',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'tax_rate' => 11.00,
                'formatted_address' => '',
                'formatted_phone' => '',
            ];
        }
    }

    /**
     * Approve fixed asset and post acquisition journal entry
     */
    public function approveFixedAsset($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $fixedAsset = \App\Models\FixedAsset::find($id);
            
            if (!$fixedAsset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktiva tetap tidak ditemukan'
                ], 404);
            }

            // Check if asset can be approved
            if (!$fixedAsset->canBeApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aktiva tetap hanya dapat diapprove jika statusnya draft'
                ], 422);
            }

            // Get accounting book for fixed assets
            $book = null;
            
            // First, try to use book_id from fixed asset if provided
            if ($fixedAsset->book_id) {
                $book = AccountingBook::where('id', $fixedAsset->book_id)
                    ->where('outlet_id', $fixedAsset->outlet_id)
                    ->where('status', 'active')
                    ->first();
                    
                if (!$book) {
                    \Log::warning('Fixed asset book_id not found or not active', [
                        'book_id' => $fixedAsset->book_id,
                        'outlet_id' => $fixedAsset->outlet_id
                    ]);
                }
            }
            
            // If no book from fixed asset, try to find general book first
            if (!$book) {
                $book = AccountingBook::where('outlet_id', $fixedAsset->outlet_id)
                    ->where('status', 'active')
                    ->where(function($query) {
                        $query->where('type', 'general')
                              ->orWhere('type', 'inventory'); // Allow inventory book as fallback
                    })
                    ->first();
            }

            // If still not found, try any active book
            if (!$book) {
                $book = AccountingBook::where('outlet_id', $fixedAsset->outlet_id)
                    ->where('status', 'active')
                    ->first();
            }

            if (!$book) {
                \Log::error('No active accounting book found for approval', [
                    'outlet_id' => $fixedAsset->outlet_id,
                    'book_id_from_asset' => $fixedAsset->book_id,
                    'available_books' => AccountingBook::where('outlet_id', $fixedAsset->outlet_id)->get()->toArray()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada buku akuntansi aktif untuk outlet ini. Silakan buat buku akuntansi terlebih dahulu.'
                ], 422);
            }
            
            \Log::info('Using accounting book for fixed asset approval', [
                'book_id' => $book->id,
                'book_name' => $book->name,
                'outlet_id' => $book->outlet_id
            ]);

            // Generate transaction number
            $transactionNumber = 'FA-ACQ-' . $fixedAsset->code;

            // Create journal entry for acquisition
            $journalEntry = JournalEntry::create([
                'book_id' => $book->id,
                'outlet_id' => $fixedAsset->outlet_id,
                'transaction_number' => $transactionNumber,
                'transaction_date' => $fixedAsset->acquisition_date,
                'description' => 'Perolehan Aktiva Tetap - ' . $fixedAsset->name,
                'reference_type' => 'fixed_asset_acquisition',
                'reference_number' => $fixedAsset->code,
                'status' => 'posted',
                'total_debit' => $fixedAsset->acquisition_cost,
                'total_credit' => $fixedAsset->acquisition_cost,
                'posted_at' => now()
            ]);

            // Create journal entry details
            // Debit: Asset Account
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $fixedAsset->asset_account_id,
                'debit' => $fixedAsset->acquisition_cost,
                'credit' => 0,
                'description' => 'Perolehan ' . $fixedAsset->name
            ]);

            // Credit: Payment Account
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $fixedAsset->payment_account_id,
                'debit' => 0,
                'credit' => $fixedAsset->acquisition_cost,
                'description' => 'Pembayaran ' . $fixedAsset->name
            ]);

            // Update account balances
            $assetAccount = ChartOfAccount::find($fixedAsset->asset_account_id);
            $paymentAccount = ChartOfAccount::find($fixedAsset->payment_account_id);
            
            $assetAccount->updateBalance($fixedAsset->acquisition_cost);
            $paymentAccount->updateBalance(-$fixedAsset->acquisition_cost);

            // Update book total entries
            $book->incrementEntries();

            // Update fixed asset status to active
            $fixedAsset->update([
                'status' => 'active',
                'book_id' => $book->id // Ensure book_id is set
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $fixedAsset->load(['outlet', 'book', 'assetAccount', 'paymentAccount']),
                'journal_entry' => $journalEntry,
                'message' => 'Aktiva tetap berhasil diapprove dan jurnal telah diposting'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving fixed asset: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal approve aktiva tetap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk post depreciation entries
     */
    public function bulkPostDepreciations(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'depreciation_ids' => 'required|array|min:1',
                'depreciation_ids.*' => 'required|exists:fixed_asset_depreciations,id',
                'book_id' => 'required|exists:accounting_books,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $depreciationIds = $request->depreciation_ids;
            $bookId = $request->book_id;
            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            // Validate book exists and is active
            $book = AccountingBook::where('id', $bookId)
                ->where('status', 'active')
                ->first();

            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku akuntansi tidak ditemukan atau tidak aktif'
                ], 422);
            }

            foreach ($depreciationIds as $id) {
                try {
                    $depreciation = FixedAssetDepreciation::with([
                        'fixedAsset.outlet',
                        'fixedAsset.depreciationExpenseAccount',
                        'fixedAsset.accumulatedDepreciationAccount'
                    ])->find($id);

                    if (!$depreciation) {
                        $errors[] = "Depreciation ID {$id} not found";
                        $failedCount++;
                        continue;
                    }

                    // Only post draft depreciations
                    if ($depreciation->status !== 'draft') {
                        $errors[] = "Depreciation ID {$id} is not in draft status";
                        $failedCount++;
                        continue;
                    }

                    // Validate book belongs to same outlet as fixed asset
                    if ($book->outlet_id !== $depreciation->fixedAsset->outlet_id) {
                        $errors[] = "Book outlet mismatch for depreciation ID {$id}";
                        $failedCount++;
                        continue;
                    }

                    // Validate user has access to this outlet
                    $this->validateOutletAccess($depreciation->fixedAsset->outlet_id);

                    // Generate transaction number
                    $transactionNumber = 'FA-DEP-' . $depreciation->fixedAsset->code . '-' . $depreciation->period;

                    // Create journal entry
                    $journalEntry = JournalEntry::create([
                        'book_id' => $book->id,
                        'outlet_id' => $depreciation->fixedAsset->outlet_id,
                        'transaction_number' => $transactionNumber,
                        'transaction_date' => $depreciation->depreciation_date,
                        'description' => 'Penyusutan Aktiva Tetap - ' . $depreciation->fixedAsset->name . ' - Periode ' . $depreciation->period,
                        'reference_type' => 'fixed_asset_depreciation',
                        'reference_number' => $depreciation->fixedAsset->code . '-' . $depreciation->period,
                        'status' => 'posted',
                        'total_debit' => $depreciation->amount,
                        'total_credit' => $depreciation->amount,
                        'posted_at' => now()
                    ]);

                    // Create journal entry details
                    // Debit: Depreciation Expense
                    JournalEntryDetail::create([
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $depreciation->fixedAsset->depreciation_expense_account_id,
                        'debit' => $depreciation->amount,
                        'credit' => 0,
                        'description' => 'Beban penyusutan ' . $depreciation->fixedAsset->name . ' periode ' . $depreciation->period
                    ]);

                    // Credit: Accumulated Depreciation
                    JournalEntryDetail::create([
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $depreciation->fixedAsset->accumulated_depreciation_account_id,
                        'debit' => 0,
                        'credit' => $depreciation->amount,
                        'description' => 'Akumulasi penyusutan ' . $depreciation->fixedAsset->name . ' periode ' . $depreciation->period
                    ]);

                    // Update depreciation status
                    $depreciation->update([
                        'status' => 'posted',
                        'journal_entry_id' => $journalEntry->id
                    ]);

                    // Update account balances
                    $expenseAccount = ChartOfAccount::find($depreciation->fixedAsset->depreciation_expense_account_id);
                    $accumulatedAccount = ChartOfAccount::find($depreciation->fixedAsset->accumulated_depreciation_account_id);

                    if ($expenseAccount) {
                        $expenseAccount->updateBalance($depreciation->amount);
                    }

                    if ($accumulatedAccount) {
                        $accumulatedAccount->updateBalance($depreciation->amount);
                    }

                    // Update fixed asset accumulated depreciation and book value
                    $fixedAsset = $depreciation->fixedAsset;
                    $fixedAsset->increment('accumulated_depreciation', $depreciation->amount);
                    $fixedAsset->decrement('book_value', $depreciation->amount);

                    // Update book total entries
                    $book->incrementEntries();

                    $successCount++;

                } catch (\Exception $e) {
                    \Log::error("Error posting depreciation ID {$id}: " . $e->getMessage());
                    $errors[] = "Error posting depreciation ID {$id}: " . $e->getMessage();
                    $failedCount++;
                }
            }

            DB::commit();

            $message = "Bulk posting selesai: {$successCount} berhasil";
            if ($failedCount > 0) {
                $message .= ", {$failedCount} gagal";
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'success_count' => $successCount,
                    'failed_count' => $failedCount,
                    'errors' => $errors,
                    'book_name' => $book->name
                ],
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in bulk post depreciations: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan bulk posting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete depreciation entries
     */
    public function bulkDeleteDepreciations(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'depreciation_ids' => 'required|array|min:1',
                'depreciation_ids.*' => 'required|exists:fixed_asset_depreciations,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $depreciationIds = $request->depreciation_ids;
            $deletedCount = 0;
            $errors = [];

            foreach ($depreciationIds as $id) {
                try {
                    $depreciation = FixedAssetDepreciation::with([
                        'fixedAsset',
                        'journalEntry.journalEntryDetails'
                    ])->find($id);

                    if (!$depreciation) {
                        $errors[] = "Depreciation ID {$id} not found";
                        continue;
                    }

                    // If depreciation is posted, reverse the journal entry first
                    if ($depreciation->status === 'posted' && $depreciation->journal_entry_id) {
                        $journalEntry = $depreciation->journalEntry;
                        
                        if ($journalEntry) {
                            // Reverse account balances
                            foreach ($journalEntry->journalEntryDetails as $detail) {
                                $account = ChartOfAccount::find($detail->account_id);
                                if ($account) {
                                    // Reverse the balance change
                                    $balanceChange = $detail->debit - $detail->credit;
                                    $account->updateBalance(-$balanceChange);
                                }
                            }

                            // Delete journal entry details
                            JournalEntryDetail::where('journal_entry_id', $journalEntry->id)->delete();
                            
                            // Delete journal entry
                            $journalEntry->delete();

                            // Update fixed asset accumulated depreciation and book value
                            $fixedAsset = $depreciation->fixedAsset;
                            $fixedAsset->accumulated_depreciation -= $depreciation->amount;
                            $fixedAsset->book_value = $fixedAsset->acquisition_cost - $fixedAsset->accumulated_depreciation;
                            $fixedAsset->save();

                            // Update book total entries
                            if ($journalEntry->book) {
                                $journalEntry->book->decrementEntries();
                            }
                        }
                    }

                    // Delete the depreciation record
                    $depreciation->delete();
                    $deletedCount++;

                } catch (\Exception $e) {
                    $errors[] = "Error deleting depreciation ID {$id}: " . $e->getMessage();
                    Log::error("Error in bulk delete depreciation ID {$id}: " . $e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'deleted_count' => $deletedCount,
                    'total_requested' => count($depreciationIds),
                    'errors' => $errors
                ],
                'message' => "Bulk delete selesai. {$deletedCount} riwayat penyusutan berhasil dihapus."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk delete depreciations: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan bulk delete: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export piutang to Excel
     */
    public function exportPiutangExcel(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $status = $request->get('status', 'all');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $search = $request->get('search');

            // Get piutang data using same logic as getPiutangData
            $data = $this->getPiutangDataForExport($request);

            return Excel::download(
                new \App\Exports\PiutangExport($data),
                'piutang_' . date('Y-m-d_His') . '.xlsx'
            );
        } catch (\Exception $e) {
            \Log::error('Error exporting piutang to Excel: ' . $e->getMessage());
            return back()->with('error', 'Gagal export data piutang');
        }
    }

    /**
     * Export piutang to PDF
     */
    public function exportPiutangPdf(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $status = $request->get('status', 'all');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $search = $request->get('search');

            // Get piutang data using same logic as getPiutangData
            $data = $this->getPiutangDataForExport($request);

            $pdf = \PDF::loadView('admin.finance.piutang.pdf', [
                'piutangData' => $data['data'],
                'summary' => $data['summary'],
                'filters' => [
                    'outlet_id' => $outletId,
                    'status' => $status,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ])->setPaper('a4', 'landscape');

            return $pdf->download('piutang_' . date('Y-m-d_His') . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Error exporting piutang to PDF: ' . $e->getMessage());
            return back()->with('error', 'Gagal export PDF piutang');
        }
    }

    /**
     * Helper method to get piutang data for export
     */
    private function getPiutangDataForExport(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $status = $request->get('status', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');

        // Query 1: sales_invoice dengan payment history
        $query = DB::table('sales_invoice as si')
            ->leftJoin('member as m', function($join) {
                $join->on('si.id_member', '=', 'm.id_member')
                     ->whereNotNull('si.id_member');
            })
            ->leftJoin('prospek as p', function($join) {
                $join->on('si.id_prospek', '=', 'p.id_prospek')
                     ->whereNotNull('si.id_prospek');
            })
            ->leftJoin('outlets as o', 'si.id_outlet', '=', 'o.id_outlet')
            ->select(
                'si.id_sales_invoice',
                'si.no_invoice',
                'si.tanggal',
                'si.due_date',
                'si.total',
                'si.total_dibayar',
                'si.sisa_tagihan',
                'si.status',
                'si.id_penjualan',
                'si.id_outlet',
                DB::raw('COALESCE(m.nama, p.nama, "Customer") as nama_customer'),
                'o.nama_outlet as outlet'
            )
            ->where('si.status', '!=', 'draft')
            ->where('si.status', '!=', 'dibatalkan');

        if ($outletId) {
            $query->where('si.id_outlet', $outletId);
        }

        if ($status !== 'all') {
            if ($status === 'belum_lunas') {
                $query->whereIn('si.status', ['menunggu', 'dibayar_sebagian']);
            } elseif ($status === 'lunas') {
                $query->where('si.status', 'lunas');
            }
        }

        if ($startDate && $endDate) {
            $query->whereDate('si.tanggal', '>=', $startDate)
                  ->whereDate('si.tanggal', '<=', $endDate);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('si.no_invoice', 'like', "%{$search}%")
                  ->orWhere('m.nama', 'like', "%{$search}%")
                  ->orWhere('p.nama', 'like', "%{$search}%");
            });
        }

        $invoices = $query->orderBy('si.tanggal', 'desc')->get();

        // Query 2: POS sales
        $posQuery = DB::table('pos_sales as ps')
            ->leftJoin('member as m', 'ps.id_member', '=', 'm.id_member')
            ->leftJoin('outlets as o', 'ps.id_outlet', '=', 'o.id_outlet')
            ->leftJoin('piutang as pt', 'ps.id_penjualan', '=', 'pt.id_penjualan')
            ->select(
                'ps.id as id_pos_sale',
                'ps.no_transaksi',
                'ps.tanggal',
                'pt.tanggal_jatuh_tempo as due_date',
                'ps.total',
                'pt.jumlah_dibayar as total_dibayar',
                'pt.sisa_piutang',
                'pt.status',
                'ps.id_penjualan',
                'ps.id_outlet',
                DB::raw('COALESCE(m.nama, "Pelanggan Umum") as nama_customer'),
                'o.nama_outlet as outlet'
            )
            ->where('ps.is_bon', true);

        if ($outletId) {
            $posQuery->where('ps.id_outlet', $outletId);
        }

        if ($status !== 'all') {
            if ($status === 'belum_lunas') {
                $posQuery->where('pt.status', 'belum_lunas');
            } elseif ($status === 'lunas') {
                $posQuery->where('pt.status', 'lunas');
            }
        }

        if ($startDate && $endDate) {
            $posQuery->whereDate('ps.tanggal', '>=', $startDate)
                     ->whereDate('ps.tanggal', '<=', $endDate);
        }

        if ($search) {
            $posQuery->where(function($q) use ($search) {
                $q->where('ps.no_transaksi', 'like', "%{$search}%")
                  ->orWhere('m.nama', 'like', "%{$search}%");
            });
        }

        $posSales = $posQuery->orderBy('ps.tanggal', 'desc')->get();

        // Calculate summary
        $totalPiutang = 0;
        $totalDibayar = 0;
        $totalSisa = 0;
        $countBelumLunas = 0;
        $countLunas = 0;
        $countOverdue = 0;

        $formattedData = [];

        // Process sales invoices
        foreach ($invoices as $invoice) {
            $totalPayments = DB::table('invoice_payment_history')
                ->where('id_sales_invoice', $invoice->id_sales_invoice)
                ->sum('jumlah_bayar');

            $sisaTagihan = $invoice->total - $totalPayments;
            
            $invoiceStatus = 'belum_lunas';
            if ($totalPayments >= $invoice->total) {
                $invoiceStatus = 'lunas';
            } elseif ($totalPayments > 0) {
                $invoiceStatus = 'dibayar_sebagian';
            }

            $isOverdue = false;
            $daysOverdue = 0;
            if ($invoice->due_date && $invoiceStatus !== 'lunas') {
                $dueDate = \Carbon\Carbon::parse($invoice->due_date);
                $today = \Carbon\Carbon::today();
                if ($today->gt($dueDate)) {
                    $isOverdue = true;
                    $daysOverdue = $today->diffInDays($dueDate);
                }
            }

            $totalPiutang += $invoice->total;
            $totalDibayar += $totalPayments;
            $totalSisa += $sisaTagihan;
            
            if ($invoiceStatus === 'lunas') {
                $countLunas++;
            } else {
                $countBelumLunas++;
            }
            
            if ($isOverdue) {
                $countOverdue++;
            }

            $formattedData[] = [
                'tanggal' => $invoice->tanggal,
                'tanggal_jatuh_tempo' => $invoice->due_date,
                'nama_customer' => $invoice->nama_customer ?: '-',
                'outlet' => $invoice->outlet ?: '-',
                'jumlah_piutang' => (float) $invoice->total,
                'jumlah_dibayar' => (float) $totalPayments,
                'sisa_piutang' => (float) $sisaTagihan,
                'status' => $invoiceStatus,
                'is_overdue' => $isOverdue,
                'days_overdue' => $daysOverdue,
                'invoice_number' => $invoice->no_invoice ?: '-',
                'source' => 'invoice'
            ];
        }

        // Process POS sales
        foreach ($posSales as $posSale) {
            $sisaTagihan = $posSale->sisa_piutang ?? $posSale->total;
            $totalPayments = ($posSale->total_dibayar ?? 0);
            
            $invoiceStatus = $posSale->status ?? 'belum_lunas';
            
            $isOverdue = false;
            $daysOverdue = 0;
            if ($posSale->due_date && $invoiceStatus !== 'lunas') {
                $dueDate = \Carbon\Carbon::parse($posSale->due_date);
                $today = \Carbon\Carbon::today();
                if ($today->gt($dueDate)) {
                    $isOverdue = true;
                    $daysOverdue = $today->diffInDays($dueDate);
                }
            }

            $totalPiutang += $posSale->total;
            $totalDibayar += $totalPayments;
            $totalSisa += $sisaTagihan;
            
            if ($invoiceStatus === 'lunas') {
                $countLunas++;
            } else {
                $countBelumLunas++;
            }
            
            if ($isOverdue) {
                $countOverdue++;
            }

            $formattedData[] = [
                'tanggal' => $posSale->tanggal,
                'tanggal_jatuh_tempo' => $posSale->due_date,
                'nama_customer' => $posSale->nama_customer ?: '-',
                'outlet' => $posSale->outlet ?: '-',
                'jumlah_piutang' => (float) $posSale->total,
                'jumlah_dibayar' => (float) $totalPayments,
                'sisa_piutang' => (float) $sisaTagihan,
                'status' => $invoiceStatus,
                'is_overdue' => $isOverdue,
                'days_overdue' => $daysOverdue,
                'invoice_number' => $posSale->no_transaksi ?: '-',
                'source' => 'pos'
            ];
        }

        // Sort by date descending
        usort($formattedData, function($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        $summary = [
            'total_piutang' => $totalPiutang,
            'total_dibayar' => $totalDibayar,
            'total_sisa' => $totalSisa,
            'count_belum_lunas' => $countBelumLunas,
            'count_lunas' => $countLunas,
            'count_overdue' => $countOverdue
        ];

        return [
            'data' => $formattedData,
            'summary' => $summary
        ];
    }

}
