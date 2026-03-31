<?php
require_once dirname(__DIR__) . '/config/db.php';
$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
try {
    $pdo->exec("ALTER TABLE master_districts ADD COLUMN IF NOT EXISTS state_id INT AFTER name");
    $pdo->exec("ALTER TABLE master_taluks ADD COLUMN IF NOT EXISTS district_id INT AFTER name");
    $pdo->exec("ALTER TABLE master_towns ADD COLUMN IF NOT EXISTS taluk_id INT AFTER name");
    echo "Success: Columns added or already exist.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
