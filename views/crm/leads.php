<?php
$statusConfig = [
    'new'           => ['label'=>'New',            'bg'=>'#f0eeff','color'=>'#6c5ce7'],
    'contacted'     => ['label'=>'Contacted',       'bg'=>'#e3f2fd','color'=>'#0984e3'],
    'qualified'     => ['label'=>'Qualified',       'bg'=>'#e8f5e9','color'=>'#00b894'],
    'proposal_sent' => ['label'=>'Proposal Sent',   'bg'=>'#fffde7','color'=>'#f9a825'],
    'negotiation'   => ['label'=>'Negotiation',     'bg'=>'#fbe9e7','color'=>'#e17055'],
    'won'           => ['label'=>'Won',             'bg'=>'#e8f5e9','color'=>'#2e7d32'],
    'lost'          => ['label'=>'Lost',            'bg'=>'#ffebee','color'=>'#d63031'],
    'on_hold'       => ['label'=>'On Hold',         'bg'=>'#f5f5f5','color'=>'#636e72'],
];
$priConfig = [
    'high'   => ['label'=>'High',   'bg'=>'#ffebee','color'=>'#d63031'],
    'medium' => ['label'=>'Medium', 'bg'=>'#fffde7','color'=>'#f9a825'],
    'low'    => ['label'=>'Low',    'bg'=>'#f0eeff','color'=>'#6c5ce7'],
];
$totalStatusCount = array_sum($statusCounts);
?>

<!-- Action Bar -->
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
  <h5 class="mb-0 fw-semibold text-uppercase" style="color:#666;font-size:13px"><i class="bi bi-people me-1"></i> CRM Leads (<?= count($leads) ?>)</h5>
  <div class="d-flex gap-2">
    <a href="<?= u('crm/kanban') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-kanban me-1"></i><span class="d-none d-sm-inline">Board</span></a>
    <a href="<?= u('crm/addLead') ?>" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Lead</a>
  </div>
</div>

<!-- Status Filter Tabs -->
<div class="d-flex flex-wrap gap-1 mb-3" style="overflow-x:auto;padding-bottom:4px;-webkit-overflow-scrolling:touch">
  <a href="<?= u('crm/leads') ?>" class="btn btn-sm <?= !$status ? 'btn-primary' : 'btn-outline-secondary' ?>">
    All <span class="badge bg-white text-dark ms-1"><?= $totalStatusCount ?></span>
  </a>
  <?php foreach ($statusConfig as $st => $cfg): ?>
  <a href="<?= u('crm/leads') ?>&status=<?= $st ?>" class="btn btn-sm" style="<?= $status===$st ? "background:{$cfg['color']};color:#fff;border-color:{$cfg['color']}" : "border:1px solid {$cfg['color']};color:{$cfg['color']}" ?>">
    <?= $cfg['label'] ?>
    <?php if (isset($statusCounts[$st])): ?>
    <span class="badge ms-1" style="background:rgba(0,0,0,.15)"><?= $statusCounts[$st] ?></span>
    <?php endif; ?>
  </a>
  <?php endforeach; ?>
</div>

<!-- Filter Form -->
<div class="card mb-3">
  <div class="card-body p-2 p-md-3">
    <form method="GET" action="<?= u('crm/leads') ?>" class="row g-2 align-items-end">
      <input type="hidden" name="url" value="crm/leads">
      <?php if ($status): ?><input type="hidden" name="status" value="<?= $status ?>"><?php endif; ?>
      <div class="col-12 col-sm-6 col-md-3">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search company, contact..." value="<?= htmlspecialchars($search) ?>">
      </div>
      <div class="col-6 col-sm-3 col-md-2">
        <select name="priority" class="form-select form-select-sm">
          <option value="">All Priority</option>
          <option value="high" <?= $priority==='high'?'selected':'' ?>>High</option>
          <option value="medium" <?= $priority==='medium'?'selected':'' ?>>Medium</option>
          <option value="low" <?= $priority==='low'?'selected':'' ?>>Low</option>
        </select>
      </div>
      <div class="col-6 col-sm-3 col-md-2">
        <input type="date" name="from" class="form-control form-control-sm" value="<?= $from ?>">
      </div>
      <div class="col-6 col-sm-3 col-md-2">
        <input type="date" name="to" class="form-control form-control-sm" value="<?= $to ?>">
      </div>
      <div class="col-6 col-sm-3 col-md-3 d-flex gap-2">
        <button class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i>Search</button>
        <a href="<?= u('crm/leads') ?>" class="btn btn-sm btn-outline-secondary flex-shrink-0">Clear</a>
      </div>
    </form>
  </div>
