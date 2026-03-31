<?php
// Minimal test - no session, no DB, no includes
echo "<!DOCTYPE html><html><body>";
echo "<h1 style='color:green'>HRMS is reachable!</h1>";
echo "<p>Now testing database...</p>";

// Test DB
$host = 'localhost'; $db = 'hrms_saktheeswari'; $user = 'root'; $pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    echo "<p style='color:green'>✓ Database connected!</p>";
    $count = $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
    echo "<p>Employees in DB: $count</p>";
} catch(Exception $e) {
    echo "<p style='color:orange'>DB not connected: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please import config/install.sql first</p>";
}

echo "<hr><p><a href='index.php'>Go to main app</a></p>";
echo "</body></html>";
