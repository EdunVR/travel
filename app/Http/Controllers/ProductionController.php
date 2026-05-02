<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\ProductionMaterial;
use App\Models\ProductionLaborCost;
use App\Models\ProductionOperationalCost;
use App\Models\MonthlyProductionCost;
use App\Models\Produk;
use App\Models\Bahan;
use App\Models\BahanDetail;
use App\Models\HppProduk;
use App\Models\Outlet;
use App\Models\Attendance;
use App\Models\ProductionRealization;
use App\Exports\ProductionsExport;
use App\Traits\HasOutletFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProductionController extends Controller
{
    use HasOutletFilter;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display production index page
     */
    public function index(Request $request)
    {
        $selectedOutlet = $this->getSelectedOutlet($request);
        $outlets = $this->getAccessibleOutlets();
        
        return view('admin.produksi.produksi.index', compact(
            'selectedOutlet', 
            'outlets'
        ));
    }

    /**
     * Store a new production
     */
    public function store(Request $request)
    {
        try {
            Log::info('Production Store Request', [
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            // Validate the request
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id_outlet',
                'production_line' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'expiry_date' => 'nullable|date',
                'priority' => 'required|in:normal,high,urgent',
                'description' => 'nullable|string|max:1000',
                'business_type' => 'nullable|string|max:50',
                
                // Multi-product support
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|integer|exists:produk,id_produk',
                'products.*.target_quantity' => 'required|integer|min:1',
                'products.*.sample_quantity' => 'nullable|integer|min:0',
                
                // Materials
                'materials' => 'nullable|array',
                'materials.*.material_id' => 'required_with:materials|integer|exists:bahan,id_bahan',
                'materials.*.quantity' => 'required_with:materials|numeric|min:0.01',
                'materials.*.material_type' => 'required_with:materials|string|in:bahan,produk',
                
                // Labor costs
                'labor_costs.worker_count' => 'nullable|integer|min:0',
                'labor_costs.cost_per_worker' => 'nullable|numeric|min:0',
                'labor_costs.total_cost' => 'nullable|numeric|min:0',
                
                // Operational costs
                'operational_costs' => 'nullable|array',
                'operational_costs.*.cost_type' => 'nullable|string|max:255',
                'operational_costs.*.description' => 'nullable|string|max:255',
                'operational_costs.*.amount' => 'required_with:operational_costs|numeric|min:0',
                
                // Tofu specific data
                'tofu_data' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                Log::error('Production Store Validation Failed', [
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->all()
                ]);
                
                // Create user-friendly error messages
                $errors = $validator->errors();
                $userFriendlyErrors = [];
                
                foreach ($errors->all() as $error) {
                    if (strpos($error, 'products.') !== false && strpos($error, 'product_id') !== false) {
                        // Extract product index from error key
                        preg_match('/products\.(\d+)\.product_id/', $error, $matches);
                        $productIndex = isset($matches[1]) ? (int)$matches[1] + 1 : 'salah satu';
                        $userFriendlyErrors[] = "Produk pada baris {$productIndex} belum dipilih. Silakan pilih produk terlebih dahulu.";
                    } else {
                        $userFriendlyErrors[] = $error;
                    }
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal. Periksa data yang dimasukkan.',
                    'errors' => $errors,
                    'user_friendly_errors' => $userFriendlyErrors
                ], 422);
            }

            // Custom validation for operational costs
            if (!empty($request->operational_costs)) {
                foreach ($request->operational_costs as $index => $cost) {
                    if (!empty($cost['amount']) && empty($cost['cost_type']) && empty($cost['description'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Validasi gagal',
                            'errors' => [
                                "operational_costs.{$index}.cost_type" => ["Either cost_type or description is required for operational costs."]
                            ]
                        ], 422);
                    }
                }
            }

            DB::beginTransaction();

            // Calculate total target quantity
            $totalTargetQuantity = 0;
            foreach ($request->products as $product) {
                $totalTargetQuantity += intval($product['target_quantity'] ?? 0);
            }

            // Generate production code
            $outletCode = DB::table('outlets')->where('id_outlet', $request->outlet_id)->value('kode_outlet') ?? 'OUT';
            // Generate unique production code
            $attempts = 0;
            do {
                $randomNumber = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                $productionCode = $outletCode . '-PROD-' . date('Ymd') . '-' . $randomNumber;
                $exists = Production::where('production_code', $productionCode)->exists();
                $attempts++;
                
                if ($attempts > 100) {
                    throw new \Exception('Unable to generate unique production code after 100 attempts');
                }
            } while ($exists);

            // Create production record
            $production = Production::create([
                'outlet_id' => $request->outlet_id,
                'production_code' => $productionCode,
                'production_line' => $request->production_line,
                'target_quantity' => $totalTargetQuantity,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'expiry_date' => $request->expiry_date,
                'priority' => $request->priority,
                'description' => $request->description,
                'business_type' => $request->business_type,
                'tofu_data' => $request->tofu_data ? json_encode($request->tofu_data) : null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Create HPP records for each product
            foreach ($request->products as $product) {
                if (!empty($product['product_id']) && !empty($product['target_quantity'])) {
                    HppProduk::create([
                        'id_produk' => $product['product_id'],
                        'production_id' => $production->id,
                        'target_quantity' => $product['target_quantity'],
                        'sample_quantity' => $product['sample_quantity'] ?? 0,
                        'stok' => 0, // Will be updated when production is realized
                        'hpp' => 0, // Will be calculated
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Store materials
            if (!empty($request->materials)) {
                foreach ($request->materials as $material) {
                    if (!empty($material['material_id']) && !empty($material['quantity'])) {
                        // Get unit from bahan table
                        $unit = 'unit'; // Default fallback
                        if ($material['material_type'] === 'bahan') {
                            $bahanData = DB::table('bahan')
                                ->leftJoin('satuan', 'bahan.id_satuan', '=', 'satuan.id_satuan')
                                ->where('bahan.id_bahan', $material['material_id'])
                                ->select('satuan.nama_satuan')
                                ->first();
                            
                            if ($bahanData && $bahanData->nama_satuan) {
                                $unit = $bahanData->nama_satuan;
                            }
                        } else {
                            // For produk type materials, get unit from produk table if needed
                            $produkData = DB::table('produk')
                                ->leftJoin('satuan', 'produk.id_satuan', '=', 'satuan.id_satuan')
                                ->where('produk.id_produk', $material['material_id'])
                                ->select('satuan.nama_satuan')
                                ->first();
                            
                            if ($produkData && $produkData->nama_satuan) {
                                $unit = $produkData->nama_satuan;
                            }
                        }
                        
                        ProductionMaterial::create([
                            'production_id' => $production->id,
                            'material_id' => $material['material_id'],
                            'material_type' => $material['material_type'] ?? 'bahan',
                            'quantity_required' => $material['quantity'],
                            'unit' => $unit,
                        ]);
                    }
                }
            }

            // Store labor costs
            if (!empty($request->labor_costs)) {
                $laborCosts = $request->labor_costs;
                $workerCount = intval($laborCosts['worker_count'] ?? 0);
                $costPerWorker = floatval($laborCosts['cost_per_worker'] ?? 0);
                $totalCost = floatval($laborCosts['total_cost'] ?? 0);

                if ($workerCount > 0 || $totalCost > 0) {
                    ProductionLaborCost::create([
                        'production_id' => $production->id,
                        'worker_count' => $workerCount,
                        'cost_per_worker' => $costPerWorker,
                        'total_cost' => $totalCost > 0 ? $totalCost : ($workerCount * $costPerWorker),
                    ]);
                }
            }

            // Store operational costs
            if (!empty($request->operational_costs)) {
                foreach ($request->operational_costs as $cost) {
                    // Handle both manual (cost_type) and auto-generated (description) operational costs
                    $costType = $cost['cost_type'] ?? $cost['description'] ?? '';
                    if (!empty($costType) && !empty($cost['amount'])) {
                        ProductionOperationalCost::create([
                            'production_id' => $production->id,
                            'cost_type' => $costType,
                            'amount' => $cost['amount'],
                        ]);
                    }
                }
            }

            DB::commit();

            Log::info('Production Created Successfully', [
                'production_id' => $production->id,
                'production_code' => $production->production_code,
                'total_target_quantity' => $totalTargetQuantity
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produksi berhasil dibuat',
                'data' => [
                    'id' => $production->id,
                    'production_code' => $production->production_code,
                    'target_quantity' => $totalTargetQuantity,
                    'status' => $production->status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Production Store Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat produksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing production
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Production Update Request', [
                'production_id' => $id,
                'request_data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            $production = Production::findOrFail($id);

            // Only allow updates for draft status
            if ($production->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya produksi dengan status draft yang dapat diubah'
                ], 403);
            }

            // Validate the request (same as store)
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id_outlet',
                'production_line' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'expiry_date' => 'nullable|date',
                'priority' => 'required|in:normal,high,urgent',
                'description' => 'nullable|string|max:1000',
                'business_type' => 'nullable|string|max:50',
                
                // Multi-product support
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|integer|exists:produk,id_produk',
                'products.*.target_quantity' => 'required|integer|min:1',
                'products.*.sample_quantity' => 'nullable|integer|min:0',
                
                // Materials
                'materials' => 'nullable|array',
                'materials.*.material_id' => 'required_with:materials|integer|exists:bahan,id_bahan',
                'materials.*.quantity' => 'required_with:materials|numeric|min:0.01',
                'materials.*.material_type' => 'required_with:materials|string|in:bahan,produk',
                
                // Labor costs
                'labor_costs.worker_count' => 'nullable|integer|min:0',
                'labor_costs.cost_per_worker' => 'nullable|numeric|min:0',
                'labor_costs.total_cost' => 'nullable|numeric|min:0',
                
                // Operational costs (support both manual and auto-generated)
                'operational_costs' => 'nullable|array',
                'operational_costs.*.cost_type' => 'nullable|string|max:255',
                'operational_costs.*.description' => 'nullable|string|max:255',
                'operational_costs.*.amount' => 'required_with:operational_costs|numeric|min:0',
                
                // Tofu specific data
                'tofu_data' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                Log::error('Production Update Validation Failed', [
                    'production_id' => $id,
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Custom validation for operational costs
            if (!empty($request->operational_costs)) {
                foreach ($request->operational_costs as $index => $cost) {
                    if (!empty($cost['amount']) && empty($cost['cost_type']) && empty($cost['description'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Validasi gagal',
                            'errors' => [
                                "operational_costs.{$index}.cost_type" => ["Either cost_type or description is required for operational costs."]
                            ]
                        ], 422);
                    }
                }
            }

            DB::beginTransaction();

            // Calculate total target quantity
            $totalTargetQuantity = 0;
            foreach ($request->products as $product) {
                $totalTargetQuantity += intval($product['target_quantity'] ?? 0);
            }

            // Update production record
            $production->update([
                'outlet_id' => $request->outlet_id,
                'production_line' => $request->production_line,
                'target_quantity' => $totalTargetQuantity,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'expiry_date' => $request->expiry_date,
                'priority' => $request->priority,
                'description' => $request->description,
                'business_type' => $request->business_type,
                'tofu_data' => $request->tofu_data ? json_encode($request->tofu_data) : null,
                'updated_by' => auth()->id(),
            ]);

            // Delete existing HPP records and create new ones
            HppProduk::where('production_id', $production->id)->delete();
            foreach ($request->products as $product) {
                if (!empty($product['product_id']) && !empty($product['target_quantity'])) {
                    HppProduk::create([
                        'id_produk' => $product['product_id'],
                        'production_id' => $production->id,
                        'target_quantity' => $product['target_quantity'],
                        'sample_quantity' => $product['sample_quantity'] ?? 0,
                        'stok' => 0,
                        'hpp' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Update materials
            ProductionMaterial::where('production_id', $production->id)->delete();
            if (!empty($request->materials)) {
                foreach ($request->materials as $material) {
                    if (!empty($material['material_id']) && !empty($material['quantity'])) {
                        // Get unit from bahan table
                        $unit = 'unit'; // Default fallback
                        if ($material['material_type'] === 'bahan') {
                            $bahanData = DB::table('bahan')
                                ->leftJoin('satuan', 'bahan.id_satuan', '=', 'satuan.id_satuan')
                                ->where('bahan.id_bahan', $material['material_id'])
                                ->select('satuan.nama_satuan')
                                ->first();
                            
                            if ($bahanData && $bahanData->nama_satuan) {
                                $unit = $bahanData->nama_satuan;
                            }
                        } else {
                            // For produk type materials, get unit from produk table if needed
                            $produkData = DB::table('produk')
                                ->leftJoin('satuan', 'produk.id_satuan', '=', 'satuan.id_satuan')
                                ->where('produk.id_produk', $material['material_id'])
                                ->select('satuan.nama_satuan')
                                ->first();
                            
                            if ($produkData && $produkData->nama_satuan) {
                                $unit = $produkData->nama_satuan;
                            }
                        }
                        
                        ProductionMaterial::create([
                            'production_id' => $production->id,
                            'material_id' => $material['material_id'],
                            'material_type' => $material['material_type'] ?? 'bahan',
                            'quantity_required' => $material['quantity'],
                            'unit' => $unit,
                        ]);
                    }
                }
            }

            // Update labor costs
            ProductionLaborCost::where('production_id', $production->id)->delete();
            if (!empty($request->labor_costs)) {
                $laborCosts = $request->labor_costs;
                $workerCount = intval($laborCosts['worker_count'] ?? 0);
                $costPerWorker = floatval($laborCosts['cost_per_worker'] ?? 0);
                $totalCost = floatval($laborCosts['total_cost'] ?? 0);

                if ($workerCount > 0 || $totalCost > 0) {
                    ProductionLaborCost::create([
                        'production_id' => $production->id,
                        'worker_count' => $workerCount,
                        'cost_per_worker' => $costPerWorker,
                        'total_cost' => $totalCost > 0 ? $totalCost : ($workerCount * $costPerWorker),
                    ]);
                }
            }

            // Update operational costs
            ProductionOperationalCost::where('production_id', $production->id)->delete();
            if (!empty($request->operational_costs)) {
                foreach ($request->operational_costs as $cost) {
                    // Handle both manual (cost_type) and auto-generated (description) operational costs
                    $costType = $cost['cost_type'] ?? $cost['description'] ?? '';
                    if (!empty($costType) && !empty($cost['amount'])) {
                        ProductionOperationalCost::create([
                            'production_id' => $production->id,
                            'cost_type' => $costType,
                            'amount' => $cost['amount'],
                        ]);
                    }
                }
            }

            DB::commit();

            Log::info('Production Updated Successfully', [
                'production_id' => $production->id,
                'production_code' => $production->production_code,
                'total_target_quantity' => $totalTargetQuantity
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produksi berhasil diperbarui',
                'data' => [
                    'id' => $production->id,
                    'production_code' => $production->production_code,
                    'target_quantity' => $totalTargetQuantity,
                    'status' => $production->status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Production Update Error', [
                'production_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui produksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add realization with multi-product support
     */
    public function addRealization(Request $request, $id)
    {
        // Check if this is multi-product realization
        if ($request->has('products') && is_array($request->products)) {
            return $this->addMultiProductRealization($request, $id);
        }
        
        // Legacy single product realization (backward compatibility)
        return $this->addSingleProductRealization($request, $id);
    }

    /**
     * Add multi-product realization
     */
    private function addMultiProductRealization(Request $request, $id)
    {
        // Log request data for debugging
        Log::info('Multi-Product Realization Request', [
            'production_id' => $id,
            'request_data' => $request->all(),
            'products_count' => is_array($request->products) ? count($request->products) : 0
        ]);

        $validator = Validator::make($request->all(), [
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|exists:produk,id_produk',
            'products.*.hpp_record_id' => 'required|integer|exists:hpp_produk,id',
            'products.*.quantity_produced' => 'required|integer|min:1',
            'products.*.quantity_rejected' => 'nullable|integer|min:0',
            'products.*.notes' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000', // Global notes
        ]);

        if ($validator->fails()) {
            Log::error('Multi-Product Realization Validation Failed', [
                'production_id' => $id,
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $production = Production::with(['materials', 'hppRecords'])->findOrFail($id);

            if ($production->status !== 'in_progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya produksi yang sedang berjalan yang dapat ditambahkan realisasi'
                ], 403);
            }

            $totalProduced = 0;
            $totalRejected = 0;
            $realizationDetails = [];

            // Process each product realization
            foreach ($request->products as $productData) {
                $quantityProduced = $productData['quantity_produced'] ?? 0;
                $quantityRejected = $productData['quantity_rejected'] ?? 0;
                
                if ($quantityProduced <= 0) continue; // Skip if no production
                
                $totalProduced += $quantityProduced;
                $totalRejected += $quantityRejected;

                // Update HPP record stock
                $hppRecord = HppProduk::find($productData['hpp_record_id']);
                if ($hppRecord) {
                    // Calculate HPP for this production batch
                    $materialCost = $production->materials->sum(function($material) {
                        return $material->quantity_required * $this->getFifoPrice($material->material_id, $material->material_type);
                    });
                    
                    $laborCost = $production->laborCosts->sum(function($labor) {
                        return $labor->worker_count * $labor->cost_per_worker;
                    });
                    
                    $operationalCost = $production->operationalCosts->sum('amount');
                    $totalCost = $materialCost + $laborCost + $operationalCost;
                    $hppPerUnit = $production->target_quantity > 0 ? $totalCost / $production->target_quantity : 0;
                    Log::info('💰 [HPP CALCULATION] Material cost breakdown', [
                        'production_id' => $production->id,
                        'material_cost' => $materialCost,
                        'labor_cost' => $laborCost,
                        'operational_cost' => $operationalCost,
                        'total_cost' => $totalCost,
                        'hpp_per_unit' => $hppPerUnit,
                        'target_quantity' => $production->target_quantity
                    ]);

                    // Update existing HPP record (no duplication)
                    $hppRecord->stok += $quantityProduced;
                    $hppRecord->hpp = $hppPerUnit; // Update with calculated HPP
                    $hppRecord->realized_quantity = ($hppRecord->realized_quantity ?? 0) + $quantityProduced;
                    $hppRecord->rejected_quantity = ($hppRecord->rejected_quantity ?? 0) + $quantityRejected;
                    $hppRecord->save();

                    Log::info('✅ [INVENTORY] Updated existing HPP record', [
                        'hpp_record_id' => $hppRecord->id,
                        'product_id' => $hppRecord->id_produk,
                        'new_stock' => $hppRecord->stok,
                        'hpp_per_unit' => $hppPerUnit,
                        'quantity_added' => $quantityProduced
                    ]);
                }

                $realizationDetails[] = [
                    'product_id' => $productData['product_id'],
                    'hpp_record_id' => $productData['hpp_record_id'],
                    'quantity_produced' => $quantityProduced,
                    'quantity_rejected' => $quantityRejected,
                    'notes' => $productData['notes'] ?? null,
                ];
            }

            if ($totalProduced <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimal harus ada satu produk yang diproduksi'
                ], 400);
            }

            // Reduce material stock using FIFO system
            $this->reduceMaterialStock($production, $totalProduced);

            // Create realization record (only use columns that exist in the table)
            $realization = ProductionRealization::create([
                'production_id' => $production->id,
                'quantity_produced' => $totalProduced,
                'quantity_rejected' => $totalRejected,
                'realization_date' => now()->format('Y-m-d'),
                'recorded_by' => auth()->id(),
                'notes' => $request->notes, // Store basic notes first
            ]);

            // Store realization details in notes as JSON since realization_details column doesn't exist
            if (!empty($realizationDetails)) {
                $notesWithDetails = $request->notes ?? '';
                if (!empty($realizationDetails)) {
                    $notesWithDetails .= "\n\nDetailed breakdown: " . json_encode($realizationDetails, JSON_PRETTY_PRINT);
                }
                $realization->update(['notes' => $notesWithDetails]);
            }

            // Update production realized quantity
            $production->realized_quantity += $totalProduced;
            $production->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Realisasi berhasil ditambahkan',
                'data' => [
                    'realization' => $realization,
                    'total_produced' => $totalProduced,
                    'total_rejected' => $totalRejected,
                    'products_count' => count($realizationDetails)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding multi-product realization: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan realisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Legacy single product realization (backward compatibility)
     */
    private function addSingleProductRealization(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity_produced' => 'required|integer|min:1',
            'quantity_rejected' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $production = Production::with(['materials', 'hppRecords'])->findOrFail($id);

            if ($production->status !== 'in_progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya produksi yang sedang berjalan yang dapat ditambahkan realisasi'
                ], 403);
            }

            // Reduce material stock using FIFO system
            $this->reduceMaterialStock($production, $request->quantity_produced);

            // Update existing HPP record for single product (no duplication)
            $hppRecord = $production->hppRecords->first();
            if ($hppRecord) {
                // Calculate HPP for this production batch
                $materialCost = $production->materials->sum(function($material) {
                    return $material->quantity_required * $this->getFifoPrice($material->material_id, $material->material_type);
                });
                
                $laborCost = $production->laborCosts->sum(function($labor) {
                    return $labor->worker_count * $labor->cost_per_worker;
                });
                
                $operationalCost = $production->operationalCosts->sum('amount');
                $totalCost = $materialCost + $laborCost + $operationalCost;
                $hppPerUnit = $production->target_quantity > 0 ? $totalCost / $production->target_quantity : 0;
                    Log::info('💰 [HPP CALCULATION] Material cost breakdown', [
                        'production_id' => $production->id,
                        'material_cost' => $materialCost,
                        'labor_cost' => $laborCost,
                        'operational_cost' => $operationalCost,
                        'total_cost' => $totalCost,
                        'hpp_per_unit' => $hppPerUnit,
                        'target_quantity' => $production->target_quantity
                    ]);

                // Update existing HPP record (no duplication)
                $hppRecord->stok += $request->quantity_produced;
                $hppRecord->hpp = $hppPerUnit; // Update with calculated HPP
                $hppRecord->realized_quantity = ($hppRecord->realized_quantity ?? 0) + $request->quantity_produced;
                $hppRecord->rejected_quantity = ($hppRecord->rejected_quantity ?? 0) + ($request->quantity_rejected ?? 0);
                $hppRecord->save();

                Log::info('✅ [INVENTORY] Updated existing HPP record (single product)', [
                    'hpp_record_id' => $hppRecord->id,
                    'product_id' => $hppRecord->id_produk,
                    'new_stock' => $hppRecord->stok,
                    'hpp_per_unit' => $hppPerUnit,
                    'quantity_added' => $request->quantity_produced
                ]);
            }

            // Create realization record (only use columns that exist in the table)
            $realization = ProductionRealization::create([
                'production_id' => $production->id,
                'quantity_produced' => $request->quantity_produced,
                'quantity_rejected' => $request->quantity_rejected ?? 0,
                'realization_date' => now()->format('Y-m-d'),
                'recorded_by' => auth()->id(),
                'notes' => $request->notes,
            ]);

            // Update production realized quantity
            $production->realized_quantity += $request->quantity_produced;
            $production->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Realisasi berhasil ditambahkan',
                'data' => $realization
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding realization: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan realisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get production data for DataTables
     */
    public function getData(Request $request)
    {
        Log::info('🔄 [PRODUCTION DATA] Starting getData request', [
            'request_params' => $request->all(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString()
        ]);

        $outletId = $this->getSelectedOutlet($request);
        Log::info('🏢 [PRODUCTION DATA] Selected outlet', [
            'outlet_id' => $outletId,
            'from_request' => $request->get('outlet_id'),
            'from_session' => session('selected_outlet')
        ]);
        
        $query = Production::with(['outlet', 'laborCosts', 'operationalCosts', 'realizations', 'materials', 'hppRecords.product']);

        // Filter by outlet - only if specific outlet is selected
        if ($outletId && $outletId !== 'ALL' && $outletId !== '') {
            $query->where('outlet_id', $outletId);
            Log::info('🔍 [PRODUCTION DATA] Outlet filter applied', ['outlet_id' => $outletId]);
        } else {
            Log::info('🔍 [PRODUCTION DATA] Outlet filter skipped - showing all outlets', ['outlet_id' => $outletId]);
        }

        // Handle grid view request
        if ($request->filled('for_grid')) {
            Log::info('📋 [PRODUCTION DATA] Grid view requested');
            
            try {
                $productions = $query->orderBy('created_at', 'desc')->get();
                Log::info('📋 [PRODUCTION DATA] Query executed', [
                    'total_records' => $productions->count(),
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings()
                ]);

                if ($productions->isEmpty()) {
                    Log::warning('⚠️ [PRODUCTION DATA] No productions found', [
                        'outlet_id' => $outletId,
                        'filters' => $request->only(['status', 'production_line', 'start_date', 'end_date'])
                    ]);
                }
                
                // Transform data for grid view
                $gridData = $productions->map(function($production) {
                    Log::debug('🔄 [PRODUCTION DATA] Processing production', [
                        'id' => $production->id,
                        'code' => $production->production_code,
                        'hpp_records_count' => $production->hppRecords->count()
                    ]);

                    $realized = $production->realizations->sum('quantity_produced');
                    $rejected = $production->realizations->sum('quantity_rejected');
                    $progress = $production->target_quantity > 0 ? ($realized / $production->target_quantity) * 100 : 0;
                    
                    // Get product names from HPP records (multi-product support)
                    $productNames = [];
                    if ($production->hppRecords && $production->hppRecords->count() > 0) {
                        foreach ($production->hppRecords as $hppRecord) {
                            if ($hppRecord->product) {
                                $productNames[] = $hppRecord->product->nama_produk;
                            }
                        }
                    }
                    
                    // Join product names or show fallback
                    $productNameDisplay = !empty($productNames) ? implode(', ', $productNames) : 'Produk tidak ditemukan';
                    
                    Log::debug('🔄 [PRODUCTION DATA] Product names', [
                        'production_id' => $production->id,
                        'product_names' => $productNames,
                        'display' => $productNameDisplay
                    ]);
                    
                    // Calculate actual HPP and total cost using FIFO pricing
                    $materialCost = $production->materials->sum(function($material) {
                        return $material->quantity_required * $this->getFifoPrice($material->material_id, $material->material_type);
                    });
                    
                    $laborCost = $production->laborCosts->sum(function($labor) {
                        return $labor->worker_count * $labor->cost_per_worker;
                    });
                    
                    $operationalCost = $production->operationalCosts->sum('amount');
                    $totalCost = $materialCost + $laborCost + $operationalCost;
                    $hppPerUnit = $production->target_quantity > 0 ? $totalCost / $production->target_quantity : 0;
                    Log::info('💰 [HPP CALCULATION] Material cost breakdown', [
                        'production_id' => $production->id,
                        'material_cost' => $materialCost,
                        'labor_cost' => $laborCost,
                        'operational_cost' => $operationalCost,
                        'total_cost' => $totalCost,
                        'hpp_per_unit' => $hppPerUnit,
                        'target_quantity' => $production->target_quantity
                    ]);
                    
                    return [
                        'id' => $production->id,
                        'production_code' => $production->production_code,
                        'product_name' => $productNameDisplay,
                        'target_quantity' => $production->target_quantity,
                        'target_quantity_formatted' => number_format($production->target_quantity, 0, ',', '.'),
                        'realized_quantity' => $realized,
                        'realized_quantity_formatted' => number_format($realized, 0, ',', '.'),
                        'rejected_quantity' => $rejected,
                        'rejected_quantity_formatted' => number_format($rejected, 0, ',', '.'),
                        'progress' => round($progress, 1),
                        'progress_formatted' => number_format($progress, 1) . '%',
                        'hpp_per_unit' => $hppPerUnit,
                        'hpp_per_unit_formatted' => $hppPerUnit > 0 ? 'Rp ' . number_format($hppPerUnit, 0, ',', '.') : '-',
                        'total_cost' => $totalCost,
                        'total_cost_formatted' => $totalCost > 0 ? 'Rp ' . number_format($totalCost, 0, ',', '.') : '-',
                        'start_date' => $production->start_date ? $production->start_date : null,
                        'end_date' => $production->end_date ? $production->end_date : null,
                        'expiry_date' => $production->expiry_date ? $production->expiry_date : null,
                        'status' => $production->status,
                        'status_formatted' => ucfirst(str_replace('_', ' ', $production->status)),
                        'priority' => $production->priority ?? 'normal',
                        'priority_formatted' => ucfirst($production->priority ?? 'normal'),
                        'production_line' => $production->production_line,
                        'description' => $production->description,
                        'business_type' => $production->business_type,
                        'tofu_data' => $production->tofu_data,
                        'created_at' => $production->created_at->format('d/m/Y H:i'),
                        'hpp_records' => $production->hppRecords->map(function($hpp) {
                            return [
                                'id' => $hpp->id,
                                'product_id' => $hpp->id_produk,
                                'product_name' => $hpp->product ? $hpp->product->nama_produk : 'Unknown',
                                'target_quantity' => $hpp->target_quantity ?? 0,
                                'realized_quantity' => $hpp->realized_quantity ?? 0,
                                'rejected_quantity' => $hpp->rejected_quantity ?? 0,
                            ];
                        }),
                    ];
                });
                
                Log::info('✅ [PRODUCTION DATA] Grid data prepared', [
                    'total_items' => $gridData->count(),
                    'sample_item' => $gridData->first()
                ]);
                
                return response()->json([
                    'success' => true,
                    'data' => $gridData,
                    'debug' => [
                        'outlet_id' => $outletId,
                        'total_records' => $productions->count(),
                        'filters_applied' => $request->only(['status', 'production_line', 'start_date', 'end_date']),
                        'timestamp' => now()->toDateTimeString()
                    ]
                ]);
                
            } catch (\Exception $e) {
                Log::error('💥 [PRODUCTION DATA] Exception in grid data processing', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error processing production data: ' . $e->getMessage(),
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    /**
     * Show the form for editing the specified production
     */
    public function edit($id)
    {
        try {
            $production = Production::with([
                'materials',
                'laborCosts',
                'operationalCosts',
                'outlet',
                'hppRecords.product'
            ])->findOrFail($id);

            // Only allow editing for draft status
            if ($production->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya produksi dengan status draft yang dapat diedit'
                ], 403);
            }

            // Transform data for frontend
            $productionData = [
                'id' => $production->id,
                'production_code' => $production->production_code,
                'outlet_id' => $production->outlet_id,
                'production_line' => $production->production_line,
                'start_date' => $production->start_date ? $production->start_date->format('Y-m-d') : null,
                'end_date' => $production->end_date ? $production->end_date->format('Y-m-d') : null,
                'expiry_date' => $production->expiry_date ? $production->expiry_date->format('Y-m-d') : null,
                'priority' => $production->priority,
                'description' => $production->description,
                'business_type' => $production->business_type,
                'tofu_data' => $production->tofu_data ? json_decode($production->tofu_data, true) : [],
                'status' => $production->status,
                
                // Products from HPP records
                'products' => $production->hppRecords->map(function($hpp) {
                    return [
                        'product_id' => $hpp->id_produk,
                        'target_quantity' => $hpp->target_quantity,
                        'sample_quantity' => $hpp->sample_quantity ?? 0,
                        'product_name' => $hpp->product ? $hpp->product->nama_produk : 'Unknown'
                    ];
                })->toArray(),
                
                // Materials with proper data
                'materials' => $production->materials->map(function($material) {
                    // Get material name based on type
                    $materialName = 'Unknown';
                    if ($material->material_type === 'bahan') {
                        $bahan = DB::table('bahan')->where('id_bahan', $material->material_id)->first();
                        $materialName = $bahan ? $bahan->nama_bahan : 'Unknown';
                    } else {
                        $produk = DB::table('produk')->where('id_produk', $material->material_id)->first();
                        $materialName = $produk ? $produk->nama_produk : 'Unknown';
                    }
                    
                    return [
                        'material_id' => $material->material_id,
                        'material_type' => $material->material_type,
                        'quantity' => $material->quantity_required,
                        'unit' => $material->unit,
                        'material_name' => $materialName
                    ];
                })->toArray(),
                
                // Labor costs
                'labor_costs' => $production->laborCosts->first() ? [
                    'worker_count' => $production->laborCosts->first()->worker_count ?? 0,
                    'cost_per_worker' => $production->laborCosts->first()->cost_per_worker ?? 0,
                    'total_cost' => $production->laborCosts->first()->total_cost ?? 0
                ] : [
                    'worker_count' => 0,
                    'cost_per_worker' => 0,
                    'total_cost' => 0
                ],
                
                // Operational costs
                'operational_costs' => $production->operationalCosts->map(function($cost) {
                    return [
                        'cost_type' => $cost->cost_type,
                        'amount' => $cost->amount
                    ];
                })->toArray(),
                
                // Additional data for debugging
                'debug_info' => [
                    'materials_count' => $production->materials->count(),
                    'hpp_records_count' => $production->hppRecords->count(),
                    'labor_costs_count' => $production->laborCosts->count(),
                    'operational_costs_count' => $production->operationalCosts->count(),
                ]
            ];

            Log::info('Production Edit Data Loaded', [
                'production_id' => $id,
                'products_count' => count($productionData['products']),
                'materials_count' => count($productionData['materials']),
                'labor_costs' => $productionData['labor_costs'],
                'operational_costs_count' => count($productionData['operational_costs'])
            ]);

            return response()->json([
                'success' => true,
                'data' => $productionData
            ]);

        } catch (\Exception $e) {
            Log::error('Error editing production: ' . $e->getMessage(), [
                'production_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Produksi tidak ditemukan atau tidak dapat diedit'
            ], 404);
        }
    }

    /**
     * Show production details
     */
    public function show($id)
    {
        try {
            $production = Production::with([
                'product',
                'materials',
                'laborCosts',
                'operationalCosts',
                'realizations',
                'outlet',
                'hppRecords.product'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $production
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing production: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Produksi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Generate PDF report for production details
     */
    public function generatePdf($id)
    {
        // Add debugging to ensure method is being called
        Log::info('🎯 [PDF GENERATION] Method called successfully', [
            'id' => $id,
            'method' => __METHOD__,
            'class' => __CLASS__,
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString()
        ]);
        
        try {
            Log::info('Generate PDF Start', [
                'id' => $id,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Unknown'
            ]);

            $production = Production::with([
                'materials', 
                'laborCosts',
                'operationalCosts',
                'realizations', 
                'creator', 
                'approver',
                'outlet',
                'hppRecords.product'
            ])->findOrFail($id);

            // Get product names from HPP records
            $productNames = [];
            if ($production->hppRecords && $production->hppRecords->count() > 0) {
                foreach ($production->hppRecords as $hppRecord) {
                    if ($hppRecord->product) {
                        $productNames[] = $hppRecord->product->nama_produk;
                    }
                }
            }
            $productNameDisplay = !empty($productNames) ? implode(', ', $productNames) : 'Produk tidak ditemukan';

            Log::info('Production found for PDF generation', [
                'id' => $id,
                'production_code' => $production->production_code,
                'product_names' => $productNames,
                'product_display' => $productNameDisplay,
                'hpp_records_count' => $production->hppRecords->count()
            ]);

            // Manually load the material relationships and calculate FIFO prices
            $materialsWithFifoPrice = [];
            foreach ($production->materials as $material) {
                $materialData = [
                    'material' => $material,
                    'fifo_price' => $this->getFifoPrice($material->material_id, $material->material_type)
                ];
                
                if ($material->material_type === 'bahan') {
                    $bahan = Bahan::with('hargaBahan')->find($material->material_id);
                    $material->setRelation('material', $bahan);
                    $materialData['name'] = $bahan ? $bahan->nama_bahan : 'Unknown Bahan';
                } else {
                    $produk = Produk::find($material->material_id);
                    $material->setRelation('material', $produk);
                    $materialData['name'] = $produk ? $produk->nama_produk : 'Unknown Produk';
                }
                
                $materialsWithFifoPrice[] = $materialData;
            }

            // Calculate costs using FIFO pricing (consistent with HPP preview)
            $materialCost = $production->materials->sum(function($material) {
                return $material->quantity_required * $this->getFifoPrice($material->material_id, $material->material_type);
            });
            
            $laborCost = $production->laborCosts->sum(function($labor) {
                return $labor->worker_count * $labor->cost_per_worker;
            });
            
            $operationalCost = $production->operationalCosts->sum('amount');
            $totalCost = $materialCost + $laborCost + $operationalCost;
            
            $hppCalculation = [
                'material_cost' => $materialCost,
                'labor_cost' => $laborCost,
                'operational_cost' => $operationalCost,
                'total_cost' => $totalCost,
                'hpp_per_unit' => $production->target_quantity > 0 ? $totalCost / $production->target_quantity : 0
            ];

            Log::info('PDF calculations completed', [
                'id' => $id,
                'total_cost' => $totalCost,
                'hpp_per_unit' => $hppCalculation['hpp_per_unit']
            ]);

            // Generate PDF using DomPDF
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('admin.produksi.produksi.pdf', compact('production', 'hppCalculation', 'materialsWithFifoPrice'));
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'Laporan_Produksi_' . $production->production_code . '_' . date('Y-m-d') . '.pdf';
            
            Log::info('PDF generated successfully', [
                'id' => $id,
                'filename' => $filename
            ]);
            
            return $pdf->stream($filename);

        } catch (\Exception $e) {
            Log::error('Production PDF Generation Error', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan PDF'
            ], 500);
        }
    }

    /**
     * Generate QC Egg Tofu Mentah PDF report
     */
    public function generateQcTofuPdf($id)
    {
        try {
            Log::info('🎯 [QC TOFU PDF] Method called successfully', [
                'id' => $id,
                'method' => __METHOD__,
                'user_id' => auth()->id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            $production = Production::with([
                'outlet',
                'hppRecords.product'
            ])->findOrFail($id);

            // Check if this is a tofu production
            if ($production->business_type !== 'tofu') {
                return response()->json([
                    'success' => false,
                    'message' => 'QC Egg Tofu Mentah hanya tersedia untuk produksi tofu'
                ], 400);
            }

            // Parse tofu data
            $tofuData = $production->tofu_data ? json_decode($production->tofu_data, true) : [];
            
            if (empty($tofuData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data QC Egg Tofu Mentah tidak ditemukan'
                ], 404);
            }

            // Get product names from HPP records
            $productNames = [];
            if ($production->hppRecords && $production->hppRecords->count() > 0) {
                foreach ($production->hppRecords as $hppRecord) {
                    if ($hppRecord->product) {
                        $productNames[] = $hppRecord->product->nama_produk;
                    }
                }
            }
            $productNameDisplay = !empty($productNames) ? implode(', ', $productNames) : 'Produk tidak ditemukan';

            Log::info('QC Tofu PDF data prepared', [
                'id' => $id,
                'production_code' => $production->production_code,
                'product_names' => $productNames,
                'tofu_data_fields' => array_keys($tofuData)
            ]);

            // Generate PDF using DomPDF
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('admin.produksi.produksi.qc-tofu-pdf', compact('production', 'tofuData', 'productNameDisplay'));
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'QC_Egg_Tofu_Mentah_' . $production->production_code . '_' . date('Y-m-d') . '.pdf';
            
            Log::info('QC Tofu PDF generated successfully', [
                'id' => $id,
                'filename' => $filename
            ]);
            
            return $pdf->stream($filename);

        } catch (\Exception $e) {
            Log::error('QC Tofu PDF Generation Error', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan QC Egg Tofu Mentah PDF'
            ], 500);
        }
    }

    /**
     * Get production statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $outletId = $this->getSelectedOutlet($request);
            
            // Get base query with outlet filter
            $query = Production::query();
            
            if ($outletId !== 'ALL') {
                $query->where('outlet_id', $outletId);
            }
            
            // Calculate statistics using actual column names
            $stats = [
                'total' => $query->count(),
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
                'completed' => (clone $query)->where('status', 'completed')->count(),
                'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
                'total_cost' => (clone $query)->sum('total_cost'), // Use actual column name
                'total_hpp_per_unit' => (clone $query)->sum('hpp_per_unit'), // Use actual column name
                'labor_cost' => (clone $query)->sum('labor_cost'),
                'operational_cost' => (clone $query)->sum('operational_cost'),
                'material_cost' => (clone $query)->sum('material_cost'),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting production statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik produksi'
            ], 500);
        }
    }

    /**
     * Get materials for production
     */
    public function getMaterials(Request $request)
    {
        try {
            $outletId = $this->getSelectedOutlet($request);
            $search = $request->get('search', '');
            
            // Get materials from bahan table with accumulated stock from harga_bahan table
            $query = \App\Models\Bahan::select(
                'bahan.id_bahan', 
                'bahan.kode_bahan', 
                'bahan.nama_bahan',
                'bahan.merk',
                'bahan.harga_beli',
                'satuan.nama_satuan',
                \DB::raw('COALESCE(SUM(harga_bahan.stok), 0) as total_stock'), // Accumulate stock from harga_bahan
                \DB::raw('AVG(harga_bahan.harga_beli) as avg_price') // Average price from harga_bahan (FIFO system)
            )
            ->leftJoin('satuan', 'bahan.id_satuan', '=', 'satuan.id_satuan')
            ->leftJoin('harga_bahan', 'bahan.id_bahan', '=', 'harga_bahan.id_bahan')
            ->where('bahan.id_outlet', $outletId)
            ->where('bahan.is_active', true)
            ->groupBy('bahan.id_bahan', 'bahan.kode_bahan', 'bahan.nama_bahan', 'bahan.merk', 'bahan.harga_beli', 'satuan.nama_satuan')
            ->having('total_stock', '>', 0); // Only materials with stock from harga_bahan
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('bahan.kode_bahan', 'like', "%{$search}%")
                      ->orWhere('bahan.nama_bahan', 'like', "%{$search}%")
                      ->orWhere('bahan.merk', 'like', "%{$search}%");
                });
            }
            
            $materials = $query->limit(20)
                ->get()
                ->map(function($material) {
                    return [
                        'id' => $material->id_bahan,
                        'code' => $material->kode_bahan ?? 'No Code',
                        'name' => $material->nama_bahan ?? 'Unknown Material',
                        'merk' => $material->merk ?? '',
                        'cost' => $material->avg_price ?? $material->harga_beli ?? 0, // Use average price from harga_bahan, fallback to bahan.harga_beli
                        'stock' => $material->total_stock ?? 0, // Accumulated stock from harga_bahan
                        'unit' => $material->nama_satuan ?? 'Unit',
                        'type' => 'bahan' // Mark as raw material
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $materials
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting materials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data material'
            ], 500);
        }
    }

    /**
     * Get FIFO price for material (consistent with HPP preview)
     */
    private function getFifoPrice($materialId, $materialType = 'bahan')
    {
        if ($materialType === 'bahan') {
            // Get FIFO price from harga_bahan table (oldest first)
            $hargaBahan = DB::table('harga_bahan')
                ->where('id_bahan', $materialId)
                ->where('stok', '>', 0)
                ->orderBy('created_at', 'asc') // FIFO order
                ->first();
            
            if ($hargaBahan) {
                return $hargaBahan->harga_beli;
            }
            
            // Fallback to base price from bahan table
            $bahanData = \App\Models\Bahan::find($materialId);
            return $bahanData ? $bahanData->harga_beli : 0;
        } else {
            // For produk type materials
            $produk = \App\Models\Produk::find($materialId);
            if ($produk && method_exists($produk, 'calculateHpp')) {
                return $produk->calculateHpp() ?? 0;
            }
            return 0;
        }
    }

    /**
     * Calculate HPP preview for production
     */
    public function calculateHppPreview(Request $request)
    {
        try {
            $materials = $request->get('materials', []);
            $operationalCosts = $request->get('operational_costs', []);
            $laborCosts = $request->get('labor_costs', []);
            $products = $request->get('products', []);
            
            // Calculate total target quantity from all products
            $totalTargetQuantity = 0;
            if (!empty($products)) {
                foreach ($products as $product) {
                    $targetQuantity = intval($product['target_quantity'] ?? 0);
                    $totalTargetQuantity += $targetQuantity;
                }
            }
            
            // Fallback to single quantity field if no products array
            if ($totalTargetQuantity <= 0) {
                $totalTargetQuantity = intval($request->get('quantity', 1));
            }
            
            // Ensure we have at least 1 for calculation
            if ($totalTargetQuantity <= 0) {
                $totalTargetQuantity = 1;
            }
            
            Log::info('HPP Preview Quantity Calculation', [
                'products_count' => count($products),
                'total_target_quantity' => $totalTargetQuantity,
                'fallback_quantity' => $request->get('quantity', 1)
            ]);
            
            $totalMaterialCost = 0;
            $totalOperationalCost = 0;
            $totalLaborCost = 0;
            $materialBreakdown = [];
            
            // Calculate material costs from harga_bahan table (FIFO system)
            foreach ($materials as $material) {
                if (!empty($material['material_id']) && !empty($material['quantity'])) {
                    // Get material data from bahan table
                    $bahanData = \App\Models\Bahan::find($material['material_id']);
                    
                    if ($bahanData) {
                        // Get FIFO price from harga_bahan table (oldest first)
                        $hargaBahan = DB::table('harga_bahan')
                            ->where('id_bahan', $material['material_id'])
                            ->where('stok', '>', 0)
                            ->orderBy('created_at', 'asc') // FIFO order
                            ->first();
                        
                        // Use FIFO price if available, otherwise use base price from bahan table
                        $unitPrice = $hargaBahan ? $hargaBahan->harga_beli : $bahanData->harga_beli;
                        
                        $materialCost = $unitPrice * ($material['quantity'] ?? 0);
                        $totalMaterialCost += $materialCost;
                        
                        $materialBreakdown[] = [
                            'id' => $bahanData->id_bahan,
                            'name' => $bahanData->nama_bahan,
                            'code' => $bahanData->kode_bahan,
                            'merk' => $bahanData->merk,
                            'unit_price' => $unitPrice,
                            'quantity' => $material['quantity'],
                            'total_cost' => $materialCost,
                            'unit' => $bahanData->satuan ? $bahanData->satuan->nama_satuan : 'Unit',
                            'fifo_used' => $hargaBahan ? true : false // Indicate if FIFO price was used
                        ];
                    }
                }
            }
            
            // Calculate operational costs
            foreach ($operationalCosts as $cost) {
                $totalOperationalCost += $cost['amount'] ?? 0;
            }
            
            // Calculate labor costs
            if (!empty($laborCosts)) {
                $workerCount = intval($laborCosts['worker_count'] ?? 0);
                $costPerWorker = floatval($laborCosts['cost_per_worker'] ?? 0);
                $providedTotalCost = floatval($laborCosts['total_cost'] ?? 0);
                
                // Calculate from worker count and cost per worker
                $calculatedLaborCost = $workerCount * $costPerWorker;
                
                // Priority logic for labor cost calculation:
                // 1. If total_cost is provided and > 0, use it
                // 2. If cost_per_worker is provided and > 0, use calculated cost
                // 3. Otherwise, use 0
                if ($providedTotalCost > 0) {
                    $totalLaborCost = $providedTotalCost;
                    $finalCostSource = 'provided_total';
                } elseif ($costPerWorker > 0 && $workerCount > 0) {
                    $totalLaborCost = $calculatedLaborCost;
                    $finalCostSource = 'calculated';
                } else {
                    $totalLaborCost = 0;
                    $finalCostSource = 'zero_fallback';
                }
                
                // Log for debugging
                Log::info('Labor Cost Calculation', [
                    'worker_count' => $workerCount,
                    'cost_per_worker' => $costPerWorker,
                    'provided_total_cost' => $providedTotalCost,
                    'calculated_cost' => $calculatedLaborCost,
                    'final_labor_cost' => $totalLaborCost,
                    'cost_source' => $finalCostSource
                ]);
            }
            
            $totalCost = $totalMaterialCost + $totalLaborCost + $totalOperationalCost;
            $hppPerUnit = $totalTargetQuantity > 0 ? $totalCost / $totalTargetQuantity : 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'material_cost' => $totalMaterialCost,
                    'labor_cost' => $totalLaborCost,
                    'operational_cost' => $totalOperationalCost,
                    'total_cost' => $totalCost,
                    'quantity' => $totalTargetQuantity,
                    'hpp_per_unit' => $hppPerUnit,
                    'breakdown' => [
                        'materials' => $materialBreakdown,
                        'operational_costs' => $operationalCosts,
                        'labor_costs' => [
                            'worker_count' => $workerCount ?? 0,
                            'cost_per_worker' => $costPerWorker ?? 0,
                            'total_cost' => $totalLaborCost
                        ]
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error calculating HPP preview: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung preview HPP'
            ], 500);
        }
    }

    /**
     * Get material FIFO data
     */
    public function getMaterialFifo(Request $request, $id)
    {
        try {
            $material = Produk::findOrFail($id);
            
            // Get FIFO data from hpp_produk table (FIFO system)
            $hppRecords = HppProduk::where('id_produk', $id)
                ->orderBy('created_at', 'asc') // FIFO order
                ->get();
            
            $fifoLayers = $hppRecords->map(function($record) {
                return [
                    'date' => $record->created_at->format('Y-m-d'),
                    'quantity' => $record->stok ?? 0,
                    'cost' => $record->hpp ?? 0,
                    'remaining' => $record->stok ?? 0,
                    'production_id' => $record->production_id
                ];
            });
            
            // Calculate average cost
            $totalCost = $hppRecords->sum('hpp');
            $totalQuantity = $hppRecords->sum('stok');
            $averageCost = $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;
            
            $fifoData = [
                'material_id' => $material->id_produk,
                'material_name' => $material->nama_produk,
                'current_stock' => $totalQuantity, // Use calculated total quantity from hpp records
                'average_cost' => $averageCost,
                'fifo_layers' => $fifoLayers
            ];
            
            return response()->json([
                'success' => true,
                'data' => $fifoData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting material FIFO: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data FIFO material'
            ], 500);
        }
    }

    /**
     * Get attendance count for production
     */
    public function getAttendanceCount(Request $request)
    {
        try {
            $outletId = $this->getSelectedOutlet($request);
            $date = $request->get('date', now()->format('Y-m-d'));
            
            $attendanceCount = Attendance::whereDate('tanggal', $date);
            
            if ($outletId !== 'ALL') {
                $attendanceCount->where('id_outlet', $outletId);
            }
            
            $count = $attendanceCount->where('status', 'hadir')->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $date,
                    'attendance_count' => $count,
                    'outlet_id' => $outletId
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting attendance count: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kehadiran'
            ], 500);
        }
    }

    /**
     * Get monthly costs
     */
    public function getMonthlyCosts(Request $request)
    {
        try {
            $outletId = $this->getSelectedOutlet($request);
            $limit = $request->get('limit', 12);
            
            $query = MonthlyProductionCost::with('outlet')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit($limit);
            
            if ($outletId !== 'ALL') {
                $query->where('outlet_id', $outletId);
            }
            
            $costs = $query->get()->map(function($cost) {
                return [
                    'id' => $cost->id,
                    'year' => $cost->year,
                    'month' => $cost->month,
                    'month_name' => date('F', mktime(0, 0, 0, $cost->month, 1)),
                    'period' => date('F Y', mktime(0, 0, 0, $cost->month, 1, $cost->year)),
                    'total_cost' => $cost->total_cost,
                    'electricity_cost' => $cost->electricity_cost,
                    'water_cost' => $cost->water_cost,
                    'fuel_cost' => $cost->fuel_cost,
                    'office_salary_cost' => $cost->office_salary_cost,
                    'other_costs' => $cost->other_costs,
                    'notes' => $cost->notes,
                    'outlet_id' => $cost->outlet_id,
                    'outlet_name' => $cost->outlet ? $cost->outlet->nama_outlet : 'Unknown',
                    'created_at' => $cost->created_at->format('Y-m-d H:i:s')
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $costs
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting monthly costs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data biaya bulanan'
            ], 500);
        }
    }

    /**
     * Store monthly cost
     */
    public function storeMonthlyCost(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'outlet_id' => 'required|exists:outlets,id_outlet',
                'year' => 'required|integer|min:2020|max:2030',
                'month' => 'required|integer|min:1|max:12',
                'total_cost' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $monthlyCost = MonthlyProductionCost::updateOrCreate(
                [
                    'outlet_id' => $request->outlet_id,
                    'year' => $request->year,
                    'month' => $request->month
                ],
                [
                    'total_cost' => $request->total_cost
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Biaya bulanan berhasil disimpan',
                'data' => $monthlyCost
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error storing monthly cost: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan biaya bulanan'
            ], 500);
        }
    }

    /**
     * Delete monthly cost
     */
    public function deleteMonthlyCost($id)
    {
        try {
            $monthlyCost = MonthlyProductionCost::findOrFail($id);
            $monthlyCost->delete();

            return response()->json([
                'success' => true,
                'message' => 'Biaya bulanan berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting monthly cost: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus biaya bulanan'
            ], 500);
        }
    }

    /**
     * Approve production
     */
    public function approve(Request $request, $id)
    {
        try {
            $production = Production::findOrFail($id);
            
            if (!in_array($production->status, ['pending', 'draft'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya produksi dengan status draft atau pending yang dapat disetujui'
                ], 400);
            }
            
            $production->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produksi berhasil disetujui'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error approving production: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui produksi'
            ], 500);
        }
    }

    /**
     * Start production
     */
    public function start(Request $request, $id)
    {
        try {
            $production = Production::findOrFail($id);
            
            if (!in_array($production->status, ['approved', 'pending'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produksi harus dalam status approved atau pending untuk dapat dimulai'
                ], 400);
            }
            
            // Only update status and started_by, do NOT change start_date
            $production->update([
                'status' => 'in_progress',
                'started_by' => auth()->id(),
                'actual_start_date' => now() // Use separate field for actual start time if needed
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produksi berhasil dimulai'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error starting production: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai produksi'
            ], 500);
        }
    }

    /**
     * Complete production
     */
    public function complete(Request $request, $id)
    {
        try {
            $production = Production::with(['materials', 'hppRecords'])->findOrFail($id);
            
            if ($production->status !== 'in_progress') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya produksi yang sedang berlangsung yang dapat diselesaikan'
                ], 400);
            }

            DB::beginTransaction();

            // Check if we should consume remaining materials
            $consumeRemainingMaterials = $request->input('consume_remaining_materials', false);
            
            if ($consumeRemainingMaterials) {
                // Calculate remaining materials and adjust stock
                $this->adjustRemainingMaterials($production);
            }
            
            // Update production status
            $production->update([
                'status' => 'completed',
                'completed_by' => auth()->id(),
                'actual_end_date' => now(),
                'materials_consumed_fully' => $consumeRemainingMaterials
            ]);

            DB::commit();

            Log::info('Production completed successfully', [
                'production_id' => $production->id,
                'production_code' => $production->production_code,
                'consume_remaining_materials' => $consumeRemainingMaterials,
                'completed_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produksi berhasil diselesaikan'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing production: ' . $e->getMessage(), [
                'production_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyelesaikan produksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Adjust remaining materials when completing production
     */
    private function adjustRemainingMaterials($production)
    {
        Log::info('Adjusting remaining materials for production completion', [
            'production_id' => $production->id,
            'production_code' => $production->production_code
        ]);

        foreach ($production->materials as $material) {
            try {
                // Calculate how much material was actually used vs planned
                $plannedQuantity = $material->quantity_required;
                $actualUsedQuantity = $material->quantity_used ?? 0; // This should be tracked during realization
                
                // If actual usage is less than planned, we need to consume the difference
                $remainingQuantity = $plannedQuantity - $actualUsedQuantity;
                
                if ($remainingQuantity > 0) {
                    if ($material->material_type === 'bahan') {
                        // Reduce bahan stock using FIFO method
                        $this->reduceBahanStockFifo($material->material_id, $remainingQuantity, $production);
                        
                        Log::info('Reduced bahan stock for remaining materials', [
                            'material_id' => $material->material_id,
                            'remaining_quantity' => $remainingQuantity,
                            'outlet_id' => $production->outlet_id
                        ]);
                    } elseif ($material->material_type === 'produk') {
                        // Reduce produk stock from hpp_produk
                        $this->reduceProdukStock($material->material_id, $remainingQuantity, $production);
                        
                        Log::info('Reduced produk stock for remaining materials', [
                            'material_id' => $material->material_id,
                            'remaining_quantity' => $remainingQuantity,
                            'outlet_id' => $production->outlet_id
                        ]);
                    }
                    
                    // Update the material record to show it was fully consumed
                    $material->update([
                        'quantity_used' => $plannedQuantity,
                        'fully_consumed' => true
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error adjusting material stock', [
                    'production_id' => $production->id,
                    'material_id' => $material->material_id,
                    'material_type' => $material->material_type,
                    'error' => $e->getMessage()
                ]);
                // Continue with other materials even if one fails
            }
        }
    }

    /**
     * Reduce produk stock from hpp_produk
     */
    private function reduceProdukStock($produkId, $quantity, $production)
    {
        $remainingToReduce = $quantity;
        $outletId = is_object($production) ? $production->outlet_id : $production;
        
        // Get hpp_produk records ordered by date (FIFO)
        $hppRecords = HppProduk::where('id_produk', $produkId)
            ->whereHas('produk', function($query) use ($outletId) {
                $query->where('id_outlet', $outletId);
            })
            ->where('stok', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();
        
        foreach ($hppRecords as $record) {
            if ($remainingToReduce <= 0) break;
            
            $availableStock = $record->stok;
            $toReduce = min($remainingToReduce, $availableStock);
            
            // Update the hpp record stock
            $record->update([
                'stok' => $availableStock - $toReduce
            ]);
            
            $remainingToReduce -= $toReduce;
            
            Log::info('Reduced hpp produk stock', [
                'hpp_record_id' => $record->id,
                'reduced_quantity' => $toReduce,
                'remaining_stock' => $availableStock - $toReduce
            ]);
        }
        
        if ($remainingToReduce > 0) {
            Log::warning('Could not reduce all required produk stock - insufficient stock', [
                'produk_id' => $produkId,
                'outlet_id' => $outletId,
                'requested_reduction' => $quantity,
                'remaining_unreduced' => $remainingToReduce
            ]);
        }
    }

    /**
     * Get products for autocomplete search
     */
    public function getProducts(Request $request)
    {
        $outletId = $this->getSelectedOutlet($request);
        $search = $request->get('search', '');
        
        // Calculate stock from hpp_produk table since produk table doesn't have stok column
        $query = Produk::select(
            'produk.id_produk', 
            'produk.kode_produk', 
            'produk.nama_produk', 
            'produk.harga_jual',
            \DB::raw('COALESCE(SUM(hpp_produk.stok), 0) as total_stock') // Calculate total stock from hpp_produk
        )
        ->leftJoin('hpp_produk', 'produk.id_produk', '=', 'hpp_produk.id_produk')
        ->where('produk.id_outlet', $outletId)
        ->where('produk.is_active', true)
        ->groupBy('produk.id_produk', 'produk.kode_produk', 'produk.nama_produk', 'produk.harga_jual');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('produk.kode_produk', 'like', "%{$search}%")
                  ->orWhere('produk.nama_produk', 'like', "%{$search}%");
            });
        }
        
        $products = $query->limit(10)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id_produk,
                    'code' => $product->kode_produk ?? 'No Code',
                    'name' => $product->nama_produk ?? 'Unknown Product',
                    'price' => $product->harga_jual ?? 0,
                    'stock' => $product->total_stock ?? 0, // Calculated stock from hpp_produk
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Delete production record
     */
    public function destroy($id)
    {
        try {
            $production = Production::findOrFail($id);
            
            // Only allow deletion for draft status
            if ($production->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya produksi dengan status draft yang dapat dihapus'
                ], 403);
            }
            
            Log::info('Production Delete Request', [
                'production_id' => $id,
                'production_code' => $production->production_code,
                'status' => $production->status,
                'user_id' => auth()->id()
            ]);
            
            DB::beginTransaction();
            
            // Delete related records first
            // Delete HPP records
            HppProduk::where('production_id', $production->id)->delete();
            
            // Delete materials
            ProductionMaterial::where('production_id', $production->id)->delete();
            
            // Delete labor costs
            ProductionLaborCost::where('production_id', $production->id)->delete();
            
            // Delete operational costs
            ProductionOperationalCost::where('production_id', $production->id)->delete();
            
            // Delete realizations
            ProductionRealization::where('production_id', $production->id)->delete();
            
            // Delete the production record
            $production->delete();
            
            DB::commit();
            
            Log::info('Production Deleted Successfully', [
                'production_id' => $id,
                'production_code' => $production->production_code
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Produksi berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Production Delete Error', [
                'production_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reduce material stock using FIFO system when production is realized
     */
    private function reduceMaterialStock($production, $totalProduced)
    {
        try {
            Log::info('🔄 [INVENTORY] Starting material stock reduction', [
                'production_id' => $production->id,
                'production_code' => $production->production_code,
                'total_produced' => $totalProduced,
                'target_quantity' => $production->target_quantity
            ]);

            // Calculate the ratio of production vs target to determine material usage
            $productionRatio = $production->target_quantity > 0 ? $totalProduced / $production->target_quantity : 1;
            
            foreach ($production->materials as $material) {
                if ($material->material_type !== 'bahan') {
                    continue; // Only process bahan materials, skip produk materials
                }
                
                // Calculate actual material usage based on production ratio
                $requiredQuantity = $material->quantity_required * $productionRatio;
                
                Log::info('🔄 [INVENTORY] Processing material', [
                    'material_id' => $material->material_id,
                    'material_type' => $material->material_type,
                    'planned_quantity' => $material->quantity_required,
                    'actual_quantity_needed' => $requiredQuantity,
                    'production_ratio' => $productionRatio,
                    'previous_quantity_used' => $material->quantity_used ?? 0
                ]);
                
                // Reduce stock using FIFO system
                $this->reduceBahanStockFifo($material->material_id, $requiredQuantity, $production);
                
                // Update quantity_used to track actual material consumption
                // This is cumulative - add to existing quantity_used
                $currentQuantityUsed = $material->quantity_used ?? 0;
                $newQuantityUsed = $currentQuantityUsed + $requiredQuantity;
                
                $material->update([
                    'quantity_used' => $newQuantityUsed
                ]);
                
                Log::info('📊 [INVENTORY] Updated material quantity_used', [
                    'material_id' => $material->material_id,
                    'previous_quantity_used' => $currentQuantityUsed,
                    'quantity_added' => $requiredQuantity,
                    'new_quantity_used' => $newQuantityUsed,
                    'planned_quantity' => $material->quantity_required,
                    'remaining' => $material->quantity_required - $newQuantityUsed
                ]);
            }
            
            Log::info('✅ [INVENTORY] Material stock reduction completed', [
                'production_id' => $production->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ [INVENTORY] Error reducing material stock', [
                'production_id' => $production->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Reduce bahan stock using FIFO system
     */
    private function reduceBahanStockFifo($bahanId, $quantityNeeded, $production)
    {
        try {
            Log::info('🔄 [FIFO] Starting FIFO stock reduction', [
                'bahan_id' => $bahanId,
                'quantity_needed' => $quantityNeeded,
                'production_id' => $production->id
            ]);

            // Get available stock batches ordered by FIFO (oldest first)
            // Join with bahan table to filter by outlet since harga_bahan doesn't have id_outlet column
            $stockBatches = DB::table('harga_bahan')
                ->join('bahan', 'harga_bahan.id_bahan', '=', 'bahan.id_bahan')
                ->where('harga_bahan.id_bahan', $bahanId)
                ->where('bahan.id_outlet', $production->outlet_id)
                ->where('harga_bahan.stok', '>', 0)
                ->orderBy('harga_bahan.created_at', 'asc') // FIFO: oldest first
                ->select('harga_bahan.*') // Only select harga_bahan columns
                ->get();

            if ($stockBatches->isEmpty()) {
                Log::warning('⚠️ [FIFO] No stock available for bahan', [
                    'bahan_id' => $bahanId,
                    'outlet_id' => $production->outlet_id
                ]);
                return;
            }

            $remainingNeeded = $quantityNeeded;
            $totalAvailable = $stockBatches->sum('stok');
            
            Log::info('📊 [FIFO] Stock analysis', [
                'bahan_id' => $bahanId,
                'total_available' => $totalAvailable,
                'quantity_needed' => $quantityNeeded,
                'batches_count' => $stockBatches->count()
            ]);

            if ($totalAvailable < $quantityNeeded) {
                Log::warning('⚠️ [FIFO] Insufficient stock available', [
                    'bahan_id' => $bahanId,
                    'available' => $totalAvailable,
                    'needed' => $quantityNeeded,
                    'shortage' => $quantityNeeded - $totalAvailable
                ]);
                // Continue with available stock
                $remainingNeeded = $totalAvailable;
            }

            // Process each batch in FIFO order
            foreach ($stockBatches as $batch) {
                if ($remainingNeeded <= 0) {
                    break;
                }

                $quantityToReduce = min($batch->stok, $remainingNeeded);
                $newStock = $batch->stok - $quantityToReduce;

                // Update the batch stock
                DB::table('harga_bahan')
                    ->where('id', $batch->id)
                    ->update([
                        'stok' => $newStock,
                        'updated_at' => now()
                    ]);

                Log::info('📉 [FIFO] Stock reduced from batch', [
                    'batch_id' => $batch->id,
                    'bahan_id' => $bahanId,
                    'original_stock' => $batch->stok,
                    'quantity_reduced' => $quantityToReduce,
                    'new_stock' => $newStock,
                    'batch_date' => $batch->created_at
                ]);

                $remainingNeeded -= $quantityToReduce;
            }

            Log::info('✅ [FIFO] FIFO stock reduction completed', [
                'bahan_id' => $bahanId,
                'total_reduced' => $quantityNeeded - $remainingNeeded,
                'remaining_needed' => $remainingNeeded
            ]);

        } catch (\Exception $e) {
            Log::error('❌ [FIFO] Error in FIFO stock reduction', [
                'bahan_id' => $bahanId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Export bulk production PDF report
     */
    public function exportBulkProductionPdf(Request $request)
    {
        try {
            Log::info('🎯 [BULK EXPORT] Bulk Production PDF export started', [
                'filters' => $request->all(),
                'user_id' => auth()->id()
            ]);

            $outletId = $this->getSelectedOutlet($request);
            
            // Get company settings for header
            $companySetting = DB::table('company_settings')->first();
            if (!$companySetting) {
                // Try alternative table names
                $alternativeNames = ['company_setting', 'settings', 'company_profiles'];
                foreach ($alternativeNames as $tableName) {
                    try {
                        $companySetting = DB::table($tableName)->first();
                        if ($companySetting) break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
            
            // Default company info if not found
            $companyName = $companySetting->company_name ?? 'PT. PELITA NUSANTARA INDONESIA';
            $companyLogo = $companySetting->company_logo ?? null;
            
            // Build query with same filters as getData method
            $query = Production::with([
                'outlet',
                'hppRecords.produk',
                'materials', // Remove .bahan relationship as it doesn't exist
                'laborCosts',
                'operationalCosts',
                'realizations'
            ]);

            // Apply outlet filter
            if ($outletId !== 'ALL') {
                $query->where('outlet_id', $outletId);
            }

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('production_code', 'like', "%{$search}%")
                      ->orWhereHas('hppRecords.produk', function($q) use ($search) {
                          $q->where('nama_produk', 'like', "%{$search}%");
                      });
                });
            }

            // Apply status filter
            if ($request->filled('status') && $request->status !== 'ALL') {
                $query->where('status', $request->status);
            }

            // Apply production line filter
            if ($request->filled('production_line') && $request->production_line !== 'ALL') {
                $query->where('production_line', $request->production_line);
            }

            // Apply sorting
            $sortKey = $request->get('sort_key', 'created_at');
            $sortDir = $request->get('sort_dir', 'desc');
            $query->orderBy($sortKey, $sortDir);

            // Get all matching productions
            $productions = $query->get();

            Log::info('🎯 [BULK EXPORT] Productions retrieved', [
                'count' => $productions->count(),
                'outlet_id' => $outletId
            ]);

            // Prepare data for PDF
            $exportData = $productions->map(function($production) {
                // Get product names
                $productNames = $production->hppRecords->map(function($hpp) {
                    return $hpp->produk ? $hpp->produk->nama_produk : 'Unknown Product';
                })->unique()->toArray();
                $productNameDisplay = !empty($productNames) ? implode(', ', $productNames) : 'Produk tidak ditemukan';

                // Calculate costs using FIFO pricing (consistent with other methods)
                $materialCost = $production->materials->sum(function($material) {
                    return $material->quantity_required * $this->getFifoPrice($material->material_id, $material->material_type);
                });

                $laborCost = $production->laborCosts->sum('total_cost');
                $operationalCost = $production->operationalCosts->sum('amount');
                $totalCost = $materialCost + $laborCost + $operationalCost;
                $hppPerUnit = $production->target_quantity > 0 ? $totalCost / $production->target_quantity : 0;
                    Log::info('💰 [HPP CALCULATION] Material cost breakdown', [
                        'production_id' => $production->id,
                        'material_cost' => $materialCost,
                        'labor_cost' => $laborCost,
                        'operational_cost' => $operationalCost,
                        'total_cost' => $totalCost,
                        'hpp_per_unit' => $hppPerUnit,
                        'target_quantity' => $production->target_quantity
                    ]);

                // Get reject data from realizations (FIX: was using non-existent column)
                $rejectedQuantity = $production->realizations->sum('quantity_rejected');
                
                // Calculate progress
                $progress = $production->target_quantity > 0 ? 
                    min(100, ($production->realized_quantity / $production->target_quantity) * 100) : 0;

                return [
                    'production_code' => $production->production_code,
                    'product_name' => $productNameDisplay,
                    'production_line' => $production->production_line,
                    'target_quantity' => $production->target_quantity,
                    'realized_quantity' => $production->realized_quantity ?? 0,
                    'rejected_quantity' => $rejectedQuantity, // FIX: Now gets actual data from realizations
                    'progress' => round($progress, 1),
                    'status' => $production->status,
                    'status_text' => $this->getStatusText($production->status),
                    'priority' => $production->priority,
                    'priority_text' => $this->getPriorityText($production->priority),
                    'start_date' => $production->start_date ? $production->start_date->format('d/m/Y') : '-',
                    'end_date' => $production->end_date ? $production->end_date->format('d/m/Y') : '-',
                    'expiry_date' => $production->expiry_date ? $production->expiry_date->format('d/m/Y') : '-',
                    'hpp_per_unit' => $hppPerUnit, // Keep as number for statistics
                    'hpp_per_unit_formatted' => 'Rp ' . number_format($hppPerUnit, 0, ',', '.'),
                    'total_cost' => $totalCost, // Keep as number for statistics
                    'total_cost_formatted' => 'Rp ' . number_format($totalCost, 0, ',', '.'),
                    'outlet_name' => $production->outlet ? $production->outlet->nama_outlet : 'Unknown Outlet',
                    'created_at' => $production->created_at->format('d/m/Y H:i'),
                ];
            });

            // Calculate statistics for PDF
            $totalCount = $exportData->count();
            $statistics = [
                'total_productions' => $totalCount,
                'total_target' => $exportData->sum('target_quantity'),
                'total_realized' => $exportData->sum('realized_quantity'),
                'total_rejected' => $exportData->sum('rejected_quantity'),
                'avg_hpp' => $totalCount > 0 ? $exportData->avg('hpp_per_unit') : 0,
                'avg_target' => $totalCount > 0 ? $exportData->avg('target_quantity') : 0,
                'avg_realized' => $totalCount > 0 ? $exportData->avg('realized_quantity') : 0,
                'total_cost' => $exportData->sum('total_cost'),
            ];

            // Generate PDF
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('admin.produksi.produksi.bulk-production-pdf', [
                'productions' => $exportData,
                'statistics' => $statistics, // Add statistics
                'companyName' => $companyName,
                'companyLogo' => $companyLogo,
                'filters' => [
                    'outlet' => $outletId !== 'ALL' ? $productions->first()?->outlet?->nama_outlet : 'Semua Outlet',
                    'status' => $request->status !== 'ALL' ? $this->getStatusText($request->status) : 'Semua Status',
                    'production_line' => $request->production_line !== 'ALL' ? $request->production_line : 'Semua Lini',
                    'search' => $request->search ?? '',
                    'export_date' => now()->format('d/m/Y H:i:s'),
                    'total_count' => $totalCount
                ]
            ]);
            $pdf->setPaper('A4', 'landscape'); // Landscape for better table display

            $filename = 'Laporan_Produksi_Bulk_' . date('Y-m-d_H-i-s') . '.pdf';

            Log::info('🎯 [BULK EXPORT] Bulk Production PDF generated successfully', [
                'filename' => $filename,
                'productions_count' => $totalCount,
                'statistics' => $statistics
            ]);

            return $pdf->stream($filename);

        } catch (\Exception $e) {
            Log::error('❌ [BULK EXPORT] Bulk Production PDF export error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'filters' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export QC Egg Tofu Mentah PDF report with professional format
     */
    public function exportQcTofuMentahPdf(Request $request)
    {
        try {
            Log::info('🎯 [QC TOFU EXPORT] QC Egg Tofu Mentah PDF export started', [
                'filters' => $request->all(),
                'user_id' => auth()->id()
            ]);

            $outletId = $this->getSelectedOutlet($request);
            
            // Get company settings for header
            $companySetting = DB::table('company_settings')->first();
            if (!$companySetting) {
                // Try alternative table names
                $alternativeNames = ['company_setting', 'settings', 'company_profiles'];
                foreach ($alternativeNames as $tableName) {
                    try {
                        $companySetting = DB::table($tableName)->first();
                        if ($companySetting) break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
            
            // Default company info if not found
            $companyName = $companySetting->company_name ?? 'PT.PELITA NUSANTARA INDONESIA';
            $companyLogo = $companySetting->company_logo ?? null;
            
            // Build query for tofu productions
            $query = Production::with([
                'outlet',
                'hppRecords.produk'
            ])->where('business_type', 'tofu')
              ->whereNotNull('tofu_data');

            // Apply outlet filter
            if ($outletId !== 'ALL') {
                $query->where('outlet_id', $outletId);
            }

            // Apply date filter for period
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            if ($startDate && $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->whereDate('start_date', '>=', $startDate);
            } elseif ($endDate) {
                $query->whereDate('start_date', '<=', $endDate);
            }

            // Apply other filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('production_code', 'like', "%{$search}%")
                      ->orWhereHas('hppRecords.produk', function($q) use ($search) {
                          $q->where('nama_produk', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('status') && $request->status !== 'ALL') {
                $query->where('status', $request->status);
            }

            if ($request->filled('production_line') && $request->production_line !== 'ALL') {
                $query->where('production_line', $request->production_line);
            }

            // Order by production date
            $query->orderBy('start_date', 'asc');

            // Get productions
            $productions = $query->get();

            Log::info('🎯 [QC TOFU EXPORT] Productions retrieved', [
                'count' => $productions->count(),
                'outlet_id' => $outletId
            ]);

            // Determine period for header
            $period = 'Semua Periode';
            if ($productions->isNotEmpty()) {
                $firstDate = $productions->first()->start_date;
                $lastDate = $productions->last()->start_date;
                
                if ($firstDate && $lastDate) {
                    if ($firstDate->format('Y-m') === $lastDate->format('Y-m')) {
                        // Same month
                        $period = $firstDate->format('F Y');
                    } else {
                        // Different months
                        $period = $firstDate->format('F Y') . ' - ' . $lastDate->format('F Y');
                    }
                }
            }

            // Process QC data
            $qcData = $productions->map(function($production, $index) {
                // Parse tofu_data JSON (use same pattern as individual QC PDF)
                $tofuData = [];
                if ($production->tofu_data) {
                    $tofuData = json_decode($production->tofu_data, true) ?: [];
                }

                // Get product code (first product or production code)
                $productCode = $production->production_code;
                if ($production->hppRecords->isNotEmpty()) {
                    $firstProduct = $production->hppRecords->first()->produk;
                    if ($firstProduct && $firstProduct->kode_produk) {
                        $productCode = $firstProduct->kode_produk;
                    }
                }

                return [
                    'no' => $index + 1,
                    'tanggal_produksi' => $production->start_date ? $production->start_date->format('d/m/Y') : '-',
                    'kode_produk' => $productCode,
                    
                    // Perendaman Kacang Kedelai
                    'perendaman_waktu' => $tofuData['perendaman_waktu'] ?? '-',
                    'perendaman_kuantitas' => $tofuData['perendaman_qty'] ?? ($tofuData['perendaman_kuantitas'] ?? '-'),
                    
                    // Jumlah Reject Telur
                    'reject_telur_kuantitas' => $tofuData['rijek_telur'] ?? ($tofuData['reject_telur_kuantitas'] ?? '-'),
                    
                    // Pasteurisasi
                    'pasteurisasi_waktu' => $tofuData['pasteurisasi_waktu'] ?? '-',
                    'pasteurisasi_suhu' => $tofuData['pasteurisasi_suhu'] ?? '-',
                    
                    // Berat Akhir Sari Kedelai
                    'berat_akhir_sari_kedelai' => $tofuData['berat_sari_kedelai'] ?? ($tofuData['berat_akhir_sari_kedelai'] ?? '-'),
                    
                    // Pencampuran
                    'pencampuran_waktu' => $tofuData['waktu_pencampuran'] ?? ($tofuData['pencampuran_waktu'] ?? '-'),
                    
                    // Filling & Pengemasan
                    'filling_waktu' => $tofuData['filling_waktu'] ?? '-',
                    'filling_kuantitas' => $tofuData['filling_kuantitas'] ?? ($production->target_quantity ?? '-'),
                    'filling_mesin_1' => $tofuData['filling_mesin1'] ?? ($tofuData['mesin_1'] ?? '-'),
                    'filling_mesin_2' => $tofuData['filling_mesin2'] ?? ($tofuData['mesin_2'] ?? '-'),
                    
                    // Total & Reject
                    'total_kuantitas' => $tofuData['filling_total'] ?? ($tofuData['total_kuantitas'] ?? ($production->realized_quantity ?? '-')),
                    'jumlah_reject_mentah' => $tofuData['rijek_mentah'] ?? ($tofuData['jumlah_reject_mentah'] ?? ($production->rejected_quantity ?? '-')),
                ];
            });

            // Generate auto document number and current date
            $documentNumber = 'PNI/FSOP/QC/01-' . now()->format('y');
            $currentDate = now()->format('d F Y');
            $revision = '00';

            // Generate PDF
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('admin.produksi.produksi.qc-tofu-mentah-pdf', [
                'qcData' => $qcData,
                'companyName' => $companyName,
                'companyLogo' => $companyLogo,
                'period' => $period,
                'exportDate' => now()->format('d/m/Y H:i:s'),
                'totalRecords' => $qcData->count(),
                'documentNumber' => $documentNumber,
                'currentDate' => $currentDate,
                'revision' => $revision
            ]);
            
            $pdf->setPaper('A4', 'landscape');

            // Generate filename
            $monthYear = now()->format('m_Y');
            if ($productions->isNotEmpty() && $productions->first()->start_date) {
                $monthYear = $productions->first()->start_date->format('m_Y');
            }
            
            $filename = "qc_tofu_mentah_{$monthYear}.pdf";

            Log::info('🎯 [QC TOFU EXPORT] PDF generated successfully', [
                'filename' => $filename,
                'records_count' => $qcData->count()
            ]);

            return $pdf->stream($filename);

        } catch (\Exception $e) {
            Log::error('❌ [QC TOFU EXPORT] Export error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'filters' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan QC PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Legacy export PDF method (redirects to bulk production PDF)
     */
    public function exportPdf(Request $request)
    {
        return $this->exportBulkProductionPdf($request);
    }

    /**
     * Export Excel method (placeholder for existing functionality)
     */
    public function exportExcel(Request $request)
    {
        try {
            $outletId = $this->getSelectedOutlet($request);
            
            // Use existing ProductionsExport class if available
            return Excel::download(new ProductionsExport($request->all()), 'productions_' . date('Y-m-d_H-i-s') . '.xlsx');
            
        } catch (\Exception $e) {
            Log::error('Excel Export Error', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan Excel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to get status text
     */
    private function getStatusText($status)
    {
        $statusMap = [
            'draft' => 'Draft',
            'pending' => 'Pending',
            'approved' => 'Disetujui',
            'in_progress' => 'Berjalan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];

        return $statusMap[$status] ?? ucfirst($status);
    }

    /**
     * Helper method to get priority text
     */
    private function getPriorityText($priority)
    {
        $priorityMap = [
            'normal' => 'Normal',
            'high' => 'Tinggi',
            'urgent' => 'Mendesak'
        ];

        return $priorityMap[$priority] ?? ucfirst($priority);
    }

}