</div>

<!-- Table -->
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle" id="leadsTable" style="font-size:13px">
        <thead class="table-light">
          <tr>
            <th style="width:30px" class="d-none d-md-table-cell">#</th>
            <th>Lead</th>
            <th class="d-none d-sm-table-cell">Contact</th>
            <th class="d-none d-md-table-cell">Source</th>
            <th>Value</th>
            <th class="d-none d-sm-table-cell">Priority</th>
            <th>Status</th>
            <th class="d-none d-lg-table-cell">Follow-up</th>
            <th class="d-none d-lg-table-cell">Activity</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($leads)): ?>
          <tr><td colspan="10" class="text-center text-muted py-4">No leads found</td></tr>
          <?php else: foreach ($leads as $i => $l):
            $sc = $statusConfig[$l['status']] ?? ['label'=>$l['status'],'bg'=>'#eee','color'=>'#333'];
            $pc = $priConfig[$l['priority']] ?? ['label'=>$l['priority'],'bg'=>'#eee','color'=>'#333'];
            $overdue = $l['follow_up_date'] && $l['follow_up_date'] < date('Y-m-d') && !in_array($l['status'],['won','lost']);
          ?>
          <tr>
            <td class="text-muted d-none d-md-table-cell"><?= $i+1 ?></td>
            <td>
              <a href="<?= u("crm/viewLead/{$l['id']}") ?>" class="fw-semibold text-decoration-none" style="color:#2d3436">
                <?= htmlspecialchars($l['company_name']) ?>
              </a><br>
              <small class="text-muted"><?= $l['lead_code'] ?><?= $l['industry'] ? ' · '.$l['industry'] : '' ?></small>
            </td>
            <td class="d-none d-sm-table-cell">
              <?= htmlspecialchars($l['contact_person']) ?><br>
              <small class="text-muted"><?= $l['mobile'] ?></small>
            </td>
            <td class="d-none d-md-table-cell"><span class="badge rounded-pill" style="background:#f0eeff;color:#6c5ce7;font-size:11px"><?= $l['source'] ?></span></td>
            <td class="fw-semibold" style="color:#6c5ce7">
              <?= $l['expected_value'] > 0 ? '₹'.number_format($l['expected_value']/100000,2).'L' : '—' ?>
            </td>
            <td class="d-none d-sm-table-cell"><span class="badge rounded-pill" style="background:<?= $pc['bg'] ?>;color:<?= $pc['color'] ?>"><?= $pc['label'] ?></span></td>
            <td><span class="badge rounded-pill" style="background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>"><?= $sc['label'] ?></span></td>
            <td class="d-none d-lg-table-cell <?= $overdue ? 'text-danger fw-bold' : '' ?>">
              <?= $l['follow_up_date'] ?: '—' ?>
              <?php if ($overdue): ?><i class="bi bi-exclamation-circle text-danger"></i><?php endif; ?>
            </td>
            <td class="d-none d-lg-table-cell text-center">
              <span class="badge bg-secondary"><?= $l['activity_count'] ?></span>
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="<?= u("crm/viewLead/{$l['id']}") ?>" class="btn btn-xs btn-outline-primary" title="View" style="padding:4px 8px;font-size:12px"><i class="bi bi-eye"></i></a>
                <a href="<?= u("crm/editLead/{$l['id']}") ?>" class="btn btn-xs btn-outline-secondary" title="Edit" style="padding:4px 8px;font-size:12px"><i class="bi bi-pencil"></i></a>
                <?php if (!in_array($l['status'],['won','lost'])): ?>
                <a href="<?= u("crm/convertToClient/{$l['id']}") ?>" class="btn btn-xs btn-outline-success d-none d-sm-inline-flex" title="Convert" style="padding:4px 8px;font-size:12px"><i class="bi bi-arrow-right-circle"></i></a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
if (typeof $.fn.DataTable !== 'undefined') {
  $('#leadsTable').DataTable({ paging: true, pageLength: 25, ordering: true, searching: false, info: true });
}
</script>
