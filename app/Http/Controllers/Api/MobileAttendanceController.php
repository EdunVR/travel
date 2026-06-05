<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Recruitment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MobileAttendanceController extends Controller
{
    // ─── Login karyawan via mobile ────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        // Cari data karyawan berdasarkan email atau nama
        $employee = Recruitment::where('email', $user->email)
            ->orWhere('name', $user->name)
            ->where('status', 'active')
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak terhubung dengan data karyawan aktif.',
            ], 403);
        }

        // Generate token — gunakan Sanctum jika tersedia, fallback ke simple token
        $token = $user->createToken('mobile-attendance')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token'       => $token,
                'user_id'     => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'employee_id' => $employee->id,
                'outlet_id'   => $employee->outlet_id,
            ],
        ]);
    }

    // ─── Logout ───────────────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Logout berhasil.']);
    }

    // ─── Status absensi hari ini ──────────────────────────────────────────────
    public function todayStatus(Request $request)
    {
        $employee = $this->getEmployee($request->user());
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $today = Carbon::today()->toDateString();
        $attendance = Attendance::where('recruitment_id', $employee->id)
            ->where('date', $today)
            ->first();

        return response()->json([
            'success' => true,
            'data'    => $attendance ? $this->formatAttendance($attendance) : null,
            'date'    => $today,
            'employee' => [
                'id'   => $employee->id,
                'name' => $employee->name,
            ],
        ]);
    }

    // ─── Clock-in ─────────────────────────────────────────────────────────────
    public function clockIn(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address'   => 'nullable|string|max:500',
            'device_info' => 'nullable|string|max:255',
        ]);

        $employee = $this->getEmployee($request->user());
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $today = Carbon::today()->toDateString();
        $existing = Attendance::where('recruitment_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($existing && $existing->clock_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan clock-in hari ini pukul ' . $existing->clock_in . '.',
                'data'    => $this->formatAttendance($existing),
            ], 409);
        }

        $now = Carbon::now();

        if ($existing) {
            $existing->update([
                'clock_in'         => $now->format('H:i:s'),
                'status'           => 'present',
                'source'           => 'online',
                'latitude'         => $request->latitude,
                'longitude'        => $request->longitude,
                'location_address' => $request->address,
                'device_info'      => $request->device_info,
            ]);
            $attendance = $existing;
        } else {
            $attendance = Attendance::create([
                'recruitment_id'   => $employee->id,
                'employee_name'    => $employee->name,
                'outlet_id'        => $employee->outlet_id,
                'date'             => $today,
                'clock_in'         => $now->format('H:i:s'),
                'status'           => 'present',
                'source'           => 'online',
                'latitude'         => $request->latitude,
                'longitude'        => $request->longitude,
                'location_address' => $request->address,
                'device_info'      => $request->device_info,
            ]);
        }

        Log::info('Mobile clock-in', [
            'employee_id' => $employee->id,
            'name'        => $employee->name,
            'time'        => $now->toDateTimeString(),
            'lat'         => $request->latitude,
            'lng'         => $request->longitude,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clock-in berhasil pada pukul ' . $now->format('H:i') . '.',
            'data'    => $this->formatAttendance($attendance),
        ]);
    }

    // ─── Clock-out ────────────────────────────────────────────────────────────
    public function clockOut(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address'   => 'nullable|string|max:500',
            'device_info' => 'nullable|string|max:255',
        ]);

        $employee = $this->getEmployee($request->user());
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $today = Carbon::today()->toDateString();
        $attendance = Attendance::where('recruitment_id', $employee->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan clock-in hari ini.',
            ], 400);
        }

        if ($attendance->clock_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan clock-out hari ini pukul ' . $attendance->clock_out . '.',
                'data'    => $this->formatAttendance($attendance),
            ], 409);
        }

        $now   = Carbon::now();
        $inTime = Carbon::parse($today . ' ' . $attendance->clock_in);
        $workHours = round($inTime->diffInMinutes($now) / 60, 2);

        $attendance->update([
            'clock_out'    => $now->format('H:i:s'),
            'hours_worked' => $workHours,
            'work_hours'   => $workHours,
        ]);

        Log::info('Mobile clock-out', [
            'employee_id' => $employee->id,
            'name'        => $employee->name,
            'time'        => $now->toDateTimeString(),
            'lat'         => $request->latitude,
            'lng'         => $request->longitude,
            'work_hours'  => $workHours,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clock-out berhasil. Total jam kerja: ' . number_format($workHours, 1) . ' jam.',
            'data'    => $this->formatAttendance($attendance),
        ]);
    }

    // ─── Riwayat absensi ──────────────────────────────────────────────────────
    public function history(Request $request)
    {
        $employee = $this->getEmployee($request->user());
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $month = $request->get('month', Carbon::now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $records = Attendance::where('recruitment_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $mon)
            ->orderByDesc('date')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $records->map(fn($a) => $this->formatAttendance($a)),
            'month'   => $month,
            'summary' => [
                'present'  => $records->whereIn('status', ['present', 'late'])->count(),
                'absent'   => $records->where('status', 'absent')->count(),
                'late'     => $records->where('status', 'late')->count(),
                'total_hours' => round($records->sum('work_hours'), 1),
            ],
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function getEmployee(User $user): ?Recruitment
    {
        return Recruitment::where('status', 'active')
            ->where(function ($q) use ($user) {
                $q->where('email', $user->email)
                  ->orWhere('name', $user->name);
            })
            ->first();
    }

    private function formatAttendance(Attendance $a): array
    {
        // Format work_duration sebagai string yang mudah dibaca
        $workDuration = '';
        $workHours = (float)($a->hours_worked ?? $a->work_hours ?? 0);
        if ($workHours > 0) {
            $hours   = (int)floor($workHours);
            $minutes = (int)round(($workHours - $hours) * 60);
            if ($hours > 0 && $minutes > 0) {
                $workDuration = "{$hours} jam {$minutes} mnt";
            } elseif ($hours > 0) {
                $workDuration = "{$hours} jam";
            } elseif ($minutes > 0) {
                $workDuration = "{$minutes} mnt";
            }
        }

        return [
            'id'               => $a->id,
            'date'             => $a->date?->format('Y-m-d'),
            'clock_in'         => $a->clock_in,
            'clock_out'        => $a->clock_out,
            'break_in'         => $a->break_in,
            'break_out'        => $a->break_out,
            'status'           => $a->status,
            'work_hours'       => $workHours,
            'hours_worked'     => $workHours,
            'work_duration'    => $workDuration,
            'late_minutes'     => (int)($a->late_minutes ?? 0),
            'notes'            => $a->notes,
            'source'           => $a->source ?? 'fingerprint',
            'latitude'         => $a->latitude  ? (float)$a->latitude  : null,
            'longitude'        => $a->longitude ? (float)$a->longitude : null,
            'location_address' => $a->location_address,
            'device_info'      => $a->device_info,
        ];
    }
}
