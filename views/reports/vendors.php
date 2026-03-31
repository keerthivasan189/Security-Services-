<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Vendor Report</h5>
  <div class="d-flex gap-2 no-print">
    <?php $exUrl = BASE_URL.'/index.php?url=reports/vendors&export=excel&search='.urlencode($search); ?>
    <a href="<?= $exUrl ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="reports/vendors">
      <div class="col-md-3"><label class="form-label small">Search Vendor</label><input name="search" class="form-control form-control-sm" placeholder="Vendor name..." value="<?=$search?>"></div>
      <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
        <a href="<?= u('reports/vendors') ?>" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Vendors</div><div class="value text-primary"><?= count($list) ?></div></div></div>
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Items</div><div class="value text-success"><?= array_sum(array_column($list,'item_count')) ?></div></div></div>
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Supplied</div><div class="value text-warning"><?= Helper::money(array_sum(array_column($list,'total_supplied'))) ?></div></div></div>
</div>

<div class="card">
  <div class="card-body p-0"><table class="table table-hover datatable mb-0">
    <thead class="table-light"><tr><th>#</th><th>Vendor Name</th><th>Contact</th><th>Mobile</th><th>Address</th><th>Items</th><th>Total Supplied</th></tr></thead>
    <tbody>
    <?php foreach ($list as $i => $v): ?>
    <tr>
      <td><?=$i+1?></td>
      <td class="fw-semibold"><?=htmlspecialchars($v['vendor_name'])?></td>
      <td><?=htmlspecialchars($v['contact_name']??'—')?></td>
      <td><?=htmlspecialchars($v['mobile']??'—')?></td>
      <td class="small text-muted"><?=htmlspecialchars($v['address']??'—')?></td>
      <td><span class="badge bg-primary rounded-pill"><?=$v['item_count']?></span></td>
      <td class="fw-bold text-success"><?=Helper::money($v['total_supplied'])?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
