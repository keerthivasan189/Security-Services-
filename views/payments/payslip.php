<div class="d-flex justify-content-between align-items-center mb-3 no-print">
  <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Pay Slip — <?= htmlspecialchars($salary['psl_no']) ?></h5>
  <div class="d-flex gap-2">
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
    <a href="<?= u('payments/salarylist') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
  </div>
</div>

<div class="card" style="max-width:800px;margin:0 auto">
  <div class="card-body p-4">
    <div class="row mb-3">
      <div class="col-6">
        <p class="mb-1 small"><strong>Emp. Name:</strong> <?= htmlspecialchars($salary['name']) ?></p>
        <p class="mb-1 small"><strong>Emp. ID:</strong> <?= htmlspecialchars($salary['emp_code']) ?></p>
        <p class="mb-1 small"><strong>Designation:</strong> <?= htmlspecialchars($salary['designation']) ?></p>
        <p class="mb-1 small"><strong>DOJ:</strong> <?= $salary['doj'] ? date('d M Y', strtotime($salary['doj'])) : '—' ?></p>
        <p class="mb-0 small"><strong>Cell:</strong> <?= htmlspecialchars($salary['mobile']) ?></p>
      </div>
      <div class="col-6 text-end">
        <p class="mb-1 small"><strong>Pay Slip No.:</strong> <?= htmlspecialchars($salary['psl_no']) ?></p>
        <p class="mb-1 small"><strong>UAN No:</strong> <?= htmlspecialchars($salary['uan_no'] ?: '—') ?></p>
        <p class="mb-1 small"><strong>ESI No.:</strong> <?= htmlspecialchars($salary['esi_no'] ?: '—') ?></p>
        <p class="mb-1 small"><strong>Bank Name:</strong> <?= htmlspecialchars($salary['bank_name'] ?: '—') ?></p>
        <p class="mb-0 small"><strong>Bank A/C No:</strong> <?= htmlspecialchars($salary['bank_account'] ?: '—') ?></p>
      </div>
    </div>

    <h6 class="text-center border p-2 rounded bg-light mb-3 fw-bold">
      Pay Slip for <?= Helper::monthName($salary['salary_month']) ?>
    </h6>

    <!-- Site-wise duties -->
    <?php if (!empty($positions)): ?>
    <table class="table table-bordered table-sm mb-3" style="font-size:12px">
      <thead class="table-dark">
        <tr><th>Site / Client</th><th>Duties</th><th>OT</th><th>OFF</th><th>LOP</th><th>Total</th></tr>
      </thead>
      <tbody>
      <?php $totalDuties=0; ?>
      <?php foreach ($positions as $pos): ?>
      <?php $total = ($pos['duties']??0) + ($pos['ot']??0) + ($pos['off_days']??0); $totalDuties+=$total; ?>
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
        <td><?= $salary['days_present'] ?></td>
        <td><?= $salary['days_ot'] ?></td>
        <td><?= $salary['days_off'] ?></td>
        <td><?= $salary['days_absent'] ?></td>
        <td><?= $salary['total_days'] ?></td>
      </tr>
      </tbody>
    </table>
    <?php endif; ?>

    <!-- Earnings & Deductions -->
    <table class="table table-bordered table-sm mb-3" style="font-size:12px">
      <thead><tr><th colspan="2" class="text-center bg-light">Earnings</th><th colspan="2" class="text-center bg-light">Deductions</th></tr></thead>
      <tbody>
        <tr>
          <td>Basic Wage (<?= Helper::money($salary['basic_wage']) ?>)</td>
          <td class="text-end"><?= Helper::money($salary['total_earnings'] - $salary['attendance_incentive']) ?></td>
          <td>Salary Advance</td>
          <td class="text-end"><?= Helper::money($salary['salary_advance_ded']) ?></td>
        </tr>
        <tr>
          <td>DA</td><td class="text-end"><?= Helper::money($salary['da']) ?></td>
          <td>Uniform Due</td><td class="text-end"><?= Helper::money($salary['uniform_due_ded']) ?></td>
        </tr>
        <tr>
          <td>HRA</td><td class="text-end"><?= Helper::money($salary['hra']) ?></td>
          <td>Extra Deduction/Penalty</td><td class="text-end"><?= Helper::money($salary['extra_deduction']) ?></td>
        </tr>
        <tr>
          <td>CONV</td><td class="text-end"><?= Helper::money($salary['conv']) ?></td>
          <td>Insurance Premium</td><td class="text-end"><?= Helper::money($salary['insurance_premium']) ?></td>
        </tr>
        <tr>
          <td>Others</td><td class="text-end"><?= Helper::money($salary['others']) ?></td>
          <td>EPF</td><td class="text-end"><?= Helper::money($salary['epf']) ?></td>
        </tr>
        <tr>
          <td>Med Wash</td><td class="text-end"><?= Helper::money($salary['med_wash']) ?></td>
          <td>ESI</td><td class="text-end"><?= Helper::money($salary['esi']) ?></td>
        </tr>
        <tr>
          <td>Extra Allowance</td><td class="text-end"><?= Helper::money($salary['extra_allowance']) ?></td>
          <td>Other Deduction</td><td class="text-end"><?= Helper::money($salary['other_deduction']) ?></td>
        </tr>
        <tr>
          <td>Attendance Incentives</td><td class="text-end"><?= Helper::money($salary['attendance_incentive']) ?></td>
          <td>—</td><td></td>
        </tr>
      </tbody>
      <tfoot class="table-secondary fw-bold">
        <tr>
          <td>Total Earnings</td><td class="text-end"><?= Helper::money($salary['total_earnings']) ?></td>
          <td>Total Deductions</td><td class="text-end"><?= Helper::money($salary['total_deductions']) ?></td>
        </tr>
        <tr>
          <td colspan="2" class="text-center">Net Salary</td>
          <td colspan="2" class="text-center fs-5 fw-bold text-success"><?= Helper::money($salary['net_salary']) ?></td>
        </tr>
      </tfoot>
    </table>

    <p class="text-center fw-semibold small border p-2 rounded bg-light">
      AMOUNT IN WORDS: <?= Helper::moneyWords((float)$salary['net_salary']) ?>
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
</div>
