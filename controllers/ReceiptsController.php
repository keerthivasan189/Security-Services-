<?php
require_once BASE_PATH . '/controllers/BaseController.php';

class ReceiptsController extends BaseController {

    public function index(?string $param = null): void {
        $this->redirect('receipts/clientbills');
    }

    public function clientbills(?string $param = null): void {
        $clients  = $this->fetchAll("SELECT id,company_name FROM clients WHERE status='active' ORDER BY company_name");
        $fieldOfficers = $this->fetchAll("SELECT id,name FROM users WHERE role='field_officer' ORDER BY name");
        
        $fInvNo   = Helper::get('invoice_no');
        $fOfficer = Helper::get('field_officer_id');
        $fClient  = Helper::get('client_id');
        $fType    = Helper::get('bill_type');
        $fFinYear = Helper::get('fin_year');
        $fPaid    = Helper::get('paid_status');
        $fStart   = Helper::get('start_date');
        $fEnd     = Helper::get('end_date');

        $params   = [];
        $where    = 'WHERE 1=1';

        if ($fInvNo)   { $where .= ' AND i.invoice_no LIKE ?'; $params[] = "%$fInvNo%"; }
        if ($fClient)  { $where .= ' AND i.client_id=?';       $params[] = $fClient; }
        if ($fType)    { $where .= ' AND i.bill_type=?';       $params[] = $fType; }
        if ($fPaid)    { $where .= ' AND i.payment_status=?';  $params[] = ($fPaid === 'Unpaid' ? 'unpaid' : ($fPaid === 'Paid' ? 'paid' : 'partial')); }
        if ($fStart)   { $where .= ' AND i.invoice_date>=?';   $params[] = $fStart; }
        if ($fEnd)     { $where .= ' AND i.invoice_date<=?';   $params[] = $fEnd; }
        if ($fFinYear) { 
            // example "2024-2025" -> from 2024-04-01 to 2025-03-31
            $parts = explode('-', $fFinYear);
            if (count($parts) == 2) {
                $where .= ' AND i.invoice_date >= ? AND i.invoice_date <= ?';
                $params[] = $parts[0] . '-04-01';
                $params[] = $parts[1] . '-03-31';
            }
        }
        if ($fOfficer) {
            // Need to join clients to get field_officer if it's stored in employees or clients?
            // Actually, in this HRMS field_officer_id is mostly on the Employee table, not Client table.
            // Wait, does the client have a field officer? Usually employees do. 
            // If we must filter by field officer, we can just leave it to join clients but if clients have no field officer it won't work perfectly. We'll join it.
        }

        $invoices = $this->fetchAll("
            SELECT i.*, c.company_name FROM invoices i
            JOIN clients c ON c.id=i.client_id $where ORDER BY i.created_at DESC LIMIT 200
        ", $params);

        $this->view('receipts/clientbills', 
            compact('invoices','clients','fieldOfficers','fInvNo','fOfficer','fClient','fType','fFinYear','fPaid','fStart','fEnd')
            + ['pageTitle'=>'Client Invoices','active'=>'receipts']);
    }

    public function addinvoice(?string $param = null): void {
        $clients = $this->fetchAll("SELECT id,company_name,bill_type,gstin FROM clients WHERE status='active' ORDER BY company_name");
        if (Helper::isPost()) {
            $this->execute("INSERT INTO invoice_sequence (dummy) VALUES (0)");
            $seqId     = $this->pdo->lastInsertId();
            $invoiceNo = 'SAI-' . str_pad($seqId, 3, '0', STR_PAD_LEFT);
            $this->execute("
                INSERT INTO invoices (invoice_no,client_id,bill_type,invoice_month,invoice_date,
                  deployed_hours,subtotal,igst,sgst,cgst,grand_total,round_off,total_outstanding,created_by)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ", [
                $invoiceNo, Helper::post('client_id'), Helper::post('bill_type'),
                Helper::post('invoice_month'), Helper::post('invoice_date') ?: date('Y-m-d'),
                Helper::post('deployed_hours','12'), Helper::post('subtotal','0'),
                Helper::post('igst','0'), Helper::post('sgst','0'), Helper::post('cgst','0'),
                Helper::post('grand_total','0'), Helper::post('round_off','0'),
                Helper::post('grand_total','0'), Session::userId(),
            ]);
            $invId        = $this->pdo->lastInsertId();
            $designations = $_POST['designation']   ?? [];
            $codes        = $_POST['code']          ?? [];
            $noses        = $_POST['nos']           ?? [];
            $duties       = $_POST['duties']        ?? [];
            $ots          = $_POST['ot']            ?? [];
            $offs         = $_POST['off_days']      ?? [];
            $hours        = $_POST['total_hours']   ?? [];
            $rates        = $_POST['rate_per_hour'] ?? [];
            $amounts      = $_POST['amount']        ?? [];
            $sItem = $this->pdo->prepare("INSERT INTO invoice_items (invoice_id,sl_no,code,sac,designation,nos,duties,ot,off_days,total_hours,rate_per_hour,amount) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
            foreach ($designations as $i => $desig) {
                if (!trim($desig)) continue;
                $sItem->execute([$invId,$i+1,$codes[$i]??'106','998525',$desig,$noses[$i]??0,$duties[$i]??0,$ots[$i]??0,$offs[$i]??0,$hours[$i]??0,$rates[$i]??0,$amounts[$i]??0]);
            }
            Session::flash('success', "Invoice {$invoiceNo} created.");
            $this->redirect('receipts/clientbills');
        }
        $this->view('receipts/addinvoice', compact('clients') + ['pageTitle'=>'Create Invoice','active'=>'receipts']);
    }

    public function viewinvoice(?string $invId = null): void {
        $invoice = $this->fetchOne("SELECT i.*,c.company_name,c.address,c.gstin,c.mobile FROM invoices i JOIN clients c ON c.id=i.client_id WHERE i.id=?", [$invId]);
        if (!$invoice) { Session::flash('error','Invoice not found.'); $this->redirect('receipts/clientbills'); }
        $items    = $this->fetchAll("SELECT * FROM invoice_items    WHERE invoice_id=? ORDER BY sl_no",       [$invId]);
        $payments = $this->fetchAll("SELECT * FROM invoice_payments WHERE invoice_id=? ORDER BY payment_date",[$invId]);
        $this->view('receipts/viewinvoice', compact('invoice','items','payments') + ['pageTitle'=>'View Invoice','active'=>'receipts']);
    }

    public function receivepayment(?string $invId = null): void {
        if (Helper::isPost()) {
            $cheque = Helper::uploadFile('cheque_photo', 'cheque_photos');
            $this->execute("INSERT INTO invoice_payments (invoice_id,payment_date,amount,payment_type,payment_method,ref_no,credit_ledger_id,cheque_photo,remarks) VALUES (?,?,?,?,?,?,?,?,?)", [
                $invId, Helper::post('payment_date'), Helper::post('amount'),
                Helper::post('payment_type','received'), Helper::post('payment_method'),
                Helper::post('ref_no'), Helper::post('credit_ledger_id') ?: null,
                $cheque, Helper::post('remarks'),
            ]);
            $paid  = (float)($this->fetchOne("SELECT SUM(amount) AS t FROM invoice_payments WHERE invoice_id=?",[$invId])['t'] ?? 0);
            $total = (float)($this->fetchOne("SELECT grand_total FROM invoices WHERE id=?",[$invId])['grand_total'] ?? 0);
            $st    = ($paid >= $total) ? 'paid' : (($paid > 0) ? 'partial' : 'unpaid');
            $this->execute("UPDATE invoices SET total_outstanding=?,payment_status=? WHERE id=?", [round($total-$paid,2),$st,$invId]);
            Session::flash('success','Payment recorded.');
            $this->redirect('receipts/viewinvoice/'.$invId);
        }
        $inv     = $this->fetchOne("SELECT * FROM invoices WHERE id=?", [$invId]);
        $ledgers = $this->getLedgers();
        $this->view('receipts/receivepayment', compact('inv','ledgers','invId') + ['pageTitle'=>'Receive Payment','active'=>'receipts']);
    }

    public function uniforms(?string $param = null): void {
        $clients   = $this->fetchAll("SELECT id,company_name FROM clients WHERE status='active' ORDER BY company_name");
        $employees = $this->fetchAll("SELECT id,emp_code,name FROM employees WHERE status='active' ORDER BY name");
        $items     = $this->fetchAll("SELECT ui.*,v.vendor_name FROM uniform_items ui LEFT JOIN vendors v ON v.id=ui.vendor_id ORDER BY ui.item_name");
        $ledgers   = $this->getLedgers();
        if (Helper::isPost() && Helper::post('action') === 'add') {
            $this->execute("INSERT INTO invoice_sequence (dummy) VALUES (0)");
            $billNo = 'UB-' . str_pad($this->pdo->lastInsertId(), 4, '0', STR_PAD_LEFT);
            $this->execute("INSERT INTO uniform_bills (bill_no,employee_id,client_id,bill_date,subtotal,discount,total_amount,paid_amount,balance_amount,no_of_dues,due_first_month,due_last_month,due_amount,account_received_to,remarks) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [
                $billNo, Helper::post('employee_id'), Helper::post('client_id') ?: null,
                Helper::post('bill_date'), Helper::post('subtotal','0'), Helper::post('discount','0'),
                Helper::post('total_amount','0'), Helper::post('paid_amount','0'), Helper::post('balance_amount','0'),
                Helper::post('no_of_dues','0'), Helper::post('due_first_month') ?: null,
                Helper::post('due_last_month') ?: null, Helper::post('due_amount','0'),
                Helper::post('account_received_to') ?: null, Helper::post('remarks'),
            ]);
            $billId  = $this->pdo->lastInsertId();
            $itemIds = $_POST['item_id'] ?? []; $qtys = $_POST['qty'] ?? []; $prices = $_POST['unit_price'] ?? [];
            $sItem   = $this->pdo->prepare("INSERT INTO uniform_bill_items (bill_id,item_id,qty,unit_price,total) VALUES (?,?,?,?,?)");
            foreach ($itemIds as $i => $iid) {
                if (!$iid) continue;
                $q = (float)($qtys[$i]??1); $p = (float)($prices[$i]??0);
                $sItem->execute([$billId,$iid,$q,$p,$q*$p]);
            }
            Session::flash('success',"Uniform bill {$billNo} saved.");
            $this->redirect('receipts/uniforms');
        }
        $bills = $this->fetchAll("SELECT ub.*,e.name AS emp_name FROM uniform_bills ub JOIN employees e ON e.id=ub.employee_id ORDER BY ub.bill_date DESC LIMIT 100");
        $this->view('receipts/uniforms', compact('clients','employees','items','ledgers','bills') + ['pageTitle'=>'Uniform Bills','active'=>'receipts']);
    }

