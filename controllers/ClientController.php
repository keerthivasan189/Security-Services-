<?php
require_once BASE_PATH . '/controllers/BaseController.php';

class ClientController extends BaseController {

    public function index(?string $param = null): void {
        $status   = Helper::get('status', 'active');
        $search   = Helper::get('search');
        $branch   = Helper::get('branch');
        $schedule = Helper::get('schedule');
        $billType = Helper::get('bill_type');

        $where = 'WHERE status=?'; $params = [$status];
        if ($search)   { $where .= ' AND (company_name LIKE ? OR client_code LIKE ? OR contact_person LIKE ? OR mobile LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($branch)   { $where .= ' AND branch=?'; $params[] = $branch; }
        if ($schedule) { $where .= ' AND invoice_schedule=?'; $params[] = $schedule; }
        if ($billType) { $where .= ' AND bill_type=?'; $params[] = $billType; }

        $clients  = $this->fetchAll("SELECT c.*, (SELECT COUNT(*) FROM trades WHERE client_id = c.id) AS total_trades FROM clients c $where ORDER BY c.company_name", $params);
        $branches = array_column($this->fetchAll("SELECT DISTINCT branch FROM clients WHERE branch IS NOT NULL ORDER BY branch"), 'branch');
        $schedules = array_column($this->fetchAll("SELECT DISTINCT invoice_schedule FROM clients WHERE invoice_schedule IS NOT NULL ORDER BY invoice_schedule"), 'invoice_schedule');

        // Counts per category
        $counts = [
            'pre_client' => (int)$this->pdo->query("SELECT COUNT(*) FROM clients WHERE status='pre_client'")->fetchColumn(),
            'active'     => (int)$this->pdo->query("SELECT COUNT(*) FROM clients WHERE status='active'")->fetchColumn(),
            'inactive'   => (int)$this->pdo->query("SELECT COUNT(*) FROM clients WHERE status='inactive'")->fetchColumn(),
        ];

        $this->view('clients/index', compact('clients','status','search','branch','schedule','billType','branches','schedules','counts')
            + ['pageTitle' => 'Client Master', 'active' => 'clients']);
    }

    public function add(?string $param = null): void {
        if (Helper::isPost()) {
            $shifts = implode(',', $_POST['service_shifts'] ?? []);
            $woffs  = implode(',', $_POST['weekly_off_for'] ?? []);
            $coffs  = implode(',', $_POST['company_off_days'] ?? []);
            $quo    = Helper::uploadFile('quotation_file', 'clients');
            
            $this->execute("
                INSERT INTO clients (
                    client_code, company_name, phone, ext_no, contact_person, role, mobile, whatsapp,
                    email, gstin, address, branch, field_officer, state, district, taluk, town, pincode,
                    service_shifts, invoice_calculation_by, invoice_schedule, bill_type, status, reference, quotation_file, preclient_remarks,
                    work_order_no, work_order_date, gst_exempted, gst_calc_method, sac_code, salary_calc_employee, month_denominator,
                    tds_avail, tds_percent, tds_on_gst_percent, epf_avail, epf_percent, epf_on_value, esi_avail, esi_percent, esi_on_value,
                    weekly_off_for, company_off_days, hours_per_duty, bill_send_by, grace_period,
                    service_charges_avail, service_charges_type, service_charges_value,
                    doc_attach_invoice, prev_contractor_name, prev_contractor_mobile, strength,
                    bank_account_show, bill_header, bill_footer
                ) VALUES (" . implode(',', array_fill(0, 57, '?')) . ")
            ", [
                'CLT-' . rand(1000,9999), 
                Helper::post('company_name'), Helper::post('phone'), Helper::post('ext_no'),
                Helper::post('contact_person'), Helper::post('role'), Helper::post('mobile'), Helper::post('whatsapp'),
                Helper::post('email'), Helper::post('gstin'), Helper::post('address'), Helper::post('branch', 'CUDDALORE(HO)'), Helper::post('field_officer'),
                Helper::post('state', 'TAMIL NADU'), Helper::post('district'), Helper::post('taluk'), Helper::post('town'), Helper::post('pincode'),
                $shifts, Helper::post('invoice_calculation_by'), Helper::post('invoice_schedule') ?: null, Helper::post('bill_type', 'GST'),
                Helper::post('status', 'pre_client'), Helper::post('reference'), $quo, Helper::post('preclient_remarks'),
                Helper::post('work_order_no'), Helper::post('work_order_date') ?: null,
                Helper::post('gst_exempted','No'), Helper::post('gst_calc_method'), Helper::post('sac_code'), Helper::post('salary_calc_employee'), Helper::post('month_denominator'),
                Helper::post('tds_avail','No'), Helper::post('tds_percent')?:null, Helper::post('tds_on_gst_percent')?:null,
                Helper::post('epf_avail','No'), Helper::post('epf_percent')?:null, Helper::post('epf_on_value'),
                Helper::post('esi_avail','No'), Helper::post('esi_percent')?:null, Helper::post('esi_on_value'),
                $woffs, $coffs, (int)Helper::post('hours_per_duty', 8),
                Helper::post('bill_send_by'), (int)Helper::post('grace_period', 0),
                Helper::post('service_charges_avail','No'), Helper::post('service_charges_type'), Helper::post('service_charges_value')?:null,
                Helper::post('doc_attach_invoice'), Helper::post('prev_contractor_name'), Helper::post('prev_contractor_mobile'), Helper::post('strength'),
                Helper::post('bank_account_show'), Helper::post('bill_header','YES'), Helper::post('bill_footer','YES')
            ]);
            Session::flash('success', 'Client added successfully.');
            $this->redirect('clients/index&status=' . Helper::post('status', 'pre_client'));
        }
        $this->view('clients/add', ['pageTitle' => 'Add Client', 'active' => 'clients']);
    }

