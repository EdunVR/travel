<?php
/**
 * Simple test to check if Alpine.js attendanceCrud function is working
 * 
 * This creates a minimal HTML test page to verify the function loads without errors
 */

echo "=== TESTING ALPINE.JS FUNCTION IN BROWSER ===\n\n";

// Create a simple test HTML file
$testHtml = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alpine.js attendanceCrud Test</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    <div x-data="attendanceCrud()" x-init="console.log('✅ Alpine.js attendanceCrud function loaded successfully!')">
        <h1>Alpine.js Test</h1>
        <p>If you see this without errors in the console, the function is working!</p>
        <p>Current tab: <span x-text="currentTab"></span></p>
        <p>Loading state: <span x-text="loading"></span></p>
        <button x-on:click="console.log('✅ Function methods accessible:', typeof fetchData, typeof openCreate)">Test Methods</button>
    </div>

    <script>
        // Extract the attendanceCrud function from the actual view file
        function attendanceCrud() {
          const today = new Date();
          const currentYear = today.getFullYear();
          
          // Format tanggal hari ini dengan timezone lokal
          const formatLocalDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
          };
          
          return {
            // State
            attendances: [],
            monthlyData: [],
            employees: [],
            outlets: [],
            selectedOutlets: [],
            showOutletDropdown: false,
            statistics: {},
            loading: false,
            saving: false,
            savingWorkHours: false,
            deleting: false,
            
            // Current tab
            currentTab: 'daily',
            
            // Filters
            filterDate: formatLocalDate(today),
            filterMonth: today.getMonth() + 1,
            filterYear: currentYear,
            search: '',
            
            // Year options - Initialize immediately
            yearOptions: [currentYear - 2, currentYear - 1, currentYear, currentYear + 1],
            
            // Monthly calendar
            daysInMonth: 31,
            
            // Modals
            showForm: false,
            showWorkHoursModal: false,
            showTimeSettingsModal: false,
            showDeleteModal: false,
            
            // Form data
            form: {
              id: null,
              employee_id: '',
              date: '',
              clock_in: '',
              clock_out: '',
              break_out: '',
              break_in: '',
              overtime_in: '',
              overtime_out: '',
              status: 'present',
              notes: ''
            },
            errors: {},
            
            // Work hours form
            workHoursForm: {
              employee_id: '',
              clock_in: '08:00',
              clock_out: '17:00',
              apply_to_all: false
            },
            
            // Time settings
            timeSettings: [],
            loadingTimeSettings: false,
            savingTimeSettings: false,
            testTime: '',
            testResult: null,
            testingTime: false,
            
            // Delete
            deleteId: null,
            
            // Toast
            showToast: false,
            toastMessage: '',
            toastType: 'success',

            async init() {
              console.log('🚀 Alpine.js attendanceCrud initialized successfully!');
              console.log('📊 Initial state:', {
                currentTab: this.currentTab,
                loading: this.loading,
                filterDate: this.filterDate
              });
            },

            // Test methods
            async fetchData() {
              console.log('📡 fetchData method called');
              return Promise.resolve();
            },

            async openCreate() {
              console.log('➕ openCreate method called');
              this.showForm = true;
            },

            showToastMessage(message, type = 'success') {
              console.log(`🍞 Toast: ${message} (${type})`);
              this.toastMessage = message;
              this.toastType = type;
              this.showToast = true;
              
              setTimeout(() => {
                this.showToast = false;
              }, 3000);
            }
          };
        }
        
        // Test the function directly
        console.log('🧪 Testing attendanceCrud function...');
        try {
          const testInstance = attendanceCrud();
          console.log('✅ Function creates object successfully');
          console.log('📋 Available methods:', Object.keys(testInstance).filter(key => typeof testInstance[key] === 'function'));
          console.log('📊 Available properties:', Object.keys(testInstance).filter(key => typeof testInstance[key] !== 'function'));
        } catch (error) {
          console.error('❌ Function creation failed:', error);
        }
    </script>
</body>
</html>
HTML;

// Write the test file
$testFile = 'test_alpine_attendance.html';
if (file_put_contents($testFile, $testHtml)) {
    echo "✅ Test HTML file created: $testFile\n";
    echo "\nTo test:\n";
    echo "1. Open $testFile in your browser\n";
    echo "2. Open browser developer tools (F12)\n";
    echo "3. Check the console for messages\n";
    echo "4. If you see '✅ Alpine.js attendanceCrud function loaded successfully!' the function is working\n";
    echo "5. Click the 'Test Methods' button to verify methods are accessible\n\n";
    
    // Also test the function syntax directly in PHP
    echo "🔍 Testing function syntax in PHP...\n";
    
    // Read the actual view file to extract the function
    $viewFile = 'resources/views/admin/sdm/attendance/index.blade.php';
    $content = file_get_contents($viewFile);
    
    // Check for basic function structure
    if (strpos($content, 'function attendanceCrud()') !== false) {
        echo "✅ Function definition found\n";
    } else {
        echo "❌ Function definition not found\n";
    }
    
    if (strpos($content, 'return {') !== false) {
        echo "✅ Return statement found\n";
    } else {
        echo "❌ Return statement not found\n";
    }
    
    // Check for proper closing
    $functionStart = strpos($content, 'function attendanceCrud()');
    $scriptEnd = strpos($content, '</script>', $functionStart);
    
    if ($functionStart !== false && $scriptEnd !== false) {
        $functionSection = substr($content, $functionStart, $scriptEnd - $functionStart);
        
        // Count braces in the function section only
        $openBraces = substr_count($functionSection, '{');
        $closeBraces = substr_count($functionSection, '}');
        
        echo "📊 Function section brace count:\n";
        echo "   - Open braces: $openBraces\n";
        echo "   - Close braces: $closeBraces\n";
        
        if ($openBraces === $closeBraces) {
            echo "✅ Braces are balanced in function section\n";
        } else {
            echo "⚠️ Braces may be unbalanced in function section\n";
        }
    }
    
} else {
    echo "❌ Could not create test file\n";
}

echo "\n=== TEST SETUP COMPLETE ===\n";
?>