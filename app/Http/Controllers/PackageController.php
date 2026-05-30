<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TravelPackage;
use App\Models\HppCalculation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\HasOutletFilter;

class PackageController extends Controller
{
    use HasOutletFilter;
    public function __construct()
    {
        $this->middleware('permission:travel.package.view')->only(['index', 'getData', 'show']);
        $this->middleware('permission:travel.package.create')->only(['store']);
        $this->middleware('permission:travel.package.edit')->only(['update', 'updateHpp']);
        $this->middleware('permission:travel.package.delete')->only(['destroy']);
    }

    /**
     * Display package management page
     */
    public function index()
        {
            Log::info('Loading Package Index Page');

            // Get user's accessible outlets for filter
            $user = Auth::user();
            $outlets = collect();

            if ($user && method_exists($user, 'outlets')) {
                $outlets = $user->outlets;
            }

            // If user has no outlet access, get all outlets (for super admin)
            if ($outlets->isEmpty()) {
                $outlets = \App\Models\Outlet::all();
            }

            return view('admin.travel.package.index', compact('outlets'));
        }

    /**
     * Show the form for creating a new package
     */
    public function create()
    {
        Log::info('Loading Package Create Page');
        
        // Get user's accessible outlets
        $user = Auth::user();
        $outlets = collect();
        
        if ($user && method_exists($user, 'outlets')) {
            $outlets = $user->outlets;
        }
        
        // If user has no outlet access, get all outlets (for super admin)
        if ($outlets->isEmpty()) {
            $outlets = \App\Models\Outlet::all();
        }
        
        return view('admin.travel.package.create', compact('outlets'));
    }

    /**
     * Show the form for editing a package
     */
    public function edit($id)
    {
        Log::info('Loading Package Edit Page', ['package_id' => $id]);
        
        $package = TravelPackage::with(['outlet', 'hppCalculation', 'flight', 'hotel', 'hotelRoomType'])->find($id);
        
        if (!$package) {
            abort(404, 'Package not found');
        }
        
        // Get user's accessible outlets
        $user = Auth::user();
        $outlets = collect();
        
        if ($user && method_exists($user, 'outlets')) {
            $outlets = $user->outlets;
        }
        
        // If user has no outlet access, get all outlets (for super admin)
        if ($outlets->isEmpty()) {
            $outlets = \App\Models\Outlet::all();
        }
        
        return view('admin.travel.package.edit', compact('package', 'outlets'));
    }


    /**
     * Display package detail page
     */
    public function showPage($id)
    {
        $package = TravelPackage::with(['outlet', 'hppCalculation'])->find($id);
        
        if (!$package) {
            abort(404, 'Package not found');
        }

        return view('admin.travel.package.show', compact('package'));
    }

