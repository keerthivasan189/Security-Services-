<?php
require_once BASE_PATH . '/controllers/BaseController.php';

class PaymentsController extends BaseController {

    public function index(?string $param = null): void {
        $this->redirect('payments/salarylist');
    }

    public function generate(?string $param = null): void {
        $clients = $this->fetchAll("SELECT id, company_name FROM clients WHERE status='active' ORDER BY company_name");
        $msg     = '';

        if (Helper::isPost()) {
            $month    = Helper::post('salary_month');
            $clientId = Helper::post('client_id');
            [$yr, $mo] = explode('-', $month);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$mo, (int)$yr);

            $emps = $this->fetchAll("
                SELECT DISTINCT e.id, e.basic_wage, e.epf_applicable, e.esi_applicable, e.insurance_amount
                FROM employees e
                JOIN positions p ON p.employee_id = e.id
                WHERE p.client_id = ? AND p.status = 'active'
            ", [$clientId]);

            $count = 0;
            foreach ($emps as $emp) {
                $a = $this->fetchOne("
                    SELECT
                      COALESCE(SUM(status='P'),0)   AS P,
                      COALESCE(SUM(status='OT'),0)  AS OT,
                      COALESCE(SUM(status='OFF'),0) AS OFF,
                      COALESCE(SUM(status='A'),0)   AS A,
                      COUNT(*)                       AS total
                    FROM attendance
                    WHERE employee_id=? AND client_id=? AND DATE_FORMAT(att_date,'%Y-%m')=?
                ", [$emp['id'], $clientId, $month]);

                $present   = (int)($a['P']     ?? 0);
                $totalDays = (int)($a['total'] ?? 0);
                $basic     = $daysInMonth > 0 ? round(($emp['basic_wage'] / $daysInMonth) * $present, 2) : 0;
                $attInc    = ($present > 0 && $present >= $daysInMonth) ? 1000.00 : 0.00;
                $totalEarn = $basic + $attInc;
                $epf       = $emp['epf_applicable'] ? round($basic * 0.12, 2) : 0;
                $esi       = ($emp['esi_applicable'] && $totalEarn <= 21000) ? round($totalEarn * 0.0075, 2) : 0;
                $ins       = (float)($emp['insurance_amount'] ?? 0);

                $advRow = $this->fetchOne("
                    SELECT COALESCE(SUM(due_amount),0) AS t
                    FROM advance_dues
                    WHERE employee_id=? AND due_month=? AND paid=0
                ", [$emp['id'], $month]);
                $advDed = (float)($advRow['t'] ?? 0);

                $uniRow = $this->fetchOne("
                    SELECT COALESCE(SUM(due_amount),0) AS t
                    FROM uniform_bills
                    WHERE employee_id=? AND due_first_month<=? AND due_last_month>=?
                ", [$emp['id'], $month, $month]);
                $uniDed = (float)($uniRow['t'] ?? 0);

                $totalDed  = $epf + $esi + $ins + $advDed + $uniDed;
                $netSalary = round($totalEarn - $totalDed, 2);
                $pslNo     = 'PSL-' . str_pad($emp['id'] . substr(str_replace('-','', $month), 2), 6, '0', STR_PAD_LEFT);

                $this->execute("
                    INSERT INTO salaries
                      (employee_id, salary_month, days_present, days_ot, days_off, days_absent,
                       total_days, basic_wage, attendance_incentive, total_earnings,
                       epf, esi, insurance_premium, salary_advance_ded, uniform_due_ded,
                       total_deductions, net_salary, psl_no)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
                    ON DUPLICATE KEY UPDATE
                      days_present=VALUES(days_present), days_ot=VALUES(days_ot),
                      days_off=VALUES(days_off), days_absent=VALUES(days_absent),
                      total_days=VALUES(total_days), basic_wage=VALUES(basic_wage),
                      attendance_incentive=VALUES(attendance_incentive),
                      total_earnings=VALUES(total_earnings), epf=VALUES(epf),
                      esi=VALUES(esi), insurance_premium=VALUES(insurance_premium),
                      salary_advance_ded=VALUES(salary_advance_ded),
                      uniform_due_ded=VALUES(uniform_due_ded),
                      total_deductions=VALUES(total_deductions), net_salary=VALUES(net_salary)
                ", [
                    $emp['id'], $month,
                    $present, $a['OT']??0, $a['OFF']??0, $a['A']??0, $totalDays,
                    $basic, $attInc, $totalEarn,
                    $epf, $esi, $ins, $advDed, $uniDed,
                    $totalDed, $netSalary, $pslNo,
                ]);
                $count++;
            }
            $msg = "Salary generated for {$count} employee(s) for " . Helper::monthName($month) . '.';
        }
        $this->view('payments/generate', compact('clients','msg')
            + ['pageTitle' => 'Generate Salary', 'active' => 'payments']);
    }

    public function salarylist(?string $param = null): void {
        $clients       = $this->fetchAll("SELECT id, company_name FROM clients WHERE status='active' ORDER BY company_name");
        $employees     = $this->fetchAll("SELECT id, emp_code, name FROM employees WHERE status='active' ORDER BY name");
        $fieldOfficers = $this->fetchAll("SELECT id, name FROM users WHERE role='field_officer' ORDER BY name");

        $month      = Helper::get('month', date('Y-m'));
        $clientId   = Helper::get('client_id');
        $empCode    = Helper::get('emp_code');
        $fOfficer   = Helper::get('field_officer_id');
        $fEmp       = Helper::get('employee_id');
        $fPaid      = Helper::get('paid_status');
        $fMode      = Helper::get('mode');
        $salaries   = [];

        $where = 'WHERE s.salary_month = ?'; $params = [$month];
        if ($clientId) { $where .= ' AND p.client_id = ?';          $params[] = $clientId; }
        if ($empCode)  { $where .= ' AND e.emp_code LIKE ?';        $params[] = "%$empCode%"; }
        if ($fOfficer) { $where .= ' AND e.field_officer_id = ?';   $params[] = $fOfficer; }
        if ($fEmp)     { $where .= ' AND e.id = ?';                 $params[] = $fEmp; }
        if ($fPaid)    { $where .= ' AND s.payment_status = ?';     $params[] = $fPaid; }
        if ($fMode)    { $where .= ' AND s.payment_mode = ?';       $params[] = $fMode; }

        if ($clientId || $empCode || $fOfficer || $fEmp || $fPaid || $fMode) {
            $join = $clientId
                ? "JOIN positions p ON p.employee_id = e.id AND p.client_id = ? AND p.status = 'active'"
                : "LEFT JOIN positions p ON p.employee_id = e.id AND p.status = 'active'";
            // Rebuild properly without duplicate client_id clause
            $where2 = 'WHERE s.salary_month = ?'; $params2 = [$month];
            if ($empCode)  { $where2 .= ' AND e.emp_code LIKE ?';        $params2[] = "%$empCode%"; }
            if ($fOfficer) { $where2 .= ' AND e.field_officer_id = ?';   $params2[] = $fOfficer; }
            if ($fEmp)     { $where2 .= ' AND e.id = ?';                 $params2[] = $fEmp; }
            if ($fPaid)    { $where2 .= ' AND s.payment_status = ?';     $params2[] = $fPaid; }
            if ($fMode)    { $where2 .= ' AND s.payment_mode = ?';       $params2[] = $fMode; }

            if ($clientId) {
                $salaries = $this->fetchAll("
                    SELECT DISTINCT s.*, e.name, e.emp_code, e.designation,
                           e.bank_name, e.bank_account, e.uan_no, e.esi_no,
                           (SELECT c.company_name FROM positions pos JOIN clients c ON c.id=pos.client_id WHERE pos.employee_id=e.id AND pos.status='active' LIMIT 1) AS current_company
                    FROM salaries s
                    JOIN employees e ON e.id = s.employee_id
                    JOIN positions p ON p.employee_id = e.id AND p.client_id = ? AND p.status = 'active'
                    $where2
                    ORDER BY e.name
                ", array_merge([$clientId], $params2));
            } else {
                $salaries = $this->fetchAll("
                    SELECT DISTINCT s.*, e.name, e.emp_code, e.designation,
                           e.bank_name, e.bank_account, e.uan_no, e.esi_no,
                           (SELECT c.company_name FROM positions pos JOIN clients c ON c.id=pos.client_id WHERE pos.employee_id=e.id AND pos.status='active' LIMIT 1) AS current_company
                    FROM salaries s
                    JOIN employees e ON e.id = s.employee_id
                    $where2
                    ORDER BY e.name
                ", $params2);
            }
        }

        $this->view('payments/salarylist',
            compact('clients','employees','fieldOfficers','salaries','month',
                    'clientId','empCode','fOfficer','fEmp','fPaid','fMode')
            + ['pageTitle' => 'Staff Salary List', 'active' => 'payments']);
    }

    public function payslipBulk(?string $param = null): void {
        $clients = $this->fetchAll("SELECT id, company_name FROM clients WHERE status='active' ORDER BY company_name");
        $month   = Helper::post('month') ?: date('Y-m', strtotime('-1 month'));
        $clientId = Helper::post('client_id');
        $salaries = [];
        $salaryPositions = [];

        if ($clientId && $month) {
            $salaries = $this->fetchAll("
                SELECT DISTINCT s.*, e.name, e.emp_code, e.designation, e.doj, e.mobile,
                       e.uan_no, e.esi_no, e.bank_name, e.bank_account
                FROM salaries s
                JOIN employees e ON e.id = s.employee_id
                JOIN positions p ON p.employee_id = e.id AND p.client_id = ? AND p.status = 'active'
                WHERE s.salary_month = ?
                ORDER BY e.name
            ", [$clientId, $month]);

            foreach ($salaries as $s) {
                $salaryPositions[$s['id']] = $this->fetchAll("
                    SELECT p.*, c.company_name,
                        (SELECT COUNT(*) FROM attendance a WHERE a.employee_id=p.employee_id AND a.client_id=p.client_id AND a.status='P' AND DATE_FORMAT(a.att_date,'%Y-%m')=?) AS duties,
                        (SELECT COUNT(*) FROM attendance a WHERE a.employee_id=p.employee_id AND a.client_id=p.client_id AND a.status='OT' AND DATE_FORMAT(a.att_date,'%Y-%m')=?) AS ot,
                        (SELECT COUNT(*) FROM attendance a WHERE a.employee_id=p.employee_id AND a.client_id=p.client_id AND a.status='OFF' AND DATE_FORMAT(a.att_date,'%Y-%m')=?) AS off_days
                    FROM positions p
                    JOIN clients c ON c.id = p.client_id
                    WHERE p.employee_id = ? AND p.status = 'active'
                ", [$month, $month, $month, $s['employee_id']]);
            }
        }

        $this->view('payments/payslip_bulk',
            compact('clients','salaries','salaryPositions','month','clientId')
            + ['pageTitle' => 'Payslip Bulk Print', 'active' => 'payments']);
    }

    public function payslip(?string $salaryId = null): void {
        $salary = $this->fetchOne("
            SELECT s.*, e.name, e.emp_code, e.designation, e.doj, e.mobile,
                   e.uan_no, e.esi_no, e.bank_name, e.bank_account
            FROM salaries s
            JOIN employees e ON e.id = s.employee_id
            WHERE s.id = ?
        ", [$salaryId]);

        if (!$salary) {
            Session::flash('error', 'Salary record not found.');
            $this->redirect('payments/salarylist');
        }

        $positions = $this->fetchAll("
            SELECT c.company_name, t.designation, t.shift,
              (SELECT COALESCE(SUM(status='P'),0)   FROM attendance a WHERE a.employee_id=? AND a.client_id=p.client_id AND DATE_FORMAT(a.att_date,'%Y-%m')=?) AS duties,
              (SELECT COALESCE(SUM(status='OT'),0)  FROM attendance a WHERE a.employee_id=? AND a.client_id=p.client_id AND DATE_FORMAT(a.att_date,'%Y-%m')=?) AS ot,
              (SELECT COALESCE(SUM(status='OFF'),0) FROM attendance a WHERE a.employee_id=? AND a.client_id=p.client_id AND DATE_FORMAT(a.att_date,'%Y-%m')=?) AS off_days
            FROM positions p
            JOIN clients c ON c.id = p.client_id
            JOIN trades  t ON t.id = p.trade_id
            WHERE p.employee_id = ? AND p.status = 'active'
        ", [
            $salary['employee_id'], $salary['salary_month'],
            $salary['employee_id'], $salary['salary_month'],
            $salary['employee_id'], $salary['salary_month'],
            $salary['employee_id'],
        ]);

        $this->view('payments/payslip', compact('salary','positions')
            + ['pageTitle' => 'Pay Slip', 'active' => 'payments']);
    }

    public function advances(?string $param = null): void {
        $clients       = $this->fetchAll("SELECT id, company_name FROM clients WHERE status='active' ORDER BY company_name");
        $employees     = $this->fetchAll("SELECT id, emp_code, name FROM employees WHERE status='active' ORDER BY name");
        $fieldOfficers = $this->fetchAll("SELECT id, name FROM users WHERE role='field_officer' ORDER BY name");
        $ledgers       = $this->getLedgers();

        if (Helper::isPost() && Helper::post('action') === 'add') {
            $this->execute("
                INSERT INTO advances
                  (employee_id, advance_type, advance_date, amount, no_of_dues,
                   due_first_month, due_last_month, due_amount, account_pay_from, remarks)
                VALUES (?,?,?,?,?,?,?,?,?,?)
            ", [
                Helper::post('employee_id'),
                Helper::post('advance_type'),
                Helper::post('advance_date'),
                Helper::post('amount'),
                (int)Helper::post('no_of_dues', '1'),
                Helper::post('due_first_month') ?: null,
                Helper::post('due_last_month')  ?: null,
                Helper::post('due_amount', '0'),
                Helper::post('account_pay_from') ?: null,
                Helper::post('remarks'),
            ]);
            $advId    = $this->pdo->lastInsertId();
            $noOfDues = max(1, (int)Helper::post('no_of_dues','1'));
            $dueAmt   = (float)Helper::post('due_amount','0');
            $firstMo  = Helper::post('due_first_month');
            if ($firstMo && $noOfDues > 0) {
                $d = new DateTime($firstMo . '-01');
                for ($i = 0; $i < $noOfDues; $i++) {
                    $this->execute("INSERT INTO advance_dues (advance_id, employee_id, due_month, due_amount) VALUES (?,?,?,?)",
                        [$advId, Helper::post('employee_id'), $d->format('Y-m'), $dueAmt]);
                    $d->modify('+1 month');
                }
            }
            Session::flash('success', 'Advance saved successfully.');
            $this->redirect('payments/advances');
        }

        // Filters
        $fEmpId    = Helper::get('employee_id');
        $fType     = Helper::get('advance_type');
        $fOfficer  = Helper::get('field_officer_id');
        $fClient   = Helper::get('client_id');
        $fStart    = Helper::get('start_date');
        $fEnd      = Helper::get('end_date');

        $where = 'WHERE 1=1'; $params = [];
        if ($fEmpId)   { $where .= ' AND a.employee_id=?';           $params[] = $fEmpId; }
        if ($fType)    { $where .= ' AND a.advance_type=?';           $params[] = $fType; }
        if ($fOfficer) { $where .= ' AND e.field_officer_id=?';       $params[] = $fOfficer; }
        if ($fClient)  { $where .= ' AND e.id IN (SELECT employee_id FROM positions WHERE client_id=? AND status="active")'; $params[] = $fClient; }
        if ($fStart)   { $where .= ' AND a.advance_date >= ?';        $params[] = $fStart; }
        if ($fEnd)     { $where .= ' AND a.advance_date <= ?';        $params[] = $fEnd; }

        $advList = $this->fetchAll("
            SELECT a.*, e.name, e.emp_code
            FROM advances a
            JOIN employees e ON e.id = a.employee_id
            $where
            ORDER BY a.advance_date DESC
            LIMIT 200
        ", $params);

        $this->view('payments/advances', compact('clients','employees','fieldOfficers','ledgers','advList',
            'fEmpId','fType','fOfficer','fClient','fStart','fEnd')
            + ['pageTitle' => 'Advances', 'active' => 'payments']);
    }

    public function fuel(?string $param = null): void {
        $vehicles = $this->fetchAll("SELECT * FROM vehicles ORDER BY vehicle_no");
        $ledgers  = $this->getLedgers();

        if (Helper::isPost() && Helper::post('action') === 'add') {
            $photo = Helper::uploadFile('bill_photo', 'fuel_bills');
            $this->execute("
                INSERT INTO fuel_expenses
                  (ref_no, vehicle_no, current_km, biller, bill_date, bill_amount,
                   igst, sgst, cgst, bill_photo, paid_status, paid_from_ledger, transaction_no, remarks)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ", [
                Helper::post('ref_no'),
                Helper::post('vehicle_no'),
                (int)(Helper::post('current_km') ?: 0),
                Helper::post('biller'),
                Helper::post('bill_date'),
                Helper::post('bill_amount', '0'),
                Helper::post('igst', '0'),
                Helper::post('sgst', '0'),
                Helper::post('cgst', '0'),
                $photo,
                Helper::post('paid_status', 'Not Paid'),
                Helper::post('paid_from_ledger') ?: null,
                Helper::post('transaction_no'),
                Helper::post('remarks'),
            ]);
            Session::flash('success', 'Fuel bill saved.');
            $this->redirect('payments/fuel');
        }

        // Filters
        $fVehicle  = Helper::get('vehicle_no');
        $fPaid     = Helper::get('paid_status');
        $fLedger   = Helper::get('paid_from_ledger');
        $fStart    = Helper::get('start_date');
        $fEnd      = Helper::get('end_date');

        $where = 'WHERE 1=1'; $params = [];
        if ($fVehicle) { $where .= ' AND f.vehicle_no=?';       $params[] = $fVehicle; }
        if ($fPaid)    { $where .= ' AND f.paid_status=?';      $params[] = $fPaid; }
        if ($fLedger)  { $where .= ' AND f.paid_from_ledger=?'; $params[] = $fLedger; }
        if ($fStart)   { $where .= ' AND f.bill_date >= ?';     $params[] = $fStart; }
        if ($fEnd)     { $where .= ' AND f.bill_date <= ?';     $params[] = $fEnd; }

        $bills = $this->fetchAll("
            SELECT f.*, l.account_name
            FROM fuel_expenses f
            LEFT JOIN ledger_accounts l ON l.id = f.paid_from_ledger
            $where
            ORDER BY f.bill_date DESC
            LIMIT 200
        ", $params);

        $this->view('payments/fuel', compact('vehicles','ledgers','bills','fVehicle','fPaid','fLedger','fStart','fEnd')
            + ['pageTitle' => 'Fuel Expenses', 'active' => 'payments']);
    }

    public function misc(?string $param = null): void {
        $ledgers = $this->getLedgers();

        if (Helper::isPost() && Helper::post('action') === 'add') {
            $photo  = Helper::uploadFile('bill_photo',   'misc_bills');
            $cheque = Helper::uploadFile('cheque_photo', 'cheque_photos');
            $this->execute("
                INSERT INTO misc_expenses
                  (bill_no, ref_no, expense_desc, expense_type, gross_amount, biller,
                   bill_date, bill_amount, igst, sgst, cgst, discount, bill_photo,
                   paid_status, paid_from_ledger, through_ledger, cheque_photo, transaction_no, remarks)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ", [
                Helper::post('bill_no'),
                Helper::post('ref_no'),
                Helper::post('expense_desc'),
                Helper::post('expense_type', 'OTHER EXPENSES'),
                Helper::post('gross_amount', '0'),
                Helper::post('biller'),
                Helper::post('bill_date'),
                Helper::post('bill_amount', '0'),
                Helper::post('igst', '0'),
                Helper::post('sgst', '0'),
                Helper::post('cgst', '0'),
                Helper::post('discount', '0'),
                $photo,
                Helper::post('paid_status', 'Not Paid'),
                Helper::post('paid_from_ledger') ?: null,
                Helper::post('through_ledger')   ?: null,
                $cheque,
                Helper::post('transaction_no'),
                Helper::post('remarks'),
            ]);
            Session::flash('success', 'MISC bill saved.');
            $this->redirect('payments/misc');
        }

        // Filters
        $fType   = Helper::get('expense_type');
        $fPaid   = Helper::get('paid_status');
        $fBiller = Helper::get('biller');
        $fStart  = Helper::get('start_date');
        $fEnd    = Helper::get('end_date');

        $where = 'WHERE 1=1'; $params = [];
        if ($fType)   { $where .= ' AND expense_type=?';  $params[] = $fType; }
        if ($fPaid)   { $where .= ' AND paid_status=?';   $params[] = $fPaid; }
        if ($fBiller) { $where .= ' AND biller LIKE ?';   $params[] = "%$fBiller%"; }
        if ($fStart)  { $where .= ' AND bill_date >= ?';  $params[] = $fStart; }
        if ($fEnd)    { $where .= ' AND bill_date <= ?';  $params[] = $fEnd; }

        $bills = $this->fetchAll("SELECT * FROM misc_expenses $where ORDER BY bill_date DESC LIMIT 200", $params);
        $this->view('payments/misc', compact('ledgers','bills','fType','fPaid','fBiller','fStart','fEnd')
            + ['pageTitle' => 'MISC Expenses', 'active' => 'payments']);
    }

    public function allowances(?string $param = null): void {
        $employees = $this->fetchAll("SELECT id, emp_code, name FROM employees WHERE status='active' ORDER BY name");
        $clients   = $this->fetchAll("SELECT id, company_name FROM clients WHERE status='active' ORDER BY company_name");

        if (Helper::isPost() && Helper::post('action') === 'add') {
            $this->execute("
                INSERT INTO other_allowances (employee_id, client_id, allowance_date, amount, reason, remarks)
                VALUES (?,?,?,?,?,?)
            ", [
                Helper::post('employee_id'),
                Helper::post('client_id') ?: null,
                Helper::post('allowance_date'),
                Helper::post('amount'),
                Helper::post('reason'),
                Helper::post('remarks'),
            ]);
            Session::flash('success', 'Allowance saved.');
            $this->redirect('payments/allowances');
        }

        $fClient = Helper::get('client_id');
        $fEmp    = Helper::get('employee_id');
        $fStart  = Helper::get('start_date');
        $fEnd    = Helper::get('end_date');

        $where = 'WHERE 1=1'; $params = [];
        if ($fClient) { $where .= ' AND a.client_id=?';        $params[] = $fClient; }
        if ($fEmp)    { $where .= ' AND a.employee_id=?';      $params[] = $fEmp; }
        if ($fStart)  { $where .= ' AND a.allowance_date>=?';  $params[] = $fStart; }
        if ($fEnd)    { $where .= ' AND a.allowance_date<=?';  $params[] = $fEnd; }

        $list = $this->fetchAll("
            SELECT a.*, e.name AS emp_name, e.emp_code, c.company_name
            FROM other_allowances a
            JOIN employees e  ON e.id = a.employee_id
            LEFT JOIN clients c ON c.id = a.client_id
            $where
            ORDER BY a.allowance_date DESC
            LIMIT 200
        ", $params);

        $this->view('payments/allowances',
            compact('employees','clients','list','fClient','fEmp','fStart','fEnd')
            + ['pageTitle' => 'Other Allowances / Gifts', 'active' => 'payments']);
    }
}
