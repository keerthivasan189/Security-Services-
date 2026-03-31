<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Appointment / Transfer / Relieve</h5>
  <a href="<?= u('positions/index') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card" style="max-width:650px">
  <div class="card-header">Assign employee to a client position</div>
  <div class="card-body">
    <form method="POST">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold small">Company <span class="text-danger">*</span></label>
          <select name="client_id" class="form-select" required onchange="loadTrades(this.value)">
            <option value="">— Select Company —</option>
            <?php foreach ($clients as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['company_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold small">Trade with Shift <span class="text-danger">*</span></label>
          <select name="trade_id" class="form-select" id="tradeSel" required>
            <option value="">— Select Trade —</option>
            <?php foreach ($trades as $t): ?>
            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['label']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold small">Employee Name</label>
          <select name="employee_id" class="form-select">
            <option value="">— Select Employee —</option>
            <?php foreach ($employees as $e): ?>
            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name'] . ' (' . $e['emp_code'] . ')') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold small">Appointment Date</label>
          <input type="date" name="appointed_date" class="form-control" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold small">Remarks</label>
          <textarea name="remarks" class="form-control" rows="2" placeholder="Initial Position Added"></textarea>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Save / Update</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function loadTrades(clientId){
  const sel = document.getElementById('tradeSel');
  sel.innerHTML = '<option value="">Loading...</option>';
  fetch('<?= BASE_URL ?>/index.php?url=positions/getTrades&client_id=' + clientId)
    .then(r => r.json())
    .then(data => {
      sel.innerHTML = '<option value="">— Select Trade —</option>';
      data.forEach(t => sel.innerHTML += `<option value="${t.id}">${t.label}</option>`);
    });
}
</script>
