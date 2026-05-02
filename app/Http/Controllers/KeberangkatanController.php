<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keberangkatan;
use App\Models\TravelPackage;
use App\Models\Member;
use App\Models\JamaahBooking;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\KeberangkatanVisa;
use App\Models\KeberangkatanReminder;
use App\Services\RabIntegrationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\HasOutletFilter;

class KeberangkatanController extends Controller
{
    use HasOutletFilter;
    
    protected $rabService;

    public function __construct(RabIntegrationService $rabService)
    {
        $this->rabService = $rabService;
        $this->middleware('permission:travel.keberangkatan.view')->only(['index', 'getData', 'show', 'getRabData', 'getBudgetVariance', 'getFinancialSummary', 'getKeberangkatanRabModal']);
        $this->middleware('permission:travel.keberangkatan.create')->only(['store', 'createRab']);
        $this->middleware('permission:travel.keberangkatan.edit')->only(['update', 'assignJamaah', 'removeJamaah', 'updateRabRealisasi', 'updateKeberangkatanRabItem', 'sesuaikanLaporan', 'resetPenyesuaianLaporan']);
        $this->middleware('permission:travel.keberangkatan.delete')->only(['destroy']);
    }

    /**
     * Display keberangkatan management page
     */
    public function index()
    {
        Log::info('Loading Keberangkatan Index Page');
        return view('admin.travel.keberangkatan.index');
    }

