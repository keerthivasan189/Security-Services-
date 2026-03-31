<div class="d-flex justify-content-between align-items-center mb-3 no-print">
  <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Invoice — <?= htmlspecialchars($invoice['invoice_no']) ?></h5>
  <div class="d-flex gap-2">
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
    <a href="<?= u('receipts/receivepayment/' . $invoice['id']) ?>" class="btn btn-success btn-sm"><i class="bi bi-cash me-1"></i>Receive Payment</a>
    <a href="<?= u('receipts/clientbills') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
  </div>
</div>

<div class="card" style="max-width:800px;margin:0 auto">
  <div class="card-body p-4">
    <!-- Company Header -->
    <?php if ($invoice['bill_type'] !== 'CASH'): ?>
    <div class="text-center mb-3">
      <h4 class="fw-bold mb-1">SAI SAKTHEESWARI SECURITY SERVICES</h4>
      <p class="text-muted small mb-0">(No physical copy required; valid electronically)</p>
    </div>

    <div class="row mb-3">
      <div class="col-6">
        <p class="mb-1 fw-semibold">SRI SAKTHEESWARI TOWER</p>
        <p class="small mb-0 text-muted">No. 16A, Nellikuppam Main Road, S. N. Chavadi<br>
        Cuddalore-607006 | Phone: 04142-291855<br>
        Cell: 93676 26855, 93606 26855, 93616 26855<br>
        E-mail: saisaktheeswari@gmail.com</p>
      </div>
      <div class="col-6 text-end">
        <p class="small mb-0 text-muted">
          Govt.Regd: TN 985 PY149<br>
          EPF Code No: CBTRY1744659000<br>
          ESI Code No: 51001255960001018<br>
          GSTIN: 33ADIFS7131D1ZB<br>
          PAN No: ADIFS7131D
        </p>
      </div>
    </div>
    <?php endif; ?>

    <div class="row mb-3">
      <div class="col-6">
        <div class="border p-2 rounded">
          <p class="small fw-bold mb-1">CLINT ID: <?= htmlspecialchars($invoice['client_id']) ?></p>
          <p class="small mb-0">
            <strong>To,</strong><br>
            <?= htmlspecialchars($invoice['company_name']) ?><br>
            <?= nl2br(htmlspecialchars($invoice['address'])) ?><br>
            <?php if ($invoice['gstin']): ?><strong>GSTIN:</strong> <?= htmlspecialchars($invoice['gstin']) ?><?php endif; ?>
          </p>
        </div>
      </div>
      <div class="col-6">
        <div class="border p-2 rounded text-center">
          <h6 class="fw-bold mb-2">DIGITAL INVOICE</h6>
          <table class="table table-sm table-borderless mb-0 small">
            <tr><td>Invoice No</td><td class="fw-bold"><?= htmlspecialchars($invoice['invoice_no']) ?></td></tr>
            <tr><td>Date</td><td><?= $invoice['invoice_date'] ? date('d M Y', strtotime($invoice['invoice_date'])) : '—' ?></td></tr>
            <tr><td>Month</td><td><?= htmlspecialchars($invoice['invoice_month']) ?></td></tr>
            <tr><td>Deployed Hours</td><td><?= $invoice['deployed_hours'] ?></td></tr>
            <tr><td>Bill Type</td><td><span class="badge <?= $invoice['bill_type']==='GST'?'bg-info text-dark':'bg-warning text-dark' ?>"><?= $invoice['bill_type'] ?></span></td></tr>
          </table>
        </div>
      </div>
    </div>

    <!-- Items Table -->
    <table class="table table-bordered table-sm mb-3" style="font-size:12px">
      <thead class="table-dark">
        <tr>
          <th>Sl</th><th>Code</th><th>SAC</th><th>Trade/Designation</th>
          <th>NOS</th><th>Duties</th><th>OT</th><th>OFF</th>
          <th>Total Hours</th><th>Rate/Hour</th><th>Amount</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><?= $item['sl_no'] ?></td>
        <td><?= htmlspecialchars($item['code']) ?></td>
        <td><?= htmlspecialchars($item['sac']) ?></td>
        <td><?= htmlspecialchars($item['designation']) ?></td>
        <td><?= $item['nos'] ?></td>
        <td><?= $item['duties'] ?></td>
        <td><?= $item['ot'] ?></td>
        <td><?= $item['off_days'] ?></td>
        <td><?= $item['total_hours'] ?></td>
        <td>₹<?= number_format($item['rate_per_hour'],2) ?></td>
        <td class="fw-bold">₹<?= number_format($item['amount'],2) ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>

    <?php if ($invoice['bill_type']==='RCM'): ?>
    <p class="text-center fw-semibold small border p-1 rounded bg-light">
      GST PAID BY SERVICE RECIPIENT UNDER REVERSE CHARGE MECHANISM
    </p>
    <?php endif; ?>

    <!-- Totals -->
    <div class="row">
      <div class="col-6">
        <!-- Outstanding bills table -->
        <?php if (!empty($payments)): ?>
        <h6 class="fw-semibold small mb-2">Payment History</h6>
        <table class="table table-sm table-bordered" style="font-size:11px">
          <thead class="table-light"><tr><th>Date</th><th>Type</th><th>Amount</th></tr></thead>
          <tbody>
          <?php foreach ($payments as $p): ?>
          <tr>
            <td><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
            <td><?= ucfirst($p['payment_type']) ?></td>
            <td><?= Helper::money($p['amount']) ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
      <div class="col-6">
        <table class="table table-sm table-bordered ms-auto" style="font-size:12px;max-width:260px">
          <tr><td class="fw-semibold">Total Outstanding</td><td class="text-end fw-bold">₹<?= number_format($invoice['total_outstanding'],2) ?></td></tr>
          <tr><td>Grand Total</td><td class="text-end fw-bold">₹<?= number_format($invoice['grand_total'],2) ?></td></tr>
          <tr><td>Round Off</td><td class="text-end">₹<?= number_format($invoice['round_off'],2) ?></td></tr>
        </table>
        <div class="text-end mt-2">
          <span class="badge <?= $invoice['payment_status']==='paid'?'bg-success':($invoice['payment_status']==='partial'?'bg-warning text-dark':'bg-danger') ?> fs-6">
            <?= ucfirst($invoice['payment_status']) ?>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>
