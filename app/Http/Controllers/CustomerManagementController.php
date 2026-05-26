<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Tipe;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerExport;
use App\Traits\HasOutletFilter;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use OnePointHub\LaravelOcr\Facades\Ocr;

class CustomerManagementController extends Controller
{
    use HasOutletFilter;
    use KtpParserHelper;
    use PassportParserHelper;
    use VisaParserHelper;

    /**
     * Display customer management page
     */
    public function index(Request $request)
    {
        // Get user's accessible outlets only
        $outlets = $this->getUserOutlets();
        
        // Get tipe customer only from accessible outlets
        $accessibleOutletIds = $this->getAccessibleOutletIds();
        $tipes = Tipe::whereIn('id_outlet', $accessibleOutletIds)->get();
        
        return view('admin.crm.pelanggan.index', compact('outlets', 'tipes'));
    }

    /**
     * Get customer data for grid/table view
     */
    public function getData(Request $request)
    {
        $outletIds = $request->get('outlet_ids', []);
        $tipeFilter = $request->get('tipe_filter', 'all');
        $search = $request->get('search', '');

        // Get accessible outlet IDs for this user
        $accessibleOutletIds = $this->getAccessibleOutletIds();

        $query = Member::with(['tipe', 'outlet'])
            ->withTotalPiutang();

        // Apply outlet filter: tampilkan member yang:
        // 1. id_outlet-nya ada di accessible outlets, ATAU
        // 2. punya booking (jamaah_bookings) di accessible outlets
        if (!empty($accessibleOutletIds)) {
            $user = auth()->user();
            if (!$user->hasRole('super_admin')) {
                $query->where(function($q) use ($accessibleOutletIds) {
                    $q->whereIn('id_outlet', $accessibleOutletIds)
                      ->orWhereHas('jamaahBookings', function($bq) use ($accessibleOutletIds) {
                          $bq->whereIn('id_outlet', $accessibleOutletIds);
                      });
                });
            }
        } else {
            // User has no outlet access
            $query->whereRaw('1 = 0');
        }

        // Additional outlet filter from request (multiple outlets)
        if (!empty($outletIds) && is_array($outletIds)) {
            $query->where(function($q) use ($outletIds) {
                $q->whereIn('id_outlet', $outletIds)
                  ->orWhereHas('jamaahBookings', function($bq) use ($outletIds) {
                      $bq->whereIn('id_outlet', $outletIds);
                  });
            });
        }

        // Filter tipe
        if ($tipeFilter !== 'all') {
            $query->where('id_tipe', $tipeFilter);
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('telepon', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('kode_member', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('nama', 'asc')->get();

        // Transform data for frontend
        $data = $customers->map(function($customer) {
            return [
                'id_member' => $customer->id_member,
                'kode_display' => $customer->getMemberCodeWithPrefix() ?? $customer->kode_member ?? '-',
                'kode_member' => $customer->kode_member,
                'nama' => $customer->nama,
                'nama_perusahaan' => $customer->nama_perusahaan,
                'telepon' => $customer->telepon,
                'alamat' => $customer->alamat,
                'tipe_nama' => $customer->tipe ? $customer->tipe->nama_tipe : '-',
                'outlet_nama' => $customer->outlet ? $customer->outlet->nama_outlet : '-',
                'total_piutang' => $customer->total_piutang ?? 0,
                'total_piutang_formatted' => 'Rp ' . number_format($customer->total_piutang ?? 0, 0, ',', '.'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Store new customer
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string',
            'id_tipe' => 'nullable|exists:tipe,id_tipe',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            'pas_foto' => 'nullable|mimes:jpg,jpeg,png|max:5120',
            'ktp_foto' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'ktp_nik' => 'nullable|string|max:20',
            'ktp_nama' => 'nullable|string|max:255',
            'ktp_tempat_lahir' => 'nullable|string|max:255',
            'ktp_tanggal_lahir' => 'nullable|date',
            'ktp_alamat' => 'nullable|string',
            'passport_foto' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'passport_nomor' => 'nullable|string|max:20',
            'passport_nama' => 'nullable|string|max:255',
            'passport_tanggal_lahir' => 'nullable|date',
            'passport_tanggal_kadaluarsa' => 'nullable|date',
            'passport_kewarganegaraan' => 'nullable|string|max:100',
            // Jamaah-specific fields
            'is_jamaah' => 'nullable|boolean',
            'jamaah_type' => 'nullable|in:hajj,umrah',
            'mahram_name' => 'nullable|string|max:255',
            'mahram_relationship' => 'nullable|string|max:100',
            'mahram_phone' => 'nullable|string|max:20',
            'mahram_ktp_nik' => 'nullable|string|regex:/^\d{16}$/',
            'health_conditions' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'room_preference' => 'nullable|in:single,double,triple,quad',
            'special_requests' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate outlet access
        $this->validateOutletAccess($request->id_outlet);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'nama', 'nama_perusahaan', 'telepon', 'alamat', 'id_tipe', 'id_outlet',
                'ktp_nik', 'ktp_nama', 'ktp_tempat_lahir', 'ktp_tanggal_lahir', 'ktp_alamat',
                'passport_nomor', 'passport_nama', 'passport_tanggal_lahir', 
                'passport_tanggal_kadaluarsa', 'passport_kewarganegaraan',
                'passport_title', 'passport_gender', 'passport_tanggal_terbit',
                'passport_kantor_terbit', 'passport_tempat_lahir',
                // Jamaah-specific fields
                'is_jamaah', 'jamaah_type', 'mahram_name', 'mahram_relationship', 'mahram_phone',
                'mahram_ktp_nik', 'health_conditions', 'emergency_contact_name', 
                'emergency_contact_phone', 'emergency_contact_relationship',
                'room_preference', 'special_requests', 'gender'
            ]);
            

            // Handle family_members JSON
            if ($request->has('family_members')) {
                $familyRaw = $request->input('family_members');
                $familyDecoded = is_string($familyRaw) ? json_decode($familyRaw, true) : $familyRaw;
                if (is_array($familyDecoded)) {
                    foreach ($familyDecoded as $fm) {
                        if (empty($fm['nama'])) {
                            return response()->json(['success' => false, 'message' => 'Nama anggota keluarga wajib diisi', 'errors' => ['family_members' => ['Nama setiap anggota keluarga wajib diisi']]], 422);
                        }
                    }
                    $data['family_members'] = json_encode($familyDecoded);
                }
            }
            // Convert is_jamaah to boolean
            $data['is_jamaah'] = $request->has('is_jamaah') && $request->is_jamaah ? true : false;
            
            // Jamaah-specific validation
            if ($data['is_jamaah']) {
                // Validate KTP NIK format (16 digits)
                if (!empty($data['ktp_nik']) && !preg_match('/^\d{16}$/', $data['ktp_nik'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'KTP NIK must be exactly 16 digits',
                        'errors' => ['ktp_nik' => ['KTP NIK must be exactly 16 digits']]
                    ], 422);
                }
                
                // Validate mahram KTP NIK format if provided
                if (!empty($data['mahram_ktp_nik']) && !preg_match('/^\d{16}$/', $data['mahram_ktp_nik'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mahram KTP NIK must be exactly 16 digits',
                        'errors' => ['mahram_ktp_nik' => ['Mahram KTP NIK must be exactly 16 digits']]
                    ], 422);
                }
                
                // Validate mahram for female jamaah under 45
                if (($data['gender'] ?? null) === 'female' && !empty($data['ktp_tanggal_lahir'])) {
                    $age = \Carbon\Carbon::parse($data['ktp_tanggal_lahir'])->age;
                    if ($age < 45 && empty($data['mahram_name'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Female jamaah under 45 years must have a registered mahram',
                            'errors' => ['mahram_name' => ['Female jamaah under 45 years must have a registered mahram']]
                        ], 422);
                    }
                }
                
                // Validate age requirements
                if (!empty($data['ktp_tanggal_lahir']) && !empty($data['jamaah_type'] ?? null)) {
                    $age = \Carbon\Carbon::parse($data['ktp_tanggal_lahir'])->age;
                    if (($data['jamaah_type'] ?? null) === 'umrah' && $age < 12) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Jamaah must be at least 12 years old for Umrah',
                            'errors' => ['ktp_tanggal_lahir' => ['Jamaah must be at least 12 years old for Umrah']]
                        ], 422);
                    }
                    if (($data['jamaah_type'] ?? null) === 'hajj' && $age < 18) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Jamaah must be at least 18 years old for Hajj',
                            'errors' => ['ktp_tanggal_lahir' => ['Jamaah must be at least 18 years old for Hajj']]
                        ], 422);
                    }
                }
            }
            