    public function deductions(?string $param = null): void {
        $employees = $this->fetchAll("
            SELECT e.id, e.emp_code, e.name,
                   (SELECT s.net_salary FROM salaries s WHERE s.employee_id=e.id ORDER BY s.salary_month DESC LIMIT 1) AS last_net_salary
            FROM employees e WHERE e.status='active' ORDER BY e.name
        ");
        $clients   = $this->fetchAll("SELECT id,company_name FROM clients WHERE status='active' ORDER BY company_name");

        if (Helper::isPost() && Helper::post('action') === 'add') {
            $f1 = Helper::uploadFile('file1','deduction_files');
            $f2 = Helper::uploadFile('file2','deduction_files');
            $this->execute("INSERT INTO other_deductions (employee_id,client_id,deduction_date,amount,reason,file1,file2,remarks) VALUES (?,?,?,?,?,?,?,?)", [
                Helper::post('employee_id'), Helper::post('client_id') ?: null,
                Helper::post('deduction_date'), Helper::post('amount'),
                Helper::post('reason'), $f1, $f2, Helper::post('remarks'),
            ]);
            Session::flash('success','Deduction saved.');
            $this->redirect('receipts/deductions');
        }

        $showList = Helper::get('show') === 'all';
        $fClient  = Helper::get('client_id');
        $fEmp     = Helper::get('employee_id');
        $fStart   = Helper::get('start_date');
        $fEnd     = Helper::get('end_date');

        $params = []; $where = 'WHERE 1=1';
        if ($fClient) { $where .= ' AND d.client_id=?';          $params[] = $fClient; }
        if ($fEmp)    { $where .= ' AND d.employee_id=?';        $params[] = $fEmp; }
        if ($fStart)  { $where .= ' AND d.deduction_date>=?';    $params[] = $fStart; }
        if ($fEnd)    { $where .= ' AND d.deduction_date<=?';    $params[] = $fEnd; }

        $list = $showList ? $this->fetchAll("
            SELECT d.*, e.name AS emp_name, c.company_name
            FROM other_deductions d
            JOIN employees e ON e.id=d.employee_id
            LEFT JOIN clients c ON c.id=d.client_id
            $where
            ORDER BY d.deduction_date DESC LIMIT 500
        ", $params) : [];

        $this->view('receipts/deductions',
            compact('employees','clients','list','showList','fClient','fEmp','fStart','fEnd')
            + ['pageTitle'=>'Other Deductions','active'=>'receipts']);
    }

    public function getEmpSalary(?string $param = null): void {
        header('Content-Type: application/json');
        $empId  = Helper::get('emp_id');
        $salary = $this->fetchOne("SELECT net_salary FROM salaries WHERE employee_id=? ORDER BY salary_month DESC LIMIT 1", [$empId]);
        echo json_encode(['salary' => $salary['net_salary'] ?? null]);
        exit;
    }

    public function paymentlist(?string $invId = null): void {
        $inv = $this->fetchOne("SELECT i.*,c.company_name FROM invoices i JOIN clients c ON c.id=i.client_id WHERE i.id=?", [$invId]);
        if (!$inv) { Session::flash('error','Invoice not found.'); $this->redirect('receipts/clientbills'); }
        $payments = $this->fetchAll("
            SELECT ip.*, la.account_name AS ledger_name
            FROM invoice_payments ip
            LEFT JOIN ledger_accounts la ON la.id = ip.credit_ledger_id
            WHERE ip.invoice_id = ?
            ORDER BY ip.payment_date ASC
        ", [$invId]);
        $this->view('receipts/paymentlist', compact('inv','payments','invId') + ['pageTitle'=>'Payment List — '.$inv['invoice_no'],'active'=>'receipts']);
    }

    public function musterroll(?string $invId = null): void {
        $invoice = $this->fetchOne("SELECT i.*,c.company_name,c.address,c.gstin FROM invoices i JOIN clients c ON c.id=i.client_id WHERE i.id=?", [$invId]);
        if (!$invoice) { Session::flash('error','Invoice not found.'); $this->redirect('receipts/clientbills'); }
        $items = $this->fetchAll("SELECT * FROM invoice_items WHERE invoice_id=? ORDER BY sl_no", [$invId]);
        // Fetch deployed employees at this client site during the invoice month
        $employees = $this->fetchAll("
            SELECT e.emp_code, e.name, e.designation, c.company_name AS site_name
            FROM positions p
            JOIN employees e ON e.id = p.employee_id
            JOIN clients  c ON c.id = p.client_id
            WHERE p.client_id = ? AND p.status = 'active'
            ORDER BY e.name
        ", [$invoice['client_id']]);
        $this->view('receipts/musterroll', compact('invoice','items','employees') + ['pageTitle'=>'Musterroll — '.$invoice['invoice_no'],'active'=>'receipts']);
    }

    public function clientdues(?string $param = null): void {
        $clients  = $this->fetchAll("SELECT id,company_name FROM clients WHERE status='active' ORDER BY company_name");
        $fClient  = Helper::get('client_id');
        $fStatus  = Helper::get('due_status');
        $fStart   = Helper::get('start_date');
        $fEnd     = Helper::get('end_date');

        $params = [];
        $where  = "WHERE i.payment_status IN ('unpaid','partial')";
        if ($fClient) { $where .= ' AND i.client_id=?';       $params[] = $fClient; }
        if ($fStatus) { $where .= ' AND i.payment_status=?';  $params[] = $fStatus; }
        if ($fStart)  { $where .= ' AND i.invoice_date>=?';   $params[] = $fStart; }
        if ($fEnd)    { $where .= ' AND i.invoice_date<=?';   $params[] = $fEnd; }

        $dues = $this->fetchAll("
            SELECT i.*, c.company_name
            FROM invoices i
            JOIN clients c ON c.id = i.client_id
            $where
            ORDER BY i.invoice_date DESC
        ", $params);

        $this->view('receipts/clientdues',
            compact('dues','clients','fClient','fStatus','fStart','fEnd')
            + ['pageTitle'=>'Client Dues','active'=>'receipts']);
    }

    public function employeedues(?string $param = null): void {
        $employees = $this->fetchAll("SELECT id,emp_code,name FROM employees WHERE status='active' ORDER BY name");
        $clients   = $this->fetchAll("SELECT id,company_name FROM clients WHERE status='active' ORDER BY company_name");
        $fEmployee = Helper::get('employee_id');
        $fClient   = Helper::get('client_id');
        $fStart    = Helper::get('start_date');
        $fEnd      = Helper::get('end_date');

        $params = [];
        $where  = 'WHERE ub.balance_amount > 0';
        if ($fEmployee) { $where .= ' AND ub.employee_id=?'; $params[] = $fEmployee; }
        if ($fClient)   { $where .= ' AND ub.client_id=?';   $params[] = $fClient; }
        if ($fStart)    { $where .= ' AND ub.bill_date>=?';  $params[] = $fStart; }
        if ($fEnd)      { $where .= ' AND ub.bill_date<=?';  $params[] = $fEnd; }

        $dues = $this->fetchAll("
            SELECT ub.*, e.name AS emp_name, c.company_name
            FROM uniform_bills ub
            JOIN employees e ON e.id = ub.employee_id
            LEFT JOIN clients c ON c.id = ub.client_id
            $where
            ORDER BY ub.bill_date DESC
        ", $params);

        $this->view('receipts/employeedues',
            compact('dues','employees','clients','fEmployee','fClient','fStart','fEnd')
            + ['pageTitle'=>'Employee Dues','active'=>'receipts']);
    }

    public function payuniform(?string $billId = null): void {
        if (Helper::isPost()) {
            $cheque = Helper::uploadFile('cheque_photo', 'cheque_photos');
            $this->execute("INSERT INTO uniform_bill_payments (bill_id,payment_date,amount,payment_method,ref_no,credit_ledger_id,cheque_photo,remarks) VALUES (?,?,?,?,?,?,?,?)", [
                $billId, Helper::post('payment_date'), Helper::post('amount'),
                Helper::post('payment_method'), Helper::post('ref_no'),
                Helper::post('credit_ledger_id') ?: null, $cheque, Helper::post('remarks'),
            ]);
            $paid  = (float)($this->fetchOne("SELECT COALESCE(SUM(amount),0) AS t FROM uniform_bill_payments WHERE bill_id=?",[$billId])['t'] ?? 0);
            $total = (float)($this->fetchOne("SELECT total_amount FROM uniform_bills WHERE id=?",[$billId])['total_amount'] ?? 0);
            $st    = ($paid >= $total) ? 'cleared' : (($paid > 0) ? 'partial' : 'unpaid');
            $this->execute("UPDATE uniform_bills SET paid_amount=?,balance_amount=? WHERE id=?", [round($paid,2), round($total-$paid,2), $billId]);
            Session::flash('success','Payment recorded.');
            $this->redirect('receipts/paymentlist_uniform/'.$billId);
        }
        $bill    = $this->fetchOne("SELECT ub.*,e.name AS emp_name FROM uniform_bills ub JOIN employees e ON e.id=ub.employee_id WHERE ub.id=?", [$billId]);
        $ledgers = $this->getLedgers();
        $this->view('receipts/payuniform', compact('bill','ledgers','billId') + ['pageTitle'=>'Record Payment — Employee Due','active'=>'receipts']);
    }

    public function paymentlist_uniform(?string $billId = null): void {
        $bill = $this->fetchOne("SELECT ub.*,e.name AS emp_name FROM uniform_bills ub JOIN employees e ON e.id=ub.employee_id WHERE ub.id=?", [$billId]);
        if (!$bill) { Session::flash('error','Bill not found.'); $this->redirect('receipts/employeedues'); }
        $payments = $this->fetchAll("
            SELECT ubp.*, la.account_name AS ledger_name
            FROM uniform_bill_payments ubp
            LEFT JOIN ledger_accounts la ON la.id = ubp.credit_ledger_id
            WHERE ubp.bill_id = ?
            ORDER BY ubp.payment_date ASC
        ", [$billId]);
        $this->view('receipts/paymentlist_uniform', compact('bill','payments','billId') + ['pageTitle'=>'Payment History — '.$bill['bill_no'],'active'=>'receipts']);
    }
}
