<?php
$statusLabel = strtoupper(str_replace('_','-',$status));
?>

<!-- Tab Navigation Counts -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <a href="<?= u('clients/index') ?>&status=pre_client" class="text-decoration-none">
      <div class="card border-0 shadow-sm <?= $status==='pre_client'?'bg-primary text-white':'bg-white text-muted' ?>" style="border-radius:10px">
        <div class="card-body p-3 text-center">
          <h6 class="mb-1 small fw-bold">PRE-CLIENTS</h6>
          <h3 class="mb-0 fw-bold"><?= $counts['pre_client'] ?></h3>
        </div>
      </div>
    </a>
  </div>
  <div class="col-md-3">
    <a href="<?= u('clients/index') ?>&status=active" class="text-decoration-none">
      <div class="card border-0 shadow-sm <?= $status==='active'?'bg-success text-white':'bg-white text-muted' ?>" style="border-radius:10px">
        <div class="card-body p-3 text-center">
          <h6 class="mb-1 small fw-bold">ACTIVE CLIENTS</h6>
          <h3 class="mb-0 fw-bold"><?= $counts['active'] ?></h3>
        </div>
      </div>
    </a>
  </div>
  <div class="col-md-3">
    <a href="<?= u('clients/index') ?>&status=inactive" class="text-decoration-none">
      <div class="card border-0 shadow-sm <?= $status==='inactive'?'bg-danger text-white':'bg-white text-muted' ?>" style="border-radius:10px">
        <div class="card-body p-3 text-center">
          <h6 class="mb-1 small fw-bold">RELIEVED CLIENTS</h6>
          <h3 class="mb-0 fw-bold"><?= $counts['inactive'] ?></h3>
        </div>
      </div>
    </a>
  </div>
  <div class="col-md-3">
    <a href="<?= u('clients/add') ?>" class="text-decoration-none">
      <div class="card border-0 shadow-sm bg-dark text-white" style="border-radius:10px">
        <div class="card-body p-3 text-center d-flex align-items-center justify-content-center" style="height: 100%;">
          <h6 class="mb-0 fw-bold"><i class="bi bi-plus-lg me-1"></i> ADD NEW CLIENT</h6>
        </div>
      </div>
    </a>
  </div>
</div>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0 text-uppercase fw-semibold" style="color:#666">LIST OF <?= $statusLabel ?>S</h5>
  <a href="<?= u('clients/index') ?>&status=<?= htmlspecialchars($status) ?>" class="btn btn-secondary btn-sm px-3 shadow-sm border-0" style="background:#64748b">
    REFRESH LIST
  </a>
</div>

<!-- Filter Panel -->
<div class="card mb-4 border-0 shadow-sm" style="border-radius:10px">
  <div class="card-body p-3">
    <form class="row g-2 align-items-center" method="GET" action="index.php">
      <input type="hidden" name="url" value="clients/index">
      <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
      
      <div class="col-md-2">
        <label class="small fw-bold text-muted mb-1">Branch</label>
        <select name="branch" class="form-select form-select-sm border-0 bg-light">
          <option value="">All Branches</option>
          <?php foreach ($branches as $b): ?>
            <option value="<?= $b ?>" <?= $branch===$b?'selected':'' ?>><?= $b ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="small fw-bold text-muted mb-1">Bill Type</label>
        <select name="bill_type" class="form-select form-select-sm border-0 bg-light">
          <option value="">All Types</option>
          <option value="GST" <?= $billType==='GST'?'selected':'' ?>>GST</option>
          <option value="RCM" <?= $billType==='RCM'?'selected':'' ?>>RCM</option>
          <option value="EXEMPT" <?= $billType==='EXEMPT'?'selected':'' ?>>EXEMPT</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="small fw-bold text-muted mb-1">Invoice Schedule</label>
        <select name="schedule" class="form-select form-select-sm border-0 bg-light">
          <option value="">All Schedules</option>
          <?php foreach ($schedules as $s): ?>
            <option value="<?= $s ?>" <?= $schedule===$s?'selected':'' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="small fw-bold text-muted mb-1">Search Keywords</label>
        <input type="text" name="search" class="form-control form-control-sm border-0 bg-light" placeholder="Name, Code, Contact..." value="<?= htmlspecialchars($search ?? '') ?>">
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-sm btn-primary w-100 shadow-sm border-0" style="background:#5a4fcf">APPLY FILTER</button>
      </div>
    </form>
  </div>
