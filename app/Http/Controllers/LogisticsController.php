<?php

namespace App\Http\Controllers;

use App\Models\EquipmentChecklist;
use App\Models\Keberangkatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\HasOutletFilter;

class LogisticsController extends Controller
{
    use HasOutletFilter;
    
    public function __construct()
    {
        $this->middleware('permission:travel.logistics.view')->only(['show']);
        $this->middleware('permission:travel.logistics.create')->only(['store', 'initializeDefaults']);
        $this->middleware('permission:travel.logistics.update')->only(['update', 'updateStatus']);
        $this->middleware('permission:travel.logistics.delete')->only(['destroy']);
        // Index and packingList accessible to anyone with keberangkatan access
    }
    
    /**
     * Display equipment checklist for a keberangkatan
     */
    public function index($keberangkatanId)
    {
        try {
            $keberangkatan = Keberangkatan::with(['equipmentChecklists', 'travelPackage'])
                ->findOrFail($keberangkatanId);

            $equipmentItems = $keberangkatan->equipmentChecklists()
                ->orderBy('equipment_category')
                ->orderBy('equipment_name')
                ->get();

            // Get statistics
            $stats = [
                'total_items' => $equipmentItems->count(),
                'not_ordered' => $equipmentItems->where('status', 'not_ordered')->count(),
                'ordered' => $equipmentItems->where('status', 'ordered')->count(),
                'received' => $equipmentItems->where('status', 'received')->count(),
                'packed' => $equipmentItems->where('status', 'packed')->count(),
                'shipped' => $equipmentItems->where('status', 'shipped')->count(),
                'approaching_deadline' => $equipmentItems->filter(fn($item) => $item->isDeadlineApproaching())->count(),
                'overdue' => $equipmentItems->filter(fn($item) => $item->isDeadlineOverdue())->count(),
            ];

            return view('admin.travel.logistics.index', compact('keberangkatan', 'equipmentItems', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading logistics page: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load logistics page');
        }
    }

    /**
     * Store a new equipment item
     */
    public function store(Request $request, $keberangkatanId)
    {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:255',
            'equipment_category' => 'nullable|string|max:255',
            'quantity_needed' => 'required|integer|min:1',
            'supplier_name' => 'nullable|string|max:255',
            'order_date' => 'nullable|date',
            'shipping_deadline' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string'
        ]);

        try {
            $keberangkatan = Keberangkatan::findOrFail($keberangkatanId);

            $equipment = EquipmentChecklist::create([
                'id_keberangkatan' => $keberangkatan->id,
                'equipment_name' => $validated['equipment_name'],
                'equipment_category' => $validated['equipment_category'] ?? null,
                'quantity_needed' => $validated['quantity_needed'],
                'quantity_received' => 0,
                'status' => 'not_ordered',
                'supplier_name' => $validated['supplier_name'] ?? null,
                'order_date' => $validated['order_date'] ?? null,
                'shipping_deadline' => $validated['shipping_deadline'] ?? null,
                'notes' => $validated['notes'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Equipment item added successfully',
                'data' => $equipment
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating equipment item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add equipment item'
            ], 500);
        }
    }

    /**
     * Update equipment item
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:255',
            'equipment_category' => 'nullable|string|max:255',
            'quantity_needed' => 'required|integer|min:1',
            'quantity_received' => 'required|integer|min:0',
            'status' => 'required|in:not_ordered,ordered,received,packed,shipped',
            'supplier_name' => 'nullable|string|max:255',
            'order_date' => 'nullable|date',
            'shipping_deadline' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        try {
            $equipment = EquipmentChecklist::findOrFail($id);
            
            // Validate quantity received doesn't exceed quantity needed
            if ($validated['quantity_received'] > $validated['quantity_needed']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quantity received cannot exceed quantity needed'
                ], 422);
            }

            $equipment->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Equipment item updated successfully',
                'data' => $equipment
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating equipment item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update equipment item'
            ], 500);
        }
    }

    /**
     * Update equipment status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:not_ordered,ordered,received,packed,shipped',
            'quantity_received' => 'nullable|integer|min:0'
        ]);

        try {
            $equipment = EquipmentChecklist::findOrFail($id);
            
            $equipment->status = $validated['status'];
            
            if (isset($validated['quantity_received'])) {
                if ($validated['quantity_received'] > $equipment->quantity_needed) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Quantity received cannot exceed quantity needed'
                    ], 422);
                }
                $equipment->quantity_received = $validated['quantity_received'];
            }
            
            $equipment->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => $equipment
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating equipment status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    /**
     * Delete equipment item
     */
    public function destroy($id)
    {
        try {
            $equipment = EquipmentChecklist::findOrFail($id);
            $equipment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Equipment item deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting equipment item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete equipment item'
            ], 500);
        }
    }

    /**
     * Initialize default equipment checklist for a keberangkatan
     */
    public function initializeDefaults($keberangkatanId)
    {
        try {
            $keberangkatan = Keberangkatan::findOrFail($keberangkatanId);
            
            // Check if checklist already exists
            if ($keberangkatan->equipmentChecklists()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Equipment checklist already exists for this keberangkatan'
                ], 422);
            }

            $jamaahCount = $keberangkatan->getConfirmedJamaahCount();

            // Default equipment categories with quantities
            $defaultEquipment = [
                ['category' => 'Bagasi', 'name' => 'Label Bagasi', 'qty_per_person' => 2],
                ['category' => 'Perlengkapan Perjalanan', 'name' => 'Tas Perjalanan', 'qty_per_person' => 1],
                ['category' => 'Ibadah', 'name' => 'Sajadah', 'qty_per_person' => 1],
                ['category' => 'Dokumentasi', 'name' => 'Buku Panduan', 'qty_per_person' => 1],
                ['category' => 'Medis', 'name' => 'Kotak P3K', 'qty_per_person' => 0.1], // 1 per 10 orang
                ['category' => 'Identifikasi', 'name' => 'Kartu Identitas', 'qty_per_person' => 1],
                ['category' => 'Kenyamanan', 'name' => 'Bantal Leher', 'qty_per_person' => 1],
                ['category' => 'Hidrasi', 'name' => 'Botol Minum', 'qty_per_person' => 1],
            ];

            DB::beginTransaction();

            foreach ($defaultEquipment as $item) {
                EquipmentChecklist::create([
                    'id_keberangkatan' => $keberangkatan->id,
                    'equipment_name' => $item['name'],
                    'equipment_category' => $item['category'],
                    'quantity_needed' => max(1, (int)ceil($jamaahCount * $item['qty_per_person'])),
                    'quantity_received' => 0,
                    'status' => 'not_ordered'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Default equipment checklist initialized successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error initializing equipment checklist: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize equipment checklist'
            ], 500);
        }
    }

    /**
     * Generate packing list PDF
     */
    public function generatePackingList($keberangkatanId)
    {
        try {
            $keberangkatan = Keberangkatan::with(['equipmentChecklists', 'travelPackage'])
                ->findOrFail($keberangkatanId);

            $equipmentItems = $keberangkatan->equipmentChecklists()
                ->orderBy('equipment_category')
                ->orderBy('equipment_name')
                ->get();

            // Group by category
            $groupedEquipment = $equipmentItems->groupBy('equipment_category');

            $pdf = Pdf::loadView('admin.travel.logistics.packing-list-pdf', [
                'keberangkatan' => $keberangkatan,
                'groupedEquipment' => $groupedEquipment,
                'generatedAt' => now()
            ]);

            return $pdf->download('packing-list-' . $keberangkatan->keberangkatan_code . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error generating packing list: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate packing list');
        }
    }
}
