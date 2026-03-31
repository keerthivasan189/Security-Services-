<?php $netBalance = $totalCredit - $totalDebit; ?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <div>
    <nav aria-label="breadcrumb" class="mb-1">
      <ol class="breadcrumb mb-0" style="font-size:12px">
        <li class="breadcrumb-item"><a href="<?= u('accounts/ledgers') ?>">Ledger Accounts</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($ledger['account_name']) ?></li>
      </ol>
    </nav>
    <h5 class="mb-0"><i class="bi bi-bank me-2"></i><?= htmlspecialchars($ledger['account_name']) ?> — Account Statement</h5>
  </div>
  <div class="d-flex gap-2 no-print">
    <a href="<?= u('accounts/add') ?>?credit_ledger_id=<?= $ledger['id'] ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Entry</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
    <a href="<?= u('accounts/ledgers') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
  </div>
</div>

<!-- Account Info Card -->
<div class="row g-3 mb-3">
  <div class="col-md-3">
    <div class="stat-card border-start border-4 border-primary">
      <div class="label">Account Type</div>
      <div class="value text-primary" style="font-size:18px"><?= htmlspecialchars($ledger['account_type']) ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card border-start border-4 border-danger">
      <div class="label">Total Debit (Out)</div>
      <div class="value text-danger"><?= Helper::money($totalDebit) ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card border-start border-4 border-success">
      <div class="label">Total Credit (In)</div>
      <div class="value text-success"><?= Helper::money($totalCredit) ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card border-start border-4 <?= $ledger['current_balance'] >= 0 ? 'border-info' : 'border-warning' ?>">
      <div class="label">Current Balance</div>
      <div class="value <?= $ledger['current_balance'] >= 0 ? 'text-info' : 'text-warning' ?>"><?= Helper::money($ledger['current_balance']) ?></div>
    </div>
  </div>
</div>

<!-- Date Filter & Monthly Summary side by side -->
<div class="row g-3 mb-3 no-print">
  <!-- Date Filter -->
  <div class="col-md-5">
    <div class="card h-100">
      <div class="card-header py-2 fw-semibold small"><i class="bi bi-funnel me-1"></i>Filter by Date</div>
      <div class="card-body py-2">
        <form method="GET" action="<?= BASE_URL ?>/index.php" class="row g-2 align-items-end">
          <input type="hidden" name="url" value="accounts/ledgerView/<?= $ledger['id'] ?>">
          <div class="col-5">
            <label class="form-label small">From</label>
            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $from ?? '' ?>">
          </div>
          <div class="col-5">
            <label class="form-label small">To</label>
            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $to ?? '' ?>">
          </div>
          <div class="col-2">
            <button class="btn btn-success btn-sm w-100"><i class="bi bi-search"></i></button>
          </div>
          <?php if ($from || $to): ?>
          <div class="col-12">
            <a href="<?= u('accounts/ledgerView/'.$ledger['id']) ?>" class="btn btn-outline-secondary btn-sm btn-xs">Clear Filter</a>
            <span class="text-muted small ms-2">Showing: <?= $from ? date('d M Y', strtotime($from)) : '—' ?> to <?= $to ? date('d M Y', strtotime($to)) : 'Today' ?></span>
          </div>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <!-- Monthly Summary -->
  <div class="col-md-7">
    <div class="card h-100">
      <div class="card-header py-2 fw-semibold small"><i class="bi bi-bar-chart me-1"></i>Monthly Summary (Last 12 Months)</div>
      <div class="card-body p-0" style="overflow-x:auto">
        <?php if (!empty($monthly)): ?>
        <table class="table table-sm mb-0" style="font-size:11px">
          <thead class="table-light">
            <tr><th>Month</th><th class="text-danger">Debit (Out)</th><th class="text-success">Credit (In)</th><th>Net</th><th>Entries</th></tr>
          </thead>
          <tbody>
          <?php foreach ($monthly as $m): $net = $m['credit'] - $m['debit']; ?>
          <tr>
            <td class="fw-semibold"><?= $m['mon'] ?></td>
            <td class="text-danger"><?= Helper::money($m['debit']) ?></td>
            <td class="text-success"><?= Helper::money($m['credit']) ?></td>
            <td class="fw-bold <?= $net >= 0 ? 'text-primary' : 'text-danger' ?>"><?= Helper::money($net) ?></td>
            <td><span class="badge bg-light text-dark"><?= $m['cnt'] ?></span></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div class="text-center text-muted py-3 small">No transactions in last 12 months</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Transaction Table -->
