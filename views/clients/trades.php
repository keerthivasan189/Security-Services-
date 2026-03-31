<?php
$showForm = isset($_GET['action']) && $_GET['action'] === 'add';
?>
<?php if($showForm): ?>

<!-- ADD TRADES VIEW -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0 text-uppercase fw-semibold" style="color:#666">
    <?= isset($et['id']) ? '<i class="bi bi-pencil-square me-2"></i>EDITING TRADE' : '<i class="bi bi-plus-circle me-2"></i>ADD NEW TRADE' ?> 
    IN <?= htmlspecialchars($client['company_name']) ?>
  </h5>
  <a href="<?= u('clients/trades/'.$client['id']) ?>" class="btn btn-primary btn-sm px-3" style="background:#5a4fcf;border-color:#5a4fcf">
    VIEW ALL TRADES
  </a>
</div>

<div class="card mb-4 border-0 shadow-sm" style="border-radius:10px">
  <div class="card-body p-4">
    <?php $et = $editTrade ?? []; ?>
    <form method="POST">
      <input type="hidden" name="action" value="<?= isset($et['id']) ? 'edit_trade' : 'add_trade' ?>">
      <?php if(isset($et['id'])): ?>
        <input type="hidden" name="trade_id" value="<?= $et['id'] ?>">
      <?php endif; ?>
      
      <div class="row g-3 mb-4">
        <div class="col-md-2">
          <label class="form-label fw-semibold small text-muted">Designation: <span class="text-danger">*</span></label>
          <select name="designation" class="form-select form-select-sm border-0 bg-light" required>
            <option value="">---------</option>
            <?php foreach(['HOUSE KEEPING','SECURITY GUARD','LADY SECURITY GUARD','SECURITY OFFICER','AREA OFFICER','DRIVER','OFFICE BOY','SR HR & ADMIN'] as $d): ?>
            <option <?= ($et['designation']??'')===$d?'selected':'' ?>><?= $d ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold small text-muted">Salary Basis: <span class="text-danger">*</span></label>
          <select name="salary_basis" class="form-select form-select-sm border-0 bg-light" required>
            <option <?= ($et['salary_basis']??'')==='PRO MONTH'?'selected':'' ?>>PRO MONTH</option>
            <option <?= ($et['salary_basis']??'')==='PER DAY'?'selected':'' ?>>PER DAY</option>
            <option <?= ($et['salary_basis']??'')==='PER HOUR'?'selected':'' ?>>PER HOUR</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold small text-muted">No of Post: <span class="text-danger">*</span></label>
          <input type="number" name="no_of_positions" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($et['no_of_positions'] ?? '0') ?>" required>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold small text-muted">Shift: <span class="text-danger">*</span></label>
          <select name="shift" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" required>
            <?php foreach(['NIGHT SHIFT','DAY SHIFT','GENERAL SHIFT','A SHIFT','B SHIFT','C SHIFT'] as $s): ?>
            <option <?= ($et['shift']??'')===$s?'selected':'' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
           <label class="form-label fw-semibold small text-muted">Rate/Each: <span class="text-danger">*</span></label>
           <input type="number" step="0.01" name="rate" class="form-control form-control-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" value="<?= htmlspecialchars($et['rate'] ?? '0.00') ?>" required>
        </div>
        <div class="col-md-2">
           <label class="form-label fw-semibold small text-muted">PaySalary/Each: <span class="text-danger">*</span></label>
           <input type="number" step="0.01" name="payable" class="form-control form-control-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" value="<?= htmlspecialchars($et['payable'] ?? '0.00') ?>" required>
        </div>

        <div class="col-md-3 mt-4">
           <label class="form-label fw-semibold small text-muted">EPF Amount:</label>
           <input type="number" step="0.01" name="epf_amount" class="form-control form-control-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" value="<?= htmlspecialchars($et['epf_amount'] ?? '0.00') ?>">
        </div>
        <div class="col-md-3 mt-4">
           <label class="form-label fw-semibold small text-muted">ESI Amount:</label>
           <input type="number" step="0.01" name="esi_amount" class="form-control form-control-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" value="<?= htmlspecialchars($et['esi_amount'] ?? '0.00') ?>">
        </div>
        <div class="col-md-3 mt-4">
          <label class="form-label fw-semibold small text-muted">Days for Incentives:</label>
          <select name="days_for_incentives" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
            <option <?= ($et['days_for_incentives']??'')==='Full Month'?'selected':'' ?>>Full Month</option>
            <option <?= ($et['days_for_incentives']??'')==='No Incentive'?'selected':'' ?>>No Incentive</option>
          </select>
        </div>
        <div class="col-md-3 mt-4">
           <label class="form-label fw-semibold small text-muted">Attendance Incentive:</label>
           <input type="number" step="0.01" name="attendance_incentive" class="form-control form-control-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" value="<?= htmlspecialchars($et['attendance_incentive'] ?? '0.00') ?>">
        </div>

        <div class="col-12 mt-4">
          <label class="form-label fw-semibold small text-muted">Remarks:</label>
          <textarea name="remarks" class="form-control border-0 bg-light" rows="3"><?= htmlspecialchars($et['remarks'] ?? '') ?></textarea>
        </div>

        <div class="col-12 mt-4">
          <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" style="background:#5a4fcf;border:none">
            <?= isset($et['id']) ? 'UPDATE TRADE' : 'SAVE NEW TRADE' ?>
          </button>
          <?php if(isset($et['id'])): ?>
            <a href="<?= u('clients/trades/'.$client['id']) ?>&action=add" class="btn btn-link link-secondary btn-sm ms-2 text-decoration-none">Cancel Edit</a>
          <?php endif; ?>
        </div>
      </div>

    </form>
  </div>
