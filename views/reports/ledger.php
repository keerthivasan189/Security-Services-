<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Ledger Statement</h5>
  <div class="d-flex gap-2 no-print">
    <?php if ($ledgerId): ?>
    <?php $exUrl = BASE_URL.'/index.php?url=reports/ledger&export=excel&ledger_id='.$ledgerId.'&start_date='.urlencode($start??'').'&end_date='.urlencode($end??''); ?>
    <a href="<?= $exUrl ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <?php endif; ?>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="reports/ledger">
      <div class="col-md-4"><label class="form-label small">Account/Ledger</label>
        <select name="ledger_id" class="form-select form-select-sm" required>
          <option value="">— Select Ledger —</option>
          <?php foreach ($ledgers as $l): ?><option value="<?= $l['id'] ?>" <?=$ledgerId==$l['id']?'selected':''?>><?= htmlspecialchars($l['account_name']) ?> (<?= $l['account_type'] ?>)</option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2"><label class="form-label small">From</label><input type="date" name="start_date" class="form-control form-control-sm" value="<?= $start ?>"></div>
      <div class="col-md-2"><label class="form-label small">To</label><input type="date" name="end_date" class="form-control form-control-sm" value="<?= $end ?>"></div>
      <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Search</button>
        <a href="<?= u('reports/ledger') ?>" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
  </div>
</div>

<?php if ($ledgerId && !empty($txns)): ?>
<div class="stat-card mb-3"><div class="label">Transactions Found</div><div class="value text-primary"><?= count($txns) ?></div>
  <small class="text-muted">Total amount: <?= Helper::money(array_sum(array_column($txns,'amount'))) ?></small></div>
<div class="card"><div class="card-body p-0"><table class="table table-hover datatable mb-0">
  <thead class="table-light"><tr><th>Date</th><th>Description</th><th>Debit Account</th><th>Credit Account</th><th>Amount</th></tr></thead>
  <tbody>
  <?php foreach ($txns as $t): ?>
  <tr>
    <td><?= date('d M Y', strtotime($t['txn_date'])) ?></td>
    <td><?= htmlspecialchars($t['description']) ?></td>
    <td><?= htmlspecialchars($t['debit_acct'] ?? '—') ?></td>
    <td><?= htmlspecialchars($t['credit_acct'] ?? '—') ?></td>
    <td class="fw-semibold"><?= Helper::money($t['amount']) ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table></div></div>
<?php elseif ($ledgerId): ?>
<div class="alert alert-info">No transactions found for this ledger.</div>
<?php endif; ?>
