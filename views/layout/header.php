<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'HRMS') ?> — Sai Saktheeswari</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
:root{--sidebar-w:240px;--header-h:56px}
body{background:#f4f6fa;font-size:14px;overflow-x:hidden}
.sidebar{position:fixed;top:0;left:0;width:var(--sidebar-w);height:100vh;background:#6c5ce7;overflow-y:auto;z-index:1050;transition:all 0.3s ease}
.sidebar .brand{padding:14px 16px;background:rgba(0,0,0,.15);color:#fff;font-weight:700;font-size:13px;border-bottom:1px solid rgba(255,255,255,.1)}
.sidebar .nav-link{color:rgba(255,255,255,.85);padding:8px 16px;font-size:13px;display:flex;align-items:center;gap:8px}
.sidebar .nav-link:hover,.sidebar .nav-link.active{background:rgba(255,255,255,.15);color:#fff}
.sidebar .sub-menu{background:rgba(0,0,0,.1)}
.sidebar .sub-menu .nav-link{padding:6px 16px 6px 40px;font-size:12.5px}

.topbar{position:fixed;top:0;left:var(--sidebar-w);right:0;height:var(--header-h);background:#fff;border-bottom:1px solid #e0e0e0;z-index:1030;display:flex;align-items:center;padding:0 20px;gap:12px;transition:all 0.3s ease}
.main-content{margin-left:var(--sidebar-w);padding-top:calc(var(--header-h) + 20px);padding-left:20px;padding-right:20px;padding-bottom:30px;min-height:100vh;transition:all 0.3s ease}

.sidebar-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1040;display:none}

/* Responsive Breakpoints */
@media (max-width: 991.98px) {
  .sidebar { left: calc(-1 * var(--sidebar-w)); }
  .topbar { left: 0; }
  .main-content { margin-left: 0; padding-left: 12px; padding-right: 12px; }

  body.sidebar-open { overflow: hidden; }
  body.sidebar-open .sidebar { left: 0; }
  body.sidebar-open .sidebar-overlay { display: block; }

  .topbar .ms-auto { gap: 10px !important; }
  .topbar .date-text { display: none; }

  /* Wrap card-header flex rows on tablet */
  .card-header.d-flex { flex-wrap: wrap; gap: 6px; }
}

/* ── Small phone improvements ── */
@media (max-width: 575.98px) {
  .stat-card { padding: 12px 14px; }
  .stat-card .value { font-size: 22px; }
  .stat-card .label { font-size: 10px; }

  /* Prevent iOS auto-zoom on focus */
  input.form-control, select.form-select, textarea.form-control { font-size: 16px !important; }
  input.form-control-sm, select.form-select-sm { font-size: 15px !important; }

  /* Touch-friendly buttons */
  .btn { min-height: 36px; }
  .btn-sm { min-height: 32px; padding: 5px 10px; }

  /* Table action buttons */
  .btn-xs { min-height: 28px; min-width: 28px; }

  /* Card spacing */
  .card-body { padding: 12px !important; }
  .card-header { font-size: 12px; padding: 10px 12px; }

  /* Stack d-flex bars on mobile */
  .mobile-stack { flex-direction: column; align-items: stretch !important; }
  .mobile-stack .btn { width: 100%; justify-content: center; }

  /* Table font */
  .table { font-size: 12px; }
  th, td { padding: 6px 8px !important; }

  /* Better page headings */
  h5.fw-semibold, h5.fw-bold { font-size: 14px; }

  /* Hide secondary table columns on mobile via utility */
  .d-mobile-none { display: none !important; }

  /* Progress bars */
  .progress { height: 6px !important; }
}

/* ── Touch-friendly table action buttons (all breakpoints) ── */
.btn-xs { display: inline-flex; align-items: center; justify-content: center; }

/* ── Horizontal scroll hint for overflow containers ── */
.scroll-x { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.scroll-x::-webkit-scrollbar { height: 4px; }
.scroll-x::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }

.card{border:1px solid #e4e4e4;border-radius:10px;box-shadow:none}
.card-header{background:#f8f9fa;border-bottom:1px solid #e4e4e4;font-weight:600;font-size:13px}
.stat-card{border-radius:10px;padding:18px 20px;border:1px solid #e4e4e4;background:#fff;transition:all .2s ease-in-out}
.stat-card:hover{transform:translateY(-3px);box-shadow:0 5px 15px rgba(0,0,0,.05);border-color:#6c5ce7}
.stat-card .label{font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.5px}
.stat-card .value{font-size:26px;font-weight:700;margin-top:4px}
.table{font-size:13px}
.badge-paid{background:#d4edda;color:#155724}
.badge-partial{background:#fff3cd;color:#856404}
.badge-unpaid{background:#f8d7da;color:#721c24}
a.text-decoration-none:hover .stat-card{border-color:#6c5ce7}
@media print{.sidebar,.topbar,.no-print{display:none!important}.main-content{margin:0;padding:0}}
</style>
</head>
<body>
<div class="sidebar-overlay" onclick="document.body.classList.remove('sidebar-open')"></div>
<?php
// Helper to build URL - works with or without mod_rewrite
function u(string $path): string {
    return BASE_URL . '/index.php?url=' . ltrim($path, '/');
}
?>
<nav class="sidebar">
  <div class="brand"><i class="bi bi-shield-check"></i> SAI SAKTHEESWARI<br>
  <small style="font-weight:400;font-size:11px">Security Services HRMS</small></div>
  <ul class="nav flex-column mt-2">

    <li><a class="nav-link <?= ($active??'')==='dashboard'?'active':'' ?>" href="<?= u('dashboard/index') ?>">
      <i class="bi bi-speedometer2"></i> Dashboard</a></li>

    <li>
      <a class="nav-link <?= str_starts_with($active??'','accounts')?'active':'' ?>" data-bs-toggle="collapse" href="#acc-menu">
        <i class="bi bi-bank"></i> Accounts <i class="bi bi-chevron-down ms-auto" style="font-size:10px"></i></a>
      <div class="collapse sub-menu <?= str_starts_with($active??'','accounts')?'show':'' ?>" id="acc-menu">
        <ul class="nav flex-column">
          <li><a class="nav-link" href="<?= u('accounts/dashboard') ?>"><i class="bi bi-speedometer me-1" style="font-size:10px"></i>Financial Dashboard</a></li>
          <li><a class="nav-link" href="<?= u('accounts/cashflow') ?>"><i class="bi bi-graph-up me-1" style="font-size:10px"></i>Cash Flow</a></li>
          <li><a class="nav-link" href="<?= u('accounts/statements') ?>"><i class="bi bi-journal-text me-1" style="font-size:10px"></i>Statements</a></li>
          <li><a class="nav-link" href="<?= u('accounts/add') ?>"><i class="bi bi-plus-circle me-1" style="font-size:10px"></i>Add Transaction</a></li>
          <li><a class="nav-link" href="<?= u('accounts/ledgers') ?>"><i class="bi bi-bank me-1" style="font-size:10px"></i>Ledger Accounts</a></li>
        </ul>
      </div>
    </li>

    <li>
      <a class="nav-link <?= str_starts_with($active??'','attendance')?'active':'' ?>" data-bs-toggle="collapse" href="#att-menu">
        <i class="bi bi-calendar-check"></i> Attendance <i class="bi bi-chevron-down ms-auto" style="font-size:10px"></i></a>
      <div class="collapse sub-menu <?= str_starts_with($active??'','attendance')?'show':'' ?>" id="att-menu">
        <ul class="nav flex-column">
          <li><a class="nav-link" href="<?= u('attendance/bulk') ?>">Bulk Entry</a></li>
          <li><a class="nav-link" href="<?= u('attendance/single') ?>">Single Entry</a></li>
          <li><a class="nav-link" href="<?= u('attendance/viewAttendance') ?>">View Attendance</a></li>
        </ul>
      </div>
    </li>

    <li>
      <a class="nav-link <?= str_starts_with($active??'','payments')?'active':'' ?>" data-bs-toggle="collapse" href="#pay-menu">
        <i class="bi bi-cash-stack"></i> Payments <i class="bi bi-chevron-down ms-auto" style="font-size:10px"></i></a>
      <div class="collapse sub-menu <?= str_starts_with($active??'','payments')?'show':'' ?>" id="pay-menu">
        <ul class="nav flex-column">
          <li><a class="nav-link" href="<?= u('payments/generate') ?>">Generate Salary</a></li>
          <li><a class="nav-link" href="<?= u('payments/salarylist') ?>">Staffs Salary List</a></li>
          <li><a class="nav-link" href="<?= u('payments/payslipBulk') ?>">Payslip Bulk Print</a></li>
          <li><a class="nav-link" href="<?= u('payments/salarylist') ?>">Aqutence</a></li>
          <li><a class="nav-link" href="<?= u('payments/advances') ?>">Advances</a></li>
          <li><a class="nav-link" href="<?= u('payments/fuel') ?>">Fuel Expenses</a></li>
          <li><a class="nav-link" href="<?= u('payments/misc') ?>">MISC Expenses</a></li>
          <li><a class="nav-link" href="<?= u('payments/allowances') ?>">Other Allowances / Compliment</a></li>
        </ul>
      </div>
    </li>

    <li>
      <a class="nav-link <?= str_starts_with($active??'','receipts')?'active':'' ?>" data-bs-toggle="collapse" href="#rec-menu">
        <i class="bi bi-receipt"></i> Receipts <i class="bi bi-chevron-down ms-auto" style="font-size:10px"></i></a>
      <div class="collapse sub-menu <?= str_starts_with($active??'','receipts')?'show':'' ?>" id="rec-menu">
        <ul class="nav flex-column">
          <li><a class="nav-link" href="<?= u('receipts/clientbills') ?>">Client Bills</a></li>
          <li><a class="nav-link" href="<?= u('receipts/clientdues') ?>">Client Dues</a></li>
          <li><a class="nav-link" href="<?= u('receipts/uniforms') ?>">Issue of Uniforms</a></li>
          <li><a class="nav-link" href="<?= u('receipts/employeedues') ?>">Employee Dues</a></li>
          <li><a class="nav-link" href="<?= u('receipts/deductions') ?>">Other Deductions</a></li>
        </ul>
      </div>
    </li>

    <li>
      <a class="nav-link <?= str_starts_with($active??'','positions')?'active':'' ?>" data-bs-toggle="collapse" href="#pos-menu">
        <i class="bi bi-person-badge"></i> Position List <i class="bi bi-chevron-down ms-auto" style="font-size:10px"></i></a>
      <div class="collapse sub-menu <?= str_starts_with($active??'','positions')?'show':'' ?>" id="pos-menu">
        <ul class="nav flex-column">
          <li><a class="nav-link" href="<?= u('positions/index') ?>">Current Positions</a></li>
          <li><a class="nav-link" href="<?= u('positions/appoint') ?>">Appoint / Transfer</a></li>
          <li><a class="nav-link" href="<?= u('positions/history') ?>">Transfer History</a></li>
        </ul>
      </div>
    </li>

    <li>
      <a class="nav-link <?= str_starts_with($active??'','crm')?'active':'' ?>" data-bs-toggle="collapse" href="#crm-menu">
        <i class="bi bi-diagram-3"></i> CRM <i class="bi bi-chevron-down ms-auto" style="font-size:10px"></i></a>
      <div class="collapse sub-menu <?= str_starts_with($active??'','crm')?'show':'' ?>" id="crm-menu">
        <ul class="nav flex-column">
          <li><a class="nav-link" href="<?= u('crm/dashboard') ?>"><i class="bi bi-speedometer me-1" style="font-size:10px"></i>CRM Dashboard</a></li>
          <li><a class="nav-link" href="<?= u('crm/leads') ?>"><i class="bi bi-people me-1" style="font-size:10px"></i>All Leads</a></li>
          <li><a class="nav-link" href="<?= u('crm/leads') ?>&status=new"><i class="bi bi-star me-1" style="font-size:10px"></i>New Leads</a></li>
          <li><a class="nav-link" href="<?= u('crm/leads') ?>&priority=high"><i class="bi bi-fire me-1" style="font-size:10px"></i>Hot Leads</a></li>
          <li><a class="nav-link" href="<?= u('crm/kanban') ?>"><i class="bi bi-kanban me-1" style="font-size:10px"></i>SAI SAKTHEESWARI</a></li>
          <li><a class="nav-link" href="<?= u('crm/addLead') ?>"><i class="bi bi-plus-circle me-1" style="font-size:10px"></i>Add Lead</a></li>
          <li><a class="nav-link" href="<?= u('crm/reports') ?>"><i class="bi bi-bar-chart me-1" style="font-size:10px"></i>CRM Reports</a></li>
        </ul>
      </div>
    </li>

    <li>
      <a class="nav-link <?= ($active??'')==='clients'?'active':'' ?>" data-bs-toggle="collapse" href="#client-menu">
        <i class="bi bi-building"></i> Client Master <i class="bi bi-chevron-down ms-auto" style="font-size:10px"></i></a>
      <div class="collapse sub-menu <?= ($active??'')==='clients'?'show':'' ?>" id="client-menu">
        <ul class="nav flex-column">
          <li><a class="nav-link" href="<?= u('clients/index') ?>&status=pre_client">Pre-Clients</a></li>
          <li><a class="nav-link" href="<?= u('clients/index') ?>&status=active">Active Clients</a></li>
          <li><a class="nav-link" href="<?= u('clients/index') ?>&status=inactive">Relieved Clients</a></li>
        </ul>
      </div>
    </li>
    <li>
      <a class="nav-link <?= ($active??'')==='employees'?'active':'' ?>" data-bs-toggle="collapse" href="#emp-menu">
        <i class="bi bi-people"></i> Employee Master <i class="bi bi-chevron-down ms-auto" style="font-size:10px"></i></a>
      <div class="collapse sub-menu <?= ($active??'')==='employees'?'show':'' ?>" id="emp-menu">
        <ul class="nav flex-column">
          <li><a class="nav-link" href="<?= u('employees/index') ?>&status=pre_employee"><i class="bi bi-person-plus text-warning me-1" style="font-size:10px"></i>Pre Employees</a></li>
          <li><a class="nav-link" href="<?= u('employees/index') ?>&status=active"><i class="bi bi-person-check text-success me-1" style="font-size:10px"></i>Active Employees</a></li>
          <li><a class="nav-link" href="<?= u('employees/index') ?>&status=inactive"><i class="bi bi-person-x text-secondary me-1" style="font-size:10px"></i>Relieved Employees</a></li>
          <li><a class="nav-link" href="<?= u('employees/add') ?>"><i class="bi bi-plus-circle me-1" style="font-size:10px"></i>Add Employee</a></li>
        </ul>
      </div>
    </li>

    <li>
      <a class="nav-link <?= str_starts_with($active??'','inventory')?'active':'' ?>" data-bs-toggle="collapse" href="#inv-menu">
        <i class="bi bi-box-seam"></i> Inventory & Vendors <i class="bi bi-chevron-down ms-auto" style="font-size:10px"></i></a>
      <div class="collapse sub-menu <?= str_starts_with($active??'','inventory')?'show':'' ?>" id="inv-menu">
        <ul class="nav flex-column">
          <li><a class="nav-link" href="<?= u('inventory/index') ?>">Vendors</a></li>
          <li><a class="nav-link" href="<?= u('inventory/items') ?>">Uniform Items</a></li>
        </ul>
      </div>
    </li>

    <li>
      <a class="nav-link <?= str_starts_with($active??'','masterdata')?'active':'' ?>" data-bs-toggle="collapse" href="#md-menu">
        <i class="bi bi-gear"></i> Master Data <i class="bi bi-chevron-down ms-auto" style="font-size:10px"></i></a>
      <div class="collapse sub-menu <?= str_starts_with($active??'','masterdata')?'show':'' ?>" id="md-menu">
        <ul class="nav flex-column">
          <li><a class="nav-link fw-semibold" href="<?= u('masterdata/index') ?>"><i class="bi bi-grid me-1" style="font-size:10px"></i>All Master Data</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/designations') ?>">Designations</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/vehicles') ?>">Vehicles</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/branches') ?>">Branches</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/schedules') ?>">Invoice Schedule Types</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/shifts') ?>">Work Shifts</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/blood_groups') ?>">Blood Groups</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/body_types') ?>">Body Types</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/expense_types') ?>">Misc Expense Types</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/relationships') ?>">Relationships</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/references') ?>">Reference Sources</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/statuses') ?>">Status Lists</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/contact_roles') ?>">Contact Person Roles</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/gst_types') ?>">GST Types</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/qualifications') ?>">Qualifications</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/salary_calc') ?>">Salary Calc Types</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/bill_types') ?>">Bill Types</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/advance_types') ?>">Advance Types</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/towns') ?>">Towns</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/taluks') ?>">Taluks</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/districts') ?>">Districts</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/states') ?>">States</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/languages') ?>">Languages</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/att_statuses') ?>">Attendance Statuses</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/salary_modes') ?>">Salary Modes</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/weekly_off') ?>">Weekly Off Types</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/due_categories') ?>">Due Categories</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/exec_shifts') ?>">Execution Shifts</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/invoice_dates') ?>">Invoice Date Settings</a></li>
          <li><a class="nav-link" href="<?= u('masterdata/manage/ledger_accounts') ?>">Accounts Ledger List</a></li>
        </ul>
      </div>
    </li>

    <li>
      <a class="nav-link <?= str_starts_with($active??'','reports')?'active':'' ?>" data-bs-toggle="collapse" href="#rpt-menu">
        <i class="bi bi-bar-chart"></i> Reports <i class="bi bi-chevron-down ms-auto" style="font-size:10px"></i></a>
      <div class="collapse sub-menu <?= str_starts_with($active??'','reports')?'show':'' ?>" id="rpt-menu">
        <ul class="nav flex-column">
          <li><a class="nav-link" href="<?= u('reports/index') ?>">Report Hub</a></li>
          <li><a class="nav-link" href="<?= u('reports/attendance') ?>">Attendance Report</a></li>
          <li><a class="nav-link" href="<?= u('reports/clients') ?>">Client Report</a></li>
          <li><a class="nav-link" href="<?= u('reports/employees') ?>">Employee Report</a></li>
          <li><a class="nav-link" href="<?= u('reports/salary') ?>">Salary Report</a></li>
          <li><a class="nav-link" href="<?= u('reports/invoices') ?>">Invoice Report</a></li>
          <li><a class="nav-link" href="<?= u('reports/payments') ?>">Payments Report</a></li>
          <li><a class="nav-link" href="<?= u('reports/outstanding') ?>">Outstanding Report</a></li>
          <li><a class="nav-link" href="<?= u('reports/expenses') ?>">Expense Report</a></li>
          <li><a class="nav-link" href="<?= u('reports/inventory') ?>">Inventory Report</a></li>
          <li><a class="nav-link" href="<?= u('reports/vendors') ?>">Vendor Report</a></li>
          <li><a class="nav-link" href="<?= u('reports/ledger') ?>">Ledger Statement</a></li>
        </ul>
      </div>
    </li>

    <li class="mt-3 border-top border-white border-opacity-25 pt-2">
      <a class="nav-link text-white-50" href="<?= u('auth/logout') ?>">
        <i class="bi bi-box-arrow-right"></i> Logout</a></li>
  </ul>
</nav>

<div class="topbar">
  <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="document.body.classList.toggle('sidebar-open')">
    <i class="bi bi-list"></i>
  </button>
  <span class="fw-semibold text-muted" style="font-size:13px"><?= htmlspecialchars($pageTitle ?? '') ?></span>
  <div class="ms-auto d-flex align-items-center gap-3">
    <small class="text-muted date-text"><?= date('d M Y') ?></small>
    <span class="badge bg-secondary"><?= htmlspecialchars(Session::userName()) ?></span>
  </div>
</div>

<div class="main-content">
<?php $flash = Session::getFlash('success'); if ($flash): ?>
<div class="alert alert-success alert-dismissible fade show no-print">
  <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($flash) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php $ferr = Session::getFlash('error'); if ($ferr): ?>
<div class="alert alert-danger alert-dismissible fade show no-print">
  <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($ferr) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
