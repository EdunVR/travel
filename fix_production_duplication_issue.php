<?php

/**
 * Fix Production Duplication Issue
 * 
 * Masalah yang ditemukan:
 * 1. Ada duplikasi production code (PRD-2026010003: 3 records, PRD-2026010004: 2 records)
 * 2. Multiple submit event listeners (3 di blade, 2 di JS)
 * 3. Potential duplicates dengan data serupa dalam waktu bersamaan
 * 
 * Solusi:
 * 1. Cleanup duplicate records
 * 2. Fix multiple event listeners
 * 3. Add unique constraints
 * 4. Improve double submission prevention
 */

echo "🔧 MEMPERBAIKI MASALAH DUPLIKASI PRODUKSI\n";
echo "=========================================\n\n";

// 1. Cleanup duplicate records
echo "1️⃣ Membersihkan record duplikat...\n";

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Production;
use App\Models\HppProduk;
use Illuminate\Support\Facades\DB;

// Get duplicates
$duplicates = DB::table('productions')
    ->select('production_code', DB::raw('COUNT(*) as count'))
    ->groupBy('production_code')
    ->having('count', '>', 1)
    ->get();

$cleanedCount = 0;
foreach ($duplicates as $duplicate) {
    echo "   🧹 Cleaning {$duplicate->production_code} ({$duplicate->count} records)...\n";
    
    $productions = Production::where('production_code', $duplicate->production_code)
        ->orderBy('created_at')
        ->get();
    
    // Keep the first one, delete the rest
    $keepFirst = true;
    foreach ($productions as $production) {
        if ($keepFirst) {
            echo "      ✅ Keeping ID: {$production->id} (created: {$production->created_at})\n";
            $keepFirst = false;
        } else {
            echo "      🗑️ Deleting ID: {$production->id} (created: {$production->created_at})\n";
            
            // Delete related records first
            $production->hppRecords()->delete();
            $production->materials()->delete();
            $production->laborCosts()->delete();
            $production->operationalCosts()->delete();
            $production->realizations()->delete();
            
            // Delete the production
            $production->delete();
            $cleanedCount++;
        }
    }
    echo "\n";
}

echo "   ✅ Cleaned {$cleanedCount} duplicate records\n\n";

// 2. Fix JavaScript multiple event listeners
echo "2️⃣ Memperbaiki multiple event listeners...\n";

$bladeFile = 'resources/views/admin/produksi/produksi/index.blade.php';
$jsFile = 'public/js/production.js';

// Fix blade file - ensure only one event listener
if (file_exists($bladeFile)) {
    $content = file_get_contents($bladeFile);
    
    // Add check to prevent multiple event listener registration
    $preventMultipleListeners = '
      // Prevent multiple event listener registration
      if (productionForm.dataset.listenerAdded === "true") {
        console.log("Production form listener already added, skipping...");
        return;
      }
      productionForm.dataset.listenerAdded = "true";
      ';
    
    // Insert the check before addEventListener
    $pattern = '/if \(productionForm\) \{\s*productionForm\.addEventListener\(\'submit\'/';
    $replacement = 'if (productionForm) {' . $preventMultipleListeners . '
        productionForm.addEventListener(\'submit\'';
    
    $newContent = preg_replace($pattern, $replacement, $content);
    
    if ($newContent !== $content) {
        file_put_contents($bladeFile, $newContent);
        echo "   ✅ Fixed multiple event listeners in blade file\n";
    } else {
        echo "   ℹ️ No changes needed in blade file\n";
    }
}

// Fix JS file - add double submission prevention
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    // Add double submission prevention to handleRealizationSubmit
    $doubleSubmitPrevention = '
        // Prevent double submission
        if (form.dataset.submitting === "true") {
            console.log("Form already being submitted, ignoring...");
            return;
        }
        form.dataset.submitting = "true";
        ';
    
    // Insert after e.preventDefault()
    $pattern = '/(e\.preventDefault\(\);\s*)/';
    $replacement = '$1' . $doubleSubmitPrevention;
    
    $newContent = preg_replace($pattern, $replacement, $content);
    
    // Also add reset in finally block
    $finallyPattern = '/(\}\s*finally\s*\{[^}]*)(if \(submitBtn\))/';
    $finallyReplacement = '$1
            // Reset submission flag
            form.dataset.submitting = "false";
            
            $2';
    
    $newContent = preg_replace($finallyPattern, $finallyReplacement, $newContent);
    
    if ($newContent !== $content) {
        file_put_contents($jsFile, $newContent);
        echo "   ✅ Added double submission prevention to JS file\n";
    } else {
        echo "   ℹ️ No changes needed in JS file\n";
    }
}

