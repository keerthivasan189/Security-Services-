<?php
require_once dirname(__DIR__) . '/config/db.php';
$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
$tables = ['master_states', 'master_districts', 'master_taluks', 'master_towns'];
foreach ($tables as $t) {
    echo "Table: $t\n";
    $stmt = $pdo->query("DESCRIBE `$t`");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['Field']} - {$row['Type']}\n";
    }
    echo "\n";
}
