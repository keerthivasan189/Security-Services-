<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Transfer History</h5>
</div>
<div class="card mb-3">
  <div class="card-body py-2">
    <form class="row g-2 align-items-center" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="positions/history">
      <div class="col-md-4">
        <select name="employee_id" class="form-select form-select-sm">
          <option value="">— All Employees —</option>
          <?php foreach ($employees as $e): ?>
          <option value="<?= $e['id'] ?>" <?= $empId==$e['id']?'selected':'' ?>><?= htmlspecialchars($e['name'] . ' (' . $e['emp_code'] . ')') ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto">
        <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Search</button>
      </div>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover datatable mb-0">
      <thead class="table-light">
        <tr><th>Employee</th><th>Company</th><th>Designation</th><th>Shift</th><th>Appointed</th><th>Relieved</th><th>Status</th></tr>
      </thead>
      <tbody>
      <?php foreach ($history as $h): ?>
      <tr>
        <td><?= htmlspecialchars($h['emp_name']) ?></td>
        <td><?= htmlspecialchars($h['company_name']) ?></td>
        <td><?= htmlspecialchars($h['designation']) ?></td>
        <td><?= htmlspecialchars($h['shift']) ?></td>
        <td><?= $h['appointed_date'] ? date('d M Y', strtotime($h['appointed_date'])) : '—' ?></td>
        <td><?= $h['relieved_date'] ? date('d M Y', strtotime($h['relieved_date'])) : '—' ?></td>
        <td><span class="badge <?= $h['status']==='active'?'bg-success':($h['status']==='relieved'?'bg-danger':'bg-warning text-dark') ?>"><?= ucfirst($h['status']) ?></span></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($history)): ?><tr><td colspan="7" class="text-center text-muted py-4">No records found</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
