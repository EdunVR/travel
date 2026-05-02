<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlightGroup;
use App\Models\FlightGroupItem;
use App\Models\Flight;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FlightGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:master.flight.view')->only(['index', 'getData', 'show']);
        $this->middleware('permission:master.flight.create')->only(['store']);
        $this->middleware('permission:master.flight.edit')->only(['update']);
        $this->middleware('permission:master.flight.delete')->only(['destroy']);
    }

    /**
     * Get flight groups data
     */
    public function getData(Request $request)
    {
        Log::info('Fetching Flight Groups Data');

        $query = FlightGroup::with(['outlet', 'items.flight']);

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
            $query->where('group_name', 'like', "%{$request->search}%");
        }

        $groups = $query->orderBy('created_at', 'desc')->get();

        $data = $groups->map(function ($group) {
            $departure = $group->getDepartureFlight();
            $return = $group->getReturnFlight();
            
            return [
                'id' => $group->id,
                'group_name' => $group->group_name,
                'description' => $group->description,
                'route' => $group->getFormattedRoute(),
                'departure_flight' => $departure ? [
                    'id' => $departure->id,
                    'airline' => $departure->airline_name,
                    'flight_number' => $departure->flight_number,
                    'route' => $departure->departure_airport . ' → ' . $departure->arrival_airport,
                    'departure_time' => $departure->departure_time ? $departure->departure_time->format('d/m/Y H:i') : '-',
                ] : null,
                'return_flight' => $return ? [
                    'id' => $return->id,
                    'airline' => $return->airline_name,
                    'flight_number' => $return->flight_number,
                    'route' => $return->departure_airport . ' → ' . $return->arrival_airport,
                    'departure_time' => $return->departure_time ? $return->departure_time->format('d/m/Y H:i') : '-',
                ] : null,
                'flight_count' => $group->items->count(),
                'id_outlet' => $group->id_outlet,
                'outlet_name' => $group->outlet ? $group->outlet->nama_outlet : '-',
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Store a new flight group
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'id_outlet' => 'required|exists:outlets,id_outlet',
            'flights' => 'required|array|min:1',
            'flights.*.id_flight' => 'required|exists:flights,id',
            'flights.*.flight_type' => 'required|in:departure,return,transit',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $group = FlightGroup::create([
                'group_name' => $request->group_name,
                'description' => $request->description,
                'id_outlet' => $request->id_outlet,
            ]);

            // Add flights to group
            foreach ($request->flights as $index => $flightData) {
                FlightGroupItem::create([
                    'id_flight_group' => $group->id,
                    'id_flight' => $flightData['id_flight'],
                    'flight_type' => $flightData['flight_type'],
                    'sequence' => $index,
                ]);
            }

            DB::commit();

            Log::info('Flight group created successfully', ['group_id' => $group->id]);

            return response()->json([
                'message' => 'Group penerbangan berhasil dibuat',
                'data' => $group
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating flight group: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal membuat group penerbangan'
            ], 500);
        }
    }

    /**
     * Show a specific flight group
     */
    public function show($id)
    {
        $group = FlightGroup::with(['outlet', 'items.flight'])->find($id);
        
        if (!$group) {
            return response()->json(['error' => 'Group penerbangan tidak ditemukan'], 404);
        }

        $flights = $group->items->map(function($item) {
            return [
                'id_flight' => $item->id_flight,
                'flight_type' => $item->flight_type,
                'sequence' => $item->sequence,
                'flight' => [
                    'id' => $item->flight->id,
                    'airline_name' => $item->flight->airline_name,
                    'flight_number' => $item->flight->flight_number,
                    'route' => $item->flight->departure_airport . ' → ' . $item->flight->arrival_airport,
                    'departure_time' => $item->flight->departure_time ? $item->flight->departure_time->format('Y-m-d\TH:i') : null,
                ]
            ];
        });

        return response()->json([
            'id' => $group->id,
            'group_name' => $group->group_name,
            'description' => $group->description,
            'id_outlet' => $group->id_outlet,
            'flights' => $flights
        ]);
    }

    /**
     * Update a flight group
     */
    public function update(Request $request, $id)
    {
        $group = FlightGroup::find($id);
        
        if (!$group) {
            return response()->json(['error' => 'Group penerbangan tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'id_outlet' => 'required|exists:outlets,id_outlet',
            'flights' => 'required|array|min:1',
            'flights.*.id_flight' => 'required|exists:flights,id',
            'flights.*.flight_type' => 'required|in:departure,return,transit',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $group->update([
                'group_name' => $request->group_name,
                'description' => $request->description,
                'id_outlet' => $request->id_outlet,
            ]);

            // Delete existing items
            $group->items()->delete();

            // Add new flights
            foreach ($request->flights as $index => $flightData) {
                FlightGroupItem::create([
                    'id_flight_group' => $group->id,
                    'id_flight' => $flightData['id_flight'],
                    'flight_type' => $flightData['flight_type'],
                    'sequence' => $index,
                ]);
            }

            DB::commit();

            Log::info('Flight group updated successfully', ['group_id' => $group->id]);

            return response()->json([
                'message' => 'Group penerbangan berhasil diupdate',
                'data' => $group
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating flight group: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mengupdate group penerbangan'
            ], 500);
        }
    }

    /**
     * Delete a flight group
     */
    public function destroy($id)
    {
        $group = FlightGroup::find($id);
        
        if (!$group) {
            return response()->json(['error' => 'Group penerbangan tidak ditemukan'], 404);
        }

        try {
            $group->delete();

            Log::info('Flight group deleted successfully', ['group_id' => $id]);

            return response()->json([
                'message' => 'Group penerbangan berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting flight group: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal menghapus group penerbangan'
            ], 500);
        }
    }
}
