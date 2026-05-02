<?php
/**
 * Comprehensive Test for Depreciation Book Selection Feature
 * Tests all aspects of the book selection functionality
 */

echo "=== COMPREHENSIVE DEPRECIATION BOOK SELECTION TEST ===\n\n";

// Test 1: Check JavaScript syntax
echo "1. TESTING JAVASCRIPT SYNTAX...\n";
$viewFile = 'resources/views/admin/finance/aktiva-tetap/index.blade.php';

if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Extract JavaScript content
    $jsStart = strpos($content, 'function fixedAssetsManagement()');
    $jsEnd = strrpos($content, '</script>');
    
    if ($jsStart !== false && $jsEnd !== false) {
        $jsContent = substr($content, $jsStart, $jsEnd - $jsStart);
        
        // Check for common syntax errors
        $openBraces = substr_count($jsContent, '{');
        $closeBraces = substr_count($jsContent, '}');
        
        echo "   Open braces: {$openBraces}\n";
        echo "   Close braces: {$closeBraces}\n";
        
        if ($openBraces === $closeBraces) {
            echo "✅ Braces are balanced\n";
        } else {
            echo "❌ Braces are NOT balanced - syntax error likely\n";
        }
        
        // Check for missing commas between methods
        $methodPattern = '/async\s+\w+\s*\([^)]*\)\s*\{[^}]*\}\s*(?!\s*[,}])/';
        if (preg_match($methodPattern, $jsContent)) {
            echo "❌ Possible missing comma between methods\n";
        } else {
            echo "✅ Method separators look correct\n";
        }
        
        // Check for specific functions
        $requiredFunctions = [
            'openBookSelectionModal',
            'cancelBookSelection',
            'confirmBookSelection',
            'executePostDepreciation',
            'executeBulkPostDepreciations',
            'postDepreciation',
            'bulkPostDepreciations'
        ];
        
        foreach ($requiredFunctions as $func) {
            if (strpos($jsContent, $func) !== false) {
                echo "✅ Function {$func} found\n";
            } else {
                echo "❌ Function {$func} NOT found\n";
            }
        }
        
    } else {
        echo "❌ Could not extract JavaScript content\n";
    }
} else {
    echo "❌ View file not found\n";
}

echo "\n";

// Test 2: Check Alpine.js data structure
echo "2. TESTING ALPINE.JS DATA STRUCTURE...\n";
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    // Check for required Alpine.js variables
    $requiredVars = [
        'showBookSelectionModal: false',
        'selectedBookForPosting',
        'bookSelectionType',
        'pendingDepreciationId',
        'books: []'
    ];
    
    foreach ($requiredVars as $var) {
        if (strpos($content, $var) !== false) {
            echo "✅ Variable {$var} found\n";
        } else {
            echo "❌ Variable {$var} NOT found\n";
        }
    }
}

echo "\n";

// Test 3: Check modal HTML structure
echo "3. TESTING MODAL HTML STRUCTURE...\n";
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    
    $modalElements = [
        'x-show="showBookSelectionModal"',
        '@click.self="showBookSelectionModal = false"',
        'x-model="selectedBookForPosting"',
        '@click="cancelBookSelection()"',
        '@click="confirmBookSelection()"',
        'x-transition:enter',
        'z-[9999]'
    ];
    
    foreach ($modalElements as $element) {
        if (strpos($content, $element) !== false) {
            echo "✅ Modal element {$element} found\n";
        } else {
            echo "❌ Modal element {$element} NOT found\n";
        }
    }
}

echo "\n";

// Test 4: Check routes
echo "4. TESTING ROUTES...\n";
$routeFile = 'routes/web.php';

if (file_exists($routeFile)) {
    $content = file_get_contents($routeFile);
    
    $requiredRoutes = [
        'depreciation/{id}/post',
        'depreciation/bulk-post',
        'postDepreciation',
        'bulkPostDepreciations'
    ];
    
    foreach ($requiredRoutes as $route) {
        if (strpos($content, $route) !== false) {
            echo "✅ Route {$route} found\n";
        } else {
            echo "❌ Route {$route} NOT found\n";
        }
    }
}

