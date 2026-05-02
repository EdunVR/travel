<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Recruitment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Check-in attendance via fingerprint
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'fingerprint_id' => 'required|string',
            'timestamp' => 'nullable|date'
        ]);

        // Find employee by fingerprint ID
        $employee = Recruitment::where('fingerprint_id', $request->fingerprint_id)
            ->where('status', 'aktif')
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan atau tidak aktif'
            ], 404);
        }

        $timestamp = $request->timestamp ? Carbon::parse($request->timestamp) : now();
        $date = $timestamp->format('Y-m-d');
        $time = $timestamp->format('H:i:s');

        // Check if already checked in today
        $existing = Attendance::where('employee_id', $employee->id)
            ->where('date', $date)
            ->first();

        if ($existing && $existing->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah melakukan check-in hari ini',
                'data' => $existing
            ], 400);
        }

        // Create or update attendance
        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $date
            ],
            [
                'check_in' => $time,
                'status' => 'hadir',
                'normal_hours' => 8
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil',
            'data' => [
                'employee_name' => $employee->nama,
                'date' => $date,
                'check_in' => $time,
                'attendance_id' => $attendance->id
            ]
        ]);
    }

    /**
     * Check-out attendance via fingerprint
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'fingerprint_id' => 'required|string',
            'timestamp' => 'nullable|date'
        ]);

        // Find employee by fingerprint ID
        $employee = Recruitment::where('fingerprint_id', $request->fingerprint_id)
            ->where('status', 'aktif')
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan atau tidak aktif'
            ], 404);
        }

        $timestamp = $request->timestamp ? Carbon::parse($request->timestamp) : now();
        $date = $timestamp->format('Y-m-d');
        $time = $timestamp->format('H:i:s');

        // Find today's attendance
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $date)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Belum melakukan check-in hari ini'
            ], 400);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah melakukan check-out hari ini',
                'data' => $attendance
            ], 400);
        }

        // Update check-out time
        $attendance->check_out = $time;
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil',
            'data' => [
                'employee_name' => $employee->nama,
                'date' => $date,
                'check_in' => $attendance->check_in,
                'check_out' => $time,
                'work_hours' => $attendance->work_hours,
                'overtime_hours' => $attendance->overtime_hours,
                'attendance_id' => $attendance->id
            ]
        ]);
    }

    /**
     * Store attendance data (bulk or single)
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:recruitments,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i:s',
            'check_out' => 'nullable|date_format:H:i:s',
            'status' => 'required|in:hadir,izin,sakit,alpha,cuti',
            'normal_hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date' => $request->date
            ],
            [
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'status' => $request->status,
                'normal_hours' => $request->normal_hours ?? 8,
                'notes' => $request->notes
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Data absensi berhasil disimpan',
            'data' => $attendance
        ]);
    }

    /**
     * Get employee by fingerprint ID
     */
    public function getEmployeeByFingerprint($fingerprintId)
    {
        $employee = Recruitment::where('fingerprint_id', $fingerprintId)
            ->where('status', 'aktif')
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $employee->id,
                'nama' => $employee->nama,
                'jabatan' => $employee->jabatan,
                'departemen' => $employee->departemen,
                'fingerprint_id' => $employee->fingerprint_id
            ]
        ]);
    }

    /**
     * Get today's attendance for an employee
     */
    public function getTodayAttendance($employeeId)
    {
        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('date', now()->format('Y-m-d'))
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada data absensi hari ini'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);
    }
}
