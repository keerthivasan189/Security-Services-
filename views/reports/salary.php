<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Salary Report (Acquittance)</h5>
  <div class="d-flex gap-2 no-print">
    <?php $exUrl = BASE_URL.'/index.php?url=reports/salary&export=excel&client_id='.urlencode($clientId??'').'&month='.$month.'&mode='.urlencode($mode??''); ?>
    <a href="<?= $exUrl ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="reports/salary">
      <div class="col-md-3"><label class="form-label small">Client</label>
        <select name="client_id" class="form-select form-select-sm">
          <option value="">All Clients</option>
          <?php foreach ($clients as $c): ?><option value="<?= $c['id'] ?>" <?= $clientId==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['company_name']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2"><label class="form-label small">Month</label><input type="month" name="month" class="form-control form-control-sm" value="<?= $month ?>"></div>
      <div class="col-md-2"><label class="form-label small">Payment Mode</label>
        <select name="mode" class="form-select form-select-sm">
          <option value="">All</option>
          <option value="NEFT" <?=($mode??'')==='NEFT'?'selected':''?>>NEFT</option>
          <option value="Cash" <?=($mode??'')==='Cash'?'selected':''?>>Cash</option>
          <option value="Cheque" <?=($mode??'')==='Cheque'?'selected':''?>>Cheque</option>
        </select>
      </div>
      <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
        <a href="<?= u('reports/salary') ?>" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
  </div>
</div>

<?php if (!empty($salaries)): $totalNet = 0; $totalEarn = 0; $totalDed = 0; foreach($salaries as $s) { $totalNet += $s['net_salary']; $totalEarn += $s['total_earnings']; $totalDed += $s['total_deductions']; } ?>
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="stat-card"><div class="label">Employees</div><div class="value text-primary"><?= count($salaries) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Total Earnings</div><div class="value text-success"><?= Helper::money($totalEarn) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Total Deductions</div><div class="value text-danger"><?= Helper::money($totalDed) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Net Salary</div><div class="value text-info"><?= Helper::money($totalNet) ?></div></div></div>
</div>
<div class="card">
  <div class="card-body p-0" style="overflow-x:auto">
    <table class="table table-bordered table-sm mb-0" style="font-size:11px">
      <thead class="table-light">
        <tr><th>S.No</th><th>Code</th><th>Name</th><th>T.D</th><th>Basic</th><th>DA</th><th>HRA</th><th>Att.Inc</th><th>Total Earn</th><th>Advance</th><th>Uniform</th><th>INS</th><th>EPF</th><th>ESI</th><th>Other</th><th>Tot.Ded</th><th>Net</th><th>Mode</th></tr>
      </thead>
      <tbody>
      <?php foreach ($salaries as $i => $s): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><?= htmlspecialchars($s['emp_code']) ?></td>
        <td><?= htmlspecialchars($s['name']) ?></td>
        <td><?= $s['days_present'] ?></td>
        <td><?= number_format($s['basic_wage'],2) ?></td>
        <td><?= number_format($s['da'],2) ?></td>
        <td><?= number_format($s['hra'],2) ?></td>
        <td><?= number_format($s['attendance_incentive'],2) ?></td>
        <td class="fw-bold"><?= number_format($s['total_earnings'],2) ?></td>
        <td><?= number_format($s['salary_advance_ded'],2) ?></td>
        <td><?= number_format($s['uniform_due_ded'],2) ?></td>
        <td><?= number_format($s['insurance_premium'],2) ?></td>
        <td><?= number_format($s['epf'],2) ?></td>
        <td><?= number_format($s['esi'],2) ?></td>
        <td><?= number_format($s['other_deduction'] ?? 0,2) ?></td>
        <td class="text-danger"><?= number_format($s['total_deductions'],2) ?></td>
        <td class="fw-bold text-success"><?= number_format($s['net_salary'],2) ?></td>
        <td><?= $s['payment_mode'] ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot class="table-light">
        <tr><td colspan="8" class="fw-bold text-end">Totals:</td>
        <td class="fw-bold"><?=number_format($totalEarn,2)?></td><td colspan="7"></td>
        <td class="text-danger fw-bold"><?=number_format($totalDed,2)?></td>
        <td class="text-success fw-bold"><?=number_format($totalNet,2)?></td><td></td></tr>
      </tfoot>
    </table>
  </div>
</div>
<?php else: ?>
<div class="alert alert-info">Select a month to view salary records. Optionally filter by client or payment mode.</div>
<?php endif; ?>
