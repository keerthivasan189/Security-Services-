<?php
require_once BASE_PATH . '/controllers/BaseController.php';

class CrmController extends BaseController {

    // ─────────────────────────────────────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────────────────────────────────────
    public function index(?string $param = null): void {
        $this->dashboard();
    }

    public function dashboard(?string $param = null): void {
        // Stage counts
        $stages = $this->fetchAll("
            SELECT status, COUNT(*) AS cnt, COALESCE(SUM(expected_value),0) AS total_val
            FROM crm_leads
            GROUP BY status
        ");
        $stageCounts = [];
        foreach ($stages as $s) {
            $stageCounts[$s['status']] = ['cnt' => $s['cnt'], 'val' => $s['total_val']];
        }

        // Totals
        $totalLeads   = (int)$this->pdo->query("SELECT COUNT(*) FROM crm_leads")->fetchColumn();
        $totalWon     = (int)$this->pdo->query("SELECT COUNT(*) FROM crm_leads WHERE status='won'")->fetchColumn();
        $totalLost    = (int)$this->pdo->query("SELECT COUNT(*) FROM crm_leads WHERE status='lost'")->fetchColumn();
        $totalOpen    = (int)$this->pdo->query("SELECT COUNT(*) FROM crm_leads WHERE status NOT IN ('won','lost')")->fetchColumn();
        $pipelineVal  = (float)$this->pdo->query("SELECT COALESCE(SUM(expected_value),0) FROM crm_leads WHERE status NOT IN ('won','lost')")->fetchColumn();
        $wonVal       = (float)$this->pdo->query("SELECT COALESCE(SUM(expected_value),0) FROM crm_leads WHERE status='won'")->fetchColumn();

        // Recent activities
        $recentActivities = $this->fetchAll("
            SELECT a.*, l.company_name, l.id AS lead_id
            FROM crm_activities a
            JOIN crm_leads l ON l.id = a.lead_id
            ORDER BY a.created_at DESC LIMIT 8
        ");

        // Today's follow-ups
        $todayFollowups = $this->fetchAll("
            SELECT * FROM crm_leads
            WHERE follow_up_date = CURDATE() AND status NOT IN ('won','lost')
            ORDER BY priority DESC
        ");

        // Overdue follow-ups
        $overdueFollowups = $this->fetchAll("
            SELECT * FROM crm_leads
            WHERE follow_up_date < CURDATE() AND status NOT IN ('won','lost')
            ORDER BY follow_up_date ASC LIMIT 10
        ");

        // Hot leads
        $hotLeads = $this->fetchAll("
            SELECT * FROM crm_leads
            WHERE priority='high' AND status NOT IN ('won','lost')
            ORDER BY updated_at DESC LIMIT 6
        ");

        // Monthly trend (last 6 months)
        $monthlyTrend = $this->fetchAll("
            SELECT DATE_FORMAT(created_at,'%Y-%m') AS m, COUNT(*) AS cnt
            FROM crm_leads
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY m ORDER BY m
        ");

        $this->view('crm/dashboard', compact(
            'stageCounts','totalLeads','totalWon','totalLost','totalOpen',
            'pipelineVal','wonVal','recentActivities','todayFollowups',
            'overdueFollowups','hotLeads','monthlyTrend'
        ) + ['pageTitle' => 'CRM Dashboard', 'active' => 'crm']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LEADS LIST
    // ─────────────────────────────────────────────────────────────────────────
    public function leads(?string $param = null): void {
        $status   = Helper::get('status');
        $priority = Helper::get('priority');
        $search   = Helper::get('search');
        $from     = Helper::get('from');
        $to       = Helper::get('to');

        $where = 'WHERE 1=1'; $params = [];
        if ($status)   { $where .= ' AND status=?';   $params[] = $status; }
        if ($priority) { $where .= ' AND priority=?'; $params[] = $priority; }
        if ($search)   {
            $where .= ' AND (company_name LIKE ? OR contact_person LIKE ? OR mobile LIKE ? OR lead_code LIKE ?)';
            $params = array_merge($params, ["%$search%","%$search%","%$search%","%$search%"]);
        }
        if ($from) { $where .= ' AND created_at >= ?'; $params[] = $from . ' 00:00:00'; }
        if ($to)   { $where .= ' AND created_at <= ?'; $params[] = $to   . ' 23:59:59'; }

        $leads = $this->fetchAll("
            SELECT l.*,
                (SELECT COUNT(*) FROM crm_activities WHERE lead_id=l.id) AS activity_count,
                (SELECT MAX(activity_date) FROM crm_activities WHERE lead_id=l.id) AS last_activity
            FROM crm_leads l $where ORDER BY l.updated_at DESC
        ", $params);

        // counts per status
        $statusCounts = [];
        $rows = $this->fetchAll("SELECT status, COUNT(*) AS cnt FROM crm_leads GROUP BY status");
        foreach ($rows as $r) $statusCounts[$r['status']] = $r['cnt'];

        $this->view('crm/leads', compact('leads','status','priority','search','from','to','statusCounts')
            + ['pageTitle' => 'CRM Leads', 'active' => 'crm']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ADD LEAD
    // ─────────────────────────────────────────────────────────────────────────
    public function addLead(?string $param = null): void {
        if (Helper::isPost()) {
            $code = 'LEAD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $this->execute("
                INSERT INTO crm_leads (lead_code,company_name,contact_person,mobile,phone,email,
                    address,district,state,pincode,industry,source,reference_name,service_needed,
                    expected_strength,expected_value,assigned_to,status,priority,remarks,follow_up_date)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ", [
                $code,
                Helper::post('company_name'), Helper::post('contact_person'), Helper::post('mobile'),
                Helper::post('phone'), Helper::post('email'), Helper::post('address'),
                Helper::post('district'), Helper::post('state','TAMIL NADU'), Helper::post('pincode'),
                Helper::post('industry'), Helper::post('source','Cold Call'), Helper::post('reference_name'),
                Helper::post('service_needed'), (int)Helper::post('expected_strength'),
                (float)Helper::post('expected_value'), Helper::post('assigned_to'),
                Helper::post('status','new'), Helper::post('priority','medium'),
                Helper::post('remarks'), Helper::post('follow_up_date') ?: null
            ]);
            $id = $this->pdo->lastInsertId();
            Session::flash('success', "Lead $code added successfully.");
            $this->redirect("crm/viewLead/$id");
        }
        $crmMasters = $this->getCrmMasters();
        $this->view('crm/add_lead', $crmMasters + ['pageTitle' => 'Add Lead', 'active' => 'crm']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VIEW LEAD
    // ─────────────────────────────────────────────────────────────────────────
    public function viewLead(?string $id = null): void {
        $lead = $this->fetchOne("SELECT * FROM crm_leads WHERE id=?", [$id]);
        if (!$lead) { Session::flash('error', 'Lead not found.'); $this->redirect('crm/leads'); }

        $activities = $this->fetchAll("
            SELECT * FROM crm_activities WHERE lead_id=? ORDER BY activity_date DESC, activity_time DESC
        ", [$id]);

        $proposals   = $this->fetchAll("SELECT * FROM crm_proposals WHERE lead_id=? ORDER BY created_at DESC", [$id]);
        $reminders   = $this->fetchAll("SELECT * FROM crm_reminders WHERE lead_id=? ORDER BY reminder_date ASC", [$id]);
        $this->ensureAttachmentsTable();
        $attachments = $this->fetchAll("SELECT * FROM crm_attachments WHERE lead_id=? ORDER BY uploaded_at DESC", [$id]);

        $this->view('crm/view_lead', compact('lead','activities','proposals','reminders','attachments')
            + ['pageTitle' => 'Lead: ' . $lead['company_name'], 'active' => 'crm']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EDIT LEAD
    // ─────────────────────────────────────────────────────────────────────────
    public function editLead(?string $id = null): void {
        $lead = $this->fetchOne("SELECT * FROM crm_leads WHERE id=?", [$id]);
        if (!$lead) { Session::flash('error', 'Lead not found.'); $this->redirect('crm/leads'); }

        if (Helper::isPost()) {
            $this->execute("
                UPDATE crm_leads SET
                    company_name=?,contact_person=?,mobile=?,phone=?,email=?,
                    address=?,district=?,state=?,pincode=?,industry=?,source=?,reference_name=?,
                    service_needed=?,expected_strength=?,expected_value=?,assigned_to=?,
                    status=?,priority=?,lost_reason=?,remarks=?,follow_up_date=?
                WHERE id=?
            ", [
                Helper::post('company_name'), Helper::post('contact_person'), Helper::post('mobile'),
                Helper::post('phone'), Helper::post('email'), Helper::post('address'),
                Helper::post('district'), Helper::post('state','TAMIL NADU'), Helper::post('pincode'),
                Helper::post('industry'), Helper::post('source'), Helper::post('reference_name'),
                Helper::post('service_needed'), (int)Helper::post('expected_strength'),
                (float)Helper::post('expected_value'), Helper::post('assigned_to'),
                Helper::post('status'), Helper::post('priority'),
                Helper::post('lost_reason'), Helper::post('remarks'),
                Helper::post('follow_up_date') ?: null,
                $id
            ]);
            Session::flash('success', 'Lead updated successfully.');
            $this->redirect("crm/viewLead/$id");
        }
        $crmMasters = $this->getCrmMasters();
        $this->view('crm/edit_lead', compact('lead') + $crmMasters + ['pageTitle' => 'Edit Lead', 'active' => 'crm']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ADD ACTIVITY (AJAX / POST)
    // ─────────────────────────────────────────────────────────────────────────
    public function addActivity(?string $leadId = null): void {
        if (!Helper::isPost()) { $this->redirect('crm/leads'); }

        $this->execute("
            INSERT INTO crm_activities (lead_id,activity_type,subject,description,outcome,
                next_action,next_action_date,done_by,activity_date,activity_time,duration_min)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)
        ", [
            $leadId,
            Helper::post('activity_type','note'),
            Helper::post('subject'),
            Helper::post('description'),
            Helper::post('outcome'),
            Helper::post('next_action'),
            Helper::post('next_action_date') ?: null,
            Helper::post('done_by'),
            Helper::post('activity_date', date('Y-m-d')),
            Helper::post('activity_time') ?: null,
            (int)Helper::post('duration_min')
        ]);

        // Update follow_up_date on lead if next_action_date provided
        if (Helper::post('next_action_date')) {
            $this->execute("UPDATE crm_leads SET follow_up_date=? WHERE id=?",
                [Helper::post('next_action_date'), $leadId]);
        }

        Session::flash('success', 'Activity logged successfully.');
        $this->redirect("crm/viewLead/$leadId");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ADD PROPOSAL
    // ─────────────────────────────────────────────────────────────────────────
    public function addProposal(?string $leadId = null): void {
        if (!Helper::isPost()) { $this->redirect('crm/leads'); }

        $no = 'PRO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $monthly = (float)Helper::post('monthly_value');
        $this->execute("
            INSERT INTO crm_proposals (proposal_no,lead_id,title,service_details,manpower,
                monthly_value,annual_value,validity_days,sent_date,status,remarks)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)
        ", [
            $no, $leadId,
            Helper::post('title'), Helper::post('service_details'),
            (int)Helper::post('manpower'), $monthly,
            $monthly * 12,
            (int)Helper::post('validity_days', 30),
            Helper::post('sent_date') ?: null,
            Helper::post('status','draft'),
            Helper::post('remarks')
        ]);

        // Update lead expected value
        $this->execute("UPDATE crm_leads SET expected_value=?, status='proposal_sent' WHERE id=? AND status NOT IN ('won','lost')",
            [$monthly, $leadId]);

        Session::flash('success', 'Proposal created.');
        $this->redirect("crm/viewLead/$leadId");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CONVERT TO CLIENT
    // ─────────────────────────────────────────────────────────────────────────
    public function convertToClient(?string $id = null): void {
        $lead = $this->fetchOne("SELECT * FROM crm_leads WHERE id=?", [$id]);
        if (!$lead) { Session::flash('error', 'Lead not found.'); $this->redirect('crm/leads'); }

        if (Helper::isPost()) {
            $code = 'CLT-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $this->execute("
                INSERT INTO clients (client_code,company_name,contact_person,mobile,email,address,
                    state,district,pincode,status,reference)
                VALUES (?,?,?,?,?,?,?,?,?,?,?)
            ", [
                $code, $lead['company_name'], $lead['contact_person'], $lead['mobile'],
                $lead['email'], $lead['address'], $lead['state'], $lead['district'],
                $lead['pincode'], 'pre_client', 'CRM Lead: ' . $lead['lead_code']
            ]);
            $clientId = $this->pdo->lastInsertId();

            $this->execute("UPDATE crm_leads SET status='won', converted_client_id=? WHERE id=?",
                [$clientId, $id]);

            Session::flash('success', 'Lead converted to client successfully!');
            $this->redirect("clients/edit/$clientId");
        }
        $this->view('crm/convert', compact('lead') + ['pageTitle' => 'Convert to Client', 'active' => 'crm']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // KANBAN VIEW
    // ─────────────────────────────────────────────────────────────────────────
    public function kanban(?string $param = null): void {
        $columns = ['new','contacted','qualified','proposal_sent','negotiation','won','lost'];
        $company_filter = Helper::get('company', '');
        
        // Get all unique companies for filter dropdown
        $allCompanies = $this->fetchAll("
            SELECT DISTINCT company_name
            FROM crm_leads
            ORDER BY company_name ASC
        ");
        
        // Build query with optional company filter
        $where = '';
        $params = [];
        if ($company_filter) {
            $where = ' WHERE company_name = ?';
            $params = [$company_filter];
        }
        
        $leads   = $this->fetchAll("
            SELECT l.*,
                (SELECT COUNT(*) FROM crm_activities WHERE lead_id=l.id) AS activity_count
            FROM crm_leads l
            $where
            ORDER BY priority DESC, updated_at DESC
        ", $params);

        $board = [];
        foreach ($columns as $col) $board[$col] = [];
        foreach ($leads as $lead) {
            if (isset($board[$lead['status']])) $board[$lead['status']][] = $lead;
        }

        $this->view('crm/kanban', compact('board','columns','allCompanies','company_filter')
            + ['pageTitle' => 'SAI SAKTHEESWARI', 'active' => 'crm']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // QUICK STATUS UPDATE (AJAX)
    // ─────────────────────────────────────────────────────────────────────────
    public function updateStatus(?string $id = null): void {
        if (!Helper::isPost()) { $this->json(['ok' => false]); return; }
        $status = Helper::post('status');
        $allowed = ['new','contacted','qualified','proposal_sent','negotiation','won','lost','on_hold'];
        if (!in_array($status, $allowed)) { $this->json(['ok' => false]); return; }
        $this->execute("UPDATE crm_leads SET status=? WHERE id=?", [$status, $id]);
        $this->json(['ok' => true]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REPORTS
    // ─────────────────────────────────────────────────────────────────────────
    public function reports(?string $param = null): void {
        $from = Helper::get('from', date('Y-m-01'));
        $to   = Helper::get('to',   date('Y-m-d'));

        $bySource = $this->fetchAll("
            SELECT source, COUNT(*) AS cnt, COALESCE(SUM(expected_value),0) AS val
            FROM crm_leads WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY source ORDER BY cnt DESC
        ", [$from, $to]);

        $byStatus = $this->fetchAll("
            SELECT status, COUNT(*) AS cnt, COALESCE(SUM(expected_value),0) AS val
            FROM crm_leads WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY status
        ", [$from, $to]);

        $byAssigned = $this->fetchAll("
            SELECT assigned_to, COUNT(*) AS cnt, SUM(status='won') AS won_cnt,
                COALESCE(SUM(expected_value),0) AS val
            FROM crm_leads WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY assigned_to ORDER BY cnt DESC
        ", [$from, $to]);

        $conversionRate = 0;
        $totalInPeriod = array_sum(array_column($byStatus, 'cnt'));
        foreach ($byStatus as $row) {
            if ($row['status'] === 'won' && $totalInPeriod > 0) {
                $conversionRate = round($row['cnt'] / $totalInPeriod * 100, 1);
            }
        }

        $recentWins = $this->fetchAll("
            SELECT * FROM crm_leads WHERE status='won'
            ORDER BY updated_at DESC LIMIT 10
        ");

        $this->view('crm/reports', compact('bySource','byStatus','byAssigned','conversionRate','from','to','recentWins')
            + ['pageTitle' => 'CRM Reports', 'active' => 'crm']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MASTER DATA FOR CRM FORMS
    // ─────────────────────────────────────────────────────────────────────────
    private function getCrmMasters(): array {
        $q = fn(string $sql) => $this->pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        try {
            return [
                'crmSources'   => $q("SELECT name FROM master_references ORDER BY name"),
                'crmDistricts' => $q("SELECT name FROM master_districts ORDER BY name"),
                'crmStates'    => $q("SELECT name FROM master_states ORDER BY name"),
            ];
        } catch (\Exception $e) {
            return ['crmSources' => [], 'crmDistricts' => [], 'crmStates' => []];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ATTACHMENTS
    // ─────────────────────────────────────────────────────────────────────────
    private static bool $attachmentsTableEnsured = false;

    private function ensureAttachmentsTable(): void {
        if (self::$attachmentsTableEnsured) return;
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS `crm_attachments` (
            `id`            INT AUTO_INCREMENT PRIMARY KEY,
            `lead_id`       INT NOT NULL,
            `original_name` VARCHAR(255) NOT NULL,
            `stored_name`   VARCHAR(255) NOT NULL,
            `file_type`     VARCHAR(20)  NOT NULL,
            `file_size`     INT          NOT NULL DEFAULT 0,
            `uploaded_at`   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
            INDEX (`lead_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        self::$attachmentsTableEnsured = true;
    }

    public function uploadAttachment(?string $leadId = null): void {
        if (!Helper::isPost() || !$leadId) { $this->redirect('crm/leads'); }
        $this->ensureAttachmentsTable();

        $lead = $this->fetchOne("SELECT id FROM crm_leads WHERE id=?", [$leadId]);
        if (!$lead) { Session::flash('error', 'Lead not found.'); $this->redirect('crm/leads'); }

        if (!isset($_FILES['attachment']) || $_FILES['attachment']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'No file selected or upload error.');
            $this->redirect("crm/viewLead/$leadId");
            return;
        }

        $allowed  = ['jpg','jpeg','png','gif','pdf','webp','doc','docx','xls','xlsx','txt','csv'];
        $origName = basename($_FILES['attachment']['name']);
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            Session::flash('error', 'File type not allowed. Allowed: ' . implode(', ', $allowed));
            $this->redirect("crm/viewLead/$leadId");
            return;
        }

        if ($_FILES['attachment']['size'] > 10 * 1024 * 1024) {
            Session::flash('error', 'File too large (max 10 MB).');
            $this->redirect("crm/viewLead/$leadId");
            return;
        }

        $dir = BASE_PATH . '/uploads/crm_attachments/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $stored = uniqid('crm_', true) . '.' . $ext;
        if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $dir . $stored)) {
            Session::flash('error', 'Failed to save file on server.');
            $this->redirect("crm/viewLead/$leadId");
            return;
        }

        $this->execute(
            "INSERT INTO crm_attachments (lead_id, original_name, stored_name, file_type, file_size) VALUES (?,?,?,?,?)",
            [$leadId, $origName, $stored, $ext, (int)$_FILES['attachment']['size']]
        );

        Session::flash('success', 'Attachment "' . htmlspecialchars($origName) . '" uploaded.');
        $this->redirect("crm/viewLead/$leadId");
    }

    public function serveAttachment(?string $id = null): void {
        $this->ensureAttachmentsTable();
        $att = $this->fetchOne("SELECT * FROM crm_attachments WHERE id=?", [$id]);
        if (!$att) { http_response_code(404); echo 'Attachment not found.'; exit; }

        $path = BASE_PATH . '/uploads/crm_attachments/' . $att['stored_name'];
        if (!file_exists($path)) { http_response_code(404); echo 'File missing on server.'; exit; }

        $mimeMap = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',  'jpeg' => 'image/jpeg',
            'png'  => 'image/png',   'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'txt'  => 'text/plain',
            'csv'  => 'text/csv',
        ];
        $mime   = $mimeMap[$att['file_type']] ?? 'application/octet-stream';
        $inline = in_array($att['file_type'], ['pdf','jpg','jpeg','png','gif','webp','txt']);
        $disp   = $inline ? 'inline' : 'attachment';

        header('Content-Type: ' . $mime);
        header('Content-Disposition: ' . $disp . '; filename="' . $att['original_name'] . '"');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: private, max-age=3600');
        readfile($path);
        exit;
    }

    public function deleteAttachment(?string $id = null): void {
        $this->ensureAttachmentsTable();
        $att = $this->fetchOne("SELECT * FROM crm_attachments WHERE id=?", [$id]);
        if (!$att) { Session::flash('error', 'Attachment not found.'); $this->redirect('crm/leads'); }

        $path = BASE_PATH . '/uploads/crm_attachments/' . $att['stored_name'];
        if (file_exists($path)) @unlink($path);

        $this->execute("DELETE FROM crm_attachments WHERE id=?", [$id]);
        Session::flash('success', 'Attachment deleted.');
        $this->redirect("crm/viewLead/{$att['lead_id']}");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE LEAD
    // ─────────────────────────────────────────────────────────────────────────
    public function deleteLead(?string $id = null): void {
        $this->execute("DELETE FROM crm_leads WHERE id=?", [$id]);
        Session::flash('success', 'Lead deleted.');
        $this->redirect('crm/leads');
    }
}
