<?php
/* ── Fuel Expenses — List + Filter + Add Modal ── */
$expTypes = ['FUEL EXPENSES','VEHICLE MAINTENANCE','VEHICLE REPAIR','VEHICLE INSURANCE','OTHER VEHICLE'];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-fuel-pump me-2"></i>Fuel Expenses Bills</h5>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFuelModal">
    <i class="bi bi-plus-lg me-1"></i>Add Fuel Expenses Bills
  </button>
</div>

<!-- Filter Panel -->
<div class="card mb-3">
  <div class="card-body py-2">
    <p class="small fw-semibold text-muted mb-2">USE THIS FILTERS TO GET YOUR EXACT LIST</p>
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="payments/fuel">
      <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Vehicle:</label>
        <select name="vehicle_no" class="form-select form-select-sm">
          <option value="">——</option>
          <?php foreach ($vehicles as $v): ?>
          <option value="<?= htmlspecialchars($v['vehicle_no']) ?>" <?= ($fVehicle??'')===$v['vehicle_no']?'selected':'' ?>><?= htmlspecialchars($v['vehicle_no']) ?></option>
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
        <label class="form-label small fw-semibold mb-1">Paid From:</label>
        <select name="paid_from_ledger" class="form-select form-select-sm">
          <option value="">——</option>
          <?php foreach ($ledgers as $l): ?>
          <option value="<?= $l['id'] ?>" <?= ($fLedger??'')==$l['id']?'selected':'' ?>><?= htmlspecialchars($l['account_name']) ?></option>
          <?php endforeach; ?>
        </select>
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
        <a href="<?= u('payments/fuel') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
      </div>
    </form>
  </div>
</div>

<!-- Bills Table -->
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover datatable mb-0" style="font-size:13px">
      <thead class="table-light">
        <tr><th>#</th><th>Ref No</th><th>Date</th><th>Vehicle</th><th>Biller</th><th>Bill Amount</th><th>Gross Amount</th><th>Status</th><th>Paid From</th></tr>
      </thead>
      <tbody>
      <?php $sno=0; foreach ($bills as $b): $sno++; ?>
      <tr>
        <td><?= $sno ?></td>
        <td class="small text-muted"><?= htmlspecialchars($b['ref_no']??'—') ?></td>
        <td><?= date('d M Y', strtotime($b['bill_date'])) ?></td>
        <td class="fw-semibold"><?= htmlspecialchars($b['vehicle_no']) ?></td>
        <td><?= htmlspecialchars($b['biller']) ?></td>
        <td><?= Helper::money($b['bill_amount']) ?></td>
        <td><?= Helper::money(($b['bill_amount']??0)+($b['igst']??0)+($b['sgst']??0)+($b['cgst']??0)) ?></td>
        <td><span class="badge <?= $b['paid_status']==='Paid'?'bg-success':'bg-danger' ?>"><?= $b['paid_status'] ?></span></td>
        <td class="small text-muted"><?= htmlspecialchars($b['account_name']??'—') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($bills)): ?><tr><td colspan="9" class="text-center text-muted py-3">No fuel bills found</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Fuel Bill Modal -->
<div class="modal fade" id="addFuelModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-fuel-pump me-2"></i>Add Fuel Bill</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-2"><label class="form-label fw-semibold small">Ref No <span class="text-danger">*</span></label><input type="text" name="ref_no" class="form-control" required></div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Expense Type <span class="text-danger">*</span></label>
              <select name="expense_type" class="form-select">
                <?php foreach ($expTypes as $et): ?><option><?= $et ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Vehicle <span class="text-danger">*</span></label>
              <select name="vehicle_no" class="form-select" required>
                <option value="">— Select —</option>
                <?php foreach ($vehicles as $v): ?><option value="<?= $v['vehicle_no'] ?>"><?= $v['vehicle_no'] ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2"><label class="form-label fw-semibold small">Current KM <span class="text-danger">*</span></label><input type="number" name="current_km" class="form-control" value="0"></div>
            <div class="col-md-3"><label class="form-label fw-semibold small">Biller <span class="text-danger">*</span></label><input type="text" name="biller" class="form-control" required></div>
            <div class="col-md-2"><label class="form-label fw-semibold small">Date of Bill <span class="text-danger">*</span></label><input type="date" name="bill_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
            <div class="col-md-2"><label class="form-label fw-semibold small">Bill Amount <span class="text-danger">*</span></label><input type="number" step="0.01" name="bill_amount" class="form-control" id="fBillAmt" oninput="calcFuelGross()" required></div>
            <div class="col-md-2"><label class="form-label fw-semibold small">IGST</label><input type="number" step="0.01" name="igst" class="form-control fuelTax" value="0" oninput="calcFuelGross()"></div>
            <div class="col-md-2"><label class="form-label fw-semibold small">SGST</label><input type="number" step="0.01" name="sgst" class="form-control fuelTax" value="0" oninput="calcFuelGross()"></div>
            <div class="col-md-2"><label class="form-label fw-semibold small">CGST</label><input type="number" step="0.01" name="cgst" class="form-control fuelTax" value="0" oninput="calcFuelGross()"></div>
            <div class="col-md-2"><label class="form-label fw-semibold small">Gross Amount</label><input type="text" id="fGross" class="form-control bg-light" readonly value="0.0"></div>
            <div class="col-md-4"><label class="form-label fw-semibold small">Photo Copy of Bill <span class="text-danger">*</span></label><input type="file" name="bill_photo" class="form-control" accept=".jpg,.jpeg,.png,.pdf"></div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Paid or Not <span class="text-danger">*</span></label>
              <select name="paid_status" class="form-select"><option>Not Paid</option><option>Paid</option></select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Paid From</label>
              <select name="paid_from_ledger" class="form-select">
                <option value="">——</option>
                <?php foreach ($ledgers as $l): ?><option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['account_name']) ?> — <?= Helper::money($l['current_balance']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4"><label class="form-label fw-semibold small">Photo of Cheque (if paid by cheque)</label><input type="file" name="cheque_photo" class="form-control" accept=".jpg,.jpeg,.png,.pdf"></div>
            <div class="col-md-3"><label class="form-label fw-semibold small">Transaction No (if paid online)</label><input type="text" name="transaction_no" class="form-control"></div>
            <div class="col-12"><label class="form-label fw-semibold small">Remarks, if Any</label><input type="text" name="remarks" class="form-control"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Save Fuel Bill</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
function calcFuelGross(){
  const amt  = parseFloat(document.getElementById('fBillAmt').value)||0;
  let tax = 0;
  document.querySelectorAll('.fuelTax').forEach(i => tax += parseFloat(i.value)||0);
  document.getElementById('fGross').value = (amt + tax).toFixed(2);
}
</script>
