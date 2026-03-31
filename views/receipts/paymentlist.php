<?php
  $totalReceived = 0; $totalTDS = 0; $totalDamage = 0;
  foreach ($payments as $p) {
    if ($p['payment_type'] === 'received')       $totalReceived += $p['amount'];
    elseif ($p['payment_type'] === 'tds_deduct') $totalTDS      += $p['amount'];
    else                                          $totalDamage   += $p['amount'];
  }
  $totalDeducted = $totalTDS + $totalDamage;
  $totalPaid     = $totalReceived + $totalDeducted;
  $balance       = $inv['grand_total'] - $totalPaid;
?>
<div class="d-flex justify-content-between align-items-center mb-3 no-print">
  <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Payment List — <?= htmlspecialchars($inv['invoice_no']) ?></h5>
  <div class="d-flex gap-2">
    <a href="<?= u('receipts/receivepayment/' . $inv['id']) ?>" class="btn btn-primary btn-sm">
      <i class="bi bi-cash me-1"></i>Receive Payment
    </a>
    <a href="<?= u('receipts/receivepayment/' . $inv['id']) ?>?type=damage_deduct" class="btn btn-dark btn-sm">
      Damage Deduct Payment
    </a>
    <a href="<?= u('receipts/receivepayment/' . $inv['id']) ?>?type=tds_deduct" class="btn btn-secondary btn-sm">
      <i class="bi bi-arrow-counterclockwise me-1"></i>TDS Deduct
    </a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm no-print"><i class="bi bi-printer me-1"></i>Print</button>
    <a href="<?= u('receipts/clientbills') ?>" class="btn btn-outline-secondary btn-sm no-print"><i class="bi bi-arrow-left me-1"></i>Back</a>
  </div>
</div>

<!-- Invoice Summary Card -->
<div class="row mb-3">
  <div class="col-md-5">
    <div class="card">
      <div class="card-body py-3">
        <h6 class="fw-bold mb-2">Invoice Details</h6>
        <table class="table table-sm mb-0">
          <tr><td class="text-muted">Invoice No</td><td class="fw-bold"><?= htmlspecialchars($inv['invoice_no']) ?></td></tr>
          <tr><td class="text-muted">Company</td><td><?= htmlspecialchars($inv['company_name']) ?></td></tr>
          <tr><td class="text-muted">Month</td><td><?= htmlspecialchars($inv['invoice_month']) ?></td></tr>
          <tr><td class="text-muted">Grand Total</td><td class="fw-bold"><?= Helper::money($inv['grand_total']) ?></td></tr>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="row g-2">
      <div class="col-6">
        <div class="card text-center py-3" style="border-left:4px solid #28a745">
          <div class="card-body py-1">
            <div class="small text-muted">Total Received</div>
            <div class="fs-5 fw-bold text-success"><?= Helper::money($totalReceived) ?></div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card text-center py-3" style="border-left:4px solid #6c757d">
          <div class="card-body py-1">
            <div class="small text-muted">Total Deducted</div>
            <div class="fs-5 fw-bold text-secondary"><?= Helper::money($totalDeducted) ?></div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card text-center py-3" style="border-left:4px solid #0d6efd">
          <div class="card-body py-1">
            <div class="small text-muted">Total Paid</div>
            <div class="fs-5 fw-bold text-primary"><?= Helper::money($totalPaid) ?></div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card text-center py-3" style="border-left:4px solid <?= $balance > 0 ? '#dc3545' : '#28a745' ?>">
          <div class="card-body py-1">
            <div class="small text-muted">Balance Due</div>
            <div class="fs-5 fw-bold <?= $balance > 0 ? 'text-danger' : 'text-success' ?>"><?= Helper::money($balance) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Payment History Table -->
<h6 class="fw-bold mb-0">PAYMENT LIST</h6>
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Amount Received/Deducted</th>
          <th>Credit Account</th>
          <th>Method</th>
          <th>Ref No/Cheque No</th>
          <th class="no-print">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($payments)): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">No payments recorded yet.</td></tr>
      <?php endif; ?>
      <?php foreach ($payments as $i => $p): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
          <td>
            <?= Helper::money($p['amount']) ?>
            <span class="badge ms-1 <?=
              $p['payment_type'] === 'received'       ? 'bg-success' :
              ($p['payment_type'] === 'tds_deduct'    ? 'bg-secondary' : 'bg-dark')
            ?>"><?= ucwords(str_replace('_', ' ', $p['payment_type'])) ?></span>
          </td>
          <td><?= htmlspecialchars($p['ledger_name'] ?? '—') ?></td>
          <td><?= htmlspecialchars($p['payment_method'] ?? '—') ?></td>
          <td><?= htmlspecialchars($p['ref_no'] ?? '—') ?></td>
          <td class="no-print">
            <?php if ($p['cheque_photo']): ?>
              <a href="<?= BASE_URL ?>/uploads/cheque_photos/<?= htmlspecialchars($p['cheque_photo']) ?>" target="_blank" class="btn btn-xs btn-outline-info btn-sm">
                <i class="bi bi-image me-1"></i>Photo
              </a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
      <?php if (!empty($payments)): ?>
      <tfoot class="table-secondary fw-bold">
        <tr>
          <td colspan="2" class="text-end">TOTAL:</td>
          <td><?= Helper::money($totalPaid) ?></td>
          <td colspan="4"></td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>
