<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-bag me-2"></i>Issue Uniforms</h5>
</div>
<div class="card mb-3">
  <div class="card-header">Uniform Sale Bill</div>
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="add">
      <div class="row g-3">
        <div class="col-md-3"><label class="form-label fw-semibold small">Date</label><input type="date" name="bill_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
        <div class="col-md-4"><label class="form-label fw-semibold small">Employee <span class="text-danger">*</span></label>
          <select name="employee_id" class="form-select" required>
            <option value="">— Select Employee —</option>
            <?php foreach($employees as $e): ?><option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name'].' ('.$e['emp_code'].')') ?></option><?php endforeach; ?>
          </select></div>
        <div class="col-md-4"><label class="form-label fw-semibold small">Company</label>
          <select name="client_id" class="form-select">
            <option value="">— Select —</option>
            <?php foreach($clients as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['company_name']) ?></option><?php endforeach; ?>
          </select></div>
      </div>
      <hr class="my-3">
      <h6 class="fw-semibold small mb-2">Uniform Items</h6>
      <table class="table table-bordered table-sm" id="uniTable">
        <thead class="table-light"><tr><th>Item</th><th>Qty</th><th>Unit Price</th><th>Total</th><th></th></tr></thead>
        <tbody id="uniBody">
          <tr>
            <td><select name="item_id[]" class="form-select form-select-sm" style="min-width:200px" onchange="setPrice(this)">
              <option value="">— Select Item —</option>
              <?php foreach($items as $it): ?><option value="<?= $it['id'] ?>" data-price="<?= $it['unit_price'] ?>"><?= htmlspecialchars($it['item_name'].' ('.$it['vendor_name'].')') ?></option><?php endforeach; ?>
            </select></td>
            <td><input type="number" name="qty[]" class="form-control form-control-sm" value="1" min="1" style="width:70px" oninput="calcUniRow(this)"></td>
            <td><input type="number" step="0.01" name="unit_price[]" class="form-control form-control-sm price" style="width:90px" oninput="calcUniRow(this)"></td>
            <td><input type="number" step="0.01" name="item_total[]" class="form-control form-control-sm item-total" style="width:90px" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm py-0" onclick="this.closest('tr').remove();calcUniTotals()">×</button></td>
          </tr>
        </tbody>
      </table>
      <button type="button" class="btn btn-success btn-sm mb-3" onclick="addUniRow()"><i class="bi bi-plus me-1"></i>Add Item</button>
      <div class="row g-3">
        <div class="col-md-2"><label class="form-label fw-semibold small">Sub-Total</label><input type="number" step="0.01" name="subtotal" id="uniSub" class="form-control" readonly></div>
        <div class="col-md-2"><label class="form-label fw-semibold small">Discount</label><input type="number" step="0.01" name="discount" class="form-control" value="0" oninput="calcUniTotals()"></div>
        <div class="col-md-2"><label class="form-label fw-semibold small">Total Amount</label><input type="number" step="0.01" name="total_amount" id="uniTotal" class="form-control" readonly></div>
        <div class="col-md-2"><label class="form-label fw-semibold small">Paid Amount</label><input type="number" step="0.01" name="paid_amount" class="form-control" value="0" id="paidAmt" oninput="calcUniBalance()"></div>
        <div class="col-md-2"><label class="form-label fw-semibold small">Balance</label><input type="number" step="0.01" name="balance_amount" id="uniBalance" class="form-control" readonly></div>
        <div class="col-md-2"><label class="form-label fw-semibold small">No. of Dues</label><input type="number" name="no_of_dues" class="form-control" value="0"></div>
        <div class="col-md-2"><label class="form-label fw-semibold small">Due First Month</label><input type="month" name="due_first_month" class="form-control"></div>
        <div class="col-md-2"><label class="form-label fw-semibold small">Due Last Month</label><input type="month" name="due_last_month" class="form-control"></div>
        <div class="col-md-2"><label class="form-label fw-semibold small">Due Amount</label><input type="number" step="0.01" name="due_amount" class="form-control" value="0"></div>
        <div class="col-md-4"><label class="form-label fw-semibold small">Amt Received To</label>
          <select name="account_received_to" class="form-select">
            <option value="">— Select Ledger —</option>
            <?php foreach($ledgers as $l): ?><option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['account_name']) ?></option><?php endforeach; ?>
          </select></div>
        <div class="col-12"><button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Save Uniform Bill</button></div>
      </div>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover mb-0" id="uniformBillsTable">
      <thead class="table-light"><tr><th>Date</th><th>Employee</th><th>Total</th><th>Paid</th><th>Balance</th><th>Dues</th></tr></thead>
      <tbody>
      <?php foreach($bills as $b): ?>
      <tr>
        <td><?= date('d M Y',strtotime($b['bill_date'])) ?></td>
        <td class="fw-semibold"><?= htmlspecialchars($b['emp_name']) ?></td>
        <td><?= Helper::money($b['total_amount']) ?></td>
        <td><?= Helper::money($b['paid_amount']) ?></td>
        <td class="<?= $b['balance_amount']>0?'text-danger fw-bold':'' ?>"><?= Helper::money($b['balance_amount']) ?></td>
        <td><?= $b['no_of_dues'] ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($bills)): ?><tr><td colspan="6" class="text-center text-muted py-3">No uniform bills</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script>
const itemPrices = {};
<?php foreach($items as $it): ?>itemPrices[<?= $it['id'] ?>] = <?= $it['unit_price'] ?>;<?php endforeach; ?>
function setPrice(sel){
  const row = sel.closest('tr');
  const id = sel.value;
  const priceInput = row.querySelector('.price');
  if(id && itemPrices[id]) priceInput.value = itemPrices[id];
  calcUniRow(priceInput);
}
function calcUniRow(el){
  const row = el.closest('tr');
  const qty = parseFloat(row.querySelector('[name="qty[]"]').value)||0;
  const price = parseFloat(row.querySelector('.price').value)||0;
  row.querySelector('.item-total').value = (qty*price).toFixed(2);
  calcUniTotals();
}
function calcUniTotals(){
  let sub=0;
  document.querySelectorAll('.item-total').forEach(i=>sub+=parseFloat(i.value)||0);
  const disc = parseFloat(document.querySelector('[name="discount"]')?.value)||0;
  document.getElementById('uniSub').value = sub.toFixed(2);
  document.getElementById('uniTotal').value = (sub-disc).toFixed(2);
  calcUniBalance();
}
function calcUniBalance(){
  const total = parseFloat(document.getElementById('uniTotal').value)||0;
  const paid  = parseFloat(document.getElementById('paidAmt').value)||0;
  document.getElementById('uniBalance').value = (total-paid).toFixed(2);
}
function addUniRow(){
  const tbody = document.getElementById('uniBody');
  const first = tbody.rows[0].cloneNode(true);
  first.querySelectorAll('input').forEach(i=>i.value=i.readOnly?'':i.defaultValue||'');
  first.querySelector('select').value='';
  tbody.appendChild(first);
}
$('#uniformBillsTable').DataTable({pageLength:25, order:[]});
</script>
