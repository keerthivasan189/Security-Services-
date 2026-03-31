<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0 fw-semibold"><i class="bi bi-person-plus me-2" style="color:#6c5ce7"></i>Add New Lead</h5>
  <a href="<?= u('crm/leads') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Leads</a>
</div>

<form method="POST" action="<?= u('crm/addLead') ?>">
<div class="row g-3">

  <!-- Company Info -->
  <div class="col-12">
    <div class="card">
      <div class="card-header bg-white" style="border-left:4px solid #6c5ce7">
        <i class="bi bi-building me-1"></i> Company Information
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Company Name <span class="text-danger">*</span></label>
            <input type="text" name="company_name" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Contact Person</label>
            <input type="text" name="contact_person" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Industry / Sector</label>
            <input type="text" name="industry" class="form-control" list="industry-list" placeholder="e.g. IT, Manufacturing, Healthcare">
            <datalist id="industry-list">
              <option>IT / Software</option><option>Manufacturing</option><option>Healthcare</option>
              <option>Education</option><option>Retail</option><option>Construction</option>
              <option>Government</option><option>Banking / Finance</option><option>Hospitality</option>
              <option>Security Services</option><option>Other</option>
            </datalist>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Mobile</label>
            <input type="text" name="mobile" class="form-control" maxlength="15">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Phone / Landline</label>
            <input type="text" name="phone" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">District</label>
            <input type="text" name="district" class="form-control" list="district-list">
            <datalist id="district-list">
              <?php foreach ($crmDistricts as $d): ?>
              <option><?= htmlspecialchars($d) ?></option>
              <?php endforeach; ?></datalist>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Address</label>
            <textarea name="address" class="form-control" rows="2"></textarea>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">State</label>
            <input type="text" name="state" class="form-control" value="TAMIL NADU" list="state-list">
            <datalist id="state-list">
              <?php foreach ($crmStates as $st): ?>
              <option><?= htmlspecialchars($st) ?></option>
              <?php endforeach; ?></datalist>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Pincode</label>
            <input type="text" name="pincode" class="form-control" maxlength="6">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Lead Details -->
  <div class="col-12">
    <div class="card">
      <div class="card-header bg-white" style="border-left:4px solid #0984e3">
        <i class="bi bi-graph-up me-1"></i> Lead Details
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label fw-semibold">Lead Source <span class="text-danger">*</span></label>
            <select name="source" class="form-select" required>
              <?php
              $srcList = !empty($crmSources) ? $crmSources : ['Cold Call','Reference','Walk-in','Online','Social Media','Exhibition','Email Campaign','Other'];
              foreach ($srcList as $src):
              ?>
              <option value="<?= htmlspecialchars($src) ?>"><?= htmlspecialchars($src) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Reference Name</label>
            <input type="text" name="reference_name" class="form-control" placeholder="If referred by someone">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Assigned To</label>
            <input type="text" name="assigned_to" class="form-control" placeholder="Sales person name">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Follow-up Date</label>
            <input type="date" name="follow_up_date" class="form-control" value="<?= date('Y-m-d', strtotime('+1 day')) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Service Needed</label>
            <textarea name="service_needed" class="form-control" rows="2" placeholder="Security guards, CCTV, etc."></textarea>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-semibold">Expected Strength</label>
            <input type="number" name="expected_strength" class="form-control" min="0" placeholder="No. of guards">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Expected Monthly Value (₹)</label>
            <input type="number" name="expected_value" class="form-control" step="0.01" min="0" placeholder="Monthly billing">
          </div>
          <div class="col-md-3">
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                  <option value="new">New</option>
                  <option value="contacted">Contacted</option>
                  <option value="qualified">Qualified</option>
                  <option value="proposal_sent">Proposal Sent</option>
                  <option value="negotiation">Negotiation</option>
                </select>
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Priority</label>
                <select name="priority" class="form-select">
                  <option value="medium" selected>Medium</option>
                  <option value="high">High</option>
                  <option value="low">Low</option>
                </select>
              </div>
            </div>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Remarks / Notes</label>
            <textarea name="remarks" class="form-control" rows="2"></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 d-flex gap-2">
    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Save Lead</button>
    <a href="<?= u('crm/leads') ?>" class="btn btn-outline-secondary">Cancel</a>
  </div>

</div>
</form>
