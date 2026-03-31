<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>CASH FLOW — OFFICE WISE</h5>
  <button onclick="window.print()" class="btn btn-outline-secondary btn-sm no-print"><i class="bi bi-printer me-1"></i>Print</button>
</div>

<!-- Cash Position by Ledger -->
<div class="row g-3 mb-3">
  <?php foreach ($ledgers as $l): ?>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="label"><?= htmlspecialchars($l['account_name']) ?></div>
      <div class="value <?= $l['current_balance']>=0?'text-success':'text-danger' ?>" style="font-size:18px">
        <?= Helper::money($l['current_balance']) ?>
      </div>
      <small class="text-muted">
        <span class="badge bg-<?= $l['account_type']==='Bank'?'primary':($l['account_type']==='Petty Cash'?'warning text-dark':'secondary') ?>"><?= $l['account_type'] ?></span>
      </small>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Detailed Ledger Flow -->
<div class="card mb-3">
  <div class="card-header fw-semibold"><i class="bi bi-arrow-left-right me-1"></i>Debit / Credit Flow Per Account</div>
  <div class="card-body p-0">
    <table class="table table-bordered table-hover mb-0" style="font-size:13px">
      <thead class="table-dark">
        <tr>
          <th>Account</th><th>Type</th><th>Opening Balance</th>
          <th class="text-danger">Total Debit (Money Out)</th>
          <th class="text-success">Total Credit (Money In)</th>
          <th>Net Movement</th><th>Current Balance</th>
          <th class="no-print">View Statement</th>
        </tr>
      </thead>
      <tbody>
      <?php $grandDebit=0; $grandCredit=0; $grandBal=0; $grandOpening=0; ?>
      <?php foreach ($ledgers as $l): 
        $net = $l['total_credit'] - $l['total_debit'];
        $openingBal = $l['current_balance'] - $l['total_credit'] + $l['total_debit'];
        $grandDebit   += $l['total_debit'];
        $grandCredit  += $l['total_credit'];
        $grandBal     += $l['current_balance'];
        $grandOpening += $openingBal;
      ?>
      <tr>
        <td class="fw-semibold">
          <a href="<?= u('accounts/ledgerView/'.$l['id']) ?>"><?= htmlspecialchars($l['account_name']) ?></a>
        </td>
        <td><span class="badge bg-<?= $l['account_type']==='Bank'?'primary':($l['account_type']==='Petty Cash'?'warning text-dark':'secondary') ?>"><?= $l['account_type'] ?></span></td>
        <td><?= Helper::money($openingBal) ?></td>
        <td class="text-danger fw-semibold"><?= Helper::money($l['total_debit']) ?></td>
        <td class="text-success fw-semibold"><?= Helper::money($l['total_credit']) ?></td>
        <td class="fw-bold <?= $net>=0?'text-success':'text-danger' ?>"><?= ($net>=0?'+':'') . Helper::money($net) ?></td>
        <td class="fw-bold <?= $l['current_balance']>=0?'text-success':'text-danger' ?>"><?= Helper::money($l['current_balance']) ?></td>
        <td class="no-print">
          <a href="<?= u('accounts/ledgerView/'.$l['id']) ?>" class="btn btn-xs btn-outline-primary py-0 px-2"><i class="bi bi-eye me-1"></i>View</a>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot class="table-secondary fw-bold">
        <tr>
          <td colspan="2">GRAND TOTAL</td>
          <td><?= Helper::money($grandOpening) ?></td>
          <td class="text-danger"><?= Helper::money($grandDebit) ?></td>
          <td class="text-success"><?= Helper::money($grandCredit) ?></td>
          <td class="<?= ($grandCredit-$grandDebit)>=0?'text-success':'text-danger' ?>"><?= ($grandCredit-$grandDebit>=0?'+':'').Helper::money($grandCredit-$grandDebit) ?></td>
          <td class="<?= $grandBal>=0?'text-success':'text-danger' ?>"><?= Helper::money($grandBal) ?></td>
          <td class="no-print"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<!-- Monthly Cash Flow -->
<div class="card">
  <div class="card-header fw-semibold"><i class="bi bi-bar-chart me-1"></i>Monthly Flow (Last 6 Months)</div>
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr><th>Month</th><th>Total Amount</th><th>Transactions</th><th>Visual</th></tr>
      </thead>
      <tbody>
      <?php $maxAmt = max(array_column($monthlyFlow, 'total') ?: [1]); ?>
      <?php foreach ($monthlyFlow as $m): ?>
      <tr>
        <td class="fw-semibold"><?= date('F Y', strtotime($m['month'].'-01')) ?></td>
        <td class="fw-bold text-primary"><?= Helper::money($m['total']) ?></td>
        <td><?= $m['txn_count'] ?> entries</td>
        <td style="width:40%">
          <div class="progress" style="height:20px">
            <div class="progress-bar bg-primary" style="width:<?= round(($m['total']/$maxAmt)*100) ?>%"><?= Helper::money($m['total']) ?></div>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($monthlyFlow)): ?><tr><td colspan="4" class="text-center text-muted py-3">No data</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
