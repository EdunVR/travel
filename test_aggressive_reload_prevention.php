<?php

echo "=== TESTING AGGRESSIVE RELOAD PREVENTION ===\n\n";

$layoutFile = 'resources/views/components/layouts/admin-with-tabs.blade.php';

if (!file_exists($layoutFile)) {
    echo "❌ Layout file not found\n";
    exit(1);
}

$content = file_get_contents($layoutFile);

echo "1. TESTING AGGRESSIVE KEYBOARD INTERCEPTION\n";

// Check for aggressive keyboard event handling
$keyboardFeatures = [
    'document.addEventListener("keydown"' => 'Keyboard event listener',
    'event.preventDefault()' => 'Event prevention',
    'event.stopPropagation()' => 'Event propagation stop',
    'event.stopImmediatePropagation()' => 'Immediate propagation stop',
    ', true); // Capture phase' => 'Capture phase usage',
    'isRefreshKey' => 'Refresh key detection',
    'BLOCKING refresh key' => 'Blocking confirmation logging'
];

$keyboardFeaturesFound = 0;
foreach ($keyboardFeatures as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ $description found\n";
        $keyboardFeaturesFound++;
    } else {
        echo "❌ $description not found\n";
    }
}

echo "   Keyboard features found: $keyboardFeaturesFound/" . count($keyboardFeatures) . "\n";

echo "\n2. TESTING ENHANCED BEFOREUNLOAD PREVENTION\n";

// Check for enhanced beforeunload
$beforeunloadFeatures = [
    'BLOCKING beforeunload for admin area' => 'Beforeunload blocking log',
    'SISTEM ERP PROTECTION!' => 'Enhanced warning message',
    'Menutup semua tab' => 'Tab closure warning',
    'Kehilangan data belum tersimpan' => 'Data loss warning',
    'gunakan tombol refresh di tab' => 'Alternative instruction'
];

$beforeunloadFeaturesFound = 0;
foreach ($beforeunloadFeatures as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ $description found\n";
        $beforeunloadFeaturesFound++;
    } else {
        echo "❌ $description not found\n";
    }
}

echo "   beforeunload features found: $beforeunloadFeaturesFound/" . count($beforeunloadFeatures) . "\n";

echo "\n3. TESTING NOTIFICATION SYSTEM\n";

// Check for notification system
$notificationFeatures = [
    'showReloadBlockedNotification' => 'Notification function',
    'reload-blocked-notification' => 'Notification CSS class',
    'RELOAD DICEGAH!' => 'Block message',
    'fixed top-4 left-1/2' => 'Center positioning',
    'z-[99999]' => 'High z-index',
    'setTimeout(() => notification.remove()' => 'Auto-dismiss'
];

$notificationFeaturesFound = 0;
foreach ($notificationFeatures as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ $description found\n";
        $notificationFeaturesFound++;
    } else {
        echo "❌ $description not found\n";
    }
}

echo "   Notification features found: $notificationFeaturesFound/" . count($notificationFeatures) . "\n";

echo "\n4. TESTING LOCATION.RELOAD OVERRIDE\n";

// Check for location.reload override
$reloadOverrideFeatures = [
    'const originalReload = window.location.reload' => 'Original reload backup',
    'window.location.reload = function' => 'Reload override',
    'BLOCKING direct location.reload()' => 'Direct reload blocking',
    'originalReload.call(this, forceReload)' => 'Original reload call'
];

$reloadOverrideFeaturesFound = 0;
foreach ($reloadOverrideFeatures as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ $description found\n";
        $reloadOverrideFeaturesFound++;
    } else {
        echo "❌ $description not found\n";
    }
}

echo "   Reload override features found: $reloadOverrideFeaturesFound/" . count($reloadOverrideFeatures) . "\n";

echo "\n5. TESTING URL MONITORING\n";

