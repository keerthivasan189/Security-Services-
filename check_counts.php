<?php
$h = 'localhost'; $u = 'root'; $p = ''; $d = 'hrms_saktheeswari';
try {
    $pdo = new PDO("mysql:host=$h;dbname=$d", $u, $p);
    $stmt = $pdo->query("SELECT status, COUNT(*) as qty FROM employees GROUP BY status");
    while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $r['status'] . ": " . $r['qty'] . "\n";
    }
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage();
}
?>
