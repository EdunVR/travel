<?php

namespace App\Http\Controllers;

use App\Models\JamaahDocument;
use App\Models\JamaahBooking;
use App\Models\Keberangkatan;
use App\Services\NotificationService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Traits\HasOutletFilter;

class DocumentController extends Controller
{
    use HasOutletFilter;
    
    protected $notificationService;
    protected $auditService;

    public function __construct(NotificationService $notificationService, AuditService $auditService)
    {
        $this->notificationService = $notificationService;
        $this->auditService = $auditService;
        $this->middleware('permission:travel.document.view')->only(['index', 'show']);
        $this->middleware('permission:travel.document.create')->only(['store', 'upload']);
        $this->middleware('permission:travel.document.update')->only(['update', 'verify']);
        $this->middleware('permission:travel.document.delete')->only(['destroy']);
        // Generate methods (manifest, siskopatuh, roomlist) accessible to anyone with keberangkatan access
    }
    /**
     * Display documents for a jamaah booking
     */
    public function index($bookingId)
    {
        $booking = JamaahBooking::with(['jamaah', 'travelPackage', 'keberangkatan', 'documents.verifier'])
            ->findOrFail($bookingId);
        
        $documentTypes = ['passport', 'visa', 'ticket', 'insurance', 'health_certificate'];
        
        return view('admin.travel.document.index', compact('booking', 'documentTypes'));
    }

    /**
     * Upload a document
     */
    public function upload(Request $request, $bookingId)
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|in:passport,visa,ticket,insurance,health_certificate',
            'document_number' => 'nullable|string|max:100',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = JamaahBooking::findOrFail($bookingId);

