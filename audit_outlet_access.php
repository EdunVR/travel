<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== OUTLET ACCESS CONTROL AUDIT ===\n\n";

// Find all controllers
$controllerPath = __DIR__ . '/app/Http/Controllers';
$controllers = [];

function scanControllers($dir, &$controllers) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            scanControllers($path, $controllers);
        } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $controllers[] = $path;
        }
    }
}

scanControllers($controllerPath, $controllers);

echo "Found " . count($controllers) . " controllers\n\n";

$withOutletFilter = [];
$withHasOutletFilterTrait = [];
$needsImplementation = [];

foreach ($controllers as $controllerFile) {
    $content = file_get_contents($controllerFile);
    $relativePath = str_replace(__DIR__ . '/', '', $controllerFile);
    
    // Check if controller has outlet-related code
    $hasOutletFilter = (
        stripos($content, 'outlet_id') !== false ||
        stripos($content, 'outlet_filter') !== false ||
        stripos($content, 'getAccessibleOutlets') !== false ||
        stripos($content, 'whereIn(\'outlet_id\'') !== false
    );
    
    // Check if uses HasOutletFilter trait
    $usesTrait = stripos($content, 'use HasOutletFilter') !== false;
    
    if ($hasOutletFilter) {
        $withOutletFilter[] = $relativePath;
        
        if ($usesTrait) {
            $withHasOutletFilterTrait[] = $relativePath;
        } else {
            $needsImplementation[] = $relativePath;
        }
    }
}

echo "=== CONTROLLERS WITH OUTLET FILTER ===\n";
echo "Total: " . count($withOutletFilter) . "\n\n";

foreach ($withOutletFilter as $controller) {
    $usesTrait = in_array($controller, $withHasOutletFilterTrait);
    $status = $usesTrait ? '✓' : '✗';
    echo "{$status} {$controller}\n";
}

echo "\n=== SUMMARY ===\n";
echo "Controllers with outlet filter: " . count($withOutletFilter) . "\n";
echo "Using HasOutletFilter trait: " . count($withHasOutletFilterTrait) . "\n";
echo "Need implementation: " . count($needsImplementation) . "\n";

if (count($needsImplementation) > 0) {
    echo "\n=== CONTROLLERS NEEDING IMPLEMENTATION ===\n";
    foreach ($needsImplementation as $controller) {
        echo "- {$controller}\n";
    }
}

echo "\n=== DONE ===\n";
