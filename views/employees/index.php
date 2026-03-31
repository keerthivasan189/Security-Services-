<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-people me-2"></i>Employee Master</h5>
  <a href="<?= u('employees/add') ?>" class="btn btn-primary btn-sm"><i class="bi bi-person-plus me-1"></i>Add Employee</a>
</div>

<!-- Category Tabs -->
<div class="row g-3 mb-3">
  <div class="col-md-4">
    <a href="<?= u('employees/index') ?>&status=pre_employee" class="text-decoration-none">
      <div class="stat-card <?= $status==='pre_employee'?'border-start border-warning border-4':'' ?>">
        <div class="d-flex align-items-center gap-2"><i class="bi bi-person-plus text-warning" style="font-size:1.3rem"></i><div>
          <div class="label">Pre Employees</div><div class="value text-warning"><?= $counts['pre_employee'] ?? 0 ?></div>
        </div></div>
      </div>
    </a>
  </div>
  <div class="col-md-4">
    <a href="<?= u('employees/index') ?>&status=active" class="text-decoration-none">
      <div class="stat-card <?= $status==='active'?'border-start border-success border-4':'' ?>">
        <div class="d-flex align-items-center gap-2"><i class="bi bi-person-check text-success" style="font-size:1.3rem"></i><div>
          <div class="label">Active Employees</div><div class="value text-success"><?= $counts['active'] ?></div>
        </div></div>
      </div>
    </a>
  </div>
  <div class="col-md-4">
    <a href="<?= u('employees/index') ?>&status=inactive" class="text-decoration-none">
      <div class="stat-card <?= $status==='inactive'?'border-start border-secondary border-4':'' ?>">
        <div class="d-flex align-items-center gap-2"><i class="bi bi-person-x text-secondary" style="font-size:1.3rem"></i><div>
          <div class="label">Relieved / Inactive</div><div class="value text-secondary"><?= $counts['inactive'] ?></div>
        </div></div>
      </div>
    </a>
  </div>
</div>

<!-- Filter Panel -->
<div class="card mb-3 shadow-sm border-0">
  <div class="card-header bg-dark text-white py-2 fw-semibold small d-flex justify-content-between align-items-center">
    <span><i class="bi bi-funnel me-1"></i>ADVANCED SEARCH & FILTERING (14+ CRITERIA)</span>
    <button class="btn btn-link btn-sm text-white p-0" type="button" data-bs-toggle="collapse" data-bs-target="#filterBody"><i class="bi bi-chevron-down"></i></button>
  </div>
  <div id="filterBody" class="collapse show">
    <div class="card-body py-3 bg-light">
      <form method="GET" action="<?= BASE_URL ?>/index.php">
        <input type="hidden" name="url" value="employees/index">
        <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
        
        <div class="row g-3">
          <!-- Row 1: Identifiers -->
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted mb-1">Search ID/Name/Mobile</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input name="search" class="form-control" placeholder="Code, Name, Aadhaar..." value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Designation</label>
            <select name="designation" class="form-select form-select-sm">
              <option value="">— All Designations —</option>
              <?php foreach ($masters['designations'] as $d): ?>
                <option value="<?=htmlspecialchars($d['name'])?>" <?=($desig??'')===$d['name']?'selected':''?>><?=htmlspecialchars($d['name'])?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Deployed Client</label>
            <select name="client_id" class="form-select form-select-sm">
              <option value="">— All Clients —</option>
              <?php foreach ($clList as $c): ?><option value="<?=$c['id']?>" <?=($clientId??'')==$c['id']?'selected':''?>><?=htmlspecialchars($c['company_name'])?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Field Officer</label>
            <select name="field_officer_id" class="form-select form-select-sm">
              <option value="">— All Officers —</option>
              <?php foreach ($masters['officers'] as $o): ?><option value="<?=$o['id']?>" <?=($officerId??'')==$o['id']?'selected':''?>><?=htmlspecialchars($o['name'])?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-1">
            <label class="form-label small fw-bold text-muted mb-1">Gender</label>
            <select name="gender" class="form-select form-select-sm">
              <option value="">All</option>
              <option value="Male" <?=($gender??'')==='Male'?'selected':''?>>M</option>
              <option value="Female" <?=($gender??'')==='Female'?'selected':''?>>F</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Qualification</label>
            <select name="qualification_id" class="form-select form-select-sm">
              <option value="">— All —</option>
              <?php foreach ($masters['qualifications'] as $q): ?><option value="<?=$q['id']?>" <?=($qual??'')==$q['id']?'selected':''?>><?=htmlspecialchars($q['name'])?></option><?php endforeach; ?>
            </select>
          </div>

          <!-- Row 2: Expectations & Location -->
          <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Min Expected Sal.</label>
            <input type="number" name="expected_salary" class="form-control form-control-sm" value="<?= htmlspecialchars($expSal ?? '') ?>" placeholder="Min. Amount">
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Pref. Company</label>
            <select name="expected_company_id" class="form-select form-select-sm">
              <option value="">— All —</option>
              <?php foreach ($clList as $c): ?><option value="<?=$c['id']?>" <?=($expComp??'')==$c['id']?'selected':''?>><?=htmlspecialchars($c['company_name'])?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Referred By</label>
            <select name="referred_by_id" class="form-select form-select-sm">
              <option value="">— All —</option>
              <?php foreach ($masters['employees'] as $re): ?><option value="<?=$re['id']?>" <?=($refBy??'')==$re['id']?'selected':''?>><?=htmlspecialchars($re['name'])?> (<?=$re['emp_code']?>)</option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Branch</label>
            <select name="branch_id" class="form-select form-select-sm">
              <option value="">— All —</option>
              <?php foreach ($masters['branches'] as $b): ?><option value="<?=$b['id']?>" <?=($branch??'')==$b['id']?'selected':''?>><?=htmlspecialchars($b['name'])?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Preferred Town</label>
            <select name="exp_town_id" class="form-select form-select-sm">
              <option value="">— All —</option>
              <?php foreach ($masters['towns'] as $t): ?><option value="<?=$t['id']?>" <?=($town??'')==$t['id']?'selected':''?>><?=htmlspecialchars($t['name'])?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold text-muted mb-1">Creation Date Range</label>
            <div class="input-group input-group-sm">
              <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate ?? '') ?>">
              <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate ?? '') ?>">
            </div>
          </div>

          <div class="col-12 text-center mt-3 pt-2 border-top">
            <button class="btn btn-primary btn-sm px-4 shadow-sm"><i class="bi bi-search me-1"></i>APPLY FILTERS</button>
            <a href="<?= u('employees/index') ?>&status=<?= $status ?>" class="btn btn-outline-secondary btn-sm ms-2">Reset All</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Employee List -->
