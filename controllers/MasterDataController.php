<?php
require_once BASE_PATH . '/controllers/BaseController.php';

/**
 * Generic Master Data Controller
 * Handles CRUD for all 28+ configurable master tables
 * via a single reusable pattern.
 */
class MasterDataController extends BaseController {

    /**
     * Master table registry — maps URL slug → [table, display name, columns, has_extra_cols]
     */
    private function registry(): array {
        return [
            'designations'    => ['designations',               'Designations',             'name,daily_rate',  'bi-person-badge',    '#6c5ce7'],
            'vehicles'        => ['vehicles',                   'Vehicles',                 'vehicle_no,model,last_km','bi-car-front','#00b894'],
            'branches'        => ['branches',                   'Branches',                 'name',  'bi-geo-alt',         '#e17055'],
            'schedules'       => ['invoice_schedule_types',     'Invoice Schedule Types',   'name',  'bi-calendar3',       '#0984e3'],
            'shifts'          => ['shifts',                     'Work Shifts',              'name',  'bi-clock',           '#fdcb6e'],
            'blood_groups'    => ['master_blood_groups',        'Blood Groups',             'name',  'bi-droplet',         '#d63031'],
            'body_types'      => ['master_body_types',          'Body Types',               'name',  'bi-person',          '#636e72'],
            'expense_types'   => ['master_expense_types',       'Misc Expense Types',       'name',  'bi-receipt-cutoff',  '#e84393'],
            'relationships'   => ['master_relationships',       'Relationships',            'name',  'bi-people',          '#00cec9'],
            'references'      => ['master_references',          'Reference Sources',        'name',  'bi-link-45deg',      '#74b9ff'],
            'statuses'        => ['master_statuses',            'Status Lists',             'name,category','bi-toggle-on','#a29bfe'],
            'contact_roles'   => ['master_contact_roles',       'Contact Person Roles',     'name',  'bi-person-vcard',    '#fab1a0'],
            'gst_types'       => ['master_gst_types',           'GST Types',                'name',  'bi-percent',         '#ffeaa7'],
            'qualifications'  => ['master_qualifications',      'Qualifications',           'name',  'bi-mortarboard',     '#55efc4'],
            'salary_calc'     => ['master_salary_calc_types',   'Salary Calculation Types',  'name',  'bi-calculator',     '#dfe6e9'],
            'bill_types'      => ['master_bill_types',          'Bill Types',               'name',  'bi-file-earmark',    '#fd79a8'],
            'advance_types'   => ['master_advance_types',       'Advance Types',            'name',  'bi-cash',            '#00b894'],
            'towns'           => ['master_towns',               'Towns',                    'name',  'bi-house',           '#81ecec'],
            'taluks'          => ['master_taluks',              'Taluks',                   'name',  'bi-signpost',        '#fab1a0'],
            'districts'       => ['master_districts',           'Districts',                'name',  'bi-map',             '#74b9ff'],
            'states'          => ['master_states',              'States',                   'name',  'bi-globe',           '#a29bfe'],
            'languages'       => ['master_languages',           'Languages',                'name',  'bi-translate',       '#ffeaa7'],
            'att_statuses'    => ['master_attendance_statuses', 'Attendance Statuses',      'code,name','bi-check-circle', '#55efc4'],
            'salary_modes'    => ['master_salary_modes',        'Salary Modes',             'name',  'bi-credit-card',     '#e17055'],
            'weekly_off'      => ['master_weekly_off_types',    'Weekly Off Types',         'name',  'bi-calendar-x',      '#636e72'],
            'due_categories'  => ['master_due_categories',      'Due Categories',           'name',  'bi-collection',      '#d63031'],
            'exec_shifts'     => ['master_execution_shifts',    'Execution Shifts',         'name',  'bi-alarm',           '#0984e3'],
            'invoice_dates'   => ['master_invoice_dates',       'Invoice Date Settings',    'name',  'bi-calendar-date',   '#fdcb6e'],
            'ledger_accounts' => ['ledger_accounts',            'Accounts Ledger List',     'account_name,account_type,current_balance','bi-bank','#6c5ce7'],
        ];
    }

    /* ── Overview page ───────────────────────────────────────────── */
    public function index(?string $param = null): void {
        $reg    = $this->registry();
        $counts = [];
        foreach ($reg as $slug => $cfg) {
            $counts[$slug] = [
                'count' => (int) $this->pdo->query("SELECT COUNT(*) FROM `{$cfg[0]}`")->fetchColumn(),
                'label' => $cfg[1],
                'icon'  => $cfg[3],
                'color' => $cfg[4],
            ];
        }
        $this->view('masterdata/index', compact('counts')
            + ['pageTitle' => 'Master Data Management', 'active' => 'masterdata']);
    }

    /* ── Generic CRUD for any master table ────────────────────────── */
    public function manage(?string $slug = null): void {
        $reg = $this->registry();
        if (!$slug || !isset($reg[$slug])) {
            Session::flash('error', 'Unknown master data type.');
            $this->redirect('masterdata/index');
            return;
        }

        [$table, $label, $colStr, $icon, $color] = $reg[$slug];
        $columns = explode(',', $colStr);

        // ADD
        if (Helper::isPost() && Helper::post('action') === 'add') {
            $vals = []; $placeholders = []; $cols = [];
            foreach ($columns as $col) {
                $cols[]         = "`$col`";
                $placeholders[] = '?';
                $val            = trim(Helper::post($col) ?? '');
                // Avoid strtoupper for numeric fields or balances
                $vals[]         = (is_numeric($val) && (strpos($col, 'rate') !== false || strpos($col, 'balance') !== false)) ? $val : strtoupper($val);
            }
            $colList = implode(',', $cols);
            $phList  = implode(',', $placeholders);
            try {
                $this->execute("INSERT INTO `$table` ($colList) VALUES ($phList)", $vals);
                Session::flash('success', 'Record added to ' . $label);
            } catch (\Exception $e) {
                Session::flash('error', 'Duplicate or invalid entry.');
            }
            $this->redirect('masterdata/manage/' . $slug);
        }

        // DELETE
        if (Helper::isPost() && Helper::post('action') === 'delete') {
            try {
                $this->execute("DELETE FROM `$table` WHERE id=?", [Helper::post('id')]);
                Session::flash('success', 'Record deleted from ' . $label);
            } catch (\Exception $e) {
                Session::flash('error', 'Cannot delete — record may be in use.');
            }
            $this->redirect('masterdata/manage/' . $slug);
        }

        // EDIT
        if (Helper::isPost() && Helper::post('action') === 'edit') {
            $sets = []; $vals = [];
            foreach ($columns as $col) {
                $sets[] = "`$col`=?";
                $val    = trim(Helper::post($col) ?? '');
                $vals[] = (is_numeric($val) && (strpos($col, 'rate') !== false || strpos($col, 'balance') !== false)) ? $val : strtoupper($val);
            }
            $vals[] = Helper::post('id');
            try {
                $this->execute("UPDATE `$table` SET " . implode(',', $sets) . " WHERE id=?", $vals);
                Session::flash('success', 'Record updated.');
            } catch (\Exception $e) {
                Session::flash('error', 'Duplicate or invalid entry.');
            }
            $this->redirect('masterdata/manage/' . $slug);
        }

        $list = $this->fetchAll("SELECT * FROM `$table` ORDER BY 1");
        $this->view('masterdata/manage', compact('list', 'slug', 'label', 'columns', 'icon', 'color')
            + ['pageTitle' => $label, 'active' => 'masterdata']);
    }
}