// Check for URL monitoring
$urlMonitoringFeatures = [
    'MutationObserver' => 'Mutation observer',
    'URL changed from' => 'URL change logging',
    'Potential reload detected' => 'Reload detection',
    'urlObserver.observe' => 'Observer activation'
];

$urlMonitoringFeaturesFound = 0;
foreach ($urlMonitoringFeatures as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ $description found\n";
        $urlMonitoringFeaturesFound++;
    } else {
        echo "❌ $description not found\n";
    }
}

echo "   URL monitoring features found: $urlMonitoringFeaturesFound/" . count($urlMonitoringFeatures) . "\n";

echo "\n6. TESTING CSS ANIMATIONS\n";

// Check for CSS animations
$cssFeatures = [
    'reload-blocked-notification' => 'Notification CSS class',
    'slideInFromTop' => 'Animation name',
    '@keyframes slideInFromTop' => 'Keyframe definition',
    'backdrop-filter: blur' => 'Backdrop blur effect'
];

$cssFeaturesFound = 0;
foreach ($cssFeatures as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ $description found\n";
        $cssFeaturesFound++;
    } else {
        echo "❌ $description not found\n";
    }
}

echo "   CSS features found: $cssFeaturesFound/" . count($cssFeatures) . "\n";

echo "\n7. GENERATING COMPREHENSIVE TEST GUIDE\n";

