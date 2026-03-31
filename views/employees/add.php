<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Add New Employee</h5>
  <a href="<?= u('employees/index') ?>" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Back
  </a>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" id="employeeForm">
<div class="row g-4">

  <!-- 1. Basic Details -->
  <div class="col-12">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3"><h6 class="mb-0 text-primary">Basic Details</h6></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label small fw-bold">Candidate Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Branch <span class="text-danger">*</span></label>
            <select name="branch_id" class="form-select form-select-sm" required>
              <option value="">---------</option>
              <?php foreach ($masters['branches'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Date of Walk-in</label>
            <input type="date" name="date_of_walkin" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
          </div>
          <div class="col-md-3 text-center">
            <label class="form-label small fw-bold d-block">Photo</label>
            <input type="file" name="photo" class="form-control form-control-sm" accept="image/*">
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-bold">Father Name <span class="text-danger">*</span></label>
            <input type="text" name="father_name" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Mother Name</label>
            <input type="text" name="mother_name" class="form-control form-control-sm">
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold">Gender <span class="text-danger">*</span></label>
            <select name="gender" class="form-select form-select-sm" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold">Date of Birth <span class="text-danger">*</span></label>
            <input type="date" name="dob" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold">Married ? <span class="text-danger">*</span></label>
            <select name="married_status" class="form-select form-select-sm" required>
              <option value="Single">Single</option>
              <option value="Married">Married</option>
              <option value="Divorced">Divorced</option>
              <option value="Widowed">Widowed</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-bold">Mobile Number <span class="text-danger">*</span></label>
            <input type="text" name="mobile" class="form-control form-control-sm" required maxlength="15">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">WhatsApp Number</label>
            <input type="text" name="whatsapp_number" class="form-control form-control-sm" maxlength="15">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Email ID</label>
            <input type="email" name="email_id" class="form-control form-control-sm">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Qualification <span class="text-danger">*</span></label>
            <select name="qualification_id" class="form-select form-select-sm" required>
              <option value="">---------</option>
              <?php foreach ($masters['qualifications'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-bold">Blood Group</label>
            <select name="blood_group_id" class="form-select form-select-sm">
              <option value="">---------</option>
              <?php foreach ($masters['blood_groups'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-9">
            <label class="form-label small fw-bold">Communication Address <span class="text-danger">*</span></label>
            <input type="text" name="address" class="form-control form-control-sm" required>
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-bold">State <span class="text-danger">*</span></label>
            <select name="communication_state_id" class="form-select form-select-sm" required>
              <option value="">---------</option>
              <?php foreach ($masters['states'] as $m): ?>
              <option value="<?= $m['name'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">District <span class="text-danger">*</span></label>
            <select name="communication_district_id" class="form-select form-select-sm" required>
              <option value="">---------</option>
              <?php foreach ($masters['districts'] as $m): ?>
              <option value="<?= $m['name'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Taluk <span class="text-danger">*</span></label>
            <select name="communication_taluk_id" class="form-select form-select-sm" required>
              <option value="">---------</option>
              <?php foreach ($masters['taluks'] as $m): ?>
              <option value="<?= $m['name'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold">Town <span class="text-danger">*</span></label>
            <select name="communication_town_id" class="form-select form-select-sm" required>
              <option value="">---------</option>
              <?php foreach ($masters['towns'] as $m): ?>
              <option value="<?= $m['name'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-1">
            <label class="form-label small fw-bold">PIN Code</label>
            <input type="text" name="communication_pincode" class="form-control form-control-sm">
          </div>
          
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="sameAddress">
              <label class="form-check-label small" for="sameAddress">My Permanent Address same as My Communication Address</label>
            </div>
            <textarea name="permanent_address" id="permanent_address" class="form-control form-control-sm mt-2" placeholder="Permanent Address" rows="1"></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. Contact Details & References -->
  <div class="col-12">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3"><h6 class="mb-0 text-primary">Contact Details & References</h6></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small fw-bold">Contact Person Name for Emergency</label>
            <input type="text" name="emg_contact_name" class="form-control form-control-sm">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Relation</label>
            <select name="emg_contact_relation_id" class="form-select form-select-sm">
              <option value="">---------</option>
              <?php foreach ($masters['relationships'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Mobile No.</label>
            <input type="text" name="emg_contact_mobile" class="form-control form-control-sm">
          </div>

          <div class="col-md-6">
            <label class="form-label small fw-bold">Trusted Person Name</label>
            <input type="text" name="trusted_person_name" class="form-control form-control-sm">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Relation</label>
            <select name="trusted_person_relation_id" class="form-select form-select-sm">
              <option value="">---------</option>
              <?php foreach ($masters['relationships'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Mobile No.</label>
            <input type="text" name="trusted_person_mobile" class="form-control form-control-sm">
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-bold">Introducer Relate <span class="text-danger">*</span></label>
            <select name="introducer_relate_id" class="form-select form-select-sm" required>
              <option value="">---------</option>
              <?php foreach ($masters['relationships'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-9">
            <label class="form-label small fw-bold">Intro Employee</label>
            <select name="intro_employee_id" class="form-select form-select-sm">
              <option value="">---------</option>
              <?php foreach ($masters['employees'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name'] . ' (' . $m['emp_code'] . ')') ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label small fw-bold">Introducer Name (if not 4S Employee)</label>
            <input type="text" name="introducer_name" class="form-control form-control-sm">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold">Mobile No. <span class="text-danger">*</span></label>
            <input type="text" name="introducer_mobile" class="form-control form-control-sm" required>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 3. Fitness Details -->
  <div class="col-md-12">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3"><h6 class="mb-0 text-primary">Fitness Details</h6></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-2">
            <label class="form-label small fw-bold">Height in CM <span class="text-danger">*</span></label>
            <input type="number" step="0.1" name="height_cm" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold">Weight in Kgs <span class="text-danger">*</span></label>
            <input type="number" step="0.1" name="weight_kg" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold">Chest in Inches <span class="text-danger">*</span></label>
            <input type="number" step="0.1" name="chest_inches" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-bold">Hip in Inches <span class="text-danger">*</span></label>
            <input type="number" step="0.1" name="hip_inches" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Body Type <span class="text-danger">*</span></label>
            <select name="body_type_id" class="form-select form-select-sm" required>
              <option value="">---------</option>
              <?php foreach ($masters['body_types'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 4. Previous Work Experience -->
  <div class="col-12">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3"><h6 class="mb-0 text-primary">Previous Work Experience</h6></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label small fw-bold">Work Exp. <span class="text-danger">*</span></label>
            <select name="work_exp_years" class="form-select form-select-sm" required>
              <option value="0">No</option>
              <?php for($i=1;$i<=30;$i++): ?><option value="<?= $i ?>"><?= $i ?> Year<?= $i>1?'s':'' ?></option><?php endfor; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Company</label>
            <input type="text" name="prev_company" class="form-control form-control-sm">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Contact Number</label>
            <input type="text" name="prev_contact_number" class="form-control form-control-sm">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Strength</label>
            <input type="text" name="prev_strength" class="form-control form-control-sm">
          </div>
          <div class="col-12">
            <label class="form-label small fw-bold">Reason For Relieve</label>
            <textarea name="reason_for_relieve" class="form-control form-control-sm" rows="1"></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 5. Salary & Designation Expectations -->
  <div class="col-12">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3"><h6 class="mb-0 text-primary">Salary & Designation Expectations</h6></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label small fw-bold">Expected Salary 1 <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="expected_salary_1" id="expected_salary_1" class="form-control form-control-sm" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Expected Salary 2</label>
            <input type="number" step="0.01" name="expected_salary_2" class="form-control form-control-sm">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Work Hours <span class="text-danger">*</span></label>
            <input type="number" name="work_hours" class="form-control form-control-sm" value="8" required>
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-bold">Expected Designation 1 <span class="text-danger">*</span></label>
            <select name="expected_designation_id_1" id="designation_select" class="form-select form-select-sm" required>
              <option value="">---------</option>
              <?php foreach ($masters['designations'] as $m): ?>
              <option value="<?= $m['id'] ?>" data-rate="<?= $m['daily_rate'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Expected Designation 2</label>
            <select name="expected_designation_id_2" class="form-select form-select-sm">
              <option value="">---------</option>
              <?php foreach ($masters['designations'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Expected Company 1</label>
            <select name="expected_company_id_1" class="form-select form-select-sm">
              <option value="">---------</option>
              <?php foreach ($masters['employees'] as $m): ?> <!-- Assuming this is meant to be clients or similar -->
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Expected Company 2</label>
            <select name="expected_company_id_2" class="form-select form-select-sm">
              <option value="">---------</option>
              <?php foreach ($masters['employees'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-bold">Exp. State</label>
            <select name="exp_state_id" class="form-select form-select-sm">
              <option value="">---------</option>
              <?php foreach ($masters['states'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Exp. District</label>
            <select name="exp_district_id" class="form-select form-select-sm">
              <option value="">---------</option>
              <?php foreach ($masters['districts'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Exp. Taluk</label>
            <select name="exp_taluk_id" class="form-select form-select-sm">
              <option value="">---------</option>
              <?php foreach ($masters['taluks'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Exp. Town <span class="text-danger">*</span></label>
            <select name="exp_town_id" class="form-select form-select-sm" required>
              <option value="">---------</option>
              <?php foreach ($masters['towns'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- 6. Salary & Financials -->
  <div class="col-12">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3"><h6 class="mb-0 text-primary">Salary & Financial Particulars</h6></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Basic Wage (Daily) <span class="text-danger">*</span></label>
            <div class="input-group input-group-sm">
                <span class="input-group-text">₹</span>
                <input type="number" step="0.01" name="basic_wage" id="basic_wage" class="form-control fw-bold" value="0" required>
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Daily Allowance (DA)</label>
            <input type="number" step="0.01" name="salary_da" class="form-control form-control-sm" value="0">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">HRA (Daily)</label>
            <input type="number" step="0.01" name="salary_hra" class="form-control form-control-sm" value="0">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Conveyance (Daily)</label>
            <input type="number" step="0.01" name="salary_conv" class="form-control form-control-sm" value="0">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Med / Washing</label>
            <input type="number" step="0.01" name="salary_med_wash" class="form-control form-control-sm" value="0">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Other Allowances</label>
            <input type="number" step="0.01" name="salary_other" class="form-control form-control-sm" value="0">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold text-muted">OverTime (OT) Details</label>
            <input type="text" name="ot_details" class="form-control form-control-sm" placeholder="e.g. 1.5x for Sunday">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 7. Compliance & Statutory -->
  <div class="col-12">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3"><h6 class="mb-0 text-success">Statutory Compliance & EPF/ESI</h6></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6 border-end">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="epf_applicable" value="YES" id="epf_check">
                <label class="form-check-label small fw-bold" for="epf_check">EPF Applicable</label>
            </div>
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label small text-muted mb-1">Calculation Base Amount</label>
                <input type="number" name="amt_for_calc_epf" class="form-control form-control-sm" value="0">
              </div>
              <div class="col-6">
                <label class="form-label small text-muted mb-1">EPF Member ID / No</label>
                <input type="text" name="epf_no" class="form-control form-control-sm text-uppercase">
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="esi_applicable" value="YES" id="esi_check">
                <label class="form-check-label small fw-bold" for="esi_check">ESI Applicable</label>
            </div>
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label small text-muted mb-1">Calculation Base Amount</label>
                <input type="number" name="amt_for_calc_esi" class="form-control form-control-sm" value="0">
              </div>
              <div class="col-6">
                <label class="form-label small text-muted mb-1">ESI Number</label>
                <input type="text" name="esi_no" class="form-control form-control-sm">
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">TDS Applicable?</label>
            <select name="tds_avail" class="form-select form-select-sm">
                <option value="NO">No</option>
                <option value="YES">Yes</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">TDS Number (PAN)</label>
            <input type="text" name="tds_no" class="form-control form-control-sm text-uppercase">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Bank Branch Address</label>
            <input type="text" name="bank_branch" class="form-control form-control-sm">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted d-block">Will have Header?</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="will_have_header" value="YES">
              <label class="form-check-label small">Yes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="will_have_header" value="NO" checked>
              <label class="form-check-label small">No</label>
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted d-block">Will have Footer?</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="will_have_footer" value="YES">
              <label class="form-check-label small">Yes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="will_have_footer" value="NO" checked>
              <label class="form-check-label small">No</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 8. Insurance & Nominee -->
  <div class="col-12">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3"><h6 class="mb-0 text-info">Insurance & Nominee Details</h6></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Nominee Name</label>
            <input type="text" name="nominee_name" class="form-control form-control-sm">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Relation</label>
            <select name="nominee_relation_id" class="form-select form-select-sm">
                <option value="">— Select —</option>
                <?php foreach ($masters['relationships'] as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Policy Number</label>
            <input type="text" name="ins_number" class="form-control form-control-sm">
          </div>
          <div class="col-md-2">
             <label class="form-label small fw-bold text-muted">Renewal Date</label>
             <input type="date" name="ins_renewal_date" class="form-control form-control-sm">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 9. Document Uploads -->
  <div class="col-12">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3"><h6 class="mb-0 text-dark">Document Attachments</h6></div>
      <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><label class="small fw-bold mb-1">Application Form</label><input type="file" name="doc_application" class="form-control form-control-sm"></div>
            <div class="col-md-3"><label class="small fw-bold mb-1">Aadhaar Card</label><input type="file" name="doc_aadhaar" class="form-control form-control-sm"></div>
            <div class="col-md-3"><label class="small fw-bold mb-1">Bank Passbook</label><input type="file" name="doc_passbook" class="form-control form-control-sm"></div>
            <div class="col-md-3"><label class="small fw-bold mb-1">Passport Photo</label><input type="file" name="doc_photo" class="form-control form-control-sm"></div>
            <div class="col-md-3"><label class="small fw-bold mb-1">Settlement Doc</label><input type="file" name="doc_settlement" class="form-control form-control-sm"></div>
            <div class="col-md-3"><label class="small fw-bold mb-1">Extra 1</label><input type="file" name="doc_extra_1" class="form-control form-control-sm"></div>
            <div class="col-md-3"><label class="small fw-bold mb-1">Extra 2</label><input type="file" name="doc_extra_2" class="form-control form-control-sm"></div>
            <div class="col-md-3"><label class="small fw-bold mb-1">Extra 3</label><input type="file" name="doc_extra_3" class="form-control form-control-sm"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- 6. Legal & Identification -->
  <div class="col-12">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3"><h6 class="mb-0 text-primary">Legal & Identification</h6></div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label small fw-bold">Convicted By Court? <span class="text-danger">*</span></label>
            <select name="convicted" class="form-select form-select-sm" required>
              <option value="No">No</option>
              <option value="Yes">Yes</option>
            </select>
          </div>
          <div class="col-md-8">
            <label class="form-label small fw-bold">Reason</label>
            <input type="text" name="convicted_reason" class="form-control form-control-sm">
          </div>

          <div class="col-md-4">
            <label class="form-label small fw-bold">Identification Mark 1</label>
            <input type="text" name="id_mark_1" class="form-control form-control-sm">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Identification Mark 2</label>
            <input type="text" name="id_mark_2" class="form-control form-control-sm">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Nearby Police Station</label>
            <input type="text" name="nearby_police_station" class="form-control form-control-sm">
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-bold">Status <span class="text-danger">*</span></label>
            <select name="status" class="form-select form-select-sm" required>
              <option value="pre_employee">Pre Employee</option>
              <option value="active" selected>Active</option>
              <option value="inactive">Relieved / Inactive</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-bold">Referred By <span class="text-danger">*</span></label>
            <select name="referred_by_id" class="form-select form-select-sm" required>
              <option value="">---------</option>
              <?php foreach ($masters['references'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold">Field Officer</label>
            <select name="field_officer_id" class="form-select form-select-sm">
              <option value="">---------</option>
              <?php foreach ($masters['officers'] as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="col-12">
            <label class="form-label small fw-bold">Remarks, if Any</label>
            <textarea name="remarks_extra" class="form-control form-control-sm" rows="2"></textarea>
          </div>

          <!-- Legacy / Hidden fields removal -->
          <input type="hidden" name="designation" id="actual_designation" value="">
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 text-end pb-5">
    <button type="submit" class="btn btn-primary px-5 shadow-sm">
      <i class="bi bi-save me-2"></i>SAVE EMPLOYEE
    </button>
  </div>
</div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const sameAddress = document.getElementById('sameAddress');
  const commAddr = document.querySelector('input[name="address"]');
  const permAddr = document.getElementById('permanent_address');
  
  sameAddress.addEventListener('change', function() {
    if (this.checked) {
      permAddr.value = commAddr.value;
      permAddr.readOnly = true;
    } else {
      permAddr.readOnly = false;
    }
  });

  // Daily Salary Logic
  const desigSelect = document.getElementById('designation_select');
  const actualDesig = document.getElementById('actual_designation');
  const basicWageInput = document.getElementById('basic_wage');

  desigSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const rate = selectedOption.getAttribute('data-rate');
    const name = selectedOption.text;
    
    if (rate && rate > 0) {
      basicWageInput.value = rate;
    }
    actualDesig.value = name;
  });
});
</script>

