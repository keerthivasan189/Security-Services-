<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Expense Report</h5>
  <div class="d-flex gap-2 no-print">
    <?php $exUrl = BASE_URL.'/index.php?url=reports/expenses&export=excel&start_date='.$start.'&end_date='.$end.'&type='.$type; ?>
    <a href="<?= $exUrl ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="reports/expenses">
      <div class="col-md-2"><label class="form-label small">Type</label>
        <select name="type" class="form-select form-select-sm">
          <option value="all" <?=$type==='all'?'selected':''?>>All Expenses</option>
          <option value="fuel" <?=$type==='fuel'?'selected':''?>>Fuel Only</option>
          <option value="misc" <?=$type==='misc'?'selected':''?>>Misc Only</option>
        </select>
      </div>
      <div class="col-md-2"><label class="form-label small">From</label><input type="date" name="start_date" class="form-control form-control-sm" value="<?=$start?>"></div>
      <div class="col-md-2"><label class="form-label small">To</label><input type="date" name="end_date" class="form-control form-control-sm" value="<?=$end?>"></div>
      <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
        <a href="<?= u('reports/expenses') ?>" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Expenses</div><div class="value text-danger"><?= Helper::money($totalAmt) ?></div></div></div>
  <div class="col-md-4"><div class="stat-card"><div class="label">Records</div><div class="value text-primary"><?= count($list) ?></div></div></div>
  <div class="col-md-4"><div class="stat-card"><div class="label">Period</div><div class="value" style="font-size:16px"><?= date('d M',strtotime($start)) ?> → <?= date('d M Y',strtotime($end)) ?></div></div></div>
</div>

<div class="card">
  <div class="card-body p-0"><table class="table table-hover datatable mb-0">
    <thead class="table-light"><tr><th>#</th><th>Type</th><th>Ref No</th><th>Description</th><th>Biller</th><th>Date</th><th>Amount</th><th>SGST</th><th>CGST</th><th>Total</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($list as $i => $e): ?>
    <tr>
      <td><?=$i+1?></td>
      <td><span class="badge <?=$e['exp_type']==='Fuel'?'bg-warning text-dark':'bg-info text-dark'?>"><?=$e['exp_type']?></span></td>
      <td class="small"><?=htmlspecialchars($e['ref_no'])?></td>
      <td><?=htmlspecialchars($e['description'])?></td>
      <td class="small"><?=htmlspecialchars($e['biller'])?></td>
      <td><?=date('d M Y',strtotime($e['bill_date']))?></td>
      <td><?=Helper::money($e['bill_amount'])?></td>
      <td class="small"><?=number_format($e['sgst'],2)?></td>
      <td class="small"><?=number_format($e['cgst'],2)?></td>
      <td class="fw-bold"><?=Helper::money($e['total'])?></td>
      <td><span class="badge <?=$e['paid_status']==='Paid'?'bg-success':'bg-danger'?>"><?=$e['paid_status']?></span></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