    /**
     * Get keberangkatan data for DataTables
     */
    public function getData(Request $request)
    {
        Log::info('Fetching Keberangkatan Data with filters', $request->all());

        $query = Keberangkatan::with(['outlet', 'travelPackage']);

        // Get user's outlet filter
        $user = Auth::user();
        if ($user && method_exists($user, 'outlets')) {
            $userOutlets = $user->outlets->pluck('id_outlet')->toArray();
            if (!empty($userOutlets)) {
                $query->whereIn('id_outlet', $userOutlets);
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->has('status_filter') && $request->status_filter !== 'ALL') {
            $query->withStatus($request->status_filter);
        }

        // Filter by package
        if ($request->has('package_filter') && $request->package_filter !== 'ALL') {
            $query->forPackage($request->package_filter);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Sorting
        $sortColumn = $request->sort_key ?? 'id';
        $sortDirection = $request->sort_dir ?? 'desc';
        
        $columnMapping = [
            'code' => 'keberangkatan_code',
            'name' => 'keberangkatan_name',
            'departure' => 'departure_date',
            'status' => 'status'
        ];
        
        $sortColumn = $columnMapping[$sortColumn] ?? $sortColumn;
        $query->orderBy($sortColumn, $sortDirection);

        $keberangkatans = $query->get();

        return datatables()
            ->of($keberangkatans)
            ->addIndexColumn()
            ->addColumn('code', function ($keberangkatan) {
                return $keberangkatan->keberangkatan_code;
            })
            ->addColumn('name', function ($keberangkatan) {
                return $keberangkatan->keberangkatan_name;
            })
            ->addColumn('package', function ($keberangkatan) {
                return $keberangkatan->travelPackage ? $keberangkatan->travelPackage->package_name : '-';
            })
            ->addColumn('departure_date', function ($keberangkatan) {
                return $keberangkatan->departure_date ? $keberangkatan->departure_date->format('d M Y') : '-';
            })
            ->addColumn('return_date', function ($keberangkatan) {
                return $keberangkatan->return_date ? $keberangkatan->return_date->format('d M Y') : '-';
            })
            ->addColumn('jamaah_count', function ($keberangkatan) {
                // Count jamaah assigned to this keberangkatan
                $assignedToKeberangkatan = $keberangkatan->getConfirmedJamaahCount();
                
                // Count jamaah from same package but not assigned to any keberangkatan yet
                $unassignedFromPackage = JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
                    ->whereNull('id_keberangkatan')
                    ->whereNotIn('status', ['cancelled'])
                    ->count();
                
                $total = $keberangkatan->total_jamaah;
                $confirmed = $assignedToKeberangkatan + $unassignedFromPackage;
                $available = $total - $confirmed;
                
                $html = '<div class="text-sm">';
                $html .= '<div class="font-medium">' . $confirmed . ' / ' . $total . '</div>';
                
                if ($assignedToKeberangkatan > 0) {
                    $html .= '<div class="text-xs text-green-600">' . $assignedToKeberangkatan . ' terdaftar</div>';
                }
                
                if ($unassignedFromPackage > 0) {
                    $html .= '<div class="text-xs text-orange-600">' . $unassignedFromPackage . ' belum ditugaskan</div>';
                }
                
                if ($available > 0) {
                    $html .= '<div class="text-xs text-slate-500">' . $available . ' tersedia</div>';
                } else {
                    $html .= '<div class="text-xs text-red-600">Penuh</div>';
                }
                
                $html .= '</div>';
                
                return $html;
            })
            ->addColumn('status', function ($keberangkatan) {
                $badges = [
                    'planning' => 'bg-gray-100 text-gray-800',
                    'confirmed' => 'bg-green-100 text-green-800',
                    'departed' => 'bg-blue-100 text-blue-800',
                    'completed' => 'bg-purple-100 text-purple-800'
                ];
                $badge = $badges[$keberangkatan->status] ?? 'bg-gray-100 text-gray-800';
                return '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $badge . '">' . strtoupper($keberangkatan->status) . '</span>';
            })
            ->addColumn('outlet', function ($keberangkatan) {
                return $keberangkatan->outlet ? $keberangkatan->outlet->nama_outlet : '-';
            })
            ->addColumn('aksi', function ($keberangkatan) {
                $detailUrl = route('admin.inventaris.travel.keberangkatan.show', $keberangkatan->id);
                return '
                    <div class="flex justify-end gap-2">
                        <button x-on:click="showDetail({id: ' . $keberangkatan->id . '})" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50 text-xs">
                            <i class="bx bx-show"></i> Detail
                        </button>
                        <button x-on:click="openEdit({id: ' . $keberangkatan->id . '})" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50 text-xs">
                            <i class="bx bx-edit-alt"></i> Edit
                        </button>
                        <button x-on:click="confirmDelete({id: ' . $keberangkatan->id . ', name: \'' . addslashes($keberangkatan->keberangkatan_name) . '\', code: \'' . $keberangkatan->keberangkatan_code . '\', package: \'' . addslashes($keberangkatan->travelPackage ? $keberangkatan->travelPackage->package_name : '-') . '\'})" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50 text-xs">
                            <i class="bx bx-trash"></i> Hapus
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['status', 'aksi', 'jamaah_count'])
            ->make(true);
    }

    /**
     * Store a new keberangkatan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keberangkatan_code' => 'required|string|max:255|unique:keberangkatan,keberangkatan_code',
            'keberangkatan_name' => 'required|string|max:255',
            'id_travel_package' => 'required|exists:travel_packages,id',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'total_jamaah' => 'required|integer|min:1',
            'id_outlet' => 'required|exists:outlets,id_outlet'
        ], [
            'return_date.after_or_equal' => 'Tanggal kembali harus sama atau setelah tanggal keberangkatan',
            'total_jamaah.min' => 'Total jamaah harus berupa bilangan positif'
        ]);

        if ($validator->fails()) {
            Log::warning('Keberangkatan store validation failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);
            return response()->json([
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $keberangkatan = Keberangkatan::create(array_merge(
                $request->all(),
                ['status' => 'planning']
            ));

            Log::info('Keberangkatan created successfully', ['keberangkatan_id' => $keberangkatan->id]);

            return response()->json([
                'message' => 'Keberangkatan berhasil disimpan',
                'data' => $keberangkatan
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error creating keberangkatan: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menyimpan keberangkatan'
            ], 500);
        }
    }

    /**
     * Show a specific keberangkatan
     */
    public function show(Request $request, $id)
    {
        $keberangkatan = Keberangkatan::with([
            'outlet',
            'travelPackage.flightDeparture',
            'travelPackage.flightReturn',
            'travelPackage.hotelMakkah',
            'travelPackage.hotelRoomTypeMakkah',
            'travelPackage.hotelMadinah',
            'travelPackage.hotelRoomTypeMadinah',
            'jamaahBookings.jamaah'
        ])->find($id);

        if (!$keberangkatan) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Keberangkatan tidak ditemukan'], 404);
            }
            abort(404);
        }

        // Get ALL jamaah bookings from the same package (both assigned and unassigned)
        $allPackageBookings = JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
            ->with('jamaah')
            ->whereNotIn('status', ['cancelled'])
            ->get();

        // Separate into assigned and unassigned
        $assignedToThisKeberangkatan = $allPackageBookings->where('id_keberangkatan', $keberangkatan->id);
        $unassignedBookings = $allPackageBookings->whereNull('id_keberangkatan');

        // Get available flights from the travel package (not all flights from inventory)
        $package = $keberangkatan->travelPackage;
        $availableFlights = collect();
        
        if ($package) {
            // Add departure flight if exists
            if ($package->id_flight_departure && $package->flightDeparture) {
                $flight = $package->flightDeparture;
                $availableFlights->push([
                    'id' => $flight->id,
                    'flight_number' => $flight->flight_number,
                    'airline' => $flight->airline_name,
                    'route' => $flight->departure_airport . ' - ' . $flight->arrival_airport,
                    'departure_time' => $package->departure_datetime ? \Carbon\Carbon::parse($package->departure_datetime)->format('d/m/Y H:i') : ($flight->departure_time ? \Carbon\Carbon::parse($flight->departure_time)->format('d/m/Y H:i') : null),
                    'arrival_time' => $flight->arrival_time ? \Carbon\Carbon::parse($flight->arrival_time)->format('d/m/Y H:i') : null,
                    'price_per_person' => $flight->price_per_person ?? 0,
                    'type' => 'Keberangkatan'
                ]);
            }
            
            // Add return flight if exists
            if ($package->id_flight_return && $package->flightReturn) {
                $flight = $package->flightReturn;
                $availableFlights->push([
                    'id' => $flight->id,
                    'flight_number' => $flight->flight_number,
                    'airline' => $flight->airline_name,
                    'route' => $flight->departure_airport . ' - ' . $flight->arrival_airport,
                    'departure_time' => $package->return_datetime ? \Carbon\Carbon::parse($package->return_datetime)->format('d/m/Y H:i') : ($flight->departure_time ? \Carbon\Carbon::parse($flight->departure_time)->format('d/m/Y H:i') : null),
                    'arrival_time' => $flight->arrival_time ? \Carbon\Carbon::parse($flight->arrival_time)->format('d/m/Y H:i') : null,
                    'price_per_person' => $flight->price_per_person ?? 0,
                    'type' => 'Kepulangan'
                ]);
            }
        }

        // Get available hotels from the travel package (not all hotels from inventory)
        $availableHotels = collect();
        
        if ($package) {
            // Add Makkah hotel if exists
            if ($package->id_hotel_makkah && $package->hotelMakkah) {
                $hotel = $package->hotelMakkah;
                $roomType = $package->hotelRoomTypeMakkah;
                
                $availableHotels->push([
                    'id' => $hotel->id,
                    'hotel_name' => $hotel->hotel_name,
                    'location' => $hotel->city . ', ' . $hotel->country,
                    'star_rating' => $hotel->star_rating,
                    'price_per_night' => $roomType ? $roomType->price_per_night : 0,
                    'room_type' => $roomType ? $roomType->room_type_name : '-',
                    'check_in' => $package->makkah_check_in ? $package->makkah_check_in->format('d/m/Y') : null,
                    'check_out' => $package->makkah_check_out ? $package->makkah_check_out->format('d/m/Y') : null,
                    'city_type' => 'Mekkah'
                ]);
            }
            
            // Add Madinah hotel if exists
            if ($package->id_hotel_madinah && $package->hotelMadinah) {
                $hotel = $package->hotelMadinah;
                $roomType = $package->hotelRoomTypeMadinah;
                
                $availableHotels->push([
                    'id' => $hotel->id,
                    'hotel_name' => $hotel->hotel_name,
                    'location' => $hotel->city . ', ' . $hotel->country,
                    'star_rating' => $hotel->star_rating,
                    'price_per_night' => $roomType ? $roomType->price_per_night : 0,
                    'room_type' => $roomType ? $roomType->room_type_name : '-',
                    'check_in' => $package->madinah_check_in ? $package->madinah_check_in->format('d/m/Y') : null,
                    'check_out' => $package->madinah_check_out ? $package->madinah_check_out->format('d/m/Y') : null,
                    'city_type' => 'Madinah'
                ]);
            }
        }

        $data = [
            'id' => $keberangkatan->id,
            'keberangkatan_code' => $keberangkatan->keberangkatan_code,
            'keberangkatan_name' => $keberangkatan->keberangkatan_name,
            'id_travel_package' => $keberangkatan->id_travel_package,
            'package_name' => $keberangkatan->travelPackage ? $keberangkatan->travelPackage->package_name : null,
            'departure_date' => $keberangkatan->departure_date ? $keberangkatan->departure_date->format('Y-m-d') : null,
            'departure_date_formatted' => $keberangkatan->departure_date ? $keberangkatan->departure_date->format('d/m/Y') : null,
            'return_date' => $keberangkatan->return_date ? $keberangkatan->return_date->format('Y-m-d') : null,
            'return_date_formatted' => $keberangkatan->return_date ? $keberangkatan->return_date->format('d/m/Y') : null,
            'total_jamaah' => $keberangkatan->total_jamaah,
            // Count ALL confirmed bookings from the package (not just assigned to this keberangkatan)
            'confirmed_jamaah' => $allPackageBookings->count(),
            'available_capacity' => $keberangkatan->total_jamaah - $allPackageBookings->count(),
            'status' => $keberangkatan->status,
            'id_rab' => $keberangkatan->id_rab,
            'id_outlet' => $keberangkatan->id_outlet,
            'outlet_name' => $keberangkatan->outlet ? $keberangkatan->outlet->nama_outlet : null,

            // Jamaah list from bookings - showing ALL bookings from the package
            'jamaah_list' => $allPackageBookings->map(function($booking) use ($keberangkatan) {
                $member = $booking->jamaah; // This is the Member model
                return [
                    'booking_id' => $booking->id,
                    'jamaah_id' => $booking->id_member,
                    'jamaah_name' => $member ? ($member->nama ?? $member->ktp_nama ?? 'Tidak ada nama') : 'Tidak ada nama',
                    'no_ktp' => $member ? ($member->ktp_nik ?? '-') : '-',
                    'no_passport' => $member ? ($member->passport_nomor ?? '-') : '-',
                    'no_telp' => $member ? ($member->telepon ?? '-') : '-',
                    'booking_status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'is_assigned' => $booking->id_keberangkatan == $keberangkatan->id,
                    'assigned_to' => $booking->id_keberangkatan
                ];
            }),

            // Statistics
            'booking_stats' => [
                'total_package_bookings' => $allPackageBookings->count(),
                'assigned_to_this' => $assignedToThisKeberangkatan->count(),
                'unassigned' => $unassignedBookings->count()
            ],

            // Available flights from inventory (not from keberangkatan bookings)
            'available_flights' => $availableFlights,

            // Available hotels from inventory (not from keberangkatan bookings)
            'available_hotels' => $availableHotels
        ];

        // Return JSON for AJAX requests, view for direct access
        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('admin.travel.keberangkatan.show', compact('keberangkatan'));
    }


    /**
     * Update a keberangkatan
     */
    public function update(Request $request, $id)
    {
        $keberangkatan = Keberangkatan::find($id);
        
        if (!$keberangkatan) {
            return response()->json(['error' => 'Keberangkatan tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'keberangkatan_code' => 'required|string|max:255|unique:keberangkatan,keberangkatan_code,' . $id,
            'keberangkatan_name' => 'required|string|max:255',
            'id_travel_package' => 'required|exists:travel_packages,id',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'total_jamaah' => 'required|integer|min:1',
            'status' => 'nullable|in:planning,confirmed,departed,completed',
            'id_outlet' => 'required|exists:outlets,id_outlet'
        ], [
            'return_date.after_or_equal' => 'Tanggal kembali harus sama atau setelah tanggal keberangkatan',
            'total_jamaah.min' => 'Total jamaah harus berupa bilangan positif'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $keberangkatan->update($request->all());

            Log::info('Keberangkatan updated successfully', ['keberangkatan_id' => $keberangkatan->id]);

            return response()->json([
                'message' => 'Keberangkatan berhasil diupdate',
                'data' => $keberangkatan
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating keberangkatan: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mengupdate keberangkatan'
            ], 500);
        }
    }

    /**
     * Delete a keberangkatan
     */
    public function destroy($id)
    {
        $keberangkatan = Keberangkatan::find($id);
        
        if (!$keberangkatan) {
            return response()->json(['error' => 'Keberangkatan tidak ditemukan'], 404);
        }

        // Check if keberangkatan has confirmed jamaah
        $confirmedJamaah = $keberangkatan->jamaahBookings()
            ->whereNotIn('status', ['cancelled'])
            ->count();

        if ($confirmedJamaah > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus keberangkatan: terdapat jamaah yang sudah terdaftar',
                'code' => 'CONFIRMED_JAMAAH_EXIST'
            ], 400);
        }

        try {
            $keberangkatan->delete();

            Log::info('Keberangkatan deleted successfully', ['keberangkatan_id' => $id]);

            return response()->json([
                'message' => 'Keberangkatan berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting keberangkatan: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menghapus keberangkatan'
            ], 500);
        }
    }

    /**
     * Get available packages for dropdown
     */
    public function getAvailablePackages()
    {
        $user = Auth::user();
        $query = TravelPackage::where('status', 'active');

        // Filter by user's outlets
        if ($user && method_exists($user, 'outlets')) {
            $userOutlets = $user->outlets->pluck('id_outlet')->toArray();
            if (!empty($userOutlets)) {
                $query->whereIn('id_outlet', $userOutlets);
            }
        }

        $packages = $query->get([
            'id', 
            'package_code', 
            'package_name', 
            'package_type', 
            'duration_days', 
            'capacity', 
            'price',
            'departure_date',
            'return_date'
        ]);

        return response()->json($packages);
    }

    /**
     * Generate unique keberangkatan code
     */
    public function generateCode(Request $request)
    {
        $packageId = $request->input('package_id');
        
        if (!$packageId) {
            return response()->json(['error' => 'Package ID required'], 400);
        }

        $package = TravelPackage::find($packageId);
        if (!$package) {
            return response()->json(['error' => 'Package not found'], 404);
        }

        // Get count of existing keberangkatan for this package
        $count = Keberangkatan::where('id_travel_package', $packageId)->count() + 1;
        
        // Format: PKG-CODE-BATCH-NUMBER
        // Example: UMR001-B001, HJJ002-B002
        $code = strtoupper($package->package_code) . '-B' . str_pad($count, 3, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        while (Keberangkatan::where('keberangkatan_code', $code)->exists()) {
            $count++;
            $code = strtoupper($package->package_code) . '-B' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        return response()->json(['code' => $code]);
    }

    /**
     * Get capacity tracking for a keberangkatan
     */
    public function getCapacityTracking($id)
    {
        $keberangkatan = Keberangkatan::find($id);
        
        if (!$keberangkatan) {
            return response()->json(['error' => 'Keberangkatan tidak ditemukan'], 404);
        }

        return response()->json([
            'total_capacity' => $keberangkatan->total_jamaah,
            'confirmed_jamaah' => $keberangkatan->getConfirmedJamaahCount(),
            'available_capacity' => $keberangkatan->getAvailableCapacity(),
            'is_full' => $keberangkatan->isFull(),
            'utilization_percentage' => $keberangkatan->total_jamaah > 0 
                ? round(($keberangkatan->getConfirmedJamaahCount() / $keberangkatan->total_jamaah) * 100, 2)
                : 0
        ]);
    }

    /**
     * Create RAB for keberangkatan
     */
    public function createRab($id)
    {
        try {
            $keberangkatan = Keberangkatan::with('travelPackage.hppCalculation')->find($id);
            
            if (!$keberangkatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keberangkatan tidak ditemukan'
                ], 404);
            }

            // Check if RAB already exists
            if ($keberangkatan->id_rab) {
                return response()->json([
                    'success' => false,
                    'message' => 'RAB sudah dibuat untuk keberangkatan ini'
                ], 400);
            }

            // Check if package has HPP calculation
            if (!$keberangkatan->travelPackage || !$keberangkatan->travelPackage->hppCalculation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket belum memiliki kalkulasi HPP'
                ], 400);
            }

            // Create RAB using service
            $rab = $this->rabService->createRabForKeberangkatan($keberangkatan);

            Log::info('RAB created for keberangkatan via controller', [
                'keberangkatan_id' => $keberangkatan->id,
                'rab_id' => $rab->id_rab
            ]);

            return response()->json([
                'success' => true,
                'message' => 'RAB berhasil dibuat',
                'data' => [
                    'rab_id' => $rab->id_rab,
                    'rab_name' => $rab->nama_template,
                    'total_budget' => $rab->total_biaya
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error creating RAB for keberangkatan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat RAB: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get RAB data for keberangkatan
     */
    public function getRabData($id)
    {
        try {
            $keberangkatan = Keberangkatan::find($id);
            
            if (!$keberangkatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keberangkatan tidak ditemukan'
                ], 404);
            }

            $rab = $this->rabService->getRabForKeberangkatan($keberangkatan);

            if (!$rab) {
                return response()->json([
                    'success' => false,
                    'message' => 'RAB belum dibuat untuk keberangkatan ini',
                    'has_rab' => false
                ], 404);
            }

            return response()->json([
                'success' => true,
                'has_rab' => true,
                'data' => [
                    'id' => $rab->id_rab,
                    'name' => $rab->nama_template,
                    'description' => $rab->deskripsi,
                    'total_budget' => $rab->total_biaya,
                    'total_approved' => $rab->total_nilai_disetujui,
                    'total_actual' => $rab->total_realisasi,
                    'status' => $rab->status,
                    'details' => $rab->details->map(function($detail) {
                        return [
                            'id' => $detail->id_rab_detail ?? $detail->id,
                            'item' => $detail->nama_komponen,
                            'description' => $detail->deskripsi,
                            'qty' => $detail->jumlah,
                            'unit' => $detail->satuan,
                            'unit_price' => $detail->harga_satuan,
                            'budget' => $detail->budget,
                            'approved' => $detail->nilai_disetujui,
                            'actual' => $detail->realisasi_pemakaian,
                            'is_approved' => $detail->disetujui
                        ];
                    })
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching RAB data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data RAB'
            ], 500);
        }
    }

    /**
     * Get budget variance for keberangkatan
     */
    public function getBudgetVariance($id)
    {
        try {
            $keberangkatan = Keberangkatan::find($id);
            
            if (!$keberangkatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keberangkatan tidak ditemukan'
                ], 404);
            }

            $variance = $this->rabService->calculateBudgetVariance($keberangkatan);

            return response()->json([
                'success' => true,
                'data' => $variance
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error calculating budget variance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung varians budget'
            ], 500);
        }
    }

    /**
     * Get available jamaah for keberangkatan
     */
    public function getAvailableJamaah(Request $request, $id)
    {
        try {
            $keberangkatan = Keberangkatan::with('travelPackage')->find($id);
            
            if (!$keberangkatan) {
                return response()->json(['error' => 'Keberangkatan tidak ditemukan'], 404);
            }

            // Get jamaah who have bookings for this package but not assigned to any keberangkatan yet
            $query = Member::whereHas('jamaahBookings', function($q) use ($keberangkatan) {
                $q->where('id_travel_package', $keberangkatan->id_travel_package)
                  ->whereNull('id_keberangkatan')
                  ->whereNotIn('status', ['cancelled']);
            });

            // Search filter
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->search . '%')
                      ->orWhere('ktp_nik', 'like', '%' . $request->search . '%')
                      ->orWhere('ktp_nama', 'like', '%' . $request->search . '%')
                      ->orWhere('passport_nomor', 'like', '%' . $request->search . '%');
                });
            }

            $jamaah = $query->with(['jamaahBookings' => function($q) use ($keberangkatan) {
                $q->where('id_travel_package', $keberangkatan->id_travel_package)
                  ->whereNull('id_keberangkatan');
            }])->get();

            return response()->json($jamaah->map(function($member) {
                $booking = $member->jamaahBookings->first();
                
                // Get photo URL
                $photoUrl = null;
                if ($member->pas_foto) {
                    $photoUrl = asset('storage/' . $member->pas_foto);
                }
                
                return [
                    'id' => $member->id_member,
                    'booking_id' => $booking ? $booking->id : null,
                    'nama' => $member->nama ?? $member->ktp_nama ?? 'Tidak ada nama',
                    'no_ktp' => $member->ktp_nik ?? '-',
                    'no_passport' => $member->passport_nomor ?? '-',
                    'pas_foto' => $photoUrl,
                    'payment_status' => $booking ? $booking->payment_status : null,
                    'booking_status' => $booking ? $booking->status : null,
                    'total_price' => $booking ? $booking->total_price : 0,
                    'paid_amount' => $booking ? $booking->paid_amount : 0,
                    'remaining_amount' => $booking ? $booking->remaining_amount : 0
                ];
            }));

        } catch (\Exception $e) {
            Log::error('Error fetching available jamaah: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Gagal mengambil data jamaah: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Add jamaah to keberangkatan
     */
    public function addJamaah(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:jamaah_bookings,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $keberangkatan = Keberangkatan::find($id);
            
            if (!$keberangkatan) {
                return response()->json(['error' => 'Keberangkatan tidak ditemukan'], 404);
            }

            // Check capacity
            $requestedCount = count($request->booking_ids);
            $availableCapacity = $keberangkatan->getAvailableCapacity();

            if ($requestedCount > $availableCapacity) {
                return response()->json([
                    'success' => false,
                    'message' => "Kapasitas tidak cukup. Tersedia: {$availableCapacity}, Diminta: {$requestedCount}"
                ], 400);
            }

            DB::beginTransaction();

            // Assign bookings to keberangkatan
            \App\Models\JamaahBooking::whereIn('id', $request->booking_ids)
                ->update(['id_keberangkatan' => $keberangkatan->id]);

            DB::commit();

            Log::info('Jamaah added to keberangkatan', [
                'keberangkatan_id' => $keberangkatan->id,
                'booking_count' => $requestedCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$requestedCount} jamaah berhasil ditambahkan",
                'data' => [
                    'confirmed_jamaah' => $keberangkatan->getConfirmedJamaahCount(),
                    'available_capacity' => $keberangkatan->getAvailableCapacity()
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding jamaah to keberangkatan: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menambahkan jamaah'], 500);
        }
    }

    /**
     * Remove jamaah from keberangkatan
     */
    public function removeJamaah(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:jamaah_bookings,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $keberangkatan = Keberangkatan::find($id);
            
            if (!$keberangkatan) {
                return response()->json(['error' => 'Keberangkatan tidak ditemukan'], 404);
            }

            $booking = \App\Models\JamaahBooking::find($request->booking_id);

            if ($booking->id_keberangkatan != $keberangkatan->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jamaah tidak terdaftar di keberangkatan ini'
                ], 400);
            }

            DB::beginTransaction();

            // Remove keberangkatan assignment
            $booking->id_keberangkatan = null;
            $booking->save();

            DB::commit();

            Log::info('Jamaah removed from keberangkatan', [
                'keberangkatan_id' => $keberangkatan->id,
                'booking_id' => $booking->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jamaah berhasil dihapus dari keberangkatan',
                'data' => [
                    'confirmed_jamaah' => $keberangkatan->getConfirmedJamaahCount(),
                    'available_capacity' => $keberangkatan->getAvailableCapacity()
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing jamaah from keberangkatan: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghapus jamaah'], 500);
        }
    }

    /**
     * Get total cost calculation for keberangkatan
     */
    public function getTotalCost($id)
    {
        try {
            $keberangkatan = Keberangkatan::with(['travelPackage.hppCalculation'])->find($id);
            
            if (!$keberangkatan) {
                return response()->json(['error' => 'Keberangkatan tidak ditemukan'], 404);
            }

            $package = $keberangkatan->travelPackage;
            $hpp = $package ? $package->hppCalculation : null;
            
            // Count ALL jamaah from the package (not just assigned to this keberangkatan)
            $jamaahCount = JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
                ->whereNotIn('status', ['cancelled'])
                ->count();

            if (!$hpp) {
                return response()->json([
                    'success' => false,
                    'message' => 'HPP belum dihitung untuk paket ini'
                ], 404);
            }

            // Calculate total costs based on unit prices × jamaah count
            $flightCostTotal = $hpp->flight_cost * $jamaahCount;
            $hotelCostTotal = $hpp->hotel_cost * $jamaahCount;
            $transportationCostTotal = $hpp->transportation_cost * $jamaahCount;
            $mealCostTotal = $hpp->meal_cost * $jamaahCount;
            $visaCostTotal = $hpp->visa_cost * $jamaahCount;
            $guideCostTotal = $hpp->guide_cost * $jamaahCount;
            $insuranceCostTotal = $hpp->insurance_cost * $jamaahCount;
            $operationalOverheadTotal = $hpp->operational_overhead * $jamaahCount;
            $contingencyTotal = $hpp->contingency * $jamaahCount;

            $totalCost = $flightCostTotal + $hotelCostTotal + $transportationCostTotal + 
                        $mealCostTotal + $visaCostTotal + $guideCostTotal + 
                        $insuranceCostTotal + $operationalOverheadTotal + $contingencyTotal;

            $totalRevenue = $package->price * $jamaahCount;
            $totalProfit = $totalRevenue - $totalCost;
            $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'jamaah_count' => $jamaahCount,
                    'unit_prices' => [
                        'flight_cost' => $hpp->flight_cost,
                        'hotel_cost' => $hpp->hotel_cost,
                        'transportation_cost' => $hpp->transportation_cost,
                        'meal_cost' => $hpp->meal_cost,
                        'visa_cost' => $hpp->visa_cost,
                        'guide_cost' => $hpp->guide_cost,
                        'insurance_cost' => $hpp->insurance_cost,
                        'operational_overhead' => $hpp->operational_overhead,
                        'contingency' => $hpp->contingency
                    ],
                    'total_costs' => [
                        'flight_cost' => $flightCostTotal,
                        'hotel_cost' => $hotelCostTotal,
                        'transportation_cost' => $transportationCostTotal,
                        'meal_cost' => $mealCostTotal,
                        'visa_cost' => $visaCostTotal,
                        'guide_cost' => $guideCostTotal,
                        'insurance_cost' => $insuranceCostTotal,
                        'operational_overhead' => $operationalOverheadTotal,
                        'contingency' => $contingencyTotal
                    ],
                    'total_cost' => $totalCost,
                    'total_revenue' => $totalRevenue,
                    'total_profit' => $totalProfit,
                    'profit_margin' => $profitMargin,
                    'cost_per_jamaah' => $jamaahCount > 0 ? $totalCost / $jamaahCount : 0,
                    'revenue_per_jamaah' => $package->price
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error calculating total cost: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghitung total biaya'], 500);
        }
    }

    /**
     * Auto-assign unassigned bookings to keberangkatan
     */
    public function autoAssignJamaah($id)
    {
        try {
            $keberangkatan = Keberangkatan::findOrFail($id);
            
            // Get unassigned bookings for the same package
            $unassignedBookings = JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
                ->whereNull('id_keberangkatan')
                ->whereNotIn('status', ['cancelled'])
                ->get();
            
            if ($unassignedBookings->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada booking yang belum ditugaskan untuk paket ini'
                ], 404);
            }
            
            $assigned = 0;
            $available = $keberangkatan->getAvailableCapacity();
            
            foreach ($unassignedBookings as $booking) {
                if ($available <= 0) {
                    break; // Keberangkatan sudah penuh
                }
                
                $booking->update(['id_keberangkatan' => $keberangkatan->id]);
                $assigned++;
                $available--;
            }
            
            return response()->json([
                'success' => true,
                'message' => "{$assigned} jamaah berhasil ditugaskan ke keberangkatan ini",
                'data' => [
                    'assigned_count' => $assigned,
                    'remaining_unassigned' => $unassignedBookings->count() - $assigned
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error auto-assigning jamaah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menugaskan jamaah: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get unassigned bookings for a package
     */
    public function getUnassignedBookings($id)
    {
        try {
            $keberangkatan = Keberangkatan::findOrFail($id);
            
            $unassignedBookings = JamaahBooking::with(['jamaah', 'travelPackage'])
                ->where('id_travel_package', $keberangkatan->id_travel_package)
                ->whereNull('id_keberangkatan')
                ->whereNotIn('status', ['cancelled'])
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $unassignedBookings->map(function($booking) {
                    return [
                        'id' => $booking->id,
                        'booking_code' => $booking->booking_code,
                        'jamaah_name' => $booking->jamaah ? $booking->jamaah->nama_member : '-',
                        'booking_date' => $booking->booking_date->format('d M Y'),
                        'status' => $booking->status,
                        'payment_status' => $booking->payment_status
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching unassigned bookings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data booking'
            ], 500);
        }
    }

    // ===================== VISA MANAGEMENT =====================

    public function getVisas($id)
    {
        $visas = KeberangkatanVisa::where('id_keberangkatan', $id)->get()->map(function($v) {
            return [
                'id'               => $v->id,
                'visa_type'        => $v->visa_type,
                'seller_name'      => $v->seller_name ?? '-',
                'seller_phone'     => $v->seller_phone ?? '-',
                'price_per_person' => $v->price_per_person,
                'price_formatted'  => 'Rp ' . number_format($v->price_per_person, 0, ',', '.'),
                'status'           => $v->status,
                'status_label'     => $v->status_label,
                'submission_date'  => $v->submission_date?->format('Y-m-d'),
                'ready_date'       => $v->ready_date?->format('Y-m-d'),
                'notes'            => $v->notes,
            ];
        });

        return response()->json(['data' => $visas]);
    }

    public function storeVisa(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'visa_type'        => 'required|string|max:100',
            'seller_name'      => 'nullable|string|max:255',
            'seller_phone'     => 'nullable|string|max:30',
            'price_per_person' => 'required|numeric|min:0',
            'status'           => 'nullable|in:pending,processing,ready,distributed',
            'submission_date'  => 'nullable|date',
            'ready_date'       => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $visa = KeberangkatanVisa::create(array_merge(
            $request->all(),
            ['id_keberangkatan' => $id, 'status' => $request->status ?? 'pending']
        ));

        return response()->json(['message' => 'Visa berhasil disimpan', 'data' => $visa], 200);
    }

    public function updateVisa(Request $request, $id, $visaId)
    {
        $visa = KeberangkatanVisa::where('id_keberangkatan', $id)->findOrFail($visaId);

        $validator = Validator::make($request->all(), [
            'visa_type'        => 'required|string|max:100',
            'seller_name'      => 'nullable|string|max:255',
            'seller_phone'     => 'nullable|string|max:30',
            'price_per_person' => 'required|numeric|min:0',
            'status'           => 'nullable|in:pending,processing,ready,distributed',
            'submission_date'  => 'nullable|date',
            'ready_date'       => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $visa->update($request->all());

        return response()->json(['message' => 'Visa berhasil diupdate', 'data' => $visa], 200);
    }

    public function destroyVisa($id, $visaId)
    {
        KeberangkatanVisa::where('id_keberangkatan', $id)->findOrFail($visaId)->delete();
        return response()->json(['message' => 'Visa berhasil dihapus'], 200);
    }

    public function sendReminders($id)
    {
        $keberangkatan = Keberangkatan::findOrFail($id);

        try {
            \Artisan::call('travel:send-reminders', [
                '--keberangkatan' => $id,
            ]);
        } catch (\Exception $e) {
            // Command mungkin tidak support --keberangkatan flag, jalankan manual
        }

        // Trigger langsung tanpa artisan untuk keberangkatan spesifik ini
        $command = new \App\Console\Commands\SendKeberangkatanReminders();
        $command->setLaravel(app());

        $now = \Carbon\Carbon::now();
        $items = \App\Console\Commands\SendKeberangkatanReminders::REMINDER_ITEMS;

        $sent = 0;
        foreach ($items as $type => $role) {
            $recentlySent = \App\Models\KeberangkatanReminder::where('id_keberangkatan', $keberangkatan->id)
                ->where('reminder_type', $type)
                ->where('status', 'sent')
                ->where('sent_at', '>=', $now->copy()->subDays(7))
                ->exists();

            if ($recentlySent) continue;

            $daysLeft = $now->diffInDays($keberangkatan->departure_date, false);
            $label    = \App\Models\KeberangkatanReminder::typeLabel($type);
            $message  = "REMINDER: {$label} untuk keberangkatan {$keberangkatan->keberangkatan_code} ({$keberangkatan->departure_date->format('d M Y')}) belum dikonfirmasi. Sisa {$daysLeft} hari lagi.";

            $reminder = \App\Models\KeberangkatanReminder::create([
                'id_keberangkatan' => $keberangkatan->id,
                'reminder_type'    => $type,
                'target_role'      => $role,
                'status'           => 'pending',
                'scheduled_at'     => $now,
                'message'          => $message,
            ]);

            $users = \App\Models\User::role($role)->get();
            if ($users->isEmpty()) {
                $users = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'super-admin']))->get();
            }

            foreach ($users as $user) {
                try {
                    $user->notify(new \App\Notifications\KeberangkatanReminderNotification($reminder, $keberangkatan));
                } catch (\Exception $e) {
                    \Log::warning("Reminder notify failed for user {$user->id}: " . $e->getMessage());
                }
            }

            $reminder->update(['status' => 'sent', 'sent_at' => $now]);
            $sent++;
        }

        return response()->json([
            'message' => $sent > 0
                ? "{$sent} reminder berhasil dikirim"
                : 'Semua reminder sudah dikirim minggu ini',
        ]);
    }

    /**
     * Get RAB modal data: HPP dasar + HPP aktual hotel & addons per jamaah
     * Ini adalah modal RAB khusus keberangkatan, bukan RabTemplate
     */
    public function getKeberangkatanRabModal($id)
    {
        $keberangkatan = Keberangkatan::with([
            'travelPackage.hppCalculation',
            'jamaahBookings.jamaah',
            'jamaahBookings.addons',
            'jamaahBookings.hotelBookings.hotel',
        ])->findOrFail($id);

        $package = $keberangkatan->travelPackage;
        $hpp     = $package?->hppCalculation;

        if (!$hpp) {
            return response()->json(['error' => 'HPP belum dihitung untuk paket ini'], 404);
        }

        $payStatus = $hpp->component_payment_status ?? [];
        $hutangAmt = $hpp->component_hutang_amount ?? [];
        $realisasiMap = $hpp->component_realisasi ?? [];
        $jamaahCount = $keberangkatan->jamaahBookings->whereNotIn('status', ['cancelled'])->count() ?: 1;

        // ===== BARIS HPP DASAR (per komponen × jamaah) =====
        $hppDasarItems = [];
        $dasarDefs = [
            ['key' => 'flight_cost',         'label' => 'Tiket Pesawat'],
            ['key' => 'transportation_cost', 'label' => 'Transportasi Saudi'],
            ['key' => 'meal_cost',           'label' => 'Makan'],
            ['key' => 'visa_cost',           'label' => 'Visa'],
            ['key' => 'guide_cost',          'label' => 'Pembimbing/Guide'],
            ['key' => 'insurance_cost',      'label' => 'Asuransi'],
            ['key' => 'operational_overhead','label' => 'Operasional'],
            ['key' => 'contingency',         'label' => 'Kontingensi'],
        ];

        foreach ($dasarDefs as $def) {
            $unitPrice = (float) ($hpp->{$def['key']} ?? 0);
            if ($unitPrice <= 0) continue;

            $total   = $unitPrice * $jamaahCount;
            $status  = $payStatus[$def['key']] ?? 'lunas';
            $hutang  = (float) ($hutangAmt[$def['key']] ?? ($status === 'hutang' ? $total : 0));
            // Realisasi: ambil dari component_realisasi jika ada, fallback ke logika status
            $realisasi = isset($realisasiMap[$def['key']])
                ? (float) $realisasiMap[$def['key']]
                : (($status === 'lunas') ? $total : 0);

            $hppDasarItems[] = [
                'id'          => 'hpp_' . $def['key'],
                'hpp_key'     => $def['key'],
                'label'       => $def['label'],
                'type'        => 'hpp_dasar',
                'unit_price'  => $unitPrice,
                'qty'         => $jamaahCount,
                'total'       => $total,
                'payment_status' => $status,
                'realisasi'   => $realisasi,
                'hutang_amount' => $hutang,
            ];
        }

        // ===== BARIS HPP AKTUAL HOTEL per jamaah =====
        $hotelItems = [];
        foreach ($keberangkatan->jamaahBookings->whereNotIn('status', ['cancelled']) as $booking) {
            foreach ($booking->hotelBookings as $hb) {
                if (!$hb->is_charged) continue;
                $total = (float) $hb->price_per_night * $hb->nights;
                if ($total <= 0) continue;

                $hotelItems[] = [
                    'id'          => 'hotel_' . $hb->id,
                    'hpp_key'     => null,
                    'label'       => 'Hotel ' . ($hb->hotel?->hotel_name ?? '-') . ' (' . ($hb->city_type ?? '') . ') - ' . ($booking->jamaah?->nama ?? 'Jamaah'),
                    'type'        => 'hotel_aktual',
                    'unit_price'  => (float) $hb->price_per_night,
                    'qty'         => $hb->nights,
                    'total'       => $total,
                    'payment_status' => 'lunas',
                    'realisasi'   => $total,
                    'hutang_amount' => 0,
                ];
            }
        }

        // ===== BARIS HPP AKTUAL ADDONS per jamaah =====
        $addonItems = [];
        foreach ($keberangkatan->jamaahBookings->whereNotIn('status', ['cancelled']) as $booking) {
            foreach ($booking->addons->where('masuk_hpp', true) as $addon) {
                $total = (float) $addon->harga * $addon->qty;
                if ($total <= 0) continue;

                $addonItems[] = [
                    'id'          => 'addon_' . $addon->id,
                    'hpp_key'     => null,
                    'label'       => ($addon->nama ?? 'Add-on') . ' - ' . ($booking->jamaah?->nama ?? 'Jamaah'),
                    'type'        => 'addon_aktual',
                    'unit_price'  => (float) $addon->harga,
                    'qty'         => $addon->qty,
                    'total'       => $total,
                    'payment_status' => 'lunas',
                    'realisasi'   => $total,
                    'hutang_amount' => 0,
                ];
            }
        }

        $allItems = array_merge($hppDasarItems, $hotelItems, $addonItems);
        $totalBudget    = array_sum(array_column($allItems, 'total'));
        $totalRealisasi = array_sum(array_column($allItems, 'realisasi'));
        $totalHutang    = array_sum(array_column($allItems, 'hutang_amount'));

        return response()->json([
            'keberangkatan_code' => $keberangkatan->keberangkatan_code,
            'keberangkatan_name' => $keberangkatan->keberangkatan_name,
            'jamaah_count'       => $jamaahCount,
            'total_budget'       => $totalBudget,
            'total_realisasi'    => $totalRealisasi,
            'total_hutang'       => $totalHutang,
            'items'              => $allItems,
            'laporan_disesuaikan'   => (bool) ($hpp->laporan_disesuaikan ?? false),
            'laporan_adjustment'    => (float) ($hpp->laporan_adjustment ?? 0),
            'laporan_disesuaikan_at'=> $hpp->laporan_disesuaikan_at?->format('d M Y H:i') ?? null,
        ]);
    }

    /**
     * Update status pembayaran item RAB modal (sync ke HPP)
     * Hanya untuk item HPP dasar (hpp_key tidak null)
     */
    public function updateKeberangkatanRabItem(Request $request, $id)
    {
        $keberangkatan = Keberangkatan::with('travelPackage.hppCalculation')->findOrFail($id);
        $hpp = $keberangkatan->travelPackage?->hppCalculation;

        if (!$hpp) {
            return response()->json(['error' => 'HPP tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'hpp_key'        => 'required|string',
            'payment_status' => 'required|in:lunas,hutang',
            'hutang_amount'  => 'nullable|numeric|min:0',
            'realisasi'      => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $key       = $request->hpp_key;
        $status    = $request->payment_status;
        $hutang    = ($status === 'hutang') ? (float) ($request->hutang_amount ?? 0) : 0;
        $realisasi = (float) ($request->realisasi ?? 0);

        $payStatus    = $hpp->component_payment_status ?? [];
        $hutangAmt    = $hpp->component_hutang_amount ?? [];
        $realisasiMap = $hpp->component_realisasi ?? [];

        $payStatus[$key] = $status;
        $realisasiMap[$key] = $realisasi;

        if ($status === 'hutang') {
            $hutangAmt[$key] = $hutang;
        } else {
            unset($hutangAmt[$key]);
        }

        $hpp->update([
            'component_payment_status' => $payStatus,
            'component_hutang_amount'  => $hutangAmt,
            'component_realisasi'      => $realisasiMap,
        ]);

        // Sync ke RAB template jika ada
        if ($keberangkatan->id_rab) {
            try { $this->rabService->syncHppStatusToRab($keberangkatan); } catch (\Exception $e) {}
        }

        return response()->json(['message' => 'Realisasi berhasil disimpan']);
    }

    /**
     * Reset penyesuaian laporan keuangan
     */
    public function resetPenyesuaianLaporan($id)
    {
        $keberangkatan = Keberangkatan::with('travelPackage.hppCalculation')->findOrFail($id);
        $hpp = $keberangkatan->travelPackage?->hppCalculation;
        if (!$hpp) {
            return response()->json(['error' => 'HPP tidak ditemukan'], 404);
        }

        $hpp->update([
            'laporan_adjustment'    => 0,
            'laporan_disesuaikan'   => false,
            'laporan_disesuaikan_at'=> null,
        ]);

        return response()->json(['message' => 'Penyesuaian laporan berhasil direset']);
    }

    /**
     * Sesuaikan laporan keuangan berdasarkan surplus/defisit RAB
     * Surplus: costs dikurangi nilai surplus (efisiensi)
     * Defisit: costs ditambah nilai defisit (kelebihan pengeluaran)
     */
    public function sesuaikanLaporan(Request $request, $id)
    {
        $keberangkatan = Keberangkatan::with([
            'travelPackage.hppCalculation',
            'jamaahBookings.addons',
            'jamaahBookings.hotelBookings',
        ])->findOrFail($id);

        $hpp = $keberangkatan->travelPackage?->hppCalculation;
        if (!$hpp) {
            return response()->json(['error' => 'HPP tidak ditemukan'], 404);
        }

        // Hitung total budget HPP dasar
        $bookings = $keberangkatan->jamaahBookings->whereNotIn('status', ['cancelled']);
        $jamaahCount = $bookings->count() ?: 1;
        $realisasiMap = $hpp->component_realisasi ?? [];
        $payStatus    = $hpp->component_payment_status ?? [];
        $dasarKeys    = ['flight_cost','transportation_cost','meal_cost','visa_cost','guide_cost','insurance_cost','operational_overhead','contingency'];

        $totalBudget  = 0;
        $totalRealisasi = 0;
        foreach ($dasarKeys as $k) {
            $up = (float) ($hpp->{$k} ?? 0);
            if ($up <= 0) continue;
            $tot = $up * $jamaahCount;
            $totalBudget += $tot;
            $st  = $payStatus[$k] ?? 'lunas';
            $rel = isset($realisasiMap[$k]) ? (float)$realisasiMap[$k] : (($st === 'lunas') ? $tot : 0);
            $totalRealisasi += $rel;
        }

        // Tambahkan hotel & addon aktual
        $hotelRealisasi = $bookings->flatMap(fn($b) => $b->hotelBookings ?? collect())
            ->where('is_charged', true)
            ->sum(fn($h) => (float)$h->price_per_night * $h->nights);
        $addonRealisasi = $bookings->flatMap(fn($b) => $b->addons ?? collect())
            ->where('masuk_hpp', true)
            ->sum(fn($a) => (float)$a->harga * $a->qty);

        $totalBudgetFull    = $totalBudget + $hotelRealisasi + $addonRealisasi;
        $totalRealisasiFull = $totalRealisasi + $hotelRealisasi + $addonRealisasi;

        // Surplus = budget - realisasi (positif = efisiensi, negatif = defisit)
        $surplusDefisit = $totalBudgetFull - $totalRealisasiFull;

        // Simpan adjustment ke HPP
        $hpp->update([
            'laporan_adjustment'    => $surplusDefisit,
            'laporan_disesuaikan'   => true,
            'laporan_disesuaikan_at'=> now(),
        ]);

        $type = $surplusDefisit >= 0 ? 'surplus' : 'defisit';
        return response()->json([
            'message'        => 'Laporan keuangan berhasil disesuaikan',
            'type'           => $type,
            'adjustment'     => $surplusDefisit,
            'total_budget'   => $totalBudgetFull,
            'total_realisasi'=> $totalRealisasiFull,
        ]);
    }

    /**
     * Update realisasi RAB item (saat hutang dibayar)
     * Juga sync status ke HPP calculation
     */
    public function updateRabRealisasi(Request $request, $id)
    {
        $keberangkatan = Keberangkatan::with(['travelPackage.hppCalculation'])->findOrFail($id);

        if (!$keberangkatan->id_rab) {
            return response()->json(['error' => 'RAB belum dibuat untuk keberangkatan ini'], 404);
        }

        $validator = Validator::make($request->all(), [
            'detail_id'      => 'required|integer',
            'payment_status' => 'required|in:lunas,hutang',
            'hutang_amount'  => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $detail = \App\Models\RabDetail::where('id', $request->detail_id)
            ->where('id_rab', $keberangkatan->id_rab)
            ->firstOrFail();

        $status    = $request->payment_status;
        $budget    = (float) $detail->budget;
        $realisasi = ($status === 'lunas') ? $budget : 0;
        $hutang    = ($status === 'hutang') ? (float) ($request->hutang_amount ?? 0) : 0;

        $detail->update([
            'payment_status'      => $status,
            'hutang_amount'       => $hutang,
            'realisasi_pemakaian' => $realisasi,
        ]);

        // Sync balik ke HPP component_payment_status
        $hpp = $keberangkatan->travelPackage?->hppCalculation;
        if ($hpp) {
            $itemKeyMap = [
                'Tiket Pesawat' => 'flight_cost',
                'Hotel'         => 'hotel_cost',
                'Transportasi'  => 'transportation_cost',
                'Makan'         => 'meal_cost',
                'Visa'          => 'visa_cost',
                'Guide'         => 'guide_cost',
                'Asuransi'      => 'insurance_cost',
                'Operasional'   => 'operational_overhead',
                'Kontingensi'   => 'contingency',
            ];
            $key = $itemKeyMap[$detail->item] ?? null;
            if ($key) {
                $payStatus = $hpp->component_payment_status ?? [];
                $hutangAmt = $hpp->component_hutang_amount ?? [];
                $payStatus[$key] = $status;
                if ($status === 'hutang') $hutangAmt[$key] = $hutang;
                else unset($hutangAmt[$key]);
                $hpp->update([
                    'component_payment_status' => $payStatus,
                    'component_hutang_amount'  => $hutangAmt,
                ]);
            }
        }

        return response()->json([
            'message'  => 'Realisasi berhasil diupdate',
            'detail'   => $detail->fresh(),
        ]);
    }

    /**
     * Get financial summary for keberangkatan (untuk laporan keuangan)
     */
    public function getFinancialSummary($id)
    {
        $keberangkatan = Keberangkatan::with([
            'travelPackage.hppCalculation',
            'jamaahBookings.payments',
            'jamaahBookings.addons',
        ])->findOrFail($id);

        $bookings = $keberangkatan->jamaahBookings;
        $revenue  = $bookings->sum('total_price');

        $hpp = $keberangkatan->travelPackage?->hppCalculation;
        $hppPerPerson = $hpp ? $hpp->getHppPerPerson() : 0;
        $jamaahCount  = $bookings->count();
        $hppTotal     = $hppPerPerson * $jamaahCount;

        // HPP aktual dari hotel booking & addons
        $addonHpp = $bookings->flatMap(fn($b) => $b->addons ?? collect())
            ->where('masuk_hpp', true)
            ->sum(fn($a) => ($a->harga ?? 0) * ($a->qty ?? 1));

        $totalCosts = $hppTotal + $addonHpp;
        $profit     = $revenue - $totalCosts;

        // RAB realisasi aktual dari component_realisasi
        $rabRealisasi = 0;
        $rabHutang    = 0;
        if ($hpp) {
            $realisasiMap = $hpp->component_realisasi ?? [];
            $payStatus    = $hpp->component_payment_status ?? [];
            $hutangAmt    = $hpp->component_hutang_amount ?? [];
            $dasarKeys    = ['flight_cost','transportation_cost','meal_cost','visa_cost','guide_cost','insurance_cost','operational_overhead','contingency'];
            foreach ($dasarKeys as $k) {
                $unitPrice = (float) ($hpp->{$k} ?? 0);
                if ($unitPrice <= 0) continue;
                $total = $unitPrice * $jamaahCount;
                $status = $payStatus[$k] ?? 'lunas';
                // Realisasi aktual: dari component_realisasi jika ada, fallback ke logika status
                $realisasi = isset($realisasiMap[$k]) ? (float)$realisasiMap[$k] : (($status === 'lunas') ? $total : 0);
                $rabRealisasi += $realisasi;
                $rabHutang    += (float) ($hutangAmt[$k] ?? 0);
            }
        }
        // Tambahkan hotel & addon aktual ke realisasi
        $hotelRealisasi = $bookings->flatMap(fn($b) => $b->hotelBookings ?? collect())
            ->where('is_charged', true)
            ->sum(fn($h) => (float)$h->price_per_night * $h->nights);
        $addonRealisasi = $addonHpp;
        $rabRealisasi += $hotelRealisasi + $addonRealisasi;

        $totalCosts = $hppTotal + $addonHpp;
        $profit     = $revenue - $totalCosts;

        return response()->json([
            'keberangkatan_code' => $keberangkatan->keberangkatan_code,
            'keberangkatan_name' => $keberangkatan->keberangkatan_name,
            'jamaah_count'       => $jamaahCount,
            'revenue'            => $revenue,
            'hpp_per_person'     => $hppPerPerson,
            'hpp_total'          => $hppTotal,
            'addon_hpp'          => $addonHpp,
            'total_costs'        => $totalCosts,
            'profit'             => $profit,
            'profit_margin'      => $revenue > 0 ? ($profit / $revenue) * 100 : 0,
            'rab_realisasi'      => $rabRealisasi,
            'rab_hutang'         => $rabHutang,
            'total_budget'       => $hppTotal + $hotelRealisasi + $addonRealisasi,
            'surplus_defisit'    => ($hppTotal + $hotelRealisasi + $addonRealisasi) - $rabRealisasi,
        ]);
    }
}
