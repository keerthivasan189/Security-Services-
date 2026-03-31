<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-speedometer me-2"></i>FINANCIAL DASHBOARD</h5>
  <div class="d-flex gap-2 no-print">
    <a href="<?= u('accounts/add') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Transaction</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-3">
  <div class="col-md-3">
    <div class="stat-card">
      <div class="label">TOTAL CASH ON HAND</div>
      <div class="value <?= $totalBalance >= 0 ? 'text-success' : 'text-danger' ?>">
        <?= Helper::money(abs($totalBalance)) ?>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="label">TODAY'S TRANSACTIONS</div>
      <div class="value text-primary"><?= Helper::money($todayTotal) ?></div>
      <small class="text-muted"><?= count($todayTxns) ?> entries</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="label">THIS MONTH</div>
      <div class="value text-info"><?= Helper::money($monthTotal) ?></div>
      <small class="text-muted"><?= date('F Y') ?></small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="label">TOTAL RECORDS</div>
      <div class="value text-secondary"><?= number_format($txnCount) ?></div>
      <small class="text-muted">All time</small>
    </div>
  </div>
</div>

<!-- Cash Flow by Ledger / Office -->
<div class="row g-3 mb-3">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-wallet2 me-1"></i>Cash On Hand — Office / Account Wise</div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0" style="font-size:13px">
          <thead class="table-light">
            <tr>
              <th>Account Name</th>
              <th>Type</th>
              <th>Opening Balance</th>
              <th class="text-danger">Total Debit (Money Out)</th>
              <th class="text-success">Total Credit (Money In)</th>
              <th>Net Movement</th>
              <th>Current Balance</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($ledgers as $l):
            $openingBal = $l['current_balance'] - $l['total_credit'] + $l['total_debit'];
            $netMovement = $l['total_credit'] - $l['total_debit'];
          ?>
          <tr>
            <td class="fw-semibold">
              <a href="<?= u('accounts/ledgerView/'.$l['id']) ?>"><?= htmlspecialchars($l['account_name']) ?></a>
            </td>
            <td><span class="badge <?= $l['account_type']==='Bank'?'bg-primary':($l['account_type']==='Petty Cash'?'bg-warning text-dark':'bg-secondary') ?>"><?= $l['account_type'] ?></span></td>
            <td><?= Helper::money($openingBal) ?></td>
            <td class="text-danger"><?= Helper::money($l['total_debit']) ?></td>
            <td class="text-success"><?= Helper::money($l['total_credit']) ?></td>
            <td class="fw-bold <?= $netMovement>=0?'text-success':'text-danger' ?>"><?= ($netMovement>=0?'+':'').Helper::money($netMovement) ?></td>
            <td class="fw-bold <?= $l['current_balance']>=0?'text-success':'text-danger' ?>"><?= Helper::money($l['current_balance']) ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
          <tfoot class="table-secondary fw-bold">
            <tr>
              <?php
                $grandDebit  = array_sum(array_column($ledgers,'total_debit'));
                $grandCredit = array_sum(array_column($ledgers,'total_credit'));
                $grandNet    = $grandCredit - $grandDebit;
              ?>
              <td colspan="2">TOTAL</td>
              <td><?= Helper::money($totalBalance - ($grandCredit - $grandDebit)) ?></td>
              <td class="text-danger"><?= Helper::money($grandDebit) ?></td>
              <td class="text-success"><?= Helper::money($grandCredit) ?></td>
              <td class="<?= $grandNet>=0?'text-success':'text-danger' ?>"><?= ($grandNet>=0?'+':'').Helper::money($grandNet) ?></td>
              <td class="<?= $totalBalance>=0?'text-success':'text-danger' ?>"><?= Helper::money($totalBalance) ?></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-clock-history me-1"></i>Today's Entries — <?= date('d M Y') ?></div>
      <div class="card-body p-0">
        <?php if (!empty($todayTxns)): ?>
        <table class="table table-sm mb-0" style="font-size:12px">
          <thead class="table-light"><tr><th>Description</th><th>Debit</th><th>Credit</th><th>Amount</th></tr></thead>
          <tbody>
          <?php foreach ($todayTxns as $t): ?>
          <tr>
            <td><?= htmlspecialchars($t['description']) ?></td>
            <td class="text-muted small"><?= htmlspecialchars($t['debit_acct'] ?? '') ?></td>
            <td class="text-muted small"><?= htmlspecialchars($t['credit_acct'] ?? '') ?></td>
            <td class="fw-semibold"><?= Helper::money($t['amount']) ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div class="text-center text-muted py-4"><i class="bi bi-inbox" style="font-size:24px"></i><br>No entries today</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Recent Transactions -->
<div class="card">
  <div class="card-header fw-semibold d-flex justify-content-between">
    <span><i class="bi bi-list-ul me-1"></i>Recent Transactions</span>
    <a href="<?= u('accounts/statements') ?>" class="btn btn-sm btn-outline-primary">View All Statements →</a>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover mb-0" style="font-size:13px">
      <thead class="table-light">
        <tr><th>Date</th><th>Description</th><th>Debit Account</th><th>Credit Account</th><th>Amount</th><th>By</th></tr>
      </thead>
      <tbody>
      <?php foreach ($recentTxns as $t): ?>
      <tr>
        <td><?= date('d M Y', strtotime($t['txn_date'])) ?></td>
        <td><?= htmlspecialchars($t['description']) ?></td>
        <td><span class="text-danger small"><?= htmlspecialchars($t['debit_acct'] ?? '—') ?></span></td>
        <td><span class="text-success small"><?= htmlspecialchars($t['credit_acct'] ?? '—') ?></span></td>
        <td class="fw-semibold"><?= Helper::money($t['amount']) ?></td>
        <td class="text-muted small"><?= htmlspecialchars($t['created_by_name'] ?? '—') ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
