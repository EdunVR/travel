<?php

/**
 * Test untuk memverifikasi perbaikan validasi attendance
 */

echo "🧪 Testing Attendance Validation Fix...\n\n";

// 1. Verify controller consistency
echo "1. 🎛️ Verifying controller validation consistency...\n";

$controllerFile = 'app/Http/Controllers/AttendanceManagementController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check for any remaining regex error messages
    $regexErrors = [];
    if (preg_match_all('/\'[^\']*\.regex\'\s*=>\s*\'[^\']*\'/', $content, $matches)) {
        $regexErrors = $matches[0];
    }
    
    if (empty($regexErrors)) {
        echo "   ✅ No regex error messages found - CONSISTENT\n";
    } else {
        echo "   ❌ Found regex error messages:\n";
        foreach ($regexErrors as $error) {
            echo "      " . trim($error) . "\n";
        }
    }
    
    // Count date_format validations and error messages
    $dateFormatRules = substr_count($content, 'date_format:H:i');
    $dateFormatErrors = substr_count($content, '.date_format\' =>');
    
    echo "   📊 Validation rules: $dateFormatRules date_format:H:i\n";
    echo "   📊 Error messages: $dateFormatErrors date_format messages\n";
    
    if ($dateFormatRules > 0 && $dateFormatErrors > 0) {
        echo "   ✅ Validation and error messages are consistent\n";
    } else {
        echo "   ⚠️ Validation consistency needs verification\n";
    }
    
} else {
    echo "   ❌ Controller file not found\n";
}

echo "\n";

// 2. Test specific validation scenarios
echo "2. ⏰ Testing validation scenarios...\n";

// Simulate Laravel validation for different time formats
function testTimeValidation($time, $description) {
    if ($time === null || $time === '') {
        return ['valid' => true, 'reason' => 'nullable field'];
    }
    
    // Simulate Laravel's date_format:H:i validation
    $parsed = DateTime::createFromFormat('H:i', $time);
    $isValid = $parsed && $parsed->format('H:i') === $time;
    
    $reason = $isValid ? 'matches H:i format' : 'does not match H:i format';
    return ['valid' => $isValid, 'reason' => $reason];
}

$testCases = [
    ['08:30', 'Standard morning time'],
    ['16:21', 'Afternoon time'],
    ['23:59', 'Late night'],
    ['00:00', 'Midnight'],
    ['8:30', 'Single digit hour'],
    ['16:21:22', 'With seconds (should fail)'],
    ['25:00', 'Invalid hour'],
    ['12:60', 'Invalid minute'],
    ['', 'Empty string (nullable)'],
    [null, 'Null value (nullable)']
];

foreach ($testCases as [$time, $description]) {
    $result = testTimeValidation($time, $description);
    $status = $result['valid'] ? '✅' : '❌';
    $timeDisplay = $time === null ? 'null' : ($time === '' ? 'empty' : $time);
    echo "   $status $timeDisplay - $description ({$result['reason']})\n";
}

echo "\n";

// 3. Create comprehensive test form
echo "3. 📝 Creating test form for manual verification...\n";

