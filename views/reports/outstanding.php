<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Balance Outstanding Report</h5>
  <div class="d-flex gap-2 no-print">
    <?php $exUrl = BASE_URL.'/index.php?url=reports/outstanding&export=excel&branch='.urlencode($branch??''); ?>
    <a href="<?= $exUrl ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="reports/outstanding">
      <div class="col-md-3"><label class="form-label small">Branch</label>
        <select name="branch" class="form-select form-select-sm"><option value="">All Branches</option>
        <?php foreach ($branches as $b): ?><option value="<?=$b?>" <?=($branch??'')===$b?'selected':''?>><?=$b?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
        <a href="<?= u('reports/outstanding') ?>" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
  </div>
</div>

<div class="stat-card mb-3"><div class="label">Grand Total Outstanding</div><div class="value text-danger"><?= Helper::money($grandTotal) ?></div>
  <small class="text-muted">across <?= count($list) ?> client(s)</small></div>

<div class="card"><div class="card-body p-0"><table class="table table-hover datatable mb-0">
  <thead class="table-light"><tr><th>Code</th><th>Company</th><th>Branch</th><th>Bills</th><th>Billed</th><th>Outstanding</th></tr></thead>
  <tbody>
  <?php foreach ($list as $c): ?>
  <tr>
    <td><span class="badge bg-secondary"><?= htmlspecialchars($c['client_code'] ?? '—') ?></span></td>
    <td class="fw-semibold"><?= htmlspecialchars($c['company_name']) ?></td>
    <td class="small"><?= htmlspecialchars($c['branch'] ?? '—') ?></td>
    <td><?= $c['bill_count'] ?></td>
    <td><?= Helper::money($c['total_billed']) ?></td>
    <td class="fw-bold text-danger"><?= Helper::money($c['total_outstanding']) ?></td>
  </tr>
  <?php endforeach; ?>
  <?php if (empty($list)): ?><tr><td colspan="6" class="text-center text-muted py-4">No outstanding balances</td></tr><?php endif; ?>
  </tbody>
</table></div></div>