<div class="card">
  <div class="card-header py-2 d-flex justify-content-between align-items-center">
    <span class="fw-semibold small"><i class="bi bi-list-ul me-1"></i>All Transactions — <?= count($transactions) ?> entries</span>
    <?php if ($from || $to): ?>
    <span class="badge bg-info text-dark" style="font-size:10px">Filtered: <?= $from ? date('d M',strtotime($from)) : '—' ?> to <?= $to ? date('d M Y',strtotime($to)) : 'Today' ?></span>
    <?php endif; ?>
  </div>
  <div class="card-body p-0" style="overflow-x:auto">
    <table class="table table-hover datatable mb-0" style="font-size:12px">
      <thead class="table-light">
        <tr>
          <th>Date</th>
          <th>Description</th>
          <th>Linked To</th>
          <th>Particulars</th>
          <th class="text-danger">Debit (Out)</th>
          <th class="text-success">Credit (In)</th>
          <th>Balance</th>
          <th>Type</th>
          <th>By</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($transactions as $t): ?>
      <tr>
        <td class="text-nowrap fw-semibold"><?= date('d M Y', strtotime($t['txn_date'])) ?></td>
        <td><?= htmlspecialchars($t['description']) ?></td>
        <td class="small">
          <?php if (!empty($t['client_name'])): ?>
          <span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building"></i> <?= htmlspecialchars($t['client_name']) ?></span><br>
          <?php endif; ?>
          <?php if (!empty($t['emp_name'])): ?>
          <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-person"></i> <?= htmlspecialchars($t['emp_name']) ?> (<?= $t['emp_code_val'] ?>)</span><br>
          <?php endif; ?>
          <?php if (!empty($t['vendor_name'])): ?>
          <span class="badge bg-warning bg-opacity-10 text-dark"><i class="bi bi-truck"></i> <?= htmlspecialchars($t['vendor_name']) ?></span>
          <?php endif; ?>
          <?php if (empty($t['client_name']) && empty($t['emp_name']) && empty($t['vendor_name'])): ?>—<?php endif; ?>
        </td>
        <td class="small text-muted">
          <?php if (($t['dr_amount'] ?? 0) > 0): ?>
            <span class="text-danger"><i class="bi bi-arrow-up-right"></i></span> To: <?= htmlspecialchars($t['credit_acct'] ?? '—') ?>
          <?php else: ?>
            <span class="text-success"><i class="bi bi-arrow-down-left"></i></span> From: <?= htmlspecialchars($t['debit_acct'] ?? '—') ?>
          <?php endif; ?>
        </td>
        <td class="text-danger fw-semibold"><?= ($t['dr_amount'] ?? 0) > 0 ? Helper::money($t['dr_amount']) : '' ?></td>
        <td class="text-success fw-semibold"><?= ($t['cr_amount'] ?? 0) > 0 ? Helper::money($t['cr_amount']) : '' ?></td>
        <td class="fw-bold <?= ($t['running_balance'] ?? 0) >= 0 ? 'text-primary' : 'text-danger' ?>">
          <?= Helper::money($t['running_balance'] ?? 0) ?>
        </td>
        <td><span class="badge bg-light text-dark" style="font-size:10px"><?= $t['txn_type'] ?? 'general' ?></span></td>
        <td class="text-muted small"><?= htmlspecialchars($t['created_by_name'] ?? '—') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($transactions)): ?>
      <tr><td colspan="9" class="text-center text-muted py-5">
        <i class="bi bi-inbox" style="font-size:28px"></i><br>No transactions for this account<?= ($from||$to) ? ' in the selected period' : '' ?>
      </td></tr>
      <?php endif; ?>
      </tbody>
      <?php if (!empty($transactions)): ?>
      <tfoot class="table-secondary fw-bold" style="font-size:12px">
        <tr>
          <td colspan="4">TOTAL</td>
          <td class="text-danger"><?= Helper::money($totalDebit) ?></td>
          <td class="text-success"><?= Helper::money($totalCredit) ?></td>
          <td class="<?= $ledger['current_balance'] >= 0 ? 'text-primary' : 'text-danger' ?>"><?= Helper::money($ledger['current_balance']) ?></td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>
