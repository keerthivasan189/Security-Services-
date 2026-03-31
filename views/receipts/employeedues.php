<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-person-exclamation me-2 text-danger"></i>Employee Dues</h5>
</div>

<!-- Filter Card -->
<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <p class="small fw-semibold text-muted mb-2">FILTER EMPLOYEE DUES</p>
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="receipts/employeedues">

      <div class="col-md-4">
        <label class="form-label small fw-semibold mb-1">Employee:</label>
        <select name="employee_id" class="form-select form-select-sm">
          <option value="">——— All Employees ———</option>
          <?php foreach ($employees as $e): ?>
          <option value="<?= $e['id'] ?>" <?= ($fEmployee??'')==$e['id']?'selected':'' ?>><?= htmlspecialchars($e['emp_code'] . ' — ' . $e['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Client:</label>
        <select name="client_id" class="form-select form-select-sm">
          <option value="">——— All Clients ———</option>
          <?php foreach ($clients as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($fClient??'')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['company_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">From Date:</label>
        <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $fStart??'' ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">To Date:</label>
        <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $fEnd??'' ?>">
      </div>
      <div class="col-auto d-flex gap-2">
        <button class="btn btn-success btn-sm"><i class="bi bi-search me-1"></i>SEARCH</button>
        <a href="<?= u('receipts/employeedues') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
      </div>
    </form>
  </div>
</div>

<?php
  $duesArr      = is_array($dues) ? $dues : [];
  $totalBill    = array_sum(array_column($duesArr, 'total_amount'));
  $totalPaid    = array_sum(array_column($duesArr, 'paid_amount'));
  $totalBalance = array_sum(array_column($duesArr, 'balance_amount'));
?>

<!-- Summary Stats -->
<div class="row g-3 mb-3">
  <div class="col-md-3">
    <div class="card text-center py-2" style="border-left:4px solid #6c5ce7">
      <div class="card-body py-1">
        <div class="small text-muted">Total Bills</div>
        <div class="fs-4 fw-bold"><?= count($dues) ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center py-2" style="border-left:4px solid #0d6efd">
      <div class="card-body py-1">
        <div class="small text-muted">Total Billed</div>
        <div class="fs-5 fw-bold text-primary"><?= Helper::money($totalBill) ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center py-2" style="border-left:4px solid #28a745">
      <div class="card-body py-1">
        <div class="small text-muted">Total Paid</div>
        <div class="fs-5 fw-bold text-success"><?= Helper::money($totalPaid) ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center py-2" style="border-left:4px solid #dc3545">
      <div class="card-body py-1">
        <div class="small text-muted">Total Balance Due</div>
        <div class="fs-5 fw-bold text-danger"><?= Helper::money($totalBalance) ?></div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover datatable mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Bill No</th>
          <th>Employee</th>
          <th>Client</th>
          <th>Bill Date</th>
          <th>Total Amount</th>
          <th>Paid</th>
          <th class="text-danger">Balance Due</th>
          <th>Due Period</th>
          <th>Status</th>
          <th class="no-print">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($duesArr as $i => $bill): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($bill['bill_no']) ?></td>
          <td><?= htmlspecialchars($bill['emp_name']) ?></td>
          <td><?= htmlspecialchars($bill['company_name'] ?? '—') ?></td>
          <td><?= $bill['bill_date'] ? date('d M Y', strtotime($bill['bill_date'])) : '—' ?></td>
          <td><?= Helper::money($bill['total_amount']) ?></td>
          <td class="text-success"><?= Helper::money($bill['paid_amount']) ?></td>
          <td class="text-danger fw-bold"><?= Helper::money($bill['balance_amount']) ?></td>
          <td class="small text-muted">
            <?php if ($bill['due_first_month'] && $bill['due_last_month']): ?>
              <?= date('M Y', strtotime($bill['due_first_month'])) ?> – <?= date('M Y', strtotime($bill['due_last_month'])) ?>
              <span class="badge bg-secondary ms-1"><?= $bill['no_of_dues'] ?> dues</span>
            <?php else: ?>
            —
            <?php endif; ?>
          </td>
          <td>
            <?php if ($bill['balance_amount'] <= 0): ?>
              <span class="badge bg-success">Cleared</span>
            <?php elseif ($bill['paid_amount'] > 0): ?>
              <span class="badge bg-warning text-dark">Partial</span>
            <?php else: ?>
              <span class="badge bg-danger">Unpaid</span>
            <?php endif; ?>
          </td>
          <td class="no-print">
            <?php if ($bill['balance_amount'] > 0): ?>
              <div class="btn-group btn-group-sm" role="group">
                <a href="<?= u('receipts/payuniform/' . $bill['id']) ?>" class="btn btn-primary"><i class="bi bi-cash-coin me-1"></i>Pay</a>
                <a href="<?= u('receipts/paymentlist_uniform/' . $bill['id']) ?>" class="btn btn-info"><i class="bi bi-list-check me-1"></i>History</a>
              </div>
            <?php else: ?>
              <a href="<?= u('receipts/paymentlist_uniform/' . $bill['id']) ?>" class="btn btn-info btn-sm"><i class="bi bi-list-check me-1"></i>History</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($dues)): ?>
        <tr><td colspan="11" class="text-center text-muted py-4"><i class="bi bi-check-circle text-success me-2 fs-4"></i><br>No employee dues found!</td></tr>
      <?php endif; ?>
      </tbody>
      <?php if (!empty($dues)): ?>
      <tfoot class="table-danger fw-bold">
        <tr>
          <td colspan="5" class="text-end">TOTAL (<?= count($duesArr) ?> bills):</td>
          <td><?= Helper::money($totalBill) ?></td>
          <td class="text-success"><?= Helper::money($totalPaid) ?></td>
          <td><?= Helper::money($totalBalance) ?></td>
          <td colspan="3"></td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>