    /**
     * Get package data for DataTables
     */
    public function getData(Request $request)
    {
        Log::info('Fetching Package Data with filters', $request->all());

        $query = TravelPackage::with(['outlet', 'hppCalculation', 'flight', 'hotelRoomType', 'saudiTransport']);

        // Get user's outlet filter
        $user = Auth::user();
        if ($user && method_exists($user, 'outlets')) {
            $userOutlets = $user->outlets->pluck('id_outlet')->toArray();
            if (!empty($userOutlets)) {
                $query->whereIn('id_outlet', $userOutlets);
            }
        }

        // Filter by outlet (from dropdown)
        if ($request->has('outlet_filter') && $request->outlet_filter !== 'ALL') {
            $query->where('id_outlet', $request->outlet_filter);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by package type
        if ($request->has('type_filter') && $request->type_filter !== 'ALL') {
            $query->ofType($request->type_filter);
        }

        // Filter by status
        if ($request->has('status_filter') && $request->status_filter !== 'ALL') {
            $query->withStatus($request->status_filter);
        }

        // Filter by workflow stage
        if ($request->has('stage_filter') && $request->stage_filter !== 'ALL') {
            $query->inWorkflowStage($request->stage_filter);
        }

        // Sorting
        $sortColumn = $request->sort_key ?? 'id';
        $sortDirection = $request->sort_dir ?? 'desc';
        
        $columnMapping = [
            'code' => 'package_code',
            'name' => 'package_name',
            'type' => 'package_type',
            'departure' => 'departure_date',
            'price' => 'price',
            'status' => 'status'
        ];
        
        $sortColumn = $columnMapping[$sortColumn] ?? $sortColumn;
        $query->orderBy($sortColumn, $sortDirection);

        $packages = $query->get();

        return datatables()
            ->of($packages)
            ->addIndexColumn()
            ->addColumn('code', function ($package) {
                return $package->package_code;
            })
            ->addColumn('name', function ($package) {
                return $package->package_name;
            })
            ->addColumn('type', function ($package) {
                $badge = $package->package_type === 'hajj' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
                return '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $badge . '">' . strtoupper($package->package_type) . '</span>';
            })
            ->addColumn('departure_date', function ($package) {
                return $package->departure_date ? $package->departure_date->format('d M Y') : '-';
            })
            ->addColumn('duration', function ($package) {
                return $package->duration_days . ' hari';
            })
            ->addColumn('capacity', function ($package) {
                $bookings = $package->jamaahBookings()->with('jamaah')->whereNotIn('status', ['cancelled'])->get();
                $bookedCount = $bookings->sum(function($b) {
                    $fm = $b->jamaah->family_members ?? [];
                    if (is_string($fm)) $fm = json_decode($fm, true);
                    return 1 + (is_array($fm) ? count($fm) : 0);
                });
                $available = $package->capacity - $bookedCount;
                return $package->capacity . ' (' . $available . ' tersedia)';
            })
            ->addColumn('booked_count', function ($package) {
                $bookings = $package->jamaahBookings()->with('jamaah')->whereNotIn('status', ['cancelled'])->get();
                return $bookings->sum(function($b) {
                    $fm = $b->jamaah->family_members ?? [];
                    if (is_string($fm)) $fm = json_decode($fm, true);
                    return 1 + (is_array($fm) ? count($fm) : 0);
                });
            })
            ->addColumn('price', function ($package) {
                // Tampilkan semua paket harga jika ada
                if ($package->price_packages && is_array($package->price_packages) && count($package->price_packages) > 0) {
                    $lines = [];
                    foreach ($package->price_packages as $pkg) {
                        $pkgName = $pkg['name'] ?? 'Paket';
                        $variantLines = [];
                        foreach ($pkg['variants'] ?? [] as $v) {
                            if (($v['price'] ?? 0) > 0) {
                                $variantLines[] = ucfirst($v['type']) . ': Rp ' . number_format($v['price'], 0, ',', '.');
                            }
                        }
                        if (!empty($variantLines)) {
                            $lines[] = '<strong>' . $pkgName . '</strong><br><small>' . implode(' | ', $variantLines) . '</small>';
                        }
                    }
                    if (!empty($lines)) {
                        return implode('<br>', $lines);
                    }
                }
                return 'Rp ' . number_format($package->price, 0, ',', '.');
            })
            ->addColumn('price_raw', function ($package) {
                return $package->price;
            })
            ->addColumn('hpp', function ($package) {
                // Display HPP per person from hpp_calculation
                if ($package->hppCalculation) {
                    $hppPerPerson = $package->hppCalculation->getHppPerPerson();
                    return 'Rp ' . number_format($hppPerPerson, 0, ',', '.');
                }
                return '-';
            })
            ->addColumn('hpp_raw', function ($package) {
                // Return HPP per person for calculations
                if ($package->hppCalculation) {
                    return $package->hppCalculation->getHppPerPerson();
                }
                return 0;
            })
            ->addColumn('profit_margin', function ($package) {
                if ($package->hppCalculation && $package->price > 0) {
                    $hppPerPerson = $package->hppCalculation->getHppPerPerson();
                    $margin = (($package->price - $hppPerPerson) / $package->price) * 100;
                    return number_format($margin, 2) . '%';
                }
                return '-';
            })
            ->addColumn('profit_margin_raw', function ($package) {
                if ($package->hppCalculation && $package->price > 0) {
                    $hppPerPerson = $package->hppCalculation->getHppPerPerson();
                    return (($package->price - $hppPerPerson) / $package->price) * 100;
                }
                return 0;
            })
            ->addColumn('status', function ($package) {
                $badges = [
                    'draft' => 'bg-gray-100 text-gray-800',
                    'active' => 'bg-green-100 text-green-800',
                    'full' => 'bg-yellow-100 text-yellow-800',
                    'completed' => 'bg-blue-100 text-blue-800',
                    'cancelled' => 'bg-red-100 text-red-800'
                ];
                $badge = $badges[$package->status] ?? 'bg-gray-100 text-gray-800';
                return '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $badge . '">' . strtoupper($package->status) . '</span>';
            })
            ->addColumn('workflow_stage', function ($package) {
                return '<span class="text-xs text-gray-600">' . str_replace('_', ' ', ucwords($package->current_workflow_stage, '_')) . '</span>';
            })
            ->addColumn('outlet', function ($package) {
                return $package->outlet ? $package->outlet->nama_outlet : '-';
            })
            ->addColumn('id_flight', function ($package) {
                return $package->id_flight;
            })
            ->addColumn('id_hotel', function ($package) {
                return $package->id_hotel;
            })
            ->addColumn('id_hotel_room_type', function ($package) {
                return $package->id_hotel_room_type;
            })
            ->addColumn('id_flight_departure', function ($package) {
                return $package->id_flight_departure;
            })
            ->addColumn('id_flight_return', function ($package) {
                return $package->id_flight_return;
            })
            ->addColumn('id_hotel_makkah', function ($package) {
                return $package->id_hotel_makkah;
            })
            ->addColumn('id_hotel_room_type_makkah', function ($package) {
                return $package->id_hotel_room_type_makkah;
            })
            ->addColumn('id_hotel_madinah', function ($package) {
                return $package->id_hotel_madinah;
            })
            ->addColumn('id_hotel_room_type_madinah', function ($package) {
                return $package->id_hotel_room_type_madinah;
            })
            ->addColumn('id_saudi_transport', function ($package) {
                return $package->id_saudi_transport;
            })
            ->addColumn('saudi_transport_price', function ($package) {
                if ($package->id_saudi_transport && $package->saudiTransport) {
                    return (float) $package->saudiTransport->price_per_person;
                }
                return 0;
            })
            ->addColumn('duration_days', function ($package) {
                return $package->duration_days;
            })
            ->addColumn('id_outlet', function ($package) {
                return $package->id_outlet;
            })
            ->addColumn('price_packages', function ($package) {
                return $package->price_packages ?? [];
            })
            ->addColumn('aksi', function ($package) {
                return '
                    <div class="flex justify-end gap-2">
                        <a href="'. route('admin.inventaris.travel.package.detail', $package->id) .'" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                            <i class="bx bx-show"></i> Detail
                        </a>
                        <button onclick="editForm(`'. route('admin.inventaris.travel.package.update', $package->id) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">
                            <i class="bx bx-edit-alt"></i> Edit
                        </button>
                        <button onclick="manageHpp(`'. route('admin.inventaris.travel.package.hpp', $package->id) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-blue-200 text-blue-700 px-3 py-1.5 hover:bg-blue-50">
                            <i class="bx bx-calculator"></i> HPP
                        </button>
                        <button onclick="deleteData(`'. route('admin.inventaris.travel.package.destroy', $package->id) .'`)" class="inline-flex items-center gap-1 rounded-lg border border-red-200 text-red-700 px-3 py-1.5 hover:bg-red-50">
                            <i class="bx bx-trash"></i> Hapus
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['type', 'status', 'workflow_stage', 'aksi', 'price'])
            ->make(true);
    }

    /**
     * Store a new package
     */
    public function store(Request $request)
    {
        // Use custom validation messages from lang file
        $validator = Validator::make($request->all(), [
            'package_code' => 'required|string|max:255|unique:travel_packages,package_code',
            'package_name' => 'required|string|max:255',
            'package_subtype' => 'required|in:umroh_regular,umroh_plus,umroh_ramadhan,haji',
            'description' => 'nullable|string',
            'ustadz_name' => 'nullable|string|max:255',
            'inclusions' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'thumbnail_crop_settings' => 'nullable|json',
            'package_photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'duration_days' => 'required|integer|min:1',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            // Flight Information
            'id_flight_departure' => 'nullable|exists:flights,id',
            'departure_datetime' => 'nullable|date',
            'id_flight_return' => 'nullable|exists:flights,id',
            'return_datetime' => 'nullable|date',
            // Hotel Makkah
            'id_hotel_makkah' => 'nullable|exists:hotels,id',
            'id_hotel_room_type_makkah' => 'nullable|exists:hotel_room_types,id',
            'makkah_check_in' => 'nullable|date',
            'makkah_check_out' => 'nullable|date|after:makkah_check_in',
            // Hotel Madinah
            'id_hotel_madinah' => 'nullable|exists:hotels,id',
            'id_hotel_room_type_madinah' => 'nullable|exists:hotel_room_types,id',
            'madinah_check_in' => 'nullable|date',
            'madinah_check_out' => 'nullable|date|after:madinah_check_in',
            // Saudi Transport
            'id_saudi_transport' => 'nullable|exists:saudi_transports,id',
            // Legacy fields (for backward compatibility)
            'id_flight' => 'nullable|exists:flights,id',
            'id_hotel' => 'nullable|exists:hotels,id',
            'id_hotel_room_type' => 'nullable|exists:hotel_room_types,id',
            'airline' => 'nullable|string|max:255',
            'hotel_name' => 'nullable|string|max:255',
            'departure_date' => 'nullable|date',
            'return_date' => 'nullable|date'
        ], [
            'package_code.required' => __('travel-validation.required', ['attribute' => 'kode paket']),
            'package_code.unique' => __('travel-validation.unique', ['attribute' => 'kode paket']),
            'package_name.required' => __('travel-validation.required', ['attribute' => 'nama paket']),
            'package_subtype.required' => __('travel-validation.required', ['attribute' => 'kategori paket']),
            'package_subtype.in' => __('travel-validation.in', ['attribute' => 'kategori paket']),
            'duration_days.required' => __('travel-validation.required', ['attribute' => 'durasi']),
            'duration_days.min' => __('travel-validation.min.numeric', ['attribute' => 'durasi', 'min' => 1]),
            'departure_date.required' => __('travel-validation.departure_date.required'),
            'departure_date.after' => __('travel-validation.departure_date.future'),
            'return_date.required' => __('travel-validation.return_date.required'),
            'return_date.after' => __('travel-validation.return_date.after_departure'),
            'capacity.required' => __('travel-validation.required', ['attribute' => 'kapasitas']),
            'capacity.min' => __('travel-validation.min.numeric', ['attribute' => 'kapasitas', 'min' => 1]),
            'price.required' => __('travel-validation.required', ['attribute' => 'harga']),
            'price.min' => __('travel-validation.positive', ['attribute' => 'harga']),
            'image.image' => __('travel-validation.image', ['attribute' => 'gambar']),
            'image.mimes' => __('travel-validation.mimes', ['attribute' => 'gambar', 'values' => 'jpeg, png, jpg']),
            'image.max' => __('travel-validation.max_file', ['attribute' => 'gambar', 'max' => 2048]),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Additional validation: Check if price > HPP (warning only, not blocking)
        // This will be checked after HPP is calculated
        $warnings = [];

        DB::beginTransaction();
        try {
            $data = $request->except(['image', 'package_photos']);
            
            // Auto-set package_type based on package_subtype
            if (isset($data['package_subtype'])) {
                $data['package_type'] = ($data['package_subtype'] === 'haji') ? 'hajj' : 'umrah';
            }
            
            // Handle thumbnail_crop_settings JSON string → decode to array
            if (isset($data['thumbnail_crop_settings']) && is_string($data['thumbnail_crop_settings'])) {
                $decoded = json_decode($data['thumbnail_crop_settings'], true);
                $data['thumbnail_crop_settings'] = is_array($decoded) ? $decoded : null;
            }
            
            // Handle price_packages JSON string → decode to array (model will re-encode)
            if (isset($data['price_packages']) && is_string($data['price_packages'])) {
                $decoded = json_decode($data['price_packages'], true);
                $data['price_packages'] = is_array($decoded) ? $decoded : null;
                // Extract max price from price_packages if price not set
                if (empty($data['price']) && is_array($decoded)) {
                    $maxPrice = 0;
                    foreach ($decoded as $pkg) {
                        foreach ($pkg['variants'] ?? [] as $v) {
                            $maxPrice = max($maxPrice, (float)($v['price'] ?? 0));
                        }
                    }
                    if ($maxPrice > 0) $data['price'] = $maxPrice;
                }
            }
            
            // Handle main image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('travel-packages', 'public');
                $data['image_path'] = $imagePath;
            }
            
            // Handle multiple package photos upload
            $packagePhotos = [];
            if ($request->hasFile('package_photos')) {
                foreach ($request->file('package_photos') as $photo) {
                    $photoPath = $photo->store('travel-packages/photos', 'public');
                    $packagePhotos[] = $photoPath;
                }
                $data['package_photos'] = json_encode($packagePhotos);
            }
            
            // Handle saudi_transports JSON string → decode to array
            if (isset($data['saudi_transports']) && is_string($data['saudi_transports'])) {
                $decoded = json_decode($data['saudi_transports'], true);
                $data['saudi_transports'] = is_array($decoded) ? $decoded : null;
            }
            
            // Handle hotels JSON string → decode to array
            if (isset($data['hotels']) && is_string($data['hotels'])) {
                $decoded = json_decode($data['hotels'], true);
                $data['hotels'] = is_array($decoded) ? $decoded : null;
            }
            
            // Convert boolean strings to integers for MySQL TINYINT columns
            if (isset($data['is_promo'])) {
                $data['is_promo'] = filter_var($data['is_promo'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
            if (isset($data['is_best_seller'])) {
                $data['is_best_seller'] = filter_var($data['is_best_seller'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
            
            // Set default dates if not provided
            if (!isset($data['departure_date']) && isset($data['departure_datetime'])) {
                $data['departure_date'] = date('Y-m-d', strtotime($data['departure_datetime']));
            }
            if (!isset($data['return_date']) && isset($data['return_datetime'])) {
                $data['return_date'] = date('Y-m-d', strtotime($data['return_datetime']));
            }
            
            // Create package
            $package = TravelPackage::create(array_merge(
                $data,
                [
                    'status' => 'draft',
                    'current_workflow_stage' => 'product_analysis',
                    'view_count' => 0,
                    'booking_count' => 0
                ]
            ));

            // Create empty HPP calculation
            HppCalculation::create([
                'id_travel_package' => $package->id,
                'flight_cost' => 0,
                'hotel_cost' => 0,
                'transportation_cost' => 0,
                'meal_cost' => 0,
                'visa_cost' => 0,
                'guide_cost' => 0,
                'insurance_cost' => 0,
                'operational_overhead' => 0,
                'contingency' => 0,
                'total_hpp' => 0
            ]);

            // AUTO-CREATE DEFAULT KEBERANGKATAN
            // Buat keberangkatan default sesuai tanggal paket
            if ($package->departure_date && $package->return_date) {
                $keberangkatanCode = 'KB-' . strtoupper($package->package_code) . '-' . $package->departure_date->format('Ymd');
                $keberangkatanName = $package->package_name . ' - ' . $package->departure_date->format('d M Y');
                
                \App\Models\Keberangkatan::create([
                    'keberangkatan_code' => $keberangkatanCode,
                    'keberangkatan_name' => $keberangkatanName,
                    'id_travel_package' => $package->id,
                    'departure_date' => $package->departure_date,
                    'return_date' => $package->return_date,
                    'total_jamaah' => $package->capacity, // Set capacity from package
                    'status' => 'planning', // Valid enum: planning, confirmed, departed, completed
                    'id_outlet' => $package->id_outlet,
                ]);
                
                Log::info('Default keberangkatan created for package', [
                    'package_id' => $package->id,
                    'keberangkatan_code' => $keberangkatanCode
                ]);
            }

            DB::commit();
            Log::info('Package created successfully', ['package_id' => $package->id]);

            return response()->json([
                'success' => true,
                'message' => 'Paket perjalanan berhasil disimpan',
                'data' => $package,
                'warnings' => $warnings
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating package: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menyimpan paket perjalanan'
            ], 500);
        }
    }

    /**
     * Show a specific package
     */
    public function show($id)
    {
        $package = TravelPackage::with([
            'outlet', 
            'hppCalculation',
            'flightDeparture',
            'flightReturn',
            'hotelMakkah',
            'hotelRoomTypeMakkah',
            'hotelMadinah',
            'hotelRoomTypeMadinah'
        ])->find($id);
        
        if (!$package) {
            return response()->json(['error' => 'Paket tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $package->id,
            'package_code' => $package->package_code,
            'package_name' => $package->package_name,
            'package_type' => $package->package_type,
            'description' => $package->description,
            'inclusions' => $package->inclusions,
            'duration_days' => $package->duration_days,
            'capacity' => $package->capacity,
            'available_seats' => $package->getAvailableSeats(),
            'price' => $package->price,
            'hpp' => $package->hpp,
            'profit_margin' => $package->profit_margin,
            'status' => $package->status,
            'current_workflow_stage' => $package->current_workflow_stage,
            'id_outlet' => $package->id_outlet,
            'outlet_name' => $package->outlet ? $package->outlet->nama_outlet : null,
            // Flight Information
            'id_flight_departure' => $package->id_flight_departure,
            'departure_datetime' => $package->departure_datetime ? $package->departure_datetime->format('Y-m-d\TH:i') : null,
            'flight_departure' => $package->flightDeparture ? [
                'id' => $package->flightDeparture->id,
                'airline_name' => $package->flightDeparture->airline_name,
                'flight_number' => $package->flightDeparture->flight_number,
                'route' => $package->flightDeparture->departure_airport . ' → ' . $package->flightDeparture->arrival_airport
            ] : null,
            'id_flight_return' => $package->id_flight_return,
            'return_datetime' => $package->return_datetime ? $package->return_datetime->format('Y-m-d\TH:i') : null,
            'flight_return' => $package->flightReturn ? [
                'id' => $package->flightReturn->id,
                'airline_name' => $package->flightReturn->airline_name,
                'flight_number' => $package->flightReturn->flight_number,
                'route' => $package->flightReturn->departure_airport . ' → ' . $package->flightReturn->arrival_airport
            ] : null,
            // Hotel Makkah
            'id_hotel_makkah' => $package->id_hotel_makkah,
            'id_hotel_room_type_makkah' => $package->id_hotel_room_type_makkah,
            'makkah_check_in' => $package->makkah_check_in ? $package->makkah_check_in->format('Y-m-d') : null,
            'makkah_check_out' => $package->makkah_check_out ? $package->makkah_check_out->format('Y-m-d') : null,
            'hotel_makkah' => $package->hotelMakkah ? [
                'id' => $package->hotelMakkah->id,
                'hotel_name' => $package->hotelMakkah->hotel_name,
                'location' => $package->hotelMakkah->location,
                'star_rating' => $package->hotelMakkah->star_rating
            ] : null,
            'hotel_room_type_makkah' => $package->hotelRoomTypeMakkah ? [
                'id' => $package->hotelRoomTypeMakkah->id,
                'room_type_name' => $package->hotelRoomTypeMakkah->room_type_name,
                'price_per_night' => $package->hotelRoomTypeMakkah->price_per_night
            ] : null,
            // Hotel Madinah
            'id_hotel_madinah' => $package->id_hotel_madinah,
            'id_hotel_room_type_madinah' => $package->id_hotel_room_type_madinah,
            'madinah_check_in' => $package->madinah_check_in ? $package->madinah_check_in->format('Y-m-d') : null,
            'madinah_check_out' => $package->madinah_check_out ? $package->madinah_check_out->format('Y-m-d') : null,
            'hotel_madinah' => $package->hotelMadinah ? [
                'id' => $package->hotelMadinah->id,
                'hotel_name' => $package->hotelMadinah->hotel_name,
                'location' => $package->hotelMadinah->location,
                'star_rating' => $package->hotelMadinah->star_rating
            ] : null,
            'hotel_room_type_madinah' => $package->hotelRoomTypeMadinah ? [
                'id' => $package->hotelRoomTypeMadinah->id,
                'room_type_name' => $package->hotelRoomTypeMadinah->room_type_name,
                'price_per_night' => $package->hotelRoomTypeMadinah->price_per_night
            ] : null,
            // Photos
            'image_url' => $package->getImageUrl(),
            'package_photos' => $package->getPackagePhotosUrls(),
            // HPP Calculation
            'hpp_calculation' => $package->hppCalculation ? $package->hppCalculation->getCostBreakdown() : null,
            // Legacy fields
            'departure_date' => $package->departure_date ? $package->departure_date->format('Y-m-d') : null,
            'return_date' => $package->return_date ? $package->return_date->format('Y-m-d') : null,
            // Price packages
            'price_packages' => $package->price_packages ?? [],
            // Saudi Transport
            'id_saudi_transport' => $package->id_saudi_transport,
            'saudi_transport' => $package->saudiTransport ? [
                'id'             => $package->saudiTransport->id,
                'transport_name' => $package->saudiTransport->transport_name,
                'transport_type' => $package->saudiTransport->transport_type,
                'type_label'     => $package->saudiTransport->getTypeLabel(),
                'route'          => trim(($package->saudiTransport->route_from ?? '') . ' → ' . ($package->saudiTransport->route_to ?? ''), ' → '),
                'operator'       => $package->saudiTransport->operator,
                'seller_name'    => $package->saudiTransport->seller_name,
            ] : null,
            // Multiple Saudi Transports
            'saudi_transports' => $package->saudi_transports ?? ['makkah' => [], 'madinah' => []],
        ]);
    }

    /**
     * Update a package
     */
    public function update(Request $request, $id)
    {
        $package = TravelPackage::find($id);
        
        if (!$package) {
            return response()->json(['error' => 'Paket tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'package_code' => 'required|string|max:255|unique:travel_packages,package_code,' . $id,
            'package_name' => 'required|string|max:255',
            'package_subtype' => 'required|in:umroh_regular,umroh_plus,umroh_ramadhan,haji',
            'description' => 'nullable|string',
            'ustadz_name' => 'nullable|string|max:255',
            'inclusions' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'thumbnail_crop_settings' => 'nullable|json',
            'package_photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'duration_days' => 'required|integer|min:1',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|in:draft,active,full,completed,cancelled',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            // Flight Information
            'id_flight_departure' => 'nullable|exists:flights,id',
            'departure_datetime' => 'nullable|date',
            'id_flight_return' => 'nullable|exists:flights,id',
            'return_datetime' => 'nullable|date',
            // Hotel Makkah
            'id_hotel_makkah' => 'nullable|exists:hotels,id',
            'id_hotel_room_type_makkah' => 'nullable|exists:hotel_room_types,id',
            'makkah_check_in' => 'nullable|date',
            'makkah_check_out' => 'nullable|date|after:makkah_check_in',
            // Hotel Madinah
            'id_hotel_madinah' => 'nullable|exists:hotels,id',
            'id_hotel_room_type_madinah' => 'nullable|exists:hotel_room_types,id',
            'madinah_check_in' => 'nullable|date',
            'madinah_check_out' => 'nullable|date|after:madinah_check_in',
            // Saudi Transport
            'id_saudi_transport' => 'nullable|exists:saudi_transports,id',
            // Legacy fields
            'id_flight' => 'nullable|exists:flights,id',
            'id_hotel' => 'nullable|exists:hotels,id',
            'id_hotel_room_type' => 'nullable|exists:hotel_room_types,id',
            'airline' => 'nullable|string|max:255',
            'hotel_name' => 'nullable|string|max:255',
            'departure_date' => 'nullable|date',
            'return_date' => 'nullable|date'
        ], [
            'departure_date.after' => 'Tanggal keberangkatan harus di masa depan',
            'return_date.after' => 'Tanggal kembali harus setelah tanggal keberangkatan',
            'capacity.min' => 'Kapasitas harus berupa bilangan positif',
            'price.min' => 'Harga harus berupa bilangan positif'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Additional validation: Check if price > HPP (warning only)
        $warnings = [];
        if ($package->hpp && $request->price <= $package->hpp) {
            $warnings[] = 'Peringatan: Harga jual lebih rendah atau sama dengan HPP. Margin keuntungan rendah atau negatif.';
        }

        try {
            $data = $request->except(['image', 'package_photos']);
            
            // Auto-set package_type based on package_subtype
            if (isset($data['package_subtype'])) {
                $data['package_type'] = ($data['package_subtype'] === 'haji') ? 'hajj' : 'umrah';
            }
            
            // Handle thumbnail_crop_settings JSON string → decode to array
            if (isset($data['thumbnail_crop_settings']) && is_string($data['thumbnail_crop_settings'])) {
                $decoded = json_decode($data['thumbnail_crop_settings'], true);
                $data['thumbnail_crop_settings'] = is_array($decoded) ? $decoded : null;
            }
            
            // Handle price_packages JSON string → decode to array (model will re-encode)
            if (isset($data['price_packages']) && is_string($data['price_packages'])) {
                $decoded = json_decode($data['price_packages'], true);
                $data['price_packages'] = is_array($decoded) ? $decoded : null;
                // Extract max price from price_packages if price not set
                if (empty($data['price']) && is_array($decoded)) {
                    $maxPrice = 0;
                    foreach ($decoded as $pkg) {
                        foreach ($pkg['variants'] ?? [] as $v) {
                            $maxPrice = max($maxPrice, (float)($v['price'] ?? 0));
                        }
                    }
                    if ($maxPrice > 0) $data['price'] = $maxPrice;
                }
            }
            
            // Handle main image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($package->image_path && \Storage::disk('public')->exists($package->image_path)) {
                    \Storage::disk('public')->delete($package->image_path);
                }
                
                $image = $request->file('image');
                $imagePath = $image->store('travel-packages', 'public');
                $data['image_path'] = $imagePath;
            }
            
            // Handle multiple package photos upload
            if ($request->hasFile('package_photos')) {
                $packagePhotos = $package->package_photos ?? [];
                
                foreach ($request->file('package_photos') as $photo) {
                    $photoPath = $photo->store('travel-packages/photos', 'public');
                    $packagePhotos[] = $photoPath;
                }
                
                $data['package_photos'] = json_encode($packagePhotos);
            }
            
            // Handle saudi_transports JSON string → decode to array
            if (isset($data['saudi_transports']) && is_string($data['saudi_transports'])) {
                $decoded = json_decode($data['saudi_transports'], true);
                $data['saudi_transports'] = is_array($decoded) ? $decoded : null;
            }
            
            // Handle hotels JSON string → decode to array
            if (isset($data['hotels']) && is_string($data['hotels'])) {
                $decoded = json_decode($data['hotels'], true);
                $data['hotels'] = is_array($decoded) ? $decoded : null;
            }
            
            // Convert boolean strings to integers for MySQL TINYINT columns
            if (isset($data['is_promo'])) {
                $data['is_promo'] = filter_var($data['is_promo'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
            if (isset($data['is_best_seller'])) {
                $data['is_best_seller'] = filter_var($data['is_best_seller'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
            
            // Set default dates if not provided
            if (!isset($data['departure_date']) && isset($data['departure_datetime'])) {
                $data['departure_date'] = date('Y-m-d', strtotime($data['departure_datetime']));
            }
            if (!isset($data['return_date']) && isset($data['return_datetime'])) {
                $data['return_date'] = date('Y-m-d', strtotime($data['return_datetime']));
            }
            
            $package->update($data);
            
            // Recalculate profit margin if price changed
            if ($request->has('price')) {
                $package->calculateProfitMargin();
                $package->save();
            }

            Log::info('Package updated successfully', ['package_id' => $package->id]);

            return response()->json([
                'message' => 'Paket perjalanan berhasil diupdate',
                'data' => $package,
                'warnings' => $warnings
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating package: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mengupdate paket perjalanan'
            ], 500);
        }
    }

    /**
     * Delete a package
     */
    public function destroy($id)
    {
        $package = TravelPackage::find($id);
        
        if (!$package) {
            return response()->json(['error' => 'Paket tidak ditemukan'], 404);
        }

        $force = request()->boolean('force');

        // Check if package has active bookings
        $activeBookings = $package->jamaahBookings()
            ->whereNotIn('status', ['cancelled'])
            ->count();

        if ($activeBookings > 0 && !$force) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus paket: terdapat ' . $activeBookings . ' booking aktif',
                'code' => 'ACTIVE_BOOKINGS_EXIST'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Force: cancel all active bookings first
            if ($force && $activeBookings > 0) {
                $package->jamaahBookings()
                    ->whereNotIn('status', ['cancelled'])
                    ->update(['status' => 'cancelled']);
            }

            if ($package->hppCalculation) {
                $package->hppCalculation->delete();
            }
            
            $package->delete();

            DB::commit();
            Log::info('Package deleted successfully', ['package_id' => $id, 'force' => $force]);

            return response()->json([
                'message' => 'Paket perjalanan berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting package: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menghapus paket perjalanan'
            ], 500);
        }
    }

    /**
     * Get HPP calculation for a package
     */
    public function getHpp($id)
    {
        $package = TravelPackage::with('hppCalculation')->find($id);
        
        if (!$package) {
            return response()->json(['error' => 'Paket tidak ditemukan'], 404);
        }

        if (!$package->hppCalculation) {
            return response()->json(['error' => 'HPP calculation tidak ditemukan'], 404);
        }

        $hpp = $package->hppCalculation;
        
        return response()->json([
            'id' => $hpp->id,
            'id_travel_package' => $hpp->id_travel_package,
            'flight_cost' => $hpp->flight_cost,
            'hotel_cost' => $hpp->hotel_cost,
            'transportation_cost' => $hpp->transportation_cost,
            'meal_cost' => $hpp->meal_cost,
            'visa_cost' => $hpp->visa_cost,
            'guide_cost' => $hpp->guide_cost,
            'insurance_cost' => $hpp->insurance_cost,
            'operational_overhead' => $hpp->operational_overhead,
            'contingency' => $hpp->contingency,
            'total_hpp' => $hpp->total_hpp,
            'is_locked' => $hpp->is_locked,
            'locked_at' => $hpp->locked_at,
            'locked_by' => $hpp->locked_by,
            'package_price' => $package->price,
            'profit_margin' => $package->profit_margin,
            'component_payment_status' => $hpp->component_payment_status ?? [],
            'component_hutang_amount' => $hpp->component_hutang_amount ?? [],
            'custom_components' => $hpp->custom_components ?? [], // IMPORTANT: Send custom components to frontend
        ]);
    }

    /**
     * Update HPP calculation
     */
    public function updateHpp(Request $request, $id)
    {
        $package = TravelPackage::with('hppCalculation')->find($id);
        
        if (!$package) {
            return response()->json(['error' => 'Paket tidak ditemukan'], 404);
        }

        if (!$package->hppCalculation) {
            return response()->json(['error' => 'HPP calculation tidak ditemukan'], 404);
        }

        $hpp = $package->hppCalculation;

        // Check if HPP is locked
        if ($hpp->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'HPP calculation sudah terkunci dan tidak dapat diubah',
                'code' => 'HPP_LOCKED'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'flight_cost' => 'required|numeric|min:0',
            'hotel_cost' => 'required|numeric|min:0',
            'transportation_cost' => 'required|numeric|min:0',
            'meal_cost' => 'required|numeric|min:0',
            'visa_cost' => 'required|numeric|min:0',
            'guide_cost' => 'required|numeric|min:0',
            'insurance_cost' => 'required|numeric|min:0',
            'operational_overhead' => 'required|numeric|min:0',
            'contingency' => 'required|numeric|min:0'
        ], [
            '*.min' => 'Semua komponen biaya harus berupa bilangan non-negatif'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // DEBUG: Log request data
            Log::info('=== HPP UPDATE REQUEST ===', [
                'package_id' => $package->id,
                'has_custom_components' => $request->has('custom_components'),
                'custom_components' => $request->input('custom_components'),
                'all_keys' => array_keys($request->all()),
            ]);
            
            // Update HPP components (standard fields)
            $hpp->update($request->only([
                'flight_cost',
                'hotel_cost',
                'transportation_cost',
                'meal_cost',
                'visa_cost',
                'guide_cost',
                'insurance_cost',
                'operational_overhead',
                'contingency'
            ]));

            // Save custom components from request
            $customComponents = $request->input('custom_components', []);
            if (is_array($customComponents)) {
                Log::info('Saving custom components:', $customComponents);
                $hpp->custom_components = $customComponents;
            } else {
                Log::warning('Custom components is not an array:', ['type' => gettype($customComponents), 'value' => $customComponents]);
            }

            // Update payment status & hutang amounts
            if ($request->has('component_payment_status')) {
                $hpp->component_payment_status = $request->component_payment_status;
            }
            if ($request->has('component_hutang_amount')) {
                $hpp->component_hutang_amount = $request->component_hutang_amount;
            }

            // Calculate total HPP
            $hpp->calculateTotal();
            $hpp->save();

            // Update package HPP and profit margin
            $package->updateHppFromCalculation();

            // Sync status ke RAB keberangkatan yang sudah ada
            $keberangkatans = $package->keberangkatan()->whereNotNull('id_rab')->get();
            if ($keberangkatans->isNotEmpty()) {
                $rabService = app(\App\Services\RabIntegrationService::class);
                foreach ($keberangkatans as $kb) {
                    try { $rabService->syncHppStatusToRab($kb); } catch (\Exception $e) {
                        Log::warning('Failed to sync HPP to RAB: ' . $e->getMessage());
                    }
                }
            }

            DB::commit();
            Log::info('HPP calculation updated successfully', ['package_id' => $package->id]);

            return response()->json([
                'message' => 'HPP calculation berhasil diupdate',
                'data' => [
                    'hpp' => $hpp->getCostBreakdown(),
                    'package_price' => $package->price,
                    'profit_margin' => $package->profit_margin
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating HPP calculation: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mengupdate HPP calculation'
            ], 500);
        }
    }

    /**
     * Lock HPP calculation
     */
    public function lockHpp($id)
    {
        $package = TravelPackage::with('hppCalculation')->find($id);
        
        if (!$package) {
            return response()->json(['error' => 'Paket tidak ditemukan'], 404);
        }

        if (!$package->hppCalculation) {
            return response()->json(['error' => 'HPP calculation tidak ditemukan'], 404);
        }

        $hpp = $package->hppCalculation;

        if ($hpp->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'HPP calculation sudah terkunci',
                'code' => 'HPP_ALREADY_LOCKED'
            ], 400);
        }

        try {
            $hpp->lock(Auth::id());

            Log::info('HPP calculation locked successfully', ['package_id' => $package->id]);

            return response()->json([
                'message' => 'HPP calculation berhasil dikunci',
                'data' => $hpp
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error locking HPP calculation: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mengunci HPP calculation'
            ], 500);
        }
    }

    /**
     * Get package types for filter
     */
    public function getPackageTypes()
    {
        return response()->json(['hajj', 'umrah']);
    }

    /**
     * Get workflow stages for filter
     */
    public function getWorkflowStages()
    {
        $stages = [
            'product_analysis',
            'flight_tickets',
            'design_materials',
            'finance',
            'follow_up',
            'closing',
            'cs_all_divisions',
            'social_media',
            'administration',
            'logistics',
            'save_jamaah_data',
            'offer_package'
        ];

        return response()->json($stages);
    }

    /**
     * Get workflow progress for a package
     */
    public function getWorkflowProgress($id)
    {
        $package = TravelPackage::with(['workflowHistory.user', 'workflowHistory.fromStageDetails', 'workflowHistory.toStageDetails'])->find($id);
        
        if (!$package) {
            return response()->json(['error' => 'Paket tidak ditemukan'], 404);
        }

        $workflowService = app(\App\Services\WorkflowService::class);
        $progress = $workflowService->getWorkflowProgress($package);
        $history = $package->workflowHistory()->with(['user', 'fromStageDetails', 'toStageDetails'])->ordered()->get();

        return response()->json([
            'progress' => $progress,
            'history' => $history,
            'current_stage' => $package->current_workflow_stage,
            'next_stage' => $workflowService->getNextStage($package)
        ]);
    }

    /**
     * Transition package to next workflow stage
     */
    public function transitionWorkflow(Request $request, $id)
    {
        $package = TravelPackage::find($id);
        
        if (!$package) {
            return response()->json(['error' => 'Paket tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'to_stage' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workflowService = app(\App\Services\WorkflowService::class);
        $result = $workflowService->transitionToStage($package, $request->to_stage, $request->notes);

        if ($result['success']) {
            Log::info('Workflow transition successful', [
                'package_id' => $package->id,
                'to_stage' => $request->to_stage
            ]);

            return response()->json([
                'message' => $result['message'],
                'data' => [
                    'current_stage' => $package->fresh()->current_workflow_stage,
                    'stage_details' => $result['stage']
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
    }

    /**
     * Get workflow tasks for a package
     */
    public function getWorkflowTasks($id)
    {
        $package = TravelPackage::find($id);
        
        if (!$package) {
            return response()->json(['error' => 'Paket tidak ditemukan'], 404);
        }

        $tasks = \App\Models\WorkflowTask::where('id_travel_package', $package->id)
            ->with(['workflowStage', 'team', 'assignedUser', 'completedByUser'])
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json($tasks);
    }

    /**
     * Get available flights for HPP calculation
     */
    public function getFlights(Request $request)
    {
        $query = \App\Models\Flight::query();

        // Filter by user's outlet access
        $user = Auth::user();
        if ($user && method_exists($user, 'outlets')) {
            $userOutlets = $user->outlets->pluck('id_outlet')->toArray();
            if (!empty($userOutlets)) {
                $query->whereIn('id_outlet', $userOutlets);
            }
        }

        // Filter by outlet if provided
        if ($request->has('outlet_id') && $request->outlet_id) {
            $query->where('id_outlet', $request->outlet_id);
        }

        // Search by airline name or flight number
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('airline_name', 'like', '%' . $request->search . '%')
                  ->orWhere('flight_number', 'like', '%' . $request->search . '%')
                  ->orWhere('departure_airport', 'like', '%' . $request->search . '%')
                  ->orWhere('arrival_airport', 'like', '%' . $request->search . '%');
            });
        }

        $flights = $query->orderBy('airline_name')
            ->orderBy('flight_number')
            ->get()
            ->map(function($flight) {
                $transitInfo = $flight->hasTransit() ? ' [' . $flight->getFormattedTransitInfo() . ']' : '';
                return [
                    'id' => $flight->id,
                    'label' => $flight->airline_name . ' - ' . $flight->flight_number . ' (' . $flight->departure_airport . ' → ' . $flight->arrival_airport . ')' . $transitInfo,
                    'airline_name' => $flight->airline_name,
                    'flight_number' => $flight->flight_number,
                    'flight_group_code' => $flight->flight_group_code,
                    'flight_direction' => $flight->flight_direction,
                    'departure_airport' => $flight->departure_airport,
                    'arrival_airport' => $flight->arrival_airport,
                    'departure_time' => $flight->departure_time ? $flight->departure_time->format('Y-m-d\TH:i') : null,
                    'arrival_time' => $flight->arrival_time ? $flight->arrival_time->format('Y-m-d\TH:i') : null,
                    'transit_info' => $flight->transit_info,
                    'has_transit' => $flight->hasTransit(),
                    'formatted_transit' => $flight->getFormattedTransitInfo(),
                    'price_per_person' => $flight->price_per_person,
                    'capacity' => $flight->capacity,
                    'seller_name' => $flight->seller_name,
                ];
            });

        return response()->json($flights);
    }

    /**
     * Get available hotels for HPP calculation
     */
    public function getHotels(Request $request)
    {
        $query = \App\Models\Hotel::with('roomTypes');

        // Filter by user's outlet access
        $user = Auth::user();
        if ($user && method_exists($user, 'outlets')) {
            $userOutlets = $user->outlets->pluck('id_outlet')->toArray();
            if (!empty($userOutlets)) {
                $query->whereIn('id_outlet', $userOutlets);
            }
        }

        // Filter by outlet if provided
        if ($request->has('outlet_id') && $request->outlet_id) {
            $query->where('id_outlet', $request->outlet_id);
        }

        // Search by hotel name or location
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('hotel_name', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by city if provided
        if ($request->has('city') && $request->city) {
            $query->where('city', $request->city);
        }

        $hotels = $query->orderBy('hotel_name')
            ->get()
            ->map(function($hotel) {
                return [
                    'id' => $hotel->id,
                    'hotel_name' => $hotel->hotel_name,
                    'location' => $hotel->location,
                    'city' => $hotel->city,
                    'star_rating' => $hotel->star_rating,
                    'seller_name' => $hotel->seller_name,
                    'room_types' => $hotel->roomTypes->map(function($rt) {
                        return [
                            'id' => $rt->id,
                            'room_type_name' => $rt->room_type_name,
                            'price_per_night' => $rt->price_per_night,
                        ];
                    }),
                ];
            });

        return response()->json($hotels);
    }

    /**
     * Get available Saudi transports for package creation
     */
    public function getSaudiTransports(Request $request)
    {
        $query = \App\Models\SaudiTransport::query();

        $user = Auth::user();
        if ($user && method_exists($user, 'outlets')) {
            $userOutlets = $user->outlets->pluck('id_outlet')->toArray();
            if (!empty($userOutlets)) {
                $query->whereIn('id_outlet', $userOutlets);
            }
        }

        $transports = $query->orderBy('transport_name')->get()->map(function($t) {
            $route = trim(($t->route_from ?? '') . ' → ' . ($t->route_to ?? ''), ' → ');
            return [
                'id'             => $t->id,
                'transport_code' => $t->transport_code,
                'transport_name' => $t->transport_name,
                'transport_type' => $t->transport_type,
                'type_label'     => $t->getTypeLabel(),
                'route'          => $route,
                'operator'       => $t->operator,
                'price_per_person' => $t->price_per_person,
                'seller_name'    => $t->seller_name,
            ];
        });

        return response()->json($transports);
    }

    /**
     * Get flight groups for quick selection
     */
    public function getFlightGroups(Request $request)
    {
        $user = Auth::user();
        $query = \App\Models\Flight::query();

        // Filter by user's outlet access
        if ($user && method_exists($user, 'outlets')) {
            $userOutlets = $user->outlets->pluck('id_outlet')->toArray();
            if (!empty($userOutlets)) {
                $query->whereIn('id_outlet', $userOutlets);
            }
        }

        // Get all flights with group codes, grouped by code
        $allGroupFlights = $query->whereNotNull('flight_group_code')
            ->orderBy('flight_group_code')
            ->orderBy('flight_direction')
            ->get()
            ->groupBy('flight_group_code');

        $groups = [];
        foreach ($allGroupFlights as $groupCode => $flights) {
            // Separate departure and return flights
            $departureFlights = $flights->where('flight_direction', 'departure')->values();
            $returnFlights = $flights->where('flight_direction', 'return')->values();

            // Build label with all flights
            $label = $groupCode;
            
            if ($departureFlights->count() > 0) {
                $label .= ' - Berangkat: ';
                $depLabels = [];
                foreach ($departureFlights as $f) {
                    $transitInfo = $f->hasTransit() ? ' [' . $f->getFormattedTransitInfo() . ']' : '';
                    $depLabels[] = $f->airline_name . ' ' . $f->flight_number . 
                                  ' (' . $f->departure_airport . ' → ' . $f->arrival_airport . ')' . $transitInfo;
                }
                $label .= implode(', ', $depLabels);
            }
            
            if ($returnFlights->count() > 0) {
                $label .= ' | Pulang: ';
                $retLabels = [];
                foreach ($returnFlights as $f) {
                    $transitInfo = $f->hasTransit() ? ' [' . $f->getFormattedTransitInfo() . ']' : '';
                    $retLabels[] = $f->airline_name . ' ' . $f->flight_number . 
                                  ' (' . $f->departure_airport . ' → ' . $f->arrival_airport . ')' . $transitInfo;
                }
                $label .= implode(', ', $retLabels);
            }

            $groups[] = [
                'code' => $groupCode,
                'label' => $label,
                'departure_flights' => $departureFlights->map(function($f) {
                    return [
                        'id' => $f->id,
                        'airline_name' => $f->airline_name,
                        'flight_number' => $f->flight_number,
                        'departure_airport' => $f->departure_airport,
                        'arrival_airport' => $f->arrival_airport,
                        'departure_time' => $f->departure_time ? $f->departure_time->format('Y-m-d H:i') : null,
                        'arrival_time' => $f->arrival_time ? $f->arrival_time->format('Y-m-d H:i') : null,
                        'transit_info' => $f->transit_info,
                        'has_transit' => $f->hasTransit(),
                        'formatted_transit' => $f->getFormattedTransitInfo(),
                    ];
                })->values(),
                'return_flights' => $returnFlights->map(function($f) {
                    return [
                        'id' => $f->id,
                        'airline_name' => $f->airline_name,
                        'flight_number' => $f->flight_number,
                        'departure_airport' => $f->departure_airport,
                        'arrival_airport' => $f->arrival_airport,
                        'departure_time' => $f->departure_time ? $f->departure_time->format('Y-m-d H:i') : null,
                        'arrival_time' => $f->arrival_time ? $f->arrival_time->format('Y-m-d H:i') : null,
                        'transit_info' => $f->transit_info,
                        'has_transit' => $f->hasTransit(),
                        'formatted_transit' => $f->getFormattedTransitInfo(),
                    ];
                })->values(),
                // For backward compatibility, include first flight IDs
                'departure_flight_id' => $departureFlights->first() ? $departureFlights->first()->id : null,
                'return_flight_id' => $returnFlights->first() ? $returnFlights->first()->id : null,
            ];
        }

        return response()->json($groups);
    }

    /**
     * Export package detail to PDF
     */
    public function exportPdf($id)
    {
        try {
            $package = TravelPackage::with([
                'outlet',
                'flightDeparture',
                'flightReturn',
                'hotelMakkah',
                'hotelRoomTypeMakkah',
                'hotelMadinah',
                'hotelRoomTypeMadinah',
                'hppCalculation'
            ])->find($id);
            
            if (!$package) {
                abort(404, 'Package not found');
            }

            $pdf = \PDF::loadView('admin.travel.package.package-detail-pdf', compact('package'));
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'Package_' . $package->package_code . '_' . date('YmdHis') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error exporting package PDF: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mengexport PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Info Paket form page
     */
    public function infoPaketForm($id)
    {
        $package = TravelPackage::with(['outlet', 'flightDeparture', 'flightReturn', 'hotelMakkah', 'hotelMadinah'])->findOrFail($id);
        $keberangkatan = \App\Models\Keberangkatan::with(['jamaahBookings', 'hotelBookings.hotel'])
            ->where('id_travel_package', $id)->first();

        if (!$keberangkatan) {
            return redirect()->route('admin.inventaris.travel.package.detail', $id)
                ->with('error', 'Belum ada keberangkatan. Buat keberangkatan terlebih dahulu.');
        }

        return view('admin.travel.package.info-paket-form', compact('package', 'keberangkatan'));
    }

    /**
     * Get Info Paket data (auto-fill or saved)
     */
    public function getInfoPaketData($id, $keberangkatanId)
    {
        $package = TravelPackage::with(['outlet', 'flightDeparture', 'flightReturn', 'hotelMakkah', 'hotelMadinah'])->findOrFail($id);
        $keberangkatan = \App\Models\Keberangkatan::with(['jamaahBookings', 'hotelBookings.hotel'])
            ->where('id', $keberangkatanId)->where('id_travel_package', $id)->firstOrFail();

        // Check if saved data exists
        $saved = \App\Models\InfoPaketData::where('id_travel_package', $id)
            ->where('id_keberangkatan', $keberangkatanId)->first();

        if ($saved) {
            return response()->json([
                'form' => [
                    'group_name' => $saved->group_name,
                    'tour_leader_name' => $saved->tour_leader_name,
                    'adult_count' => $saved->adult_count,
                    'child_count' => $saved->child_count,
                    'infant_count' => $saved->infant_count,
                    'rawdah_rows' => $saved->rawdah_rows ?? [],
                    'itinerary_rows' => $saved->itinerary_rows ?? [],
                ]
            ]);
        }

        // Auto-fill from package data
        $departureDate = $keberangkatan->departure_date;
        $returnDate = $keberangkatan->return_date;
        $rawdahDate = $departureDate ? $departureDate->copy()->addDay() : null;

        // Count pax including family members
        $adultCount = 0; $childCount = 0; $infantCount = 0;
        foreach ($keberangkatan->jamaahBookings as $booking) {
            $adultCount++;
            $fm = $booking->family_members_booking;
            if (is_string($fm)) $fm = json_decode($fm, true);
            if (is_array($fm)) {
                foreach ($fm as $member) {
                    if (!empty($member['tanggal_lahir'])) {
                        $age = \Carbon\Carbon::parse($member['tanggal_lahir'])->age;
                        if ($age < 2) $infantCount++;
                        elseif ($age <= 12) $childCount++;
                        else $adultCount++;
                    } else {
                        $adultCount++;
                    }
                }
            }
        }
        if ($keberangkatan->jamaahBookings->count() == 0 && $keberangkatan->total_jamaah > 0) {
            $adultCount = $keberangkatan->total_jamaah;
        }

        // Group name
        $groupName = $departureDate ? $departureDate->format('d') . ' ' . strtoupper($departureDate->translatedFormat('F')) . ' ' . $departureDate->format('Y') : '';

        // Rawdah rows auto-fill
        $rawdahDateStr = $rawdahDate ? $rawdahDate->format('d') . ' ' . strtoupper($rawdahDate->translatedFormat('F')) . ' ' . $rawdahDate->format('Y') : '';
        $rawdahRows = [
            ['activity' => 'RAWDAH FOR WOMEN', 'date' => $rawdahDateStr, 'time' => '07.00 WAS'],
            ['activity' => 'RAWDAH FOR MEN', 'date' => $rawdahDateStr, 'time' => '17.00 WAS'],
            ['activity' => 'UMRAH', 'date' => '', 'time' => ''],
        ];

        // Itinerary auto-fill from flight/hotel data
        $itineraryRows = [];
        $flightDep = $package->flightDeparture;
        $flightRet = $package->flightReturn;

        // Row 1: Airport to Hotel Madinah
        if ($flightDep) {
            $depDateStr = $departureDate ? $departureDate->format('d') . ' ' . strtoupper($departureDate->translatedFormat('F')) . ' ' . $departureDate->format('Y') : '';
            $arrTime = $flightDep->arrival_time ? $flightDep->arrival_time->format('H.i') . ' WAS' : '';
            $itineraryRows[] = [
                'from' => strtoupper($flightDep->arrival_airport ?? '') . ' AIRPORT',
                'to' => $package->hotelMadinah ? strtoupper($package->hotelMadinah->hotel_name) : 'HOTEL MADINAH',
                'date' => $depDateStr,
                'time' => $arrTime,
                'remark' => 'Landing ' . ($flightDep->arrival_time ? $flightDep->arrival_time->format('H.i') : '') . ' BAWA KOPER',
            ];
        }

        // Row last: Hotel Makkah to Airport (return)
        if ($flightRet && $returnDate) {
            $retDateStr = $returnDate->format('d') . ' ' . strtoupper($returnDate->translatedFormat('F')) . ' ' . $returnDate->format('Y');
            $depTime = $flightRet->departure_time ? $flightRet->departure_time->format('H.i') . ' WAS' : '';
            $itineraryRows[] = [
                'from' => $package->hotelMakkah ? strtoupper($package->hotelMakkah->hotel_name) : 'HOTEL MAKKAH',
                'to' => strtoupper($flightRet->departure_airport ?? '') . ' AIRPORT',
                'date' => $retDateStr,
                'time' => '',
                'remark' => 'Takeoff Pukul ' . ($flightRet->departure_time ? $flightRet->departure_time->format('H.i') . ' WAS' : ''),
            ];
        }

        return response()->json([
            'form' => [
                'group_name' => $groupName,
                'tour_leader_name' => $package->ustadz_name ?? '',
                'adult_count' => $adultCount,
                'child_count' => $childCount,
                'infant_count' => $infantCount,
                'rawdah_rows' => $rawdahRows,
                'itinerary_rows' => $itineraryRows,
            ]
        ]);
    }

    /**
     * Save Info Paket data
     */
    public function saveInfoPaketData(Request $request, $id, $keberangkatanId)
    {
        try {
            $data = \App\Models\InfoPaketData::updateOrCreate(
                ['id_travel_package' => $id, 'id_keberangkatan' => $keberangkatanId],
                [
                    'group_name' => $request->input('group_name'),
                    'tour_leader_name' => $request->input('tour_leader_name'),
                    'adult_count' => $request->input('adult_count', 0),
                    'child_count' => $request->input('child_count', 0),
                    'infant_count' => $request->input('infant_count', 0),
                    'itinerary_rows' => $request->input('itinerary_rows', []),
                    'rawdah_rows' => $request->input('rawdah_rows', []),
                ]
            );

            return response()->json(['success' => true, 'message' => 'Data info paket berhasil disimpan']);
        } catch (\Exception $e) {
            Log::error('Error saving info paket data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Stream Info Paket PDF for a specific keberangkatan
     */
    public function streamInfoPaket($id, $keberangkatanId)
    {
        try {
            $package = TravelPackage::with([
                'outlet',
                'flightDeparture',
                'flightReturn',
                'hotelMakkah',
                'hotelRoomTypeMakkah',
                'hotelMadinah',
                'hotelRoomTypeMadinah',
            ])->find($id);
            
            if (!$package) {
                abort(404, 'Package not found');
            }

            // If keberangkatanId is 0, pick the first keberangkatan for this package
            if ($keberangkatanId == 0) {
                $keberangkatan = \App\Models\Keberangkatan::with([
                    'travelPackage',
                    'jamaahBookings',
                    'hotelBookings.hotel',
                ])->where('id_travel_package', $id)->first();
            } else {
                $keberangkatan = \App\Models\Keberangkatan::with([
                    'travelPackage',
                    'jamaahBookings',
                    'hotelBookings.hotel',
                ])->where('id', $keberangkatanId)
                  ->where('id_travel_package', $id)
                  ->first();
            }

            if (!$keberangkatan) {
                abort(404, 'Keberangkatan not found. Silakan buat keberangkatan terlebih dahulu.');
            }

            // Load saved info paket data
            $infoPaketData = \App\Models\InfoPaketData::where('id_travel_package', $id)
                ->where('id_keberangkatan', $keberangkatan->id)->first();

            $pdf = \PDF::loadView('admin.travel.package.info-paket-pdf', compact('keberangkatan', 'infoPaketData'));
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'Info_Paket_' . $package->package_code . '_' . ($keberangkatan->departure_date ? $keberangkatan->departure_date->format('d_M_Y') : 'draft') . '.pdf';
            
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            Log::error('Error streaming info paket PDF: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal generate Info Paket PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tour plans for a package
     */
    public function getTourPlans($id)
    {
        $package = TravelPackage::findOrFail($id);
        $tourPlans = $package->tourPlans()->with('activities')->get();
        
        return response()->json($tourPlans);
    }

    /**
     * Save tour plans for a package
     */
    public function saveTourPlans(Request $request, $id)
    {
        $package = TravelPackage::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Delete existing tour plans
            $package->tourPlans()->delete();
            
            // Create new tour plans
            $tourPlansData = $request->input('tour_plans', []);
            
            foreach ($tourPlansData as $planData) {
                $tourPlan = $package->tourPlans()->create([
                    'day_number' => $planData['day_number'],
                    'day_title' => $planData['day_title'],
                    'day_date' => $planData['day_date'],
                    'description' => $planData['description'] ?? null,
                    'order' => $planData['order'] ?? 0
                ]);
                
                // Create activities for this day
                if (isset($planData['activities']) && is_array($planData['activities'])) {
                    foreach ($planData['activities'] as $activityData) {
                        $tourPlan->activities()->create([
                            'activity_time' => $activityData['activity_time'],
                            'activity_title' => $activityData['activity_title'],
                            'activity_description' => $activityData['activity_description'] ?? null,
                            'order' => $activityData['order'] ?? 0
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Tour plan berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving tour plans: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan tour plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update handling & lounge fee settings for a package
     */
    public function updateHandlingFee(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'include_handling_lounge_fee' => 'required|boolean',
                'handling_lounge_fee_amount' => 'nullable|numeric|min:0',
                'handling_lounge_fee_description' => 'nullable|string|max:500',
            ]);

            $package = TravelPackage::findOrFail($id);

            // Check outlet access
            if (Auth::user()->id_outlet && $package->id_outlet != Auth::user()->id_outlet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this package'
                ], 403);
            }

            // Update handling fee settings
            $package->update([
                'include_handling_lounge_fee' => $validated['include_handling_lounge_fee'],
                'handling_lounge_fee_amount' => $validated['handling_lounge_fee_amount'] ?? 500000,
                'handling_lounge_fee_description' => $validated['handling_lounge_fee_description'] ?? 'Handling & Lounge Fee Wajib',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan handling fee berhasil diperbarui',
                'data' => [
                    'include_handling_lounge_fee' => $package->include_handling_lounge_fee,
                    'handling_lounge_fee_amount' => $package->handling_lounge_fee_amount,
                    'handling_lounge_fee_description' => $package->handling_lounge_fee_description,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to update handling fee: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pengaturan handling fee: ' . $e->getMessage()
            ], 500);
        }
    }
}
