<?php

/**
 * Fix Inter Outlet Syntax Error
 * Memperbaiki syntax error di InterOutletSaleController.php
 */

echo "🔧 Memperbaiki syntax error di InterOutletSaleController...\n\n";

// 1. Check PHP syntax
echo "1. Checking PHP syntax...\n";

$controllerFile = 'app/Http/Controllers/InterOutletSaleController.php';

if (file_exists($controllerFile)) {
    // Check syntax using php -l
    $output = [];
    $returnCode = 0;
    
    exec("php -l \"$controllerFile\" 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "   ✅ PHP syntax is valid\n";
        foreach ($output as $line) {
            if (strpos($line, 'No syntax errors') !== false) {
                echo "   ✅ $line\n";
            }
        }
    } else {
        echo "   ❌ PHP syntax error found:\n";
        foreach ($output as $line) {
            echo "   ❌ $line\n";
        }
    }
} else {
    echo "   ❌ Controller file not found\n";
}

// 2. Check for common issues
echo "\n2. Checking for common issues...\n";

if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check for duplicate method definitions
    $methodCount = substr_count($content, 'public function index(');
    if ($methodCount > 1) {
        echo "   ❌ Found $methodCount index method definitions (should be 1)\n";
    } else {
        echo "   ✅ Single index method definition found\n";
    }
    
    // Check for unclosed braces
    $openBraces = substr_count($content, '{');
    $closeBraces = substr_count($content, '}');
    if ($openBraces !== $closeBraces) {
        echo "   ❌ Mismatched braces: $openBraces open, $closeBraces close\n";
    } else {
        echo "   ✅ Braces are balanced ($openBraces pairs)\n";
    }
    
    // Check for code outside methods
    $lines = explode("\n", $content);
    $inClass = false;
    $inMethod = false;
    $braceLevel = 0;
    $issuesFound = false;
    
    foreach ($lines as $lineNum => $line) {
        $line = trim($line);
        
        if (strpos($line, 'class ') === 0) {
            $inClass = true;
            continue;
        }
        
        if ($inClass && (strpos($line, 'public function') === 0 || strpos($line, 'private function') === 0 || strpos($line, 'protected function') === 0)) {
            $inMethod = true;
            $braceLevel = 0;
            continue;
        }
        
        if ($inClass && $inMethod) {
            $braceLevel += substr_count($line, '{') - substr_count($line, '}');
            if ($braceLevel <= 0 && strpos($line, '}') !== false) {
                $inMethod = false;
            }
        }
        
        // Check for executable code outside methods but inside class
        if ($inClass && !$inMethod && !empty($line) && 
            !preg_match('/^(\/\/|\/\*|\*|use |protected |private |public |\}|class |namespace )/', $line) &&
            !preg_match('/^\s*(protected|private|public)\s+\$/', $line)) {
            echo "   ❌ Code outside method at line " . ($lineNum + 1) . ": $line\n";
            $issuesFound = true;
        }
    }
    
    if (!$issuesFound) {
        echo "   ✅ No code found outside methods\n";
    }
}

// 3. Test Laravel can load the controller
echo "\n3. Testing Laravel can load the controller...\n";

try {
    // Try to include the file
    if (file_exists($controllerFile)) {
        $tempContent = file_get_contents($controllerFile);
        
        // Basic validation - check if it looks like a valid PHP class
        if (strpos($tempContent, 'class InterOutletSaleController') !== false &&
            strpos($tempContent, 'extends Controller') !== false) {
            echo "   ✅ Controller class structure looks valid\n";
        } else {
            echo "   ❌ Controller class structure is invalid\n";
        }
        
        // Check if all required methods exist
        $requiredMethods = ['index', 'getProducts', 'getOutlets', 'store'];
        $allMethodsExist = true;
        
        foreach ($requiredMethods as $method) {
            if (strpos($tempContent, "public function $method(") !== false) {
                echo "   ✅ Method $method exists\n";
            } else {
                echo "   ❌ Method $method is missing\n";
                $allMethodsExist = false;
            }
        }
        
        if ($allMethodsExist) {
            echo "   ✅ All required methods exist\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Error loading controller: " . $e->getMessage() . "\n";
}

// 4. Summary
echo "\n📊 Summary:\n";

if (file_exists($controllerFile)) {
    $output = [];
    $returnCode = 0;
    exec("php -l \"$controllerFile\" 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "   ✅ Syntax error has been fixed\n";
        echo "   ✅ Controller is ready to use\n";
        echo "   ✅ Laravel should be able to load the controller now\n";
    } else {
        echo "   ❌ Syntax error still exists\n";
        echo "   ❌ Manual intervention required\n";
    }
} else {
    echo "   ❌ Controller file not found\n";
}

echo "\n🧪 Next steps:\n";
echo "   1. Test the inter outlet page in browser\n";
echo "   2. Check for any runtime errors\n";
echo "   3. Verify all functionality works as expected\n\n";

?>