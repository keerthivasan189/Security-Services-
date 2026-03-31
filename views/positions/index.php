<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Current Positions</h5>
  <a href="<?= u('positions/appoint') ?>" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-circle me-1"></i>Appoint / Transfer
  </a>
</div>

<!-- Filter Panel -->
<div class="card mb-3">
  <div class="card-header py-2 fw-semibold small"><i class="bi bi-funnel me-1"></i>USE THESE FILTERS TO GET YOUR EXACT LIST</div>
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="positions/index">
      <div class="col-md-3"><label class="form-label small">Company:</label>
        <select name="client_id" class="form-select form-select-sm"><option value="">— All —</option>
        <?php foreach ($clList as $c): ?><option value="<?=$c['id']?>" <?=($clientId??'')==$c['id']?'selected':''?>><?=htmlspecialchars($c['company_name'])?></option><?php endforeach; ?>
        </select></div>
      <div class="col-md-2"><label class="form-label small">Designation:</label>
        <select name="designation" class="form-select form-select-sm"><option value="">— All —</option>
        <?php foreach ($designations as $d): ?><option value="<?=htmlspecialchars($d)?>" <?=($desig??'')===$d?'selected':''?>><?=htmlspecialchars($d)?></option><?php endforeach; ?>
        </select></div>
      <div class="col-md-3"><label class="form-label small">Search (Name / Emp Code):</label>
        <input name="search" class="form-control form-control-sm" placeholder="Search..." value="<?=htmlspecialchars($search??'')?>"></div>
      <div class="col-auto">
        <button class="btn btn-success btn-sm"><i class="bi bi-search me-1"></i>SEARCH</button>
        <a href="<?= u('positions/index') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
      </div>
    </form>
  </div>
</div>

<div class="stat-card mb-3"><div class="label">Active Positions</div><div class="value text-primary"><?= count($positions) ?></div></div>

<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover datatable mb-0">
      <thead class="table-light">
        <tr><th>Company</th><th>Trade / Shift</th><th>Employee</th><th>Emp Code</th><th>Appointed</th><th>Actions</th></tr>
      </thead>
      <tbody>
      <?php foreach ($positions as $p): ?>
      <tr>
        <td class="fw-semibold text-primary"><?= htmlspecialchars($p['company_name']) ?></td>
        <td>
          <span class="fw-semibold"><?= htmlspecialchars($p['designation']) ?></span><br>
          <span class="badge bg-light text-dark small"><?= htmlspecialchars($p['shift']) ?></span>
        </td>
        <td><a href="<?= u('employees/profile/' . $p['employee_id']) ?>"><?= htmlspecialchars($p['emp_name']) ?></a></td>
        <td><span class="badge bg-secondary"><?= htmlspecialchars($p['emp_code']) ?></span></td>
        <td><?= $p['appointed_date'] ? date('d M Y', strtotime($p['appointed_date'])) : '—' ?></td>
        <td>
          <a href="<?= u('positions/relieve/' . $p['id']) ?>"
            class="btn btn-xs btn-outline-danger py-0 px-2"
            onclick="return confirm('Relieve this employee from position?')"
            title="Relieve"><i class="bi bi-person-dash"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($positions)): ?>
      <tr><td colspan="6" class="text-center text-muted py-4">No active positions found</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
