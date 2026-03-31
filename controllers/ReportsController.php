<?php
require_once BASE_PATH . '/controllers/BaseController.php';

/**
 * Comprehensive Reports Controller
 * All reports include: date filters, entity filters, Excel export, Print
 */
class ReportsController extends BaseController {

    /* ── Excel Export Helper ─────────────────────────────────────── */
    private function exportExcel(string $filename, array $headers, array $rows): void {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        echo '<html><head><meta charset="utf-8"></head><body>';
        echo '<table border="1" cellpadding="4" cellspacing="0" style="font-family:Arial;font-size:11px">';
        echo '<tr>';
        foreach ($headers as $h) echo '<th style="background:#6c5ce7;color:#fff;font-weight:bold">' . htmlspecialchars($h) . '</th>';
        echo '</tr>';
        foreach ($rows as $r) {
            echo '<tr>';
            foreach ($r as $v) echo '<td>' . htmlspecialchars($v ?? '') . '</td>';
            echo '</tr>';
        }
        echo '</table></body></html>';
        exit;
    }

    /* ── Report Hub ──────────────────────────────────────────────── */
    public function index(?string $param = null): void {
        $this->view('reports/index', ['pageTitle' => 'Report Hub', 'active' => 'reports']);
    }

    /* ── 1. Attendance Report ────────────────────────────────────── */
    public function attendance(?string $param = null): void {
        $clients  = $this->allClients();
        $clientId = Helper::get('client_id');
        $start    = Helper::get('start_date', date('Y-m-01'));
        $end      = Helper::get('end_date',   date('Y-m-t'));
        $rows = []; $days = [];

        if ($clientId) {
            $from = new DateTime($start);
            $to   = new DateTime($end);
            for ($d = clone $from; $d <= $to; $d->modify('+1 day')) $days[] = $d->format('Y-m-d');

            $raw = $this->fetchAll("
                SELECT e.id, e.name, e.emp_code, t.designation,
                       a.att_date, a.status
                FROM employees e
                JOIN positions p ON p.employee_id=e.id AND p.client_id=? AND p.status='active'
                JOIN trades t ON t.id=p.trade_id
                LEFT JOIN attendance a ON a.employee_id=e.id AND a.client_id=?
                    AND a.att_date BETWEEN ? AND ?
                ORDER BY e.name, a.att_date
            ", [$clientId, $clientId, $start, $end]);

            foreach ($raw as $r) {
                if (!isset($rows[$r['id']])) {
                    $rows[$r['id']] = ['name'=>$r['name'],'emp_code'=>$r['emp_code'],
                        'designation'=>$r['designation'],'dates'=>[],'P'=>0,'A'=>0,'OT'=>0,'OFF'=>0];
                }
                if ($r['att_date']) {
                    $rows[$r['id']]['dates'][$r['att_date']] = $r['status'];
                    $st = $r['status'];
                    if (isset($rows[$r['id']][$st])) $rows[$r['id']][$st]++;
                }
            }

            // Excel export
            if (Helper::get('export') === 'excel') {
                $headers = ['Code','Name','Designation'];
                foreach ($days as $d) $headers[] = date('d', strtotime($d));
                $headers = array_merge($headers, ['P','A','OT','OFF']);
                $exRows = [];
                foreach ($rows as $r) {
                    $row = [$r['emp_code'], $r['name'], $r['designation']];
                    foreach ($days as $d) $row[] = $r['dates'][$d] ?? '-';
                    $row[] = $r['P']; $row[] = $r['A']; $row[] = $r['OT']; $row[] = $r['OFF'];
                    $exRows[] = $row;
                }
                $this->exportExcel('attendance_report_' . $start . '_' . $end, $headers, $exRows);
            }
        }
        $this->view('reports/attendance', compact('clients','clientId','start','end','rows','days')
            + ['pageTitle'=>'Attendance Report','active'=>'reports']);
    }

    /* ── 2. Client Report ────────────────────────────────────────── */
    public function clients(?string $param = null): void {
        $status = Helper::get('status', '');
        $branch = Helper::get('branch', '');
        $search = Helper::get('search', '');

        $where = 'WHERE 1=1'; $params = [];
        if ($status) { $where .= ' AND c.status=?'; $params[] = $status; }
        if ($branch) { $where .= ' AND c.branch=?'; $params[] = $branch; }
        if ($search) { $where .= ' AND (c.company_name LIKE ? OR c.client_code LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }

        $list = $this->fetchAll("
            SELECT c.*, (SELECT COUNT(*) FROM positions p WHERE p.client_id=c.id AND p.status='active') AS deployed_count,
                   (SELECT COALESCE(SUM(i.total_outstanding),0) FROM invoices i WHERE i.client_id=c.id AND i.payment_status!='paid') AS outstanding
            FROM clients c $where ORDER BY c.company_name
        ", $params);

        $branches = array_column($this->fetchAll("SELECT DISTINCT branch FROM clients WHERE branch IS NOT NULL ORDER BY branch"), 'branch');

        if (Helper::get('export') === 'excel') {
            $this->exportExcel('client_report', ['Code','Company','Contact','Mobile','Branch','Status','Deployed','Outstanding'],
                array_map(fn($c) => [$c['client_code'],$c['company_name'],$c['contact_person'],$c['mobile'],$c['branch'],$c['status'],$c['deployed_count'],number_format($c['outstanding'],2)], $list));
        }

        $this->view('reports/clients', compact('list','status','branch','search','branches')
            + ['pageTitle'=>'Client Report','active'=>'reports']);
    }

    /* ── 3. Employee Report ──────────────────────────────────────── */
    public function employees(?string $param = null): void {
        $status   = Helper::get('status', '');
        $desig    = Helper::get('designation', '');
        $clientId = Helper::get('client_id', '');
        $search   = Helper::get('search', '');

        $where = 'WHERE 1=1'; $params = [];
        if ($status) { $where .= ' AND e.status=?'; $params[] = $status; }
        if ($desig)  { $where .= ' AND e.designation=?'; $params[] = $desig; }
        if ($search) { $where .= ' AND (e.name LIKE ? OR e.emp_code LIKE ? OR e.mobile LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($clientId) {
            $where .= ' AND e.id IN (SELECT employee_id FROM positions WHERE client_id=? AND status="active")';
            $params[] = $clientId;
        }

        $list = $this->fetchAll("
            SELECT e.*, (SELECT c.company_name FROM clients c JOIN positions p ON p.client_id=c.id WHERE p.employee_id=e.id AND p.status='active' LIMIT 1) AS deployed_at
            FROM employees e $where ORDER BY e.name LIMIT 500
        ", $params);

        $designations = array_column($this->fetchAll("SELECT DISTINCT designation FROM employees ORDER BY designation"), 'designation');
        $clList = $this->allClients();

        if (Helper::get('export') === 'excel') {
            $this->exportExcel('employee_report', ['Code','Name','Designation','DOJ','Mobile','Bank','Status','Deployed At'],
                array_map(fn($e) => [$e['emp_code'],$e['name'],$e['designation'],$e['doj'],$e['mobile'],$e['bank_name'],$e['status'],$e['deployed_at']??'—'], $list));
        }

        $this->view('reports/employees', compact('list','status','desig','clientId','search','designations','clList')
            + ['pageTitle'=>'Employee Report','active'=>'reports']);
    }

    /* ── 4. Salary Report ────────────────────────────────────────── */
    public function salary(?string $param = null): void {
        $clients  = $this->allClients();
        $clientId = Helper::get('client_id');
        $month    = Helper::get('month', date('Y-m'));
        $mode     = Helper::get('mode', '');
        $salaries = [];

        $where = 'WHERE s.salary_month=?'; $params = [$month];
        if ($clientId) {
            $where .= ' AND e.id IN (SELECT employee_id FROM positions WHERE client_id=? AND status="active")';
            $params[] = $clientId;
        }
        if ($mode) { $where .= ' AND s.payment_mode=?'; $params[] = $mode; }

        $salaries = $this->fetchAll("
            SELECT s.*, e.name, e.emp_code, e.designation, e.bank_name, e.bank_account
            FROM salaries s JOIN employees e ON e.id = s.employee_id
            $where ORDER BY e.name
        ", $params);

        if (Helper::get('export') === 'excel') {
            $this->exportExcel('salary_report_' . $month,
                ['Code','Name','Designation','Days','Basic','DA','HRA','Att.Inc','Total Earn','EPF','ESI','Advance','Uniform','Tot.Ded','Net','Mode'],
                array_map(fn($s) => [$s['emp_code'],$s['name'],$s['designation'],$s['days_present'],
                    number_format($s['basic_wage'],2),number_format($s['da'],2),number_format($s['hra'],2),
                    number_format($s['attendance_incentive'],2),number_format($s['total_earnings'],2),
                    number_format($s['epf'],2),number_format($s['esi'],2),
                    number_format($s['salary_advance_ded'],2),number_format($s['uniform_due_ded'],2),
                    number_format($s['total_deductions'],2),number_format($s['net_salary'],2),$s['payment_mode']], $salaries));
        }

        $this->view('reports/salary', compact('clients','salaries','month','clientId','mode')
            + ['pageTitle'=>'Salary Report','active'=>'reports']);
    }

    /* ── 5. Invoice Report ───────────────────────────────────────── */
    public function invoices(?string $param = null): void {
        $month    = Helper::get('month');
        $clientId = Helper::get('client_id');
        $status   = Helper::get('status');
        $clients  = $this->allClients();

        $where = 'WHERE 1=1'; $params = [];
        if ($month)    { $where .= ' AND i.invoice_month=?'; $params[] = $month; }
        if ($clientId) { $where .= ' AND i.client_id=?'; $params[] = $clientId; }
        if ($status)   { $where .= ' AND i.payment_status=?'; $params[] = $status; }

        $list = $this->fetchAll("
            SELECT i.*, c.company_name FROM invoices i JOIN clients c ON c.id=i.client_id
            $where ORDER BY i.created_at DESC LIMIT 500
        ", $params);

        $totals = ['amount' => array_sum(array_column($list,'grand_total')),
            'outstanding' => array_sum(array_column($list,'total_outstanding'))];

        if (Helper::get('export') === 'excel') {
            $this->exportExcel('invoice_report',
                ['Invoice No','Company','Type','Month','Amount','Outstanding','Status'],
                array_map(fn($i) => [$i['invoice_no'],$i['company_name'],$i['bill_type'],$i['invoice_month'],
                    number_format($i['grand_total'],2),number_format($i['total_outstanding'],2),$i['payment_status']], $list));
        }

        $this->view('reports/invoices', compact('list','month','totals','clients','clientId','status')
            + ['pageTitle'=>'Invoice Report','active'=>'reports']);
    }

    /* ── 6. Payments Report ──────────────────────────────────────── */
    public function payments(?string $param = null): void {
        $start    = Helper::get('start_date', date('Y-m-01'));
        $end      = Helper::get('end_date',   date('Y-m-d'));
        $clientId = Helper::get('client_id');
        $method   = Helper::get('method');
        $clients  = $this->allClients();

        $where = 'WHERE ip.payment_date BETWEEN ? AND ?'; $params = [$start, $end];
        if ($clientId) { $where .= ' AND i.client_id=?'; $params[] = $clientId; }
        if ($method)   { $where .= ' AND ip.payment_method=?'; $params[] = $method; }

        $list = $this->fetchAll("
            SELECT ip.*, i.invoice_no, c.company_name
            FROM invoice_payments ip JOIN invoices i ON i.id=ip.invoice_id
            JOIN clients c ON c.id=i.client_id $where ORDER BY ip.payment_date DESC
        ", $params);
        $total = array_sum(array_column($list,'amount'));

        if (Helper::get('export') === 'excel') {
            $this->exportExcel('payments_report',
                ['Date','Invoice','Company','Type','Method','Ref No','Amount'],
                array_map(fn($p) => [date('d M Y',strtotime($p['payment_date'])),$p['invoice_no'],$p['company_name'],
                    $p['payment_type'],$p['payment_method']??'—',$p['ref_no']??'—',number_format($p['amount'],2)], $list));
        }

        $this->view('reports/payments', compact('list','start','end','total','clients','clientId','method')
            + ['pageTitle'=>'Payments Report','active'=>'reports']);
    }

    /* ── 7. Outstanding Report ───────────────────────────────────── */
    public function outstanding(?string $param = null): void {
        $branch = Helper::get('branch');
        $where = ''; $params = [];
        if ($branch) { $where = 'AND c.branch=?'; $params[] = $branch; }

        $list = $this->fetchAll("
            SELECT c.company_name, c.client_code, c.branch,
                   COUNT(i.id) AS bill_count,
                   COALESCE(SUM(i.grand_total),0) AS total_billed,
                   COALESCE(SUM(i.total_outstanding),0) AS total_outstanding
            FROM clients c JOIN invoices i ON i.client_id=c.id AND i.payment_status!='paid'
            $where GROUP BY c.id HAVING total_outstanding > 0 ORDER BY total_outstanding DESC
        ", $params);
        $grandTotal = array_sum(array_column($list,'total_outstanding'));
        $branches = array_column($this->fetchAll("SELECT DISTINCT branch FROM clients WHERE branch IS NOT NULL ORDER BY branch"), 'branch');

        if (Helper::get('export') === 'excel') {
            $this->exportExcel('outstanding_report',
                ['Code','Company','Branch','Bills','Billed','Outstanding'],
                array_map(fn($c) => [$c['client_code'],$c['company_name'],$c['branch']??'—',$c['bill_count'],
                    number_format($c['total_billed'],2),number_format($c['total_outstanding'],2)], $list));
        }

        $this->view('reports/outstanding', compact('list','grandTotal','branch','branches')
            + ['pageTitle'=>'Balance Outstanding','active'=>'reports']);
    }

    /* ── 8. Expense Report (Fuel + Misc combined) ────────────────── */
    public function expenses(?string $param = null): void {
        $start = Helper::get('start_date', date('Y-m-01'));
        $end   = Helper::get('end_date',   date('Y-m-d'));
        $type  = Helper::get('type', 'all'); // all, fuel, misc

        $fuel = []; $misc = [];
        if ($type === 'all' || $type === 'fuel') {
            $fuel = $this->fetchAll("SELECT 'Fuel' AS exp_type, ref_no, vehicle_no AS description, biller, bill_date, bill_amount, sgst, cgst, (bill_amount+sgst+cgst) AS total, paid_status FROM fuel_expenses WHERE bill_date BETWEEN ? AND ? ORDER BY bill_date DESC", [$start, $end]);
        }
        if ($type === 'all' || $type === 'misc') {
            $misc = $this->fetchAll("SELECT 'Misc' AS exp_type, bill_no AS ref_no, expense_desc AS description, biller, bill_date, bill_amount, sgst, cgst, (bill_amount+sgst+cgst) AS total, paid_status FROM misc_expenses WHERE bill_date BETWEEN ? AND ? ORDER BY bill_date DESC", [$start, $end]);
        }
        $list = array_merge($fuel, $misc);
        usort($list, fn($a,$b) => strcmp($b['bill_date'], $a['bill_date']));
        $totalAmt = array_sum(array_column($list, 'total'));

        if (Helper::get('export') === 'excel') {
            $this->exportExcel('expense_report',
                ['Type','Ref No','Description','Biller','Date','Amount','SGST','CGST','Total','Status'],
                array_map(fn($e) => [$e['exp_type'],$e['ref_no'],$e['description'],$e['biller'],
                    date('d M Y',strtotime($e['bill_date'])),number_format($e['bill_amount'],2),
                    number_format($e['sgst'],2),number_format($e['cgst'],2),
                    number_format($e['total'],2),$e['paid_status']], $list));
        }

        $this->view('reports/expenses', compact('list','start','end','type','totalAmt')
            + ['pageTitle'=>'Expense Report','active'=>'reports']);
    }

    /* ── 9. Inventory Report ─────────────────────────────────────── */
    public function inventory(?string $param = null): void {
        $vendorId = Helper::get('vendor_id');
        $where = ''; $params = [];
        if ($vendorId) { $where = 'WHERE ui.vendor_id=?'; $params[] = $vendorId; }

        $items = $this->fetchAll("
            SELECT ui.*, v.vendor_name,
                   (SELECT COUNT(*) FROM uniform_bill_items ubi WHERE ubi.item_id=ui.id) AS times_issued,
                   (SELECT COALESCE(SUM(ubi.qty),0) FROM uniform_bill_items ubi WHERE ubi.item_id=ui.id) AS total_qty_issued
            FROM uniform_items ui LEFT JOIN vendors v ON v.id=ui.vendor_id
            $where ORDER BY ui.item_name
        ", $params);
        $vendors = $this->fetchAll("SELECT id, vendor_name FROM vendors ORDER BY vendor_name");

        if (Helper::get('export') === 'excel') {
            $this->exportExcel('inventory_report',
                ['Item Name','Vendor','Unit Price','Times Issued','Total Qty Issued'],
                array_map(fn($i) => [$i['item_name'],$i['vendor_name']??'—',number_format($i['unit_price'],2),$i['times_issued'],$i['total_qty_issued']], $items));
        }

        $this->view('reports/inventory', compact('items','vendors','vendorId')
            + ['pageTitle'=>'Inventory Report','active'=>'reports']);
    }

    /* ── 10. Vendor Report ───────────────────────────────────────── */
    public function vendors(?string $param = null): void {
        $search = Helper::get('search', '');
        $where = ''; $params = [];
        if ($search) { $where = "WHERE v.vendor_name LIKE ?"; $params[] = "%$search%"; }

        $list = $this->fetchAll("
            SELECT v.*,
                   (SELECT COUNT(*) FROM uniform_items ui WHERE ui.vendor_id=v.id) AS item_count,
                   (SELECT COALESCE(SUM(ubi.total),0) FROM uniform_bill_items ubi JOIN uniform_items ui ON ui.id=ubi.item_id WHERE ui.vendor_id=v.id) AS total_supplied
            FROM vendors v $where ORDER BY v.vendor_name
        ", $params);

        if (Helper::get('export') === 'excel') {
            $this->exportExcel('vendor_report',
                ['Vendor Name','Contact','Mobile','Address','Items','Total Supplied (₹)'],
                array_map(fn($v) => [$v['vendor_name'],$v['contact_name']??'—',$v['mobile']??'—',$v['address']??'—',$v['item_count'],number_format($v['total_supplied'],2)], $list));
        }

        $this->view('reports/vendors', compact('list','search')
            + ['pageTitle'=>'Vendor Report','active'=>'reports']);
    }

    /* ── 11. Ledger Statement ────────────────────────────────────── */
    public function ledger(?string $param = null): void {
        $ledgers  = $this->getLedgers();
        $ledgerId = Helper::get('ledger_id');
        $start    = Helper::get('start_date');
        $end      = Helper::get('end_date');
        $txns     = [];

        if ($ledgerId) {
            $where = 'WHERE (t.debit_ledger_id=? OR t.credit_ledger_id=?)';
            $params = [$ledgerId, $ledgerId];
            if ($start) { $where .= ' AND t.txn_date>=?'; $params[] = $start; }
            if ($end)   { $where .= ' AND t.txn_date<=?'; $params[] = $end; }
            $txns = $this->fetchAll("
                SELECT t.*, dl.account_name AS debit_acct, cl.account_name AS credit_acct
                FROM transactions t
                LEFT JOIN ledger_accounts dl ON dl.id=t.debit_ledger_id
                LEFT JOIN ledger_accounts cl ON cl.id=t.credit_ledger_id
                $where ORDER BY t.txn_date DESC LIMIT 500
            ", $params);

            if (Helper::get('export') === 'excel') {
                $this->exportExcel('ledger_statement',
                    ['Date','Description','Debit Account','Credit Account','Amount'],
                    array_map(fn($t) => [date('d M Y',strtotime($t['txn_date'])),$t['description'],
                        $t['debit_acct']??'—',$t['credit_acct']??'—',number_format($t['amount'],2)], $txns));
            }
        }
        $this->view('reports/ledger', compact('ledgers','ledgerId','start','end','txns')
            + ['pageTitle'=>'Ledger Statement','active'=>'reports']);
    }
}
