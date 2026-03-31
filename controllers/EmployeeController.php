<?php
require_once BASE_PATH . '/controllers/BaseController.php';

class EmployeeController extends BaseController {

    public function index(?string $param = null): void {
        $search    = Helper::get('search');
        $status    = Helper::get('status', 'active');
        $desig     = Helper::get('designation');
        $clientId  = Helper::get('client_id');
        $gender    = Helper::get('gender');
        $officerId = Helper::get('field_officer_id');
        $qual      = Helper::get('qualification_id');
        $expSal    = Helper::get('expected_salary');
        $expComp   = Helper::get('expected_company_id');
        $refBy     = Helper::get('referred_by_id');
        $branch    = Helper::get('branch_id');
        $town      = Helper::get('exp_town_id');
        $startDate = Helper::get('start_date');
        $endDate   = Helper::get('end_date');

        $where = 'WHERE e.status = ?'; $params = [$status];
        if ($search)    { $where .= ' AND (e.name LIKE ? OR e.emp_code LIKE ? OR e.mobile LIKE ? OR e.aadhaar LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($desig)     { $where .= ' AND (e.designation=? OR e.expected_designation_id_1=? OR e.expected_designation_id_2=?)'; $params[] = $desig; $params[] = $desig; $params[] = $desig; }
        if ($gender)    { $where .= ' AND e.gender=?'; $params[] = $gender; }
        if ($officerId) { $where .= ' AND e.field_officer_id=?'; $params[] = $officerId; }
        if ($clientId)  { $where .= ' AND e.id IN (SELECT employee_id FROM positions WHERE client_id=? AND status="active")'; $params[] = $clientId; }
        if ($qual)      { $where .= ' AND e.qualification_id=?'; $params[] = $qual; }
        if ($expSal)    { $where .= ' AND (e.expected_salary_1 >= ? OR e.expected_salary_2 >= ?)'; $params[] = $expSal; $params[] = $expSal; }
        if ($expComp)   { $where .= ' AND (e.expected_company_id_1=? OR e.expected_company_id_2=?)'; $params[] = $expComp; $params[] = $expComp; }
        if ($refBy)     { $where .= ' AND e.referred_by_id=?'; $params[] = $refBy; }
        if ($branch)    { $where .= ' AND e.branch_id=?'; $params[] = $branch; }
        if ($town)      { $where .= ' AND e.exp_town_id=?'; $params[] = $town; }
        if ($startDate) { $where .= ' AND e.created_at >= ?'; $params[] = $startDate . ' 00:00:00'; }
        if ($endDate)   { $where .= ' AND e.created_at <= ?'; $params[] = $endDate . ' 23:59:59'; }

        $employees = $this->fetchAll("
            SELECT e.*, u.name AS field_officer_name,
                   (SELECT c.company_name FROM clients c JOIN positions p ON p.client_id=c.id WHERE p.employee_id=e.id AND p.status='active' LIMIT 1) AS deployed_at
            FROM employees e LEFT JOIN users u ON u.id = e.field_officer_id
            $where ORDER BY e.name LIMIT 500
        ", $params);

        $designations = array_column($this->fetchAll("SELECT DISTINCT designation FROM employees WHERE designation IS NOT NULL AND designation != '' ORDER BY designation"),'designation');
        $clList = $this->allClients();
        $officers = $this->fetchAll("SELECT id,name FROM users WHERE role='field_officer' ORDER BY name");
        $masters = $this->getMasters();

        // Counts
        $counts = [
            'pre_employee' => (int)$this->pdo->query("SELECT COUNT(*) FROM employees WHERE status='pre_employee'")->fetchColumn(),
            'active'       => (int)$this->pdo->query("SELECT COUNT(*) FROM employees WHERE status='active'")->fetchColumn(),
            'inactive'     => (int)$this->pdo->query("SELECT COUNT(*) FROM employees WHERE status='inactive'")->fetchColumn(),
        ];

        $this->view('employees/index', compact('employees','search','status','desig','clientId','gender','officerId','designations','clList','officers','counts','masters')
            + ['pageTitle' => 'Employee List', 'active' => 'employees']);
    }

    private function getMasters(): array {
        return [
            'branches'       => $this->pdo->query("SELECT id, name FROM branches ORDER BY name")->fetchAll(),
            'designations'   => $this->pdo->query("SELECT id, name, daily_rate FROM designations ORDER BY name")->fetchAll(),
            'qualifications' => $this->pdo->query("SELECT id, name FROM master_qualifications ORDER BY name")->fetchAll(),
            'blood_groups'   => $this->pdo->query("SELECT id, name FROM master_blood_groups ORDER BY name")->fetchAll(),
            'body_types'     => $this->pdo->query("SELECT id, name FROM master_body_types ORDER BY name")->fetchAll(),
            'states'         => $this->pdo->query("SELECT id, name FROM master_states ORDER BY name")->fetchAll(),
            'districts'      => $this->pdo->query("SELECT id, name FROM master_districts ORDER BY name")->fetchAll(),
            'taluks'         => $this->pdo->query("SELECT id, name FROM master_taluks ORDER BY name")->fetchAll(),
            'towns'          => $this->pdo->query("SELECT id, name FROM master_towns ORDER BY name")->fetchAll(),
            'relationships'  => $this->pdo->query("SELECT id, name FROM master_relationships ORDER BY name")->fetchAll(),
            'references'     => $this->pdo->query("SELECT id, name FROM master_references ORDER BY name")->fetchAll(),
            'languages'      => $this->pdo->query("SELECT id, name FROM master_languages ORDER BY name")->fetchAll(),
            'salary_modes'   => $this->pdo->query("SELECT id, name FROM master_salary_modes ORDER BY name")->fetchAll(),
            'employees'      => $this->pdo->query("SELECT id, name, emp_code FROM employees WHERE status='active' ORDER BY name")->fetchAll(),
            'officers'       => $this->pdo->query("SELECT id, name FROM users WHERE role='field_officer' ORDER BY name")->fetchAll(),
        ];
    }

    public function add(?string $param = null): void {
        $masters = $this->getMasters();
        $error = '';
        if (Helper::isPost()) {
            $photo = Helper::uploadFile('photo', 'employee_photos');
            $doc_app = Helper::uploadFile('doc_application', 'employee_docs');
            $doc_aadhaar = Helper::uploadFile('doc_aadhaar', 'employee_docs');
            $doc_passbook = Helper::uploadFile('doc_passbook', 'employee_docs');
            $doc_photo_file = Helper::uploadFile('doc_photo', 'employee_photos');
            $doc_settle = Helper::uploadFile('doc_settlement', 'employee_docs');
            $doc_ex1 = Helper::uploadFile('doc_extra_1', 'employee_docs');
            $doc_ex2 = Helper::uploadFile('doc_extra_2', 'employee_docs');
            $doc_ex3 = Helper::uploadFile('doc_extra_3', 'employee_docs');
            
            $cols = [
                'emp_code', 'name', 'father_name', 'mother_name', 'photo', 'designation', 'doj', 'dob',
                'gender', 'married_status', 'mobile', 'whatsapp_number', 'email_id', 'qualification_id',
                'blood_group_id', 'address', 'communication_state_id', 'communication_district_id',
                'communication_taluk_id', 'communication_town_id', 'communication_pincode', 'permanent_address',
                'aadhaar', 'pan', 'uan_no', 'esi_no', 'bank_name', 'bank_account', 'bank_ifsc',
                'basic_wage', 'epf_applicable', 'esi_applicable', 'insurance_amount', 'field_officer_id',
                'branch_id', 'date_of_walkin', 'emg_contact_name', 'emg_contact_relation_id', 'emg_contact_mobile',
                'trusted_person_name', 'trusted_person_relation_id', 'trusted_person_mobile', 'introducer_relate_id',
                'intro_employee_id', 'introducer_name', 'introducer_mobile', 'height_cm', 'weight_kg',
                'chest_inches', 'hip_inches', 'body_type_id', 'work_exp_years', 'prev_company',
                'prev_contact_number', 'prev_strength', 'reason_for_relieve', 'expected_salary_1',
                'expected_salary_2', 'work_hours', 'expected_designation_id_1', 'expected_designation_id_2',
                'expected_company_id_1', 'expected_company_id_2', 'exp_state_id', 'exp_district_id',
                'exp_taluk_id', 'exp_town_id', 'convicted', 'convicted_reason', 'id_mark_1', 'id_mark_2',
                'nearby_police_station', 'referred_by_id', 'remarks_extra', 'status',
                'salary_da', 'salary_hra', 'salary_conv', 'salary_other', 'ot_details', 'salary_med_wash',
                'amt_for_calc_epf', 'amt_for_calc_esi', 'epf_no', 'tds_avail', 'tds_no', 
                'nominee_name', 'nominee_relation_id', 'ins_number', 'ins_renewal_date', 'premium_amount',
                'will_have_header', 'will_have_footer', 'bank_branch',
                'doc_application', 'doc_aadhaar', 'doc_passbook', 'doc_photo', 'doc_settlement',
                'doc_extra_1', 'doc_extra_2', 'doc_extra_3'
            ];
            
            $placeholders = implode(',', array_fill(0, count($cols), '?'));
            $sql = "INSERT INTO employees (" . implode(',', $cols) . ") VALUES ($placeholders)";
            
            $values = [
                'TEMP', Helper::post('name'), Helper::post('father_name'), Helper::post('mother_name'), $photo,
                Helper::post('designation'), Helper::post('doj') ?: null, Helper::post('dob') ?: null,
                Helper::post('gender'), Helper::post('married_status'), Helper::post('mobile'),
                Helper::post('whatsapp_number'), Helper::post('email_id'), Helper::post('qualification_id') ?: null,
                Helper::post('blood_group_id') ?: null, Helper::post('address'), Helper::post('communication_state_id') ?: null,
                Helper::post('communication_district_id') ?: null, Helper::post('communication_taluk_id') ?: null,
                Helper::post('communication_town_id') ?: null, Helper::post('communication_pincode'), Helper::post('permanent_address'),
                Helper::post('aadhaar'), Helper::post('pan'), Helper::post('uan_no'), Helper::post('esi_no'),
                Helper::post('bank_name'), Helper::post('bank_account'), Helper::post('bank_ifsc'),
                (float)Helper::post('basic_wage', 0), Helper::post('epf_applicable', 'NO'), Helper::post('esi_applicable', 'NO'),
                (float)Helper::post('insurance_amount', 0), Helper::post('field_officer_id') ?: null,
                Helper::post('branch_id') ?: null, Helper::post('date_of_walkin') ?: null,
                Helper::post('emg_contact_name'), Helper::post('emg_contact_relation_id') ?: null, Helper::post('emg_contact_mobile'),
                Helper::post('trusted_person_name'), Helper::post('trusted_person_relation_id') ?: null, Helper::post('trusted_person_mobile'),
                Helper::post('introducer_relate_id') ?: null, Helper::post('intro_employee_id') ?: null,
                Helper::post('introducer_name'), Helper::post('introducer_mobile'),
                (float)Helper::post('height_cm', 0), (float)Helper::post('weight_kg', 0),
                (float)Helper::post('chest_inches', 0), (float)Helper::post('hip_inches', 0),
                Helper::post('body_type_id') ?: null, (int)Helper::post('work_exp_years', 0),
                Helper::post('prev_company'), Helper::post('prev_contact_number'), Helper::post('prev_strength'),
                Helper::post('reason_for_relieve'), (float)Helper::post('expected_salary_1', 0),
                (float)Helper::post('expected_salary_2', 0), (int)Helper::post('work_hours', 8),
                Helper::post('expected_designation_id_1') ?: null, Helper::post('expected_designation_id_2') ?: null,
                Helper::post('expected_company_id_1') ?: null, Helper::post('expected_company_id_2') ?: null,
                Helper::post('exp_state_id') ?: null, Helper::post('exp_district_id') ?: null,
                Helper::post('exp_taluk_id') ?: null, Helper::post('exp_town_id') ?: null,
                Helper::post('convicted', 'No'), Helper::post('convicted_reason'),
                Helper::post('id_mark_1'), Helper::post('id_mark_2'), Helper::post('nearby_police_station'),
                Helper::post('referred_by_id') ?: null, Helper::post('remarks_extra'),
                Helper::post('status', 'active'),
                // Phase 2 Fields
                (float)Helper::post('salary_da', 0), (float)Helper::post('salary_hra', 0), (float)Helper::post('salary_conv', 0),
                (float)Helper::post('salary_other', 0), Helper::post('ot_details'), (float)Helper::post('salary_med_wash', 0),
                (float)Helper::post('amt_for_calc_epf', 0), (float)Helper::post('amt_for_calc_esi', 0),
                Helper::post('epf_no'), Helper::post('tds_avail', 'NO'), Helper::post('tds_no'),
                Helper::post('nominee_name'), Helper::post('nominee_relation_id') ?: null,
                Helper::post('ins_number'), Helper::post('ins_renewal_date') ?: null, (float)Helper::post('premium_amount', 0),
                Helper::post('will_have_header', 'NO'), Helper::post('will_have_footer', 'NO'), Helper::post('bank_branch'),
                $doc_app, $doc_aadhaar, $doc_passbook, $doc_photo_file, $doc_settle, $doc_ex1, $doc_ex2, $doc_ex3
            ];
            
            $this->pdo->prepare($sql)->execute($values);
            $newId = $this->pdo->lastInsertId();
            $empCode = 'SAI-' . str_pad($newId, 4, '0', STR_PAD_LEFT);
            $this->pdo->prepare("UPDATE employees SET emp_code = ? WHERE id = ?")->execute([$empCode, $newId]);
            
            Session::flash('success', "Employee {$empCode} added successfully.");
            $this->redirect('employees/index');
        }
        $this->view('employees/add', compact('masters', 'error')
            + ['pageTitle' => 'Add Employee', 'active' => 'employees']);
    }

    public function edit(?string $id = null): void {
        $employee = $this->fetchOne("SELECT * FROM employees WHERE id = ?", [$id]);
        if (!$employee) { Session::flash('error','Employee not found.'); $this->redirect('employees/index'); }

        $masters = $this->getMasters();

        if (Helper::isPost()) {
            $photo = Helper::uploadFile('photo', 'employee_photos');
            $doc_app = Helper::uploadFile('doc_application', 'employee_docs');
            $doc_aadhaar = Helper::uploadFile('doc_aadhaar', 'employee_docs');
            $doc_passbook = Helper::uploadFile('doc_passbook', 'employee_docs');
            $doc_photo_file = Helper::uploadFile('doc_photo', 'employee_photos');
            $doc_settle = Helper::uploadFile('doc_settlement', 'employee_docs');
            $doc_ex1 = Helper::uploadFile('doc_extra_1', 'employee_docs');
            $doc_ex2 = Helper::uploadFile('doc_extra_2', 'employee_docs');
            $doc_ex3 = Helper::uploadFile('doc_extra_3', 'employee_docs');

            $photoSql = $photo ? ', photo=?' : '';
            $photoParams = $photo ? [$photo] : [];
            
            $docSql = ""; $docParams = [];
            if ($doc_app) { $docSql .= ", doc_application=?"; $docParams[] = $doc_app; }
            if ($doc_aadhaar) { $docSql .= ", doc_aadhaar=?"; $docParams[] = $doc_aadhaar; }
            if ($doc_passbook) { $docSql .= ", doc_passbook=?"; $docParams[] = $doc_passbook; }
            if ($doc_photo_file) { $docSql .= ", doc_photo=?"; $docParams[] = $doc_photo_file; }
            if ($doc_settle) { $docSql .= ", doc_settlement=?"; $docParams[] = $doc_settle; }
            if ($doc_ex1) { $docSql .= ", doc_extra_1=?"; $docParams[] = $doc_ex1; }
            if ($doc_ex2) { $docSql .= ", doc_extra_2=?"; $docParams[] = $doc_ex2; }
            if ($doc_ex3) { $docSql .= ", doc_extra_3=?"; $docParams[] = $doc_ex3; }

            $sql = "UPDATE employees SET
                  name=?, father_name=?, mother_name=?, designation=?, doj=?, dob=?,
                  gender=?, married_status=?, mobile=?, whatsapp_number=?, email_id=?, qualification_id=?,
                  blood_group_id=?, address=?, communication_state_id=?, communication_district_id=?,
                  communication_taluk_id=?, communication_town_id=?, communication_pincode=?, permanent_address=?,
                  aadhaar=?, pan=?, uan_no=?, esi_no=?, bank_name=?, bank_account=?, bank_ifsc=?,
                  basic_wage=?, epf_applicable=?, esi_applicable=?, insurance_amount=?, field_officer_id=?,
                  branch_id=?, date_of_walkin=?, emg_contact_name=?, emg_contact_relation_id=?, emg_contact_mobile=?,
                  trusted_person_name=?, trusted_person_relation_id=?, trusted_person_mobile=?, introducer_relate_id=?,
                  intro_employee_id=?, introducer_name=?, introducer_mobile=?, height_cm=?, weight_kg=?,
                  chest_inches=?, hip_inches=?, body_type_id=?, work_exp_years=?, prev_company=?,
                  prev_contact_number=?, prev_strength=?, reason_for_relieve=?, expected_salary_1=?,
                  expected_salary_2=?, work_hours=?, expected_designation_id_1=?, expected_designation_id_2=?,
                  expected_company_id_1=?, expected_company_id_2=?, exp_state_id=?, exp_district_id=?,
                  exp_taluk_id=?, exp_town_id=?, convicted=?, convicted_reason=?, id_mark_1=?, id_mark_2=?,
                  nearby_police_station=?, referred_by_id=?, remarks_extra=?, status=?,
                  salary_da=?, salary_hra=?, salary_conv=?, salary_other=?, ot_details=?, salary_med_wash=?,
                  amt_for_calc_epf=?, amt_for_calc_esi=?, epf_no=?, tds_avail=?, tds_no=?, 
                  nominee_name=?, nominee_relation_id=?, ins_number=?, ins_renewal_date=?, premium_amount=?,
                  will_have_header=?, will_have_footer=?, bank_branch=? $photoSql $docSql
                WHERE id=?";

            $params = [
                Helper::post('name'), Helper::post('father_name'), Helper::post('mother_name'),
                Helper::post('designation'), Helper::post('doj') ?: null, Helper::post('dob') ?: null,
                Helper::post('gender'), Helper::post('married_status'), Helper::post('mobile'),
                Helper::post('whatsapp_number'), Helper::post('email_id'), Helper::post('qualification_id') ?: null,
                Helper::post('blood_group_id') ?: null, Helper::post('address'), Helper::post('communication_state_id') ?: null,
                Helper::post('communication_district_id') ?: null, Helper::post('communication_taluk_id') ?: null,
                Helper::post('communication_town_id') ?: null, Helper::post('communication_pincode'), Helper::post('permanent_address'),
                Helper::post('aadhaar'), Helper::post('pan'), Helper::post('uan_no'), Helper::post('esi_no'),
                Helper::post('bank_name'), Helper::post('bank_account'), Helper::post('bank_ifsc'),
                (float)Helper::post('basic_wage', 0), Helper::post('epf_applicable', 'NO'), Helper::post('esi_applicable', 'NO'),
                (float)Helper::post('insurance_amount', 0), Helper::post('field_officer_id') ?: null,
                Helper::post('branch_id') ?: null, Helper::post('date_of_walkin') ?: null,
                Helper::post('emg_contact_name'), Helper::post('emg_contact_relation_id') ?: null, Helper::post('emg_contact_mobile'),
                Helper::post('trusted_person_name'), Helper::post('trusted_person_relation_id') ?: null, Helper::post('trusted_person_mobile'),
                Helper::post('introducer_relate_id') ?: null, Helper::post('intro_employee_id') ?: null,
                Helper::post('introducer_name'), Helper::post('introducer_mobile'),
                (float)Helper::post('height_cm', 0), (float)Helper::post('weight_kg', 0),
                (float)Helper::post('chest_inches', 0), (float)Helper::post('hip_inches', 0),
                Helper::post('body_type_id') ?: null, (int)Helper::post('work_exp_years', 0),
                Helper::post('prev_company'), Helper::post('prev_contact_number'), Helper::post('prev_strength'),
                Helper::post('reason_for_relieve'), (float)Helper::post('expected_salary_1', 0),
                (float)Helper::post('expected_salary_2', 0), (int)Helper::post('work_hours', 8),
                Helper::post('expected_designation_id_1') ?: null, Helper::post('expected_designation_id_2') ?: null,
                Helper::post('expected_company_id_1') ?: null, Helper::post('expected_company_id_2') ?: null,
                Helper::post('exp_state_id') ?: null, Helper::post('exp_district_id') ?: null,
                Helper::post('exp_taluk_id') ?: null, Helper::post('exp_town_id') ?: null,
                Helper::post('convicted', 'No'), Helper::post('convicted_reason'),
                Helper::post('id_mark_1'), Helper::post('id_mark_2'), Helper::post('nearby_police_station'),
                Helper::post('referred_by_id') ?: null, Helper::post('remarks_extra'),
                Helper::post('status', 'active'),
                (float)Helper::post('salary_da', 0), (float)Helper::post('salary_hra', 0), (float)Helper::post('salary_conv', 0),
                (float)Helper::post('salary_other', 0), Helper::post('ot_details'), (float)Helper::post('salary_med_wash', 0),
                (float)Helper::post('amt_for_calc_epf', 0), (float)Helper::post('amt_for_calc_esi', 0),
                Helper::post('epf_no'), Helper::post('tds_avail', 'NO'), Helper::post('tds_no'),
                Helper::post('nominee_name'), Helper::post('nominee_relation_id') ?: null,
                Helper::post('ins_number'), Helper::post('ins_renewal_date') ?: null, (float)Helper::post('premium_amount', 0),
                Helper::post('will_have_header', 'NO'), Helper::post('will_have_footer', 'NO'), Helper::post('bank_branch')
            ];
            $params = array_merge($params, $photoParams, $docParams, [$id]);
            $this->pdo->prepare($sql)->execute($params);

            Session::flash('success', 'Employee updated successfully.');
            $this->redirect('employees/profile/' . $id);
        }
        $this->view('employees/edit', compact('employee', 'masters')
            + ['pageTitle' => 'Edit Employee', 'active' => 'employees']);
    }

    /** Handle pre-employee to active conversion */
    public function migrate(?string $id = null): void {
        $employee = $this->fetchOne("SELECT * FROM employees WHERE id = ?", [$id]);
        if (!$employee) { Session::flash('error','Employee not found.'); $this->redirect('employees/index'); }
        
        $masters = $this->getMasters();
        
        if (Helper::isPost()) {
            $_POST['status'] = 'active';
            $this->edit($id);
            return;
        }
        
        $this->view('employees/migrate', compact('employee', 'masters')
            + ['pageTitle' => 'Migrate Candidate to Employee', 'active' => 'employees']);
    }

    /** Employee Profile with tabs */
    public function profile(?string $id = null): void {
        $employee = $this->fetchOne("
            SELECT e.*, u.name AS officer,
                   b.name AS branch_name, q.name AS qualification_name, bg.name AS blood_group_name,
                   s.name AS comm_state, d.name AS comm_district, t.name AS comm_taluk, tw.name AS comm_town,
                   er.name AS emg_rel, tr.name AS trust_rel, ir.name AS intro_rel, bt.name AS body_type_name,
                   ed1.name AS exp_desig_1, ed2.name AS exp_desig_2,
                   es.name AS exp_state, ed.name AS exp_district, et.name AS exp_taluk, etw.name AS exp_town
            FROM employees e
            LEFT JOIN users u ON u.id = e.field_officer_id
            LEFT JOIN branches b ON b.id = e.branch_id
            LEFT JOIN master_qualifications q ON q.id = e.qualification_id
            LEFT JOIN master_blood_groups bg ON bg.id = e.blood_group_id
            LEFT JOIN master_states s ON s.name = e.communication_state_id
            LEFT JOIN master_districts d ON d.name = e.communication_district_id
            LEFT JOIN master_taluks t ON t.name = e.communication_taluk_id
            LEFT JOIN master_towns tw ON tw.name = e.communication_town_id
            LEFT JOIN master_relationships er ON er.id = e.emg_contact_relation_id
            LEFT JOIN master_relationships tr ON tr.id = e.trusted_person_relation_id
            LEFT JOIN master_relationships ir ON ir.id = e.introducer_relate_id
            LEFT JOIN master_body_types bt ON bt.id = e.body_type_id
            LEFT JOIN designations ed1 ON ed1.id = e.expected_designation_id_1
            LEFT JOIN designations ed2 ON ed2.id = e.expected_designation_id_2
            LEFT JOIN master_states es ON es.id = e.exp_state_id
            LEFT JOIN master_districts ed ON ed.id = e.exp_district_id
            LEFT JOIN master_taluks et ON et.id = e.exp_taluk_id
            LEFT JOIN master_towns etw ON etw.id = e.exp_town_id
            WHERE e.id = ?
        ", [$id]);
        if (!$employee) { Session::flash('error','Employee not found.'); $this->redirect('employees/index'); }

        // Positions
        $positions = $this->fetchAll("
            SELECT p.*, c.company_name, t.designation, t.shift, t.rate
            FROM positions p JOIN clients c ON c.id=p.client_id JOIN trades t ON t.id=p.trade_id
            WHERE p.employee_id=? ORDER BY p.appointed_date DESC
        ", [$id]);

        // Call Logs
        $callLogs = $this->fetchAll("SELECT * FROM employee_call_logs WHERE employee_id=? ORDER BY call_date DESC LIMIT 50", [$id]);

        // Salary summary
        $salaryInfo = $this->fetchAll("SELECT * FROM salaries WHERE employee_id=? ORDER BY salary_month DESC LIMIT 6", [$id]);

        // Advances
        $advances = $this->fetchAll("SELECT * FROM advances WHERE employee_id=? ORDER BY advance_date DESC", [$id]);

        $this->view('employees/view', compact('employee','positions','callLogs','salaryInfo','advances')
            + ['pageTitle' => 'Employee Profile', 'active' => 'employees']);
    }

    /** Add call log */
    public function addlog(?string $empId = null): void {
        if (Helper::isPost()) {
            $this->execute("INSERT INTO employee_call_logs (employee_id,call_date,call_type,phone,subject,notes,follow_up_date,created_by) VALUES (?,?,?,?,?,?,?,?)", [
                $empId, Helper::post('call_date'), Helper::post('call_type','outgoing'),
                Helper::post('phone'), Helper::post('subject'), Helper::post('notes'),
                Helper::post('follow_up_date') ?: null, Session::userId(),
            ]);
            Session::flash('success', 'Call log added.');
        }
        $this->redirect('employees/profile/' . $empId);
    }
}