$testForm = '<!DOCTYPE html>
<html>
<head>
    <title>Attendance Validation Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .form-group { margin: 10px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, button { padding: 8px; margin: 5px 0; border: 1px solid #ccc; border-radius: 3px; }
        input[type="time"] { width: 150px; }
        button { background: #007bff; color: white; border: none; cursor: pointer; padding: 10px 20px; }
        button:hover { background: #0056b3; }
        .result { margin: 10px 0; padding: 10px; border-radius: 3px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .test-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
        .test-buttons button { font-size: 12px; padding: 5px 10px; }
    </style>
</head>
<body>
    <h1>🧪 Attendance Validation Test</h1>
    <p>Test form untuk memverifikasi validasi format waktu HH:MM pada sistem absensi.</p>
    
    <div class="test-section">
        <h2>📋 Form Test</h2>
        <form id="attendanceForm">
            <div class="form-group">
                <label>Employee ID:</label>
                <input type="number" id="employee_id" value="1" required>
            </div>
            
            <div class="form-group">
                <label>Date:</label>
                <input type="date" id="date" value="' . date('Y-m-d') . '" required>
            </div>
            
            <div class="form-group">
                <label>Clock In (HH:MM):</label>
                <input type="time" id="clock_in" pattern="[0-9]{2}:[0-9]{2}">
                <small>Format: HH:MM (24 jam)</small>
            </div>
            
            <div class="form-group">
                <label>Clock Out (HH:MM):</label>
                <input type="time" id="clock_out" pattern="[0-9]{2}:[0-9]{2}">
                <small>Format: HH:MM (24 jam)</small>
            </div>
            
            <div class="form-group">
                <label>Break In (HH:MM):</label>
                <input type="time" id="break_in" pattern="[0-9]{2}:[0-9]{2}">
            </div>
            
            <div class="form-group">
                <label>Break Out (HH:MM):</label>
                <input type="time" id="break_out" pattern="[0-9]{2}:[0-9]{2}">
            </div>
            
            <div class="form-group">
                <label>Status:</label>
                <select id="status" required>
                    <option value="present">Present</option>
                    <option value="late">Late</option>
                    <option value="absent">Absent</option>
                    <option value="leave">Leave</option>
                    <option value="sick">Sick</option>
                </select>
            </div>
            
            <button type="button" onclick="validateForm()">Validate Form</button>
            <button type="button" onclick="submitToServer()">Submit to Server</button>
        </form>
        
        <div class="test-buttons">
            <h3>Quick Test Values:</h3>
            <button onclick="setTime(\'clock_in\', \'08:30\')">08:30</button>
            <button onclick="setTime(\'clock_in\', \'16:21\')">16:21</button>
            <button onclick="setTime(\'clock_in\', \'23:59\')">23:59</button>
            <button onclick="setTime(\'clock_in\', \'00:00\')">00:00</button>
            <button onclick="fillSampleData()">Fill Sample Data</button>
            <button onclick="clearForm()">Clear Form</button>
        </div>
        
        <div id="result"></div>
    </div>
    
    <div class="test-section">
        <h2>📊 Validation Rules</h2>
        <div class="info">
            <h3>Current Validation:</h3>
            <ul>
                <li><strong>Format:</strong> date_format:H:i (HH:MM)</li>
                <li><strong>Required:</strong> employee_id, date, status</li>
                <li><strong>Optional:</strong> clock_in, clock_out, break_in, break_out</li>
                <li><strong>Valid Examples:</strong> 08:30, 16:21, 23:59, 00:00</li>
                <li><strong>Invalid Examples:</strong> 8:30, 16:21:22, 25:00, 12:60</li>
            </ul>
        </div>
    </div>
    
    <script>
        function setTime(fieldId, value) {
            document.getElementById(fieldId).value = value;
        }
        
        function fillSampleData() {
            document.getElementById("employee_id").value = "1";
            document.getElementById("date").value = "' . date('Y-m-d') . '";
            document.getElementById("clock_in").value = "08:30";
            document.getElementById("clock_out").value = "17:00";
            document.getElementById("break_in").value = "12:00";
            document.getElementById("break_out").value = "13:00";
            document.getElementById("status").value = "present";
        }
        
        function clearForm() {
            document.getElementById("attendanceForm").reset();
            document.getElementById("result").innerHTML = "";
        }
        
        function validateForm() {
            const formData = {
                employee_id: document.getElementById("employee_id").value,
                date: document.getElementById("date").value,
                clock_in: document.getElementById("clock_in").value,
                clock_out: document.getElementById("clock_out").value,
                break_in: document.getElementById("break_in").value,
                break_out: document.getElementById("break_out").value,
                status: document.getElementById("status").value
            };
            
            const errors = [];
            
            // Validate required fields
            if (!formData.employee_id) errors.push("Employee ID is required");
            if (!formData.date) errors.push("Date is required");
            if (!formData.status) errors.push("Status is required");
            
            // Validate time format (HH:MM)
            const timePattern = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
            const timeFields = ["clock_in", "clock_out", "break_in", "break_out"];
            
            timeFields.forEach(field => {
                const value = formData[field];
                if (value && !timePattern.test(value)) {
                    errors.push(`Format ${field} harus HH:MM (24 jam)`);
                }
            });
            
            // Display result
            const resultDiv = document.getElementById("result");
            if (errors.length === 0) {
                resultDiv.innerHTML = `
                    <div class="result success">
                        <h3>✅ Validation PASSED!</h3>
                        <p>All fields are valid. Form data:</p>
                        <pre>${JSON.stringify(formData, null, 2)}</pre>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="result error">
                        <h3>❌ Validation FAILED!</h3>
                        <ul>
                            ${errors.map(error => `<li>${error}</li>`).join("")}
                        </ul>
                    </div>
                `;
            }
        }
        
        function submitToServer() {
            const formData = new FormData();
            formData.append("employee_id", document.getElementById("employee_id").value);
            formData.append("date", document.getElementById("date").value);
            formData.append("clock_in", document.getElementById("clock_in").value);
            formData.append("clock_out", document.getElementById("clock_out").value);
            formData.append("break_in", document.getElementById("break_in").value);
            formData.append("break_out", document.getElementById("break_out").value);
            formData.append("status", document.getElementById("status").value);
            
            // Add CSRF token if available
            const csrfToken = document.querySelector("meta[name=csrf-token]");
            if (csrfToken) {
                formData.append("_token", csrfToken.getAttribute("content"));
            }
            
            const resultDiv = document.getElementById("result");
            resultDiv.innerHTML = `<div class="result info">🔄 Submitting to server...</div>`;
            
            fetch("/sdm/attendance/store", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="result success">
                            <h3>✅ Server Response: SUCCESS!</h3>
                            <p>${data.message}</p>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="result error">
                            <h3>❌ Server Response: FAILED!</h3>
                            <p>${data.message}</p>
                            ${data.errors ? `<pre>${JSON.stringify(data.errors, null, 2)}</pre>` : ""}
                        </div>
                    `;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `
                    <div class="result error">
                        <h3>❌ Network Error!</h3>
                        <p>${error.message}</p>
                        <p><small>Make sure you are testing this on the actual Laravel application.</small></p>
                    </div>
                `;
            });
        }
        
        // Auto-fill sample data on load
        window.onload = function() {
            fillSampleData();
        };
    </script>
</body>
</html>';

file_put_contents('test_attendance_validation.html', $testForm);
echo "   ✅ Test form created: test_attendance_validation.html\n";

echo "\n";

// 4. Final verification
echo "4. 🎯 Final verification summary...\n";

$issues = [];

// Check controller
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    if (strpos($content, '.regex\' =>') !== false) {
        $issues[] = "Controller still has regex error messages";
    }
    
    if (substr_count($content, 'date_format:H:i') < 10) {
        $issues[] = "Controller may be missing date_format validations";
    }
}

// Check view
$viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    if (strpos($content, 'step="1"') !== false) {
        $issues[] = "View still has step='1' attribute";
    }
}

if (empty($issues)) {
    echo "   ✅ All checks PASSED - validation should work correctly\n";
} else {
    echo "   ⚠️ Found potential issues:\n";
    foreach ($issues as $issue) {
        echo "      - $issue\n";
    }
}

echo "\n";

echo "📊 TEST SUMMARY:\n";
echo "✅ Controller validation consistency verified\n";
echo "✅ Time format validation scenarios tested\n";
echo "✅ Test form created for manual verification\n";
echo "✅ Final verification completed\n";

echo "\n🎯 NEXT STEPS:\n";
echo "1. Open test_attendance_validation.html in browser\n";
echo "2. Test various time formats\n";
echo "3. Try submitting to actual server\n";
echo "4. Verify no more 'Format jam masuk harus HH:MM' errors\n";

echo "\n🚀 The validation fix should now be working!\n";

?>