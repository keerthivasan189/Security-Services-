<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <a href="<?= u('clients/index?status='.$client['status']) ?>" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i>Back to Client List</a>
    <h5 class="mb-0 mt-1"><i class="bi bi-building me-2"></i><?= htmlspecialchars($client['company_name']) ?></h5>
  </div>
  <div class="d-flex gap-2">
    <a href="<?= u('clients/edit/'.$client['id']) ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    <a href="<?= u('clients/trades/'.$client['id']) ?>" class="btn btn-info btn-sm"><i class="bi bi-diagram-3 me-1"></i>Trades</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm no-print"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="stat-card"><div class="label">Status</div><div class="value"><span class="badge <?=$client['status']==='active'?'bg-success':($client['status']==='pre_client'?'bg-warning text-dark':'bg-secondary')?>" style="font-size:14px"><?=ucfirst(str_replace('_',' ',$client['status']))?></span></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Employees Deployed</div><div class="value text-primary"><?= count(array_filter($employees, fn($e)=>$e['status']==='active')) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Total Invoiced</div><div class="value text-success"><?= Helper::money($invoiceSummary['total_billed'] ?? 0) ?></div></div></div>
  <div class="col-md-3"><div class="stat-card"><div class="label">Outstanding</div><div class="value text-danger"><?= Helper::money($invoiceSummary['total_outstanding'] ?? 0) ?></div></div></div>
</div>

<!-- Tabbed Content -->
<ul class="nav nav-tabs mb-0" role="tablist">
  <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-info"><i class="bi bi-info-circle me-1"></i>Client Info</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-employees"><i class="bi bi-people me-1"></i>Assigned Employees (<?= count($employees) ?>)</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-trades"><i class="bi bi-diagram-3 me-1"></i>Trades (<?= count($trades) ?>)</a></li>
</ul>
<div class="tab-content card border-top-0">
  <!-- Info Tab -->
  <div class="tab-pane active p-3" id="tab-info">
    <div class="row g-3">
      <div class="col-md-6"><table class="table table-sm mb-0">
        <tr><td class="text-muted" width="35%">Client Code</td><td class="fw-semibold"><?= htmlspecialchars($client['client_code'] ?? '') ?></td></tr>
        <tr><td class="text-muted">Contact Person</td><td><?= htmlspecialchars($client['contact_person'] ?? '') ?></td></tr>
        <tr><td class="text-muted">Role</td><td><?= htmlspecialchars($client['role'] ?? '') ?></td></tr>
        <tr><td class="text-muted">Mobile</td><td><?= htmlspecialchars($client['mobile'] ?? '') ?></td></tr>
        <tr><td class="text-muted">WhatsApp</td><td><?= htmlspecialchars($client['whatsapp'] ?? '') ?></td></tr>
        <tr><td class="text-muted">Email</td><td><?= htmlspecialchars($client['email'] ?? '') ?></td></tr>
        <tr><td class="text-muted">GSTIN</td><td><?= htmlspecialchars($client['gstin'] ?? '') ?></td></tr>
      </table></div>
      <div class="col-md-6"><table class="table table-sm mb-0">
        <tr><td class="text-muted" width="35%">Address</td><td><?= nl2br(htmlspecialchars($client['address'] ?? '')) ?></td></tr>
        <tr><td class="text-muted">Branch</td><td><?= htmlspecialchars($client['branch'] ?? '') ?></td></tr>
        <tr><td class="text-muted">State / District</td><td><?= htmlspecialchars(($client['state']??'') . ' / ' . ($client['district']??'')) ?></td></tr>
        <tr><td class="text-muted">Bill Type</td><td><span class="badge bg-info text-dark"><?= $client['bill_type'] ?? '' ?></span></td></tr>
        <tr><td class="text-muted">Schedule</td><td><?= htmlspecialchars($client['invoice_schedule'] ?? '') ?></td></tr>
        <tr><td class="text-muted">Work Order</td><td><?= htmlspecialchars($client['work_order_no'] ?? '') ?></td></tr>
        <tr><td class="text-muted">Contract</td><td><?= ($client['contract_start']??'') ? date('d M Y',strtotime($client['contract_start'])).' → '.($client['contract_end']?date('d M Y',strtotime($client['contract_end'])):'—') : '—' ?></td></tr>
      </table></div>
    </div>
    <?php if (!empty($client['notes'])): ?><div class="mt-3 p-2 bg-light rounded small"><?= nl2br(htmlspecialchars($client['notes'])) ?></div><?php endif; ?>
  </div>

  <!-- Assigned Employees Tab -->
  <div class="tab-pane p-3" id="tab-employees">
    <table class="table table-hover table-sm datatable mb-0">
      <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Designation</th><th>Shift</th><th>Mobile</th><th>Appointed</th><th>Status</th></tr></thead>
      <tbody>
      <?php foreach ($employees as $e): ?>
      <tr>
        <td><span class="badge bg-secondary"><?= htmlspecialchars($e['emp_code']) ?></span></td>
        <td class="fw-semibold"><?= htmlspecialchars($e['name']) ?></td>
        <td class="small"><?= htmlspecialchars($e['designation']) ?></td>
        <td class="small"><?= htmlspecialchars($e['shift'] ?? '') ?></td>
        <td class="small"><?= htmlspecialchars($e['mobile'] ?? '') ?></td>
        <td class="small"><?= $e['appointed_date'] ? date('d M Y',strtotime($e['appointed_date'])) : '—' ?></td>
        <td><span class="badge <?=$e['status']==='active'?'bg-success':'bg-secondary'?>"><?= ucfirst($e['status']) ?></span></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Trades Tab -->
  <div class="tab-pane p-3" id="tab-trades">
    <table class="table table-sm mb-0">
      <thead class="table-light"><tr><th>Designation</th><th>Shift</th><th>Salary Basis</th><th>Rate</th><th>Payable</th><th>Positions</th></tr></thead>
      <tbody>
      <?php foreach ($trades as $t): ?>
      <tr>
        <td class="fw-semibold"><?= htmlspecialchars($t['designation']) ?></td>
        <td><?= htmlspecialchars($t['shift']) ?></td>
        <td><?= htmlspecialchars($t['salary_basis'] ?? $t['billing_mode']) ?></td>
        <td><?= Helper::money($t['rate']) ?></td>
        <td><?= Helper::money($t['payable']) ?></td>
        <td class="fw-bold"><?= $t['no_of_positions'] ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  </div>
</div>
