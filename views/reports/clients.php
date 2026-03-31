<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-building me-2"></i>Client Report</h5>
  <div class="d-flex gap-2 no-print">
    <?php $exUrl = BASE_URL.'/index.php?url=reports/clients&export=excel&status='.urlencode($status).'&branch='.urlencode($branch).'&search='.urlencode($search); ?>
    <a href="<?= $exUrl ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="reports/clients">
      <div class="col-md-2"><label class="form-label small">Status</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">All</option>
          <option value="active" <?=$status==='active'?'selected':''?>>Active</option>
          <option value="inactive" <?=$status==='inactive'?'selected':''?>>Inactive</option>
          <option value="pre_client" <?=$status==='pre_client'?'selected':''?>>Pre-Client</option>
        </select>
      </div>
      <div class="col-md-2"><label class="form-label small">Branch</label>
        <select name="branch" class="form-select form-select-sm">
          <option value="">All</option>
          <?php foreach ($branches as $b): ?><option value="<?=$b?>" <?=$branch===$b?'selected':''?>><?=$b?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3"><label class="form-label small">Search</label><input name="search" class="form-control form-control-sm" placeholder="Company or code..." value="<?=$search?>"></div>
      <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
        <a href="<?= u('reports/clients') ?>" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Clients</div><div class="value text-primary"><?= count($list) ?></div></div></div>
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Deployed</div><div class="value text-success"><?= array_sum(array_column($list,'deployed_count')) ?></div></div></div>
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Outstanding</div><div class="value text-danger"><?= Helper::money(array_sum(array_column($list,'outstanding'))) ?></div></div></div>
</div>

<div class="card">
  <div class="card-body p-0"><table class="table table-hover datatable mb-0">
    <thead class="table-light"><tr><th>#</th><th>Code</th><th>Company</th><th>Contact</th><th>Mobile</th><th>Branch</th><th>Schedule</th><th>Deployed</th><th>Outstanding</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($list as $i => $c): ?>
    <tr>
      <td><?=$i+1?></td>
      <td><span class="badge bg-secondary"><?=htmlspecialchars($c['client_code']??'—')?></span></td>
      <td class="fw-semibold"><?=htmlspecialchars($c['company_name'])?></td>
      <td><?=htmlspecialchars($c['contact_person']??'')?></td>
      <td><?=htmlspecialchars($c['mobile']??'')?></td>
      <td class="small"><?=htmlspecialchars($c['branch']??'')?></td>
      <td class="small"><?=htmlspecialchars($c['invoice_schedule']??'')?></td>
      <td class="fw-bold"><?=$c['deployed_count']?></td>
      <td class="text-danger"><?=Helper::money($c['outstanding'])?></td>
      <td><span class="badge <?=$c['status']==='active'?'bg-success':($c['status']==='inactive'?'bg-secondary':'bg-warning text-dark')?>"><?=ucfirst($c['status'])?></span></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
