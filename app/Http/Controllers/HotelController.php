<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\HotelRoomType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\HasOutletFilter;

class HotelController extends Controller
{
    use HasOutletFilter;
    
    public function __construct()
    {
        $this->middleware('permission:master.hotel.view')->only(['index', 'getData', 'show']);
        $this->middleware('permission:master.hotel.create')->only(['store']);
        $this->middleware('permission:master.hotel.edit')->only(['update']);
        $this->middleware('permission:master.hotel.delete')->only(['destroy']);
    }

    /**
     * Display hotel management page
     */
    public function index()
    {
        Log::info('Loading Hotel Index Page');
        return view('admin.inventaris.hotel.index');
    }

    /**
     * Get hotel data for DataTables
     */
    public function getData(Request $request)
    {
        Log::info('Fetching Hotel Data with filters', $request->all());

        $query = Hotel::with(['outlet', 'roomTypes']);

        // Get user's outlet filter
        $user = Auth::user();
        $userOutlets = [];
        
        if ($user && method_exists($user, 'outlets')) {
            $userOutlets = $user->outlets->pluck('id_outlet')->toArray();
            if (!empty($userOutlets)) {
                $query->whereIn('id_outlet', $userOutlets);
            }
        }

        // Filter by outlet
        if ($request->has('outlet_filter') && $request->outlet_filter !== 'ALL') {
            $query->where('id_outlet', $request->outlet_filter);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by city
        if ($request->has('city_filter') && $request->city_filter !== 'ALL') {
            $query->byCity($request->city_filter);
        }

        // Filter by star rating
        if ($request->has('star_filter') && $request->star_filter !== 'ALL') {
            $query->byStarRating($request->star_filter);
        }

        // Sorting
        $sortColumn = $request->sort_key ?? 'hotel_name';
        $sortDirection = $request->sort_dir ?? 'asc';
        
        $columnMapping = [
            'hotel_name' => 'hotel_name',
            'city' => 'city',
            'star_rating' => 'star_rating',
            'total_rooms' => 'total_rooms'
        ];
        
        $sortColumn = $columnMapping[$sortColumn] ?? 'hotel_name';
        $query->orderBy($sortColumn, $sortDirection);

        $hotels = $query->get();

        // Transform data for client-side rendering
        $data = $hotels->map(function ($hotel) {
            return [
                'id' => $hotel->id,
                'hotel_name' => $hotel->hotel_name,
                'location' => $hotel->location,
                'city_country' => $hotel->city . ', ' . $hotel->country,
                'star_rating' => $hotel->star_rating ? str_repeat('⭐', $hotel->star_rating) : '-',
                'total_rooms' => $hotel->total_rooms,
                'room_types_count' => $hotel->roomTypes->count(),
                'id_outlet' => $hotel->id_outlet,
                'outlet_name' => $hotel->outlet ? $hotel->outlet->nama_outlet : '-',
            ];
        });

        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Get user accessible outlets
     */
    public function getUserOutlets()
    {
        $user = Auth::user();
        
        if ($user && method_exists($user, 'outlets')) {
            $outlets = $user->outlets->map(function($outlet) {
                return [
                    'id_outlet' => $outlet->id_outlet,
                    'nama_outlet' => $outlet->nama_outlet
                ];
            });
            
            return response()->json($outlets);
        }
        
        return response()->json([]);
    }

    /**
     * Store a new hotel
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'star_rating' => 'nullable|integer|min:1|max:5',
            'total_rooms' => 'required|integer|min:1',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ]
        ], [
            'total_rooms.min' => 'Total kamar harus berupa bilangan positif',
            'star_rating.min' => 'Rating bintang minimal 1',
            'star_rating.max' => 'Rating bintang maksimal 5'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $hotel = Hotel::create($request->all());

            Log::info('Hotel created successfully', ['hotel_id' => $hotel->id]);

            return response()->json([
                'message' => 'Data hotel berhasil disimpan',
                'data' => $hotel
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error creating hotel: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menyimpan data hotel'
            ], 500);
        }
    }

    /**
     * Show a specific hotel
     */
    public function show($id)
    {
        $hotel = Hotel::with(['outlet', 'roomTypes'])->find($id);
        
        if (!$hotel) {
            return response()->json(['error' => 'Hotel tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $hotel->id,
            'hotel_name' => $hotel->hotel_name,
            'location' => $hotel->location,
            'city' => $hotel->city,
            'country' => $hotel->country,
            'star_rating' => $hotel->star_rating,
            'total_rooms' => $hotel->total_rooms,
            'contact_person' => $hotel->contact_person,
            'phone' => $hotel->phone,
            'email' => $hotel->email,
            'address' => $hotel->address,
            'seller_name' => $hotel->seller_name,
            'seller_phone' => $hotel->seller_phone,
            'id_outlet' => $hotel->id_outlet,
            'room_types' => $hotel->roomTypes
        ]);
    }

    /**
     * Update a hotel
     */
    public function update(Request $request, $id)
    {
        $hotel = Hotel::find($id);
        
        if (!$hotel) {
            return response()->json(['error' => 'Hotel tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'hotel_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'star_rating' => 'nullable|integer|min:1|max:5',
            'total_rooms' => 'required|integer|min:1',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ]
        ], [
            'total_rooms.min' => 'Total kamar harus berupa bilangan positif',
            'star_rating.min' => 'Rating bintang minimal 1',
            'star_rating.max' => 'Rating bintang maksimal 5'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $hotel->update($request->all());

            Log::info('Hotel updated successfully', ['hotel_id' => $hotel->id]);

            return response()->json([
                'message' => 'Data hotel berhasil diupdate',
                'data' => $hotel
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating hotel: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mengupdate data hotel'
            ], 500);
        }
    }

    /**
     * Delete a hotel
     */
    public function destroy($id)
    {
        $hotel = Hotel::find($id);
        
        if (!$hotel) {
            return response()->json(['error' => 'Hotel tidak ditemukan'], 404);
        }

        // Check if hotel has active bookings
        $activeBookings = $hotel->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        if ($activeBookings > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus hotel: terdapat booking aktif',
                'code' => 'ACTIVE_BOOKINGS_EXIST'
            ], 400);
        }

        try {
            // Delete room types first (cascade should handle this, but being explicit)
            $hotel->roomTypes()->delete();
            
            $hotel->delete();

            Log::info('Hotel deleted successfully', ['hotel_id' => $id]);

            return response()->json([
                'message' => 'Data hotel berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting hotel: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menghapus data hotel'
            ], 500);
        }
    }

    /**
     * Get unique cities for filter
     */
    public function getCities()
    {
        $cities = Hotel::select('city')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return response()->json($cities);
    }

    /**
     * Get room types for a specific hotel
     */
    public function getRoomTypes($hotelId)
    {
        try {
            $hotel = Hotel::with('roomTypes')->findOrFail($hotelId);
            
            $roomTypes = $hotel->roomTypes->map(function($roomType) {
                return [
                    'id' => $roomType->id,
                    'room_type_name' => $roomType->room_type_name,
                    'capacity' => $roomType->capacity,
                    'price_per_night' => $roomType->price_per_night,
                    'description' => $roomType->description ?? ''
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $roomTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel tidak ditemukan',
                'data' => []
            ], 404);
        }
    }

    /**
     * Store a new room type for a hotel
     */
    public function storeRoomType(Request $request, $hotelId)
    {
        $hotel = Hotel::find($hotelId);
        
        if (!$hotel) {
            return response()->json(['error' => 'Hotel tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'room_type_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'total_rooms' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0'
        ], [
            'capacity.min' => 'Kapasitas harus berupa bilangan positif',
            'total_rooms.min' => 'Total kamar harus berupa bilangan positif',
            'price_per_night.min' => 'Harga tidak boleh negatif'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $roomType = HotelRoomType::create([
                'id_hotel' => $hotelId,
                'room_type_name' => $request->room_type_name,
                'capacity' => $request->capacity,
                'total_rooms' => $request->total_rooms,
                'price_per_night' => $request->price_per_night
            ]);

            Log::info('Room type created successfully', ['room_type_id' => $roomType->id]);

            return response()->json([
                'message' => 'Tipe kamar berhasil disimpan',
                'data' => $roomType
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error creating room type: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menyimpan tipe kamar'
            ], 500);
        }
    }

    /**
     * Update a room type
     */
    public function updateRoomType(Request $request, $hotelId, $roomTypeId)
    {
        $roomType = HotelRoomType::where('id_hotel', $hotelId)->find($roomTypeId);
        
        if (!$roomType) {
            return response()->json(['error' => 'Tipe kamar tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'room_type_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'total_rooms' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0'
        ], [
            'capacity.min' => 'Kapasitas harus berupa bilangan positif',
            'total_rooms.min' => 'Total kamar harus berupa bilangan positif',
            'price_per_night.min' => 'Harga tidak boleh negatif'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $roomType->update($request->all());

            Log::info('Room type updated successfully', ['room_type_id' => $roomType->id]);

            return response()->json([
                'message' => 'Tipe kamar berhasil diupdate',
                'data' => $roomType
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating room type: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mengupdate tipe kamar'
            ], 500);
        }
    }

    /**
     * Delete a room type
     */
    public function destroyRoomType($hotelId, $roomTypeId)
    {
        $roomType = HotelRoomType::where('id_hotel', $hotelId)->find($roomTypeId);
        
        if (!$roomType) {
            return response()->json(['error' => 'Tipe kamar tidak ditemukan'], 404);
        }

        try {
            $roomType->delete();

            Log::info('Room type deleted successfully', ['room_type_id' => $roomTypeId]);

            return response()->json([
                'message' => 'Tipe kamar berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting room type: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menghapus tipe kamar'
            ], 500);
        }
    }
}
