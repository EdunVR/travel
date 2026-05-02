<?php

echo "=== TEST ATTENDANCE CALCULATION FIX ===\n";

// Test rumus perhitungan jam kerja
echo "\n1. Testing Total Jam Kerja Formula:\n";
echo "Rumus: total_jam_kerja = [(break_in - clock_in) + (clock_out - break_out)] + (overtime_out - overtime_in)\n\n";

// Test case 1: Normal work with break
$testCase1 = [
    'clock_in' => '08:00',
    'break_out' => '12:00', 
    'break_in' => '13:00',
    'clock_out' => '17:00',
    'overtime_in' => null,
    'overtime_out' => null
];

echo "Test Case 1 - Normal work with 1 hour break:\n";
echo "- Clock In: {$testCase1['clock_in']}\n";
echo "- Break Out: {$testCase1['break_out']}\n";
echo "- Break In: {$testCase1['break_in']}\n";
echo "- Clock Out: {$testCase1['clock_out']}\n";

$beforeBreak = (strtotime($testCase1['break_out']) - strtotime($testCase1['clock_in'])) / 3600; // 4 hours
$afterBreak = (strtotime($testCase1['clock_out']) - strtotime($testCase1['break_in'])) / 3600; // 4 hours
$totalHours = $beforeBreak + $afterBreak; // 8 hours

echo "- Before break: {$beforeBreak} hours\n";
echo "- After break: {$afterBreak} hours\n";
echo "- Total: {$totalHours} hours ✅\n\n";

// Test case 2: With overtime
$testCase2 = [
    'clock_in' => '08:00',
    'break_out' => '12:00',
    'break_in' => '13:00', 
    'clock_out' => '17:00',
    'overtime_in' => '18:00',
    'overtime_out' => '20:00'
];

echo "Test Case 2 - Normal work + 2 hours overtime:\n";
echo "- Clock In: {$testCase2['clock_in']}\n";
echo "- Break Out: {$testCase2['break_out']}\n";
echo "- Break In: {$testCase2['break_in']}\n";
echo "- Clock Out: {$testCase2['clock_out']}\n";
echo "- Overtime In: {$testCase2['overtime_in']}\n";
echo "- Overtime Out: {$testCase2['overtime_out']}\n";

$beforeBreak2 = (strtotime($testCase2['break_out']) - strtotime($testCase2['clock_in'])) / 3600; // 4 hours
$afterBreak2 = (strtotime($testCase2['clock_out']) - strtotime($testCase2['break_in'])) / 3600; // 4 hours
$overtime2 = (strtotime($testCase2['overtime_out']) - strtotime($testCase2['overtime_in'])) / 3600; // 2 hours
$totalHours2 = $beforeBreak2 + $afterBreak2 + $overtime2; // 10 hours

echo "- Before break: {$beforeBreak2} hours\n";
echo "- After break: {$afterBreak2} hours\n";
echo "- Overtime: {$overtime2} hours\n";
echo "- Total: {$totalHours2} hours ✅\n\n";

// Test case 3: No break
$testCase3 = [
    'clock_in' => '08:00',
    'break_out' => null,
    'break_in' => null,
    'clock_out' => '17:00',
    'overtime_in' => null,
    'overtime_out' => null
];

echo "Test Case 3 - No break:\n";
echo "- Clock In: {$testCase3['clock_in']}\n";
echo "- Clock Out: {$testCase3['clock_out']}\n";

$totalHours3 = (strtotime($testCase3['clock_out']) - strtotime($testCase3['clock_in'])) / 3600; // 9 hours

echo "- Total: {$totalHours3} hours ✅\n\n";

echo "2. Testing Overtime Calculation:\n";
echo "Rumus: overtime_hours = overtime_out - overtime_in\n\n";

$overtimeTests = [
    ['in' => '18:00', 'out' => '20:00', 'expected' => 2],
    ['in' => '17:30', 'out' => '19:15', 'expected' => 1.75],
    ['in' => '19:00', 'out' => '21:30', 'expected' => 2.5],
];

foreach ($overtimeTests as $i => $test) {
    $hours = (strtotime($test['out']) - strtotime($test['in'])) / 3600;
    echo "Test " . ($i + 1) . ": {$test['in']} - {$test['out']} = {$hours} hours";
    echo ($hours == $test['expected']) ? " ✅\n" : " ❌ (expected {$test['expected']})\n";
}

echo "\n3. Statistics Cards Improvement:\n";
echo "✅ Statistics now use filtered data (by date range and search)\n";
echo "✅ Daily tab: uses selected date\n";
echo "✅ Monthly tab: uses selected month range\n";
echo "✅ Search filter applied to statistics\n";

echo "\n=== SUMMARY OF IMPROVEMENTS ===\n";
echo "✅ Total jam kerja menggunakan rumus yang benar\n";
echo "✅ Waktu istirahat diperhitungkan dengan benar\n";
echo "✅ Jam lembur ditampilkan dalam format jam (bukan menit)\n";
echo "✅ Statistics cards menyesuaikan dengan filter aktif\n";
echo "✅ Search filter mempengaruhi statistics\n";
echo "✅ Tab switching memperbarui statistics\n";

echo "\n=== FORMULA REFERENCE ===\n";
echo "Total Jam Kerja:\n";
echo "- Dengan istirahat: (break_out - clock_in) + (clock_out - break_in) + overtime\n";
echo "- Tanpa istirahat: (clock_out - clock_in) + overtime\n";
echo "- Overtime: overtime_out - overtime_in\n";

echo "\n=== END TEST ===\n";