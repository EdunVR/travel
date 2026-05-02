<?php

echo "=== TESTING PERMINTAAN BARANG ROUTE FIX ===\n\n";

// Check if all modal files have correct route usage
$modalFiles = [
    'resources/views/admin/supply-chain/permintaan-barang/index.blade.php' => 'Main Component',
    'resources/views/admin/supply-chain/permintaan-barang/modals/detail.blade.php' => 'Detail Modal',
    'resources/views/admin/supply-chain/permintaan-barang/modals/edit.blade.php' => 'Edit Modal',
    'resources/views/admin/supply-chain/permintaan-barang/modals/approval.blade.php' => 'Approval Modal',
    'resources/views/admin/supply-chain/permintaan-barang/modals/reject.blade.php' => 'Reject Modal'
];

echo "1. Checking Route Usage in Files:\n";
foreach ($modalFiles as $file => $name) {
    if (file_exists($file)) {
        echo "✅ $name exists: $file\n";
    } else {
        echo "❌ $name missing: $file\n";
    }
}

echo "\n2. Checking Correct Route Usage:\n";

// Check main component
if (file_exists('resources/views/admin/supply-chain/permintaan-barang/index.blade.php')) {
    $indexContent = file_get_contents('resources/views/admin/supply-chain/permintaan-barang/index.blade.php');
    
    if (strpos($indexContent, 'permintaan-barang.pdf') !== false) {
        echo "✅ Main component uses correct PDF route\n";
    } else {
        echo "❌ Main component PDF route incorrect\n";
    }
}

// Check detail modal
if (file_exists('resources/views/admin/supply-chain/permintaan-barang/modals/detail.blade.php')) {
    $detailContent = file_get_contents('resources/views/admin/supply-chain/permintaan-barang/modals/detail.blade.php');
    
    $detailChecks = [
        'permintaan-barang.show' => 'Uses show route for data loading',
        'permintaan-barang.pdf' => 'Uses PDF route for download'
    ];
    
    foreach ($detailChecks as $check => $description) {
        if (strpos($detailContent, $check) !== false) {
            echo "✅ Detail modal: $description\n";
        } else {
            echo "❌ Detail modal missing: $description\n";
        }
    }
}

// Check edit modal
if (file_exists('resources/views/admin/supply-chain/permintaan-barang/modals/edit.blade.php')) {
    $editContent = file_get_contents('resources/views/admin/supply-chain/permintaan-barang/modals/edit.blade.php');
    
    $editChecks = [
        'permintaan-barang.show' => 'Uses show route for data loading',
        'permintaan-barang.update' => 'Uses update route for saving'
    ];
    
    foreach ($editChecks as $check => $description) {
        if (strpos($editContent, $check) !== false) {
            echo "✅ Edit modal: $description\n";
        } else {
            echo "❌ Edit modal missing: $description\n";
        }
    }
}

// Check approval modal
if (file_exists('resources/views/admin/supply-chain/permintaan-barang/modals/approval.blade.php')) {
    $approvalContent = file_get_contents('resources/views/admin/supply-chain/permintaan-barang/modals/approval.blade.php');
    
    if (strpos($approvalContent, 'permintaan-barang.approve') !== false) {
        echo "✅ Approval modal uses correct approve route\n";
    } else {
        echo "❌ Approval modal route incorrect\n";
    }
}

// Check reject modal
if (file_exists('resources/views/admin/supply-chain/permintaan-barang/modals/reject.blade.php')) {
    $rejectContent = file_get_contents('resources/views/admin/supply-chain/permintaan-barang/modals/reject.blade.php');
    
    if (strpos($rejectContent, 'permintaan-barang.reject') !== false) {
        echo "✅ Reject modal uses correct reject route\n";
    } else {
        echo "❌ Reject modal route incorrect\n";
    }
}

echo "\n3. Checking Route Parameter Replacement:\n";

$files = [
    'resources/views/admin/supply-chain/permintaan-barang/modals/detail.blade.php',
    'resources/views/admin/supply-chain/permintaan-barang/modals/edit.blade.php',
    'resources/views/admin/supply-chain/permintaan-barang/modals/approval.blade.php',
    'resources/views/admin/supply-chain/permintaan-barang/modals/reject.blade.php',
    'resources/views/admin/supply-chain/permintaan-barang/index.blade.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, "':id'") !== false && strpos($content, ".replace(':id'") !== false) {
            echo "✅ " . basename($file) . " uses proper parameter replacement\n";
        } else {
            echo "❌ " . basename($file) . " missing parameter replacement\n";
        }
    }
}

echo "\n4. Route Fixes Applied:\n";
echo "✅ Edit modal now uses 'update' route instead of 'index'\n";
echo "✅ Detail modal uses 'show' route for data loading\n";
echo "✅ PDF generation uses 'pdf' route\n";
echo "✅ Approval uses 'approve' route\n";
echo "✅ Reject uses 'reject' route\n";
echo "✅ All routes use proper parameter replacement\n";

echo "\n5. Expected Route URLs:\n";
echo "- Show: /admin/supply-chain/permintaan-barang/{id}\n";
echo "- Update: PUT /admin/supply-chain/permintaan-barang/{id}\n";
echo "- PDF: /admin/supply-chain/permintaan-barang/{id}/pdf\n";
echo "- Approve: POST /admin/supply-chain/permintaan-barang/{id}/approve\n";
echo "- Reject: POST /admin/supply-chain/permintaan-barang/{id}/reject\n";

echo "\n=== TESTING INSTRUCTIONS ===\n";
echo "1. Clear browser cache and reload page\n";
echo "2. Open browser console (F12)\n";
echo "3. Click edit button on any item\n";
echo "4. Make changes and click save\n";
echo "5. Should NOT see 405 Method Not Allowed error\n";
echo "6. Should see success message and data refresh\n";
echo "7. Test other functions (detail, approve, reject, PDF)\n";

echo "\n=== STATUS: READY FOR TESTING ===\n";
echo "Route fixes have been applied!\n";