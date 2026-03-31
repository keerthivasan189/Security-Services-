<?php
$colors = ['#6c5ce7','#00b894','#e17055','#0984e3','#fdcb6e','#d63031'];
?>
<div class="row g-3 mb-4">
  <div class="col-md-3 col-6">
    <a href="<?= u('employees/index&status=active') ?>" class="text-decoration-none">
        <div class="stat-card">
          <div class="label"><i class="bi bi-people me-1"></i>Active Employees</div>
          <div class="value text-primary"><?= number_format($activeEmployees) ?></div>
          <small class="text-muted">out of <?= number_format($totalEmployees) ?></small>
        </div>
    </a>
  </div>
  <div class="col-md-3 col-6">
    <a href="<?= u('employees/index&status=pre_employee') ?>" class="text-decoration-none">
        <div class="stat-card">
          <div class="label"><i class="bi bi-person-plus me-1"></i>Pre-Employees</div>
          <div class="value text-info"><?= number_format($preEmployees) ?></div>
          <small class="text-muted">candidates in pipeline</small>
        </div>
    </a>
  </div>
  <div class="col-md-3 col-6">
    <a href="<?= u('employees/index&status=inactive') ?>" class="text-decoration-none">
        <div class="stat-card">
          <div class="label"><i class="bi bi-person-x me-1"></i>Relieved Staff</div>
          <div class="value text-secondary"><?= number_format($inactiveEmployees) ?></div>
          <small class="text-muted">inactive / relieved</small>
        </div>
    </a>
  </div>
  <div class="col-md-3 col-6">
    <div class="stat-card">
      <div class="label"><i class="bi bi-building me-1"></i>Active Clients</div>
      <div class="value text-success"><?= number_format($activeClientsCount) ?></div>
      <small class="text-muted">out of <?= number_format($totalClients) ?></small>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="stat-card">
      <div class="label"><i class="bi bi-receipt me-1"></i>Invoice Value (<?= date('M Y', strtotime($lastMonth.'-01')) ?>)</div>
      <div class="value text-warning"><?= Helper::money($invoiceValue) ?></div>
      <small class="text-muted">from <?= $invoiceCount ?> bills</small>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="stat-card">
      <div class="label"><i class="bi bi-cash me-1"></i>Received Amount</div>
      <div class="value text-info"><?= Helper::money($receivedAmt) ?></div>
      <small class="text-muted">last month</small>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="stat-card">
      <div class="label"><i class="bi bi-exclamation-triangle me-1"></i>Balance Outstanding</div>
      <div class="value text-danger"><?= Helper::money($balanceAmt) ?></div>
      <small class="text-muted">all unpaid invoices</small>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-file-earmark-text me-2"></i>Recent Invoices</span>
        <a href="<?= u('receipts/clientbills') ?>" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr><th>Invoice No</th><th>Company</th><th>Month</th><th>Amount</th><th>Status</th></tr>
          </thead>
          <tbody>
          <?php foreach ($recentInvoices as $inv): ?>
          <tr>
            <td><a href="<?= u('receipts/viewinvoice/' . ($inv['id'] ?? '')) ?>" class="fw-semibold"><?= htmlspecialchars($inv['invoice_no']) ?></a></td>
            <td><?= htmlspecialchars($inv['company_name']) ?></td>
            <td><?= htmlspecialchars($inv['invoice_month']) ?></td>
            <td><?= Helper::money($inv['grand_total']) ?></td>
            <td>
              <?php $st = $inv['payment_status']; ?>
              <span class="badge badge-<?= $st ?>"><?= ucfirst($st) ?></span>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($recentInvoices)): ?>
          <tr><td colspan="5" class="text-center text-muted py-3">No invoices found</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header"><i class="bi bi-calendar3 me-2"></i>Invoice Schedule</div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
        <?php foreach ($scheduleGroups as $sg): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
          <span style="font-size:12px"><?= htmlspecialchars($sg['invoice_schedule']) ?></span>
          <span class="badge bg-primary rounded-pill"><?= $sg['cnt'] ?> clients</span>
        </li>
        <?php endforeach; ?>
        <?php if (empty($scheduleGroups)): ?>
        <li class="list-group-item text-muted small">No schedule data</li>
        <?php endif; ?>
        </ul>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header"><i class="bi bi-lightning me-2"></i>Quick Actions</div>
      <div class="card-body d-grid gap-2">
        <a href="<?= u('receipts/addinvoice') ?>" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-plus-circle me-1"></i>Create Invoice
        </a>
        <a href="<?= u('attendance/bulk') ?>" class="btn btn-outline-success btn-sm">
          <i class="bi bi-calendar-check me-1"></i>Mark Attendance
        </a>
        <a href="<?= u('payments/generate') ?>" class="btn btn-outline-warning btn-sm">
          <i class="bi bi-cash me-1"></i>Generate Salary
        </a>
        <a href="<?= u('employees/add') ?>" class="btn btn-outline-info btn-sm">
          <i class="bi bi-person-plus me-1"></i>Add Employee
        </a>
      </div>
    </div>
  </div>
</div>
