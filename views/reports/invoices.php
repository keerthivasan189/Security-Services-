<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Invoice Report</h5>
  <div class="d-flex gap-2 no-print">
    <?php $exUrl = BASE_URL.'/index.php?url=reports/invoices&export=excel&month='.urlencode($month??'').'&client_id='.urlencode($clientId??'').'&status='.urlencode($status??''); ?>
    <a href="<?= $exUrl ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="reports/invoices">
      <div class="col-md-2"><label class="form-label small">Month</label><input type="month" name="month" class="form-control form-control-sm" value="<?= $month ?>"></div>
      <div class="col-md-3"><label class="form-label small">Client</label>
        <select name="client_id" class="form-select form-select-sm"><option value="">All</option>
        <?php foreach ($clients as $c): ?><option value="<?=$c['id']?>" <?=$clientId==$c['id']?'selected':''?>><?=htmlspecialchars($c['company_name'])?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2"><label class="form-label small">Status</label>
        <select name="status" class="form-select form-select-sm"><option value="">All</option>
        <option value="paid" <?=($status??'')==='paid'?'selected':''?>>Paid</option>
        <option value="partial" <?=($status??'')==='partial'?'selected':''?>>Partial</option>
        <option value="unpaid" <?=($status??'')==='unpaid'?'selected':''?>>Unpaid</option></select>
      </div>
      <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
        <a href="<?= u('reports/invoices') ?>" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Invoiced</div><div class="value text-primary"><?= Helper::money($totals['amount']) ?></div></div></div>
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Outstanding</div><div class="value text-danger"><?= Helper::money($totals['outstanding']) ?></div></div></div>
  <div class="col-md-4"><div class="stat-card"><div class="label">Invoice Count</div><div class="value text-success"><?= count($list) ?></div></div></div>
</div>

<div class="card"><div class="card-body p-0"><table class="table table-hover datatable mb-0">
  <thead class="table-light"><tr><th>Invoice No</th><th>Company</th><th>Type</th><th>Month</th><th>Amount</th><th>Outstanding</th><th>Status</th></tr></thead>
  <tbody>
  <?php foreach ($list as $inv): ?>
  <tr>
    <td><a href="<?= u('receipts/viewinvoice/'.$inv['id']) ?>"><?= htmlspecialchars($inv['invoice_no']) ?></a></td>
    <td><?= htmlspecialchars($inv['company_name']) ?></td>
    <td><span class="badge <?=$inv['bill_type']==='RCM'?'bg-warning text-dark':'bg-info text-dark'?>"><?= $inv['bill_type'] ?></span></td>
    <td><?= $inv['invoice_month'] ?></td>
    <td><?= Helper::money($inv['grand_total']) ?></td>
    <td class="text-danger"><?= Helper::money($inv['total_outstanding']) ?></td>
    <td><span class="badge badge-<?= $inv['payment_status'] ?>"><?= ucfirst($inv['payment_status']) ?></span></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table></div></div>
