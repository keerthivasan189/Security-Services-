<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Payments Received Report</h5>
  <div class="d-flex gap-2 no-print">
    <?php $exUrl = BASE_URL.'/index.php?url=reports/payments&export=excel&start_date='.$start.'&end_date='.$end.'&client_id='.urlencode($clientId??'').'&method='.urlencode($method??''); ?>
    <a href="<?= $exUrl ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="reports/payments">
      <div class="col-md-2"><label class="form-label small">From</label><input type="date" name="start_date" class="form-control form-control-sm" value="<?=$start?>"></div>
      <div class="col-md-2"><label class="form-label small">To</label><input type="date" name="end_date" class="form-control form-control-sm" value="<?=$end?>"></div>
      <div class="col-md-3"><label class="form-label small">Client</label>
        <select name="client_id" class="form-select form-select-sm"><option value="">All</option>
        <?php foreach ($clients as $c): ?><option value="<?=$c['id']?>" <?=$clientId==$c['id']?'selected':''?>><?=htmlspecialchars($c['company_name'])?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2"><label class="form-label small">Method</label>
        <select name="method" class="form-select form-select-sm"><option value="">All</option>
        <option value="NEFT" <?=($method??'')==='NEFT'?'selected':''?>>NEFT</option>
        <option value="Cheque" <?=($method??'')==='Cheque'?'selected':''?>>Cheque</option>
        <option value="Cash" <?=($method??'')==='Cash'?'selected':''?>>Cash</option>
        <option value="UPI" <?=($method??'')==='UPI'?'selected':''?>>UPI</option></select>
      </div>
      <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
        <a href="<?= u('reports/payments') ?>" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
  </div>
</div>

<div class="stat-card mb-3"><div class="label">Total Received</div><div class="value text-success"><?= Helper::money($total) ?></div>
  <small class="text-muted"><?= count($list) ?> payment(s)</small></div>

<div class="card"><div class="card-body p-0"><table class="table table-hover datatable mb-0">
  <thead class="table-light"><tr><th>Date</th><th>Invoice</th><th>Company</th><th>Type</th><th>Method</th><th>Ref No</th><th>Amount</th></tr></thead>
  <tbody>
  <?php foreach ($list as $p): ?>
  <tr>
    <td><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
    <td><a href="<?= u('receipts/viewinvoice/'.$p['invoice_id']) ?>"><?= htmlspecialchars($p['invoice_no']) ?></a></td>
    <td><?= htmlspecialchars($p['company_name']) ?></td>
    <td><span class="badge bg-secondary"><?= ucfirst(str_replace('_',' ',$p['payment_type'])) ?></span></td>
    <td><?= htmlspecialchars($p['payment_method'] ?? '—') ?></td>
    <td class="small"><?= htmlspecialchars($p['ref_no'] ?? '—') ?></td>
    <td class="fw-bold"><?= Helper::money($p['amount']) ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table></div></div>
