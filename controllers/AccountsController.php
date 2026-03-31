<?php
require_once BASE_PATH . '/controllers/BaseController.php';

class AccountsController extends BaseController {

    public function index(?string $param = null): void {
        $this->redirect('accounts/dashboard');
    }

    /* ─── AJAX: cascading filter options ────────────────────────────── */
    public function filterOptions(?string $param = null): void {
        header('Content-Type: application/json');
        $clientId  = Helper::get('client_id')  ?: null;
        $empId     = Helper::get('employee_id') ?: null;
        $vendorId  = Helper::get('vendor_id')   ?: null;
        $ledgerId  = Helper::get('ledger_id')   ?: null;

        /* ── Employees for a selected Client ─────────────────────────
           Use POSITIONS table (actual deployments) as the primary source.
           E.g. if AAVIN DAIRY has 3 deployed employees, show those 3. */
        if ($clientId) {
            $empIds = array_column($this->fetchAll(
                "SELECT DISTINCT employee_id FROM positions WHERE client_id=?", [$clientId]
            ), 'employee_id');
        } elseif ($vendorId || $ledgerId) {
            // Fall back to transaction links when no client selected
            $tw = 'WHERE 1=1'; $tp = [];
            if ($vendorId) { $tw .= ' AND t.vendor_id=?';  $tp[] = $vendorId; }
            if ($ledgerId) { $tw .= ' AND (t.debit_ledger_id=? OR t.credit_ledger_id=?)'; $tp[] = $ledgerId; $tp[] = $ledgerId; }
            $empIds = array_column($this->fetchAll("SELECT DISTINCT t.employee_id FROM transactions t $tw AND t.employee_id IS NOT NULL", $tp), 'employee_id');
        } else {
            $empIds = [];
        }

        /* ── Clients for a selected Employee ─────────────────────────
           Use POSITIONS table (actual deployments). */
        if ($empId) {
            $clientIds = array_column($this->fetchAll(
                "SELECT DISTINCT client_id FROM positions WHERE employee_id=?", [$empId]
            ), 'client_id');
        } elseif ($vendorId || $ledgerId) {
            $tw = 'WHERE 1=1'; $tp = [];
            if ($vendorId) { $tw .= ' AND t.vendor_id=?';  $tp[] = $vendorId; }
            if ($ledgerId) { $tw .= ' AND (t.debit_ledger_id=? OR t.credit_ledger_id=?)'; $tp[] = $ledgerId; $tp[] = $ledgerId; }
            $clientIds = array_column($this->fetchAll("SELECT DISTINCT t.client_id FROM transactions t $tw AND t.client_id IS NOT NULL", $tp), 'client_id');
        } else {
            $clientIds = [];
        }

        /* ── Vendors: use transactions (no deployment table for vendors) */
        $vtw = 'WHERE 1=1'; $vtp = [];
        if ($clientId)  { $vtw .= ' AND t.client_id=?';   $vtp[] = $clientId; }
        if ($empId)     { $vtw .= ' AND t.employee_id=?';  $vtp[] = $empId; }
        if ($ledgerId)  { $vtw .= ' AND (t.debit_ledger_id=? OR t.credit_ledger_id=?)'; $vtp[] = $ledgerId; $vtp[] = $ledgerId; }
        $vendorIds = array_column($this->fetchAll("SELECT DISTINCT t.vendor_id FROM transactions t $vtw AND t.vendor_id IS NOT NULL", $vtp), 'vendor_id');

        /* ── Ledgers: use transactions */
        $ltw = 'WHERE 1=1'; $ltp = [];
        if ($clientId)  { $ltw .= ' AND t.client_id=?';   $ltp[] = $clientId; }
        if ($empId)     { $ltw .= ' AND t.employee_id=?';  $ltp[] = $empId; }
        if ($vendorId)  { $ltw .= ' AND t.vendor_id=?';    $ltp[] = $vendorId; }
        $ledgerIds = array_filter(array_column($this->fetchAll(
            "SELECT DISTINCT t.debit_ledger_id AS lid FROM transactions t $ltw
             UNION
             SELECT DISTINCT t.credit_ledger_id FROM transactions t $ltw",
            array_merge($ltp, $ltp)
        ), 'lid'));

        /* ── Fetch full records (or all if no filter restricts them) */
        $clients = empty($clientIds)
            ? $this->fetchAll("SELECT id, company_name FROM clients ORDER BY company_name")
            : $this->fetchAll("SELECT id, company_name FROM clients WHERE id IN (" . implode(',', array_fill(0, count($clientIds), '?')) . ") ORDER BY company_name", $clientIds);

        $employees = empty($empIds)
            ? $this->fetchAll("SELECT id, emp_code, name FROM employees ORDER BY name")
            : $this->fetchAll("SELECT id, emp_code, name FROM employees WHERE id IN (" . implode(',', array_fill(0, count($empIds), '?')) . ") ORDER BY name", $empIds);

        $vendors = empty($vendorIds)
            ? $this->fetchAll("SELECT id, vendor_name FROM vendors ORDER BY vendor_name")
            : $this->fetchAll("SELECT id, vendor_name FROM vendors WHERE id IN (" . implode(',', array_fill(0, count($vendorIds), '?')) . ") ORDER BY vendor_name", $vendorIds);

        $ledgers = empty($ledgerIds)
            ? $this->fetchAll("SELECT id, account_name, account_type FROM ledger_accounts ORDER BY account_name")
            : $this->fetchAll("SELECT id, account_name, account_type FROM ledger_accounts WHERE id IN (" . implode(',', array_fill(0, count($ledgerIds), '?')) . ") ORDER BY account_name", array_values($ledgerIds));

        echo json_encode(compact('clients', 'employees', 'vendors', 'ledgers'));
        exit;
    }


