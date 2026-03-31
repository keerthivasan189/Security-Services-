<?php
require_once __DIR__ . '/config/db.php';

$clientQueries = [
    "ALTER TABLE clients ADD COLUMN gst_exempted VARCHAR(10) DEFAULT 'No'",
    "ALTER TABLE clients ADD COLUMN gst_calc_method VARCHAR(50) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN sac_code VARCHAR(50) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN salary_calc_employee VARCHAR(50) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN month_denominator VARCHAR(50) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN work_order_date DATE DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN tds_avail VARCHAR(10) DEFAULT 'No'",
    "ALTER TABLE clients ADD COLUMN tds_percent DECIMAL(5,2) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN tds_on_gst_percent DECIMAL(5,2) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN epf_avail VARCHAR(10) DEFAULT 'No'",
    "ALTER TABLE clients ADD COLUMN epf_percent DECIMAL(5,2) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN epf_on_value VARCHAR(50) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN esi_avail VARCHAR(10) DEFAULT 'No'",
    "ALTER TABLE clients ADD COLUMN esi_percent DECIMAL(5,2) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN esi_on_value VARCHAR(50) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN weekly_off_for VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN company_off_days VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN hours_per_duty INT DEFAULT 8",
    "ALTER TABLE clients ADD COLUMN bill_send_by VARCHAR(50) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN grace_period INT DEFAULT 0",
    "ALTER TABLE clients ADD COLUMN service_charges_avail VARCHAR(10) DEFAULT 'No'",
    "ALTER TABLE clients ADD COLUMN service_charges_type VARCHAR(20) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN service_charges_value DECIMAL(10,2) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN doc_attach_invoice TEXT DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN prev_contractor_name VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN prev_contractor_mobile VARCHAR(20) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN strength VARCHAR(50) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN bank_account_show VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE clients ADD COLUMN bill_header VARCHAR(10) DEFAULT 'YES'",
    "ALTER TABLE clients ADD COLUMN bill_footer VARCHAR(10) DEFAULT 'YES'"
];

$tradeQueries = [
    "ALTER TABLE trades ADD COLUMN salary_basis VARCHAR(50) DEFAULT 'PRO MONTH'",
    "ALTER TABLE trades ADD COLUMN epf_amount DECIMAL(10,2) DEFAULT 0",
    "ALTER TABLE trades ADD COLUMN esi_amount DECIMAL(10,2) DEFAULT 0",
    "ALTER TABLE trades ADD COLUMN days_for_incentives VARCHAR(50) DEFAULT 'Full Month'",
    "ALTER TABLE trades ADD COLUMN attendance_incentive DECIMAL(10,2) DEFAULT 0",
    "ALTER TABLE trades ADD COLUMN remarks TEXT DEFAULT NULL"
];

$queries = array_merge($clientQueries, $tradeQueries);

foreach ($queries as $q) {
    try {
        $pdo->exec($q);
        echo "Success: $q\n";
    } catch (PDOException $e) {
        echo "Error or already exists: " . $e->getMessage() . "\n";
    }
}
echo "Migration complete.\n";
