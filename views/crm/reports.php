<!-- Reports Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0 fw-semibold"><i class="bi bi-bar-chart me-2" style="color:#6c5ce7"></i>CRM Reports</h5>
  <a href="<?= u('crm/dashboard') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
</div>

<!-- Date Filter -->
<div class="card mb-4">
  <div class="card-body p-2 p-md-3">
    <form method="GET" action="<?= u('crm/reports') ?>" class="row g-2 align-items-end">
      <input type="hidden" name="url" value="crm/reports">
      <div class="col-6 col-md-3">
        <label class="form-label small fw-semibold">From</label>
        <input type="date" name="from" class="form-control form-control-sm" value="<?= $from ?>">
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label small fw-semibold">To</label>
        <input type="date" name="to" class="form-control form-control-sm" value="<?= $to ?>">
      </div>
      <div class="col-12 col-md-6 d-flex flex-wrap gap-1 align-items-end">
        <button class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
        <a href="<?= u('crm/reports') ?>&from=<?= date('Y-m-01') ?>&to=<?= date('Y-m-d') ?>" class="btn btn-sm btn-outline-secondary">This Month</a>
        <a href="<?= u('crm/reports') ?>&from=<?= date('Y-m-01', strtotime('-1 month')) ?>&to=<?= date('Y-m-t', strtotime('-1 month')) ?>" class="btn btn-sm btn-outline-secondary">Last Month</a>
        <a href="<?= u('crm/reports') ?>&from=<?= date('Y-01-01') ?>&to=<?= date('Y-m-d') ?>" class="btn btn-sm btn-outline-secondary">This Year</a>
      </div>
    </form>
  </div>
</div>

