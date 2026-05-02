<?php

namespace App\Http\Controllers;

use App\Models\HotelBooking;
use App\Models\HotelRoomAssignment;
use App\Models\Hotel;
use App\Models\Keberangkatan;
use App\Models\JamaahBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\HasOutletFilter;

class HotelBookingController extends Controller
{
    use HasOutletFilter;
    
    public function __construct()
    {
        $this->middleware('permission:travel.hotel-booking.view')->only(['index', 'show', 'getRoomlist']);
        $this->middleware('permission:travel.hotel-booking.create')->only(['store']);
        $this->middleware('permission:travel.hotel-booking.update')->only(['update', 'assignRoom']);
        $this->middleware('permission:travel.hotel-booking.delete')->only(['destroy']);
    }

    /**
     * Store a new hotel booking for a keberangkatan
     * Requirements: 10.9
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_keberangkatan' => 'required|exists:keberangkatan,id',
            'id_hotel' => 'required|exists:hotels,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_count' => 'required|integer|min:1',
            'booking_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
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
            $hotel = Hotel::findOrFail($request->id_hotel);

            // Validate capacity (Requirement 17.6)
            $existingBookings = HotelBooking::where('id_hotel', $hotel->id)
                ->where(function($q) use ($request) {
                    $q->whereBetween('check_in_date', [$request->check_in_date, $request->check_out_date])
                      ->orWhereBetween('check_out_date', [$request->check_in_date, $request->check_out_date])
                      ->orWhere(function($q2) use ($request) {
                          $q2->where('check_in_date', '<=', $request->check_in_date)
                             ->where('check_out_date', '>=', $request->check_out_date);
                      });
                })
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->sum('room_count');

            $availableRooms = $hotel->total_rooms - $existingBookings;

            if ($request->room_count > $availableRooms) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot book {$request->room_count} rooms. Only {$availableRooms} rooms available for the selected dates.",
                    'code' => 'INSUFFICIENT_CAPACITY'
                ], 400);
            }

            // Create hotel booking
            $booking = HotelBooking::create([
                'id_keberangkatan'  => $request->id_keberangkatan,
                'id_hotel'          => $request->id_hotel,
                'check_in_date'     => $request->check_in_date,
                'check_out_date'    => $request->check_out_date,
                'room_count'        => $request->room_count,
                'room_type'         => $request->room_type,
                'seller_name'       => $request->seller_name,
                'seller_phone'      => $request->seller_phone,
                'booking_reference' => $request->booking_reference,
                'status'            => 'pending',
                'notes'             => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hotel booking created successfully',
                'data' => $booking->load(['hotel', 'keberangkatan'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create hotel booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update hotel booking status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
            'booking_reference' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $booking = HotelBooking::findOrFail($id);

            $updateData = [
                'status' => $request->status,
            ];

            if ($request->filled('booking_reference')) {
                $updateData['booking_reference'] = $request->booking_reference;
            }

            $booking->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Hotel booking status updated successfully',
                'data' => $booking->load(['hotel', 'keberangkatan'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update booking status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign jamaah to rooms
     * Requirements: 10.9
     */
    public function assignRooms(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'assignments' => 'required|array',
            'assignments.*.id_jamaah_booking' => 'required|exists:jamaah_bookings,id',
            'assignments.*.room_number' => 'required|string|max:50',
            'assignments.*.room_type' => 'nullable|string|max:50',
            'assignments.*.bed_number' => 'nullable|integer',
            'assignments.*.notes' => 'nullable|string',
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

            $booking = HotelBooking::findOrFail($id);

            // Validate that all jamaah belong to the same keberangkatan
            $jamaahBookingIds = collect($request->assignments)->pluck('id_jamaah_booking');
            $invalidJamaah = JamaahBooking::whereIn('id', $jamaahBookingIds)
                ->where('id_keberangkatan', '!=', $booking->id_keberangkatan)
                ->count();

            if ($invalidJamaah > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'All jamaah must belong to the same keberangkatan as the hotel booking',
                    'code' => 'INVALID_JAMAAH'
                ], 400);
            }

            // Create or update room assignments
            foreach ($request->assignments as $assignment) {
                HotelRoomAssignment::updateOrCreate(
                    [
                        'id_hotel_booking' => $booking->id,
                        'id_jamaah_booking' => $assignment['id_jamaah_booking'],
                    ],
                    [
                        'room_number' => $assignment['room_number'],
                        'room_type' => $assignment['room_type'] ?? null,
                        'bed_number' => $assignment['bed_number'] ?? null,
                        'notes' => $assignment['notes'] ?? null,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Room assignments saved successfully',
                'data' => $booking->load(['roomAssignments.jamaahBooking.jamaah'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign rooms: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove room assignment
     */
    public function removeAssignment($bookingId, $assignmentId)
    {
        try {
            $assignment = HotelRoomAssignment::where('id', $assignmentId)
                ->where('id_hotel_booking', $bookingId)
                ->firstOrFail();

            $assignment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Room assignment removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove room assignment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate roomlist report
     * Requirements: 10.9
     */
    public function generateRoomlist($id)
    {
        try {
            $booking = HotelBooking::with([
                'hotel',
                'keberangkatan.travelPackage',
                'roomAssignments' => function($q) {
                    $q->orderBy('room_number')->orderBy('bed_number');
                },
                'roomAssignments.jamaahBooking.jamaah'
            ])->findOrFail($id);

            // Group assignments by room number
            $roomsGrouped = $booking->roomAssignments->groupBy('room_number');

            $pdf = Pdf::loadView('admin.travel.hotel-booking.roomlist-pdf', [
                'booking' => $booking,
                'roomsGrouped' => $roomsGrouped,
                'generatedAt' => now(),
            ]);

            return $pdf->download("roomlist_{$booking->keberangkatan->keberangkatan_code}_{$booking->hotel->hotel_name}.pdf");

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate roomlist: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get hotel bookings for a keberangkatan
     */
    public function getByKeberangkatan($keberangkatanId)
    {
        try {
            $bookings = HotelBooking::with(['hotel', 'keberangkatan', 'roomAssignments'])
                ->where('id_keberangkatan', $keberangkatanId)
                ->get();

            // Format the data for frontend
            $formattedBookings = $bookings->map(function($booking) {
                return [
                    'id'                 => $booking->id,
                    'hotel_name'         => $booking->hotel ? $booking->hotel->hotel_name : 'Unknown Hotel',
                    'check_in'           => $booking->check_in_date,
                    'check_in_formatted' => $booking->check_in_date ? $booking->check_in_date->format('d/m/Y') : '-',
                    'check_out'          => $booking->check_out_date,
                    'check_out_formatted'=> $booking->check_out_date ? $booking->check_out_date->format('d/m/Y') : '-',
                    'room_count'         => $booking->room_count,
                    'room_type'          => $booking->room_type ?? '',
                    'seller_name'        => $booking->seller_name ?? '-',
                    'seller_phone'       => $booking->seller_phone ?? '-',
                    'status'             => $booking->status,
                    'booking_reference'  => $booking->booking_reference,
                    'notes'              => $booking->notes,
                    'assigned_count'     => $booking->roomAssignments->count(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedBookings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve hotel bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get room assignments for a booking
     */
    public function getRoomAssignments($id)
    {
        try {
            \Log::info("=== GET ROOM ASSIGNMENTS START ===");
            \Log::info("Hotel Booking ID: {$id}");
            
            $booking = HotelBooking::with([
                'roomAssignments.jamaahBooking.jamaah',
                'hotel',
                'keberangkatan.travelPackage.hotelMakkah',
                'keberangkatan.travelPackage.hotelMadinah'
            ])->findOrFail($id);

            \Log::info("Hotel Booking Found:", [
                'id' => $booking->id,
                'hotel_name' => $booking->hotel->hotel_name ?? 'N/A',
                'id_keberangkatan' => $booking->id_keberangkatan,
                'keberangkatan_code' => $booking->keberangkatan->keberangkatan_code ?? 'N/A'
            ]);

            // Get unassigned jamaah with their booking details including room_type
            $unassignedJamaah = $booking->getUnassignedJamaah()->load('jamaah', 'travelPackage');
            
            \Log::info("Unassigned Jamaah Count: " . $unassignedJamaah->count());
            
            if ($unassignedJamaah->isEmpty()) {
                \Log::warning("No unassigned jamaah found!");
                \Log::info("Checking all jamaah bookings for this keberangkatan...");
                
                // Check all jamaah bookings for debugging
                $allJamaahBookings = JamaahBooking::where('id_keberangkatan', $booking->id_keberangkatan)
                    ->with('jamaah')
                    ->get();
                    
                \Log::info("Total Jamaah Bookings for Keberangkatan: " . $allJamaahBookings->count());
                
                foreach ($allJamaahBookings as $jb) {
                    \Log::info("Jamaah Booking:", [
                        'id' => $jb->id,
                        'booking_code' => $jb->booking_code,
                        'jamaah_name' => $jb->jamaah->nama ?? 'N/A',
                        'room_type' => $jb->room_type ?? 'NULL',
                        'status' => $jb->status,
                        'has_assignment' => $jb->hotelRoomAssignment()->exists()
                    ]);
                }
            }
            
            // Format unassigned jamaah with room_type from booking
            $formattedUnassignedJamaah = $unassignedJamaah->map(function($jamaahBooking) {
                $formatted = [
                    'id' => $jamaahBooking->id,
                    'jamaah_name' => $jamaahBooking->jamaah->nama ?? 'Tidak ada nama',
                    'room_type' => $jamaahBooking->room_type ?? 'Standard',
                    'booking_code' => $jamaahBooking->booking_code,
                    'no_ktp' => $jamaahBooking->jamaah->ktp_nik ?? '-',
                    'no_passport' => $jamaahBooking->jamaah->passport_nomor ?? '-',
                ];
                
                \Log::info("Formatted Jamaah:", $formatted);
                
                return $formatted;
            });

            \Log::info("Formatted Unassigned Jamaah Count: " . $formattedUnassignedJamaah->count());
            \Log::info("Current Room Assignments Count: " . $booking->roomAssignments->count());
            \Log::info("=== GET ROOM ASSIGNMENTS END ===");

            return response()->json([
                'success' => true,
                'data' => [
                    'booking' => $booking,
                    'assignments' => $booking->roomAssignments,
                    'unassigned_jamaah' => $formattedUnassignedJamaah,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error("Error in getRoomAssignments: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve room assignments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a hotel booking
     */
    public function destroy($id)
    {
        try {
            $booking = HotelBooking::findOrFail($id);

            // Check if there are room assignments
            if ($booking->roomAssignments()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete hotel booking with room assignments. Please remove all assignments first.',
                    'code' => 'HAS_ASSIGNMENTS'
                ], 400);
            }

            $booking->delete();

            return response()->json([
                'success' => true,
                'message' => 'Hotel booking deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete hotel booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available hotels for booking
     */
    public function getAvailableHotels(Request $request)
    {
        try {
            $query = Hotel::query();

            // Filter by outlet if user has outlet restriction
            if (Auth::user()->id_outlet) {
                $query->where('id_outlet', Auth::user()->id_outlet);
            }

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('hotel_name', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%");
                });
            }

            $hotels = $query->get()->map(function($hotel) {
                return [
                    'id' => $hotel->id,
                    'text' => "{$hotel->hotel_name} - {$hotel->location}, {$hotel->city} ({$hotel->star_rating}★)",
                    'hotel_name' => $hotel->hotel_name,
                    'location' => $hotel->location,
                    'city' => $hotel->city,
                    'star_rating' => $hotel->star_rating,
                    'total_rooms' => $hotel->total_rooms,
                    'available_rooms' => $hotel->getAvailableRooms(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $hotels
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available hotels: ' . $e->getMessage()
            ], 500);
        }
    }
}