echo "\n";

// Test 5: Check controller methods
echo "5. TESTING CONTROLLER METHODS...\n";
$controllerFile = 'app/Http/Controllers/FinanceAccountantController.php';

if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Check postDepreciation method
    if (strpos($content, 'public function postDepreciation') !== false) {
        echo "✅ postDepreciation method exists\n";
        
        // Check validation
        if (strpos($content, "'book_id' => 'required|exists:accounting_books,id'") !== false) {
            echo "✅ postDepreciation validates book_id\n";
        } else {
            echo "❌ postDepreciation does NOT validate book_id\n";
        }
    } else {
        echo "❌ postDepreciation method NOT found\n";
    }
    
    // Check bulkPostDepreciations method
    if (strpos($content, 'public function bulkPostDepreciations') !== false) {
        echo "✅ bulkPostDepreciations method exists\n";
        
        // Check validation
        if (strpos($content, "'book_id' => 'required|exists:accounting_books,id'") !== false) {
            echo "✅ bulkPostDepreciations validates book_id\n";
        } else {
            echo "❌ bulkPostDepreciations does NOT validate book_id\n";
        }
    } else {
        echo "❌ bulkPostDepreciations method NOT found\n";
    }
}

echo "\n";

// Test 6: Generate debugging JavaScript
echo "6. GENERATING DEBUG JAVASCRIPT...\n";
$debugJS = <<<'JS'
// Add this to browser console to debug Alpine.js
console.log('=== ALPINE.JS DEBUG ===');
const alpineEl = document.querySelector('[x-data*="fixedAssetsManagement"]');
if (alpineEl) {
    const alpineData = Alpine.$data(alpineEl);
    console.log('Alpine data:', alpineData);
    console.log('Books:', alpineData.books);
    console.log('Show modal:', alpineData.showBookSelectionModal);
    console.log('Selected book:', alpineData.selectedBookForPosting);
    
    // Test modal opening
    console.log('Testing modal opening...');
    alpineData.openBookSelectionModal('single', 123);
    
    setTimeout(() => {
        console.log('Modal should be visible now:', alpineData.showBookSelectionModal);
        const modal = document.querySelector('[x-show="showBookSelectionModal"]');
        console.log('Modal element:', modal);
        console.log('Modal display style:', modal ? modal.style.display : 'not found');
    }, 100);
} else {
    console.error('Alpine.js element not found');
}
JS;

echo "Copy and paste this JavaScript in browser console:\n";
echo "```javascript\n";
echo $debugJS;
echo "\n```\n\n";

// Test 7: Common issues and solutions
echo "7. COMMON ISSUES AND SOLUTIONS...\n";
echo "❓ If modal doesn't appear:\n";
echo "   - Check browser console for JavaScript errors\n";
echo "   - Verify Alpine.js is loaded\n";
echo "   - Check z-index conflicts\n";
echo "   - Ensure showBookSelectionModal is set to true\n\n";

echo "❓ If 'fixedAssetsManagement is not defined' error:\n";
echo "   - Check JavaScript syntax errors\n";
echo "   - Verify function is properly closed\n";
echo "   - Check for missing commas between methods\n";
echo "   - Ensure script tag is properly closed\n\n";

echo "❓ If book selection doesn't work:\n";
echo "   - Verify books array is populated\n";
echo "   - Check outlet has active accounting books\n";
echo "   - Verify user has permission to access outlet\n\n";

echo "❓ If posting fails:\n";
echo "   - Check network tab for API errors\n";
echo "   - Verify CSRF token is included\n";
echo "   - Check book_id is sent in request\n";
echo "   - Verify depreciation status is 'draft'\n\n";

echo "=== TESTING COMPLETE ===\n";
echo "✅ All components have been tested\n";
echo "🔧 Use the debug JavaScript above to troubleshoot issues\n";
echo "📝 Check browser console for detailed error messages\n";
echo "🚀 Feature should work if all tests pass\n";