    public function edit(?string $id = null): void {
        $client = $this->fetchOne("SELECT * FROM clients WHERE id=?", [$id]);
        if (!$client) { Session::flash('error', 'Client not found.'); $this->redirect('clients/index'); }

        if (Helper::isPost()) {
            $shifts = implode(',', $_POST['service_shifts'] ?? []);
            $woffs  = implode(',', $_POST['weekly_off_for'] ?? []);
            $coffs  = implode(',', $_POST['company_off_days'] ?? []);
            $quo    = Helper::uploadFile('quotation_file', 'clients') ?: $client['quotation_file'];
            
            $this->execute("
                UPDATE clients SET 
                    company_name=?, phone=?, ext_no=?, contact_person=?, role=?, mobile=?, whatsapp=?,
                    email=?, gstin=?, address=?, branch=?, field_officer=?, state=?, district=?, taluk=?, town=?, pincode=?,
                    service_shifts=?, invoice_calculation_by=?, invoice_schedule=?, bill_type=?, status=?, reference=?, quotation_file=?, preclient_remarks=?,
                    work_order_no=?, work_order_date=?, gst_exempted=?, gst_calc_method=?, sac_code=?, salary_calc_employee=?, month_denominator=?,
                    tds_avail=?, tds_percent=?, tds_on_gst_percent=?, epf_avail=?, epf_percent=?, epf_on_value=?, esi_avail=?, esi_percent=?, esi_on_value=?,
                    weekly_off_for=?, company_off_days=?, hours_per_duty=?, bill_send_by=?, grace_period=?,
                    service_charges_avail=?, service_charges_type=?, service_charges_value=?,
                    doc_attach_invoice=?, prev_contractor_name=?, prev_contractor_mobile=?, strength=?,
                    bank_account_show=?, bill_header=?, bill_footer=?
                WHERE id=?
            ", [
                Helper::post('company_name'), Helper::post('phone'), Helper::post('ext_no'),
                Helper::post('contact_person'), Helper::post('role'), Helper::post('mobile'), Helper::post('whatsapp'),
                Helper::post('email'), Helper::post('gstin'), Helper::post('address'), Helper::post('branch'), Helper::post('field_officer'),
                Helper::post('state'), Helper::post('district'), Helper::post('taluk'), Helper::post('town'), Helper::post('pincode'),
                $shifts, Helper::post('invoice_calculation_by'), Helper::post('invoice_schedule') ?: null, Helper::post('bill_type'),
                Helper::post('status'), Helper::post('reference'), $quo, Helper::post('preclient_remarks'),
                Helper::post('work_order_no'), Helper::post('work_order_date') ?: null,
                Helper::post('gst_exempted','No'), Helper::post('gst_calc_method'), Helper::post('sac_code'), Helper::post('salary_calc_employee'), Helper::post('month_denominator'),
                Helper::post('tds_avail','No'), Helper::post('tds_percent')?:null, Helper::post('tds_on_gst_percent')?:null,
                Helper::post('epf_avail','No'), Helper::post('epf_percent')?:null, Helper::post('epf_on_value'),
                Helper::post('esi_avail','No'), Helper::post('esi_percent')?:null, Helper::post('esi_on_value'),
                $woffs, $coffs, (int)Helper::post('hours_per_duty', 8),
                Helper::post('bill_send_by'), (int)Helper::post('grace_period', 0),
                Helper::post('service_charges_avail','No'), Helper::post('service_charges_type'), Helper::post('service_charges_value')?:null,
                Helper::post('doc_attach_invoice'), Helper::post('prev_contractor_name'), Helper::post('prev_contractor_mobile'), Helper::post('strength'),
                Helper::post('bank_account_show'), Helper::post('bill_header','YES'), Helper::post('bill_footer','YES'),
                $id
            ]);
            Session::flash('success', 'Client updated successfully.');
            $this->redirect('clients/profile/' . $id);
        }
        $this->view('clients/edit', compact('client') + ['pageTitle' => 'Edit Client', 'active' => 'clients']);
    }

    public function migrate(?string $clientId = null): void {
        $this->execute("UPDATE clients SET status='active' WHERE id=?", [$clientId]);
        Session::flash('success', 'Client migrated to active status.');
        $this->redirect('clients/index&status=active');
    }

    public function delete(?string $id = null): void {
        $client = $this->fetchOne("SELECT * FROM clients WHERE id=?", [$id]);
        if (!$client) { Session::flash('error', 'Client not found.'); $this->redirect('clients/index'); }
        
        // Remove related trades and positions
        $this->execute("DELETE FROM positions WHERE client_id=?", [$id]);
        $this->execute("DELETE FROM trades WHERE client_id=?", [$id]);
        $this->execute("DELETE FROM clients WHERE id=?", [$id]);
        
        Session::flash('success', 'Client and related data deleted.');
        $this->redirect('clients/index&status=' . ($client['status'] ?? 'active'));
    }

    public function calllogs(?string $param = null): void {
        $this->redirect('clients/index');
    }

    /** Client Profile with tabs */
    public function profile(?string $id = null): void {
        $client = $this->fetchOne("SELECT * FROM clients WHERE id=?", [$id]);
        if (!$client) { Session::flash('error', 'Client not found.'); $this->redirect('clients/index'); }

        // Assigned employees
        $employees = $this->fetchAll("
            SELECT e.emp_code, e.name, e.designation, e.mobile, t.shift, p.appointed_date, p.status
            FROM positions p
            JOIN employees e ON e.id = p.employee_id
            JOIN trades t ON t.id = p.trade_id
            WHERE p.client_id = ? ORDER BY p.status ASC, e.name
        ", [$id]);

        // Trades
        $trades = $this->fetchAll("SELECT * FROM trades WHERE client_id=? ORDER BY designation", [$id]);

        // Invoice summary
        $invoiceSummary = $this->fetchOne("
            SELECT COUNT(*) AS total_invoices,
                   COALESCE(SUM(grand_total),0) AS total_billed,
                   COALESCE(SUM(total_outstanding),0) AS total_outstanding
            FROM invoices WHERE client_id=?
        ", [$id]);

        $this->view('clients/profile', compact('client','employees','trades','invoiceSummary')
            + ['pageTitle' => $client['company_name'], 'active' => 'clients']);
    }

    public function trades(?string $clientId = null): void {
        $client = $this->fetchOne("SELECT * FROM clients WHERE id=?", [$clientId]);
        if (!$client) { Session::flash('error','Client not found.'); $this->redirect('clients/index'); }

        if (Helper::isPost() && Helper::post('action') === 'add_trade') {
            $this->execute("INSERT INTO trades (
                client_id, designation, shift, salary_basis, billing_mode, rate, payable, no_of_positions,
                epf_amount, esi_amount, days_for_incentives, attendance_incentive, remarks
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", [
                $clientId, Helper::post('designation'), Helper::post('shift'),
                Helper::post('salary_basis','PRO MONTH'), Helper::post('salary_basis','PRO MONTH'),
                Helper::post('rate','0'),
                Helper::post('payable','0'), (int)(Helper::post('no_of_positions')?:0),
                Helper::post('epf_amount','0'), Helper::post('esi_amount','0'),
                Helper::post('days_for_incentives','Full Month'), Helper::post('attendance_incentive','0'),
                Helper::post('remarks')
            ]);
            Session::flash('success','Trade added.');
            $this->redirect('clients/trades/'.$clientId);
        }
        if (Helper::isPost() && Helper::post('action') === 'edit_trade') {
            $this->execute("UPDATE trades SET
                designation=?, shift=?, salary_basis=?, billing_mode=?, rate=?, payable=?, no_of_positions=?,
                epf_amount=?, esi_amount=?, days_for_incentives=?, attendance_incentive=?, remarks=?
            WHERE id=? AND client_id=?", [
                Helper::post('designation'), Helper::post('shift'),
                Helper::post('salary_basis','PRO MONTH'), Helper::post('salary_basis','PRO MONTH'),
                Helper::post('rate','0'),
                Helper::post('payable','0'), (int)(Helper::post('no_of_positions')?:0),
                Helper::post('epf_amount','0'), Helper::post('esi_amount','0'),
                Helper::post('days_for_incentives','Full Month'), Helper::post('attendance_incentive','0'),
                Helper::post('remarks'), Helper::post('trade_id'), $clientId
            ]);
            Session::flash('success','Trade updated.');
            $this->redirect('clients/trades/'.$clientId);
        }
        if (Helper::isPost() && Helper::post('action') === 'delete_trade') {
            $this->execute("DELETE FROM trades WHERE id=? AND client_id=?", [Helper::post('trade_id'),$clientId]);
            Session::flash('success','Trade deleted.');
        }

        if (Helper::isPost() && Helper::post('action') === 'assign_staff') {
            $empId = Helper::post('employee_id');
            
            // Check for existing active position to handle transfer
            $prev = $this->fetchOne("SELECT id, client_id FROM positions WHERE employee_id=? AND status='active' LIMIT 1", [$empId]);
            if ($prev) {
                $this->execute("UPDATE positions SET status='transferred', relieved_date=? WHERE id=?", [
                    Helper::post('appointed_date') ?: date('Y-m-d'), $prev['id']
                ]);
            }

            $this->execute("INSERT INTO positions (employee_id, trade_id, client_id, appointed_date, status) VALUES (?,?,?,?,?)", [
                $empId, Helper::post('trade_id'), $clientId,
                Helper::post('appointed_date') ?: date('Y-m-d'), 'active'
            ]);
            Session::flash('success', $prev ? 'Staff transferred to new client site.' : 'Staff assigned to trade.');
            $this->redirect('clients/trades/'.$clientId);
        }

        if (Helper::isPost() && Helper::post('action') === 'unassign_staff') {
            $this->execute("UPDATE positions SET status='relieved', relieved_date=? WHERE id=? AND client_id=?", [
                date('Y-m-d'), Helper::post('position_id'), $clientId
            ]);
            Session::flash('success','Staff removed from trade.');
            $this->redirect('clients/trades/'.$clientId);
        }
        
        $trades = $this->fetchAll("SELECT * FROM trades WHERE client_id=? ORDER BY designation", [$clientId]);
        
        // Fetch current assignments for each trade
        foreach ($trades as &$t) {
            $t['assignments'] = $this->fetchAll("
                SELECT p.id AS position_id, e.name, e.emp_code, p.appointed_date,
                       (SELECT c2.company_name FROM positions p2 JOIN clients c2 ON c2.id=p2.client_id WHERE p2.employee_id=e.id AND p2.status='transferred' AND p2.relieved_date=p.appointed_date LIMIT 1) AS prev_site
                FROM positions p 
                JOIN employees e ON e.id = p.employee_id 
                WHERE p.trade_id = ? AND p.status = 'active'
            ", [$t['id']]);
        }

        $allEmployees = $this->fetchAll("
            SELECT e.id, e.name, e.emp_code, e.designation, c.company_name AS current_site
            FROM employees e
            LEFT JOIN positions p ON p.employee_id = e.id AND p.status = 'active'
            LEFT JOIN clients c ON c.id = p.client_id
            WHERE e.status='active' 
            ORDER BY e.name
        ");
        
        $editTrade = null;
        if (isset($_GET['edit_id'])) {
            $editTrade = $this->fetchOne("SELECT * FROM trades WHERE id=? AND client_id=?", [$_GET['edit_id'], $clientId]);
        }
        
        $this->view('clients/trades', compact('client','trades','editTrade','allEmployees') + ['pageTitle'=>'Manage Trades','active'=>'clients']);
    }
}
