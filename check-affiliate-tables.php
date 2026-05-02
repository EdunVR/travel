<?php
$pdo = new PDO('mysql:host=localhost;dbname=travel', 'root', '');
$tables = [
    'affiliate_cookies',
    'affiliate_clicks', 
    'affiliate_referrals',
    'affiliate_package_commissions',
    'affiliate_payouts',
    'affiliate_settings'
];

echo "Checking affiliate tables:\n";
foreach($tables as $table) {
    $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
    $exists = $stmt->rowCount() > 0;
    echo "$table: " . ($exists ? 'EXISTS' : 'MISSING') . "\n";
}
