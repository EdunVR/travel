<?php

echo "=== TESTING PERMINTAAN BARANG MODAL FIXES ===\n\n";

// Check if all required files exist
$requiredFiles = [
    'resources/views/admin/supply-chain/permintaan-barang/index.blade.php',
    'resources/views/admin/supply-chain/permintaan-barang/modals/create.blade.php',
    'resources/views/admin/supply-chain/permintaan-barang/modals/detail.blade.php',
    'resources/views/admin/supply-chain/permintaan-barang/modals/edit.blade.php',
    'resources/views/admin/supply-chain/permintaan-barang/modals/approval.blade.php',
    'resources/views/admin/supply-chain/permintaan-barang/modals/reject.blade.php'
];

echo "1. Checking Required Files:\n";
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists\n";
    } else {
        echo "❌ $file missing\n";
    }
}

echo "\n2. Checking Modal Functions in Index View:\n";
$indexContent = file_get_contents('resources/views/admin/supply-chain/permintaan-barang/index.blade.php');

$requiredFunctions = [
    'closeModal()' => 'Universal close modal function',
    'openApprovalModal(item)' => 'Open approval modal function',
    'openRejectModal(item)' => 'Open reject modal function',
    'editItem(item)' => 'Edit item function',
    'showDetail(item)' => 'Show detail function'
];

foreach ($requiredFunctions as $function => $description) {
    if (strpos($indexContent, $function) !== false) {
        echo "✅ $description found\n";
    } else {
        echo "❌ $description missing\n";
    }
}

echo "\n3. Checking Button Click Events:\n";
$buttonChecks = [
    '@click="openApprovalModal(item)"' => 'Approval button click event',
    '@click="openRejectModal(item)"' => 'Reject button click event',
    '@click="editItem(item)"' => 'Edit button click event',
    '@click="showDetail(item)"' => 'Detail button click event'
];

foreach ($buttonChecks as $event => $description) {
    if (strpos($indexContent, $event) !== false) {
        echo "✅ $description found\n";
    } else {
        echo "❌ $description missing\n";
    }
}

echo "\n4. Checking Modal Close Functions:\n";
$modalFiles = [
    'resources/views/admin/supply-chain/permintaan-barang/modals/edit.blade.php' => 'Edit modal',
    'resources/views/admin/supply-chain/permintaan-barang/modals/detail.blade.php' => 'Detail modal',
    'resources/views/admin/supply-chain/permintaan-barang/modals/approval.blade.php' => 'Approval modal',
    'resources/views/admin/supply-chain/permintaan-barang/modals/reject.blade.php' => 'Reject modal'
];

foreach ($modalFiles as $file => $description) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, 'closeModal()') !== false) {
            echo "✅ $description has closeModal() function\n";
        } else {
            echo "❌ $description missing closeModal() function\n";
        }
    }
}

echo "\n5. Checking Alpine.js Data Properties:\n";
$dataProperties = [
    'showCreateModal: false' => 'Create modal state',
    'showDetailModal: false' => 'Detail modal state',
    'showEditModal: false' => 'Edit modal state',
    'showApprovalModal: false' => 'Approval modal state',
    'showRejectModal: false' => 'Reject modal state'
];

foreach ($dataProperties as $property => $description) {
    if (strpos($indexContent, $property) !== false) {
        echo "✅ $description found\n";
    } else {
        echo "❌ $description missing\n";
    }
}

echo "\n6. Checking Function Name Conflicts:\n";
$conflictChecks = [
    'showApprovalModal(item)' => 'Old conflicting function name (should not exist)',
    'showRejectModal(item)' => 'Old conflicting function name (should not exist)'
];

foreach ($conflictChecks as $conflict => $description) {
    if (strpos($indexContent, $conflict) === false) {
        echo "✅ $description properly removed\n";
    } else {
        echo "❌ $description still exists (conflict!)\n";
    }
}

echo "\n=== MODAL FIX TEST COMPLETE ===\n";