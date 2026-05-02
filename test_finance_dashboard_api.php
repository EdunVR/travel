<?php
/**
 * Test Finance Dashboard API
 * 
 * Akses file ini via browser untuk test API endpoint:
 * http://your-domain.com/test_finance_dashboard_api.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate request
$request = Illuminate\Http\Request::create(
    '/admin/finance/dashboard/data?outlet_id=all&start_date=2024-11-01&end_date=2024-12-07',
    'GET'
);

$request->headers->set('Accept', 'application/json');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

try {
    $response = $kernel->handle($request);
    
    echo "<h1>Finance Dashboard API Test</h1>";
    echo "<h2>Status Code: " . $response->getStatusCode() . "</h2>";
    echo "<h3>Response:</h3>";
    echo "<pre>";
    echo $response->getContent();
    echo "</pre>";
    
    $kernel->terminate($request, $response);
} catch (\Exception $e) {
    echo "<h1>Error!</h1>";
    echo "<pre>";
    echo $e->getMessage();
    echo "\n\n";
    echo $e->getTraceAsString();
    echo "</pre>";
}
