<?php

namespace App\Http\Controllers;

use App\Models\PerformanceAppraisal;
use App\Models\Recruitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\HasOutletFilter;
use Carbon\Carbon;

class PerformanceAppraisalController extends Controller
{
    use HasOutletFilter;

    public function index(Request $request)
    {
        $outlets = $this->getUserOutlets();
        return view('admin.sdm.kinerja.index', compact('outlets'));
    }

    public function getData(Request $request)
    {
        $outletFilter = $request->get('outlet_filter', 'all');
        $periodFilter = $request->get('period_filter');
        $statusFilter = $request->get('status_filter', 'all');
        $employeeFilter = $request->get('employee_filter');
        $search = $request->get('search', '');

        $query = PerformanceAppraisal::with(['outlet', 'employee', 'evaluator']);

        // Apply outlet filter
        $query = $this->applyOutletFilter($query, 'outlet_id');

        if ($outletFilter !== 'all') {
            $query->where('outlet_id', $outletFilter);
        }

        if ($periodFilter) {
            $query->where('period', $periodFilter);
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($employeeFilter) {
            $query->where('recruitment_id', $employeeFilter);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                  ->orWhere('period', 'like', "%{$search}%");
            });
        }

        $appraisals = $query->orderBy('appraisal_date', 'desc')->get();