</div>

<?php else: ?>

<!-- LIST TRADES VIEW -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0 text-uppercase fw-semibold" style="color:#666">REQUIREMENT LIST (TRADES) IN <?= htmlspecialchars($client['company_name']) ?></h5>
  <div>
    <?php $btnLabel = ($client['status'] ?? 'pre_client') === 'pre_client' ? 'PRE-CLIENT' : 'CLIENT'; ?>
    <a href="<?= u('clients/index') ?>&status=<?= htmlspecialchars($client['status'] ?? 'pre_client') ?>" class="btn btn-primary btn-sm px-3 mx-1" style="background:#8b5cf6;border-color:#8b5cf6">
      <i class="bi bi-arrow-left me-1"></i> <?= $btnLabel ?> LIST
    </a>
    <a href="<?= u('clients/trades/'.$client['id']) ?>&action=add" class="btn btn-secondary btn-sm px-3 mx-1" style="background:#64748b;border-color:#64748b">
      <i class="bi bi-person-plus me-1"></i> ADD TRADE
    </a>
    <button class="btn btn-dark btn-sm px-3 ms-1" style="background:#334155;border-color:#334155" onclick="window.print()">
      <i class="bi bi-printer me-1"></i> PRINT QUOTATION
    </button>
  </div>
</div>

