<?php
$statusLabel = fn($s) => match($s) {
    'P' => 'Present', 'A' => 'Absent', 'OT' => 'Overtime',
    'OFF' => 'Off', 'HD' => 'Half Day', default => $s,
};
$statusBadge = fn($s) => match($s) {
    'P'   => 'bg-success',
    'A'   => 'bg-danger',
    'OT'  => 'bg-primary',
    'OFF' => 'bg-secondary',
    'HD'  => 'bg-warning text-dark',
    default => 'bg-light text-dark',
};
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Bulk Attendance Entry</h5>
</div>

<!-- ── Filter Panel (GET form so filters persist) ── -->
<div class="card mb-3">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php" id="filterForm">
      <input type="hidden" name="url" value="attendance/bulk">
      <div class="col-md-3">
        <label class="form-label fw-semibold small mb-1"><i class="bi bi-building me-1"></i>Client / Company</label>
        <select name="client_id" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">— All Clients —</option>
          <?php foreach ($clients as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $selectedClient==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['company_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold small mb-1"><i class="bi bi-person-badge me-1"></i>Designation</label>
        <select name="designation" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">— All —</option>
          <?php foreach ($designations as $des): ?>
          <option value="<?= htmlspecialchars($des) ?>" <?= $selectedDesignation===$des?'selected':'' ?>><?= htmlspecialchars($des) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold small mb-1"><i class="bi bi-person-check me-1"></i>Field Officer</label>
        <select name="field_officer_id" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="">— All —</option>
          <?php foreach ($fieldOfficers as $fo): ?>
          <option value="<?= $fo['id'] ?>" <?= $selectedOfficer==$fo['id']?'selected':'' ?>><?= htmlspecialchars($fo['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold small mb-1"><i class="bi bi-search me-1"></i>Employee Search</label>
        <input type="text" name="emp_search" class="form-control form-control-sm" placeholder="Name or code…" value="<?= htmlspecialchars($empSearch) ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold small mb-1"><i class="bi bi-calendar3 me-1"></i>Date</label>
        <input type="date" name="from_date" class="form-control form-control-sm" value="<?= $selectedDate ?>" onchange="this.form.submit()">
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-filter me-1"></i>Filter</button>
        <a href="<?= u('attendance/bulk') ?>" class="btn btn-outline-secondary btn-sm ms-1">Clear</a>
      </div>
    </form>
  </div>
</div>

<?php if (!empty($employees)): ?>
<form method="POST" action="<?= BASE_URL ?>/index.php?url=attendance/bulk">
  <!-- Preserve all filter state -->
  <input type="hidden" name="url" value="attendance/bulk">

  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
      <span>
        <strong><?= count($employees) ?> employees</strong>
        <?php if ($selectedClient || $selectedDesignation || $selectedOfficer || $empSearch): ?>
        <span class="ms-1 small text-muted">— filtered</span>
        <?php endif; ?>
      </span>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <label class="form-label fw-semibold small mb-0">From:</label>
        <input type="date" name="from_date" class="form-control form-control-sm" value="<?= $selectedDate ?>" style="width:140px">
        <label class="form-label fw-semibold small mb-0">To:</label>
        <input type="date" name="to_date" class="form-control form-control-sm" value="<?= $selectedDate ?>" style="width:140px">
        <button type="button" class="btn btn-sm btn-outline-success" onclick="setAll('P')">All Present</button>
        <button type="button" class="btn btn-sm btn-outline-warning" onclick="setAll('HD')">All Half Day</button>
        <button type="button" class="btn btn-sm btn-outline-danger"  onclick="setAll('A')">All Absent</button>
      </div>
    </div>
    <div class="card-body p-0">
      <table class="table table-hover mb-0" style="font-size:13px">
        <thead class="table-light">
          <tr>
            <th width="30"><input type="checkbox" id="selectAll" checked></th>
            <th>Emp Code</th>
            <th>Name</th>
            <th>Designation</th>
            <th>Client / Shift</th>
            <th width="180">Status</th>
            <th>Today's Mark</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($employees as $i => $emp):
          $curStatus = $emp['att_status'] ?? null;
        ?>
        <tr class="<?= $curStatus ? 'table-warning' : '' ?>">
          <td><input type="checkbox" name="emp_id[]" value="<?= $emp['id'] ?>" class="emp-check" checked></td>
          <td><span class="badge bg-secondary small"><?= htmlspecialchars($emp['emp_code']) ?></span></td>
          <td class="fw-semibold"><?= htmlspecialchars($emp['name']) ?></td>
          <td class="small text-muted"><?= htmlspecialchars($emp['designation'] ?? '—') ?></td>
          <td class="small text-muted">
            <?= htmlspecialchars($emp['client_name'] ?? '—') ?>
            <?= $emp['shift'] ? '<br><span class="badge bg-light text-dark" style="font-size:9px">' . htmlspecialchars($emp['shift']) . '</span>' : '' ?>
          </td>
          <td>
            <input type="hidden" name="trade_id[]" value="<?= $emp['trade_id'] ?? '' ?>">
            <input type="hidden" name="client_id_row[]" value="<?= $emp['client_id'] ?? ($selectedClient ?: '') ?>">
            <select name="status[]" class="form-select form-select-sm status-select">
              <option value="P"   <?= ($curStatus??'P')==='P'   ?'selected':'' ?>>Present</option>
              <option value="HD"  <?= ($curStatus??'')==='HD'   ?'selected':'' ?>>Half Day</option>
              <option value="A"   <?= ($curStatus??'')==='A'    ?'selected':'' ?>>Absent</option>
              <option value="OFF" <?= ($curStatus??'')==='OFF'  ?'selected':'' ?>>Company Off</option>
              <option value="OT"  <?= ($curStatus??'')==='OT'   ?'selected':'' ?>>Overtime</option>
            </select>
          </td>
          <td>
            <?php if ($curStatus): ?>
            <span class="badge <?= $statusBadge($curStatus) ?>" style="font-size:11px">
              <?= $statusLabel($curStatus) ?>
            </span>
            <?php else: ?>
            <span class="text-muted small">Not marked</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="card-footer d-flex gap-2">
      <button type="submit" name="save_attendance" value="1" class="btn btn-primary px-4">
        <i class="bi bi-save me-2"></i>Save Attendance
      </button>
      <small class="text-muted align-self-center">Only checked employees will be saved.</small>
    </div>
  </div>
</form>

<?php elseif (isset($_GET['url'])): ?>
<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No active employees match the selected filters.</div>
<?php else: ?>
<div class="alert alert-secondary"><i class="bi bi-arrow-up-circle me-2"></i>Use the filters above to find employees and mark attendance.</div>
<?php endif; ?>

<script>
document.getElementById('selectAll')?.addEventListener('change', function(){
  document.querySelectorAll('.emp-check').forEach(c => c.checked = this.checked);
});
function setAll(val){
  document.querySelectorAll('.status-select').forEach(s => s.value = val);
}
</script>
