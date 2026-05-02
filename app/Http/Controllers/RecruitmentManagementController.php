<?php

namespace App\Http\Controllers;

use App\Models\Recruitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RecruitmentExport;
use App\Traits\HasOutletFilter;

class RecruitmentManagementController extends Controller
{
    use HasOutletFilter;

    /**
     * Display recruitment management page
     */
    public function index(Request $request)
    {
        $outlets = $this->getUserOutlets();
        return view('admin.sdm.kepegawaian.index', compact('outlets'));
    }

    /**
     * Get recruitment data for grid/table view
     */
    public function getData(Request $request)
    {
        $statusFilter = $request->get('status_filter', 'all');
        $departmentFilter = $request->get('department_filter', 'all');
        $outletFilter = $request->get('outlet_filter', 'all');
        $search = $request->get('search', '');

        $query = Recruitment::with('outlet');

        // Apply outlet filter based on user access
        $query = $this->applyOutletFilter($query, 'outlet_id');

        // Additional outlet filter from request
        if ($outletFilter !== 'all') {
            $query->where('outlet_id', $outletFilter);
        }

        // Filter status
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Filter department
        if ($departmentFilter !== 'all') {
            $query->where('department', $departmentFilter);
        }

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('name', 'asc')->get();

        // Transform data for frontend
        $data = $employees->map(function($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'position' => $employee->position,
                'department' => $employee->department ?? '-',
                'status' => $employee->status,
                'status_label' => $this->getStatusLabel($employee->status),
                'phone' => $employee->phone ?? '-',
                'email' => $employee->email ?? '-',
                'outlet_id' => $employee->outlet_id,
                'outlet_name' => $employee->outlet ? $employee->outlet->nama_outlet : '-',
                'salary' => $employee->salary ?? 0,
                'salary_formatted' => 'Rp ' . number_format($employee->salary ?? 0, 0, ',', '.'),
                'hourly_rate' => $employee->hourly_rate ?? 0,
                'hourly_rate_formatted' => 'Rp ' . number_format($employee->hourly_rate ?? 0, 0, ',', '.'),
                'join_date' => $employee->join_date ? date('d/m/Y', strtotime($employee->join_date)) : '-',
                'fingerprint_id' => $employee->fingerprint_id ?? '-',
                'rfid_uid' => $employee->rfid_uid ?? '-',
                'is_registered_fingerprint' => $employee->is_registered_fingerprint,
                'jobdesk' => $employee->jobdesk ?? [],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get departments list
     */
    public function getDepartments()
    {
        $departments = Recruitment::select('department')
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }

    /**
     * Store new employee
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,resigned',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'salary' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'join_date' => 'nullable|date',
            'fingerprint_id' => 'nullable|string|max:50',
            'rfid_uid' => 'nullable|string|max:50',
            'jobdesk' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate outlet access
        $this->validateOutletAccess($request->outlet_id);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'outlet_id', 'name', 'position', 'department', 'status', 
                'phone', 'email', 'address', 'salary', 
                'hourly_rate', 'join_date', 'fingerprint_id', 'rfid_uid', 'jobdesk'
            ]);

            // Set fingerprint registration status
            $data['is_registered_fingerprint'] = !empty($data['fingerprint_id']);

            $employee = Recruitment::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan',
                'data' => $employee
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating employee: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }

    /**
     * Show employee detail
     */
    public function show($id)
    {
        try {
            $employee = Recruitment::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'position' => $employee->position,
                    'department' => $employee->department,
                    'status' => $employee->status,
                    'phone' => $employee->phone,
                    'email' => $employee->email,
                    'address' => $employee->address,
                    'salary' => $employee->salary,
                    'hourly_rate' => $employee->hourly_rate,
                    'join_date' => $employee->join_date,
                    'resign_date' => $employee->resign_date,
                    'fingerprint_id' => $employee->fingerprint_id,
                    'rfid_uid' => $employee->rfid_uid,
                    'is_registered_fingerprint' => $employee->is_registered_fingerprint,
                    'jobdesk' => $employee->jobdesk ?? [],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update employee
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,resigned',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'salary' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'join_date' => 'nullable|date',
            'resign_date' => 'nullable|date',
            'fingerprint_id' => 'nullable|string|max:50',
            'rfid_uid' => 'nullable|string|max:50',
            'jobdesk' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate outlet access
        $this->validateOutletAccess($request->outlet_id);

        try {
            DB::beginTransaction();

            $employee = Recruitment::findOrFail($id);

            // Validate user has access to this employee's outlet
            $this->validateOutletAccess($employee->outlet_id);

            $data = $request->only([
                'outlet_id', 'name', 'position', 'department', 'status', 
                'phone', 'email', 'address', 'salary', 
                'hourly_rate', 'join_date', 'resign_date', 
                'fingerprint_id', 'rfid_uid', 'jobdesk'
            ]);

            // Update fingerprint registration status
            $data['is_registered_fingerprint'] = !empty($data['fingerprint_id']);

            $employee->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diupdate',
                'data' => $employee
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating employee: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data'
            ], 500);
        }
    }

    /**
     * Delete employee
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $employee = Recruitment::findOrFail($id);
            $employee->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting employee: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data'
            ], 500);
        }
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        $statusFilter = $request->get('status_filter', 'all');
        $departmentFilter = $request->get('department_filter', 'all');
        $outletFilter = $request->get('outlet_filter', 'all');

        $query = Recruitment::with('outlet');

        // Apply outlet filter based on user access
        $query = $this->applyOutletFilter($query, 'outlet_id');

        if ($outletFilter !== 'all') {
            $query->where('outlet_id', $outletFilter);
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($departmentFilter !== 'all') {
            $query->where('department', $departmentFilter);
        }

        $employees = $query->orderBy('name', 'asc')->get();

        $pdf = Pdf::loadView('admin.sdm.kepegawaian.pdf', [
            'employees' => $employees,
            'title' => 'Laporan Data Karyawan'
        ]);

        return $pdf->download('data-karyawan-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        $statusFilter = $request->get('status_filter', 'all');
        $departmentFilter = $request->get('department_filter', 'all');
        $outletFilter = $request->get('outlet_filter', 'all');

        return Excel::download(
            new RecruitmentExport($statusFilter, $departmentFilter, $outletFilter, $this->getUserOutletIds()), 
            'data-karyawan-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Get status label
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            'resigned' => 'Resign'
        ];

        return $labels[$status] ?? $status;
    }
}
