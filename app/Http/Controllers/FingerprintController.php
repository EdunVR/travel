<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recruitment;

class FingerprintController extends Controller
{
    public function getAvailableId()
    {
        // Ambil semua ID sidik jari yang sudah terpakai
        $usedIds = Recruitment::whereNotNull('fingerprint_id')->pluck('fingerprint_id')->toArray();

        // Cari ID yang tersedia (1-127)
        $availableIds = [];
        for ($i = 1; $i <= 500; $i++) {
            if (!in_array($i, $usedIds)) {
                $availableIds[] = $i;
            }
        }

        // Kembalikan ID yang tersedia (ambil yang pertama)
        if (!empty($availableIds)) {
            return response()->json($availableIds[0], 200); // Mengembalikan ID pertama yang tersedia
        } else {
            return response()->json(['error' => 'Tidak ada ID yang tersedia.'], 404);
        }
    }

    public function getEmployeeByFingerprintId($fingerprint_id)
    {
        try {
            // Cari karyawan berdasarkan fingerprint_id
            $employee = Recruitment::where('fingerprint_id', $fingerprint_id)
                ->select('id', 'name', 'position', 'fingerprint_id')
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
                    'name' => $employee->name,
                    'position' => $employee->position,
                    'fingerprint_id' => $employee->fingerprint_id
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}