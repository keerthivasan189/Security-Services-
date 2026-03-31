<?php
require_once BASE_PATH . '/controllers/BaseController.php';

class PositionController extends BaseController {

    public function index(?string $param = null): void {
        $clientId = Helper::get('client_id');
        $search   = Helper::get('search');
        $desig    = Helper::get('designation');

        $where = "WHERE p.status = 'active'"; $params = [];
        if ($clientId) { $where .= ' AND p.client_id=?'; $params[] = $clientId; }
        if ($search)   { $where .= ' AND (e.name LIKE ? OR e.emp_code LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($desig)    { $where .= ' AND t.designation=?'; $params[] = $desig; }

        $positions = $this->fetchAll("
            SELECT p.*, e.name AS emp_name, e.emp_code, c.company_name,
                   t.designation, t.shift
            FROM positions p
            JOIN employees e ON e.id = p.employee_id
            JOIN clients   c ON c.id = p.client_id
            JOIN trades    t ON t.id = p.trade_id
            $where ORDER BY c.company_name, e.name
        ", $params);
        $clList = $this->allClients();
        $designations = array_column($this->fetchAll("SELECT DISTINCT designation FROM trades ORDER BY designation"), 'designation');
        $this->view('positions/index', compact('positions','clList','clientId','search','desig','designations')
            + ['pageTitle' => 'Current Positions', 'active' => 'positions']);
    }

    public function appoint(?string $param = null): void {
        $clients   = $this->fetchAll("SELECT id, company_name FROM clients WHERE status='active' ORDER BY company_name");
        $employees = $this->fetchAll("SELECT id, emp_code, name FROM employees WHERE status='active' ORDER BY name");
        $trades    = [];

        if ($cid = Helper::get('client_id')) {
            $trades = $this->fetchAll(
                "SELECT id, CONCAT(designation,' / ',shift) AS label FROM trades WHERE client_id=?",
                [$cid]
            );
        }

        if (Helper::isPost()) {
            $clientId = Helper::post('client_id');
            $tradeId  = Helper::post('trade_id');
            $empId    = Helper::post('employee_id');
            $date     = Helper::post('appointed_date') ?: date('Y-m-d');
            $remarks  = Helper::post('remarks', 'Initial Position Added');

            // Transfer any existing active position at this client
            $this->execute("
                UPDATE positions SET status='transferred', relieved_date=?
                WHERE employee_id=? AND client_id=? AND status='active'
            ", [$date, $empId, $clientId]);

            $this->execute("
                INSERT INTO positions (employee_id, trade_id, client_id, appointed_date, status, remarks)
                VALUES (?,?,?,?,'active',?)
            ", [$empId, $tradeId, $clientId, $date, $remarks]);

            Session::flash('success', 'Employee appointed successfully.');
            $this->redirect('positions/index');
        }
        $this->view('positions/appoint', compact('clients','employees','trades')
            + ['pageTitle' => 'Appointment / Transfer', 'active' => 'positions']);
    }

    public function relieve(?string $posId = null): void {
        if ($posId) {
            $this->execute(
                "UPDATE positions SET status='relieved', relieved_date=? WHERE id=?",
                [date('Y-m-d'), $posId]
            );
            Session::flash('success', 'Employee relieved successfully.');
        }
        $this->redirect('positions/index');
    }

    public function history(?string $param = null): void {
        $empId     = Helper::get('employee_id');
        $employees = $this->fetchAll("SELECT id, emp_code, name FROM employees ORDER BY name");
        $history   = [];

        if ($empId) {
            $history = $this->fetchAll("
                SELECT p.*, e.name AS emp_name, c.company_name, t.designation, t.shift
                FROM positions p
                JOIN employees e ON e.id = p.employee_id
                JOIN clients   c ON c.id = p.client_id
                JOIN trades    t ON t.id = p.trade_id
                WHERE p.employee_id = ?
                ORDER BY p.appointed_date DESC
            ", [$empId]);
        }
        $this->view('positions/history', compact('employees','history','empId')
            + ['pageTitle' => 'Transfer History', 'active' => 'positions']);
    }

    public function getTrades(?string $param = null): void {
        $clientId = Helper::get('client_id');
        $trades   = $this->fetchAll(
            "SELECT id, CONCAT(designation,' / ',shift) AS label FROM trades WHERE client_id=? ORDER BY designation",
            [$clientId]
        );
        $this->json($trades);
    }
}
