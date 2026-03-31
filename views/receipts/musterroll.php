<div class="d-flex justify-content-between align-items-center mb-3 no-print">
  <h5 class="mb-0"><i class="bi bi-people me-2"></i>Musterroll — <?= htmlspecialchars($invoice['invoice_no']) ?></h5>
  <div class="d-flex gap-2">
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
    <a href="<?= u('receipts/clientbills') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
  </div>
</div>

<div class="card" style="max-width:900px;margin:0 auto">
  <div class="card-body p-4">
    <!-- Company Header -->
    <div class="text-center mb-3 border-bottom pb-3">
      <h4 class="fw-bold mb-1">SAI SAKTHEESWARI SECURITY SERVICES</h4>
      <p class="small text-muted mb-0">No. 16A, Nellikuppam Main Road, S. N. Chavadi, Cuddalore-607006</p>
    </div>

    <h5 class="text-center fw-bold mb-3">MUSTER ROLL / DEPLOYMENT RECORD</h5>

    <!-- Invoice Info -->
    <div class="row mb-3">
      <div class="col-6">
        <table class="table table-sm table-borderless mb-0 small">
          <tr><td class="text-muted fw-semibold" style="width:110px">Client</td><td><?= htmlspecialchars($invoice['company_name']) ?></td></tr>
          <tr><td class="text-muted fw-semibold">Invoice No</td><td><?= htmlspecialchars($invoice['invoice_no']) ?></td></tr>
          <tr><td class="text-muted fw-semibold">Month</td><td><?= htmlspecialchars($invoice['invoice_month']) ?></td></tr>
        </table>
      </div>
      <div class="col-6 text-end">
        <table class="table table-sm table-borderless mb-0 small ms-auto" style="width:auto">
          <tr><td class="text-muted fw-semibold">Bill Type</td><td><span class="badge bg-info text-dark"><?= $invoice['bill_type'] ?></span></td></tr>
          <tr><td class="text-muted fw-semibold">Invoice Date</td><td><?= $invoice['invoice_date'] ? date('d M Y', strtotime($invoice['invoice_date'])) : '—' ?></td></tr>
          <tr><td class="text-muted fw-semibold">Grand Total</td><td class="fw-bold">₹<?= number_format($invoice['grand_total'], 2) ?></td></tr>
        </table>
      </div>
    </div>

    <!-- Items / Deployment Table -->
    <table class="table table-bordered table-sm mb-3" style="font-size:12px">
      <thead class="table-dark">
        <tr>
          <th>Sl</th>
          <th>Code</th>
          <th>Designation / Trade</th>
          <th class="text-center">NOS</th>
          <th class="text-center">Duties</th>
          <th class="text-center">OT</th>
          <th class="text-center">OFF Days</th>
          <th class="text-center">Total Hours</th>
          <th class="text-end">Rate/Hr</th>
          <th class="text-end">Amount</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($items)): ?>
        <tr><td colspan="10" class="text-center text-muted py-3">No deployment records found for this invoice.</td></tr>
      <?php endif; ?>
      <?php foreach ($items as $row): ?>
        <tr>
          <td><?= $row['sl_no'] ?></td>
          <td><?= htmlspecialchars($row['code']) ?></td>
          <td><?= htmlspecialchars($row['designation']) ?></td>
          <td class="text-center"><?= $row['nos'] ?></td>
          <td class="text-center"><?= $row['duties'] ?></td>
          <td class="text-center"><?= $row['ot'] ?></td>
          <td class="text-center"><?= $row['off_days'] ?></td>
          <td class="text-center"><?= $row['total_hours'] ?></td>
          <td class="text-end">₹<?= number_format($row['rate_per_hour'], 2) ?></td>
          <td class="text-end fw-bold">₹<?= number_format($row['amount'], 2) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
      <?php if (!empty($items)): ?>
      <tfoot>
        <tr class="table-secondary fw-bold">
          <td colspan="3">TOTAL</td>
          <td class="text-center"><?= array_sum(array_column($items, 'nos')) ?></td>
          <td class="text-center"><?= array_sum(array_column($items, 'duties')) ?></td>
          <td class="text-center"><?= array_sum(array_column($items, 'ot')) ?></td>
          <td class="text-center"><?= array_sum(array_column($items, 'off_days')) ?></td>
          <td class="text-center"><?= array_sum(array_column($items, 'total_hours')) ?></td>
          <td></td>
          <td class="text-end">₹<?= number_format(array_sum(array_column($items, 'amount')), 2) ?></td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>

    <!-- Deployed Employees (if any) -->
    <?php if (!empty($employees)): ?>
    <h6 class="fw-bold mb-2 mt-3">Deployed Personnel</h6>
    <table class="table table-bordered table-sm" style="font-size:12px">
      <thead class="table-light">
        <tr><th>#</th><th>Emp Code</th><th>Name</th><th>Designation</th><th>Site</th></tr>
      </thead>
      <tbody>
      <?php foreach ($employees as $ei => $emp): ?>
        <tr>
          <td><?= $ei + 1 ?></td>
          <td><?= htmlspecialchars($emp['emp_code']) ?></td>
          <td><?= htmlspecialchars($emp['name']) ?></td>
          <td><?= htmlspecialchars($emp['designation'] ?? '—') ?></td>
          <td><?= htmlspecialchars($emp['site_name'] ?? '—') ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>

    <!-- Signature Row -->
    <div class="row mt-5 pt-2">
      <div class="col-4 text-center"><div class="border-top pt-2 small fw-semibold">Prepared By</div></div>
      <div class="col-4 text-center"><div class="border-top pt-2 small fw-semibold">Verified By</div></div>
      <div class="col-4 text-center"><div class="border-top pt-2 small fw-semibold">Authorised Signatory</div></div>
    </div>
  </div>
</div>