        // Validate passport expiry (6 months rule)
        if ($request->document_type === 'passport' && $request->expiry_date && $booking->keberangkatan) {
            $departureDate = $booking->keberangkatan->departure_date;
            $monthsUntilExpiry = Carbon::parse($departureDate)->diffInMonths($request->expiry_date, false);
            
            if ($monthsUntilExpiry < 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'Passport must be valid for at least 6 months from departure date'
                ], 422);
            }
        }

        // Store file
        $file = $request->file('file');
        $fileName = time() . '_' . $booking->booking_code . '_' . $request->document_type . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('documents/jamaah', $fileName, 'public');

        // Create or update document
        $document = JamaahDocument::updateOrCreate(
            [
                'id_jamaah_booking' => $bookingId,
                'document_type' => $request->document_type
            ],
            [
                'document_number' => $request->document_number,
                'issue_date' => $request->issue_date,
                'expiry_date' => $request->expiry_date,
                'file_path' => $filePath,
                'status' => 'submitted',
                'notes' => $request->notes
            ]
        );

        // Send notification to administration team
        $this->notificationService->notifyDocumentUploaded($document);

        // Log document upload to audit trail
        $this->auditService->logDocumentUpload(
            $bookingId,
            $request->document_type,
            $fileName
        );

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'document' => $document
        ]);
    }

    /**
     * Verify a document
     */
    public function verify(Request $request, $documentId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $document = JamaahDocument::findOrFail($documentId);
        
        $document->update([
            'status' => $request->status,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document ' . $request->status . ' successfully',
            'document' => $document->load('verifier')
        ]);
    }

    /**
     * Delete a document
     */
    public function destroy($documentId)
    {
        $document = JamaahDocument::findOrFail($documentId);
        
        // Delete file from storage
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    }

    /**
     * Get expiring documents alert
     */
    public function getExpiringDocuments()
    {
        $expiringDocuments = JamaahDocument::with(['jamaahBooking.jamaah', 'jamaahBooking.keberangkatan'])
            ->expiringSoon()
            ->get();

        return response()->json([
            'success' => true,
            'documents' => $expiringDocuments
        ]);
    }

    /**
     * Generate stiker koper untuk semua jamaah keberangkatan
     */
    public function generateStikerKoper($keberangkatanId)
    {
        $keberangkatan = Keberangkatan::with(['travelPackage', 'outlet'])->findOrFail($keberangkatanId);

        $jamaahBookings = JamaahBooking::where('id_keberangkatan', $keberangkatanId)
            ->with(['jamaah'])
            ->whereNotIn('status', ['cancelled'])
            ->get();

        if ($jamaahBookings->isEmpty()) {
            $jamaahBookings = JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
                ->with(['jamaah'])
                ->whereNotIn('status', ['cancelled'])
                ->get();
        }

        $companyName = config('app.name', 'Travel');
        $logoBase64  = null;
        try {
            $setting = \App\Models\CompanySetting::first();
            if ($setting) {
                $companyName = $setting->company_name ?? $companyName;
                if ($setting->company_logo) {
                    $logoPath = storage_path('app/public/' . ltrim($setting->company_logo, '/'));
                    if (file_exists($logoPath)) {
                        $mime = mime_content_type($logoPath);
                        $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
                    }
                }
            }
        } catch (\Exception $e) {}

        // Buat bendera Indonesia sebagai PNG base64 — lebih besar dan mencolok
        $flagImg = imagecreatetruecolor(90, 60);
        $red   = imagecolorallocate($flagImg, 206, 17, 38);
        $white = imagecolorallocate($flagImg, 255, 255, 255);
        imagefilledrectangle($flagImg, 0, 0, 89, 29, $red);
        imagefilledrectangle($flagImg, 0, 30, 89, 59, $white);
        // Border tipis abu-abu
        $gray = imagecolorallocate($flagImg, 180, 180, 180);
        imagerectangle($flagImg, 0, 0, 89, 59, $gray);
        ob_start();
        imagepng($flagImg);
        $flagPng = ob_get_clean();
        imagedestroy($flagImg);
        $flagBase64 = 'data:image/png;base64,' . base64_encode($flagPng);

        $pdf = Pdf::loadView('admin.travel.document.stiker-koper-pdf', compact(
            'keberangkatan', 'jamaahBookings', 'companyName', 'logoBase64', 'flagBase64'
        ))->setPaper('a4', 'portrait');

        // Set narrow margin via DomPDF options
        $pdf->getDomPDF()->set_option('margin_top', 8);
        $pdf->getDomPDF()->set_option('margin_right', 8);
        $pdf->getDomPDF()->set_option('margin_bottom', 8);
        $pdf->getDomPDF()->set_option('margin_left', 8);

        return $pdf->stream('stiker_koper_' . $keberangkatan->keberangkatan_code . '.pdf');
    }

    /**
     * Generate manifest report
     */
    public function generateManifest($keberangkatanId)
    {
        $keberangkatan = Keberangkatan::with(['travelPackage', 'outlet'])->findOrFail($keberangkatanId);
        
        // Get ALL bookings from the same package (both assigned and unassigned to this keberangkatan)
        // This matches the logic in KeberangkatanController@show
        $jamaahBookings = JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
            ->with(['jamaah', 'documents'])
            ->whereNotIn('status', ['cancelled'])
            ->get();
        
        // Add the bookings collection to keberangkatan for the view
        $keberangkatan->setRelation('jamaahBookings', $jamaahBookings);

        $pdf = Pdf::loadView('admin.travel.document.manifest-pdf', compact('keberangkatan'));
        
        return $pdf->download('manifest_' . $keberangkatan->keberangkatan_code . '.pdf');
    }

    /**
     * Generate siskopatuh report (government format)
     */
    public function generateSiskopatuh($keberangkatanId)
    {
        $keberangkatan = Keberangkatan::with(['travelPackage', 'outlet'])->findOrFail($keberangkatanId);
        
        // Get ALL bookings from the same package (both assigned and unassigned to this keberangkatan)
        $jamaahBookings = JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
            ->with(['jamaah', 'documents' => function($q) {
                $q->where('status', 'approved');
            }])
            ->whereNotIn('status', ['cancelled'])
            ->get();
        
        // Add the bookings collection to keberangkatan for the view
        $keberangkatan->setRelation('jamaahBookings', $jamaahBookings);

        $pdf = Pdf::loadView('admin.travel.document.siskopatuh-pdf', compact('keberangkatan'));
        
        return $pdf->download('siskopatuh_' . $keberangkatan->keberangkatan_code . '.pdf');
    }

    /**
     * Generate roomlist report
     */
    public function generateRoomlist($keberangkatanId)
    {
        $keberangkatan = Keberangkatan::with([
            'travelPackage',
            'hotelBookings.hotel',
            'hotelBookings.roomAssignments.jamaahBooking.jamaah'
        ])->findOrFail($keberangkatanId);

        // Get hotel bookings for this keberangkatan
        $hotelBookings = $keberangkatan->hotelBookings;

        // Get ALL jamaah bookings with their personal hotel bookings
        $jamaahBookings = JamaahBooking::where('id_travel_package', $keberangkatan->id_travel_package)
            ->with(['jamaah', 'hotelBookings.hotel'])
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $pdf = Pdf::loadView('admin.travel.document.roomlist-pdf', compact('keberangkatan', 'hotelBookings', 'jamaahBookings'));
        
        return $pdf->download('roomlist_' . $keberangkatan->keberangkatan_code . '.pdf');
    }

    /**
     * Generate Roomlist with Stream format — uses JamaahHotelBooking data
     */
    public function generateRoomlistStream($keberangkatanId)
    {
        $keberangkatan = Keberangkatan::with([
            'travelPackage.hotelMadinah',
            'travelPackage.hotelMakkah',
        ])->findOrFail($keberangkatanId);
        // Load all bookings with their hotel bookings and family members, ordered by sort_order
        $jamaahBookings = \App\Models\JamaahBooking::with([
            'jamaah',
            'hotelBookings' => function($q) { $q->orderBy('sort_order')->orderBy('id'); },
            'hotelBookings.hotel',
        ])
        ->where('id_keberangkatan', $keberangkatanId)
        ->whereNotIn('status', ['cancelled'])
        ->get()
        ->sortBy(function($b) {
            // Sort by minimum sort_order of hotel bookings
            return $b->hotelBookings->min('sort_order') ?? 9999;
        })
        ->values();

        $pdf = Pdf::loadView('admin.travel.document.roomlist-stream-pdf', compact('keberangkatan', 'jamaahBookings'))
            ->setPaper('a4', 'landscape');
        return $pdf->stream('roomlist_' . $keberangkatan->keberangkatan_code . '.pdf');
    }

    /**
     * Check document completion status for a keberangkatan
     */
    public function checkCompletionStatus($keberangkatanId)
    {
        $keberangkatan = Keberangkatan::with('jamaahBookings.documents')->findOrFail($keberangkatanId);
        
        $requiredDocTypes = ['passport', 'visa', 'ticket', 'insurance', 'health_certificate'];
        $allComplete = true;
        $completionDetails = [];

        foreach ($keberangkatan->jamaahBookings as $booking) {
            $jamaahComplete = true;
            $jamaahDocs = [];

            foreach ($requiredDocTypes as $docType) {
                $doc = $booking->documents->where('document_type', $docType)->first();
                $isComplete = $doc && $doc->status === 'approved';
                
                if (!$isComplete) {
                    $jamaahComplete = false;
                    $allComplete = false;
                }

                $jamaahDocs[$docType] = [
                    'exists' => $doc !== null,
                    'status' => $doc ? $doc->status : 'missing',
                    'approved' => $isComplete
                ];
            }

            $completionDetails[] = [
                'booking_id' => $booking->id,
                'jamaah_name' => $booking->jamaah->nama_lengkap,
                'complete' => $jamaahComplete,
                'documents' => $jamaahDocs
            ];
        }

        return response()->json([
            'success' => true,
            'all_complete' => $allComplete,
            'details' => $completionDetails
        ]);
    }

    /**
     * Download document file
     */
    public function download($documentId)
    {
        $document = JamaahDocument::findOrFail($documentId);
        
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($document->file_path);
    }

    /**
     * Preview document
     */
    public function preview($documentId)
    {
        $document = JamaahDocument::findOrFail($documentId);
        
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        $filePath = Storage::disk('public')->path($document->file_path);
        $mimeType = Storage::disk('public')->mimeType($document->file_path);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline'
        ]);
    }

    /**
     * Show manage room position page
     */
    public function manageRoomPosition($keberangkatanId)
    {
        return view('admin.travel.document.manage-room-position');
    }

    /**
     * Get room positions data — uses JamaahHotelBooking data from booking detail
     */
    public function getRoomPositions($keberangkatanId)
    {
        $keberangkatan = Keberangkatan::with(['travelPackage'])->findOrFail($keberangkatanId);

        // Load all jamaah bookings with their hotel bookings
        $jamaahBookings = \App\Models\JamaahBooking::with([
            'jamaah',
            'hotelBookings' => function($q) { $q->orderBy('sort_order')->orderBy('id'); },
            'hotelBookings.hotel',
        ])
        ->where('id_keberangkatan', $keberangkatanId)
        ->whereNotIn('status', ['cancelled'])
        ->get();

        // Group by city_type (makkah/madinah) then by hotel
        $hotelGroups = []; // key: "city_type|hotel_id|hotel_name"

        foreach ($jamaahBookings as $booking) {
            $jamaah = $booking->jamaah;
            if (!$jamaah) continue;

            // Family members
            $familyMembers = $jamaah->family_members ?? [];
            if (is_string($familyMembers)) $familyMembers = json_decode($familyMembers, true);
            if (!is_array($familyMembers)) $familyMembers = [];

            foreach ($booking->hotelBookings->sortBy('sort_order') as $hb) {
                $key = ($hb->city_type ?? 'unknown') . '|' . ($hb->id_hotel ?? 0) . '|' . ($hb->hotel->hotel_name ?? 'Hotel');
                if (!isset($hotelGroups[$key])) {
                    $hotelGroups[$key] = [
                        'id'         => $hb->id,
                        'hotel_name' => ($hb->hotel->hotel_name ?? 'Hotel') . ' (' . ucfirst($hb->city_type ?? '') . ')',
                        'city_type'  => $hb->city_type ?? '',
                        'check_in'   => $hb->check_in_date ? $hb->check_in_date->format('d/m/Y') : '-',
                        'check_out'  => $hb->check_out_date ? $hb->check_out_date->format('d/m/Y') : '-',
                        'rooms'      => [],
                    ];
                }

                // Build occupant list: jamaah utama + anggota keluarga
                $occupants = [['name' => $jamaah->nama ?? '-', 'type' => 'jamaah']];
                foreach ($familyMembers as $fm) {
                    $occupants[] = ['name' => $fm['nama'] ?? '-', 'type' => 'keluarga (' . ($fm['hubungan'] ?? '') . ')'];
                }

                $hotelGroups[$key]['rooms'][] = [
                    'booking_id'    => $booking->id,
                    'booking_code'  => $booking->booking_code,
                    'room_type'     => $hb->room_type ?? ucfirst($booking->room_type ?? 'standard'),
                    'room_position' => $hb->notes ?? '',
                    'hb_id'         => $hb->id,
                    'jamaah_list'   => $occupants,
                    'nights'        => $hb->nights ?? 0,
                    'is_charged'    => $hb->is_charged,
                ];
            }
        }

        $hotels = array_values($hotelGroups);
        $totalJamaah = $keberangkatan->getConfirmedJamaahCount();

        return response()->json([
            'success' => true,
            'keberangkatan' => [
                'id'                      => $keberangkatan->id,
                'keberangkatan_code'      => $keberangkatan->keberangkatan_code,
                'keberangkatan_name'      => $keberangkatan->keberangkatan_name,
                'departure_date'          => $keberangkatan->departure_date->format('Y-m-d'),
                'departure_date_formatted'=> $keberangkatan->departure_date->format('d F Y'),
                'return_date'             => $keberangkatan->return_date ? $keberangkatan->return_date->format('Y-m-d') : null,
                'return_date_formatted'   => $keberangkatan->return_date ? $keberangkatan->return_date->format('d F Y') : '-',
                'total_jamaah'            => $totalJamaah,
            ],
            'hotels' => $hotels,
        ]);
    }

    /**
     * Save room position notes (stored in jamaah_hotel_bookings.notes) + sort_order
     */
    public function saveRoomPositions(Request $request, $keberangkatanId)
    {
        $validated = $request->validate([
            'positions'              => 'required|array',
            'positions.*.hb_id'      => 'required|integer',
            'positions.*.notes'      => 'nullable|string|max:500',
            'positions.*.sort_order' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['positions'] as $pos) {
                $update = ['notes' => $pos['notes'] ?? null];
                if (isset($pos['sort_order'])) {
                    $update['sort_order'] = $pos['sort_order'];
                }
                \App\Models\JamaahHotelBooking::where('id', $pos['hb_id'])->update($update);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Room position berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get room assignments for manage-room-position page (new system)
     * Returns: rooms grouped by city_type, each room has list of persons
     * Persons = jamaah + family members, each draggable
     */
    public function getRoomAssignments($keberangkatanId)
    {
        $keberangkatan = Keberangkatan::with(['travelPackage'])->findOrFail($keberangkatanId);

        // Get all persons (jamaah + family) for this keberangkatan
        $jamaahBookings = \App\Models\JamaahBooking::with(['jamaah', 'hotelBookings.hotel'])
            ->where('id_keberangkatan', $keberangkatanId)
            ->whereNotIn('status', ['cancelled'])
            ->get();

        // Build person pool
        $persons = [];
        foreach ($jamaahBookings as $booking) {
            $jamaah = $booking->jamaah;
            if (!$jamaah) continue;

            // Determine city types from hotel bookings
            $cityTypes = $booking->hotelBookings->pluck('city_type')->unique()->toArray();
            if (empty($cityTypes)) $cityTypes = ['makkah', 'madinah'];

            $roomType = $booking->hotelBookings->first()->room_type ?? ucfirst($booking->room_type ?? 'double');

            foreach ($cityTypes as $cityType) {
                $hb = $booking->hotelBookings->where('city_type', $cityType)->first();
                $persons[] = [
                    'id'              => 'j_' . $booking->id . '_' . $cityType,
                    'booking_id'      => $booking->id,
                    'booking_code'    => $booking->booking_code,
                    'city_type'       => $cityType,
                    'person_type'     => 'jamaah',
                    'person_name'     => $jamaah->nama ?? '-',
                    'family_index'    => null,
                    'default_room_type' => $roomType,
                    'hotel_name'      => $hb ? ($hb->hotel->hotel_name ?? '-') : '-',
                ];

                // Family members
                $familyMembers = $jamaah->family_members ?? [];
                if (is_string($familyMembers)) $familyMembers = json_decode($familyMembers, true);
                if (!is_array($familyMembers)) $familyMembers = [];

                foreach ($familyMembers as $fi => $fm) {
                    $persons[] = [
                        'id'              => 'f_' . $booking->id . '_' . $cityType . '_' . $fi,
                        'booking_id'      => $booking->id,
                        'booking_code'    => $booking->booking_code,
                        'city_type'       => $cityType,
                        'person_type'     => 'family',
                        'person_name'     => ($fm['nama'] ?? '-') . ' (' . ($fm['hubungan'] ?? 'Keluarga') . ')',
                        'family_index'    => (string)$fi,
                        'default_room_type' => $roomType,
                        'hotel_name'      => $hb ? ($hb->hotel->hotel_name ?? '-') : '-',
                    ];
                }
            }
        }

        // Get existing room assignments
        $existing = \App\Models\RoomAssignment::where('id_keberangkatan', $keberangkatanId)
            ->orderBy('city_type')->orderBy('room_number')->orderBy('sort_order')
            ->get();

        // Build rooms structure
        $rooms = [];
        foreach ($existing->groupBy('city_type') as $cityType => $cityRooms) {
            foreach ($cityRooms->groupBy('room_number') as $roomNumber => $roomPersons) {
                $rooms[] = [
                    'room_number'   => $roomNumber,
                    'city_type'     => $cityType,
                    'room_type'     => $roomPersons->first()->room_type,
                    'room_position' => $roomPersons->first()->room_position ?? '',
                    'persons'       => $roomPersons->map(fn($p) => [
                        'id'           => ($p->person_type === 'jamaah' ? 'j_' : 'f_') . $p->id_jamaah_booking . '_' . $cityType . ($p->family_index !== null ? '_' . $p->family_index : ''),
                        'assignment_id'=> $p->id,
                        'person_name'  => $p->person_name,
                        'person_type'  => $p->person_type,
                        'booking_id'   => $p->id_jamaah_booking,
                        'booking_code' => $p->jamaahBooking->booking_code ?? '',
                        'family_index' => $p->family_index,
                    ])->values()->toArray(),
                ];
            }
        }

        // Persons not yet assigned
        $assignedIds = collect($rooms)->flatMap(fn($r) => collect($r['persons'])->pluck('id'))->toArray();
        $unassigned = array_filter($persons, fn($p) => !in_array($p['id'], $assignedIds));

        return response()->json([
            'success'      => true,
            'keberangkatan'=> [
                'id'                       => $keberangkatan->id,
                'keberangkatan_name'       => $keberangkatan->keberangkatan_name,
                'departure_date_formatted' => $keberangkatan->departure_date->format('d F Y'),
            ],
            'persons'    => array_values($persons),
            'unassigned' => array_values($unassigned),
            'rooms'      => $rooms,
        ]);
    }

    /**
     * Save room assignments
     */
    public function saveRoomAssignments(Request $request, $keberangkatanId)
    {
        $validated = $request->validate([
            'rooms'                       => 'required|array',
            'rooms.*.room_number'         => 'required',
            'rooms.*.city_type'           => 'required|string',
            'rooms.*.room_type'           => 'required|string',
            'rooms.*.room_position'       => 'nullable|string|max:255',
            'rooms.*.persons'             => 'required|array',
            'rooms.*.persons.*.booking_id'   => 'required|integer',
            'rooms.*.persons.*.person_type'  => 'required|string',
            'rooms.*.persons.*.person_name'  => 'required|string',
            'rooms.*.persons.*.family_index' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Delete existing assignments for this keberangkatan
            \App\Models\RoomAssignment::where('id_keberangkatan', $keberangkatanId)->delete();

            foreach ($validated['rooms'] as $room) {
                foreach ($room['persons'] as $idx => $person) {
                    \App\Models\RoomAssignment::create([
                        'id_keberangkatan' => $keberangkatanId,
                        'city_type'        => $room['city_type'],
                        'room_number'      => (string)($room['room_number'] ?? ''),
                        'room_type'        => $room['room_type'],
                        'person_type'      => $person['person_type'],
                        'id_jamaah_booking'=> $person['booking_id'],
                        'person_name'      => $person['person_name'],
                        'family_index'     => $person['family_index'] ?? null,
                        'room_position'    => $room['room_position'] ?? null,
                        'sort_order'       => $idx,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Room assignment berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Auto-assign rooms based on booking room_type
     */
    public function autoAssignRooms(Request $request, $keberangkatanId)
    {
        $cityType = $request->input('city_type', 'makkah');

        $jamaahBookings = \App\Models\JamaahBooking::with(['jamaah', 'hotelBookings'])
            ->where('id_keberangkatan', $keberangkatanId)
            ->whereNotIn('status', ['cancelled'])
            ->get();

        // Build person list
        $persons = [];
        foreach ($jamaahBookings as $booking) {
            $jamaah = $booking->jamaah;
            if (!$jamaah) continue;
            $hb = $booking->hotelBookings->where('city_type', $cityType)->first();
            $roomType = $hb->room_type ?? ucfirst($booking->room_type ?? 'double');

            $persons[] = ['booking_id' => $booking->id, 'person_type' => 'jamaah', 'person_name' => $jamaah->nama ?? '-', 'family_index' => null, 'room_type' => $roomType];

            $fm = $jamaah->family_members ?? [];
            if (is_string($fm)) $fm = json_decode($fm, true);
            if (!is_array($fm)) $fm = [];
            foreach ($fm as $fi => $member) {
                $persons[] = ['booking_id' => $booking->id, 'person_type' => 'family', 'person_name' => ($member['nama'] ?? '-') . ' (' . ($member['hubungan'] ?? 'Keluarga') . ')', 'family_index' => (string)$fi, 'room_type' => $roomType];
            }
        }

        // Group by room_type and assign room numbers
        $rooms = [];
        $roomCounter = 101;
        $grouped = collect($persons)->groupBy('room_type');

        foreach ($grouped as $roomType => $typePersons) {
            $capacity = \App\Models\RoomAssignment::capacityForType($roomType);
            $chunks = $typePersons->chunk($capacity);
            foreach ($chunks as $chunk) {
                $rooms[] = [
                    'room_number'   => (string)$roomCounter++,
                    'city_type'     => $cityType,
                    'room_type'     => $roomType,
                    'room_position' => '',
                    'persons'       => $chunk->values()->toArray(),
                ];
            }
        }

        // Save
        DB::beginTransaction();
        try {
            \App\Models\RoomAssignment::where('id_keberangkatan', $keberangkatanId)
                ->where('city_type', $cityType)->delete();

            foreach ($rooms as $room) {
                foreach ($room['persons'] as $idx => $person) {
                    \App\Models\RoomAssignment::create([
                        'id_keberangkatan' => $keberangkatanId,
                        'city_type'        => $cityType,
                        'room_number'      => $room['room_number'],
                        'room_type'        => $room['room_type'],
                        'person_type'      => $person['person_type'],
                        'id_jamaah_booking'=> $person['booking_id'],
                        'person_name'      => $person['person_name'],
                        'family_index'     => $person['family_index'] ?? null,
                        'room_position'    => null,
                        'sort_order'       => $idx,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Auto-assign berhasil', 'rooms' => $rooms]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