<!-- KPI Summary -->
<?php
$totalPeriod = array_sum(array_column($byStatus, 'cnt'));
$wonRow  = array_values(array_filter($byStatus, fn($r) => $r['status']==='won'))[0]  ?? ['cnt'=>0,'val'=>0];
$lostRow = array_values(array_filter($byStatus, fn($r) => $r['status']==='lost'))[0] ?? ['cnt'=>0,'val'=>0];
$totalVal = array_sum(array_column($byStatus, 'val'));
?>
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card text-center">
      <div class="label">Total Leads</div>
      <div class="value" style="color:#6c5ce7"><?= $totalPeriod ?></div>
      <small class="text-muted"><?= $from ?> to <?= $to ?></small>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card text-center">
      <div class="label">Won</div>
      <div class="value text-success"><?= $wonRow['cnt'] ?></div>
      <small class="text-muted">₹<?= number_format($wonRow['val']/100000,2) ?>L value</small>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card text-center">
      <div class="label">Conversion Rate</div>
      <div class="value" style="color:#00b894"><?= $conversionRate ?>%</div>
      <small class="text-muted">won / total</small>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card text-center">
      <div class="label">Total Pipeline Value</div>
      <div class="value" style="color:#0984e3" style="font-size:20px">₹<?= number_format($totalVal/100000,2) ?>L</div>
      <small class="text-muted">All leads in period</small>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">

  <!-- By Source -->
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-funnel me-1"></i> Leads by Source</div>
      <div class="card-body p-3">
        <?php
        $maxSrc = max([1, ...array_column($bySource, 'cnt')]);
        foreach ($bySource as $row):
          $pct = round($row['cnt'] / $maxSrc * 100);
        ?>
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1" style="font-size:12px">
            <span class="fw-semibold"><?= $row['source'] ?></span>
            <span class="text-muted"><?= $row['cnt'] ?> leads · ₹<?= number_format($row['val']/100000,2) ?>L</span>
          </div>
          <div class="progress" style="height:8px;border-radius:4px">
            <div class="progress-bar" style="width:<?= $pct ?>%;background:#6c5ce7;border-radius:4px"></div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($bySource)): ?><div class="text-muted text-center py-3" style="font-size:13px">No data</div><?php endif; ?>
      </div>
    </div>
  </div>

  <!-- By Status -->
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-pie-chart me-1"></i> Leads by Status</div>
      <div class="card-body p-3">
        <?php
        $statusConfig = [
            'new'=>['New','#6c5ce7'],'contacted'=>['Contacted','#0984e3'],
            'qualified'=>['Qualified','#00b894'],'proposal_sent'=>['Proposal Sent','#f9a825'],
            'negotiation'=>['Negotiation','#e17055'],'won'=>['Won','#2e7d32'],
            'lost'=>['Lost','#d63031'],'on_hold'=>['On Hold','#636e72']
        ];
        foreach ($byStatus as $row):
          $cfg = $statusConfig[$row['status']] ?? [ucfirst($row['status']),'#888'];
          $pct = $totalPeriod > 0 ? round($row['cnt'] / $totalPeriod * 100) : 0;
        ?>
        <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:12px">
          <span>
            <span class="rounded-circle d-inline-block me-1" style="width:8px;height:8px;background:<?= $cfg[1] ?>"></span>
            <?= $cfg[0] ?>
          </span>
          <span>
            <strong><?= $row['cnt'] ?></strong>
            <span class="text-muted ms-1">(<?= $pct ?>%)</span>
            <span class="ms-2" style="color:<?= $cfg[1] ?>">₹<?= number_format($row['val']/100000,2) ?>L</span>
          </span>
        </div>
        <?php endforeach; ?>
        <?php if (empty($byStatus)): ?><div class="text-muted text-center py-3" style="font-size:13px">No data</div><?php endif; ?>
      </div>
    </div>
  </div>

  <!-- By Assigned To -->
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-person-badge me-1"></i> Performance by Sales Person</div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0" style="font-size:12px">
          <thead class="table-light">
            <tr><th>Name</th><th class="text-center">Leads</th><th class="text-center">Won</th><th>Value</th></tr>
          </thead>
          <tbody>
            <?php foreach ($byAssigned as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['assigned_to'] ?: 'Unassigned') ?></td>
              <td class="text-center fw-semibold"><?= $row['cnt'] ?></td>
              <td class="text-center text-success fw-semibold"><?= $row['won_cnt'] ?></td>
              <td style="color:#6c5ce7">₹<?= number_format($row['val']/100000,2) ?>L</td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($byAssigned)): ?><tr><td colspan="4" class="text-center text-muted py-3">No data</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Recent Wins -->
<?php if (!empty($recentWins)): ?>
<div class="card">
  <div class="card-header"><i class="bi bi-trophy me-1 text-success"></i> Recent Wins</div>
  <div class="card-body p-0">
    <div class="table-responsive">
    <table class="table table-hover mb-0" style="font-size:13px">
      <thead class="table-light">
        <tr>
          <th>Company</th>
          <th class="d-none d-sm-table-cell">Contact</th>
          <th class="d-none d-md-table-cell">Source</th>
          <th>Value/Month</th>
          <th class="d-none d-md-table-cell">Assigned To</th>
          <th class="d-none d-sm-table-cell">Won Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentWins as $l): ?>
        <tr>
          <td><a href="<?= u("crm/viewLead/{$l['id']}") ?>"><?= htmlspecialchars($l['company_name']) ?></a></td>
          <td class="d-none d-sm-table-cell"><?= htmlspecialchars($l['contact_person']) ?> · <?= $l['mobile'] ?></td>
          <td class="d-none d-md-table-cell"><?= $l['source'] ?></td>
          <td class="fw-bold text-success">₹<?= number_format($l['expected_value']) ?></td>
          <td class="d-none d-md-table-cell"><?= htmlspecialchars($l['assigned_to'] ?: '—') ?></td>
          <td class="d-none d-sm-table-cell"><?= date('d M Y', strtotime($l['updated_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>
</div>
<?php endif; ?>
