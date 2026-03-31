<?php
$stageLabels = [
    'new'           => ['label'=>'New',           'color'=>'#6c5ce7','icon'=>'bi-star'],
    'contacted'     => ['label'=>'Contacted',      'color'=>'#0984e3','icon'=>'bi-telephone'],
    'qualified'     => ['label'=>'Qualified',      'color'=>'#00b894','icon'=>'bi-check2-circle'],
    'proposal_sent' => ['label'=>'Proposal Sent',  'color'=>'#fdcb6e','icon'=>'bi-file-earmark-text'],
    'negotiation'   => ['label'=>'Negotiation',    'color'=>'#e17055','icon'=>'bi-chat-dots'],
    'won'           => ['label'=>'Won',            'color'=>'#00b894','icon'=>'bi-trophy'],
    'lost'          => ['label'=>'Lost',           'color'=>'#d63031','icon'=>'bi-x-circle'],
    'on_hold'       => ['label'=>'On Hold',        'color'=>'#b2bec3','icon'=>'bi-pause-circle'],
];
?>

<!-- Top KPI Cards -->
<div class="row g-2 g-md-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="label">Total Leads</div>
      <div class="value" style="color:#6c5ce7"><?= $totalLeads ?></div>
      <small class="text-muted">All time</small>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="label">Open Pipeline</div>
      <div class="value text-primary"><?= $totalOpen ?></div>
      <small class="text-muted">₹<?= number_format($pipelineVal/100000,2) ?>L value</small>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="label">Won</div>
      <div class="value text-success"><?= $totalWon ?></div>
      <small class="text-muted">₹<?= number_format($wonVal/100000,2) ?>L closed</small>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="label">Lost</div>
      <div class="value text-danger"><?= $totalLost ?></div>
      <small class="text-muted">
        <?php $tot = $totalWon + $totalLost; echo $tot ? round($totalWon/$tot*100).'% win rate' : 'No closed leads'; ?>
      </small>
    </div>
  </div>
</div>

<!-- Pipeline Funnel -->
<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="bi bi-funnel me-1"></i> Pipeline by Stage</span>
        <a href="<?= u('crm/leads') ?>" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="card-body p-3">
        <?php
        $openStages = ['new','contacted','qualified','proposal_sent','negotiation'];
        $maxCnt = 1;
        foreach ($openStages as $st) {
            $cnt = $stageCounts[$st]['cnt'] ?? 0;
            if ($cnt > $maxCnt) $maxCnt = $cnt;
        }
        foreach ($openStages as $st):
            $cnt = $stageCounts[$st]['cnt'] ?? 0;
            $val = $stageCounts[$st]['val'] ?? 0;
            $cfg = $stageLabels[$st];
            $pct = $maxCnt > 0 ? round($cnt / $maxCnt * 100) : 0;
        ?>
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span style="font-size:12px;font-weight:600"><i class="bi <?= $cfg['icon'] ?> me-1"></i><?= $cfg['label'] ?></span>
            <span style="font-size:12px;color:#888"><?= $cnt ?> leads &nbsp;·&nbsp; ₹<?= number_format($val/100000,2) ?>L</span>
          </div>
          <div class="progress" style="height:10px;border-radius:6px">
            <div class="progress-bar" style="width:<?= $pct ?>%;background:<?= $cfg['color'] ?>;border-radius:6px"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-bell me-1"></i> Today's Follow-ups (<?= count($todayFollowups) ?>)</div>
      <div class="card-body p-0" style="max-height:300px;overflow-y:auto">
        <?php if (empty($todayFollowups)): ?>
          <div class="text-center text-muted py-4" style="font-size:13px">No follow-ups today 🎉</div>
        <?php else: foreach ($todayFollowups as $l): ?>
        <a href="<?= u("crm/viewLead/{$l['id']}") ?>" class="d-flex align-items-start p-3 border-bottom text-decoration-none text-dark hover-bg">
          <div class="me-2">
            <?php $pc = $l['priority']==='high'?'danger':($l['priority']==='medium'?'warning':'secondary'); ?>
            <span class="badge bg-<?= $pc ?> rounded-circle" style="width:8px;height:8px;display:inline-block;padding:0">&nbsp;</span>
          </div>
          <div>
            <div style="font-size:13px;font-weight:600"><?= htmlspecialchars($l['company_name']) ?></div>
            <div style="font-size:11px;color:#888"><?= htmlspecialchars($l['contact_person']) ?> · <?= $l['mobile'] ?></div>
          </div>
          <span class="ms-auto badge rounded-pill" style="font-size:10px;background:#f0eeff;color:#6c5ce7"><?= $stageLabels[$l['status']]['label'] ?? $l['status'] ?></span>
        </a>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Hot Leads + Recent Activities -->
