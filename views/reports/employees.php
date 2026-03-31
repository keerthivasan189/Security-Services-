<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-people me-2"></i>Employee Report</h5>
  <div class="d-flex gap-2 no-print">
    <?php $exUrl = BASE_URL.'/index.php?url=reports/employees&export=excel&status='.urlencode($status).'&designation='.urlencode($desig).'&client_id='.urlencode($clientId).'&search='.urlencode($search); ?>
    <a href="<?= $exUrl ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="reports/employees">
      <div class="col-md-2"><label class="form-label small">Status</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">All</option>
          <option value="active" <?=$status==='active'?'selected':''?>>Active</option>
          <option value="inactive" <?=$status==='inactive'?'selected':''?>>Inactive</option>
        </select>
      </div>
      <div class="col-md-2"><label class="form-label small">Designation</label>
        <select name="designation" class="form-select form-select-sm">
          <option value="">All</option>
          <?php foreach ($designations as $d): ?><option value="<?=$d?>" <?=$desig===$d?'selected':''?>><?=$d?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2"><label class="form-label small">Client</label>
        <select name="client_id" class="form-select form-select-sm">
          <option value="">All</option>
          <?php foreach ($clList as $c): ?><option value="<?=$c['id']?>" <?=$clientId==$c['id']?'selected':''?>><?=htmlspecialchars($c['company_name'])?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2"><label class="form-label small">Search</label><input name="search" class="form-control form-control-sm" placeholder="Name, code, mobile..." value="<?=$search?>"></div>
      <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
        <a href="<?= u('reports/employees') ?>" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
  </div>
</div>

<div class="stat-card mb-3"><div class="label">Showing</div><div class="value text-primary"><?= count($list) ?></div><small class="text-muted">employee(s)</small></div>

<div class="card">
  <div class="card-body p-0"><table class="table table-hover datatable mb-0">
    <thead class="table-light"><tr><th>#</th><th>Code</th><th>Name</th><th>Designation</th><th>DOJ</th><th>Mobile</th><th>Bank</th><th>Deployed At</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($list as $i => $e): ?>
    <tr>
      <td><?=$i+1?></td>
      <td><span class="badge bg-secondary"><?=htmlspecialchars($e['emp_code'])?></span></td>
      <td class="fw-semibold"><?=htmlspecialchars($e['name'])?></td>
      <td class="small"><?=htmlspecialchars($e['designation'])?></td>
      <td><?=date('d M Y',strtotime($e['doj']))?></td>
      <td><?=htmlspecialchars($e['mobile']??'')?></td>
      <td class="small"><?=htmlspecialchars($e['bank_name']??'')?></td>
      <td class="small"><?=htmlspecialchars($e['deployed_at']??'Not Deployed')?></td>
      <td><span class="badge <?=$e['status']==='active'?'bg-success':'bg-secondary'?>"><?=ucfirst($e['status'])?></span></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
