<?php
require_once BASE_PATH . '/controllers/BaseController.php';

class DashboardController extends BaseController {

    public function index(?string $param = null): void {
        $lastMonth = date('Y-m', strtotime('-1 month'));

        $activeEmployees = (int) $this->pdo->query("SELECT COUNT(*) FROM employees WHERE status='active'")->fetchColumn();
        $preEmployees    = (int) $this->pdo->query("SELECT COUNT(*) FROM employees WHERE status='pre_employee'")->fetchColumn();
        $inactiveEmployees = (int) $this->pdo->query("SELECT COUNT(*) FROM employees WHERE status='inactive'")->fetchColumn();
        $totalEmployees  = (int) $this->pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
        $activeClientsCount = (int) $this->pdo->query("SELECT COUNT(*) FROM clients  WHERE status='active'")->fetchColumn();
        $totalClients    = (int) $this->pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(grand_total),0) FROM invoices WHERE invoice_month=?");
        $stmt->execute([$lastMonth]);
        $invoiceValue = (float) $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM invoices WHERE invoice_month=?");
        $stmt->execute([$lastMonth]);
        $invoiceCount = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM invoice_payments WHERE payment_type='received' AND DATE_FORMAT(payment_date,'%Y-%m')=?");
        $stmt->execute([$lastMonth]);
        $receivedAmt = (float) $stmt->fetchColumn();

        $balanceAmt = (float) $this->pdo->query("SELECT COALESCE(SUM(total_outstanding),0) FROM invoices WHERE payment_status != 'paid'")->fetchColumn();

        $recentInvoices = $this->pdo->query("
            SELECT i.id, i.invoice_no, c.company_name, i.invoice_month,
                   i.grand_total, i.payment_status
            FROM invoices i
            JOIN clients c ON c.id = i.client_id
            ORDER BY i.created_at DESC
            LIMIT 10
        ")->fetchAll();

        $scheduleGroups = $this->pdo->query("
            SELECT invoice_schedule, COUNT(*) AS cnt
            FROM clients
            WHERE status='active' AND invoice_schedule IS NOT NULL AND invoice_schedule != ''
            GROUP BY invoice_schedule
            ORDER BY cnt DESC
        ")->fetchAll();

        $this->view('dashboard/index', compact(
            'activeEmployees','preEmployees','inactiveEmployees','totalEmployees','activeClientsCount','totalClients',
            'invoiceValue','invoiceCount','receivedAmt','balanceAmt',
            'recentInvoices','scheduleGroups','lastMonth'
        ) + ['pageTitle' => 'Admin Dashboard', 'active' => 'dashboard']);
    }
}
