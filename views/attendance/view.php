<?php
/* ── Status helpers ── */
$statusColor = fn($s) => match($s) {
    'P'   => '#198754',
    'A'   => '#dc3545',
    'OT'  => '#0d6efd',
    'OFF' => '#6c757d',
    'HD'  => '#e67e22',
    default => '#aaa',
};
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-grid-3x3 me-2"></i>View Client Attendance</h5>
  <?php if (!empty($rows)): ?>
  <div class="d-flex gap-2 no-print">
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-printer me-1"></i>Print
    </button>
  </div>
  <?php endif; ?>
</div>

<!-- Filter -->
<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="attendance/viewAttendance">
      <div class="col-md-2">
        <label class="form-label fw-semibold small mb-1"><i class="bi bi-building me-1"></i>Client</label>
        <select name="client_id" class="form-select form-select-sm">
          <option value="">— All Clients —</option>
          <?php foreach ($clients as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $clientId==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['company_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold small mb-1"><i class="bi bi-person-check me-1"></i>Field Officer</label>
        <select name="field_officer_id" class="form-select form-select-sm">
          <option value="">— All —</option>
          <?php foreach ($fieldOfficers as $fo): ?>
          <option value="<?= $fo['id'] ?>" <?= $filterOfficer==$fo['id']?'selected':'' ?>><?= htmlspecialchars($fo['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php if (!empty($designations)): ?>
      <div class="col-md-2">
        <label class="form-label fw-semibold small mb-1"><i class="bi bi-person-badge me-1"></i>Designation</label>
        <select name="designation" class="form-select form-select-sm">
          <option value="">— All —</option>
          <?php foreach ($designations as $d): ?>
          <option value="<?= htmlspecialchars($d) ?>" <?= $filterDesig===$d?'selected':'' ?>><?= htmlspecialchars($d) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>
      <div class="col-md-2">
        <label class="form-label fw-semibold small mb-1">Start Date</label>
        <input type="date" name="start_date" class="form-control form-control-sm" value="<?= htmlspecialchars($startDate) ?>">
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold small mb-1">End Date</label>
        <input type="date" name="end_date" class="form-control form-control-sm" value="<?= htmlspecialchars($endDate) ?>">
      </div>
      <div class="col-auto">
        <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Search</button>
      </div>
    </form>
  </div>
</div>

<?php if (!empty($rows)): ?>
<?php
  /* ── Pre-compute designation summaries for salary table ── */
  $desSummary = [];
  $totalDaysInPeriod = count($days);
  foreach ($rows as $emp) {
    $des = $emp['designation'];
    if (!isset($desSummary[$des])) {
      $desSummary[$des] = ['P'=>0,'OT'=>0,'OFF'=>0,'HD'=>0,'A'=>0,'total_days'=>0,'salary_earned'=>0,'count'=>0,'basic_wage'=>0];
    }
    $desSummary[$des]['P']             += $emp['P'];
    $desSummary[$des]['OT']            += $emp['OT'];
    $desSummary[$des]['OFF']           += $emp['OFF'];
    $desSummary[$des]['HD']            += $emp['HD'];
    $desSummary[$des]['A']             += $emp['A'];
    $desSummary[$des]['total_days']    += $emp['total_days'];
    $desSummary[$des]['salary_earned'] += $emp['salary_earned'];
    $desSummary[$des]['basic_wage']    += $emp['basic_wage'];
    $desSummary[$des]['count']++;
  }
?>

<!-- ── Day-wise Attendance Grid ── -->
<div class="card mb-3">
  <div class="card-header fw-semibold d-flex justify-content-between align-items-center py-2">
    <span><i class="bi bi-table me-1"></i>
    Attendance Grid — <?= date('d M Y', strtotime($startDate)) ?> to <?= date('d M Y', strtotime($endDate)) ?>
    <?php if ($filterDesig): ?><span class="badge bg-info text-dark ms-2"><?= htmlspecialchars($filterDesig) ?></span><?php endif; ?>
    </span>
    <!-- Legend -->
    <span class="d-flex gap-2 small no-print">
      <span style="color:#198754">● P=Present</span>
      <span style="color:#e67e22">● HD=Half Day</span>
      <span style="color:#dc3545">● A=Absent</span>
      <span style="color:#0d6efd">● OT=Overtime</span>
      <span style="color:#6c757d">● OFF=Off</span>
    </span>
  </div>
  <div class="card-body p-0" style="overflow-x:auto">
    <table class="table table-bordered table-sm mb-0" id="attendanceGrid" style="font-size:11px;white-space:nowrap">
      <thead class="table-dark">
        <tr>
          <th style="min-width:30px">S.No</th>
          <th style="min-width:140px">Employee</th>
          <th style="min-width:90px">Designation</th>
          <?php foreach ($days as $d): ?>
          <th style="min-width:26px;text-align:center;line-height:1.2">
            <?= date('d', strtotime($d)) ?><br>
            <span style="font-size:8px;opacity:.8"><?= date('D', strtotime($d)) ?></span>
          </th>
          <?php endforeach; ?>
          <th style="min-width:28px" title="Present">P</th>
          <th style="min-width:28px" title="Overtime">OT</th>
          <th style="min-width:30px" title="Off">OFF</th>
          <th style="min-width:30px" title="Half Day" style="color:#e67e22">HD</th>
          <th style="min-width:28px" title="Absent">Ab</th>
          <th style="min-width:36px">Tot</th>
        </tr>
      </thead>
      <tbody>
      <?php $sno = 0; foreach ($rows as $emp): $sno++; ?>
      <tr>
        <td class="text-center text-muted"><?= $sno ?></td>
        <td class="fw-semibold"><?= htmlspecialchars($emp['name']) ?></td>
        <td class="small text-muted"><?= htmlspecialchars($emp['designation']) ?></td>
        <?php foreach ($days as $d): ?>
        <?php $st = $emp['dates'][$d] ?? ''; ?>
        <td style="text-align:center;font-weight:600;color:<?= $statusColor($st) ?>">
          <?= $st ?: '—' ?>
        </td>
        <?php endforeach; ?>
        <td class="fw-bold text-success text-center"><?= $emp['P'] ?></td>
        <td class="fw-bold text-primary text-center"><?= $emp['OT'] ?></td>
        <td class="text-center text-muted"><?= $emp['OFF'] ?></td>
        <td class="fw-bold text-center" style="color:#e67e22"><?= $emp['HD'] ?></td>
        <td class="text-danger text-center"><?= $emp['A'] ?></td>
        <td class="fw-bold text-center"><?= $emp['total_days'] ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
      <?php
        /* Day-wise totals footer */
        $allPres = array_sum(array_column($rows,'P'));
        $allOT   = array_sum(array_column($rows,'OT'));
        $allOFF  = array_sum(array_column($rows,'OFF'));
        $allHD   = array_sum(array_column($rows,'HD'));
        $allAbs  = array_sum(array_column($rows,'A'));
        $allTot  = array_sum(array_column($rows,'total_days'));
      ?>
      <tfoot>
        <!-- Day-wise present totals by designation -->
        <?php foreach (array_keys($desSummary) as $des): ?>
        <tr class="table-secondary" style="font-size:10px">
          <td colspan="3" class="fw-bold small"><?= htmlspecialchars($des) ?> (<?= $desSummary[$des]['count'] ?>)</td>
          <?php foreach ($days as $d):
            $dayDesCount = array_sum(array_map(
              fn($e) => ($e['designation']===$des && isset($e['dates'][$d]) && $e['dates'][$d]==='P') ? 1 : 0,
              $rows
            ));
          ?>
          <td style="text-align:center;font-weight:700"><?= $dayDesCount ?: '' ?></td>
          <?php endforeach; ?>
          <td class="fw-bold text-center"><?= $desSummary[$des]['P'] ?></td>
          <td class="fw-bold text-center"><?= $desSummary[$des]['OT'] ?></td>
          <td class="text-center"><?= $desSummary[$des]['OFF'] ?></td>
          <td class="text-center" style="color:#e67e22"><?= $desSummary[$des]['HD'] ?></td>
          <td class="text-center"><?= $desSummary[$des]['A'] ?></td>
          <td class="fw-bold text-center"><?= $desSummary[$des]['total_days'] ?></td>
        </tr>
        <?php endforeach; ?>
        <!-- Grand total row -->
        <tr class="table-warning fw-bold">
          <td colspan="3">TOTAL DUTIES</td>
          <?php foreach ($days as $d):
            $dayTotal = array_sum(array_map(
              fn($e) => (isset($e['dates'][$d]) && in_array($e['dates'][$d],['P','OT','OFF'])) ? 1 : 0,
              $rows
            ));
          ?>
          <td style="text-align:center;font-weight:700"><?= $dayTotal ?: '' ?></td>
          <?php endforeach; ?>
          <td class="text-center"><?= $allPres ?></td>
          <td class="text-center"><?= $allOT ?></td>
          <td class="text-center"><?= $allOFF ?></td>
          <td class="text-center"><?= $allHD ?></td>
          <td class="text-center"><?= $allAbs ?></td>
          <td class="text-center"><?= $allTot ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<!-- ── Salary Summary Table ── -->
<div class="card">
  <div class="card-header fw-semibold py-2"><i class="bi bi-cash-stack me-1"></i>Salary Summary — Designation Wise</div>
  <div class="card-body p-0" style="overflow-x:auto">
    <table class="table table-bordered table-sm mb-0" style="font-size:12px">
      <thead class="table-dark">
        <tr>
          <th>Designation</th>
          <th class="text-center">P</th>
          <th class="text-center">OT</th>
          <th class="text-center">OFF</th>
          <th class="text-center" style="color:#f0ad4e">HD</th>
          <th class="text-center text-danger">Ab</th>
          <th class="text-center">TOT</th>
          <th>Rate (Basic Wage)</th>
          <th>Total Monthly Wage</th>
          <th>Days in Period</th>
          <th class="text-success">Amount for Presents</th>
          <th class="text-danger">Shortfall Days</th>
          <th class="text-danger">Shortfall Amount</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $grandSalary = 0; $grandShortfall = 0; $grandTotalWage = 0;
        foreach ($desSummary as $des => $ds):
          $monthDays    = $totalDaysInPeriod ?: 1;
          $totalWage    = $ds['basic_wage'];
          $avgWage      = $ds['count'] > 0 ? $totalWage / $ds['count'] : 0;
          $earnedDays   = $ds['P'] + $ds['OT'] + $ds['OFF'] + ($ds['HD'] * 0.5);
          $fullDutyDays = $monthDays * $ds['count'];
          $shortDays    = max(0, $fullDutyDays - ($ds['P'] + $ds['OT'] + $ds['OFF'] + $ds['HD']));
          $dailyRate    = $monthDays > 0 ? $totalWage / $monthDays : 0;
          $amtForPres   = round($ds['salary_earned'] ?? ($earnedDays * $dailyRate), 2);
          $shortAmt     = round($shortDays * ($totalWage > 0 ? $totalWage / $monthDays / $ds['count'] : 0), 2);
          $grandSalary     += $amtForPres;
          $grandShortfall  += $shortAmt;
          $grandTotalWage  += $totalWage;
      ?>
      <tr>
        <td class="fw-semibold"><?= htmlspecialchars($des) ?> (<?= $ds['count'] ?>)</td>
        <td class="text-center text-success fw-bold"><?= $ds['P'] ?></td>
        <td class="text-center text-primary"><?= $ds['OT'] ?></td>
        <td class="text-center text-muted"><?= $ds['OFF'] ?></td>
        <td class="text-center fw-bold" style="color:#e67e22"><?= $ds['HD'] ?></td>
        <td class="text-center text-danger"><?= $ds['A'] ?></td>
        <td class="text-center fw-bold"><?= $ds['total_days'] ?></td>
        <td>₹<?= number_format($avgWage, 2) ?> / month/emp</td>
        <td class="fw-bold">₹<?= number_format($totalWage, 2) ?></td>
        <td class="text-center"><?= $monthDays ?></td>
        <td class="fw-bold text-success">₹<?= number_format($amtForPres, 2) ?></td>
        <td class="text-center text-danger"><?= $shortDays ?></td>
        <td class="text-danger">₹<?= number_format($shortAmt, 2) ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot class="table-secondary fw-bold">
        <tr>
          <td>GRAND TOTAL</td>
          <td class="text-center"><?= $allPres ?></td>
          <td class="text-center"><?= $allOT ?></td>
          <td class="text-center"><?= $allOFF ?></td>
          <td class="text-center"><?= $allHD ?></td>
          <td class="text-center"><?= $allAbs ?></td>
          <td class="text-center"><?= $allTot ?></td>
          <td>—</td>
          <td class="fw-bold">₹<?= number_format($grandTotalWage ?? 0, 2) ?></td>
          <td class="text-center"><?= $totalDaysInPeriod ?></td>
          <td class="text-success">₹<?= number_format($grandSalary, 2) ?></td>
          <td></td>
          <td class="text-danger">₹<?= number_format($grandShortfall, 2) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<?php elseif ($clientId): ?>
<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No attendance records found for the selected period.</div>
<?php else: ?>
<div class="alert alert-secondary"><i class="bi bi-arrow-up-circle me-2"></i>Select a client and date range above to view attendance.</div>
<?php endif; ?>

<style>
@media print {
  .no-print { display: none !important; }
  body { font-size: 10px; }
  table { font-size: 9px !important; }
}
</style>