            // Handle file uploads
            if ($request->hasFile('pas_foto')) {
                $data['pas_foto'] = $request->file('pas_foto')->store('pelanggan/pas_foto', 'public');
            }
            
            if ($request->hasFile('ktp_foto')) {
                $data['ktp_foto'] = $request->file('ktp_foto')->store('pelanggan/ktp', 'public');
            }
            
            if ($request->hasFile('passport_foto')) {
                $data['passport_foto'] = $request->file('passport_foto')->store('pelanggan/passport', 'public');
            }
            
            // Handle additional document uploads
            if ($request->hasFile('visa_foto')) {
                $data['visa_foto'] = $request->file('visa_foto')->store('pelanggan/visa', 'public');
            }
            
            if ($request->hasFile('tiket_foto')) {
                $data['tiket_foto'] = $request->file('tiket_foto')->store('pelanggan/tiket', 'public');
            }
            
            if ($request->hasFile('asuransi_foto')) {
                $data['asuransi_foto'] = $request->file('asuransi_foto')->store('pelanggan/asuransi', 'public');
            }
            
            if ($request->hasFile('sertifikat_kesehatan_foto')) {
                $data['sertifikat_kesehatan_foto'] = $request->file('sertifikat_kesehatan_foto')->store('pelanggan/sertifikat_kesehatan', 'public');
            }
            
            // Handle manifest document uploads
            if ($request->hasFile('akta_lahir_foto')) {
                $data['akta_lahir_foto'] = $request->file('akta_lahir_foto')->store('pelanggan/akta_lahir', 'public');
            }
            if ($request->hasFile('kartu_keluarga_foto')) {
                $data['kartu_keluarga_foto'] = $request->file('kartu_keluarga_foto')->store('pelanggan/kartu_keluarga', 'public');
            }
            if ($request->hasFile('buku_nikah_foto')) {
                $data['buku_nikah_foto'] = $request->file('buku_nikah_foto')->store('pelanggan/buku_nikah', 'public');
            }
            if ($request->hasFile('vaksin_foto')) {
                $data['vaksin_foto'] = $request->file('vaksin_foto')->store('pelanggan/vaksin', 'public');
            }
            if ($request->hasFile('bpjs_foto')) {
                $data['bpjs_foto'] = $request->file('bpjs_foto')->store('pelanggan/bpjs', 'public');
            }
            
            // Generate kode member - ambil yang terbesar dari semua outlet
            $lastMember = Member::orderBy('kode_member', 'desc')
                ->whereNotNull('kode_member')
                ->first();
            
            if ($lastMember && $lastMember->kode_member) {
                // Ambil angka dari kode_member dan tambah 1
                $lastNumber = intval($lastMember->kode_member);
                $nextNumber = $lastNumber + 1;
            } else {
                // Jika belum ada member, mulai dari 1
                $nextNumber = 1;
            }
            
