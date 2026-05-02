<?php
$pdo = new PDO('mysql:host=localhost;dbname=travel', 'root', '');

$stmt = $pdo->query("SHOW TABLES LIKE 'member'");
if($stmt->rowCount() > 0) {
    echo "Table member EXISTS\n\n";
    echo "Columns:\n";
    $cols = $pdo->query('DESCRIBE member');
    foreach($cols as $col) {
        $null = $col['Null'] == 'YES' ? ' NULL' : ' NOT NULL';
        $default = $col['Default'] ? " DEFAULT '{$col['Default']}'" : '';
        echo "{$col['Field']} - {$col['Type']}{$null}{$default}\n";
    }
} else {
    echo "Table member DOES NOT EXIST\n";
}
