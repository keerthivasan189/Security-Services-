<?php if (empty($showList)): ?>
<!-- ===== ADD FORM SECTION ===== -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0 fw-bold text-uppercase">Other Deductions / Penalties</h5>
  <a href="<?= u('receipts/deductions') ?>?show=all" class="btn btn-primary btn-sm px-3">
    SHOW ALL OTHER DEDUCTIONS / PENALTIES
  </a>
</div>

<div class="card mb-3">
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="add">
      <div class="row g-3">
        <!-- Row 1 -->
        <div class="col-md-3">
          <label class="form-label fw-semibold small">Select Company:</label>
          <select name="client_id" class="form-select" id="dCompany" onchange="loadEmpBySite(this.value)">
            <option value="">——</option>
            <?php foreach($clients as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['company_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small">Employee: <span class="text-danger">*</span></label>
          <select name="employee_id" class="form-select" id="dEmployee" required onchange="fetchEmpSalary(this.value)">
            <option value="">———</option>
            <?php foreach($employees as $e): ?>
            <option value="<?= $e['id'] ?>" data-salary="<?= $e['last_net_salary'] ?? 0 ?>"><?= htmlspecialchars($e['name'] . ' (' . $e['emp_code'] . ')') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small">Date: <span class="text-danger">*</span></label>
          <input type="date" name="deduction_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small">Amount: <span class="text-danger">*</span></label>
          <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>

        <!-- Row 1b - Salary info -->
        <div class="col-md-3 d-none" id="salaryInfoBox">
          <div class="alert alert-info py-2 mb-0 small">
            <i class="bi bi-info-circle me-1"></i> Last Net Salary: <strong id="salaryInfoVal">—</strong>
          </div>
        </div>

        <!-- Row 2 -->
        <div class="col-md-3">
          <label class="form-label fw-semibold small">File 1:</label>
          <input type="file" name="file1" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small">File 2:</label>
          <input type="file" name="file2" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small">Reason: <span class="text-danger">*</span></label>
          <input type="text" name="reason" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small">Remarks, if Any:</label>
          <input type="text" name="remarks" class="form-control">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary px-4">SAVE</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function fetchEmpSalary(empId) {
  const sel = document.getElementById('dEmployee');
  const opt = sel.querySelector('option[value="' + empId + '"]');
  const box = document.getElementById('salaryInfoBox');
  const val = document.getElementById('salaryInfoVal');
  if (opt && opt.dataset.salary && parseFloat(opt.dataset.salary) > 0) {
    val.textContent = '₹' + parseFloat(opt.dataset.salary).toLocaleString('en-IN', {minimumFractionDigits:2});
    box.classList.remove('d-none');
  } else if (empId) {
    // Fetch salary via AJAX
    fetch('<?= BASE_URL ?>/index.php?url=receipts/getEmpSalary&emp_id=' + empId)
      .then(r => r.json())
      .then(d => {
        if (d.salary) {
          val.textContent = '₹' + parseFloat(d.salary).toLocaleString('en-IN', {minimumFractionDigits:2});
          box.classList.remove('d-none');
        } else {
          box.classList.add('d-none');
        }
      }).catch(() => box.classList.add('d-none'));
  } else {
    box.classList.add('d-none');
  }
}
</script>

<?php else: ?>
<!-- ===== LIST / FILTER SECTION ===== -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0 fw-bold text-uppercase">Other Deductions / Penalties — All Records</h5>
  <a href="<?= u('receipts/deductions') ?>" class="btn btn-primary btn-sm px-3">
    + ADD DEDUCTION / PENALTY
  </a>
</div>

<!-- Filter Panel -->
<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <p class="small fw-semibold text-muted mb-2">USE THIS FILTERS TO GET YOUR EXACT LIST</p>
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="receipts/deductions">
      <input type="hidden" name="show" value="all">
      <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Company:</label>
        <select name="client_id" class="form-select form-select-sm">
          <option value="">——— All ———</option>
          <?php foreach($clients as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($fClient??'')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['company_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Employee Name:</label>
        <select name="employee_id" class="form-select form-select-sm">
          <option value="">——— All ———</option>
          <?php foreach($employees as $e): ?>
          <option value="<?= $e['id'] ?>" <?= ($fEmp??'')==$e['id']?'selected':'' ?>><?= htmlspecialchars($e['name'] . ' (' . $e['emp_code'] . ')') ?></option>
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
        <button class="btn btn-success btn-sm"><i class="bi bi-search me-1"></i>SEARCH</button>
        <a href="<?= u('receipts/deductions') ?>?show=all" class="btn btn-outline-secondary btn-sm">Clear</a>
      </div>
    </form>
  </div>
</div>

<!-- Table -->
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover datatable mb-0">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Employee</th>
          <th>Company</th>
          <th>Amount</th>
          <th>Reason</th>
          <th>Remarks</th>
          <th>Files</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($list as $i => $d): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= date('d M Y', strtotime($d['deduction_date'])) ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($d['emp_name']) ?></td>
          <td><?= htmlspecialchars($d['company_name'] ?? '—') ?></td>
          <td class="text-danger fw-bold"><?= Helper::money($d['amount']) ?></td>
          <td><?= htmlspecialchars($d['reason']) ?></td>
          <td class="text-muted small"><?= htmlspecialchars($d['remarks'] ?? '') ?></td>
          <td>
            <?php if ($d['file1']): ?>
              <a href="<?= BASE_URL ?>/uploads/deduction_files/<?= htmlspecialchars($d['file1']) ?>" target="_blank" class="btn btn-xs btn-outline-info btn-sm py-0 px-1"><i class="bi bi-paperclip"></i> F1</a>
            <?php endif; ?>
            <?php if ($d['file2']): ?>
              <a href="<?= BASE_URL ?>/uploads/deduction_files/<?= htmlspecialchars($d['file2']) ?>" target="_blank" class="btn btn-xs btn-outline-info btn-sm py-0 px-1"><i class="bi bi-paperclip"></i> F2</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if(empty($list)): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">No deductions found</td></tr>
      <?php endif; ?>
      </tbody>
      <?php if(!empty($list)): ?>
      <tfoot class="table-light fw-bold">
        <tr>
          <td colspan="4" class="text-end">TOTAL (<?= count($list) ?> records):</td>
          <td class="text-danger"><?= Helper::money(array_sum(array_column($list,'amount'))) ?></td>
          <td colspan="3"></td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>
<?php endif; ?>