<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="bi bi-fire me-1 text-danger"></i> Hot Leads</span>
        <a href="<?= u('crm/leads') ?>&priority=high" class="btn btn-sm btn-outline-danger">View All</a>
      </div>
      <div class="card-body p-0">
        <?php if (empty($hotLeads)): ?>
          <div class="text-center text-muted py-4" style="font-size:13px">No high-priority leads</div>
        <?php else: foreach ($hotLeads as $l): ?>
        <a href="<?= u("crm/viewLead/{$l['id']}") ?>" class="d-flex justify-content-between align-items-center p-3 border-bottom text-decoration-none text-dark">
          <div>
            <div style="font-size:13px;font-weight:600"><?= htmlspecialchars($l['company_name']) ?></div>
            <div style="font-size:11px;color:#888"><?= $l['lead_code'] ?> · <?= $l['industry'] ?></div>
          </div>
          <div class="text-end">
            <div style="font-size:13px;font-weight:700;color:#6c5ce7">₹<?= number_format($l['expected_value']/100000,2) ?>L</div>
            <small class="text-muted"><?= $stageLabels[$l['status']]['label'] ?? $l['status'] ?></small>
          </div>
        </a>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-clock-history me-1"></i> Recent Activities</div>
      <div class="card-body p-0" style="max-height:350px;overflow-y:auto">
        <?php
        $actIcons = [
            'call'=>'bi-telephone','meeting'=>'bi-people','email'=>'bi-envelope',
            'whatsapp'=>'bi-whatsapp','site_visit'=>'bi-geo-alt','demo'=>'bi-display',
            'proposal'=>'bi-file-earmark-text','follow_up'=>'bi-arrow-repeat','note'=>'bi-sticky'
        ];
        foreach ($recentActivities as $a): ?>
        <a href="<?= u("crm/viewLead/{$a['lead_id']}") ?>" class="d-flex gap-2 p-3 border-bottom text-decoration-none text-dark">
          <span style="font-size:18px;color:#6c5ce7"><i class="bi <?= $actIcons[$a['activity_type']] ?? 'bi-dot' ?>"></i></span>
          <div>
            <div style="font-size:13px"><strong><?= ucfirst(str_replace('_',' ',$a['activity_type'])) ?></strong> — <?= htmlspecialchars($a['company_name']) ?></div>
            <div style="font-size:11px;color:#888"><?= htmlspecialchars($a['subject'] ?? '') ?> · <?= $a['activity_date'] ?></div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<!-- Overdue -->
<?php if (!empty($overdueFollowups)): ?>
<div class="card border-danger">
  <div class="card-header bg-danger text-white"><i class="bi bi-exclamation-triangle me-1"></i> Overdue Follow-ups (<?= count($overdueFollowups) ?>)</div>
  <div class="card-body p-0">
    <div class="table-responsive">
    <table class="table table-hover mb-0" style="font-size:13px">
      <thead class="table-light">
        <tr><th>Lead</th><th class="d-none d-sm-table-cell">Contact</th><th>Follow-up Date</th><th class="d-none d-md-table-cell">Status</th><th></th></tr>
      </thead>
      <tbody>
      <?php foreach ($overdueFollowups as $l): ?>
      <tr>
        <td><a href="<?= u("crm/viewLead/{$l['id']}") ?>"><?= htmlspecialchars($l['company_name']) ?></a></td>
        <td class="d-none d-sm-table-cell"><?= htmlspecialchars($l['contact_person']) ?> · <?= $l['mobile'] ?></td>
        <td class="text-danger fw-bold"><?= $l['follow_up_date'] ?></td>
        <td class="d-none d-md-table-cell"><span class="badge rounded-pill" style="background:#f0eeff;color:#6c5ce7"><?= $stageLabels[$l['status']]['label'] ?? $l['status'] ?></span></td>
        <td><a href="<?= u("crm/viewLead/{$l['id']}") ?>" class="btn btn-sm btn-outline-primary">Update</a></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>
</div>
<?php endif; ?>
