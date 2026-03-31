<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Create Invoice</h5>
  <a href="<?= u('receipts/clientbills') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" id="invoiceForm">
<div class="card mb-3">
  <div class="card-header">Invoice Details</div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label fw-semibold small">Client <span class="text-danger">*</span></label>
        <select name="client_id" class="form-select" required id="clientSel" onchange="setBillType(this)">
          <option value="">— Select Client —</option>
          <?php foreach ($clients as $c): ?>
          <option value="<?= $c['id'] ?>" data-type="<?= $c['bill_type'] ?>"><?= htmlspecialchars($c['company_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold small">Bill Type</label>
        <select name="bill_type" class="form-select" id="billType">
          <option value="GST">GST</option>
          <option value="RCM">RCM</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold small">Invoice Month</label>
        <input type="month" name="invoice_month" class="form-control" value="<?= date('Y-m') ?>" required>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold small">Invoice Date</label>
        <input type="date" name="invoice_date" class="form-control" value="<?= date('Y-m-d') ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold small">Deployed Hours</label>
        <input type="number" name="deployed_hours" class="form-control" value="12">
      </div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>Line Items</span>
    <button type="button" class="btn btn-sm btn-success" onclick="addRow()"><i class="bi bi-plus me-1"></i>Add Row</button>
  </div>
  <div class="card-body p-0">
    <table class="table table-bordered mb-0" style="font-size:12px" id="itemsTable">
      <thead class="table-light">
        <tr>
          <th>Sl</th><th>Code</th><th>SAC</th><th>Designation</th>
          <th>NOS</th><th>Duties</th><th>OT</th><th>OFF</th>
          <th>Total Hrs</th><th>Rate/Hr</th><th>Amount</th><th></th>
        </tr>
      </thead>
      <tbody id="itemsBody">
        <tr>
          <td>1</td>
          <td><input type="text" name="code[]" class="form-control form-control-sm" value="106" style="width:60px"></td>
          <td><input type="text" name="sac[]" class="form-control form-control-sm" value="998525" style="width:70px"></td>
          <td><input type="text" name="designation[]" class="form-control form-control-sm" placeholder="e.g. SECURITY GUARD" style="min-width:160px"></td>
          <td><input type="number" name="nos[]" class="form-control form-control-sm calc" style="width:55px" oninput="calcRow(this)"></td>
          <td><input type="number" name="duties[]" class="form-control form-control-sm calc" style="width:65px" oninput="calcRow(this)"></td>
          <td><input type="number" name="ot[]" class="form-control form-control-sm" value="0" style="width:50px"></td>
          <td><input type="number" name="off_days[]" class="form-control form-control-sm" value="0" style="width:50px"></td>
          <td><input type="number" name="total_hours[]" class="form-control form-control-sm hours" style="width:70px" oninput="calcAmount(this)"></td>
          <td><input type="number" step="0.01" name="rate_per_hour[]" class="form-control form-control-sm rate" style="width:80px" oninput="calcAmount(this)"></td>
          <td><input type="number" step="0.01" name="amount[]" class="form-control form-control-sm amount" style="width:90px" readonly></td>
          <td><button type="button" class="btn btn-danger btn-sm py-0" onclick="this.closest('tr').remove();calcTotals()">×</button></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header">Totals</div>
  <div class="card-body">
    <div class="row g-3" style="max-width:500px">
      <div class="col-md-6"><label class="form-label fw-semibold small">Sub Total</label>
        <input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control" readonly></div>
      <div class="col-md-6"><label class="form-label fw-semibold small">IGST</label>
        <input type="number" step="0.01" name="igst" id="igst" class="form-control" value="0" oninput="calcGrandTotal()"></div>
      <div class="col-md-6"><label class="form-label fw-semibold small">SGST</label>
        <input type="number" step="0.01" name="sgst" id="sgst" class="form-control" value="0" oninput="calcGrandTotal()"></div>
      <div class="col-md-6"><label class="form-label fw-semibold small">CGST</label>
        <input type="number" step="0.01" name="cgst" id="cgst" class="form-control" value="0" oninput="calcGrandTotal()"></div>
      <div class="col-md-6"><label class="form-label fw-semibold small">Round Off</label>
        <input type="number" step="0.01" name="round_off" id="round_off" class="form-control" value="0" oninput="calcGrandTotal()"></div>
      <div class="col-md-6"><label class="form-label fw-semibold small">Grand Total</label>
        <input type="number" step="0.01" name="grand_total" id="grand_total" class="form-control fw-bold" readonly></div>
    </div>
  </div>
</div>

<button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Save Invoice</button>
</form>

<script>
let rowCount = 1;
function addRow(){
  rowCount++;
  const tbody = document.getElementById('itemsBody');
  const tr = document.createElement('tr');
  tr.innerHTML = `<td>${rowCount}</td>
    <td><input type="text" name="code[]" class="form-control form-control-sm" value="106" style="width:60px"></td>
    <td><input type="text" name="sac[]" class="form-control form-control-sm" value="998525" style="width:70px"></td>
    <td><input type="text" name="designation[]" class="form-control form-control-sm" style="min-width:160px"></td>
    <td><input type="number" name="nos[]" class="form-control form-control-sm" style="width:55px" oninput="calcRow(this)"></td>
    <td><input type="number" name="duties[]" class="form-control form-control-sm" style="width:65px" oninput="calcRow(this)"></td>
    <td><input type="number" name="ot[]" class="form-control form-control-sm" value="0" style="width:50px"></td>
    <td><input type="number" name="off_days[]" class="form-control form-control-sm" value="0" style="width:50px"></td>
    <td><input type="number" name="total_hours[]" class="form-control form-control-sm hours" style="width:70px" oninput="calcAmount(this)"></td>
    <td><input type="number" step="0.01" name="rate_per_hour[]" class="form-control form-control-sm rate" style="width:80px" oninput="calcAmount(this)"></td>
    <td><input type="number" step="0.01" name="amount[]" class="form-control form-control-sm amount" style="width:90px" readonly></td>
    <td><button type="button" class="btn btn-danger btn-sm py-0" onclick="this.closest('tr').remove();calcTotals()">×</button></td>`;
  tbody.appendChild(tr);
}
function calcRow(el){
  const row = el.closest('tr');
  const nos = parseFloat(row.querySelector('[name="nos[]"]')?.value)||0;
  const duties = parseFloat(row.querySelector('[name="duties[]"]')?.value)||0;
  const deployedHours = parseInt(document.querySelector('[name="deployed_hours"]')?.value)||12;
  const hrs = row.querySelector('.hours');
  if(hrs) hrs.value = (nos * duties * deployedHours).toFixed(0);
  calcAmount(el);
}
function calcAmount(el){
  const row = el.closest('tr');
  const hrs  = parseFloat(row.querySelector('.hours')?.value)||0;
  const rate = parseFloat(row.querySelector('.rate')?.value)||0;
  const amt  = row.querySelector('.amount');
  if(amt) amt.value = (hrs * rate).toFixed(2);
  calcTotals();
}
function calcTotals(){
  let sub = 0;
  document.querySelectorAll('[name="amount[]"]').forEach(a => sub += parseFloat(a.value)||0);
  document.getElementById('subtotal').value = sub.toFixed(2);
  calcGrandTotal();
}
function calcGrandTotal(){
  const sub   = parseFloat(document.getElementById('subtotal').value)||0;
  const igst  = parseFloat(document.getElementById('igst').value)||0;
  const sgst  = parseFloat(document.getElementById('sgst').value)||0;
  const cgst  = parseFloat(document.getElementById('cgst').value)||0;
  const rnd   = parseFloat(document.getElementById('round_off').value)||0;
  document.getElementById('grand_total').value = (sub+igst+sgst+cgst+rnd).toFixed(2);
}
function setBillType(sel){
  const opt  = sel.options[sel.selectedIndex];
  const type = opt.getAttribute('data-type');
  if(type) document.getElementById('billType').value = type;
}
</script>
