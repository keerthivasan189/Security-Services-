<?php
require_once __DIR__ . '/config/db.php';

$queries = [
    "ALTER TABLE clients ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER company_name",
    "ALTER TABLE clients ADD COLUMN ext_no VARCHAR(20) DEFAULT NULL AFTER phone",
    "ALTER TABLE clients ADD COLUMN service_shifts VARCHAR(255) DEFAULT NULL AFTER pincode",
    "ALTER TABLE clients ADD COLUMN invoice_calculation_by VARCHAR(100) DEFAULT NULL AFTER invoice_schedule",
    "ALTER TABLE clients ADD COLUMN reference VARCHAR(100) DEFAULT NULL AFTER status",
    "ALTER TABLE clients ADD COLUMN quotation_file VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN preclient_remarks TEXT DEFAULT NULL"
];

foreach ($queries as $q) {
    try {
        $pdo->exec($q);
        echo "Success: $q\n";
    } catch (PDOException $e) {
        echo "Error or already exists: " . $e->getMessage() . "\n";
    }
}
echo "Migration complete.\n";