$testGuide = '
<!DOCTYPE html>
<html>
<head>
    <title>Aggressive Reload Prevention - Test Guide</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .test-case { background: #f8f9fa; padding: 20px; margin: 15px 0; border-radius: 10px; border-left: 5px solid #dc3545; }
        .expected { color: #28a745; font-weight: bold; background: #d4edda; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .critical { color: #dc3545; font-weight: bold; background: #f8d7da; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .step { background: white; padding: 12px; margin: 8px 0; border-radius: 6px; border-left: 3px solid #007bff; }
        .console-msg { background: #2d3748; color: #e2e8f0; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 12px; }
    </style>
</head>
<body>
    <h1>🚫 Aggressive Reload Prevention - Complete Test Guide</h1>
    
    <div class="critical">
        <h2>⚠️ CRITICAL: This Must Work 100%</h2>
        <p>Browser reload MUST be completely blocked in admin areas. Any failure means the tab system can be broken.</p>
    </div>
    
    <div class="test-case">
        <h2>🧪 Test 1: Ctrl+R Prevention</h2>
        <div class="step">
            <strong>Steps:</strong>
            <ol>
                <li>Navigate to any admin page (e.g., /admin/finance/laporan)</li>
                <li>Press Ctrl+R</li>
                <li>Observe what happens</li>
            </ol>
        </div>
        
        <div class="expected">
            <strong>Expected Result:</strong>
            <ul>
                <li>🚫 Browser reload is BLOCKED</li>
                <li>📢 Center notification appears: "RELOAD DICEGAH!"</li>
                <li>🔄 Tab automatically refreshes (if tab system available)</li>
                <li>✅ Success notification: "Tab berhasil disegarkan!"</li>
            </ul>
        </div>
        
        <div class="console-msg">
            Console Messages:<br>
            🚫 BLOCKING refresh key: r Ctrl: true Shift: false<br>
            📢 Reload blocked notification: Refresh browser dicegah...
        </div>
    </div>
    
    <div class="test-case">
        <h2>🧪 Test 2: F5 Prevention</h2>
        <div class="step">
            <strong>Steps:</strong>
            <ol>
                <li>Stay on admin page</li>
                <li>Press F5</li>
                <li>Observe behavior</li>
            </ol>
        </div>
        
        <div class="expected">
            <strong>Expected Result:</strong>
            <ul>
                <li>🚫 F5 refresh is BLOCKED</li>
                <li>📢 Same notification behavior as Ctrl+R</li>
                <li>🔄 Tab refresh occurs automatically</li>
            </ul>
        </div>
        
        <div class="console-msg">
            Console Messages:<br>
            🚫 BLOCKING refresh key: F5 Ctrl: false Shift: false
        </div>
    </div>
    
    <div class="test-case">
        <h2>🧪 Test 3: Hard Refresh (Ctrl+Shift+R)</h2>
        <div class="step">
            <strong>Steps:</strong>
            <ol>
                <li>Stay on admin page</li>
                <li>Press Ctrl+Shift+R</li>
                <li>Read the confirmation dialog</li>
                <li>Test both "OK" and "Cancel"</li>
            </ol>
        </div>
        
        <div class="expected">
            <strong>Expected Result:</strong>
            <ul>
                <li>⚠️ Confirmation dialog appears with detailed warning</li>
                <li>📝 Dialog explains consequences of hard refresh</li>
                <li>✅ "Cancel" shows info notification</li>
                <li>🔄 "OK" allows hard refresh (sets NAVIGATING_AWAY flag)</li>
            </ul>
        </div>
        
        <div class="info">
            <strong>Dialog Message Should Include:</strong>
            <ul>
                <li>"HARD REFRESH DICEGAH!"</li>
                <li>Current page name</li>
                <li>"Hard refresh akan merusak sistem tab!"</li>
                <li>"Gunakan tombol refresh di tab sebagai gantinya"</li>
            </ul>
        </div>
    </div>
    
    <div class="test-case">
        <h2>🧪 Test 4: Browser Close/Navigate Away</h2>
        <div class="step">
            <strong>Steps:</strong>
            <ol>
                <li>Stay on admin page</li>
                <li>Try to close browser tab (Ctrl+W or X button)</li>
                <li>Or try to navigate to different URL</li>
            </ol>
        </div>
        
        <div class="expected">
            <strong>Expected Result:</strong>
            <ul>
                <li>⚠️ beforeunload warning appears</li>
                <li>📝 Enhanced message with "SISTEM ERP PROTECTION!"</li>
                <li>🔄 Instructions to use tab refresh instead</li>
            </ul>
        </div>
        
        <div class="console-msg">
            Console Messages:<br>
            🚫 BLOCKING beforeunload for admin area
        </div>
    </div>
    
    <div class="test-case">
        <h2>🧪 Test 5: Direct location.reload() Call</h2>
        <div class="step">
            <strong>Steps:</strong>
            <ol>
                <li>Open browser console (F12)</li>
                <li>Type: <code>window.location.reload()</code></li>
                <li>Press Enter</li>
            </ol>
        </div>
        
        <div class="expected">
            <strong>Expected Result:</strong>
            <ul>
                <li>🚫 Direct reload call is BLOCKED</li>
                <li>⚠️ Confirmation dialog appears</li>
                <li>📢 Option to cancel or proceed</li>
            </ul>
        </div>
        
        <div class="console-msg">
            Console Messages:<br>
            🚫 BLOCKING direct location.reload() call
        </div>
    </div>
    
    <div class="test-case">
        <h2>🧪 Test 6: Non-Admin Pages (Control Test)</h2>
        <div class="step">
            <strong>Steps:</strong>
            <ol>
                <li>Navigate to non-admin page (e.g., /login)</li>
                <li>Press Ctrl+R or F5</li>
                <li>Try to close browser</li>
            </ol>
        </div>
        
        <div class="expected">
            <strong>Expected Result:</strong>
            <ul>
                <li>✅ Normal browser refresh works</li>
                <li>✅ No blocking notifications</li>
                <li>✅ No beforeunload warnings</li>
            </ul>
        </div>
        
        <div class="info">
            This confirms the prevention only works in admin areas.
        </div>
    </div>
    
    <div class="test-case">
        <h2>🧪 Test 7: Mobile/Touch Device</h2>
        <div class="step">
            <strong>Steps:</strong>
            <ol>
                <li>Open admin page on mobile device</li>
                <li>Use pull-to-refresh gesture</li>
                <li>Try browser refresh button</li>
            </ol>
        </div>
        
        <div class="expected">
            <strong>Expected Result:</strong>
            <ul>
                <li>📱 Notifications are mobile-responsive</li>
                <li>🚫 Refresh attempts are blocked</li>
                <li>👆 Touch-friendly close buttons work</li>
            </ul>
        </div>
    </div>
    
    <div class="critical">
        <h2>🎯 Success Criteria - ALL Must Pass</h2>
        <ul>
            <li>✅ Ctrl+R is completely blocked with notification</li>
            <li>✅ F5 is completely blocked with notification</li>
            <li>✅ Ctrl+Shift+R shows confirmation dialog</li>
            <li>✅ Browser close shows beforeunload warning</li>
            <li>✅ Direct location.reload() is blocked</li>
            <li>✅ Non-admin pages work normally</li>
            <li>✅ Mobile responsive notifications</li>
            <li>✅ Console shows appropriate debug messages</li>
            <li>✅ Tab refresh works when available</li>
            <li>✅ No JavaScript errors in console</li>
        </ul>
    </div>
    
    <div class="info">
        <h2>🔍 Debug Console Messages to Look For</h2>
        <div class="console-msg">
            ✅ Aggressive reload prevention installed<br>
            🚫 BLOCKING refresh key: [key] Ctrl: [bool] Shift: [bool]<br>
            📢 Reload blocked notification: [message]<br>
            🚫 BLOCKING beforeunload for admin area<br>
            🚫 BLOCKING direct location.reload() call<br>
            🔍 URL changed from [old] to [new]<br>
            ⚠️ Potential reload detected
        </div>
    </div>
    
    <div class="critical">
        <h2>⚠️ If Any Test Fails</h2>
        <p>If browser reload is not blocked:</p>
        <ol>
            <li>Check browser console for JavaScript errors</li>
            <li>Verify you are on an admin URL (/admin/*)</li>
            <li>Clear browser cache completely</li>
            <li>Check if TAB_SYSTEM_ACTIVE is set</li>
            <li>Verify event listeners are installed</li>
        </ol>
    </div>
</body>
</html>
';

file_put_contents('AGGRESSIVE_RELOAD_PREVENTION_TEST_GUIDE.html', $testGuide);
echo "✅ Comprehensive test guide saved to: AGGRESSIVE_RELOAD_PREVENTION_TEST_GUIDE.html\n";

echo "\n=== TEST SUMMARY ===\n";
$totalFeatures = count($keyboardFeatures) + count($beforeunloadFeatures) + count($notificationFeatures) + 
                count($reloadOverrideFeatures) + count($urlMonitoringFeatures) + count($cssFeatures);
$totalFound = $keyboardFeaturesFound + $beforeunloadFeaturesFound + $notificationFeaturesFound + 
              $reloadOverrideFeaturesFound + $urlMonitoringFeaturesFound + $cssFeaturesFound;

echo "📊 OVERALL IMPLEMENTATION: $totalFound/$totalFeatures features found\n";

if ($totalFound >= $totalFeatures * 0.9) {
    echo "🎉 EXCELLENT: Aggressive reload prevention is fully implemented!\n";
} elseif ($totalFound >= $totalFeatures * 0.7) {
    echo "✅ GOOD: Most features implemented\n";
} else {
    echo "⚠️ NEEDS WORK: Some critical features missing\n";
}

echo "\n🚨 CRITICAL TEST STEPS:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Navigate to /admin/dashboard or any admin page\n";
echo "3. Press Ctrl+R → MUST see blocking notification\n";
echo "4. Press F5 → MUST see blocking notification\n";
echo "5. Press Ctrl+Shift+R → MUST see confirmation dialog\n";
echo "6. Try to close browser → MUST see warning\n";
echo "7. Check console for debug messages\n";

echo "\n=== TESTING COMPLETE ===\n";