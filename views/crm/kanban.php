<?php
$stageConfig = [
    'new'           => ['label'=>'New',           'color'=>'#6c5ce7','bg'=>'#f0eeff','icon'=>'bi-star'],
    'contacted'     => ['label'=>'Contacted',      'color'=>'#0984e3','bg'=>'#e3f2fd','icon'=>'bi-telephone'],
    'qualified'     => ['label'=>'Qualified',      'color'=>'#00b894','bg'=>'#e8f5e9','icon'=>'bi-check2-circle'],
    'proposal_sent' => ['label'=>'Proposal Sent',  'color'=>'#f9a825','bg'=>'#fffde7','icon'=>'bi-file-earmark-text'],
    'negotiation'   => ['label'=>'Negotiation',    'color'=>'#e17055','bg'=>'#fbe9e7','icon'=>'bi-chat-dots'],
    'won'           => ['label'=>'Won',            'color'=>'#2e7d32','bg'=>'#e8f5e9','icon'=>'bi-trophy'],
    'lost'          => ['label'=>'Lost',           'color'=>'#d63031','bg'=>'#ffebee','icon'=>'bi-x-circle'],
];
?>

<style>
.kanban-board { display:flex; gap:12px; overflow-x:auto; padding-bottom:20px; min-height:70vh; -webkit-overflow-scrolling:touch; }
.kanban-board::-webkit-scrollbar { height:5px; }
.kanban-board::-webkit-scrollbar-thumb { background:#ccc; border-radius:4px; }
.kanban-col { min-width:210px; max-width:230px; flex-shrink:0; }
.kanban-col-header { border-radius:8px 8px 0 0; padding:10px 12px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; display:flex; justify-content:space-between; align-items:center; }
.kanban-col-body { background:#f4f6fa; border-radius:0 0 8px 8px; padding:8px; min-height:200px; }
.kanban-card { background:#fff; border-radius:8px; padding:12px; margin-bottom:8px; border:1px solid #e4e4e4; font-size:12px; transition:box-shadow .2s; cursor:pointer; -webkit-tap-highlight-color:rgba(108,92,231,.1); }
.kanban-card:hover, .kanban-card:active { box-shadow:0 4px 12px rgba(0,0,0,.1); border-color:#6c5ce7; }
.kanban-card .company { font-size:13px; font-weight:600; color:#2d3436; margin-bottom:4px; }
.kanban-card .meta { color:#888; font-size:11px; }
.kanban-card .value { font-weight:700; color:#6c5ce7; font-size:12px; }
.pri-high { border-left:3px solid #d63031 !important; }
.pri-medium { border-left:3px solid #f9a825 !important; }
.pri-low { border-left:3px solid #b2bec3 !important; }
.kanban-scroll-hint { display:none; font-size:11px; color:#aaa; text-align:center; padding:4px 0 8px; }
@media (max-width:767.98px) {
  .kanban-col { min-width:185px; max-width:200px; }
  .kanban-scroll-hint { display:block; }
  .kanban-topbar { flex-wrap:wrap; gap:8px !important; }
  .kanban-topbar h5 { font-size:14px; }
  #companyFilter { width:100% !important; }
  .kanban-topbar .btn-group-right { width:100%; display:flex; gap:6px; }
  .kanban-topbar .btn-group-right .btn { flex:1; }
}
</style>

<!-- Top Bar -->
<div class="d-flex align-items-center justify-content-between mb-3 kanban-topbar" style="gap:8px">
  <h5 class="mb-0 fw-semibold flex-shrink-0"><i class="bi bi-kanban me-2" style="color:#6c5ce7"></i>SAI SAKTHEESWARI</h5>
  <div class="d-flex flex-wrap gap-2 align-items-center">
    <select id="companyFilter" class="form-select form-select-sm" style="width:200px;min-width:140px">
      <option value="">All Companies</option>
      <?php foreach ($allCompanies as $company): ?>
        <option value="<?= htmlspecialchars($company['company_name']) ?>" <?= ($company_filter === $company['company_name']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($company['company_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <div class="btn-group-right d-flex gap-2">
      <a href="<?= u('crm/leads') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-list me-1"></i>List</a>
      <a href="<?= u('crm/addLead') ?>" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Lead</a>
    </div>
  </div>
</div>
<div class="kanban-scroll-hint"><i class="bi bi-arrow-left-right me-1"></i>Swipe to see all stages</div>

<script>
document.getElementById('companyFilter').addEventListener('change', function() {
  const company = this.value;
  const url = new URL(window.location);
  if (company) {
    url.searchParams.set('company', company);
  } else {
    url.searchParams.delete('company');
  }
  window.location = url.toString();
});
</script>

<!-- Legend -->
<div class="d-flex gap-3 mb-3" style="font-size:11px;color:#888">
  <span><span style="display:inline-block;width:10px;height:10px;background:#d63031;border-radius:2px;margin-right:4px"></span>High Priority</span>
  <span><span style="display:inline-block;width:10px;height:10px;background:#f9a825;border-radius:2px;margin-right:4px"></span>Medium Priority</span>
  <span><span style="display:inline-block;width:10px;height:10px;background:#b2bec3;border-radius:2px;margin-right:4px"></span>Low Priority</span>
</div>

<!-- Kanban Board -->
<div class="kanban-board">
  <?php foreach ($columns as $col):
    $cfg = $stageConfig[$col] ?? ['label'=>ucfirst($col),'color'=>'#636e72','bg'=>'#f5f5f5','icon'=>'bi-circle'];
    $colLeads = $board[$col] ?? [];
    $colValue = array_sum(array_column($colLeads, 'expected_value'));
  ?>
  <div class="kanban-col">
    <div class="kanban-col-header" style="background:<?= $cfg['color'] ?>;color:#fff">
      <span><i class="bi <?= $cfg['icon'] ?> me-1"></i><?= $cfg['label'] ?></span>
      <span class="badge" style="background:rgba(255,255,255,.25);color:#fff"><?= count($colLeads) ?></span>
    </div>
    <div class="kanban-col-body">
      <?php if ($colValue > 0): ?>
      <div class="text-center mb-2" style="font-size:11px;color:#888;font-weight:600">
        ₹<?= number_format($colValue/100000,2) ?>L pipeline
      </div>
      <?php endif; ?>

      <?php if (empty($colLeads)): ?>
        <div class="text-center text-muted py-4" style="font-size:12px">No leads</div>
      <?php else: foreach ($colLeads as $l): ?>
        <div class="kanban-card pri-<?= $l['priority'] ?>" onclick="window.location='<?= u("crm/viewLead/{$l['id']}") ?>'">
          <div class="company"><?= htmlspecialchars($l['company_name']) ?></div>
          <div class="meta mb-1"><?= htmlspecialchars($l['contact_person'] ?: '') ?> <?= $l['mobile'] ? '· '.$l['mobile'] : '' ?></div>
          <div class="d-flex justify-content-between align-items-center">
            <?php if ($l['expected_value'] > 0): ?>
            <span class="value">₹<?= number_format($l['expected_value']/100000,2) ?>L/mo</span>
            <?php else: ?>
            <span></span>
            <?php endif; ?>
            <?php if ($l['activity_count'] > 0): ?>
            <span style="font-size:10px;color:#888"><i class="bi bi-clock me-1"></i><?= $l['activity_count'] ?></span>
            <?php endif; ?>
          </div>
          <?php if ($l['follow_up_date'] && $l['follow_up_date'] <= date('Y-m-d')): ?>
          <div class="mt-1" style="font-size:10px;color:<?= $l['follow_up_date'] < date('Y-m-d') ? '#d63031' : '#00b894' ?>">
            <i class="bi bi-calendar-event me-1"></i>
            <?= $l['follow_up_date'] < date('Y-m-d') ? 'Overdue: ' : 'Today: ' ?><?= $l['follow_up_date'] ?>
          </div>
          <?php endif; ?>
          <?php if ($l['assigned_to']): ?>
          <div class="mt-1" style="font-size:10px;color:#888"><i class="bi bi-person me-1"></i><?= htmlspecialchars($l['assigned_to']) ?></div>
          <?php endif; ?>
        </div>
      <?php endforeach; endif; ?>

      <a href="<?= u('crm/addLead') ?>" class="btn btn-sm w-100 mt-1" style="border:1px dashed #ccc;color:#888;font-size:11px">
        <i class="bi bi-plus-lg me-1"></i>Add Lead
      </a>
    </div>
  </div>
  <?php endforeach; ?>
</div>
