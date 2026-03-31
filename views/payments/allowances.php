<?php /* Other Allowances / Gifts */ ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-gift me-2"></i>Other Allowances / Gifts</h5>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAllowModal">
    <i class="bi bi-plus-lg me-1"></i>Add Other Allowances / Gifts
  </button>
</div>

<!-- Filter Panel -->
<div class="card mb-3">
  <div class="card-body py-2">
    <p class="small fw-semibold text-muted mb-2">USE THIS FILTERS TO GET YOUR EXACT LIST</p>
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="payments/allowances">
      <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Select Company:</label>
        <select name="client_id" class="form-select form-select-sm">
          <option value="">——</option>
          <?php foreach ($clients as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($fClient??'')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['company_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small fw-semibold mb-1">Employee:</label>
        <select name="employee_id" class="form-select form-select-sm">
          <option value="">——</option>
          <?php foreach ($employees as $e): ?>
          <option value="<?= $e['id'] ?>" <?= ($fEmp??'')==$e['id']?'selected':'' ?>><?= htmlspecialchars($e['name'].' ('.$e['emp_code'].')') ?></option>
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
        <a href="<?= u('payments/allowances') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
      </div>
    </form>
  </div>
</div>

<!-- List Table -->
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover datatable mb-0" style="font-size:13px">
      <thead class="table-light">
        <tr><th>Date</th><th>Employee</th><th>Company</th><th>Amount</th><th>Reason</th><th>Remarks</th></tr>
      </thead>
      <tbody>
      <?php foreach ($list as $a): ?>
      <tr>
        <td><?= date('d M Y', strtotime($a['allowance_date'])) ?></td>
        <td class="fw-semibold"><?= htmlspecialchars($a['emp_name']) ?> <small class="text-muted"><?= $a['emp_code'] ?></small></td>
        <td><?= htmlspecialchars($a['company_name']??'—') ?></td>
        <td class="fw-bold"><?= Helper::money($a['amount']) ?></td>
        <td><?= htmlspecialchars($a['reason']) ?></td>
        <td class="text-muted small"><?= htmlspecialchars($a['remarks']??'') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($list)): ?><tr><td colspan="6" class="text-center text-muted py-3">No records found</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addAllowModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-gift me-2"></i>Add Other Allowances / Gifts</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Select Company:</label>
              <select name="client_id" class="form-select" id="allowClientSel">
                <option value="">— Select —</option>
                <?php foreach ($clients as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['company_name']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Employee: <span class="text-danger">*</span></label>
              <select name="employee_id" class="form-select" required>
                <option value="">— Select —</option>
                <?php foreach ($employees as $e): ?><option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name'].' ('.$e['emp_code'].')') ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold small">Date: <span class="text-danger">*</span></label>
              <input type="date" name="allowance_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-2">
              <label class="form-label fw-semibold small">Amount (₹): <span class="text-danger">*</span></label>
              <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>
            <div class="col-md-5">
              <label class="form-label fw-semibold small">Reason: <span class="text-danger">*</span></label>
              <input type="text" name="reason" class="form-control" required>
            </div>
            <div class="col-md-7">
              <label class="form-label fw-semibold small">Remarks, if Any:</label>
              <input type="text" name="remarks" class="form-control">
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
