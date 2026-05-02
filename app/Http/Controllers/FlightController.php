<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flight;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasOutletFilter;

class FlightController extends Controller
{
    use HasOutletFilter;
    
    public function __construct()
    {
        $this->middleware('permission:master.flight.view')->only(['index', 'getData', 'show']);
        $this->middleware('permission:master.flight.create')->only(['store']);
        $this->middleware('permission:master.flight.edit')->only(['update']);
        $this->middleware('permission:master.flight.delete')->only(['destroy']);
    }

    /**
     * Display flight management page
     */
    public function index()
    {
        Log::info('Loading Flight Index Page');
        return view('admin.inventaris.flight.index');
    }

    /**
     * Get flight data for DataTables
     */
    public function getData(Request $request)
    {
        Log::info('Fetching Flight Data with filters', $request->all());

        $query = Flight::with('outlet');

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

        // Filter by airline
        if ($request->has('airline_filter') && $request->airline_filter !== 'ALL') {
            $query->where('airline_name', $request->airline_filter);
        }

        // Filter by route
        if ($request->has('route_filter') && $request->route_filter) {
            $query->where(function($q) use ($request) {
                $q->where('departure_airport', 'like', "%{$request->route_filter}%")
                  ->orWhere('arrival_airport', 'like', "%{$request->route_filter}%");
            });
        }

        // Sorting
        $sortColumn = $request->sort_key ?? 'id';
        $sortDirection = $request->sort_dir ?? 'desc';
        
        $columnMapping = [
            'airline' => 'airline_name',
            'flight_number' => 'flight_number',
            'departure' => 'departure_time',
            'capacity' => 'capacity'
        ];
        
        $sortColumn = $columnMapping[$sortColumn] ?? $sortColumn;
        $query->orderBy($sortColumn, $sortDirection);

        $flights = $query->get();

        $data = $flights->map(function ($flight) {
            return [
                'id' => $flight->id,
                'airline_name' => $flight->airline_name,
                'flight_number' => $flight->flight_number,
                'flight_group_code' => $flight->flight_group_code,
                'flight_direction' => $flight->flight_direction,
                'route' => $flight->departure_airport . ' → ' . $flight->arrival_airport,
                'departure_airport' => $flight->departure_airport,
                'arrival_airport' => $flight->arrival_airport,
                'departure_time' => $flight->departure_time ? $flight->departure_time->format('Y-m-d H:i') : '-',
                'arrival_time' => $flight->arrival_time ? $flight->arrival_time->format('Y-m-d H:i') : '-',
                'transit_info' => $flight->transit_info,
                'has_transit' => $flight->hasTransit(),
                'formatted_transit' => $flight->getFormattedTransitInfo(),
                'capacity' => $flight->capacity,
                'available_seats' => $flight->getAvailableSeats(),
                'aircraft_type' => $flight->aircraft_type ?? '-',
                'price_per_person' => 'Rp ' . number_format($flight->price_per_person, 0, ',', '.'),
                'price_per_person_raw' => $flight->price_per_person,
                'seller_name' => $flight->seller_name ?? '-',
                'seller_phone' => $flight->seller_phone ?? '-',
                'id_outlet' => $flight->id_outlet,
                'outlet_name' => $flight->outlet ? $flight->outlet->nama_outlet : '-',
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Store a new flight
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'airline_name' => 'required|string|max:255',
            'flight_number' => 'required|string|max:255',
            'departure_airport' => 'required|string|max:255',
            'arrival_airport' => 'required|string|max:255',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'transit_info' => 'nullable|array',
            'transit_info.*.airport' => 'required_with:transit_info|string|max:255',
            'transit_info.*.arrival_time' => 'nullable|string',
            'transit_info.*.departure_time' => 'nullable|string',
            'transit_info.*.duration_minutes' => 'nullable|integer|min:0',
            'capacity' => 'nullable|integer|min:1',
            'aircraft_type' => 'nullable|string|max:255',
            'price_per_person' => 'required|numeric|min:0',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ]
        ], [
            'capacity.min' => 'Kapasitas harus berupa bilangan positif',
            'arrival_time.after' => 'Waktu kedatangan harus setelah waktu keberangkatan',
            'price_per_person.required' => 'Biaya per orang harus diisi',
            'price_per_person.min' => 'Biaya per orang harus berupa bilangan positif'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->all();
            // Pastikan capacity punya nilai default jika tidak diisi
            if (empty($data['capacity'])) {
                $data['capacity'] = 0;
            }
            
            // Handle transit_info - remove empty transits
            if (isset($data['transit_info']) && is_array($data['transit_info'])) {
                $data['transit_info'] = array_filter($data['transit_info'], function($transit) {
                    return !empty($transit['airport']);
                });
                // Re-index array
                $data['transit_info'] = array_values($data['transit_info']);
            }
            
            $flight = Flight::create($data);

            Log::info('Flight created successfully', ['flight_id' => $flight->id]);

            return response()->json([
                'message' => 'Data penerbangan berhasil disimpan',
                'data' => $flight
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error creating flight: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menyimpan data penerbangan'
            ], 500);
        }
    }

    /**
     * Show a specific flight
     */
    public function show($id)
    {
        $flight = Flight::with('outlet')->find($id);
        
        if (!$flight) {
            return response()->json(['error' => 'Penerbangan tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $flight->id,
            'airline_name' => $flight->airline_name,
            'flight_number' => $flight->flight_number,
            'flight_group_code' => $flight->flight_group_code,
            'flight_direction' => $flight->flight_direction,
            'departure_airport' => $flight->departure_airport,
            'arrival_airport' => $flight->arrival_airport,
            'departure_time' => $flight->departure_time ? $flight->departure_time->format('Y-m-d\TH:i') : null,
            'arrival_time' => $flight->arrival_time ? $flight->arrival_time->format('Y-m-d\TH:i') : null,
            'transit_info' => $flight->transit_info ?? [],
            'has_transit' => $flight->hasTransit(),
            'formatted_transit' => $flight->getFormattedTransitInfo(),
            'capacity' => $flight->capacity,
            'aircraft_type' => $flight->aircraft_type,
            'price_per_person' => $flight->price_per_person,
            'seller_name' => $flight->seller_name,
            'seller_phone' => $flight->seller_phone,
            'id_outlet' => $flight->id_outlet,
            'outlet' => $flight->outlet,
            'available_seats' => $flight->getAvailableSeats()
        ]);
    }

    /**
     * Update a flight
     */
    public function update(Request $request, $id)
    {
        $flight = Flight::find($id);
        
        if (!$flight) {
            return response()->json(['error' => 'Penerbangan tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'airline_name' => 'required|string|max:255',
            'flight_number' => 'required|string|max:255',
            'departure_airport' => 'required|string|max:255',
            'arrival_airport' => 'required|string|max:255',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'transit_info' => 'nullable|array',
            'transit_info.*.airport' => 'required_with:transit_info|string|max:255',
            'transit_info.*.arrival_time' => 'nullable|string',
            'transit_info.*.departure_time' => 'nullable|string',
            'transit_info.*.duration_minutes' => 'nullable|integer|min:0',
            'capacity' => 'nullable|integer|min:1',
            'aircraft_type' => 'nullable|string|max:255',
            'price_per_person' => 'required|numeric|min:0',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ]
        ], [
            'capacity.min' => 'Kapasitas harus berupa bilangan positif',
            'arrival_time.after' => 'Waktu kedatangan harus setelah waktu keberangkatan',
            'price_per_person.required' => 'Biaya per orang harus diisi',
            'price_per_person.min' => 'Biaya per orang harus berupa bilangan positif'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->all();
            if (empty($data['capacity'])) {
                $data['capacity'] = 0;
            }
            
            // Handle transit_info - remove empty transits
            if (isset($data['transit_info']) && is_array($data['transit_info'])) {
                $data['transit_info'] = array_filter($data['transit_info'], function($transit) {
                    return !empty($transit['airport']);
                });
                // Re-index array
                $data['transit_info'] = array_values($data['transit_info']);
            }
            
            $flight->update($data);

            Log::info('Flight updated successfully', ['flight_id' => $flight->id]);

            return response()->json([
                'message' => 'Data penerbangan berhasil diupdate',
                'data' => $flight
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating flight: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mengupdate data penerbangan'
            ], 500);
        }
    }

    /**
     * Delete a flight
     */
    public function destroy($id)
    {
        $flight = Flight::find($id);
        
        if (!$flight) {
            return response()->json(['error' => 'Penerbangan tidak ditemukan'], 404);
        }

        // Check if flight has active bookings
        $activeBookings = $flight->bookings()
            ->whereIn('status', ['confirmed', 'ticketed'])
            ->count();

        if ($activeBookings > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus penerbangan: terdapat booking aktif',
                'code' => 'ACTIVE_BOOKINGS_EXIST'
            ], 400);
        }

        try {
            $flight->delete();

            Log::info('Flight deleted successfully', ['flight_id' => $id]);

            return response()->json([
                'message' => 'Data penerbangan berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting flight: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menghapus data penerbangan'
            ], 500);
        }
    }

    /**
     * Get unique airlines for filter
     */
    public function getAirlines()
    {
        $airlines = Flight::select('airline_name')
            ->whereNotNull('airline_name')
            ->where('airline_name', '!=', '')
            ->distinct()
            ->orderBy('airline_name')
            ->pluck('airline_name');

        return response()->json($airlines);
    }
}

