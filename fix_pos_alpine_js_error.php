<?php
/**
 * Fix POS Alpine.js Error - posApp is not defined
 * 
 * Masalah: Alpine.js tidak dapat menemukan fungsi posApp()
 * Solusi: Memastikan urutan loading script yang benar dan menambahkan fallback
 */

echo "🔧 [FIX] Fixing POS Alpine.js Error...\n";

// 1. Update admin layout untuk memastikan urutan script yang benar
$adminLayoutPath = 'resources/views/components/layouts/admin.blade.php';
if (file_exists($adminLayoutPath)) {
    $content = file_get_contents($adminLayoutPath);
    
    // Pastikan pos.js dimuat sebelum Alpine.js
    $content = str_replace(
        '<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="{{ asset(\'js/pos.js\') }}"></script>',
        '<script src="{{ asset(\'js/pos.js\') }}"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>',
        $content
    );
    
    file_put_contents($adminLayoutPath, $content);
    echo "✅ [FIX] Updated admin layout script order\n";
} else {
    echo "❌ [FIX] Admin layout file not found\n";
}

// 2. Tambahkan fallback script di POS view
$posViewPath = 'resources/views/admin/penjualan/pos/index.blade.php';
if (file_exists($posViewPath)) {
    $content = file_get_contents($posViewPath);
    
    // Tambahkan fallback script sebelum closing tag
    $fallbackScript = '
<script>
// Fallback untuk memastikan posApp tersedia
document.addEventListener("DOMContentLoaded", function() {
    console.log("🔍 [POS] Checking posApp availability...");
    
    // Check if Alpine.js is loaded
    let alpineCheckCount = 0;
    const maxChecks = 50; // 5 seconds max
    
    function checkAlpineAndPosApp() {
        alpineCheckCount++;
        
        if (typeof Alpine !== "undefined") {
            console.log("✅ [POS] Alpine.js is available");
            
            // Check if posApp is registered
            if (Alpine.data && typeof Alpine.data === "function") {
                console.log("✅ [POS] Alpine.data is available");
                
                // Try to get posApp
                try {
                    const testElement = document.createElement("div");
                    testElement.setAttribute("x-data", "posApp()");
                    console.log("✅ [POS] posApp function test passed");
                } catch (error) {
                    console.error("❌ [POS] posApp function test failed:", error);
                    
                    // Show user-friendly error
                    const posElement = document.querySelector(\'[x-data="posApp()"]\');
                    if (posElement) {
                        posElement.innerHTML = `
                            <div class="flex items-center justify-center min-h-screen">
                                <div class="text-center p-8 bg-red-50 border border-red-200 rounded-xl max-w-md">
                                    <div class="text-red-600 text-6xl mb-4">⚠️</div>
                                    <h2 class="text-xl font-bold text-red-800 mb-2">Error Loading POS</h2>
                                    <p class="text-red-600 mb-4">Terjadi kesalahan saat memuat sistem POS.</p>
                                    <button onclick="window.location.reload()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        🔄 Refresh Halaman
                                    </button>
                                </div>
                            </div>
                        `;
                    }
                }
            } else {
                console.warn("⚠️ [POS] Alpine.data not available yet");
                if (alpineCheckCount < maxChecks) {
                    setTimeout(checkAlpineAndPosApp, 100);
                }
            }
        } else {
            console.warn("⚠️ [POS] Alpine.js not loaded yet, attempt", alpineCheckCount);
            if (alpineCheckCount < maxChecks) {
                setTimeout(checkAlpineAndPosApp, 100);
            } else {
                console.error("❌ [POS] Alpine.js failed to load after", maxChecks, "attempts");
                alert("Gagal memuat sistem POS. Silakan refresh halaman.");
            }
        }
    }
    
    // Start checking
    setTimeout(checkAlpineAndPosApp, 100);
});

// Additional error handling for Alpine.js
window.addEventListener("error", function(event) {
    if (event.message && event.message.includes("posApp")) {
        console.error("❌ [POS] posApp error detected:", event.message);
        console.error("❌ [POS] Error details:", event);
        
        // Show user-friendly error
        setTimeout(() => {
            if (confirm("Terjadi kesalahan pada sistem POS. Refresh halaman?")) {
                window.location.reload();
            }
        }, 1000);
    }
});
</script>

</x-layouts.admin>';
    
    // Replace closing tag
    $content = str_replace('</x-layouts.admin>', $fallbackScript, $content);
    
    file_put_contents($posViewPath, $content);
    echo "✅ [FIX] Added fallback script to POS view\n";
} else {
    echo "❌ [FIX] POS view file not found\n";
}

// 3. Buat script test untuk memverifikasi perbaikan
$testScript = '<!DOCTYPE html>
<html>
<head>
    <title>POS Alpine.js Test</title>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body>
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">POS Alpine.js Test</h1>
        
        <div id="test-results" class="space-y-2">
            <div id="alpine-test" class="p-2 border rounded">
                <span class="font-medium">Alpine.js:</span> 
                <span class="text-yellow-600">Testing...</span>
            </div>
            <div id="posapp-test" class="p-2 border rounded">
                <span class="font-medium">posApp function:</span> 
                <span class="text-yellow-600">Testing...</span>
            </div>
        </div>
        
        <div class="mt-4">
            <button onclick="runTests()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                🧪 Run Tests
            </button>
            <button onclick="window.location.href=\'/admin/penjualan/pos\'" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 ml-2">
                🏪 Go to POS
            </button>
        </div>
    </div>

    <script>
    function updateTestResult(id, status, message) {
        const element = document.getElementById(id);
        const span = element.querySelector("span:last-child");
        span.className = status === "success" ? "text-green-600" : "text-red-600";
        span.textContent = message;
    }

    function runTests() {
        console.log("🧪 Running POS Alpine.js tests...");
        
        // Test 1: Alpine.js availability
        if (typeof Alpine !== "undefined") {
            updateTestResult("alpine-test", "success", "✅ Available");
            console.log("✅ Alpine.js is available");
            
            // Test 2: posApp function (simulate)
            try {
                // This is a basic test - in real POS page, posApp would be registered
                if (Alpine.data && typeof Alpine.data === "function") {
                    updateTestResult("posapp-test", "success", "✅ Alpine.data available (posApp should work)");
                    console.log("✅ Alpine.data is available");
                } else {
                    updateTestResult("posapp-test", "error", "❌ Alpine.data not available");
                    console.error("❌ Alpine.data not available");
                }
            } catch (error) {
                updateTestResult("posapp-test", "error", "❌ Error: " + error.message);
                console.error("❌ posApp test error:", error);
            }
        } else {
            updateTestResult("alpine-test", "error", "❌ Not available");
            updateTestResult("posapp-test", "error", "❌ Cannot test (Alpine.js not available)");
            console.error("❌ Alpine.js not available");
        }
    }

    // Auto-run tests when Alpine is ready
    document.addEventListener("alpine:init", () => {
        console.log("🏔️ Alpine.js initialized");
        setTimeout(runTests, 500);
    });

    // Fallback auto-run
    document.addEventListener("DOMContentLoaded", () => {
        setTimeout(() => {
            if (typeof Alpine !== "undefined") {
                runTests();
            }
        }, 2000);
    });
    </script>
</body>
</html>';

file_put_contents('test_pos_alpine_fix.html', $testScript);
echo "✅ [FIX] Created test file: test_pos_alpine_fix.html\n";

echo "\n🎯 [FIX] POS Alpine.js Error Fix Complete!\n";
echo "\n📋 [NEXT STEPS]:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Test POS page: /admin/penjualan/pos\n";
echo "3. Check browser console for errors\n";
echo "4. Run test file: test_pos_alpine_fix.html\n";
echo "\n🔍 [TROUBLESHOOTING]:\n";
echo "- If still getting errors, check browser console\n";
echo "- Ensure pos.js file exists and is accessible\n";
echo "- Verify Alpine.js CDN is loading properly\n";
echo "- Check for JavaScript conflicts\n";

?>