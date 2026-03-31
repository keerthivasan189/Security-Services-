<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>ADD DAILY TRANSACTION</h5>
  <a href="<?= u('accounts/statements') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Statements</a>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header fw-semibold"><i class="bi bi-journal-plus me-1"></i>Record Credit / Debit Entry</div>
      <div class="card-body">
        <form method="POST">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Date <span class="text-danger">*</span></label>
              <input type="date" name="txn_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Transaction Type</label>
              <select name="txn_type" class="form-select">
                <option value="general">General Entry</option>
                <option value="payment">Payment (Money Out)</option>
                <option value="receipt">Receipt (Money In)</option>
                <option value="expense">Expense</option>
                <option value="salary">Salary Payment</option>
                <option value="gst">GST Payment</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small">Amount (₹) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">Description <span class="text-danger">*</span></label>
              <input type="text" name="description" class="form-control" placeholder="e.g. Client payment received / Salary payment / GST payment" required>
            </div>

            <!-- Entity Linking -->
            <div class="col-12"><hr class="my-1"><small class="text-muted fw-semibold">LINK TO (select the related Client / Employee / Vendor for this transaction)</small></div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small text-primary"><i class="bi bi-building"></i> Client</label>
              <select name="client_id" class="form-select form-select-sm">
                <option value="">— None —</option>
                <?php foreach ($clients as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['company_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small text-success"><i class="bi bi-person"></i> Employee</label>
              <select name="employee_id" class="form-select form-select-sm">
                <option value="">— None —</option>
                <?php foreach ($employees as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?> (<?= $e['emp_code'] ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold small text-warning"><i class="bi bi-truck"></i> Vendor</label>
              <select name="vendor_id" class="form-select form-select-sm">
                <option value="">— None —</option>
                <?php foreach ($vendors as $v): ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['vendor_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12"><hr class="my-1"><small class="text-muted fw-semibold">ACCOUNTS (Debit & Credit ledgers)</small></div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small text-danger">Debit Ledger (Money From) <span class="text-danger">*</span></label>
              <select name="debit_ledger_id" class="form-select" required>
                <option value="">— Select Debit Account —</option>
                <?php foreach ($ledgers as $l): ?>
                <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['account_name']) ?> — (<?= $l['account_type'] ?>) — <?= Helper::money($l['current_balance']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small text-success">Credit Ledger (Money To) <span class="text-danger">*</span></label>
              <select name="credit_ledger_id" class="form-select" required>
                <option value="">— Select Credit Account —</option>
                <?php foreach ($ledgers as $l): ?>
                <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['account_name']) ?> — (<?= $l['account_type'] ?>) — <?= Helper::money($l['current_balance']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold small">Remarks</label>
              <input type="text" name="remarks" class="form-control" placeholder="Optional remarks, invoice no., reference...">
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary px-5"><i class="bi bi-save me-2"></i>Save Transaction</button>
              <a href="<?= u('accounts/statements') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card mb-3">
      <div class="card-header fw-semibold small"><i class="bi bi-info-circle me-1"></i>Quick Guide</div>
      <div class="card-body small">
        <div class="mb-2">
          <strong class="text-danger"><i class="bi bi-arrow-up-right"></i> Debit</strong> = Money going OUT<br>
          <span class="text-muted">e.g. Salary paid, expense incurred</span>
        </div>
        <div class="mb-2">
          <strong class="text-success"><i class="bi bi-arrow-down-left"></i> Credit</strong> = Money coming IN<br>
          <span class="text-muted">e.g. Client payment received</span>
        </div>
        <hr>
        <div class="fw-semibold mb-1">Link To:</div>
        <ul class="ps-3 mb-0 small">
          <li><strong>Client</strong> — for client payments/receipts</li>
          <li><strong>Employee</strong> — for salary/advance payments</li>
          <li><strong>Vendor</strong> — for vendor bills/purchases</li>
        </ul>
      </div>
    </div>
    <div class="card">
      <div class="card-header fw-semibold small"><i class="bi bi-wallet2 me-1"></i>Current Balances</div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0" style="font-size:12px">
          <tbody>
          <?php foreach ($ledgers as $l): ?>
          <tr>
            <td class="fw-semibold"><?= htmlspecialchars($l['account_name']) ?></td>
            <td class="text-end fw-bold <?= $l['current_balance']>=0?'text-success':'text-danger' ?>"><?= Helper::money($l['current_balance']) ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