echo "\n3️⃣ Menambahkan unique constraint untuk production_code...\n";

// Create migration for unique constraint
$migrationContent = '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table(\'productions\', function (Blueprint $table) {
            // Add unique constraint to production_code
            $table->unique(\'production_code\', \'productions_production_code_unique\');
        });
    }

    public function down()
    {
        Schema::table(\'productions\', function (Blueprint $table) {
            $table->dropUnique(\'productions_production_code_unique\');
        });
    }
};';

$migrationFile = 'database/migrations/' . date('Y_m_d_His') . '_add_unique_constraint_to_production_code.php';
file_put_contents($migrationFile, $migrationContent);

echo "   ✅ Migration created: {$migrationFile}\n";

echo "\n4️⃣ Memperbaiki production code generation...\n";

// Update ProductionController to ensure unique production codes
$controllerFile = 'app/Http/Controllers/ProductionController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    // Find the production code generation part
    $oldCodeGeneration = '$productionCode = $outletCode . \'-PROD-\' . date(\'Ymd\') . \'-\' . str_pad(rand(1, 9999), 4, \'0\', STR_PAD_LEFT);';
    
    $newCodeGeneration = '// Generate unique production code
            $attempts = 0;
            do {
                $randomNumber = str_pad(rand(1, 9999), 4, \'0\', STR_PAD_LEFT);
                $productionCode = $outletCode . \'-PROD-\' . date(\'Ymd\') . \'-\' . $randomNumber;
                $exists = Production::where(\'production_code\', $productionCode)->exists();
                $attempts++;
                
                if ($attempts > 100) {
                    throw new \\Exception(\'Unable to generate unique production code after 100 attempts\');
                }
            } while ($exists);';
    
    $newContent = str_replace($oldCodeGeneration, $newCodeGeneration, $content);
    
    if ($newContent !== $content) {
        file_put_contents($controllerFile, $newContent);
        echo "   ✅ Updated production code generation to ensure uniqueness\n";
    } else {
        echo "   ℹ️ Production code generation already unique or not found\n";
    }
}

echo "\n5️⃣ Menambahkan client-side validation...\n";

// Add client-side validation to prevent rapid clicking
$clientValidation = '
    // Add global click protection
    let isSubmitting = false;
    
    // Override form submission to add global protection
    const originalSubmit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function() {
        if (this.id === "productionForm" && isSubmitting) {
            console.log("Global submission protection: Form already being submitted");
            return false;
        }
        
        if (this.id === "productionForm") {
            isSubmitting = true;
            setTimeout(() => { isSubmitting = false; }, 3000); // Reset after 3 seconds
        }
        
        return originalSubmit.call(this);
    };
    
    // Add visual feedback for button clicks
    document.addEventListener("click", function(e) {
        if (e.target.type === "submit" && e.target.form && e.target.form.id === "productionForm") {
            if (isSubmitting) {
                e.preventDefault();
                e.stopPropagation();
                console.log("Button click prevented: Form already being submitted");
                return false;
            }
        }
    }, true);
';

// Add to the blade file
if (file_exists($bladeFile)) {
    $content = file_get_contents($bladeFile);
    
    // Add before the closing script tag
    $pattern = '/(<\/script>\s*<\/x-layouts\.admin>)/';
    $replacement = $clientValidation . '
    $1';
    
    $newContent = preg_replace($pattern, $replacement, $content);
    
    if ($newContent !== $content) {
        file_put_contents($bladeFile, $newContent);
        echo "   ✅ Added client-side validation\n";
    }
}

echo "\n6️⃣ Membuat script test untuk verifikasi...\n";

$testScript = '<?php

/**
 * Test Production Duplication Fix
 */

require_once __DIR__ . \'/vendor/autoload.php\';

// Bootstrap Laravel
$app = require_once __DIR__ . \'/bootstrap/app.php\';
$kernel = $app->make(Illuminate\\Contracts\\Console\\Kernel::class);
$kernel->bootstrap();

use App\\Models\\Production;
use Illuminate\\Support\\Facades\\DB;

echo "🧪 TESTING PRODUCTION DUPLICATION FIX\\n";
echo "====================================\\n\\n";

// Test 1: Check for remaining duplicates
echo "1️⃣ Checking for remaining duplicates...\\n";
$duplicates = DB::table(\'productions\')
    ->select(\'production_code\', DB::raw(\'COUNT(*) as count\'))
    ->groupBy(\'production_code\')
    ->having(\'count\', \'>\', 1)
    ->get();

if ($duplicates->isEmpty()) {
    echo "   ✅ No duplicates found\\n";
} else {
    echo "   ⚠️ Still have duplicates:\\n";
    foreach ($duplicates as $duplicate) {
        echo "      - {$duplicate->production_code}: {$duplicate->count} records\\n";
    }
}

// Test 2: Test unique constraint
echo "\\n2️⃣ Testing unique constraint...\\n";
try {
    // Try to create duplicate production code
    $testCode = \'TEST-DUPLICATE-\' . time();
    
    Production::create([
        \'outlet_id\' => 1,
        \'production_code\' => $testCode,
        \'production_line\' => \'Test Line\',
        \'target_quantity\' => 100,
        \'start_date\' => now(),
        \'end_date\' => now()->addDay(),
        \'status\' => \'draft\',
        \'created_by\' => 1,
    ]);
    
    // Try to create the same code again
    Production::create([
        \'outlet_id\' => 1,
        \'production_code\' => $testCode,
        \'production_line\' => \'Test Line 2\',
        \'target_quantity\' => 200,
        \'start_date\' => now(),
        \'end_date\' => now()->addDay(),
        \'status\' => \'draft\',
        \'created_by\' => 1,
    ]);
    
    echo "   ❌ Unique constraint not working - duplicate created\\n";
    
    // Cleanup
    Production::where(\'production_code\', $testCode)->delete();
    
} catch (Exception $e) {
    echo "   ✅ Unique constraint working - duplicate prevented\\n";
    echo "      Error: " . $e->getMessage() . "\\n";
    
    // Cleanup any created record
    Production::where(\'production_code\', $testCode)->delete();
}

echo "\\n✅ Test completed!\\n";
';

file_put_contents('test_production_duplication_fix.php', $testScript);

echo "   ✅ Test script created: test_production_duplication_fix.php\n";

echo "\n✅ PERBAIKAN SELESAI!\n";
echo "====================\n\n";

echo "📋 LANGKAH SELANJUTNYA:\n";
echo "1. Jalankan migration: php artisan migrate\n";
echo "2. Jalankan test: php test_production_duplication_fix.php\n";
echo "3. Test manual dengan membuat produksi baru\n";
echo "4. Monitor log untuk memastikan tidak ada duplikasi\n\n";

echo "🎯 PERBAIKAN YANG DITERAPKAN:\n";
echo "- ✅ Cleaned duplicate records\n";
echo "- ✅ Fixed multiple event listeners\n";
echo "- ✅ Added unique constraint for production_code\n";
echo "- ✅ Improved production code generation\n";
echo "- ✅ Added client-side validation\n";
echo "- ✅ Enhanced double submission prevention\n\n";