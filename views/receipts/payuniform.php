<?php if(empty($bill)): ?>
<div class="alert alert-danger">Bill not found. <a href="<?= u('receipts/employeedues') ?>">Go back</a></div>
<?php else: ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-cash me-2"></i>Record Payment — <?= htmlspecialchars($bill['bill_no']) ?></h5>
  <a href="<?= u('receipts/paymentlist_uniform/' . $billId) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="row">
  <div class="col-md-4">
    <div class="card mb-3">
      <div class="card-body">
        <table class="table table-sm mb-0">
          <tr><td class="text-muted small">Bill No</td><td class="fw-bold"><?= htmlspecialchars($bill['bill_no']) ?></td></tr>
          <tr><td class="text-muted small">Employee</td><td class="fw-bold"><?= htmlspecialchars($bill['emp_name']) ?></td></tr>
          <tr><td class="text-muted small">Total Amount</td><td class="fw-bold"><?= Helper::money($bill['total_amount']) ?></td></tr>
          <tr><td class="text-muted small">Paid</td><td class="fw-bold text-success"><?= Helper::money($bill['paid_amount']) ?></td></tr>
          <tr><td class="text-muted small">Balance Due</td><td class="fw-bold text-danger"><?= Helper::money($bill['balance_amount']) ?></td></tr>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">Payment Entry</div>
      <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
          <div class="row g-3">
            <div class="col-md-4"><label class="form-label fw-semibold small">Date <span class="text-danger">*</span></label><input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
            <div class="col-md-4"><label class="form-label fw-semibold small">Amount (₹) <span class="text-danger">*</span></label><input type="number" step="0.01" name="amount" class="form-control" value="<?= $bill['balance_amount'] ?>" required></div>
            <div class="col-md-4"><label class="form-label fw-semibold small">Received Through</label>
              <select name="payment_method" class="form-select">
                <option value="">— Payment Method —</option>
                <option>NEFT</option><option>RTGS</option><option>Cheque</option><option>Cash</option><option>UPI</option>
              </select></div>
            <div class="col-md-4"><label class="form-label fw-semibold small">Received Account</label>
              <select name="credit_ledger_id" class="form-select">
                <option value="">— Select Ledger —</option>
                <?php foreach($ledgers as $l): ?><option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['account_name']) ?></option><?php endforeach; ?>
              </select></div>
            <div class="col-md-4"><label class="form-label fw-semibold small">Ref / Cheque No</label><input type="text" name="ref_no" class="form-control"></div>
            <div class="col-md-4"><label class="form-label fw-semibold small">Cheque / DD Photo</label><input type="file" name="cheque_photo" class="form-control" accept=".jpg,.jpeg,.png,.pdf"></div>
            <div class="col-md-8"><label class="form-label fw-semibold small">Remarks</label><input type="text" name="remarks" class="form-control"></div>
            <div class="col-12"><button type="submit" class="btn btn-success px-4"><i class="bi bi-check-circle me-2"></i>Record Payment</button></div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