    /* ─── Financial Dashboard ───────────────────────────────────────── */
    public function dashboard(?string $param = null): void {
        $ledgers = $this->fetchAll("
            SELECT la.*,
                COALESCE((SELECT SUM(amount) FROM transactions WHERE debit_ledger_id  = la.id), 0) AS total_debit,
                COALESCE((SELECT SUM(amount) FROM transactions WHERE credit_ledger_id = la.id), 0) AS total_credit
            FROM ledger_accounts la ORDER BY la.account_type, la.account_name
        ");

        $today = date('Y-m-d');
        $todayTxns = $this->fetchAll("
            SELECT t.*, dl.account_name AS debit_acct, cl.account_name AS credit_acct,
                   c.company_name AS client_name, e.name AS emp_name, v.vendor_name
            FROM transactions t
            LEFT JOIN ledger_accounts dl ON dl.id = t.debit_ledger_id
            LEFT JOIN ledger_accounts cl ON cl.id = t.credit_ledger_id
            LEFT JOIN clients c ON c.id = t.client_id
            LEFT JOIN employees e ON e.id = t.employee_id
            LEFT JOIN vendors v ON v.id = t.vendor_id
            WHERE t.txn_date = ? ORDER BY t.id DESC
        ", [$today]);

        $totalBalance = array_sum(array_column($ledgers, 'current_balance'));
        $todayTotal   = array_sum(array_column($todayTxns, 'amount'));
        $monthStart   = date('Y-m-01');
        $monthTotal   = $this->fetchOne("SELECT COALESCE(SUM(amount),0) AS total FROM transactions WHERE txn_date >= ?", [$monthStart])['total'];
        $txnCount     = $this->fetchOne("SELECT COUNT(*) AS cnt FROM transactions")['cnt'];

        $recentTxns = $this->fetchAll("
            SELECT t.*, dl.account_name AS debit_acct, cl.account_name AS credit_acct,
                   u.name AS created_by_name, c.company_name AS client_name,
                   e.name AS emp_name, v.vendor_name
            FROM transactions t
            LEFT JOIN ledger_accounts dl ON dl.id = t.debit_ledger_id
            LEFT JOIN ledger_accounts cl ON cl.id = t.credit_ledger_id
            LEFT JOIN users u            ON u.id  = t.created_by
            LEFT JOIN clients c ON c.id = t.client_id
            LEFT JOIN employees e ON e.id = t.employee_id
            LEFT JOIN vendors v ON v.id = t.vendor_id
            ORDER BY t.txn_date DESC, t.id DESC LIMIT 10
        ");

        $this->view('accounts/dashboard', compact('ledgers','todayTxns','todayTotal','monthTotal','totalBalance','txnCount','recentTxns','today')
            + ['pageTitle' => 'Financial Dashboard', 'active' => 'accounts']);
    }

    /* ─── Statement of Accounts with Running Balance ────────────────── */
    public function statements(?string $param = null): void {
        $ledgers   = $this->getLedgers();
        $employees = $this->fetchAll("SELECT id, emp_code, name FROM employees ORDER BY name");
        $clients   = $this->fetchAll("SELECT id, company_name FROM clients ORDER BY company_name");
        $vendors   = $this->fetchAll("SELECT id, vendor_name FROM vendors ORDER BY vendor_name");

        $where  = 'WHERE 1=1';
        $params = [];
        $ledId  = Helper::get('ledger_id');

        // Filter by Client (proper FK)
        if ($clientId = Helper::get('client_id')) {
            $where .= ' AND t.client_id = ?';
            $params[] = $clientId;
        }
        // Filter by Employee (proper FK)
        if ($empId = Helper::get('employee_id')) {
            $where .= ' AND t.employee_id = ?';
            $params[] = $empId;
        }
        // Filter by Employee Emp Code (search)
        if ($empCode = Helper::get('emp_code')) {
            $where .= ' AND e.emp_code = ?';
            $params[] = $empCode;
        }
        // Filter by Vendor (proper FK)
        if ($vendorId = Helper::get('vendor_id')) {
            $where .= ' AND t.vendor_id = ?';
            $params[] = $vendorId;
        }
        // Filter by Ledger
        if ($ledId) {
            $where .= ' AND (t.debit_ledger_id=? OR t.credit_ledger_id=?)';
            $params[] = $ledId; $params[] = $ledId;
        }
        // Filter by Credit/Debit
        if ($creditDebit = Helper::get('credit_debit')) {
            if ($creditDebit === 'debit' && $ledId) {
                $where .= ' AND t.debit_ledger_id=?';
                $params[] = $ledId;
            } elseif ($creditDebit === 'credit' && $ledId) {
                $where .= ' AND t.credit_ledger_id=?';
                $params[] = $ledId;
            }
        }
        // Filter by Transaction Type
        if ($txnType = Helper::get('txn_type')) {
            $where .= ' AND t.txn_type = ?';
            $params[] = $txnType;
        }
        // Date filters
        if ($from = Helper::get('start_date')) {
            $where .= ' AND t.txn_date >= ?'; $params[] = $from;
        }
        if ($to = Helper::get('end_date')) {
            $where .= ' AND t.txn_date <= ?'; $params[] = $to;
        }

        $transactions = $this->fetchAll("
            SELECT t.*,
                   dl.account_name AS debit_acct,
                   cl.account_name AS credit_acct,
                   u.name          AS created_by_name,
                   c.company_name  AS client_name,
                   e.name          AS emp_name,
                   e.emp_code      AS emp_code_val,
                   v.vendor_name
            FROM transactions t
            LEFT JOIN ledger_accounts dl ON dl.id = t.debit_ledger_id
            LEFT JOIN ledger_accounts cl ON cl.id = t.credit_ledger_id
            LEFT JOIN users u            ON u.id  = t.created_by
            LEFT JOIN clients c          ON c.id  = t.client_id
            LEFT JOIN employees e        ON e.id  = t.employee_id
            LEFT JOIN vendors v          ON v.id  = t.vendor_id
            $where
            ORDER BY t.txn_date ASC, t.id ASC
            LIMIT 500
        ", $params);

        // Calculate running balance when filtered by a specific ledger
        $runningBalance = 0;
        if ($ledId) {
            $selectedLedger = $this->fetchOne("SELECT * FROM ledger_accounts WHERE id=?", [$ledId]);
            if ($from) {
                $openBal = $this->fetchOne("
                    SELECT COALESCE(SUM(CASE WHEN credit_ledger_id=? THEN amount ELSE 0 END),0) -
                           COALESCE(SUM(CASE WHEN debit_ledger_id=? THEN amount ELSE 0 END),0) AS opening
                    FROM transactions WHERE txn_date < ? AND (debit_ledger_id=? OR credit_ledger_id=?)
                ", [$ledId, $ledId, $from, $ledId, $ledId]);
                $runningBalance = (float)($selectedLedger['current_balance'] ?? 0) + (float)($openBal['opening'] ?? 0);
            } else {
                $runningBalance = (float)($selectedLedger['current_balance'] ?? 0);
            }
            foreach ($transactions as &$t) {
                if ((int)$t['debit_ledger_id'] === (int)$ledId) {
                    $t['dr_amount'] = (float)$t['amount'];
                    $t['cr_amount'] = 0;
                    $runningBalance -= (float)$t['amount'];
                } else {
                    $t['dr_amount'] = 0;
                    $t['cr_amount'] = (float)$t['amount'];
                    $runningBalance += (float)$t['amount'];
                }
                $t['running_balance'] = $runningBalance;
            }
            unset($t);
        }

        $transactions = array_reverse($transactions);

        $this->view('accounts/statements', compact('ledgers','employees','clients','vendors','transactions','ledId')
            + ['pageTitle' => 'Statement of Accounts', 'active' => 'accounts']);
    }

    /* ─── Add Transaction  ──────────────────────────────────────────── */
    public function add(?string $param = null): void {
        $ledgers   = $this->getLedgers();
        $clients   = $this->fetchAll("SELECT id, company_name FROM clients ORDER BY company_name");
        $employees = $this->fetchAll("SELECT id, emp_code, name FROM employees ORDER BY name");
        $vendors   = $this->fetchAll("SELECT id, vendor_name FROM vendors ORDER BY vendor_name");

        if (Helper::isPost()) {
            $amount   = (float)Helper::post('amount');
            $debitId  = Helper::post('debit_ledger_id') ?: null;
            $creditId = Helper::post('credit_ledger_id') ?: null;
            $clientId = Helper::post('client_id') ?: null;
            $empId    = Helper::post('employee_id') ?: null;
            $vendorId = Helper::post('vendor_id') ?: null;
            $txnType  = Helper::post('txn_type', 'general');

            $this->execute("
                INSERT INTO transactions (txn_date, description, debit_ledger_id, credit_ledger_id, client_id, employee_id, vendor_id, txn_type, amount, remarks, created_by)
                VALUES (?,?,?,?,?,?,?,?,?,?,?)
            ", [
                Helper::post('txn_date'), Helper::post('description'),
                $debitId, $creditId, $clientId, $empId, $vendorId, $txnType,
                $amount, Helper::post('remarks'), Session::userId(),
            ]);

            // Update ledger balances
            if ($debitId)  $this->execute("UPDATE ledger_accounts SET current_balance = current_balance - ? WHERE id=?", [$amount, $debitId]);
            if ($creditId) $this->execute("UPDATE ledger_accounts SET current_balance = current_balance + ? WHERE id=?", [$amount, $creditId]);

            Session::flash('success', 'Transaction recorded — ₹' . number_format($amount, 2));
            $this->redirect('accounts/statements');
        }
        $this->view('accounts/add', compact('ledgers','clients','employees','vendors')
            + ['pageTitle' => 'Add Transaction', 'active' => 'accounts']);
    }

    /* ─── Cash Flow / Office-wise ───────────────────────────────────── */
    public function cashflow(?string $param = null): void {
        $ledgers = $this->fetchAll("
            SELECT la.*,
                COALESCE((SELECT SUM(amount) FROM transactions WHERE debit_ledger_id  = la.id), 0) AS total_debit,
                COALESCE((SELECT SUM(amount) FROM transactions WHERE credit_ledger_id = la.id), 0) AS total_credit
            FROM ledger_accounts la ORDER BY la.account_type, la.account_name
        ");

        $monthlyFlow = $this->fetchAll("
            SELECT DATE_FORMAT(txn_date, '%Y-%m') AS month,
                   SUM(amount) AS total, COUNT(*) AS txn_count
            FROM transactions
            WHERE txn_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(txn_date, '%Y-%m')
            ORDER BY month ASC
        ");

        $this->view('accounts/cashflow', compact('ledgers','monthlyFlow')
            + ['pageTitle' => 'Cash Flow', 'active' => 'accounts']);
    }

    /* ─── Ledger Accounts ───────────────────────────────────────────── */
    public function ledgers(?string $param = null): void {
        if (Helper::isPost()) {
            $action = Helper::post('action');
            if ($action === 'add') {
                $this->execute(
                    "INSERT INTO ledger_accounts (account_name, account_type, current_balance) VALUES (?,?,?)",
                    [Helper::post('account_name'), Helper::post('account_type'), (float)(Helper::post('current_balance') ?: 0)]
                );
                Session::flash('success', 'Ledger account added.');
            } elseif ($action === 'delete' && Helper::post('ledger_id')) {
                $this->execute("DELETE FROM ledger_accounts WHERE id=?", [Helper::post('ledger_id')]);
                Session::flash('success', 'Ledger account deleted.');
            }
        }
        $ledgers = $this->fetchAll("
            SELECT la.*,
                COALESCE((SELECT COUNT(*) FROM transactions WHERE debit_ledger_id=la.id OR credit_ledger_id=la.id), 0) AS txn_count
            FROM ledger_accounts la ORDER BY la.account_type, la.account_name
        ");
        $this->view('accounts/ledgers', compact('ledgers')
            + ['pageTitle' => 'Ledger Accounts', 'active' => 'accounts']);
    }

    /* ─── Ledger Detail / Account Statement ─────────────────────────── */
    public function ledgerView(?string $param = null): void {
        $id = $param ? (int)$param : (int)Helper::get('id');
        if (!$id) { $this->redirect('accounts/ledgers'); return; }

        $ledger = $this->fetchOne("SELECT * FROM ledger_accounts WHERE id=?", [$id]);
        if (!$ledger) { $this->redirect('accounts/ledgers'); return; }

        // Date range filters
        $from = Helper::get('start_date') ?: null;
        $to   = Helper::get('end_date')   ?: null;
        $w = 'WHERE (t.debit_ledger_id=? OR t.credit_ledger_id=?)'; $p = [$id, $id];
        if ($from) { $w .= ' AND t.txn_date >= ?'; $p[] = $from; }
        if ($to)   { $w .= ' AND t.txn_date <= ?'; $p[] = $to;   }

        $transactions = $this->fetchAll("
            SELECT t.*,
                   dl.account_name AS debit_acct,
                   cl.account_name AS credit_acct,
                   u.name          AS created_by_name,
                   c.company_name  AS client_name,
                   e.name          AS emp_name, e.emp_code AS emp_code_val,
                   v.vendor_name
            FROM transactions t
            LEFT JOIN ledger_accounts dl ON dl.id = t.debit_ledger_id
            LEFT JOIN ledger_accounts cl ON cl.id = t.credit_ledger_id
            LEFT JOIN users u    ON u.id = t.created_by
            LEFT JOIN clients c  ON c.id = t.client_id
            LEFT JOIN employees e ON e.id = t.employee_id
            LEFT JOIN vendors v  ON v.id = t.vendor_id
            $w ORDER BY t.txn_date ASC, t.id ASC
        ", $p);

        // Calculate running balance from the beginning
        $startBalance = 0;
        if ($from) {
            $ob = $this->fetchOne("
                SELECT COALESCE(SUM(CASE WHEN credit_ledger_id=? THEN amount ELSE 0 END),0)
                     - COALESCE(SUM(CASE WHEN debit_ledger_id=?  THEN amount ELSE 0 END),0) AS opening
                FROM transactions WHERE txn_date < ?
                  AND (debit_ledger_id=? OR credit_ledger_id=?)
            ", [$id, $id, $from, $id, $id]);
            // Opening = current balance minus movements after start_date
            $afterFrom = $this->fetchOne("
                SELECT COALESCE(SUM(CASE WHEN credit_ledger_id=? THEN amount ELSE 0 END),0)
                     - COALESCE(SUM(CASE WHEN debit_ledger_id=?  THEN amount ELSE 0 END),0) AS net
                FROM transactions WHERE txn_date >= ?
                  AND (debit_ledger_id=? OR credit_ledger_id=?)
            ", [$id, $id, $from, $id, $id]);
            $startBalance = (float)$ledger['current_balance'] - (float)($afterFrom['net'] ?? 0);
        }
        // else startBalance = 0 (compute pure running from first transaction)

        $runningBalance = $startBalance;
        $totalDebit = 0; $totalCredit = 0;
        foreach ($transactions as &$t) {
            if ((int)$t['debit_ledger_id'] === $id) {
                $t['dr_amount'] = (float)$t['amount'];
                $t['cr_amount'] = 0;
                $totalDebit    += (float)$t['amount'];
                $runningBalance -= (float)$t['amount'];
            } else {
                $t['dr_amount'] = 0;
                $t['cr_amount'] = (float)$t['amount'];
                $totalCredit   += (float)$t['amount'];
                $runningBalance += (float)$t['amount'];
            }
            $t['running_balance'] = $runningBalance;
        }
        unset($t);
        $transactions = array_reverse($transactions);

        // Monthly summary for this ledger (last 12 months)
        $monthly = $this->fetchAll("
            SELECT DATE_FORMAT(txn_date,'%b %Y') AS mon,
                   DATE_FORMAT(txn_date,'%Y-%m')  AS mon_key,
                   SUM(CASE WHEN debit_ledger_id=?  THEN amount ELSE 0 END) AS debit,
                   SUM(CASE WHEN credit_ledger_id=? THEN amount ELSE 0 END) AS credit,
                   COUNT(*) AS cnt
            FROM transactions
            WHERE (debit_ledger_id=? OR credit_ledger_id=?)
              AND txn_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(txn_date,'%Y-%m'), DATE_FORMAT(txn_date,'%b %Y')
            ORDER BY mon_key ASC
        ", [$id, $id, $id, $id]);

        $this->view('accounts/ledger_view',
            compact('ledger','transactions','totalDebit','totalCredit','startBalance','monthly','from','to')
            + ['pageTitle' => $ledger['account_name'].' — Account Statement', 'active' => 'accounts']);
    }
}