            $data['kode_member'] = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $member = Member::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil ditambahkan',
                'data' => $member
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating customer: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pelanggan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show customer detail
     */
    public function show($id)
    {
        try {
            $member = Member::with(['tipe', 'outlet', 'salesInvoices', 'piutangs'])
                ->withTotalPiutang()
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $member
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update customer
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string',
            'id_tipe' => 'nullable|exists:tipe,id_tipe',
            'id_outlet' => [
                'required',
                \Illuminate\Validation\Rule::exists('outlets', 'id_outlet')
            ],
            'pas_foto' => 'nullable|mimes:jpg,jpeg,png|max:5120',
            'ktp_foto' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'ktp_nik' => 'nullable|string|max:20',
            'ktp_nama' => 'nullable|string|max:255',
            'ktp_tempat_lahir' => 'nullable|string|max:255',
            'ktp_tanggal_lahir' => 'nullable|date',
            'ktp_alamat' => 'nullable|string',
            'passport_foto' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'passport_nomor' => 'nullable|string|max:20',
            'passport_nama' => 'nullable|string|max:255',
            'passport_tanggal_lahir' => 'nullable|date',
            'passport_tanggal_kadaluarsa' => 'nullable|date',
            'passport_kewarganegaraan' => 'nullable|string|max:100',
            // Jamaah-specific fields
            'is_jamaah' => 'nullable|boolean',
            'jamaah_type' => 'nullable|in:hajj,umrah',
            'mahram_name' => 'nullable|string|max:255',
            'mahram_relationship' => 'nullable|string|max:100',
            'mahram_phone' => 'nullable|string|max:20',
            'mahram_ktp_nik' => 'nullable|string|regex:/^\d{16}$/',
            'health_conditions' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'room_preference' => 'nullable|in:single,double,triple,quad',
            'special_requests' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
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

            $member = Member::findOrFail($id);
            
            // Validate outlet access for both old and new outlet
            $this->validateOutletAccess($member->id_outlet);
            $this->validateOutletAccess($request->id_outlet);
            
            $data = $request->only([
                'nama', 'nama_perusahaan', 'telepon', 'alamat', 'id_tipe', 'id_outlet',
                'ktp_nik', 'ktp_nama', 'ktp_tempat_lahir', 'ktp_tanggal_lahir', 'ktp_alamat',
                'passport_nomor', 'passport_nama', 'passport_tanggal_lahir', 
                'passport_tanggal_kadaluarsa', 'passport_kewarganegaraan',
                'passport_title', 'passport_gender', 'passport_tanggal_terbit',
                'passport_kantor_terbit', 'passport_tempat_lahir',
                // Jamaah-specific fields
                'is_jamaah', 'jamaah_type', 'mahram_name', 'mahram_relationship', 'mahram_phone',
                'mahram_ktp_nik', 'health_conditions', 'emergency_contact_name', 
                'emergency_contact_phone', 'emergency_contact_relationship',
                'room_preference', 'special_requests', 'gender'
            ]);
            

            // Handle family_members JSON
            if ($request->has('family_members')) {
                $familyRaw = $request->input('family_members');
                $familyDecoded = is_string($familyRaw) ? json_decode($familyRaw, true) : $familyRaw;
                if (is_array($familyDecoded)) {
                    foreach ($familyDecoded as $fm) {
                        if (empty($fm['nama'])) {
                            return response()->json(['success' => false, 'message' => 'Nama anggota keluarga wajib diisi', 'errors' => ['family_members' => ['Nama setiap anggota keluarga wajib diisi']]], 422);
                        }
                    }
                    $data['family_members'] = json_encode($familyDecoded);
                }
            }
            // Convert is_jamaah to boolean
            $data['is_jamaah'] = $request->has('is_jamaah') && $request->is_jamaah ? true : false;
            
            // Jamaah-specific validation
            if ($data['is_jamaah']) {
                // Validate KTP NIK format (16 digits)
                if (!empty($data['ktp_nik']) && !preg_match('/^\d{16}$/', $data['ktp_nik'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'KTP NIK must be exactly 16 digits',
                        'errors' => ['ktp_nik' => ['KTP NIK must be exactly 16 digits']]
                    ], 422);
                }
                
                // Validate mahram KTP NIK format if provided
                if (!empty($data['mahram_ktp_nik']) && !preg_match('/^\d{16}$/', $data['mahram_ktp_nik'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mahram KTP NIK must be exactly 16 digits',
                        'errors' => ['mahram_ktp_nik' => ['Mahram KTP NIK must be exactly 16 digits']]
                    ], 422);
                }
                
                // Validate mahram for female jamaah under 45
                if (($data['gender'] ?? null) === 'female' && !empty($data['ktp_tanggal_lahir'])) {
                    $age = \Carbon\Carbon::parse($data['ktp_tanggal_lahir'])->age;
                    if ($age < 45 && empty($data['mahram_name'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Female jamaah under 45 years must have a registered mahram',
                            'errors' => ['mahram_name' => ['Female jamaah under 45 years must have a registered mahram']]
                        ], 422);
                    }
                }
                
                // Validate age requirements
                if (!empty($data['ktp_tanggal_lahir']) && !empty($data['jamaah_type'] ?? null)) {
                    $age = \Carbon\Carbon::parse($data['ktp_tanggal_lahir'])->age;
                    if (($data['jamaah_type'] ?? null) === 'umrah' && $age < 12) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Jamaah must be at least 12 years old for Umrah',
                            'errors' => ['ktp_tanggal_lahir' => ['Jamaah must be at least 12 years old for Umrah']]
                        ], 422);
                    }
                    if (($data['jamaah_type'] ?? null) === 'hajj' && $age < 18) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Jamaah must be at least 18 years old for Hajj',
                            'errors' => ['ktp_tanggal_lahir' => ['Jamaah must be at least 18 years old for Hajj']]
                        ], 422);
                    }
                }
            }
            
            // Handle file uploads
            if ($request->hasFile('pas_foto')) {
                // Delete old file if exists
                if ($member->pas_foto && \Storage::disk('public')->exists($member->pas_foto)) {
                    \Storage::disk('public')->delete($member->pas_foto);
                }
                $data['pas_foto'] = $request->file('pas_foto')->store('pelanggan/pas_foto', 'public');
            }
            
            if ($request->hasFile('ktp_foto')) {
                // Delete old file if exists
                if ($member->ktp_foto && \Storage::disk('public')->exists($member->ktp_foto)) {
                    \Storage::disk('public')->delete($member->ktp_foto);
                }
                $data['ktp_foto'] = $request->file('ktp_foto')->store('pelanggan/ktp', 'public');
            }
            
            if ($request->hasFile('passport_foto')) {
                // Delete old file if exists
                if ($member->passport_foto && \Storage::disk('public')->exists($member->passport_foto)) {
                    \Storage::disk('public')->delete($member->passport_foto);
                }
                $data['passport_foto'] = $request->file('passport_foto')->store('pelanggan/passport', 'public');
            }
            
            // Handle additional document uploads
            if ($request->hasFile('visa_foto')) {
                if ($member->visa_foto && \Storage::disk('public')->exists($member->visa_foto)) {
                    \Storage::disk('public')->delete($member->visa_foto);
                }
                $data['visa_foto'] = $request->file('visa_foto')->store('pelanggan/visa', 'public');
            }
            
            if ($request->hasFile('tiket_foto')) {
                if ($member->tiket_foto && \Storage::disk('public')->exists($member->tiket_foto)) {
                    \Storage::disk('public')->delete($member->tiket_foto);
                }
                $data['tiket_foto'] = $request->file('tiket_foto')->store('pelanggan/tiket', 'public');
            }
            
            if ($request->hasFile('asuransi_foto')) {
                if ($member->asuransi_foto && \Storage::disk('public')->exists($member->asuransi_foto)) {
                    \Storage::disk('public')->delete($member->asuransi_foto);
                }
                $data['asuransi_foto'] = $request->file('asuransi_foto')->store('pelanggan/asuransi', 'public');
            }
            
            if ($request->hasFile('sertifikat_kesehatan_foto')) {
                if ($member->sertifikat_kesehatan_foto && \Storage::disk('public')->exists($member->sertifikat_kesehatan_foto)) {
                    \Storage::disk('public')->delete($member->sertifikat_kesehatan_foto);
                }
                $data['sertifikat_kesehatan_foto'] = $request->file('sertifikat_kesehatan_foto')->store('pelanggan/sertifikat_kesehatan', 'public');
            }
            
            // Handle manifest document uploads
            if ($request->hasFile('akta_lahir_foto')) {
                if ($member->akta_lahir_foto && \Storage::disk('public')->exists($member->akta_lahir_foto)) {
                    \Storage::disk('public')->delete($member->akta_lahir_foto);
                }
                $data['akta_lahir_foto'] = $request->file('akta_lahir_foto')->store('pelanggan/akta_lahir', 'public');
            }
            if ($request->hasFile('kartu_keluarga_foto')) {
                if ($member->kartu_keluarga_foto && \Storage::disk('public')->exists($member->kartu_keluarga_foto)) {
                    \Storage::disk('public')->delete($member->kartu_keluarga_foto);
                }
                $data['kartu_keluarga_foto'] = $request->file('kartu_keluarga_foto')->store('pelanggan/kartu_keluarga', 'public');
            }
            if ($request->hasFile('buku_nikah_foto')) {
                if ($member->buku_nikah_foto && \Storage::disk('public')->exists($member->buku_nikah_foto)) {
                    \Storage::disk('public')->delete($member->buku_nikah_foto);
                }
                $data['buku_nikah_foto'] = $request->file('buku_nikah_foto')->store('pelanggan/buku_nikah', 'public');
            }
            if ($request->hasFile('vaksin_foto')) {
                if ($member->vaksin_foto && \Storage::disk('public')->exists($member->vaksin_foto)) {
                    \Storage::disk('public')->delete($member->vaksin_foto);
                }
                $data['vaksin_foto'] = $request->file('vaksin_foto')->store('pelanggan/vaksin', 'public');
            }
            if ($request->hasFile('bpjs_foto')) {
                if ($member->bpjs_foto && \Storage::disk('public')->exists($member->bpjs_foto)) {
                    \Storage::disk('public')->delete($member->bpjs_foto);
                }
                $data['bpjs_foto'] = $request->file('bpjs_foto')->store('pelanggan/bpjs', 'public');
            }
            
            $member->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil diupdate',
                'data' => $member
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating customer: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate pelanggan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete customer
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $member = Member::findOrFail($id);
            
            // Validate outlet access
            $this->validateOutletAccess($member->id_outlet);
            
            // Check if customer has transactions
            if ($member->salesInvoices()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak dapat dihapus karena memiliki transaksi'
                ], 422);
            }

            $member->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting customer: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pelanggan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload dokumen member (manifest) — untuk admin
     * POST /admin/crm/pelanggan/{id}/upload-doc
     */
    public function uploadMemberDocument(Request $request, $id)
    {
        $request->validate([
            'doc_type' => 'required|string|in:passport_foto,ktp_foto,akta_lahir_foto,kartu_keluarga_foto,buku_nikah_foto,vaksin_foto,bpjs_foto,pas_foto',
            'file'     => 'required|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $docType = $request->input('doc_type');

        $folderMap = [
            'passport_foto'        => 'pelanggan/passport',
            'ktp_foto'             => 'pelanggan/ktp',
            'akta_lahir_foto'      => 'pelanggan/akta_lahir',
            'kartu_keluarga_foto'  => 'pelanggan/kartu_keluarga',
            'buku_nikah_foto'      => 'pelanggan/buku_nikah',
            'vaksin_foto'          => 'pelanggan/vaksin',
            'bpjs_foto'            => 'pelanggan/bpjs',
            'pas_foto'             => 'pelanggan/pas_foto',
        ];
        $folder = $folderMap[$docType] ?? 'pelanggan/dokumen';

        try {
            $member = Member::findOrFail($id);
            $this->validateOutletAccess($member->id_outlet);

            // Hapus file lama
            if ($member->$docType && \Storage::disk('public')->exists($member->$docType)) {
                \Storage::disk('public')->delete($member->$docType);
            }

            $path = $request->file('file')->store($folder, 'public');
            $member->update([$docType => $path]);

            return response()->json([
                'success'   => true,
                'message'   => 'Dokumen berhasil diupload',
                'file_url'  => asset('storage/' . $path),
                'file_path' => $path,
                'is_pdf'    => strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf',
            ]);

        } catch (\Exception $e) {
            \Log::error('uploadMemberDocument error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal upload: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Hapus dokumen member (manifest) — untuk admin
     * DELETE /admin/crm/pelanggan/{id}/delete-doc
     */
    public function deleteMemberDocument(Request $request, $id)
    {
        $request->validate([
            'doc_type' => 'required|string|in:passport_foto,ktp_foto,akta_lahir_foto,kartu_keluarga_foto,buku_nikah_foto,vaksin_foto,bpjs_foto,pas_foto',
        ]);

        $docType = $request->input('doc_type');

        try {
            $member = Member::findOrFail($id);
            $this->validateOutletAccess($member->id_outlet);

            if ($member->$docType && \Storage::disk('public')->exists($member->$docType)) {
                \Storage::disk('public')->delete($member->$docType);
            }
            $member->update([$docType => null]);

            return response()->json(['success' => true, 'message' => 'Dokumen berhasil dihapus']);

        } catch (\Exception $e) {
            \Log::error('deleteMemberDocument error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal hapus: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $outletIds = $request->get('outlet_ids', []);
            $tipeFilter = $request->get('tipe_filter', 'all');
            
            return Excel::download(
                new CustomerExport($outletIds, $tipeFilter), 
                'pelanggan_' . date('Y-m-d_His') . '.xlsx'
            );
        } catch (\Exception $e) {
            \Log::error('Error exporting customers to Excel: ' . $e->getMessage());
            return back()->with('error', 'Gagal export data pelanggan');
        }
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $outletIds = $request->get('outlet_ids', []);
            $tipeFilter = $request->get('tipe_filter', 'all');

            $query = Member::with(['tipe', 'outlet'])->withTotalPiutang();

            // Apply multiple outlet filter
            if (!empty($outletIds) && is_array($outletIds)) {
                $query->whereIn('id_outlet', $outletIds);
            }

            if ($tipeFilter !== 'all') {
                $query->where('id_tipe', $tipeFilter);
            }

            $customers = $query->get();

            $pdf = Pdf::loadView('admin.crm.pelanggan.pdf', compact('customers'))
                ->setPaper('a4', 'landscape');

            return $pdf->download('pelanggan_' . date('Y-m-d_His') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error exporting customers to PDF: ' . $e->getMessage());
            return back()->with('error', 'Gagal export PDF pelanggan');
        }
    }

    /**
     * Import customers from Excel
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $import = new \App\Imports\CustomerImport();
            Excel::import($import, $request->file('file'));

            DB::commit();

            $successCount = $import->getSuccessCount();
            $errorCount = $import->getErrorCount();
            $errors = $import->getErrors();

            if ($errorCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Import selesai. Berhasil: {$successCount}, Gagal: {$errorCount}",
                    'errors' => $errors,
                    'success_count' => $successCount,
                    'error_count' => $errorCount
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil import {$successCount} pelanggan",
                'success_count' => $successCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error importing customers: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal import data: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Get tipe customer by outlet IDs
     */
    public function getTipesByOutlets(Request $request)
    {
        try {
            $outletIds = $request->get('outlet_ids', []);
            
            // Get user's accessible outlet IDs
            $accessibleOutletIds = $this->getAccessibleOutletIds();
            
            // Filter requested outlet IDs to only accessible ones
            $validOutletIds = array_intersect($outletIds, $accessibleOutletIds);
            
            if (empty($validOutletIds)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            $tipes = Tipe::whereIn('id_outlet', $validOutletIds)
                ->orderBy('nama_tipe', 'asc')
                ->get(['id_tipe', 'nama_tipe', 'id_outlet']);
            
            return response()->json([
                'success' => true,
                'data' => $tipes
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting tipes by outlets: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tipe customer'
            ], 500);
        }
    }
    public function getStatistics(Request $request)
    {
        try {
            $outletIds = $request->get('outlet_ids', []);

            $query = Member::query();

            // Apply multiple outlet filter
            if (!empty($outletIds) && is_array($outletIds)) {
                $query->whereIn('id_outlet', $outletIds);
            }

            $totalCustomers = $query->count();
            
            // Calculate total piutang for selected outlets
            $piutangQuery = DB::table('piutang')
                ->where('status', 'belum_lunas');
                
            if (!empty($outletIds) && is_array($outletIds)) {
                $piutangQuery->whereIn('id_outlet', $outletIds);
            }
            
            $totalPiutang = $piutangQuery->sum('sisa_piutang');

            $customersByTipe = Member::select('tipe.nama_tipe', DB::raw('count(*) as total'))
                ->join('tipe', 'member.id_tipe', '=', 'tipe.id_tipe')
                ->when(!empty($outletIds) && is_array($outletIds), function($q) use ($outletIds) {
                    return $q->whereIn('member.id_outlet', $outletIds);
                })
                ->groupBy('tipe.nama_tipe')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_customers' => $totalCustomers,
                    'total_piutang' => $totalPiutang,
                    'customers_by_tipe' => $customersByTipe
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting customer statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik pelanggan'
            ], 500);
        }
    }

    /**
     * OCR KTP - Extract data from KTP image
     */
    public function ocrKtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:2048', // max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $image = $request->file('image');
            
            // Simple OCR simulation - In production, use Tesseract OCR or cloud OCR service
            // For now, return empty data structure
            $ocrData = $this->processKtpOcr($image);

            return response()->json([
                'success' => true,
                'message' => 'OCR KTP berhasil diproses',
                'data' => $ocrData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error processing KTP OCR: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR KTP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * OCR Passport - Extract data from Passport image
     */
    public function ocrPassport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:2048', // max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $image = $request->file('image');
            
            // Process OCR
            $ocrData = $this->processPassportOcr($image);

            return response()->json([
                'success' => true,
                'message' => 'OCR Passport berhasil diproses',
                'data' => $ocrData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error processing Passport OCR: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR Passport: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract text from a file using Google Vision (primary) or Tesseract (fallback).
     * Supports both images and PDFs.
     *
     * @param string $filePath  Absolute path to the file
     * @param string $lang      Language hint for Tesseract fallback ('ind' or 'eng')
     * @return string|null
     */
    private function getTextFromFile(string $filePath, string $lang = 'ind'): ?string
    {
        // --- Primary: Google Cloud Vision ---
        $vision = new \App\Services\GoogleVisionService();
        if ($vision->isAvailable()) {
            \Log::info('Using Google Vision OCR for: ' . basename($filePath));
            $text = $vision->extractText($filePath);
            if (!empty($text)) {
                \Log::info('Google Vision OCR success, length: ' . strlen($text));
                return $text;
            }
            \Log::warning('Google Vision returned empty text, falling back to Tesseract');
        }

        // --- Fallback: Tesseract ---
        $mimeType = mime_content_type($filePath);
        if ($mimeType === 'application/pdf') {
            \Log::warning('Tesseract cannot process PDF, skipping fallback');
            return null;
        }

        if (!$this->isTesseractAvailable()) {
            \Log::warning('Tesseract not available');
            return null;
        }

        \Log::info('Using Tesseract OCR fallback for: ' . basename($filePath));
        $processedPath = $this->preprocessImage($filePath);
        $results = [];

        foreach ([3, 4, 6, 11] as $psm) {
            try {
                $text = \OnePointHub\LaravelOcr\Facades\Ocr::scan($processedPath, $lang, $psm);
                if (!empty($text)) {
                    $results[] = ['text' => $text, 'length' => strlen($text)];
                }
            } catch (\Exception $e) {
                \Log::warning("Tesseract PSM $psm failed: " . $e->getMessage());
            }
        }

        if ($processedPath !== $filePath) {
            @unlink($processedPath);
        }

        if (empty($results)) {
            return null;
        }

        usort($results, fn($a, $b) => $b['length'] - $a['length']);
        return $results[0]['text'];
    }

    /**
     * Process KTP OCR - Extract data from KTP image or PDF
     */
    private function processKtpOcr($image)
    {
        try {
            // Resolve file path
            $uploadedPath = $image->getRealPath();
            if (file_exists($uploadedPath)) {
                $fullPath = $uploadedPath;
                $needsCleanup = false;
            } else {
                $tempPath = str_replace('/', DIRECTORY_SEPARATOR, $image->store('temp', 'local'));
                $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . $tempPath);
                $needsCleanup = true;
            }

            if (!file_exists($fullPath)) {
                return ['nik' => '', 'nama' => '', 'tempat_lahir' => '', 'tanggal_lahir' => '', 'alamat' => '',
                        'error' => 'File tidak dapat diproses. Silakan coba lagi.'];
            }

            \Log::info('Processing KTP OCR for: ' . basename($fullPath));

            // Extract text via Google Vision (primary) or Tesseract (fallback)
            $text = $this->getTextFromFile($fullPath, 'ind');

            if ($needsCleanup) {
                @unlink($fullPath);
            }

            if (empty($text)) {
                return ['nik' => '', 'nama' => '', 'tempat_lahir' => '', 'tanggal_lahir' => '', 'alamat' => '',
                        'error' => 'OCR tidak dapat mengekstrak teks. Pastikan file jelas dan tidak blur.'];
            }

            return $this->parseKtpText($text);

        } catch (\Exception $e) {
            \Log::error('KTP OCR Error: ' . $e->getMessage());
            return ['nik' => '', 'nama' => '', 'tempat_lahir' => '', 'tanggal_lahir' => '', 'alamat' => '',
                    'error' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
    
    /**
     * Check if Tesseract is available
            return [
                'nik' => '',
                'nama' => '',
                'tempat_lahir' => '',
                'tanggal_lahir' => '',
                'alamat' => '',
                'error' => 'Terjadi kesalahan saat memproses gambar: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Check if Tesseract is available
     */
    private function isTesseractAvailable()
    {
        try {
            $output = [];
            $returnVar = 0;
            
            // Try common Tesseract paths on Windows
            $tesseractPaths = [
                'tesseract', // If in PATH
                'C:\Program Files\Tesseract-OCR\tesseract.exe',
                'C:\Program Files (x86)\Tesseract-OCR\tesseract.exe',
            ];
            
            foreach ($tesseractPaths as $path) {
                $output = [];
                $returnVar = 0;
                
                // Try to execute tesseract --version
                @exec('"' . $path . '" --version 2>&1', $output, $returnVar);
                
                if ($returnVar === 0) {
                    \Log::info('Tesseract found at: ' . $path);
                    
                    // Set the path for Laravel OCR package
                    config(['ocr.engines.tesseract.executable' => $path]);
                    
                    return true;
                }
            }
            
            \Log::warning('Tesseract not found in any common paths');
            return false;
            
        } catch (\Exception $e) {
            \Log::error('Error checking Tesseract: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Perform OCR on image file
     * This method is used by VisaParserHelper and other OCR operations
     */
    private function performOcr($imagePath)
    {
        try {
            // Try Google Vision first
            $text = $this->getTextFromFile($imagePath, 'eng');
            if (!empty($text)) {
                return $text;
            }
            throw new \Exception('OCR returned empty result');
        } catch (\Exception $e) {
            \Log::error('performOcr error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Preprocess image for better OCR accuracy
     */
    private function preprocessImage($imagePath)
    {
        try {
            // Check if file exists
            if (!file_exists($imagePath)) {
                \Log::error('Image file not found: ' . $imagePath);
                return $imagePath;
            }
            
            // For Intervention Image v3 with GD driver
            $manager = new ImageManager(new Driver());
            $img = $manager->read($imagePath);
            
            // Get dimensions
            $width = $img->width();
            $height = $img->height();
            
            \Log::info("Original image size: {$width}x{$height}");
            
            // Resize to optimal size for OCR (1800px width is good for KTP)
            if ($width < 1800) {
                $img->scale(width: 1800);
                \Log::info("Upscaled to 1800px width");
            }
            // Resize if too large (max 2400px width)
            elseif ($width > 2400) {
                $img->scale(width: 2400);
                \Log::info("Downscaled to 2400px width");
            }
            
            // Convert to grayscale
            $img->greyscale();
            
            // Increase contrast more aggressively for better text separation
            $img->contrast(35);
            
            // Increase brightness to make text clearer
            $img->brightness(15);
            
            // Sharpen for better edge detection
            $img->sharpen(10);
            
            // Save processed image
            $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
            $processedPath = $tempDir . DIRECTORY_SEPARATOR . 'processed_' . uniqid() . '.jpg';
            
            // Create temp directory if not exists
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            // Save with high quality for OCR
            $img->save($processedPath, quality: 95);
            
            \Log::info('Image preprocessed successfully: ' . $processedPath);
            
            return $processedPath;
            
        } catch (\Exception $e) {
            \Log::error('Image preprocessing error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return $imagePath; // Return original if preprocessing fails
        }
    }
    /**
     * Parse KTP text and extract structured data
     */
    private function parseKtpText($text)
    {
        // Use new improved parser
        return $this->parseKtpTextNew($text);
    }
    
    /**
     * Parse Indonesian date format to Y-m-d
     */
    private function parseIndonesianDate($dateString)
    {
        $months = [
            'januari' => '01', 'februari' => '02', 'maret' => '03', 'mart' => '03',
            'april' => '04', 'mei' => '05', 'juni' => '06',
            'juli' => '07', 'agustus' => '08', 'september' => '09',
            'oktober' => '10', 'november' => '11', 'desember' => '12'
        ];
        
        $dateString = strtolower(trim($dateString));
        
        // Try to find month name
        foreach ($months as $month => $num) {
            if (strpos($dateString, $month) !== false) {
                // Extract day and year
                preg_match('/(\d{1,2})/', $dateString, $day);
                preg_match('/(\d{4})/', $dateString, $year);
                
                if (isset($day[1]) && isset($year[1])) {
                    return sprintf('%s-%s-%02d', $year[1], $num, $day[1]);
                }
            }
        }
        
        // Try numeric format (DD-MM-YYYY or DD/MM/YYYY)
        if (preg_match('/(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/', $dateString, $matches)) {
            return sprintf('%s-%02d-%02d', $matches[3], $matches[2], $matches[1]);
        }
        
        return '';
    }
    
    /**
     * Normalize date format to Y-m-d
     */
    private function normalizeDate($dateString)
    {
        // Handle DD-MM-YYYY or DD/MM/YYYY
        if (preg_match('/(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/', $dateString, $matches)) {
            return sprintf('%s-%02d-%02d', $matches[3], $matches[2], $matches[1]);
        }
        
        return $dateString;
    }

    /**
     * Process Passport OCR - Extract data from Passport image or PDF
     */
    private function processPassportOcr($image)
    {
        try {
            // Resolve file path
            $uploadedPath = $image->getRealPath();
            if (file_exists($uploadedPath)) {
                $fullPath = $uploadedPath;
                $needsCleanup = false;
            } else {
                $tempPath = str_replace('/', DIRECTORY_SEPARATOR, $image->store('temp', 'local'));
                $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . $tempPath);
                $needsCleanup = true;
            }

            if (!file_exists($fullPath)) {
                return ['nomor' => '', 'nama' => '', 'tanggal_lahir' => '', 'tanggal_kadaluarsa' => '', 'kewarganegaraan' => '',
                        'error' => 'File tidak dapat diproses. Silakan coba lagi.'];
            }

            \Log::info('Processing Passport OCR for: ' . basename($fullPath));

            // Extract text via Google Vision (primary) or Tesseract (fallback)
            $text = $this->getTextFromFile($fullPath, 'eng');

            if ($needsCleanup) {
                @unlink($fullPath);
            }

            if (empty($text)) {
                return ['nomor' => '', 'nama' => '', 'tanggal_lahir' => '', 'tanggal_kadaluarsa' => '', 'kewarganegaraan' => '',
                        'error' => 'OCR tidak dapat mengekstrak teks. Pastikan file jelas dan tidak blur.'];
            }

            return $this->parsePassportText($text);

        } catch (\Exception $e) {
            \Log::error('Passport OCR Error: ' . $e->getMessage());
            return ['nomor' => '', 'nama' => '', 'tanggal_lahir' => '', 'tanggal_kadaluarsa' => '', 'kewarganegaraan' => '',
                    'error' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
    
    /**
     * Parse Passport text and extract structured data
     */
    private function parsePassportText($text)
    {
        // Use new improved parser
        return $this->parsePassportTextNew($text);
    }
    
    /**
     * Parse passport date format to Y-m-d
     */
    private function parsePassportDate($dateString)
    {
        $dateString = trim($dateString);
        
        // Try DD MMM YYYY format (e.g., "15 JAN 2025")
        if (preg_match('/(\d{1,2})\s+([A-Z]{3})\s+(\d{4})/i', $dateString, $matches)) {
            $months = [
                'JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04',
                'MAY' => '05', 'MAI' => '05', 'JUN' => '06', 'JUL' => '07',
                'AUG' => '08', 'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'
            ];
            
            $month = strtoupper(substr($matches[2], 0, 3));
            if (isset($months[$month])) {
                return sprintf('%s-%s-%02d', $matches[3], $months[$month], $matches[1]);
            }
        }
        
        // Try DD-MM-YYYY or DD/MM/YYYY
        if (preg_match('/(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/', $dateString, $matches)) {
            return sprintf('%s-%02d-%02d', $matches[3], $matches[2], $matches[1]);
        }
        
        // Try YYYY-MM-DD
        if (preg_match('/(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})/', $dateString, $matches)) {
            return sprintf('%s-%02d-%02d', $matches[1], $matches[2], $matches[3]);
        }
        
        return '';
    }
    
    /**
     * Parse MRZ date format (YYMMDD) to Y-m-d
     */
    private function parseMrzDate($mrzDate)
    {
        if (strlen($mrzDate) !== 6) {
            return '';
        }
        
        $year = substr($mrzDate, 0, 2);
        $month = substr($mrzDate, 2, 2);
        $day = substr($mrzDate, 4, 2);
        
        // Determine century (assume dates > 50 are 1900s, else 2000s)
        $fullYear = (intval($year) > 50) ? '19' . $year : '20' . $year;
        
        return sprintf('%s-%s-%s', $fullYear, $month, $day);
    }

    /**
     * OCR Visa - Extract data from Visa image
     */
    public function ocrVisa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:2048', // max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $image = $request->file('image');
            
            // Store temporarily
            $tempPath = $image->store('temp', 'public');
            $fullPath = storage_path('app/public/' . $tempPath);
            
            // Process OCR using VisaParserHelper trait
            $ocrData = $this->parseVisaDocument($fullPath);
            
            // Clean up temp file
            \Storage::disk('public')->delete($tempPath);

            return response()->json([
                'success' => true,
                'message' => 'OCR Visa berhasil diproses',
                'data' => $ocrData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error processing Visa OCR: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR Visa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * OCR Sertifikat Kesehatan - Extract data from Health Certificate image
     */
    public function ocrSertifikatKesehatan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:2048', // max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $image = $request->file('image');
            
            // Store temporarily
            $tempPath = $image->store('temp', 'public');
            $fullPath = storage_path('app/public/' . $tempPath);
            
            // Perform OCR
            $text = $this->performOcr($fullPath);
            
            // Parse health certificate data
            $ocrData = $this->parseSertifikatKesehatanText($text);
            
            // Clean up temp file
            \Storage::disk('public')->delete($tempPath);

            return response()->json([
                'success' => true,
                'message' => 'OCR Sertifikat Kesehatan berhasil diproses',
                'data' => $ocrData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error processing Sertifikat Kesehatan OCR: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR Sertifikat Kesehatan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse health certificate text from OCR
     */
    private function parseSertifikatKesehatanText($text)
    {
        $data = [
            'nomor' => null,
            'jenis' => null,
            'tanggal_terbit' => null,
            'tanggal_kadaluarsa' => null,
            'penerbit' => null,
        ];

        // Extract certificate number
        if (preg_match('/(?:NO|NOMOR|NUMBER)[\s.:]*([A-Z0-9\/-]+)/i', $text, $matches)) {
            $data['nomor'] = trim($matches[1]);
        }

        // Extract certificate type
        $types = [
            'VAKSINASI' => 'Vaksinasi',
            'VACCINATION' => 'Vaksinasi',
            'MEDICAL CHECK' => 'Medical Check-up',
            'HEALTH CERTIFICATE' => 'Sertifikat Kesehatan',
            'FIT TO FLY' => 'Fit to Fly',
            'MENINGITIS' => 'Meningitis',
            'COVID' => 'COVID-19',
        ];

        foreach ($types as $keyword => $type) {
            if (stripos($text, $keyword) !== false) {
                $data['jenis'] = $type;
                break;
            }
        }

        // Extract issue date
        if (preg_match('/(?:TANGGAL|DATE|ISSUED)[\s:]*(\d{1,2}[\s\/-]\d{1,2}[\s\/-]\d{2,4})/i', $text, $matches)) {
            $data['tanggal_terbit'] = $this->parseDate($matches[1]);
        }

        // Extract expiry date
        if (preg_match('/(?:VALID|BERLAKU|EXPIRY)[\s:]*(\d{1,2}[\s\/-]\d{1,2}[\s\/-]\d{2,4})/i', $text, $matches)) {
            $data['tanggal_kadaluarsa'] = $this->parseDate($matches[1]);
        }

        // Extract issuer
        $issuers = [
            'RUMAH SAKIT' => true,
            'HOSPITAL' => true,
            'KLINIK' => true,
            'CLINIC' => true,
            'PUSKESMAS' => true,
        ];

        foreach ($issuers as $keyword => $value) {
            if (preg_match('/' . $keyword . '\s+([A-Z\s]+)/i', $text, $matches)) {
                $data['penerbit'] = trim($matches[0]);
                break;
            }
        }

        return $data;
    }

    /**
     * Parse date string to Y-m-d format
     */
    private function parseDate($dateString)
    {
        try {
            $dateString = preg_replace('/\s+/', ' ', trim($dateString));
            $dateString = str_replace(['/', ' '], '-', $dateString);
            
            $formats = ['d-m-Y', 'd-m-y', 'Y-m-d', 'm-d-Y', 'm-d-y'];
            
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}

