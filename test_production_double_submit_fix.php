<?php

/**
 * Test Production Double Submit Fix
 */

echo "🧪 TESTING PRODUCTION DOUBLE SUBMIT FIX\n";
echo "======================================\n\n";

// 1. Check production.js
$productionJsPath = "public/js/production.js";
$productionJsContent = file_get_contents($productionJsPath);

echo "1️⃣ Checking production.js...\n";
if (strpos($productionJsContent, "// form.addEventListener") !== false) {
    echo "   ✅ Event listener di production.js sudah di-disable\n";
} else if (strpos($productionJsContent, "form.addEventListener(\"submit\", handleFormSubmit)") !== false) {
    echo "   ❌ Event listener di production.js masih aktif\n";
} else {
    echo "   ✅ Tidak ada event listener submit di production.js\n";
}

// 2. Check index.blade.php
$indexPath = "resources/views/admin/produksi/produksi/index.blade.php";
$indexContent = file_get_contents($indexPath);

echo "\n2️⃣ Checking index.blade.php...\n";

// Check double submit protection
if (strpos($indexContent, "this.dataset.submitting === \"true\"") !== false) {
    echo "   ✅ Double submit protection sudah ditambahkan\n";
} else {
    echo "   ❌ Double submit protection belum ditambahkan\n";
}

// Check reset flag
if (strpos($indexContent, "this.dataset.submitting = \"false\"") !== false) {
    echo "   ✅ Reset submission flag sudah ditambahkan\n";
} else {
    echo "   ❌ Reset submission flag belum ditambahkan\n";
}

// Count handlers
$handlerCount = substr_count($indexContent, "productionForm.addEventListener(\"submit\"");
echo "   📊 Jumlah submit handlers: {$handlerCount}\n";

if ($handlerCount === 1) {
    echo "   ✅ Hanya ada 1 submit handler (correct)\n";
} else {
    echo "   ⚠️ Ada {$handlerCount} submit handlers (should be 1)\n";
}

echo "\n✅ Test selesai!\n";
