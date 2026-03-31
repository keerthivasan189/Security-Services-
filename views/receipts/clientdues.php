<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-exclamation-circle me-2 text-danger"></i>Client Dues</h5>
</div>

<!-- Filter Card -->
<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <p class="small fw-semibold text-muted mb-2">FILTER CLIENT DUES</p>
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="receipts/clientdues">

      <div class="col-md-4">
        <label class="form-label small fw-semibold mb-1">Client:</label>
        <select name="client_id" class="form-select form-select-sm">
          <option value="">——— All Clients ———</option>
          <?php foreach ($clients as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($fClient??'')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['company_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Status:</label>
        <select name="due_status" class="form-select form-select-sm">
          <option value="">All Dues</option>
          <option value="unpaid" <?= ($fStatus??'')==='unpaid'?'selected':'' ?>>Unpaid Only</option>
          <option value="partial" <?= ($fStatus??'')==='partial'?'selected':'' ?>>Partial Only</option>
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
        <a href="<?= u('receipts/clientdues') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
      </div>
    </form>
  </div>
</div>

<?php
  $duesArr      = is_array($dues) ? $dues : [];
  $totalBill    = array_sum(array_column($duesArr, 'grand_total'));
  $totalPaid    = array_sum(array_map(fn($r) => (float)$r['grand_total'] - (float)$r['total_outstanding'], $duesArr));
  $totalBalance = array_sum(array_column($duesArr, 'total_outstanding'));
?>

<!-- Summary Stats -->
<div class="row g-3 mb-3">
  <div class="col-md-3">
    <div class="card text-center py-2" style="border-left:4px solid #6c5ce7">
      <div class="card-body py-1">
        <div class="small text-muted">Total Invoices</div>
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
        <div class="small text-muted">Total Received</div>
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
          <th>Invoice No</th>
          <th>Company</th>
          <th>Type</th>
          <th>Month</th>
          <th>Invoice Date</th>
          <th>Grand Total</th>
          <th>Paid</th>
          <th class="text-danger">Balance Due</th>
          <th>Status</th>
          <th class="no-print">Action</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($duesArr as $i => $inv): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td class="fw-semibold"><a href="<?= u('receipts/viewinvoice/' . $inv['id']) ?>"><?= htmlspecialchars($inv['invoice_no']) ?></a></td>
          <td><?= htmlspecialchars($inv['company_name']) ?></td>
          <td><span class="badge <?= $inv['bill_type']==='GST'?'bg-info text-dark':'bg-warning text-dark' ?>"><?= $inv['bill_type'] ?></span></td>
          <td><?= htmlspecialchars($inv['invoice_month']) ?></td>
          <td><?= $inv['invoice_date'] ? date('d M Y', strtotime($inv['invoice_date'])) : '—' ?></td>
          <td><?= Helper::money($inv['grand_total']) ?></td>
          <td class="text-success"><?= Helper::money($inv['grand_total'] - $inv['total_outstanding']) ?></td>
          <td class="text-danger fw-bold"><?= Helper::money($inv['total_outstanding']) ?></td>
          <td><span class="badge badge-<?= $inv['payment_status'] ?>"><?= ucfirst($inv['payment_status']) ?></span></td>
          <td class="no-print">
            <a href="<?= u('receipts/receivepayment/' . $inv['id']) ?>" class="btn btn-success btn-sm">
              <i class="bi bi-cash me-1"></i>Pay
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($dues)): ?>
        <tr><td colspan="11" class="text-center text-muted py-4"><i class="bi bi-check-circle text-success me-2 fs-4"></i><br>No outstanding dues found!</td></tr>
      <?php endif; ?>
      </tbody>
      <?php if (!empty($dues)): ?>
      <tfoot class="table-danger fw-bold">
        <tr>
          <td colspan="6" class="text-end">TOTAL (<?= count($duesArr) ?> due invoices):</td>
          <td><?= Helper::money($totalBill) ?></td>
          <td class="text-success"><?= Helper::money($totalPaid) ?></td>
          <td class="fw-bold"><?= Helper::money($totalBalance) ?></td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>
