<?php
/* ── Advances — List + Filter + Add Modal ── */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Advance List</h5>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdvModal">
    <i class="bi bi-plus-lg me-1"></i>Add Advance
  </button>
</div>

<!-- Filter Panel -->
<div class="card mb-3">
  <div class="card-body py-2">
    <p class="small fw-semibold text-muted mb-2">USE THIS FILTERS TO GET YOUR EXACT LIST</p>
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="payments/advances">
      <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Field Officer:</label>
        <select name="field_officer_id" class="form-select form-select-sm">
          <option value="">——</option>
          <?php foreach ($fieldOfficers as $fo): ?>
          <option value="<?= $fo['id'] ?>" <?= ($fOfficer??'')==$fo['id']?'selected':'' ?>><?= htmlspecialchars($fo['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Select Company:</label>
        <select name="client_id" class="form-select form-select-sm">
          <option value="">——</option>
          <?php foreach ($clients as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($fClient??'')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['company_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Select Employee:</label>
        <select name="employee_id" class="form-select form-select-sm">
          <option value="">——</option>
          <?php foreach ($employees as $e): ?>
          <option value="<?= $e['id'] ?>" <?= ($fEmpId??'')==$e['id']?'selected':'' ?>><?= htmlspecialchars($e['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small fw-semibold mb-1">Advance Type:</label>
        <select name="advance_type" class="form-select form-select-sm">
          <option value="">——</option>
          <option value="Salary Advance"   <?= ($fType??'')==='Salary Advance'  ?'selected':'' ?>>Salary Advance</option>
          <option value="Emergency advance" <?= ($fType??'')==='Emergency advance'?'selected':'' ?>>Emergency advance</option>
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
        <a href="<?= u('payments/advances') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
      </div>
    </form>
  </div>
</div>

<!-- Advance List Table -->
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover datatable mb-0" style="font-size:13px">
      <thead class="table-light">
        <tr><th>Date</th><th>Employee</th><th>Type</th><th>Amount</th><th>No.Dues</th><th>Due Amt/Month</th><th>First Due</th><th>Last Due</th><th>Pay From</th></tr>
      </thead>
      <tbody>
      <?php foreach ($advList as $a): ?>
      <tr>
        <td><?= date('d M Y', strtotime($a['advance_date'])) ?></td>
        <td class="fw-semibold"><?= htmlspecialchars($a['name']) ?> <small class="text-muted"><?= $a['emp_code'] ?></small></td>
        <td><span class="badge bg-info text-dark"><?= $a['advance_type'] ?></span></td>
        <td class="fw-bold"><?= Helper::money($a['amount']) ?></td>
        <td><?= $a['no_of_dues'] ?></td>
        <td><?= Helper::money($a['due_amount']) ?></td>
        <td><?= $a['due_first_month'] ?></td>
        <td><?= $a['due_last_month'] ?></td>
        <td class="small text-muted"><?= htmlspecialchars($a['account_pay_from']??'—') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($advList)): ?><tr><td colspan="9" class="text-center text-muted py-3">No advances found</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Advance Modal -->
<div class="modal fade" id="addAdvModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>Add Advance</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Select Company <span class="text-danger">*</span></label>
              <select name="client_id_adv" class="form-select" id="advClientSel" onchange="filterAdvEmps(this.value)">
                <option value="">— Select Company —</option>
                <?php foreach ($clients as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['company_name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Select Employee <span class="text-danger">*</span></label>
              <select name="employee_id" class="form-select" required id="advEmpSel">
                <option value="">— Select Employee —</option>
                <?php foreach ($employees as $e): ?><option value="<?= $e['id'] ?>" data-client=""><?= htmlspecialchars($e['name'] . ' (' . $e['emp_code'] . ')') ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Advance Type <span class="text-danger">*</span></label>
              <select name="advance_type" class="form-select" required>
                <option>Salary Advance</option><option>Emergency advance</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Date <span class="text-danger">*</span></label>
              <input type="date" name="advance_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Amount (₹) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" name="amount" class="form-control" id="advAmt" oninput="calcAdvDue()" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">No. of Dues <span class="text-danger">*</span></label>
              <input type="number" name="no_of_dues" class="form-control" id="noDues" value="1" min="1" oninput="calcAdvDue()">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Account Pay From <span class="text-danger">*</span></label>
              <select name="account_pay_from" class="form-select">
                <option value="">——</option>
                <?php foreach ($ledgers as $l): ?><option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['account_name']) ?> — <?= Helper::money($l['current_balance']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Due First Month <span class="text-danger">*</span></label>
              <input type="month" name="due_first_month" class="form-control" id="advFirst" value="<?= date('Y-m') ?>" oninput="calcAdvDue()">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Due Last Month</label>
              <input type="month" name="due_last_month" class="form-control bg-light" id="advLast" readonly>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Due Amount (₹)</label>
              <input type="number" step="0.01" name="due_amount" class="form-control bg-light" id="dueAmt" readonly>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold small">Will Bill have Header?</label>
              <select name="bill_header" class="form-select"><option>Without Header</option><option>With Header</option></select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">Remarks, if Any</label>
              <textarea name="remarks" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
function calcAdvDue(){
  const amt   = parseFloat(document.getElementById('advAmt').value)||0;
  const dues  = parseInt(document.getElementById('noDues').value)||1;
  document.getElementById('dueAmt').value = (amt/dues).toFixed(2);
  // Calc last month
  const firstVal = document.getElementById('advFirst').value;
  if(firstVal && dues > 0){
    const d = new Date(firstVal + '-01');
    d.setMonth(d.getMonth() + dues - 1);
    document.getElementById('advLast').value = d.toISOString().slice(0,7);
  }
}
function filterAdvEmps(clientId){
  // Simple client filter — just show all for now (AJAX could be added later)
}
// Init calc on load
calcAdvDue();
</script>
