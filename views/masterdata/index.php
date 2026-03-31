<h5 class="mb-3"><i class="bi bi-gear me-2"></i>Master Data Management</h5>
<p class="text-muted small mb-3">Configure all master lists used across the HRMS system. Click any card to manage.</p>

<div class="row g-3">
<?php foreach ($counts as $slug => $c): ?>
  <div class="col-md-3 col-sm-4 col-6">
    <a href="<?= u('masterdata/manage/' . $slug) ?>" class="text-decoration-none">
      <div class="stat-card h-100" style="border-left:4px solid <?= $c['color'] ?>">
        <div class="d-flex align-items-center gap-2 mb-1">
          <i class="bi <?= $c['icon'] ?>" style="font-size:1.3rem;color:<?= $c['color'] ?>"></i>
          <span class="label mb-0" style="text-transform:none;letter-spacing:0"><?= htmlspecialchars($c['label']) ?></span>
        </div>
        <div class="value" style="font-size:22px;color:<?= $c['color'] ?>"><?= $c['count'] ?></div>
        <small class="text-muted">records</small>
      </div>
    </a>
  </div>
<?php endforeach; ?>
</div>
