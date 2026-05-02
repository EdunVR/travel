<?php

namespace App\Http\Controllers;

use App\Models\FlightBooking;
use App\Models\Flight;
use App\Models\Keberangkatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Traits\HasOutletFilter;

class FlightBookingController extends Controller
{
    use HasOutletFilter;
    
    public function __construct()
    {
        $this->middleware('permission:travel.flight-booking.view')->only(['index', 'show']);
        $this->middleware('permission:travel.flight-booking.create')->only(['store']);
        $this->middleware('permission:travel.flight-booking.update')->only(['update', 'uploadTicket', 'updateStatus']);
        $this->middleware('permission:travel.flight-booking.delete')->only(['destroy']);
    }

    /**
     * Store a new flight booking for a keberangkatan
     * Requirements: 7.1, 7.2, 7.3
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_keberangkatan' => 'required|exists:keberangkatan,id',
            'id_flight' => 'required|exists:flights,id',
            'seat_count' => 'required|integer|min:1',
            'booking_reference' => 'nullable|string|max:100',
            'confirmation_code' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $keberangkatan = Keberangkatan::findOrFail($request->id_keberangkatan);
            $flight = Flight::findOrFail($request->id_flight);

            // Validate capacity (Requirement 7.3)
            $existingBookings = FlightBooking::where('id_flight', $flight->id)
                ->whereHas('keberangkatan', function($q) {
                    $q->whereNotIn('status', ['cancelled']);
                })
                ->sum('seat_count');

            $availableSeats = $flight->capacity - $existingBookings;

            if ($request->seat_count > $availableSeats) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot book {$request->seat_count} seats. Only {$availableSeats} seats available.",
                    'code' => 'INSUFFICIENT_CAPACITY'
                ], 400);
            }

            // Create flight booking
            $booking = FlightBooking::create([
                'id_keberangkatan' => $request->id_keberangkatan,
                'id_flight' => $request->id_flight,
                'seat_count' => $request->seat_count,
                'booking_reference' => $request->booking_reference,
                'confirmation_code' => $request->confirmation_code,
                'status' => 'pending',
                'booking_date' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Flight booking created successfully',
                'data' => $booking->load(['flight', 'keberangkatan'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create flight booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update flight booking status
     * Requirements: 7.4, 7.5
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,ticketed,cancelled',
            'confirmation_code' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $booking = FlightBooking::findOrFail($id);

            $updateData = [
                'status' => $request->status,
            ];

            if ($request->filled('confirmation_code')) {
                $updateData['confirmation_code'] = $request->confirmation_code;
            }

            if ($request->status === 'confirmed') {
                $updateData['confirmed_at'] = now();
            }

            $booking->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Flight booking status updated successfully',
                'data' => $booking->load(['flight', 'keberangkatan'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update booking status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload ticket document
     * Requirements: 7.6
     */
    public function uploadTicket(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ticket_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $booking = FlightBooking::findOrFail($id);

            // Delete old ticket document if exists
            if ($booking->ticket_document_path && Storage::disk('public')->exists($booking->ticket_document_path)) {
                Storage::disk('public')->delete($booking->ticket_document_path);
            }

            // Store new ticket document
            $file = $request->file('ticket_document');
            $filename = 'flight_ticket_' . $booking->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('flight_tickets', $filename, 'public');

            $booking->update([
                'ticket_document_path' => $path,
                'status' => 'ticketed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ticket document uploaded successfully',
                'data' => $booking->load(['flight', 'keberangkatan'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload ticket document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get flight bookings for a keberangkatan
     */
    public function getByKeberangkatan($keberangkatanId)
    {
        try {
            $bookings = FlightBooking::with(['flight', 'keberangkatan'])
                ->where('id_keberangkatan', $keberangkatanId)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve flight bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a flight booking
     */
    public function destroy($id)
    {
        try {
            $booking = FlightBooking::findOrFail($id);

            // Delete ticket document if exists
            if ($booking->ticket_document_path && Storage::disk('public')->exists($booking->ticket_document_path)) {
                Storage::disk('public')->delete($booking->ticket_document_path);
            }

            $booking->delete();

            return response()->json([
                'success' => true,
                'message' => 'Flight booking deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete flight booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available flights for booking
     */
    public function getAvailableFlights(Request $request)
    {
        try {
            $query = Flight::query();

            // Filter by outlet if user has outlet restriction
            if (Auth::user()->id_outlet) {
                $query->where('id_outlet', Auth::user()->id_outlet);
            }

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('airline_name', 'like', "%{$search}%")
                      ->orWhere('flight_number', 'like', "%{$search}%")
                      ->orWhere('departure_airport', 'like', "%{$search}%")
                      ->orWhere('arrival_airport', 'like', "%{$search}%");
                });
            }

            $flights = $query->get()->map(function($flight) {
                return [
                    'id' => $flight->id,
                    'text' => "{$flight->airline_name} - {$flight->flight_number} ({$flight->departure_airport} → {$flight->arrival_airport})",
                    'airline_name' => $flight->airline_name,
                    'flight_number' => $flight->flight_number,
                    'route' => "{$flight->departure_airport} → {$flight->arrival_airport}",
                    'capacity' => $flight->capacity,
                    'available_seats' => $flight->getAvailableSeats(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $flights
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available flights: ' . $e->getMessage()
            ], 500);
        }
    }
}
