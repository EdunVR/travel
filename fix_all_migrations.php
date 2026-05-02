<?php

/**
 * Fix all migrations with foreign key dependency issues
 * Run with: php fix_all_migrations.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "Fixing all migration files...\n\n";

$migrationsPath = database_path('migrations');
$files = File::files($migrationsPath);

$fixedCount = 0;

foreach ($files as $file) {
    $content = File::get($file);
    $originalContent = $content;
    
    // Pattern 1: Fix foreign keys in Schema::create
    $pattern1 = '/(\$table->foreign\([^)]+\)->references\([^)]+\)->on\([^)]+\)(?:->onDelete\([^)]+\))?;)/';
    if (preg_match_all($pattern1, $content, $matches)) {
        // Extract the Schema::create block
        if (preg_match('/Schema::create\([^,]+,\s*function\s*\([^)]+\)\s*{(.*?)}\);/s', $content, $createMatch)) {
            $createBlock = $createMatch[1];
            $foreignKeys = [];
            
            // Extract all foreign key definitions
            foreach ($matches[0] as $fk) {
                $foreignKeys[] = $fk;
                $createBlock = str_replace($fk, '', $createBlock);
            }
            
            if (!empty($foreignKeys)) {
                // Build the new structure with conditional foreign keys
                $newCreateBlock = $createBlock;
                $afterCreate = "\n        \n        // Add foreign keys only if referenced tables exist\n";
                $afterCreate .= "        Schema::table('{table_name}', function (Blueprint \$table) {\n";
                
                foreach ($foreignKeys as $fk) {
                    // Extract table name from foreign key
                    if (preg_match('/->on\([\'"]([^\'"]+)[\'"]\)/', $fk, $tableMatch)) {
                        $refTable = $tableMatch[1];
                        $afterCreate .= "            if (Schema::hasTable('$refTable')) {\n";
                        $afterCreate .= "                $fk\n";
                        $afterCreate .= "            }\n";
                    }
                }
                
                $afterCreate .= "        });\n";
                
                // Replace in content
                $content = preg_replace(
                    '/Schema::create\(([^,]+),\s*function\s*\([^)]+\)\s*{.*?}\);/s',
                    "Schema::create($1, function (Blueprint \$table) {" . $newCreateBlock . "        });$afterCreate",
                    $content,
                    1
                );
            }
        }
    }
    
    // Only write if content changed
    if ($content !== $originalContent) {
        // Backup original
        File::copy($file, $file . '.backup');
        File::put($file, $content);
        echo "✓ Fixed: " . basename($file) . "\n";
        $fixedCount++;
    }
}

echo "\n✓ Fixed $fixedCount migration files\n";
echo "Backups created with .backup extension\n";
