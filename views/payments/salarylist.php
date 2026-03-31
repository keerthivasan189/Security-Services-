<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Staff Salary List (Aqutence)</h5>
  <div class="d-flex gap-2 no-print">
    <button onclick="window.print()" class="btn btn-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
    <button onclick="exportAqutenceExcel()" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Export to Excel</button>
  </div>
</div>

<!-- Comprehensive Filter Panel -->
<div class="card mb-3 no-print">
  <div class="card-header py-2 fw-semibold small"><i class="bi bi-funnel me-1"></i>USE THESE FILTERS TO GET YOUR EXACT LIST</div>
  <div class="card-body py-2">
    <form method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="payments/salarylist">
      <div class="row g-2 align-items-end">
        <div class="col-md-2"><label class="form-label small">Employee Emp ID:</label>
          <input name="emp_code" class="form-control form-control-sm" placeholder="" value="<?= htmlspecialchars($empCode??'') ?>"></div>
        <div class="col-md-2"><label class="form-label small">Field Officer:</label>
          <select name="field_officer_id" class="form-select form-select-sm">
            <option value="">——— All ———</option>
            <?php foreach ($fieldOfficers as $fo): ?>
            <option value="<?= $fo['id'] ?>" <?= ($fOfficer??'')==$fo['id']?'selected':'' ?>><?= htmlspecialchars($fo['name']) ?></option>
            <?php endforeach; ?>
          </select></div>
        <div class="col-md-2"><label class="form-label small">Company:</label>
          <select name="client_id" class="form-select form-select-sm">
            <option value="">——— All ———</option>
            <?php foreach ($clients as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($clientId??'')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['company_name']) ?></option>
            <?php endforeach; ?>
          </select></div>
        <div class="col-md-3"><label class="form-label small">Employee:</label>
          <select name="employee_id" class="form-select form-select-sm">
            <option value="">——— All ———</option>
            <?php foreach ($employees as $e): ?>
            <option value="<?= $e['id'] ?>" <?= ($fEmp??'')==$e['id']?'selected':'' ?>><?= htmlspecialchars($e['name'].' ('.$e['emp_code'].')') ?></option>
            <?php endforeach; ?>
          </select></div>
        <div class="col-md-2"><label class="form-label small">Month:</label>
          <input type="month" name="month" class="form-control form-control-sm" value="<?= $month ?>"></div>
      </div>
      <div class="row g-2 align-items-end mt-1">
        <div class="col-md-2"><label class="form-label small">Paid or Not:</label>
          <select name="paid_status" class="form-select form-select-sm"><option value="">——— All ———</option>
          <option value="Paid"     <?= ($fPaid??'')==='Paid'    ?'selected':'' ?>>Paid</option>
          <option value="Not Paid" <?= ($fPaid??'')==='Not Paid'?'selected':'' ?>>Not Paid</option>
          </select></div>
        <div class="col-md-2"><label class="form-label small">Salary Mode:</label>
          <select name="mode" class="form-select form-select-sm"><option value="">——— All ———</option>
          <option value="NEFT"   <?= ($fMode??'')==='NEFT'  ?'selected':'' ?>>NEFT</option>
          <option value="Cash"   <?= ($fMode??'')==='Cash'  ?'selected':'' ?>>Cash</option>
          <option value="Cheque" <?= ($fMode??'')==='Cheque'?'selected':'' ?>>Cheque</option>
          </select></div>
        <div class="col-auto">
          <button class="btn btn-success btn-sm"><i class="bi bi-search me-1"></i>SEARCH</button>
          <a href="<?= u('payments/salarylist') ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
        </div>
      </div>
    </form>
  </div>
</div>

<?php if (!empty($salaries)): ?>
<div class="card">
  <div class="card-header fw-semibold">
    <?php if ($clientId): ?>
    <?= htmlspecialchars($clients[array_search($clientId, array_column($clients,'id'))]['company_name'] ?? '') ?>
    <?php endif; ?>
    — AQUTENCE <?= strtoupper(Helper::monthName($month)) ?>
  </div>
  <div class="card-body p-0" style="overflow-x:auto">
    <table class="table table-bordered table-sm mb-0" style="font-size:11px;white-space:nowrap">
      <thead class="table-dark">
        <tr>
          <th>S.No</th><th>Code</th><th>Name</th><th>T.D</th><th>Rate</th>
          <th>Amount</th><th>Tot.Allow</th><th>Total</th>
          <th>Advance</th><th>Uniform</th><th>INS</th>
          <th>EPF</th><th>ESI</th><th>Other Ded</th><th>Tot.Ded</th>
          <th>Net</th><th>Mode</th><th>SIGN</th><th>Remarks</th><th class="no-print">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php $sno=0; $totEarn=0; $totDed=0; $totNet=0; ?>
      <?php foreach ($salaries as $s): $sno++; $totEarn+=$s['total_earnings']; $totDed+=$s['total_deductions']; $totNet+=$s['net_salary']; ?>
      <tr>
        <td><?= $sno ?></td>
        <td><?= htmlspecialchars($s['emp_code']) ?></td>
        <td class="fw-semibold"><?= htmlspecialchars($s['name']) ?> / <?= htmlspecialchars($s['current_company'] ?? 'No Company') ?></td>
        <td><?= $s['total_days'] ?></td>
        <td><?= Helper::money($s['basic_wage']) ?></td>
        <td><?= Helper::money($s['total_earnings'] - $s['attendance_incentive']) ?></td>
        <td><?= Helper::money($s['attendance_incentive']) ?></td>
        <td class="fw-bold"><?= Helper::money($s['total_earnings']) ?></td>
        <td><?= Helper::money($s['salary_advance_ded']) ?></td>
        <td><?= Helper::money($s['uniform_due_ded']) ?></td>
        <td><?= Helper::money($s['insurance_premium']) ?></td>
        <td><?= Helper::money($s['epf']) ?></td>
        <td><?= Helper::money($s['esi']) ?></td>
        <td><?= Helper::money($s['other_deduction'] ?? 0) ?></td>
        <td><?= Helper::money($s['total_deductions']) ?></td>
        <td class="fw-bold text-success"><?= Helper::money($s['net_salary']) ?></td>
        <td><span class="badge bg-light text-dark"><?= $s['payment_mode'] ?></span></td>
        <td style="min-width:80px"></td>
        <td></td>
        <td class="no-print">
          <a href="<?= u('payments/payslip/' . $s['id']) ?>" class="btn btn-xs btn-outline-info py-0 px-1" title="Payslip"><i class="bi bi-receipt"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot class="table-secondary fw-bold">
        <tr>
          <td colspan="7" class="text-end">TOTALS:</td>
          <td><?= Helper::money($totEarn) ?></td>
          <td colspan="6"></td>
          <td><?= Helper::money($totDed) ?></td>
          <td class="text-success"><?= Helper::money($totNet) ?></td>
          <td colspan="4"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
<?php elseif ($clientId): ?>
<div class="alert alert-info">No salary records for this client and month. Please generate salary first.</div>
<?php else: ?>
<div class="alert alert-secondary">Select a client and month to view salary list.</div>
<?php endif; ?>
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script>
function exportAqutenceExcel(){
  const tbl = document.querySelector('table.table-bordered');
  if(!tbl){alert('No data to export');return;}
  const wb   = XLSX.utils.book_new();
  const ws   = XLSX.utils.table_to_sheet(tbl, {raw:false});
  const month = '<?= $month ?>';
  XLSX.utils.book_append_sheet(wb, ws, 'Aqutence');
  XLSX.writeFile(wb, 'Aqutence_' + month + '.xlsx');
}
</script>
