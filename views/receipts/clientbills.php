<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Client Invoices</h5>
  <a href="<?= u('receipts/addinvoice') ?>" class="btn btn-primary btn-sm no-print">
    <i class="bi bi-plus-circle me-1"></i>Create Invoice
  </a>
</div>

<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <p class="small fw-semibold text-muted mb-2">USE THIS FILTERS TO GET YOUR EXACT LIST</p>
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="receipts/clientbills">
      
      <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Invoice No.</label>
        <input type="text" name="invoice_no" class="form-control form-control-sm" value="<?= htmlspecialchars($fInvNo??'') ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Field Officer:</label>
        <select name="field_officer_id" class="form-select form-select-sm">
          <option value="">——— All ———</option>
          <?php foreach ($fieldOfficers as $fo): ?>
          <option value="<?= $fo['id'] ?>" <?= ($fOfficer??'')==$fo['id']?'selected':'' ?>><?= htmlspecialchars($fo['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold mb-1">Client:</label>
        <select name="client_id" class="form-select form-select-sm">
          <option value="">——— All ———</option>
          <?php foreach ($clients as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($fClient??'')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['company_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Bill Type:</label>
        <select name="bill_type" class="form-select form-select-sm">
          <option value="">——— All ———</option>
          <option value="GST" <?= ($fType??'')==='GST'?'selected':'' ?>>GST</option>
          <option value="RCM" <?= ($fType??'')==='RCM'?'selected':'' ?>>RCM</option>
          <option value="CASH" <?= ($fType??'')==='CASH'?'selected':'' ?>>CASH</option>
        </select>
      </div>

      <div class="col-md-2 mt-1">
        <label class="form-label small fw-semibold mb-1">Financial Year:</label>
        <select name="fin_year" class="form-select form-select-sm">
          <option value="">——— All ———</option>
          <option value="2024-2025" <?= ($fFinYear??'')==='2024-2025'?'selected':'' ?>>2024-2025</option>
          <option value="2025-2026" <?= ($fFinYear??'')==='2025-2026'?'selected':'' ?>>2025-2026</option>
          <option value="2026-2027" <?= ($fFinYear??'')==='2026-2027'?'selected':'' ?>>2026-2027</option>
        </select>
      </div>
      <div class="col-md-3 mt-1">
        <label class="form-label small fw-semibold mb-1">Paid or Not:</label>
        <select name="paid_status" class="form-select form-select-sm">
          <option value="">——— All ———</option>
          <option value="Paid" <?= ($fPaid??'')==='Paid'?'selected':'' ?>>Paid</option>
          <option value="Partial" <?= ($fPaid??'')==='Partial'?'selected':'' ?>>Partial</option>
          <option value="Unpaid" <?= ($fPaid??'')==='Unpaid'?'selected':'' ?>>Unpaid</option>
        </select>
      </div>
      <div class="col-md-2 mt-1">
        <label class="form-label small fw-semibold mb-1">Start Date:</label>
        <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $fStart??'' ?>">
      </div>
      <div class="col-md-2 mt-1">
        <label class="form-label small fw-semibold mb-1">End Date:</label>
        <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $fEnd??'' ?>">
      </div>

      <div class="col-auto mt-1 d-flex gap-2">
        <button class="btn btn-success btn-sm"><i class="bi bi-search me-1"></i>SEARCH</button>
        <a href="<?= u('receipts/clientbills') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
      </div>
    </form>
  </div>
</div>

<?php
  $totalAmt  = array_sum(array_column($invoices, 'grand_total'));
  $totalPaid = array_sum(array_map(fn($r) => $r['grand_total'] - $r['total_outstanding'], $invoices));
  $totalBal  = array_sum(array_column($invoices, 'total_outstanding'));
?>
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover datatable mb-0">
      <thead class="table-light">
        <tr>
          <th>Bill No</th><th>Company</th><th>Type</th><th>Month</th>
          <th>Sent On</th><th>Amount</th><th>Paid</th><th>Balance</th>
          <th>Status</th><th class="no-print">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($invoices as $inv): ?>
      <tr>
        <td class="fw-semibold"><a href="<?= u('receipts/viewinvoice/' . $inv['id']) ?>"><?= htmlspecialchars($inv['invoice_no']) ?></a></td>
        <td><?= htmlspecialchars($inv['company_name']) ?></td>
        <td><span class="badge <?= $inv['bill_type']==='GST'?'bg-info text-dark':'bg-warning text-dark' ?>"><?= $inv['bill_type'] ?></span></td>
        <td><?= htmlspecialchars($inv['invoice_month']) ?></td>
        <td><?= $inv['sent_date'] ? date('d M Y', strtotime($inv['sent_date'])) : '—' ?></td>
        <td class="fw-semibold"><?= Helper::money($inv['grand_total']) ?></td>
        <td class="text-success"><?= Helper::money($inv['grand_total'] - $inv['total_outstanding']) ?></td>
        <td class="<?= $inv['total_outstanding']>0?'text-danger fw-bold':'' ?>"><?= Helper::money($inv['total_outstanding']) ?></td>
        <td><span class="badge badge-<?= $inv['payment_status'] ?>"><?= ucfirst($inv['payment_status']) ?></span></td>
        <td class="no-print">
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="<?= u('receipts/viewinvoice/' . $inv['id']) ?>"><i class="bi bi-eye me-2"></i>View Bill</a></li>
              <li><a class="dropdown-item" href="<?= u('receipts/viewinvoice/' . $inv['id']) ?>" target="_blank" onclick="var w=window.open(this.href,'_blank'); w.addEventListener('load',function(){w.print();}); return false;"><i class="bi bi-download me-2"></i>Download Bill</a></li>
              <li><a class="dropdown-item" href="<?= u('receipts/paymentlist/' . $inv['id']) ?>"><i class="bi bi-list-check me-2"></i>Payment List</a></li>
              <li><a class="dropdown-item" href="<?= u('receipts/musterroll/' . $inv['id']) ?>"><i class="bi bi-people me-2"></i>Musterroll</a></li>
            </ul>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($invoices)): ?>
      <tr><td colspan="10" class="text-center text-muted py-4">No invoices found</td></tr>
      <?php endif; ?>
      </tbody>
      <?php if (!empty($invoices)): ?>
      <tfoot class="table-secondary fw-bold">
        <tr>
          <td colspan="5" class="text-end">TOTAL (<?= count($invoices) ?> invoices):</td>
          <td><?= Helper::money($totalAmt) ?></td>
          <td class="text-success"><?= Helper::money($totalPaid) ?></td>
          <td class="text-danger"><?= Helper::money($totalBal) ?></td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>
