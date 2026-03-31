<?php
/* ── MISC Expenses — List + Filter + Add Modal ── */
$expTypes = ['OTHER EXPENSES','OFFICE EXPENSE','TRANSPORT','PRINTING','POSTAGE','BANK CHARGES','STAFF WELFARE','REPAIRS','LEGAL & PROFESSIONAL'];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-receipt-cutoff me-2"></i>Misc Bills</h5>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMiscModal">
    <i class="bi bi-plus-lg me-1"></i>Add Misc Bill
  </button>
</div>

<!-- Filter Panel -->
<div class="card mb-3">
  <div class="card-body py-2">
    <p class="small fw-semibold text-muted mb-2">USE THIS FILTERS TO GET YOUR EXACT LIST</p>
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="payments/misc">
      <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Expense Type:</label>
        <select name="expense_type" class="form-select form-select-sm">
          <option value="">——</option>
          <?php foreach ($expTypes as $et): ?>
          <option value="<?= $et ?>" <?= ($fType??'')===$et?'selected':'' ?>><?= $et ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Paid or Not:</label>
        <select name="paid_status" class="form-select form-select-sm">
          <option value="">——</option>
          <option value="Paid"     <?= ($fPaid??'')==='Paid'    ?'selected':'' ?>>Paid</option>
          <option value="Not Paid" <?= ($fPaid??'')==='Not Paid'?'selected':'' ?>>Not Paid</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Biller (Search):</label>
        <input type="text" name="biller" class="form-control form-control-sm" placeholder="Biller name…" value="<?= htmlspecialchars($fBiller??'') ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Start Date:</label>
        <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $fStart??'' ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">End Date:</label>
        <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $fEnd??'' ?>">
      </div>
      <div class="col-auto d-flex gap-2">
        <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search me-1"></i>Search</button>
        <a href="<?= u('payments/misc') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
      </div>
    </form>
  </div>
</div>

<!-- Bills Table -->
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover datatable mb-0" style="font-size:13px">
      <thead class="table-light">
        <tr><th>#</th><th>Bill No</th><th>Date</th><th>Description</th><th>Type</th><th>Biller</th><th>Gross Amt</th><th>Bill Amt</th><th>Status</th></tr>
      </thead>
      <tbody>
      <?php $sno=0; foreach ($bills as $b): $sno++; ?>
      <tr>
        <td><?= $sno ?></td>
        <td><?= htmlspecialchars($b['bill_no']??'—') ?></td>
        <td><?= date('d M Y', strtotime($b['bill_date'])) ?></td>
        <td><?= htmlspecialchars(substr($b['expense_desc']??'',0,35)) ?></td>
        <td><span class="badge bg-secondary small"><?= $b['expense_type'] ?></span></td>
        <td><?= htmlspecialchars($b['biller']??'') ?></td>
        <td><?= Helper::money($b['gross_amount']) ?></td>
        <td><?= Helper::money($b['bill_amount']) ?></td>
        <td><span class="badge <?= $b['paid_status']==='Paid'?'bg-success':'bg-danger' ?>"><?= $b['paid_status'] ?></span></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($bills)): ?><tr><td colspan="9" class="text-center text-muted py-3">No bills found</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add MISC Modal -->
<div class="modal fade" id="addMiscModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-receipt-cutoff me-2"></i>Add Misc Bill</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-3"><label class="form-label fw-semibold small">MISC Bill/Invoice No <span class="text-danger">*</span></label><input type="text" name="bill_no" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label fw-semibold small">Expense Desc <span class="text-danger">*</span></label><input type="text" name="expense_desc" class="form-control" required></div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Expense Type <span class="text-danger">*</span></label>
              <select name="expense_type" class="form-select" required>
                <?php foreach ($expTypes as $et): ?><option><?= $et ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3"><label class="form-label fw-semibold small">Gross Amount</label><input type="text" id="mGross" class="form-control bg-light" readonly value="0.0"></div>
            <div class="col-md-3"><label class="form-label fw-semibold small">Biller <span class="text-danger">*</span></label><input type="text" name="biller" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label fw-semibold small">Date of Bill Raised <span class="text-danger">*</span></label><input type="date" name="bill_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
            <div class="col-md-3"><label class="form-label fw-semibold small">Bill Amount <span class="text-danger">*</span></label><input type="number" step="0.01" name="bill_amount" class="form-control" id="mBillAmt" oninput="calcMiscGross()" required></div>
            <div class="col-md-3"><label class="form-label fw-semibold small">Ref No</label><input type="text" name="ref_no" class="form-control"></div>
            <div class="col-md-2"><label class="form-label fw-semibold small">IGST:</label><input type="number" step="0.01" name="igst" class="form-control miscTax" value="0.0" oninput="calcMiscGross()"></div>
            <div class="col-md-2"><label class="form-label fw-semibold small">SGST:</label><input type="number" step="0.01" name="sgst" class="form-control miscTax" value="0.0" oninput="calcMiscGross()"></div>
            <div class="col-md-2"><label class="form-label fw-semibold small">CGST:</label><input type="number" step="0.01" name="cgst" class="form-control miscTax" value="0.0" oninput="calcMiscGross()"></div>
            <div class="col-md-2"><label class="form-label fw-semibold small">Discount:</label><input type="number" step="0.01" name="discount" class="form-control miscDisc" value="0.0" oninput="calcMiscGross()"></div>
            <div class="col-md-4"><label class="form-label fw-semibold small">Photo Copy of Bill <span class="text-danger">*</span></label><input type="file" name="bill_photo" class="form-control" accept=".jpg,.jpeg,.png,.pdf"></div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Paid or Not <span class="text-danger">*</span></label>
              <select name="paid_status" class="form-select"><option>Not Paid</option><option>Paid</option></select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Paid From:</label>
              <select name="paid_from_ledger" class="form-select">
                <option value="">——</option>
                <?php foreach ($ledgers as $l): ?><option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['account_name']) ?> — <?= Helper::money($l['current_balance']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Through:</label>
              <select name="through_ledger" class="form-select">
                <option value="">——</option>
                <?php foreach ($ledgers as $l): ?><option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['account_name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4"><label class="form-label fw-semibold small">Photo of Cheque if paid by Cheque:</label><input type="file" name="cheque_photo" class="form-control" accept=".jpg,.jpeg,.png,.pdf"></div>
            <div class="col-md-4"><label class="form-label fw-semibold small">Transaction No, if paid By Online:</label><input type="text" name="transaction_no" class="form-control"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Save Misc Bill</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
function calcMiscGross(){
  const amt = parseFloat(document.getElementById('mBillAmt').value)||0;
  let tax = 0, disc = 0;
  document.querySelectorAll('.miscTax').forEach((el,i) => {
    if(i < 3) tax += parseFloat(el.value)||0;
    else disc = parseFloat(el.value)||0;
  });
  document.getElementById('mGross').value = (amt + tax - disc).toFixed(2);
}
function syncMiscBill(){
  // If gross is filled first, sync to bill amount
  const g = parseFloat(document.getElementById('mGross').value)||0;
  if(!document.getElementById('mBillAmt').value) document.getElementById('mBillAmt').value = g.toFixed(2);
}
</script>
