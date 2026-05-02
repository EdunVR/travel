<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Outlet;
use Illuminate\Support\Facades\File;

echo "🔍 COMPREHENSIVE OUTLET FILTER SECURITY AUDIT\n";
echo "=" . str_repeat("=", 70) . "\n\n";

// Test users
$testUsers = [
    [
        'email' => 'superadmin@gmail.com',
        'description' => 'Super Admin (All Outlets)',
        'expected_outlets' => [2, 3, 4, 6]
    ],
    [
        'email' => 'Leni@gmail.com', 
        'description' => 'Limited User (Outlet 2 only)',
        'expected_outlets' => [2]
    ]
];

// Controllers to audit
$controllersToAudit = [
    'CustomerManagementController' => 'CRM Pelanggan',
    'ServiceController' => 'Service Management', 
    'ServiceManagementController' => 'Service Management Admin',
    'SalesManagementController' => 'Sales Invoice',
    'PurchaseManagementController' => 'Purchase Order',
    'BahanController' => 'Inventaris Bahan',
    'ProdukController' => 'Inventaris Produk',
    'ProductionController' => 'Produksi',
    'FinanceAccountantController' => 'Finance/Accounting',
    'InterOutletController' => 'Inter Outlet Transfer',
    'MarginReportController' => 'Margin Report',
    'SalesReportController' => 'Sales Report',
    'InventoryController' => 'Inventory Management',
    'SparepartController' => 'Sparepart Management',
    'FixedAssetController' => 'Fixed Assets',
    'PayrollController' => 'Payroll/SDM'
];

// Common patterns that indicate potential outlet filter issues
$suspiciousPatterns = [
    '::all()' => 'Using Model::all() without outlet filtering',
    '->get()' => 'Direct get() without outlet filtering',
    'whereIn(' => 'Potential outlet filtering (good)',
    'HasOutletFilter' => 'Using outlet filter trait (good)',
    'getAccessibleOutletIds' => 'Using outlet access method (good)',
    'applyOutletFilter' => 'Applying outlet filter (good)',
    'Tipe::' => 'Tipe model usage (check for outlet filtering)',
    'Member::' => 'Member model usage (check for outlet filtering)',
    'Produk::' => 'Produk model usage (check for outlet filtering)',
    'Bahan::' => 'Bahan model usage (check for outlet filtering)',
    'select(' => 'Custom select queries (check for outlet filtering)'
];

echo "📋 PHASE 1: CONTROLLER CODE ANALYSIS\n";
echo "-" . str_repeat("-", 50) . "\n";

$potentialIssues = [];

foreach ($controllersToAudit as $controllerName => $description) {
    $controllerPath = "app/Http/Controllers/{$controllerName}.php";
    
    if (!File::exists($controllerPath)) {
        echo "⚠️  Controller not found: $controllerName\n";
        continue;
    }
    
    echo "🔍 Analyzing: $description ($controllerName)\n";
    
    $content = File::get($controllerPath);
    $lines = explode("\n", $content);
    
    $issues = [];
    $goodPractices = [];
    
    foreach ($lines as $lineNum => $line) {
        foreach ($suspiciousPatterns as $pattern => $description) {
            if (strpos($line, $pattern) !== false) {
                $lineNumber = $lineNum + 1;
                
                if (in_array($pattern, ['whereIn(', 'HasOutletFilter', 'getAccessibleOutletIds', 'applyOutletFilter'])) {
                    $goodPractices[] = [
                        'line' => $lineNumber,
                        'pattern' => $pattern,
                        'description' => $description,
                        'code' => trim($line)
                    ];
                } else {
                    $issues[] = [
                        'line' => $lineNumber,
                        'pattern' => $pattern,
                        'description' => $description,
                        'code' => trim($line)
                    ];
                }
            }
        }
    }
    
    if (!empty($issues)) {
        echo "   ❌ Potential Issues Found: " . count($issues) . "\n";
        foreach (array_slice($issues, 0, 3) as $issue) {
            echo "      Line {$issue['line']}: {$issue['pattern']} - {$issue['description']}\n";
            echo "         Code: " . substr($issue['code'], 0, 80) . "...\n";
        }
        if (count($issues) > 3) {
            echo "      ... and " . (count($issues) - 3) . " more issues\n";
        }
        
        $potentialIssues[$controllerName] = [
            'description' => $description,
            'issues' => $issues,
            'good_practices' => $goodPractices
        ];
    } else {
        echo "   ✅ No obvious issues found\n";
    }
    
    if (!empty($goodPractices)) {
        echo "   ✅ Good Practices Found: " . count($goodPractices) . "\n";
    }
    
    echo "\n";
}

echo "\n📋 PHASE 2: VIEW FILE ANALYSIS\n";
echo "-" . str_repeat("-", 50) . "\n";

// Common view patterns that might indicate dropdown/modal issues
$viewPatterns = [
    '@foreach(\$tipes' => 'Tipe dropdown (check if filtered)',
    '@foreach(\$members' => 'Member dropdown (check if filtered)', 
    '@foreach(\$produks' => 'Produk dropdown (check if filtered)',
    '@foreach(\$bahans' => 'Bahan dropdown (check if filtered)',
    'x-model="formData\.id_tipe"' => 'Tipe selection in modal',
    'x-model="formData\.id_member"' => 'Member selection in modal',
    'x-model="formData\.id_produk"' => 'Produk selection in modal',
    'x-model="formData\.id_bahan"' => 'Bahan selection in modal',
    'select.*tipe' => 'Tipe select dropdown',
    'select.*member' => 'Member select dropdown'
];

