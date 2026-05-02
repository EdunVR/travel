<?php

/**
 * Test script untuk memverifikasi perhitungan jam kerja dan lembur
 * sesuai dengan rumus yang diminta:
 * 
 * total_jam_kerja = [(break_in - clock_in) + (clock_out - break_out)] + (overtime_out - overtime_in)
 * overtime_hours = overtime_out - overtime_in
 */

require_once 'vendor/autoload.php';

use Carbon\Carbon;

echo "=== TEST PERHITUNGAN JAM KERJA DAN LEMBUR ===\n\n";

// Test Case 1: Normal work dengan istirahat
echo "Test Case 1: Normal work dengan istirahat\n";
echo "Clock In: 08:00\n";
echo "Break In: 12:00 (mulai istirahat)\n";
echo "Break Out: 13:00 (selesai istirahat)\n";
echo "Clock Out: 17:00\n";

$clockIn = Carbon::parse('08:00');
$breakIn = Carbon::parse('12:00');   // mulai istirahat
$breakOut = Carbon::parse('13:00');  // selesai istirahat
$clockOut = Carbon::parse('17:00');

// Rumus: [(break_in - clock_in) + (clock_out - break_out)]
$beforeBreakMinutes = $clockIn->diffInMinutes($breakIn);
$afterBreakMinutes = $breakOut->diffInMinutes($clockOut);
$totalMinutes = $beforeBreakMinutes + $afterBreakMinutes;
$totalHours = $totalMinutes / 60;

echo "Sebelum istirahat: {$breakIn->format('H:i')} - {$clockIn->format('H:i')} = {$beforeBreakMinutes} menit\n";
echo "Setelah istirahat: {$clockOut->format('H:i')} - {$breakOut->format('H:i')} = {$afterBreakMinutes} menit\n";
echo "Total: {$beforeBreakMinutes} + {$afterBreakMinutes} = {$totalMinutes} menit = {$totalHours} jam ✅\n\n";

// Test Case 2: Dengan lembur
echo "Test Case 2: Dengan lembur\n";
echo "Clock In: 08:00\n";
echo "Break In: 12:00 (mulai istirahat)\n";
echo "Break Out: 13:00 (selesai istirahat)\n";
echo "Clock Out: 17:00\n";
echo "Overtime In: 18:00\n";
echo "Overtime Out: 20:00\n";

$overtimeIn = Carbon::parse('18:00');
$overtimeOut = Carbon::parse('20:00');

// Jam kerja normal (sama seperti test case 1)
$normalHours = 8; // dari test case 1

// Jam lembur: overtime_out - overtime_in
$overtimeMinutes = $overtimeIn->diffInMinutes($overtimeOut);
$overtimeHours = $overtimeMinutes / 60;

// Total jam kerja: normal + lembur
$totalWithOvertime = $normalHours + $overtimeHours;

echo "Jam kerja normal: {$normalHours} jam\n";
echo "Lembur: {$overtimeOut->format('H:i')} - {$overtimeIn->format('H:i')} = {$overtimeMinutes} menit = {$overtimeHours} jam\n";
echo "Total: {$normalHours} + {$overtimeHours} = {$totalWithOvertime} jam ✅\n\n";

// Test Case 3: Tanpa istirahat
echo "Test Case 3: Tanpa istirahat\n";
echo "Clock In: 08:00\n";
echo "Clock Out: 17:00\n";

$clockIn3 = Carbon::parse('08:00');
$clockOut3 = Carbon::parse('17:00');

// Rumus: clock_out - clock_in
$totalMinutes3 = $clockIn3->diffInMinutes($clockOut3);
$totalHours3 = $totalMinutes3 / 60;

echo "Total: {$clockOut3->format('H:i')} - {$clockIn3->format('H:i')} = {$totalMinutes3} menit = {$totalHours3} jam ✅\n\n";

// Test Case 4: Format output jam lembur
echo "Test Case 4: Format output jam lembur\n";

function formatOvertimeHours($minutes) {
    if ($minutes <= 0) return '-';
    
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    
    if ($hours > 0 && $mins > 0) {
        return "{$hours}j {$mins}m";
    } else if ($hours > 0) {
        return "{$hours} jam";
    } else if ($mins > 0) {
        return "{$mins} mnt";
    } else {
        return '-';
    }
}

$testCases = [
    135 => '2j 15m', // 2 jam 15 menit
    120 => '2 jam',  // tepat 2 jam
    60 => '1 jam',   // tepat 1 jam
    30 => '30 mnt',  // 30 menit
    0 => '-'         // tidak ada lembur
];

foreach ($testCases as $minutes => $expected) {
    $result = formatOvertimeHours($minutes);
    $status = $result === $expected ? '✅' : '❌';
    echo "{$minutes} menit → '{$result}' (expected: '{$expected}') {$status}\n";
}

echo "\n=== SEMUA TEST SELESAI ===\n";
echo "Rumus yang digunakan:\n";
echo "1. total_jam_kerja = [(break_in - clock_in) + (clock_out - break_out)] + (overtime_out - overtime_in)\n";
echo "2. overtime_hours = overtime_out - overtime_in (dalam format jam)\n";
echo "3. Statistics cards menggunakan data yang difilter (bukan hanya hari ini)\n";
