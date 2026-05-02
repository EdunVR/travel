<?php
/**
 * Test Script untuk Verifikasi Perbaikan Modal Scroll Purchase Order
 * Script ini memverifikasi bahwa perbaikan efektif telah diterapkan
 */

echo "=== VERIFIKASI PERBAIKAN MODAL SCROLL PO ===\n\n";

$file_path = 'resources/views/admin/pembelian/purchase-order/index.blade.php';

if (!file_exists($file_path)) {
    echo "❌ File tidak ditemukan: $file_path\n";
    exit(1);
}

$content = file_get_contents($file_path);

echo "1. Checking CSS Classes...\n";

// Check untuk CSS classes yang diperlukan
$required_classes = [
    'modal-scroll-container' => 'Container dengan flexbox layout',
    'modal-header' => 'Header yang tidak menyusut',
    'modal-content' => 'Content area yang scrollable',
    'modal-footer' => 'Footer yang tidak menyusut',
    'modal-overlay' => 'Overlay dengan overflow hidden'
];

$all_classes_found = true;
foreach ($required_classes as $class => $description) {
    $count = substr_count($content, $class);
    if ($count > 0) {
        echo "   ✅ $class found ($count times) - $description\n";
    } else {
        echo "   ❌ $class not found - $description\n";
        $all_classes_found = false;
    }
}

echo "\n2. Checking CSS Styles...\n";

// Check untuk CSS styles yang diperlukan
$required_styles = [
    'max-height: 90vh' => 'Modal height constraint',
    'display: flex' => 'Flexbox layout',
    'flex-direction: column' => 'Vertical layout',
    'overflow-y: auto' => 'Vertical scrolling',
    'flex-shrink: 0' => 'Prevent shrinking',
    '-webkit-overflow-scrolling: touch' => 'iOS smooth scrolling'
];

$all_styles_found = true;
foreach ($required_styles as $style => $description) {
    if (strpos($content, $style) !== false) {
        echo "   ✅ $style found - $description\n";
    } else {
        echo "   ❌ $style not found - $description\n";
        $all_styles_found = false;
    }
}

echo "\n3. Checking Modal Structure...\n";

// Check struktur modal yang benar
$modal_patterns = [
    'Modal Payment PO' => [
        'showPaymentModal.*modal-overlay',
        'modal-scroll-container',
        'modal-header.*Pembayaran Purchase Order',
        'modal-content.*space-y-4',
        'modal-footer.*Batal.*Proses Pembayaran'
    ],
    'Modal Payment History' => [
        'showPaymentHistoryModal.*modal-overlay',
        'modal-scroll-container',
        'modal-header.*Riwayat Pembayaran PO',
        'modal-content',
        'modal-footer.*Tutup'
    ],
    'Modal Bukti Pembayaran' => [
        'showBuktiModal.*modal-overlay',
        'modal-scroll-container',
        'modal-header.*Bukti Pembayaran',
        'modal-content',
        'modal-footer.*Download.*Tutup'
    ],
    'Modal View Bukti Transfer' => [
        'showPaymentProofViewer.*modal-overlay',
        'modal-scroll-container',
        'modal-header.*Bukti Transfer',
        'modal-content',
        'modal-footer.*Tutup'
    ]
];

$all_modals_correct = true;
foreach ($modal_patterns as $modal_name => $patterns) {
    echo "   Checking $modal_name:\n";
    $modal_correct = true;
    
    foreach ($patterns as $pattern) {
        if (preg_match("/$pattern/s", $content)) {
            echo "      ✅ Pattern found: $pattern\n";
        } else {
            echo "      ❌ Pattern missing: $pattern\n";
            $modal_correct = false;
            $all_modals_correct = false;
        }
    }
    
    if ($modal_correct) {
        echo "   ✅ $modal_name structure correct\n";
    } else {
        echo "   ❌ $modal_name structure incorrect\n";
    }
    echo "\n";
}

echo "4. Checking Custom Scrollbar CSS...\n";

$scrollbar_styles = [
    '::-webkit-scrollbar' => 'Custom scrollbar width',
    '::-webkit-scrollbar-track' => 'Scrollbar track styling',
    '::-webkit-scrollbar-thumb' => 'Scrollbar thumb styling',
    '::-webkit-scrollbar-thumb:hover' => 'Scrollbar hover effect'
];

$scrollbar_found = true;
foreach ($scrollbar_styles as $style => $description) {
    if (strpos($content, $style) !== false) {
        echo "   ✅ $style found - $description\n";
    } else {
        echo "   ❌ $style not found - $description\n";
        $scrollbar_found = false;
    }
}

echo "\n5. Backup Verification...\n";

if (file_exists($file_path . '.backup2')) {
    echo "   ✅ Backup file exists: {$file_path}.backup2\n";
} else {
    echo "   ⚠️  No backup file found\n";
}

echo "\n6. File Size Check...\n";
$file_size = filesize($file_path);
$file_size_kb = round($file_size / 1024, 2);
echo "   📊 File size: {$file_size_kb} KB\n";

if ($file_size > 100000) { // > 100KB
    echo "   ✅ File size reasonable for complex modal structure\n";
} else {
    echo "   ⚠️  File might be too small, check if all content is present\n";
}

echo "\n=== SUMMARY ===\n";

$overall_status = $all_classes_found && $all_styles_found && $all_modals_correct && $scrollbar_found;

if ($overall_status) {
    echo "🎉 ALL CHECKS PASSED!\n";
    echo "✅ CSS classes implemented correctly\n";
    echo "✅ CSS styles applied properly\n";
    echo "✅ Modal structures updated\n";
    echo "✅ Custom scrollbar styling added\n";
    echo "\n";
    echo "🚀 READY FOR BROWSER TESTING!\n";
    echo "\n";
    echo "📋 Next Steps:\n";
    echo "1. Open Purchase Order page in browser\n";
    echo "2. Find PO with 'Vendor Bill' status\n";
    echo "3. Click 'Bayar' button\n";
    echo "4. Test scroll functionality:\n";
    echo "   - Desktop: Verify smooth scrolling\n";
    echo "   - Laptop: Check button visibility\n";
    echo "   - Tablet: Test touch scroll\n";
    echo "   - Mobile: Verify responsive layout\n";
    echo "5. Test all modal types\n";
    echo "6. Verify custom scrollbar appears\n";
} else {
    echo "❌ SOME CHECKS FAILED!\n";
    echo "\n";
    echo "Issues found:\n";
    if (!$all_classes_found) echo "- Missing required CSS classes\n";
    if (!$all_styles_found) echo "- Missing required CSS styles\n";
    if (!$all_modals_correct) echo "- Incorrect modal structure\n";
    if (!$scrollbar_found) echo "- Missing custom scrollbar CSS\n";
    echo "\n";
    echo "🔧 MANUAL REVIEW REQUIRED\n";
    echo "Check the file manually and ensure all changes are applied correctly.\n";
}

echo "\n";
echo "📞 Support:\n";
echo "- Documentation: PO_PAYMENT_MODAL_EFFECTIVE_SCROLL_FIX.md\n";
echo "- Deployment: deploy_po_payment_modal_effective_fix.bat\n";
echo "- Rollback: Use backup2 file if needed\n";
echo "\n";

if ($overall_status) {
    exit(0); // Success
} else {
    exit(1); // Failure
}
?>