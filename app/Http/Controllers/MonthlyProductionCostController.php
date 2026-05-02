<?php

namespace App\Http\Controllers;

use App\Models\MonthlyProductionCost;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MonthlyProductionCostController extends Controller
{
    public function data(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            
            if (!$outletId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Outlet ID diperlukan'
                ], 400);
            }

            // Get current month stats
            $currentMonth = now()->month;
            $currentYear = now()->year;
            $daysInMonth = now()->daysInMonth;
            $currentDay = now()->day;

            // Get current month cost data
            $currentCost = MonthlyProductionCost::where('outlet_id', $outletId)
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->first();

            $totalCost = $currentCost ? $currentCost->total_cost : 0;
            $averageDaily = $totalCost > 0 ? $totalCost / $daysInMonth : 0;
            $projected = $averageDaily * $daysInMonth;

            // Prepare current month data with detailed costs
            $currentData = [
                'total_cost' => $totalCost,
                'average_daily' => $averageDaily,
                'projected' => $projected
            ];

            // Add detailed costs if current month data exists
            if ($currentCost) {
                $currentData = array_merge($currentData, [
                    'electricity_cost' => $currentCost->electricity_cost,
                    'water_cost' => $currentCost->water_cost,
                    'fuel_cost' => $currentCost->fuel_cost,
                    'office_salary_cost' => $currentCost->office_salary_cost,
                    'other_costs' => $currentCost->other_costs,
                    'notes' => $currentCost->notes,
                    'year' => $currentCost->year,
                    'month' => $currentCost->month,
                    'period' => \Carbon\Carbon::createFromDate($currentCost->year, $currentCost->month, 1)->format('F Y')
                ]);
            }

            // Get 12 months history
            $history = MonthlyProductionCost::where('outlet_id', $outletId)
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get()
                ->map(function($item) {
                    return [
                        'period' => \Carbon\Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                        'total_cost' => $item->total_cost,
                        'electricity_cost' => $item->electricity_cost,
                        'water_cost' => $item->water_cost,
                        'fuel_cost' => $item->fuel_cost,
                        'office_salary_cost' => $item->office_salary_cost,
                        'other_costs' => $item->other_costs,
                        'notes' => $item->notes
                    ];
                });

            return response()->json([
                'success' => true,
                'current' => $currentData,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data biaya bulanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $year = $request->get('year', now()->year);
            $month = $request->get('month');

            $query = MonthlyProductionCost::with(['outlet', 'creator', 'updater'])
                ->byOutlet($outletId);

            if ($year) {
                $query->where('year', $year);
            }

            if ($month) {
                $query->where('month', $month);
            }

            $costs = $query->latest()->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $costs->items(),
                'pagination' => [
                    'current_page' => $costs->currentPage(),
                    'last_page' => $costs->lastPage(),
                    'per_page' => $costs->perPage(),
                    'total' => $costs->total()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data biaya bulanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2050',
            'electricity_cost' => 'required|numeric|min:0',
            'water_cost' => 'required|numeric|min:0',
            'fuel_cost' => 'required|numeric|min:0',
            'office_salary_cost' => 'required|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ], [
            'outlet_id.required' => 'Outlet harus dipilih',
            'outlet_id.exists' => 'Outlet tidak valid',
            'month.required' => 'Bulan harus dipilih',
            'month.integer' => 'Bulan harus berupa angka',
            'month.min' => 'Bulan minimal 1',
            'month.max' => 'Bulan maksimal 12',
            'year.required' => 'Tahun harus diisi',
            'year.integer' => 'Tahun harus berupa angka',
            'year.min' => 'Tahun minimal 2020',
            'year.max' => 'Tahun maksimal 2050',
            'electricity_cost.required' => 'Biaya listrik harus diisi',
            'electricity_cost.numeric' => 'Biaya listrik harus berupa angka',
            'electricity_cost.min' => 'Biaya listrik tidak boleh negatif',
            'water_cost.required' => 'Biaya air harus diisi',
            'water_cost.numeric' => 'Biaya air harus berupa angka',
            'water_cost.min' => 'Biaya air tidak boleh negatif',
            'fuel_cost.required' => 'Biaya bahan bakar harus diisi',
            'fuel_cost.numeric' => 'Biaya bahan bakar harus berupa angka',
            'fuel_cost.min' => 'Biaya bahan bakar tidak boleh negatif',
            'office_salary_cost.required' => 'Biaya gaji office harus diisi',
            'office_salary_cost.numeric' => 'Biaya gaji office harus berupa angka',
            'office_salary_cost.min' => 'Biaya gaji office tidak boleh negatif',
            'other_costs.numeric' => 'Biaya lain-lain harus berupa angka',
            'other_costs.min' => 'Biaya lain-lain tidak boleh negatif',
            'notes.max' => 'Catatan maksimal 1000 karakter'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if data already exists for this outlet, month, and year
            $existing = MonthlyProductionCost::where('outlet_id', $request->outlet_id)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data biaya untuk outlet, bulan, dan tahun ini sudah ada. Silakan edit data yang sudah ada.'
                ], 422);
            }

            DB::beginTransaction();

            $cost = new MonthlyProductionCost();
            $cost->outlet_id = $request->outlet_id;
            $cost->month = $request->month;
            $cost->year = $request->year;
            $cost->electricity_cost = $request->electricity_cost;
            $cost->water_cost = $request->water_cost;
            $cost->fuel_cost = $request->fuel_cost;
            $cost->office_salary_cost = $request->office_salary_cost;
            $cost->other_costs = $request->other_costs ?? 0;
            $cost->notes = $request->notes;
            $cost->created_by = Auth::id();
            
            // Calculate total cost
            $cost->calculateTotalCost();
            $cost->save();

            DB::commit();

            // Load relationships for response
            $cost->load(['outlet', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Biaya bulanan berhasil disimpan',
                'data' => $cost
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan biaya bulanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $cost = MonthlyProductionCost::with(['outlet', 'creator', 'updater'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $cost
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data biaya bulanan tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'electricity_cost' => 'required|numeric|min:0',
            'water_cost' => 'required|numeric|min:0',
            'fuel_cost' => 'required|numeric|min:0',
            'office_salary_cost' => 'required|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cost = MonthlyProductionCost::findOrFail($id);

            DB::beginTransaction();

            $cost->electricity_cost = $request->electricity_cost;
            $cost->water_cost = $request->water_cost;
            $cost->fuel_cost = $request->fuel_cost;
            $cost->office_salary_cost = $request->office_salary_cost;
            $cost->other_costs = $request->other_costs ?? 0;
            $cost->notes = $request->notes;
            $cost->updated_by = Auth::id();
            
            // Recalculate total cost
            $cost->calculateTotalCost();
            $cost->save();

            DB::commit();

            // Load relationships for response
            $cost->load(['outlet', 'creator', 'updater']);

            return response()->json([
                'success' => true,
                'message' => 'Biaya bulanan berhasil diperbarui',
                'data' => $cost
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui biaya bulanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cost = MonthlyProductionCost::findOrFail($id);

            DB::beginTransaction();
            $cost->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Biaya bulanan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus biaya bulanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCurrentStats(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            
            // Get current month cost
            $currentCost = MonthlyProductionCost::getCurrentPeriodCost($outletId);
            
            // Get latest cost if current month not available
            $latestCost = MonthlyProductionCost::getLatestCost($outletId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'current_month' => $currentCost,
                    'latest' => $latestCost,
                    'has_current_month' => $currentCost !== null,
                    'current_period' => now()->format('F Y')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik biaya bulanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getHistory(Request $request)
    {
        try {
            $outletId = $request->get('outlet_id');
            $limit = $request->get('limit', 5);

            $query = MonthlyProductionCost::with(['outlet'])
                ->byOutlet($outletId)
                ->latest()
                ->limit($limit);

            $history = $query->get();

            return response()->json([
                'success' => true,
                'data' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat riwayat biaya bulanan: ' . $e->getMessage()
            ], 500);
        }
    }
}