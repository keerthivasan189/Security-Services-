<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0 fw-semibold"><i class="bi bi-pencil me-2" style="color:#6c5ce7"></i>Edit Lead — <?= htmlspecialchars($lead['company_name']) ?></h5>
  <a href="<?= u("crm/viewLead/{$lead['id']}") ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="<?= u("crm/editLead/{$lead['id']}") ?>">
<div class="row g-3">

  <div class="col-12">
    <div class="card">
      <div class="card-header bg-white" style="border-left:4px solid #6c5ce7"><i class="bi bi-building me-1"></i> Company Information</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
            <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($lead['company_name']) ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Contact Person</label>
            <input type="text" name="contact_person" class="form-control" value="<?= htmlspecialchars($lead['contact_person']) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Industry</label>
            <input type="text" name="industry" class="form-control" value="<?= htmlspecialchars($lead['industry']) ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Mobile</label>
            <input type="text" name="mobile" class="form-control" value="<?= $lead['mobile'] ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= $lead['phone'] ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($lead['email']) ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">District</label>
            <input type="text" name="district" class="form-control" value="<?= htmlspecialchars($lead['district']) ?>" list="district-list">
            <datalist id="district-list">
              <?php foreach ($crmDistricts as $d): ?>
              <option><?= htmlspecialchars($d) ?></option>
              <?php endforeach; ?></datalist>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Address</label>
            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($lead['address']) ?></textarea>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">State</label>
            <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($lead['state']) ?>" list="state-list">
            <datalist id="state-list">
              <?php foreach ($crmStates as $st): ?>
              <option><?= htmlspecialchars($st) ?></option>
              <?php endforeach; ?></datalist>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Pincode</label>
            <input type="text" name="pincode" class="form-control" value="<?= $lead['pincode'] ?>">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-header bg-white" style="border-left:4px solid #0984e3"><i class="bi bi-graph-up me-1"></i> Lead Details</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label fw-semibold">Lead Source</label>
            <select name="source" class="form-select">
              <?php
              $srcList = !empty($crmSources) ? $crmSources : ['Cold Call','Reference','Walk-in','Online','Social Media','Exhibition','Email Campaign','Other'];
              foreach ($srcList as $src):
              ?>
              <option value="<?= htmlspecialchars($src) ?>" <?= $lead['source']===$src?'selected':'' ?>><?= htmlspecialchars($src) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Reference Name</label>
            <input type="text" name="reference_name" class="form-control" value="<?= htmlspecialchars($lead['reference_name']) ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Assigned To</label>
            <input type="text" name="assigned_to" class="form-control" value="<?= htmlspecialchars($lead['assigned_to']) ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Follow-up Date</label>
            <input type="date" name="follow_up_date" class="form-control" value="<?= $lead['follow_up_date'] ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Service Needed</label>
            <textarea name="service_needed" class="form-control" rows="2"><?= htmlspecialchars($lead['service_needed']) ?></textarea>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-semibold">Expected Strength</label>
            <input type="number" name="expected_strength" class="form-control" value="<?= $lead['expected_strength'] ?>">
          </div>
          <div class="col-md-2">
            <label class="form-label fw-semibold">Monthly Value (₹)</label>
            <input type="number" name="expected_value" class="form-control" step="0.01" value="<?= $lead['expected_value'] ?>">
          </div>
          <div class="col-md-2">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
              <?php foreach(['new','contacted','qualified','proposal_sent','negotiation','won','lost','on_hold'] as $st):
                $labels=['new'=>'New','contacted'=>'Contacted','qualified'=>'Qualified','proposal_sent'=>'Proposal Sent','negotiation'=>'Negotiation','won'=>'Won','lost'=>'Lost','on_hold'=>'On Hold'];
              ?>
              <option value="<?= $st ?>" <?= $lead['status']===$st?'selected':'' ?>><?= $labels[$st] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-semibold">Priority</label>
            <select name="priority" class="form-select">
              <option value="high" <?= $lead['priority']==='high'?'selected':'' ?>>High</option>
              <option value="medium" <?= $lead['priority']==='medium'?'selected':'' ?>>Medium</option>
              <option value="low" <?= $lead['priority']==='low'?'selected':'' ?>>Low</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Lost Reason</label>
            <input type="text" name="lost_reason" class="form-control" value="<?= htmlspecialchars($lead['lost_reason']) ?>" placeholder="If status is Lost">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Remarks</label>
            <textarea name="remarks" class="form-control" rows="2"><?= htmlspecialchars($lead['remarks']) ?></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 d-flex gap-2">
    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Update Lead</button>
    <a href="<?= u("crm/viewLead/{$lead['id']}") ?>" class="btn btn-outline-secondary">Cancel</a>
  </div>
</div>
</form>
