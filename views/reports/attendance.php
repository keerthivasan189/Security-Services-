<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Attendance Report</h5>
  <div class="d-flex gap-2 no-print">
    <?php if ($clientId): ?>
    <?php $exUrl = BASE_URL.'/index.php?url=reports/attendance&export=excel&client_id='.$clientId.'&start_date='.$start.'&end_date='.$end; ?>
    <a href="<?= $exUrl ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <?php endif; ?>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="reports/attendance">
      <div class="col-md-3">
        <label class="form-label small">Client</label>
        <select name="client_id" class="form-select form-select-sm" required>
          <option value="">— Select Client —</option>
          <?php foreach ($clients as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $clientId==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['company_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2"><label class="form-label small">From</label><input type="date" name="start_date" class="form-control form-control-sm" value="<?= $start ?>"></div>
      <div class="col-md-2"><label class="form-label small">To</label><input type="date" name="end_date" class="form-control form-control-sm" value="<?= $end ?>"></div>
      <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Search</button>
        <a href="<?= u('reports/attendance') ?>" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
  </div>
</div>

<?php if ($clientId && !empty($rows)): ?>
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="stat-card"><div class="label">Employees</div><div class="value text-primary"><?= count($rows) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Total Present</div><div class="value text-success"><?= array_sum(array_column($rows,'P')) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Total Absent</div><div class="value text-danger"><?= array_sum(array_column($rows,'A')) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Days Covered</div><div class="value text-info"><?= count($days) ?></div></div></div>
</div>
<div class="card">
  <div class="card-body p-0" style="overflow-x:auto">
    <table class="table table-bordered table-sm mb-0" style="font-size:11px">
      <thead class="table-light">
        <tr><th>Code</th><th>Name</th><th>Designation</th>
          <?php foreach ($days as $d): ?><th class="text-center" style="min-width:28px"><?= date('d', strtotime($d)) ?></th><?php endforeach; ?>
          <th>P</th><th>A</th><th>OT</th><th>OFF</th></tr>
      </thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['emp_code']) ?></td>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td class="small"><?= htmlspecialchars($r['designation']) ?></td>
        <?php foreach ($days as $d): ?><td class="text-center <?= ($r['dates'][$d]??'')==='A'?'bg-danger bg-opacity-10':''; ?>"><?= $r['dates'][$d] ?? '-' ?></td><?php endforeach; ?>
        <td class="fw-bold text-success"><?= $r['P'] ?></td>
        <td class="text-danger"><?= $r['A'] ?></td>
        <td><?= $r['OT'] ?></td>
        <td><?= $r['OFF'] ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php elseif ($clientId): ?>
<div class="alert alert-info">No attendance data found for the selected criteria.</div>
<?php endif; ?>