$viewDirectories = [
    'resources/views/admin/crm' => 'CRM Views',
    'resources/views/admin/service' => 'Service Views',
    'resources/views/admin/penjualan' => 'Sales Views',
    'resources/views/admin/pembelian' => 'Purchase Views',
    'resources/views/admin/inventaris' => 'Inventory Views',
    'resources/views/admin/produksi' => 'Production Views',
    'resources/views/admin/finance' => 'Finance Views'
];

$viewIssues = [];

foreach ($viewDirectories as $directory => $description) {
    if (!File::isDirectory($directory)) {
        continue;
    }
    
    echo "🔍 Analyzing: $description\n";
    
    $files = File::allFiles($directory);
    $directoryIssues = [];
    
    foreach ($files as $file) {
        if ($file->getExtension() !== 'php') continue;
        
        $content = File::get($file->getPathname());
        $lines = explode("\n", $content);
        
        foreach ($lines as $lineNum => $line) {
            foreach ($viewPatterns as $pattern => $desc) {
                if (strpos($line, str_replace('\\', '', $pattern)) !== false) {
                    $directoryIssues[] = [
                        'file' => str_replace(base_path() . '/', '', $file->getPathname()),
                        'line' => $lineNum + 1,
                        'pattern' => $pattern,
                        'description' => $desc,
                        'code' => trim($line)
                    ];
                }
            }
        }
    }
    
    if (!empty($directoryIssues)) {
        echo "   ⚠️  Potential Issues: " . count($directoryIssues) . "\n";
        $viewIssues[$directory] = $directoryIssues;
        
        // Show first few issues
        foreach (array_slice($directoryIssues, 0, 2) as $issue) {
            echo "      {$issue['file']}:{$issue['line']} - {$issue['description']}\n";
        }
        if (count($directoryIssues) > 2) {
            echo "      ... and " . (count($directoryIssues) - 2) . " more\n";
        }
    } else {
        echo "   ✅ No obvious issues found\n";
    }
    
    echo "\n";
}

echo "\n📋 PHASE 3: PRIORITY ISSUES IDENTIFICATION\n";
echo "-" . str_repeat("-", 50) . "\n";

$highPriorityControllers = [];

foreach ($potentialIssues as $controller => $data) {
    $riskScore = 0;
    
    // Calculate risk score based on patterns found
    foreach ($data['issues'] as $issue) {
        switch ($issue['pattern']) {
            case '::all()':
                $riskScore += 10; // High risk
                break;
            case 'Tipe::':
            case 'Member::':
            case 'Produk::':
            case 'Bahan::':
                $riskScore += 5; // Medium risk
                break;
            default:
                $riskScore += 2; // Low risk
        }
    }
    
    // Reduce risk score for good practices
    $riskScore -= count($data['good_practices']) * 3;
    
    if ($riskScore > 10) {
        $highPriorityControllers[$controller] = [
            'data' => $data,
            'risk_score' => $riskScore
        ];
    }
}

// Sort by risk score
uasort($highPriorityControllers, function($a, $b) {
    return $b['risk_score'] - $a['risk_score'];
});

echo "🚨 HIGH PRIORITY CONTROLLERS (Risk Score > 10):\n\n";

if (empty($highPriorityControllers)) {
    echo "✅ No high-priority issues found!\n";
} else {
    foreach ($highPriorityControllers as $controller => $info) {
        echo "❌ $controller ({$info['data']['description']}) - Risk Score: {$info['risk_score']}\n";
        
        // Show top issues
        $topIssues = array_slice($info['data']['issues'], 0, 3);
        foreach ($topIssues as $issue) {
            echo "   Line {$issue['line']}: {$issue['description']}\n";
            echo "      " . substr($issue['code'], 0, 100) . "...\n";
        }
        echo "\n";
    }
}

echo "\n📋 PHASE 4: RECOMMENDED NEXT ACTIONS\n";
echo "-" . str_repeat("-", 50) . "\n";

if (!empty($highPriorityControllers)) {
    echo "🎯 IMMEDIATE ACTIONS NEEDED:\n\n";
    
    $actionCount = 1;
    foreach (array_slice($highPriorityControllers, 0, 5, true) as $controller => $info) {
        echo "$actionCount. Fix $controller ({$info['data']['description']})\n";
        echo "   - Risk Score: {$info['risk_score']}\n";
        echo "   - Issues: " . count($info['data']['issues']) . "\n";
        echo "   - Focus on: Model::all() calls and dropdown filtering\n\n";
        $actionCount++;
    }
} else {
    echo "✅ All controllers appear to have proper outlet filtering!\n";
}

echo "\n🎯 SUMMARY\n";
echo "=" . str_repeat("=", 70) . "\n";
echo "📊 Controllers Analyzed: " . count($controllersToAudit) . "\n";
echo "🚨 High Priority Issues: " . count($highPriorityControllers) . "\n";
echo "⚠️  View Files with Potential Issues: " . count($viewIssues) . "\n";
echo "\n";

if (!empty($highPriorityControllers)) {
    echo "🔥 NEXT TASK: Fix " . array_keys($highPriorityControllers)[0] . "\n";
    echo "📋 This controller has the highest risk score and should be addressed first.\n";
} else {
    echo "🎉 GREAT NEWS: No critical outlet filtering issues found!\n";
    echo "📋 All controllers appear to have proper security measures in place.\n";
}

echo "\n🎯 AUDIT COMPLETE!\n";