<div class="card">
  <div class="card-header py-2"><span class="small fw-semibold"><?= count($employees) ?> employee(s) — <?= ucfirst(str_replace('_',' ',$status)) ?></span></div>
  <div class="card-body p-0"><table class="table table-hover datatable mb-0">
    <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Designation</th><th>DOJ</th><th>Mobile</th><th>Field Officer</th><th>Deployed At</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($employees as $e): ?>
    <tr>
      <td><span class="badge bg-secondary"><?= htmlspecialchars($e['emp_code']) ?></span></td>
      <td class="fw-semibold"><a href="<?= u('employees/profile/'.$e['id']) ?>" class="text-decoration-none"><?= htmlspecialchars($e['name']) ?></a></td>
      <td class="small"><?= htmlspecialchars($e['designation']) ?></td>
      <td class="small"><?= $e['doj'] ? date('d M Y', strtotime($e['doj'])) : '—' ?></td>
      <td class="small"><?= htmlspecialchars($e['mobile'] ?? '') ?></td>
      <td class="small"><?= htmlspecialchars($e['field_officer_name'] ?? '—') ?></td>
      <td class="small"><?= htmlspecialchars($e['deployed_at'] ?? 'Not Deployed') ?></td>
      <td><span class="badge <?=$e['status']==='active'?'bg-success':($e['status']==='pre_employee'?'bg-warning text-dark':'bg-secondary')?>"><?= ucfirst(str_replace('_',' ',$e['status'])) ?></span></td>
      <td class="text-nowrap text-end">
        <a href="<?= u('employees/profile/'.$e['id']) ?>" class="btn btn-xs btn-outline-primary py-0 px-2" title="Profile"><i class="bi bi-eye"></i></a>
        <a href="<?= u('employees/edit/'.$e['id']) ?>" class="btn btn-xs btn-outline-warning py-0 px-2" title="Edit"><i class="bi bi-pencil"></i></a>
        <?php if($e['status'] === 'pre_employee'): ?>
          <a href="<?= u('employees/migrate/'.$e['id']) ?>" class="btn btn-xs btn-success py-0 px-2" title="Migrate to Active"><i class="bi bi-arrow-repeat me-1"></i>Migrate</a>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if(empty($employees)): ?><tr><td colspan="9" class="text-center text-muted py-4">No employees found</td></tr><?php endif; ?>
    </tbody>
  </table></div>
</div>
