<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-bank me-2"></i>LEDGER ACCOUNTS</h5>
</div>

<!-- Add New Ledger -->
<div class="card mb-3">
  <div class="card-header py-2 fw-semibold"><i class="bi bi-plus-circle me-1"></i>Add New Ledger Account</div>
  <div class="card-body">
    <form method="POST" class="row g-3 align-items-end">
      <input type="hidden" name="action" value="add">
      <div class="col-md-4"><label class="form-label fw-semibold small">Account Name <span class="text-danger">*</span></label><input type="text" name="account_name" class="form-control" required></div>
      <div class="col-md-3"><label class="form-label fw-semibold small">Account Type</label>
        <select name="account_type" class="form-select">
          <option>Bank</option><option>Petty Cash</option><option>Income</option><option>Expense</option><option>Asset</option><option>Liability</option>
        </select></div>
      <div class="col-md-3"><label class="form-label fw-semibold small">Opening Balance (₹)</label><input type="number" step="0.01" name="current_balance" class="form-control" value="0"></div>
      <div class="col-md-2"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus me-1"></i>Add</button></div>
    </form>
  </div>
</div>

<!-- Ledger List -->
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover datatable mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>Account Name</th><th>Type</th><th>Current Balance</th><th>Transactions</th><th class="no-print">Actions</th></tr>
      </thead>
      <tbody>
      <?php foreach($ledgers as $i=>$l): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td class="fw-semibold">
          <a href="<?= u('accounts/statements') ?>?ledger_id=<?= $l['id'] ?>"><?= htmlspecialchars($l['account_name']) ?></a>
        </td>
        <td>
          <span class="badge bg-<?= $l['account_type']==='Bank'?'primary':($l['account_type']==='Petty Cash'?'warning text-dark':($l['account_type']==='Income'?'success':($l['account_type']==='Expense'?'danger':'secondary'))) ?>">
            <?= $l['account_type'] ?>
          </span>
        </td>
        <td class="fw-semibold <?= $l['current_balance']>=0?'text-success':'text-danger' ?>"><?= Helper::money($l['current_balance']) ?></td>
        <td><span class="badge bg-light text-dark"><?= $l['txn_count'] ?? 0 ?> entries</span></td>
        <td class="no-print">
          <a href="<?= u('accounts/ledgerView/'.$l['id']) ?>" class="btn btn-xs btn-outline-primary py-0 px-2" title="View Statement"><i class="bi bi-eye"></i></a>
          <?php if (($l['txn_count'] ?? 0) == 0): ?>
          <form method="POST" style="display:inline" onsubmit="return confirm('Delete this ledger?')">
            <input type="hidden" name="action" value="delete"><input type="hidden" name="ledger_id" value="<?= $l['id'] ?>">
            <button class="btn btn-xs btn-outline-danger py-0 px-2" title="Delete"><i class="bi bi-trash"></i></button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot class="table-secondary fw-bold">
        <tr>
          <td colspan="3">TOTAL BALANCE</td>
          <td class="<?= array_sum(array_column($ledgers,'current_balance'))>=0?'text-success':'text-danger' ?>">
            <?= Helper::money(array_sum(array_column($ledgers,'current_balance'))) ?>
          </td>
          <td><?= array_sum(array_column($ledgers,'txn_count')) ?> entries</td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
