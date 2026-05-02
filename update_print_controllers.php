<?php
/**
 * Script untuk mengupdate semua controller yang menangani print
 * agar menggunakan company settings
 */

echo "=== UPDATE PRINT CONTROLLERS SCRIPT ===\n\n";

// Daftar controller yang perlu diupdate
$controllers = [
    [
        'file' => 'app/Http/Controllers/SalesManagementController.php',
        'method' => 'invoicePrint',
        'description' => 'Sales Invoice Print'
    ],
    [
        'file' => 'app/Http/Controllers/ServiceController.php', 
        'method' => 'printInvoice',
        'description' => 'Service Invoice Print'
    ],
    [
        'file' => 'app/Http/Controllers/ServiceManagementController.php',
        'method' => 'invoicePrint', 
        'description' => 'Service Management Invoice Print'
    ],
    [
        'file' => 'app/Http/Controllers/PurchaseManagementController.php',
        'method' => 'print',
        'description' => 'Purchase Order Print'
    ]
];

foreach ($controllers as $controller) {
    echo "Processing: {$controller['description']}\n";
    echo "File: {$controller['file']}\n";
    
    if (!file_exists($controller['file'])) {
        echo "❌ File not found: {$controller['file']}\n\n";
        continue;
    }
    
    $content = file_get_contents($controller['file']);
    
    // Check if HasCompanySettings trait already added
    if (strpos($content, 'use App\Traits\HasCompanySettings;') === false) {
        // Add trait import after namespace
        $content = preg_replace(
            '/^(namespace [^;]+;)/m',
            "$1\n\nuse App\Traits\HasCompanySettings;",
            $content
        );
        
        // Add trait usage in class
        $content = preg_replace(
            '/(class\s+\w+Controller\s+extends\s+Controller\s*\{)/',
            "$1\n    use HasCompanySettings;\n",
            $content
        );
        
        echo "✅ Added HasCompanySettings trait\n";
    } else {
        echo "ℹ️  HasCompanySettings trait already exists\n";
    }
    
    // Add company settings to print methods
    // This is a basic pattern - may need manual adjustment
    $patterns = [
        // Pattern for adding companySettings variable
        '/(\$pdf = Pdf::loadView\([^,]+, compact\([^)]+)\)/' => '$1, \'companySettings\')',
        '/(return view\([^,]+, compact\([^)]+)\)/' => '$1, \'companySettings\')',
        
        // Pattern for adding the line before PDF/view generation
        '/(\$pdf = Pdf::loadView)/' => '$companySettings = $this->getCompanySettingsForPrint();\n        \n        $1',
        '/(return view\([^,]+, compact)/' => '$companySettings = $this->getCompanySettingsForPrint();\n        \n        return view($1'
    ];
    
    $updated = false;
    foreach ($patterns as $pattern => $replacement) {
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replacement, $content);
            $updated = true;
        }
    }
    
    if ($updated) {
        file_put_contents($controller['file'], $content);
        echo "✅ Updated print method\n";
    } else {
        echo "ℹ️  No automatic updates applied - may need manual review\n";
    }
    
    echo "\n";
}

echo "=== MANUAL REVIEW REQUIRED ===\n";
echo "Please manually review and test each controller to ensure:\n";
echo "1. HasCompanySettings trait is properly imported and used\n";
echo "2. \$companySettings variable is passed to all print views\n";
echo "3. Print methods work correctly with new data\n";
echo "4. PDF generation includes company settings\n\n";

echo "=== EXAMPLE IMPLEMENTATION ===\n";
echo "```php\n";
echo "use App\Traits\HasCompanySettings;\n\n";
echo "class YourController extends Controller\n";
echo "{\n";
echo "    use HasCompanySettings;\n\n";
echo "    public function printInvoice(\$id)\n";
echo "    {\n";
echo "        // ... existing code ...\n\n";
echo "        \$companySettings = \$this->getCompanySettingsForPrint();\n\n";
echo "        \$pdf = Pdf::loadView('your.print.view', compact('invoice', 'companySettings'));\n";
echo "        return \$pdf->download('invoice.pdf');\n";
echo "    }\n";
echo "}\n";
echo "```\n\n";

echo "Script completed!\n";
?>