</div>

<!-- Client List -->
<div class="card border-0 shadow-sm" style="border-radius:10px">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:13px">
        <thead class="table-light text-muted">
          <tr>
            <th class="ps-3 border-0 fw-semibold">CODE</th>
            <th class="border-0 fw-semibold">COMPANY NAME</th>
            <th class="border-0 fw-semibold">CONTACT PERSON</th>
            <th class="border-0 fw-semibold">BILLING</th>
            <th class="border-0 fw-semibold">TRADES</th>
            <th class="border-0 fw-semibold">BRANCH</th>
            <th class="pe-3 border-0 fw-semibold text-center">ACTIONS</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($clients as $c): ?>
        <tr>
          <td class="ps-3 fw-bold text-muted"><?= htmlspecialchars($c['client_code']) ?></td>
          <td class="fw-bold"><a href="<?= u('clients/profile/'.$c['id']) ?>" class="text-decoration-none text-primary"><?= htmlspecialchars($c['company_name']) ?></a></td>
          <td class="text-dark">
            <div class="fw-semibold"><?= htmlspecialchars($c['contact_person'] ?? '') ?></div>
            <div class="text-muted small"><?= htmlspecialchars($c['mobile'] ?? '') ?></div>
          </td>
          <td>
            <span class="badge border text-dark fw-semibold" style="background:#f8f9fa"><?= htmlspecialchars($c['bill_type']) ?></span>
            <div class="text-muted small mt-1"><?= htmlspecialchars($c['invoice_schedule'] ?? '') ?></div>
          </td>
          <td class="text-center">
            <span class="badge bg-info text-dark fw-bold"><?= $c['total_trades'] ?></span>
          </td>
          <td class="fw-semibold text-muted small"><?= htmlspecialchars($c['branch'] ?? '') ?></td>
          <td class="pe-3 text-center">
            <div class="dropdown">
              <button class="btn btn-light btn-sm border dropdown-toggle" type="button" data-bs-toggle="dropdown" style="font-size:11px;padding:4px 10px;text-transform:uppercase;letter-spacing:0.5px">
                OPTIONS
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:13px">
                <li><a class="dropdown-item py-2" href="<?= u('clients/profile/'.$c['id']) ?>"><i class="bi bi-person-badge me-2"></i>View Profile</a></li>
                <li><a class="dropdown-item py-2" href="<?= u('clients/edit/'.$c['id']) ?>"><i class="bi bi-pencil-square me-2"></i>Edit Details</a></li>
                <div class="dropdown-divider"></div>
                <li><a class="dropdown-item py-2" href="<?= u('clients/trades/'.$c['id']) ?>&action=add"><i class="bi bi-plus-circle me-2"></i>Add New Trade</a></li>
                <li><a class="dropdown-item py-2" href="<?= u('clients/trades/'.$c['id']) ?>"><i class="bi bi-list-task me-2"></i>Manage Trades</a></li>
                <div class="dropdown-divider"></div>
                <?php if ($c['status'] === 'pre_client'): ?>
                  <li><a class="dropdown-item py-2 text-success fw-bold" href="<?= u('clients/migrate/'.$c['id']) ?>" onclick="return confirm('Migrate this pre-client to an active client?');"><i class="bi bi-check-circle-fill me-2"></i>Migrate to Client</a></li>
                <?php endif; ?>
                <li><a class="dropdown-item py-2 text-danger" href="<?= u('clients/delete/'.$c['id']) ?>" onclick="return confirm('WARNING: This will delete the client and all related trades/positions. Continue?')"><i class="bi bi-trash me-2"></i>Delete Client</a></li>
              </ul>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($clients)): ?>
          <tr><td colspan="7" class="text-center text-muted py-5">
            <i class="bi bi-inbox display-4 mb-3 d-block opacity-25"></i>
            No clients found in <strong><?= $statusLabel ?></strong> category.
          </td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