<div class="card mb-4 border-0 shadow-sm" style="border-radius:10px">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:13px">
        <thead class="table-light text-muted">
          <tr>
            <th class="ps-3 border-0 fw-semibold">Designation</th>
            <th class="border-0 fw-semibold">Basis / Shift</th>
            <th class="border-0 fw-semibold text-center">Positions (Total/Rem)</th>
            <th class="border-0 fw-semibold text-center">Assigned Staff</th>
            <th class="border-0 fw-semibold text-end">Rate</th>
            <th class="border-0 fw-semibold text-end pe-3">Payable</th>
            <th class="border-0 fw-semibold text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php 
        $totalPositions = 0;
        $totalAssigned = 0;
        $totalRate = 0;
        $totalPayable = 0;
        foreach ($trades as $t): 
            $pos = (int)$t['no_of_positions'];
            $asgn_count = count($t['assignments']);
            $rem = $pos - $asgn_count;
            $rateSum = $t['rate'] * $pos;
            $paySum = $t['payable'] * $pos;
            
            $totalPositions += $pos;
            $totalAssigned += $asgn_count;
            $totalRate += $rateSum;
            $totalPayable += $paySum;
        ?>
        <tr>
          <td class="ps-3 fw-bold text-dark">
            <?= htmlspecialchars($t['designation']) ?>
            <div class="text-muted small fw-normal"><?= htmlspecialchars($t['remarks'] ?? '') ?></div>
          </td>
          <td class="fw-semibold text-dark">
            <div><?= htmlspecialchars($t['salary_basis']) ?></div>
            <div class="text-primary small"><?= htmlspecialchars($t['shift']) ?></div>
          </td>
          <td class="text-center">
            <div class="fw-bold text-dark fs-6"><?= $pos ?> / <span class="<?= $rem > 0 ? 'text-danger' : 'text-success' ?>"><?= $rem ?></span></div>
            <div class="text-muted small" style="font-size:10px">Total / Remaining</div>
          </td>
          <td>
            <?php foreach ($t['assignments'] as $asgn): ?>
              <div class="d-flex align-items-center mb-1">
                <span class="badge bg-light text-dark border me-1 fw-normal" style="font-size:11px"><?= htmlspecialchars($asgn['emp_code']) ?></span>
                <span class="fw-semibold"><?= htmlspecialchars($asgn['name']) ?></span>
                <?php if(!empty($asgn['prev_site'])): ?>
                  <span class="text-success ms-1" style="font-size:10px" title="Transferred from <?= htmlspecialchars($asgn['prev_site']) ?>"><i class="bi bi-arrow-left-right me-1"></i>From: <?= htmlspecialchars($asgn['prev_site']) ?></span>
                <?php endif; ?>
                <form method="POST" style="display:inline" class="ms-2">
                  <input type="hidden" name="action" value="unassign_staff">
                  <input type="hidden" name="position_id" value="<?= $asgn['position_id'] ?>">
                  <button type="submit" class="btn btn-link btn-sm text-danger p-0 border-0" onclick="return confirm('Remove this staff from trade?')"><i class="bi bi-x-circle"></i></button>
                </form>
              </div>
            <?php endforeach; ?>
            <?php if(empty($t['assignments'])): ?>
              <span class="text-muted small italic">Not Assigned</span>
            <?php endif; ?>
          </td>
          <td class="fw-semibold text-dark text-end"><?= Helper::money($t['rate']) ?></td>
          <td class="fw-semibold text-dark text-end pe-3"><?= Helper::money($t['payable']) ?></td>
          <td class="text-center">
            <?php if($rem > 0): ?>
               <button class="btn btn-sm btn-outline-primary py-1 px-2 me-1" data-bs-toggle="modal" data-bs-target="#assignModal<?= $t['id'] ?>" title="Assign Staff"><i class="bi bi-person-plus-fill"></i></button>
            <?php else: ?>
               <button class="btn btn-sm btn-outline-secondary py-1 px-2 me-1 disabled" title="Requirement Fulfilled"><i class="bi bi-check-all"></i></button>
            <?php endif; ?>
            <a href="<?= u('clients/trades/'.$client['id']) ?>&action=add&edit_id=<?= $t['id'] ?>" class="btn btn-sm btn-light border py-1 px-2 me-1"><i class="bi bi-pencil-square"></i></a>
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="delete_trade">
              <input type="hidden" name="trade_id" value="<?= $t['id'] ?>">
              <button type="submit" class="btn btn-sm btn-light border py-1 px-2" onclick="return confirm('Delete this trade?')"><i class="bi bi-trash-fill"></i></button>
            </form>
          </td>
        </tr>

        <!-- Assign Modal for each trade -->
        <div class="modal fade" id="assignModal<?= $t['id'] ?>" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <form method="POST">
                <input type="hidden" name="action" value="assign_staff">
                <input type="hidden" name="trade_id" value="<?= $t['id'] ?>">
                <div class="modal-header bg-light">
                  <h6 class="modal-title">Assign Staff to <?= htmlspecialchars($t['designation']) ?> (<?= htmlspecialchars($t['shift']) ?>)</h6>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label small fw-bold">Select Employee</label>
                    <select name="employee_id" class="form-select border-primary" required>
                      <option value="">-- Choose Active Employee --</option>
                      <?php foreach($allEmployees as $emp): ?>
                        <?php 
                          $isAssigned = !empty($emp['current_site']);
                          $warning = $isAssigned ? " [ALREADY AT ".strtoupper($emp['current_site'])."]" : "";
                          $isMatch = stripos($emp['designation'] ?? '', $t['designation'] ?? '') !== false;
                        ?>
                        <option value="<?= $emp['id'] ?>" <?= $isAssigned ? 'style="color:red"' : '' ?> <?= $isMatch ? 'class="fw-bold bg-light"' : '' ?>>
                          <?= htmlspecialchars($emp['emp_code'] . ' - ' . $emp['name']) ?> 
                          (<?= htmlspecialchars($emp['designation'] ?? 'No Desig.') ?>)
                          <?= $isMatch ? " [ MATCH ]" : "" ?>
                          <?= $warning ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <?php if(!empty($allEmployees)): ?>
                       <div class="form-text small text-danger"><i class="bi bi-exclamation-triangle"></i> Red names indicate employees already assigned to another site.</div>
                    <?php endif; ?>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small fw-bold">Appointment Date</label>
                    <input type="date" name="appointed_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary btn-sm">Confirm Assignment</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(!empty($trades)): ?>
        <tr class="table-group-divider" style="border-top-width: 2px;">
          <td colspan="2" class="text-end fw-bold">TOTALS:</td>
          <td class="fw-bold text-dark text-center"><?= $totalPositions ?> / <?= $totalPositions - $totalAssigned ?></td>
          <td><span class="badge bg-success small"><?= $totalAssigned ?> Assigned</span></td>
          <td class="fw-bold text-dark text-end"><?= Helper::money($totalRate) ?></td>
          <td class="fw-bold text-dark text-end pe-3"><?= Helper::money($totalPayable) ?></td>
          <td></td>
        </tr>
        <?php else: ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No trades configured for this client.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php endif; ?>
