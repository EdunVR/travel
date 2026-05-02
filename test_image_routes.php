<?php

// Test route list
echo "Testing Image Routes\n";
echo "====================\n\n";

// Check if routes exist
$routes = [
    'POST /produk/{productId}/images/set-primary',
    'DELETE /produk/{productId}/images/remove',
];

foreach ($routes as $route) {
    echo "Route: $route\n";
}

echo "\n\nTest URLs:\n";
echo "Set Primary: POST /produk/1/images/set-primary\n";
echo "Remove Image: DELETE /produk/1/images/remove\n";
