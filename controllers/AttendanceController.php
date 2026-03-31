<?php
require_once BASE_PATH . '/controllers/BaseController.php';

class AttendanceController extends BaseController {

    public function index(?string $param = null): void {
        $this->redirect('attendance/bulk');
    }

    /* ─── Bulk Attendance Entry ──────────────────────────────────────── */
    public function bulk(?string $param = null): void {
        $clients      = $this->pdo->query("SELECT id,company_name FROM clients WHERE status='active' ORDER BY company_name")->fetchAll();
        $fieldOfficers = $this->pdo->query("SELECT id,name FROM users WHERE role='field_officer' ORDER BY name")->fetchAll();
        $designations  = array_column($this->fetchAll("SELECT DISTINCT designation FROM employees WHERE designation IS NOT NULL AND designation != '' ORDER BY designation"), 'designation');

        // All filters come via GET (form uses method="GET")
        $selectedClient      = Helper::get('client_id', '');
        $selectedDate        = Helper::get('from_date', date('Y-m-d'));
        $selectedDesignation = Helper::get('designation', '');
        $selectedOfficer     = Helper::get('field_officer_id', '');
        $empSearch           = Helper::get('emp_search', '');

        /* Build employee query with all filters */
        $where  = "WHERE e.status = 'active'";
        $params = [];

        if ($selectedClient) {
            $where .= " AND e.id IN (SELECT employee_id FROM positions WHERE client_id=? AND status='active')";
            $params[] = $selectedClient;
        }
        if ($selectedDesignation) {
            $where .= " AND e.designation = ?";
            $params[] = $selectedDesignation;
        }
        if ($selectedOfficer) {
            $where .= " AND e.field_officer_id = ?";
            $params[] = $selectedOfficer;
        }
        if ($empSearch) {
            $where .= " AND (e.name LIKE ? OR e.emp_code LIKE ?)";
            $params[] = "%$empSearch%"; $params[] = "%$empSearch%";
        }

        /* Join attendance for the selected date, and get trade/client info */
        $s = $this->pdo->prepare("
            SELECT e.id, e.name, e.emp_code, e.designation,
                   p.trade_id,
                   t.shift,
                   c.id AS client_id,
                   c.company_name AS client_name,
                   a.status AS att_status
            FROM employees e
            LEFT JOIN positions p ON p.employee_id=e.id AND p.status='active'
                AND (? = '' OR p.client_id = ?)
            LEFT JOIN trades t ON t.id = p.trade_id
            LEFT JOIN clients c ON c.id = p.client_id
            LEFT JOIN attendance a ON a.employee_id=e.id AND a.att_date=?
                AND (? = '' OR a.client_id = ?)
            $where
            ORDER BY e.designation, e.name
        ");
        // Bind the client filter params for the JOIN conditions
        $allParams = array_merge([$selectedClient, $selectedClient, $selectedDate, $selectedClient, $selectedClient], $params);
        $s->execute($allParams);
        $employees = $s->fetchAll();

        /* Handle save */
        if (Helper::isPost() && isset($_POST['save_attendance'])) {
            $empIds   = $_POST['emp_id']   ?? [];
            $statuses = $_POST['status']   ?? [];
            $tradeIds = $_POST['trade_id'] ?? [];
            $clientIds = $_POST['client_id_row'] ?? [];
            $from     = new DateTime($_POST['from_date']);
            $to       = new DateTime($_POST['to_date']);
            $dates    = [];
            for ($d = clone $from; $d <= $to; $d->modify('+1 day')) {
                $dates[] = $d->format('Y-m-d');
            }
            $stmt = $this->pdo->prepare("
                INSERT INTO attendance (employee_id,client_id,trade_id,att_date,status,marked_by)
                VALUES (?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE status=VALUES(status), marked_by=VALUES(marked_by)
            ");
            foreach ($dates as $dt) {
                foreach ($empIds as $i => $empId) {
                    $stmt->execute([$empId, $clientIds[$i] ?: null, $tradeIds[$i] ?: null, $dt, $statuses[$i] ?? 'P', Session::userId()]);
                }
            }
            Session::flash('success', 'Attendance saved for ' . count($dates) . ' day(s) × ' . count($empIds) . ' employees.');
            // Redirect back preserving filters
            $qs = http_build_query(array_filter([
                'client_id' => $selectedClient, 'from_date' => $_POST['from_date'],
                'designation' => $selectedDesignation, 'field_officer_id' => $selectedOfficer,
                'emp_search' => $empSearch,
            ]));
            $this->redirect('attendance/bulk?' . $qs);
        }

        $this->view('attendance/bulk', compact(
            'clients','fieldOfficers','designations','employees',
            'selectedClient','selectedDate','selectedDesignation','selectedOfficer','empSearch'
        ) + ['pageTitle' => 'Bulk Attendance Entry', 'active' => 'attendance']);
    }

    /* ─── Single Attendance Entry ────────────────────────────────────── */
    public function single(?string $param = null): void {
        $clients = $this->pdo->query("SELECT id,company_name FROM clients WHERE status='active' ORDER BY company_name")->fetchAll();

        if (Helper::isPost() && isset($_POST['save_single'])) {
            $empId    = (int)$_POST['employee_id'];
            $clientId = (int)$_POST['client_id'];
            $tradeId  = $_POST['trade_id'] ?: null;
            $status   = $_POST['status'] ?? 'P';
            $remarks  = Helper::post('remarks');
            $stmt = $this->pdo->prepare("
                INSERT INTO attendance (employee_id,client_id,trade_id,att_date,status,remarks,marked_by)
                VALUES (?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE status=VALUES(status), remarks=VALUES(remarks), marked_by=VALUES(marked_by)
            ");
            $from = new DateTime($_POST['from_date']);
            $to   = new DateTime($_POST['to_date']);
            $count = 0;
            for ($d = clone $from; $d <= $to; $d->modify('+1 day')) {
                $stmt->execute([$empId, $clientId, $tradeId, $d->format('Y-m-d'), $status, $remarks, Session::userId()]);
                $count++;
            }
            Session::flash('success', "Attendance saved for $count day(s).");
            $this->redirect('attendance/single');
        }

        $this->view('attendance/single', compact('clients')
            + ['pageTitle' => 'Single Attendance Entry', 'active' => 'attendance']);
    }

    /* ─── AJAX: Check existing attendance for one employee+date ─────── */
    public function checkAttendance(?string $param = null): void {
        $empId    = (int)Helper::get('employee_id');
        $clientId = (int)Helper::get('client_id');
        $date     = Helper::get('date');
        $existing = [];
        if ($empId && $date) {
            $s = $this->pdo->prepare("SELECT status, remarks FROM attendance WHERE employee_id=? AND att_date=?");
            $s->execute([$empId, $date]);
            $existing = $s->fetch() ?: [];
        }
        $this->json($existing);
    }

    /* ─── View Attendance (day grid + salary summary) ───────────────── */
    public function viewAttendance(?string $clientId = null): void {
        $clients      = $this->pdo->query("SELECT id,company_name FROM clients WHERE status='active' ORDER BY company_name")->fetchAll();
        $fieldOfficers = $this->pdo->query("SELECT id,name FROM users WHERE role='field_officer' ORDER BY name")->fetchAll();
        $designations  = array_column($this->fetchAll("SELECT DISTINCT designation FROM employees WHERE designation IS NOT NULL AND designation != '' ORDER BY designation"), 'designation');

        $clientId    = $clientId ?: Helper::get('client_id');
        $startDate   = Helper::get('start_date', date('Y-m-01'));
        $endDate     = Helper::get('end_date',   date('Y-m-t'));
        $filterDesig = Helper::get('designation', '');
        $filterOfficer = Helper::get('field_officer_id', '');

        $rows = []; $days = [];

        if ($startDate && $endDate) {
            $from = new DateTime($startDate);
            $to   = new DateTime($endDate);
            for ($d = clone $from; $d <= $to; $d->modify('+1 day')) {
                $days[] = $d->format('Y-m-d');
            }

            $where  = "WHERE e.status = 'active'";
            $params = [];

            if ($clientId) {
                $where .= " AND e.id IN (SELECT employee_id FROM positions WHERE client_id=? AND status='active')";
                $params[] = $clientId;
            }
            if ($filterDesig) {
                $where .= " AND e.designation = ?";
                $params[] = $filterDesig;
            }
            if ($filterOfficer) {
                $where .= " AND e.field_officer_id = ?";
                $params[] = $filterOfficer;
            }

            $joinParams = array_merge([$clientId, $startDate, $endDate], $params);
            $s = $this->pdo->prepare("
                SELECT e.id, e.name, e.emp_code, e.designation, e.basic_wage,
                       a.att_date, a.status
                FROM employees e
                LEFT JOIN attendance a ON a.employee_id=e.id
                    AND (? = '' OR a.client_id = ?)
                    AND a.att_date BETWEEN ? AND ?
                $where
                ORDER BY e.designation, e.name, a.att_date
            ");
            $s->execute(array_merge([$clientId, $clientId, $startDate, $endDate], $params));
            $raw = $s->fetchAll();

            foreach ($raw as $r) {
                if (!isset($rows[$r['id']])) {
                    $rows[$r['id']] = [
                        'name'        => $r['name'],
                        'emp_code'    => $r['emp_code'],
                        'designation' => $r['designation'],
                        'basic_wage'  => (float)($r['basic_wage'] ?? 0),
                        'dates'       => [],
                        'P'=>0,'A'=>0,'OT'=>0,'OFF'=>0,'HD'=>0,
                    ];
                }
                if ($r['att_date']) {
                    $rows[$r['id']]['dates'][$r['att_date']] = $r['status'];
                    $st = $r['status'];
                    if (isset($rows[$r['id']][$st])) $rows[$r['id']][$st]++;
                }
            }

            $totalDaysInPeriod = count($days);
            foreach ($rows as &$emp) {
                $dailyRate = $totalDaysInPeriod > 0 ? $emp['basic_wage'] / $totalDaysInPeriod : 0;
                $earnedDays = $emp['P'] + $emp['OT'] + $emp['OFF'] + ($emp['HD'] * 0.5);
                $emp['daily_rate']    = $dailyRate;
                $emp['earned_days']   = $earnedDays;
                $emp['salary_earned'] = round($earnedDays * $dailyRate, 2);
                $emp['total_days']    = $emp['P'] + $emp['A'] + $emp['OT'] + $emp['OFF'] + $emp['HD'];
            }
            unset($emp);
        }

        $this->view('attendance/view', compact(
            'clients','fieldOfficers','designations','clientId','startDate','endDate',
            'rows','days','filterDesig','filterOfficer'
        ) + ['pageTitle' => 'View Attendance', 'active' => 'attendance']);
    }

    /* ─── AJAX: Employees for a client ──────────────────────────────── */
    public function getEmployees(?string $param = null): void {
        $clientId = Helper::get('client_id');
        $s = $this->pdo->prepare("
            SELECT e.id, e.name, t.id AS trade_id,
                   CONCAT(c.company_name,' / ',t.designation,' / ',t.shift) AS trade_label
            FROM employees e
            JOIN positions p ON p.employee_id=e.id AND p.client_id=? AND p.status='active'
            JOIN trades t ON t.id=p.trade_id
            JOIN clients c ON c.id=p.client_id
            ORDER BY e.name
        ");
        $s->execute([$clientId]);
        $this->json($s->fetchAll());
    }
}