        $data = $appraisals->map(function($appraisal) {
            $gradeInfo = $appraisal->getGradeLabel();
            
            return [
                'id' => $appraisal->id,
                'outlet_name' => $appraisal->outlet ? $appraisal->outlet->nama_outlet : '-',
                'employee_name' => $appraisal->employee_name,
                'employee_position' => $appraisal->employee ? $appraisal->employee->position : '-',
                'period' => $appraisal->period,
                'appraisal_date' => $appraisal->appraisal_date->format('d/m/Y'),
                'average_score' => number_format($appraisal->average_score, 2),
                'grade' => $appraisal->grade,
                'grade_label' => $gradeInfo['text'],
                'grade_color' => $gradeInfo['color'],
                'evaluator_name' => $appraisal->evaluator ? $appraisal->evaluator->name : '-',
                'status' => $appraisal->status,
                'status_label' => $appraisal->status === 'final' ? 'Final' : 'Draft',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getEmployees(Request $request)
    {
        $outletId = $request->get('outlet_id');
        
        $query = Recruitment::where('status', 'active');
        
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        } else {
            $query = $this->applyOutletFilter($query, 'outlet_id');
        }

        $employees = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $employees->map(function($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->name,
                    'position' => $emp->position,
                    'department' => $emp->department,
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'recruitment_id' => 'required|exists:recruitments,id',
            'period' => 'required|string',
            'appraisal_date' => 'required|date',
            'discipline_score' => 'required|integer|min:0|max:100',
            'teamwork_score' => 'required|integer|min:0|max:100',
            'work_result_score' => 'required|integer|min:0|max:100',
            'initiative_score' => 'required|integer|min:0|max:100',
            'kpi_score' => 'required|integer|min:0|max:100',
            'evaluator_notes' => 'nullable|string',
            'employee_notes' => 'nullable|string',
            'improvement_plan' => 'nullable|string',
            'status' => 'required|in:draft,final',
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

        // Check duplicate
        $exists = PerformanceAppraisal::where('recruitment_id', $request->recruitment_id)
            ->where('period', $request->period)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Penilaian kinerja untuk karyawan ini pada periode tersebut sudah ada'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $employee = Recruitment::findOrFail($request->recruitment_id);

            $appraisal = new PerformanceAppraisal($request->all());
            $appraisal->employee_name = $employee->name;
            $appraisal->evaluator_id = auth()->id();
            $appraisal->created_by = auth()->id();
            
            if ($request->status === 'final') {
                $appraisal->evaluated_at = now();
            }

            $appraisal->autoCalculate();
            $appraisal->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penilaian kinerja berhasil ditambahkan',
                'data' => $appraisal
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating performance appraisal: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $appraisal = PerformanceAppraisal::with(['outlet', 'employee', 'evaluator'])->findOrFail($id);

            // Validate outlet access
            $this->validateOutletAccess($appraisal->outlet_id);

            return response()->json([
                'success' => true,
                'data' => $appraisal
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $appraisal = PerformanceAppraisal::findOrFail($id);

        // Only draft can be edited
        if ($appraisal->status === 'final') {
            return response()->json([
                'success' => false,
                'message' => 'Penilaian yang sudah final tidak dapat diedit'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlets,id_outlet',
            'recruitment_id' => 'required|exists:recruitments,id',
            'period' => 'required|string',
            'appraisal_date' => 'required|date',
            'discipline_score' => 'required|integer|min:0|max:100',
            'teamwork_score' => 'required|integer|min:0|max:100',
            'work_result_score' => 'required|integer|min:0|max:100',
            'initiative_score' => 'required|integer|min:0|max:100',
            'kpi_score' => 'required|integer|min:0|max:100',
            'evaluator_notes' => 'nullable|string',
            'employee_notes' => 'nullable|string',
            'improvement_plan' => 'nullable|string',
            'status' => 'required|in:draft,final',
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
        $this->validateOutletAccess($appraisal->outlet_id);

        try {
            DB::beginTransaction();

            $employee = Recruitment::findOrFail($request->recruitment_id);

            $appraisal->fill($request->all());
            $appraisal->employee_name = $employee->name;
            
            // If changing to final, set evaluated_at
            if ($request->status === 'final' && $appraisal->status === 'draft') {
                $appraisal->evaluated_at = now();
            }

            $appraisal->autoCalculate();
            $appraisal->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penilaian kinerja berhasil diupdate',
                'data' => $appraisal
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating performance appraisal: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $appraisal = PerformanceAppraisal::findOrFail($id);

            // Only draft can be deleted
            if ($appraisal->status === 'final') {
                return response()->json([
                    'success' => false,
                    'message' => 'Penilaian yang sudah final tidak dapat dihapus'
                ], 422);
            }

            // Validate outlet access
            $this->validateOutletAccess($appraisal->outlet_id);

            DB::beginTransaction();
            $appraisal->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penilaian kinerja berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting performance appraisal: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPdf(Request $request)
    {
        $id = $request->get('id');
        
        if ($id) {
            // Export single appraisal
            $appraisal = PerformanceAppraisal::with(['outlet', 'employee', 'evaluator'])->findOrFail($id);
            
            // Validate outlet access
            $this->validateOutletAccess($appraisal->outlet_id);

            $pdf = Pdf::loadView('admin.sdm.kinerja.pdf-single', compact('appraisal'));
            
            return $pdf->stream('penilaian-kinerja-' . $appraisal->employee_name . '-' . $appraisal->period . '.pdf');
        } else {
            // Export multiple appraisals
            $outletFilter = $request->get('outlet_filter', 'all');
            $periodFilter = $request->get('period_filter');
            $statusFilter = $request->get('status_filter', 'all');

            $query = PerformanceAppraisal::with(['outlet', 'employee', 'evaluator']);
            $query = $this->applyOutletFilter($query, 'outlet_id');

            if ($outletFilter !== 'all') {
                $query->where('outlet_id', $outletFilter);
            }

            if ($periodFilter) {
                $query->where('period', $periodFilter);
            }

            if ($statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }

            $appraisals = $query->orderBy('appraisal_date', 'desc')->get();

            $pdf = Pdf::loadView('admin.sdm.kinerja.pdf-list', [
                'appraisals' => $appraisals,
                'period' => $periodFilter,
                'title' => 'Laporan Penilaian Kinerja'
            ]);

            return $pdf->stream('laporan-penilaian-kinerja-' . date('Y-m-d') . '.pdf');
        }
    }

    public function getStatistics(Request $request)
    {
        $periodFilter = $request->get('period_filter');
        
        $query = PerformanceAppraisal::query();
        $query = $this->applyOutletFilter($query, 'outlet_id');

        if ($periodFilter) {
            $query->where('period', $periodFilter);
        }

        $total = $query->count();
        $avgScore = $query->avg('average_score') ?? 0;
        $gradeA = (clone $query)->where('grade', 'A')->count();
        $gradeB = (clone $query)->where('grade', 'B')->count();
        $gradeC = (clone $query)->where('grade', 'C')->count();
        $gradeD = (clone $query)->where('grade', 'D')->count();
        $gradeE = (clone $query)->where('grade', 'E')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'average_score' => round($avgScore, 2),
                'grade_a' => $gradeA,
                'grade_b' => $gradeB,
                'grade_c' => $gradeC,
                'grade_d' => $gradeD,
                'grade_e' => $gradeE,
            ]
        ]);
    }
}
