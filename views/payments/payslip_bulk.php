<?php
/* ── Payslip Bulk Print ── */
?>
<div class="d-flex justify-content-between align-items-center mb-3 no-print">
  <h5 class="mb-0"><i class="bi bi-printer me-2"></i>Salary Slip Bulk Print</h5>
</div>

<!-- Selector Card (shown only on screen) -->
<div class="card mb-4 no-print">
  <div class="card-body">
    <form method="POST" id="bulkForm">
      <div class="row g-3 align-items-end">
        <div class="col-md-5">
          <label class="form-label fw-semibold">Select Client: <span class="text-danger">*</span></label>
          <select name="client_id" class="form-select" required id="bpClient">
            <option value="">— Select Client —</option>
            <?php foreach ($clients as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($clientId??'')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['company_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Select Month: <span class="text-danger">*</span></label>
          <select name="month" class="form-select" required id="bpMonth">
            <?php
            // Generate last 12 months
            for ($i = 0; $i < 12; $i++) {
                $ts  = strtotime("-$i months");
                $val = date('Y-m', $ts);
                $lbl = date("M' Y", $ts);
                $sel = ($month === $val) ? 'selected' : '';
                echo "<option value=\"$val\" $sel>$lbl</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-printer me-2"></i>Print</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php if (!empty($salaries)): ?>
<!-- Bulk Payslips (hidden screen, shown on print) -->
<?php foreach ($salaries as $s): ?>
<div class="payslip-page" style="page-break-after:always;max-width:800px;margin:0 auto 40px;padding:20px;border:1px solid #ccc">
  <!-- Header Info -->
  <div class="row mb-3">
    <div class="col-6">
      <p class="mb-1 small"><strong>Emp. Name:</strong> <?= htmlspecialchars($s['name']) ?></p>
      <p class="mb-1 small"><strong>Emp. ID:</strong> <?= htmlspecialchars($s['emp_code']) ?></p>
      <p class="mb-1 small"><strong>Designation:</strong> <?= htmlspecialchars($s['designation'] ?? '—') ?></p>
      <p class="mb-1 small"><strong>DOJ:</strong> <?= !empty($s['doj']) ? date('d M Y', strtotime($s['doj'])) : '—' ?></p>
      <p class="mb-0 small"><strong>Cell:</strong> <?= htmlspecialchars($s['mobile'] ?? '—') ?></p>
    </div>
    <div class="col-6 text-end">
      <p class="mb-1 small"><strong>Pay Slip No.:</strong> <?= htmlspecialchars($s['psl_no'] ?? '—') ?></p>
      <p class="mb-1 small"><strong>UAN No:</strong> <?= htmlspecialchars($s['uan_no'] ?: '—') ?></p>
      <p class="mb-1 small"><strong>ESI No.:</strong> <?= htmlspecialchars($s['esi_no'] ?: '—') ?></p>
      <p class="mb-1 small"><strong>Bank Name:</strong> <?= htmlspecialchars($s['bank_name'] ?: '—') ?></p>
      <p class="mb-0 small"><strong>Bank A/C No:</strong> <?= htmlspecialchars($s['bank_account'] ?: '—') ?></p>
    </div>
  </div>

  <h6 class="text-center border p-2 rounded bg-light mb-3 fw-bold">
    Pay Slip for <?= Helper::monthName($s['salary_month']) ?>
  </h6>

  <!-- Site-wise duties -->
  <?php
  $positions = $salaryPositions[$s['id']] ?? [];
  if (!empty($positions)):
  ?>
  <table class="table table-bordered table-sm mb-3" style="font-size:12px">
    <thead class="table-dark">
      <tr><th>Site / Client</th><th>Duties</th><th>OT</th><th>OFF</th><th>LOP</th><th>Total</th></tr>
    </thead>
    <tbody>
    <?php foreach ($positions as $pos): ?>
    <?php $total = ($pos['duties']??0)+($pos['ot']??0)+($pos['off_days']??0); ?>
    <tr>
      <td><?= htmlspecialchars($pos['company_name']) ?></td>
      <td><?= $pos['duties']??0 ?></td>
      <td><?= $pos['ot']??0 ?></td>
      <td><?= $pos['off_days']??0 ?></td>
      <td>0</td>
      <td class="fw-bold"><?= $total ?></td>
    </tr>
    <?php endforeach; ?>
    <tr class="table-secondary fw-bold">
      <td>TOTAL</td>
      <td><?= $s['days_present'] ?></td>
      <td><?= $s['days_ot'] ?></td>
      <td><?= $s['days_off'] ?></td>
      <td><?= $s['days_absent'] ?></td>
      <td><?= $s['total_days'] ?></td>
    </tr>
    </tbody>
  </table>
  <?php endif; ?>

  <!-- Earnings & Deductions -->
  <table class="table table-bordered table-sm mb-3" style="font-size:12px">
    <thead><tr><th colspan="2" class="text-center bg-light">Earnings</th><th colspan="2" class="text-center bg-light">Deductions</th></tr></thead>
    <tbody>
      <tr><td>Basic Wage (<?= Helper::money($s['basic_wage']) ?>)</td><td class="text-end"><?= Helper::money($s['total_earnings'] - $s['attendance_incentive']) ?></td><td>Salary Advance</td><td class="text-end"><?= Helper::money($s['salary_advance_ded']) ?></td></tr>
      <tr><td>DA</td><td class="text-end"><?= Helper::money($s['da']) ?></td><td>Uniform Due</td><td class="text-end"><?= Helper::money($s['uniform_due_ded']) ?></td></tr>
      <tr><td>HRA</td><td class="text-end"><?= Helper::money($s['hra']) ?></td><td>Extra Deduction/Penalty</td><td class="text-end"><?= Helper::money($s['extra_deduction'] ?? 0) ?></td></tr>
      <tr><td>CONV</td><td class="text-end"><?= Helper::money($s['conv']) ?></td><td>Insurance Premium</td><td class="text-end"><?= Helper::money($s['insurance_premium']) ?></td></tr>
      <tr><td>Others</td><td class="text-end"><?= Helper::money($s['others'] ?? 0) ?></td><td>EPF</td><td class="text-end"><?= Helper::money($s['epf']) ?></td></tr>
      <tr><td>Med Wash</td><td class="text-end"><?= Helper::money($s['med_wash'] ?? 0) ?></td><td>ESI</td><td class="text-end"><?= Helper::money($s['esi']) ?></td></tr>
      <tr><td>Extra Allowance</td><td class="text-end"><?= Helper::money($s['extra_allowance'] ?? 0) ?></td><td>Other Deduction</td><td class="text-end"><?= Helper::money($s['other_deduction'] ?? 0) ?></td></tr>
      <tr><td>Attendance Incentives</td><td class="text-end"><?= Helper::money($s['attendance_incentive']) ?></td><td>—</td><td></td></tr>
    </tbody>
    <tfoot class="table-secondary fw-bold">
      <tr><td>Total Earnings</td><td class="text-end"><?= Helper::money($s['total_earnings']) ?></td><td>Total Deductions</td><td class="text-end"><?= Helper::money($s['total_deductions']) ?></td></tr>
      <tr><td colspan="2" class="text-center">Net Salary</td><td colspan="2" class="text-center fs-6 fw-bold text-success"><?= Helper::money($s['net_salary']) ?></td></tr>
    </tfoot>
  </table>

  <p class="text-center fw-semibold small border p-2 rounded bg-light">
    AMOUNT IN WORDS: <?= Helper::moneyWords((float)$s['net_salary']) ?>
  </p>

  <div class="row mt-4">
    <div class="col-6 text-center border-top pt-2">
      <small class="text-muted">[ saisaktheeswari@gmail.com ]</small><br>
      <strong class="small">Issued By:</strong>
    </div>
    <div class="col-6 text-center border-top pt-2">
      <br><strong class="small">Received By:</strong>
    </div>
  </div>
</div>
<?php endforeach; ?>

<div class="text-center no-print mt-3">
  <button onclick="window.print()" class="btn btn-primary btn-lg"><i class="bi bi-printer me-2"></i>Print All <?= count($salaries) ?> Payslips</button>
</div>

<?php elseif (!empty($_POST['client_id'])): ?>
<div class="alert alert-info">No salary records found for this client and month. Please generate salaries first.</div>
<?php endif; ?>

<style>
@media print {
  .no-print { display:none !important; }
  .main-content { margin:0; padding:0; }
  .payslip-page { page-break-after: always; border:none; margin:0; padding:20px; }
}
</style>
