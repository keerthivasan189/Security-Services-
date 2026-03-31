<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>STATEMENT OF ACCOUNTS</h5>
  <div class="d-flex gap-2 no-print">
    <a href="<?= u('accounts/add') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Add Transaction</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<!-- ─── Cascading Filter Panel ──────────────────────────────── -->
<div class="card mb-3 no-print">
  <div class="card-header py-2 fw-semibold small">
    <i class="bi bi-funnel me-1"></i>USE THESE FILTERS TO GET YOUR EXACT LIST
    <span class="badge bg-info text-dark ms-2" style="font-size:10px"><i class="bi bi-magic"></i> Filters update each other automatically</span>
  </div>
  <div class="card-body py-2">
    <form method="GET" action="<?= BASE_URL ?>/index.php" id="stmtFilterForm">
      <input type="hidden" name="url" value="accounts/statements">
      <div class="row g-2 align-items-end" id="filterRow1">
        <div class="col-md-3">
          <label class="form-label small fw-semibold text-primary"><i class="bi bi-building me-1"></i>Client</label>
          <select name="client_id" id="f_client" class="form-select form-select-sm">
            <option value="">— All Clients —</option>
            <?php foreach ($clients as $c): ?>
            <option value="<?=$c['id']?>" <?=Helper::get('client_id')==$c['id']?'selected':''?>><?=htmlspecialchars($c['company_name'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-semibold text-success"><i class="bi bi-person me-1"></i>Employee</label>
          <select name="employee_id" id="f_employee" class="form-select form-select-sm">
            <option value="">— All Employees —</option>
            <?php foreach ($employees as $e): ?>
            <option value="<?=$e['id']?>" <?=Helper::get('employee_id')==$e['id']?'selected':''?>><?=htmlspecialchars($e['name'])?> (<?=$e['emp_code']?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold" style="color:#b07800"><i class="bi bi-truck me-1"></i>Vendor</label>
          <select name="vendor_id" id="f_vendor" class="form-select form-select-sm">
            <option value="">— All Vendors —</option>
            <?php foreach ($vendors as $v): ?>
            <option value="<?=$v['id']?>" <?=Helper::get('vendor_id')==$v['id']?'selected':''?>><?=htmlspecialchars($v['vendor_name'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold"><i class="bi bi-bank me-1"></i>Account/Ledger</label>
          <select name="ledger_id" id="f_ledger" class="form-select form-select-sm">
            <option value="">— All Accounts —</option>
            <?php foreach ($ledgers as $l): ?>
            <option value="<?=$l['id']?>" <?=Helper::get('ledger_id')==$l['id']?'selected':''?>><?=htmlspecialchars($l['account_name'])?> (<?=$l['account_type']?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">Credit/Debit</label>
          <select name="credit_debit" id="f_crdr" class="form-select form-select-sm">
            <option value="">— All —</option>
            <option value="debit"  <?=Helper::get('credit_debit')==='debit' ?'selected':''?>>Debit (Out)</option>
            <option value="credit" <?=Helper::get('credit_debit')==='credit'?'selected':''?>>Credit (In)</option>
          </select>
        </div>
      </div>
      <div class="row g-2 align-items-end mt-1">
        <div class="col-md-2">
          <label class="form-label small">Emp Code</label>
          <input name="emp_code" class="form-control form-control-sm" placeholder="Emp ID..." value="<?= htmlspecialchars(Helper::get('emp_code','')) ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Start Date</label>
          <input type="date" name="start_date" class="form-control form-control-sm" value="<?= Helper::get('start_date') ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label small">End Date</label>
          <input type="date" name="end_date" class="form-control form-control-sm" value="<?= Helper::get('end_date') ?>">
        </div>
        <div class="col-auto ms-auto d-flex align-items-end gap-2">
          <span id="filterSpinner" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1"></span>Updating filters…</span>
          <button class="btn btn-success btn-sm"><i class="bi bi-search me-1"></i>SEARCH</button>
          <a href="<?= u('accounts/statements') ?>" class="btn btn-outline-secondary btn-sm">Clear All</a>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Summary Cards -->
<?php
  $totalDebit = 0; $totalCredit = 0;
  foreach($transactions as $t) {
    if ($ledId) { $totalDebit += $t['dr_amount'] ?? 0; $totalCredit += $t['cr_amount'] ?? 0; }
  }
  $totalAmt = array_sum(array_column($transactions,'amount'));
?>
<div class="row g-3 mb-3">
  <?php if ($ledId): ?>
  <div class="col-md-3"><div class="stat-card"><div class="label">Total Debit (Out)</div><div class="value text-danger"><?= Helper::money($totalDebit) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Total Credit (In)</div><div class="value text-success"><?= Helper::money($totalCredit) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Net Balance</div><div class="value <?= ($totalCredit-$totalDebit)>=0?'text-success':'text-danger' ?>"><?= Helper::money($totalCredit-$totalDebit) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Transactions</div><div class="value text-primary"><?= count($transactions) ?></div></div></div>
  <?php else: ?>
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Transactions</div><div class="value text-primary"><?= count($transactions) ?></div></div></div>
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Amount</div><div class="value text-success"><?= Helper::money($totalAmt) ?></div></div></div>
  <div class="col-md-4"><div class="stat-card"><div class="label">Period</div><div class="value" style="font-size:14px"><?= Helper::get('start_date') ? date('d M',strtotime(Helper::get('start_date'))) . ' → ' . date('d M Y',strtotime(Helper::get('end_date',date('Y-m-d')))) : 'All Time' ?></div></div></div>
  <?php endif; ?>
</div>

<!-- Transaction Table -->
<div class="card">
  <div class="card-body p-0" style="overflow-x:auto">
    <table class="table table-hover <?= $ledId ? '' : 'datatable' ?> mb-0" style="font-size:12px">
      <thead class="table-light">
        <tr>
          <th>Date</th><th>Description</th><th>Related To</th>
          <?php if ($ledId): ?>
            <th>Particulars</th>
            <th class="text-danger">Debit</th>
            <th class="text-success">Credit</th>
            <th>Running Balance</th>
          <?php else: ?>
            <th>Debit Account</th><th>Credit Account</th><th>Amount</th>
          <?php endif; ?>
          <th>Type</th><th>By</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($transactions as $t): ?>
      <tr>
        <td class="text-nowrap"><?= date('d M Y', strtotime($t['txn_date'])) ?></td>
        <td><?= htmlspecialchars($t['description']) ?></td>
        <td class="small">
          <?php if (!empty($t['client_name'])): ?>
          <span class="badge bg-primary bg-opacity-10 text-primary"><i class="bi bi-building"></i> <?= htmlspecialchars($t['client_name']) ?></span>
          <?php endif; ?>
          <?php if (!empty($t['emp_name'])): ?>
          <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-person"></i> <?= htmlspecialchars($t['emp_name']) ?><?= !empty($t['emp_code_val']) ? ' ('.$t['emp_code_val'].')' : '' ?></span>
          <?php endif; ?>
          <?php if (!empty($t['vendor_name'])): ?>
          <span class="badge bg-warning bg-opacity-10 text-dark"><i class="bi bi-truck"></i> <?= htmlspecialchars($t['vendor_name']) ?></span>
          <?php endif; ?>
          <?php if (empty($t['client_name']) && empty($t['emp_name']) && empty($t['vendor_name'])): ?>—<?php endif; ?>
        </td>
        <?php if ($ledId): ?>
          <td class="small text-muted">
            <?php if (($t['dr_amount'] ?? 0) > 0): ?>To: <?= htmlspecialchars($t['credit_acct'] ?? '—') ?>
            <?php else: ?>From: <?= htmlspecialchars($t['debit_acct'] ?? '—') ?><?php endif; ?>
          </td>
          <td class="text-danger fw-semibold"><?= ($t['dr_amount'] ?? 0) > 0 ? Helper::money($t['dr_amount']) : '' ?></td>
          <td class="text-success fw-semibold"><?= ($t['cr_amount'] ?? 0) > 0 ? Helper::money($t['cr_amount']) : '' ?></td>
          <td class="fw-bold <?= ($t['running_balance'] ?? 0) >= 0 ? 'text-primary' : 'text-danger' ?>"><?= Helper::money($t['running_balance'] ?? 0) ?></td>
        <?php else: ?>
          <td><span class="text-danger small"><?= htmlspecialchars($t['debit_acct'] ?? '—') ?></span></td>
          <td><span class="text-success small"><?= htmlspecialchars($t['credit_acct'] ?? '—') ?></span></td>
          <td class="fw-semibold"><?= Helper::money($t['amount']) ?></td>
        <?php endif; ?>
        <td><span class="badge bg-light text-dark" style="font-size:10px"><?= $t['txn_type'] ?? 'general' ?></span></td>
        <td class="text-muted small"><?= htmlspecialchars($t['created_by_name'] ?? '—') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($transactions)): ?>
      <tr><td colspan="<?= $ledId ? 9 : 8 ?>" class="text-center text-muted py-4">
        <i class="bi bi-inbox" style="font-size:24px"></i><br>No transactions found
      </td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ─── Cascading Filter JavaScript ─────────────────────────── -->
<script>
(function() {
  const BASE = '<?= BASE_URL ?>/index.php?url=accounts/filterOptions';

  // Current selected values
  function getSelections() {
    return {
      client_id:   document.getElementById('f_client').value,
      employee_id: document.getElementById('f_employee').value,
      vendor_id:   document.getElementById('f_vendor').value,
      ledger_id:   document.getElementById('f_ledger').value,
    };
  }

  // Rebuild a <select> from JSON data, preserving current selection
  function rebuildSelect(selectEl, items, idKey, labelFn, currentVal) {
    const blank = selectEl.options[0]; // keep "— All —" option
    selectEl.innerHTML = '';
    selectEl.appendChild(blank);
    items.forEach(function(item) {
      const opt = document.createElement('option');
      opt.value = item[idKey];
      opt.text  = labelFn(item);
      if (String(item[idKey]) === String(currentVal)) opt.selected = true;
      selectEl.appendChild(opt);
    });
  }

  function fetchAndUpdate(changedKey) {
    const sel = getSelections();
    const spinner = document.getElementById('filterSpinner');
    spinner.classList.remove('d-none');

    const qs = Object.entries(sel).map(([k,v]) => v ? `${k}=${encodeURIComponent(v)}` : '').filter(Boolean).join('&');
    fetch(BASE + (qs ? '&' + qs : ''))
      .then(function(r) { return r.json(); })
      .then(function(data) {
        // Rebuild each dropdown EXCEPT the one that was just changed
        if (changedKey !== 'client_id')
          rebuildSelect(document.getElementById('f_client'),   data.clients,   'id', function(c){ return c.company_name; }, sel.client_id);
        if (changedKey !== 'employee_id')
          rebuildSelect(document.getElementById('f_employee'), data.employees, 'id', function(e){ return e.name + ' (' + e.emp_code + ')'; }, sel.employee_id);
        if (changedKey !== 'vendor_id')
          rebuildSelect(document.getElementById('f_vendor'),   data.vendors,   'id', function(v){ return v.vendor_name; }, sel.vendor_id);
        if (changedKey !== 'ledger_id')
          rebuildSelect(document.getElementById('f_ledger'),   data.ledgers,   'id', function(l){ return l.account_name + ' (' + l.account_type + ')'; }, sel.ledger_id);
      })
      .catch(function(err){ console.warn('Filter fetch error', err); })
      .finally(function(){ spinner.classList.add('d-none'); });
  }

  // Attach change listeners
  ['f_client','f_employee','f_vendor','f_ledger'].forEach(function(id) {
    const keyMap = { f_client:'client_id', f_employee:'employee_id', f_vendor:'vendor_id', f_ledger:'ledger_id' };
    document.getElementById(id).addEventListener('change', function() {
      fetchAndUpdate(keyMap[id]);
    });
  });

  // On page load, if any filter is pre-selected, trigger one refresh
  (function init() {
    const sel = getSelections();
    if (sel.client_id || sel.employee_id || sel.vendor_id || sel.ledger_id) {
      fetchAndUpdate(null);
    }
  })();
})();
</script>
