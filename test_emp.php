<?php
require_once 'config.php';
require_once 'core/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $where = 'WHERE e.status = ?'; 
    $params = ['active'];
    
    $stmt = $db->prepare("
        SELECT e.*, u.name AS field_officer_name,
               (SELECT c.company_name FROM clients c JOIN positions p ON p.client_id=c.id WHERE p.employee_id=e.id AND p.status='active' LIMIT 1) AS deployed_at
        FROM employees e LEFT JOIN users u ON u.id = e.field_officer_id
        $where ORDER BY e.name LIMIT 500
    ");
    $stmt->execute($params);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($res) . " active employees.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
