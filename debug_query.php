<?php
require_once 'config.php';
require_once 'core/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Simulating EmployeeController::index logic
    $status = 'active'; // Default
    $where = 'WHERE e.status = ?'; 
    $params = [$status];
    
    $query = "
        SELECT e.*, u.name AS field_officer_name,
               (SELECT c.company_name FROM clients c JOIN positions p ON p.client_id=c.id WHERE p.employee_id=e.id AND p.status='active' LIMIT 1) AS deployed_at
        FROM employees e LEFT JOIN users u ON u.id = e.field_officer_id
        $where ORDER BY e.name LIMIT 500
    ";
    
    echo "Running query with status: $status\n";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Count: " . count($results) . "\n";
    if (count($results) > 0) {
        echo "Example 1: " . $results[0]['name'] . " (ID: " . $results[0]['id'] . ")\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
