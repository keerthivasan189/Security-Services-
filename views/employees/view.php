<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <a href="<?= u('employees/index?status='.$employee['status']) ?>" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i>Back to Employee List</a>
    <h5 class="mb-0 mt-1">
      <i class="bi bi-person-badge me-2 text-primary"></i>
      <?= htmlspecialchars($employee['name']) ?> 
      <small class="text-muted fw-normal ms-2">(<?= htmlspecialchars($employee['emp_code']) ?>)</small>
    </h5>
  </div>
  <div class="d-flex gap-2 no-print">
    <a href="<?= u('employees/edit/'.$employee['id']) ?>" class="btn btn-warning btn-sm shadow-sm"><i class="bi bi-pencil me-1"></i>Edit Profile</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm shadow-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<!-- Header Summary Card -->
<div class="card shadow-sm border-0 mb-4 overflow-hidden">
  <div class="card-body p-0">
    <div class="row g-0">
      <div class="col-md-auto bg-light d-flex align-items-center justify-content-center p-4 border-end" style="min-width: 180px;">
        <div class="text-center">
          <?php if (!empty($employee['photo'])): ?>
            <img src="<?= BASE_URL ?>/uploads/employee_photos/<?= $employee['photo'] ?>" class="rounded shadow-sm mb-3 border" width="120" height="150" style="object-fit:cover">
          <?php else: ?>
            <div class="bg-white rounded border shadow-sm mb-3 d-flex align-items-center justify-content-center" style="width:120px; height:150px;">
              <i class="bi bi-person text-light" style="font-size:80px"></i>
            </div>
          <?php endif; ?>
          <span class="badge py-2 px-3 rounded-pill <?=$employee['status']==='active'?'bg-success':($employee['status']==='pre_employee'?'bg-warning text-dark':'bg-secondary')?>">
            <?= strtoupper(str_replace('_',' ',$employee['status'])) ?>
          </span>
        </div>
      </div>
      <div class="col-md p-4">
        <div class="row g-4">
          <div class="col-md-3">
            <div class="small text-muted mb-1 text-uppercase fw-bold ls-1">Current Designation</div>
            <div class="h6 mb-0 text-dark"><?= htmlspecialchars($employee['designation']) ?></div>
          </div>
          <div class="col-md-3">
            <div class="small text-muted mb-1 text-uppercase fw-bold ls-1">Daily Rate</div>
            <div class="h6 mb-0 text-primary"><?= Helper::money($employee['basic_wage']) ?> <small class="text-muted">/ Day</small></div>
          </div>
          <div class="col-md-3">
            <div class="small text-muted mb-1 text-uppercase fw-bold ls-1">Mobile Number</div>
            <div class="h6 mb-0"><?= htmlspecialchars($employee['mobile']) ?></div>
          </div>
          <div class="col-md-3 text-end">
             <div class="small text-muted mb-1 text-uppercase fw-bold ls-1">Employee Progress</div>
             <div class="progress mt-2" style="height: 10px;">
                <div class="progress-bar bg-success" style="width: <?= $employee['status'] === 'active' ? '100%' : '50%' ?>"></div>
             </div>
             <small class="text-muted"><?= $employee['status'] === 'active' ? 'Fully Registered' : 'Registration Pending' ?></small>
          </div>

          <div class="col-12 mt-4 pt-3 border-top">
            <div class="d-flex gap-4">
              <div class="d-flex align-items-center">
                <i class="bi bi-calendar-check text-primary me-2"></i>
                <span class="small text-muted me-1">Joined:</span>
                <span class="small fw-bold"><?= $employee['doj'] ? date('d M Y', strtotime($employee['doj'])) : 'Not Joined' ?></span>
              </div>
              <div class="d-flex align-items-center">
                <i class="bi bi-building text-primary me-2"></i>
                <span class="small text-muted me-1">Branch:</span>
                <span class="small fw-bold"><?= htmlspecialchars($employee['branch_name'] ?? 'N/A') ?></span>
              </div>
              <div class="d-flex align-items-center">
                <i class="bi bi-shield-check text-primary me-2"></i>
                <span class="small text-muted me-1">Compliance:</span>
                <span class="badge bg-light text-dark border ms-1 fw-normal">EPF: <?= $employee['epf_applicable'] ? 'YES' : 'NO' ?></span>
                <span class="badge bg-light text-dark border ms-1 fw-normal">ESI: <?= $employee['esi_applicable'] ? 'YES' : 'NO' ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-pills mb-3 no-print bg-white p-2 rounded shadow-sm gap-2" role="tablist">
  <li class="nav-item border-end pe-2">
    <a class="nav-link active px-4 py-2" data-bs-toggle="tab" href="#tab-basic">
      <i class="bi bi-info-circle me-2"></i>Basic Details
    </a>
  </li>
  <li class="nav-item border-end pe-2">
    <a class="nav-link px-4 py-2" data-bs-toggle="tab" href="#tab-fitness">
      <i class="bi bi-activity me-2"></i>Fitness & Experience
    </a>
  </li>
  <li class="nav-item border-end pe-2">
    <a class="nav-link px-4 py-2" data-bs-toggle="tab" href="#tab-expectations">
      <i class="bi bi-cash-stack me-2"></i>Expectations
    </a>
  </li>
  <li class="nav-item border-end pe-2">
    <a class="nav-link px-4 py-2" data-bs-toggle="tab" href="#tab-legal">
      <i class="bi bi-shield-lock me-2"></i>Legal & ID
    </a>
  </li>
  <li class="nav-item border-end pe-2">
    <a class="nav-link px-4 py-2" data-bs-toggle="tab" href="#tab-payroll">
      <i class="bi bi-bank me-2"></i>Payroll & Compliance
    </a>
  </li>
  <li class="nav-item border-end pe-2">
    <a class="nav-link px-4 py-2" data-bs-toggle="tab" href="#tab-docs">
      <i class="bi bi-paperclip me-2"></i>Attachments
    </a>
  </li>
  <li class="nav-item border-end pe-2">
    <a class="nav-link px-4 py-2" data-bs-toggle="tab" href="#tab-work">
      <i class="bi bi-briefcase me-2"></i>History
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link px-4 py-2" data-bs-toggle="tab" href="#tab-logs">
      <i class="bi bi-telephone me-2"></i>Call Logs
    </a>
  </li>
</ul>

<div class="tab-content">
  <!-- 1. Basic Details Tab -->
  <div class="tab-pane fade show active" id="tab-basic">
    <div class="row g-3">
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white py-3 border-0">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-person me-2 text-primary"></i>Personal Information</h6>
          </div>
          <div class="card-body pt-0">
            <table class="table table-sm table-borderless align-middle mb-0">
              <tr><td class="text-muted" width="40%">Father's Name</td><td class="fw-bold"><?= htmlspecialchars($employee['father_name']??'N/A') ?></td></tr>
              <tr><td class="text-muted">Mother's Name</td><td class="fw-bold"><?= htmlspecialchars($employee['mother_name']??'N/A') ?></td></tr>
              <tr><td class="text-muted">Gender / DOB</td><td class="fw-bold"><?= $employee['gender'] ?> / <?= $employee['dob'] ? date('d M Y', strtotime($employee['dob'])) : 'N/A' ?></td></tr>
              <tr><td class="text-muted">Marital Status</td><td class="fw-bold"><?= $employee['married_status'] ?></td></tr>
              <tr><td class="text-muted">Qualification</td><td class="fw-bold"><?= htmlspecialchars($employee['qualification_name']??'N/A') ?></td></tr>
              <tr><td class="text-muted">Blood Group</td><td class="fw-bold text-danger"><?= htmlspecialchars($employee['blood_group_name']??'N/A') ?></td></tr>
              <tr><td class="text-muted">Email ID</td><td class="fw-bold"><?= htmlspecialchars($employee['email_id']??'N/A') ?></td></tr>
              <tr><td class="text-muted">WhatsApp</td><td class="fw-bold text-success"><?= htmlspecialchars($employee['whatsapp_number']??'N/A') ?></td></tr>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white py-3 border-0">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-geo-alt me-2 text-primary"></i>Address & Contact</h6>
          </div>
          <div class="card-body pt-0">
            <div class="mb-3">
              <label class="small text-muted d-block mb-1">Communication Address</label>
              <div class="fw-bold mb-1"><?= nl2br(htmlspecialchars($employee['address'])) ?></div>
              <div class="small text-dark">
                <?= $employee['comm_town'] ?>, <?= $employee['comm_taluk'] ?>, <?= $employee['comm_district'] ?>, <?= $employee['comm_state'] ?> - <?= $employee['communication_pincode'] ?>
              </div>
            </div>
            <div class="mb-3 border-top pt-2">
              <label class="small text-muted d-block mb-1">Permanent Address</label>
              <div class="fw-bold small"><?= nl2br(htmlspecialchars($employee['permanent_address']??'Same as above')) ?></div>
            </div>
            <div class="row g-2 border-top pt-2">
              <div class="col-6">
                <label class="small text-muted d-block">Emergency Contact</label>
                <div class="fw-bold small"><?= htmlspecialchars($employee['emg_contact_name']??"N/A") ?> (<?= $employee['emg_rel']??"N/A" ?>)</div>
                <div class="text-primary small"><?= $employee['emg_contact_mobile'] ?></div>
              </div>
              <div class="col-6 border-start ps-3">
                <label class="small text-muted d-block">Trusted Person</label>
                <div class="fw-bold small"><?= htmlspecialchars($employee['trusted_person_name']??"N/A") ?> (<?= $employee['trust_rel']??"N/A" ?>)</div>
                <div class="text-primary small"><?= $employee['trusted_person_mobile'] ?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. Fitness & Experience Tab -->
  <div class="tab-pane fade" id="tab-fitness">
    <div class="row g-3">
      <div class="col-md-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="row text-center align-items-center">
              <div class="col-md-2 border-end">
                <div class="display-6 fw-bold text-primary mb-0"><?= $employee['height_cm'] ?></div>
                <div class="small text-muted text-uppercase">Height (CM)</div>
              </div>
              <div class="col-md-2 border-end">
                <div class="display-6 fw-bold text-primary mb-0"><?= $employee['weight_kg'] ?></div>
                <div class="small text-muted text-uppercase">Weight (KG)</div>
              </div>
              <div class="col-md-2 border-end">
                <div class="display-6 fw-bold text-primary mb-0"><?= $employee['chest_inches'] ?></div>
                <div class="small text-muted text-uppercase">Chest (Inch)</div>
              </div>
              <div class="col-md-2 border-end">
                <div class="display-6 fw-bold text-primary mb-0"><?= $employee['hip_inches'] ?></div>
                <div class="small text-muted text-uppercase">Hip (Inch)</div>
              </div>
              <div class="col-md-4">
                <div class="h3 fw-bold text-dark mb-0"><?= htmlspecialchars($employee['body_type_name']??"N/A") ?></div>
                <div class="small text-muted text-uppercase">Body Type</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white py-3 border-0">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Previous Work Experience</h6>
          </div>
          <div class="card-body pt-0">
            <div class="row">
              <div class="col-md-3">
                <div class="p-3 bg-light rounded text-center">
                  <div class="h4 mb-0 fw-bold"><?= $employee['work_exp_years'] ?></div>
                  <div class="small text-muted">Years Experience</div>
                </div>
              </div>
              <div class="col-md-9 border-start ps-4">
                <div class="mb-2">
                  <span class="text-muted">Company Name:</span>
                  <span class="fw-bold ms-2"><?= htmlspecialchars($employee['prev_company']??"N/A") ?></span>
                </div>
                <div class="mb-2">
                  <span class="text-muted">Contact / Strength:</span>
                  <span class="fw-bold ms-2"><?= $employee['prev_contact_number'] ?> / <?= $employee['prev_strength'] ?></span>
                </div>
                <div class="alert alert-light p-2 mb-0 border">
                  <i class="bi bi-info-circle me-1 small"></i>
                  <span class="small">Reason for Relieve: <?= htmlspecialchars($employee['reason_for_relieve']??"N/A") ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 3. Expectations Tab -->
  <div class="tab-pane fade" id="tab-expectations">
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-4 border-end">
            <h6 class="text-muted small text-uppercase fw-bold mb-3 border-bottom pb-2">Salary Expectations</h6>
            <div class="d-flex justify-content-between mb-2">
              <span>Preference 1:</span> <span class="fw-bold text-success"><?= Helper::money($employee['expected_salary_1']) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Preference 2:</span> <span class="fw-bold"><?= Helper::money($employee['expected_salary_2']) ?></span>
            </div>
            <div class="d-flex justify-content-between">
              <span>Work Hours:</span> <span class="fw-bold"><?= $employee['work_hours'] ?> Hours</span>
            </div>
          </div>
          <div class="col-md-4 border-end">
            <h6 class="text-muted small text-uppercase fw-bold mb-3 border-bottom pb-2">Designation & Company</h6>
            <div class="mb-2">
              <div class="small text-muted">Expected Designation:</div>
              <div class="fw-bold mb-1"><?= htmlspecialchars($employee['exp_desig_1']??"N/A") ?></div>
              <div class="small fw-normal text-muted"><?= htmlspecialchars($employee['exp_desig_2']??"N/A") ?></div>
            </div>
          </div>
          <div class="col-md-4">
            <h6 class="text-muted small text-uppercase fw-bold mb-3 border-bottom pb-2">Location Preference</h6>
            <div class="fw-bold"><?= $employee['exp_town'] ?>, <?= $employee['exp_taluk'] ?></div>
            <div class="small text-muted"><?= $employee['exp_district'] ?>, <?= $employee['exp_state'] ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 3b. Payroll & Compliance Tab -->
  <div class="tab-pane fade" id="tab-payroll">
    <div class="row g-3">
      <div class="col-md-7">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white py-3 border-0"><h6 class="mb-0 fw-bold text-dark"><i class="bi bi-cash-coin me-2 text-primary"></i>Salary Particulars (Per Day)</h6></div>
          <div class="card-body pt-0">
             <div class="row g-2">
                <div class="col-md-6 border-end">
                   <table class="table table-sm table-borderless small mb-0">
                      <tr><td class="text-muted">Basic Wage</td><td class="fw-bold text-end"><?= Helper::money($employee['basic_wage']) ?></td></tr>
                      <tr><td class="text-muted">D.A.</td><td class="fw-bold text-end"><?= Helper::money($employee['salary_da']) ?></td></tr>
                      <tr><td class="text-muted">H.R.A.</td><td class="fw-bold text-end"><?= Helper::money($employee['salary_hra']) ?></td></tr>
                      <tr><td class="text-muted">Conveyance</td><td class="fw-bold text-end"><?= Helper::money($employee['salary_conv']) ?></td></tr>
                      <tr class="border-top"><td class="fw-bold">DAILY TOTAL</td><td class="fw-bold text-end text-primary"><?= Helper::money($employee['basic_wage'] + $employee['salary_da'] + $employee['salary_hra'] + $employee['salary_conv']) ?></td></tr>
                   </table>
                </div>
                <div class="col-md-6">
                   <table class="table table-sm table-borderless small mb-0">
                      <tr><td class="text-muted">Med / Wash</td><td class="fw-bold text-end"><?= Helper::money($employee['salary_med_wash']) ?></td></tr>
                      <tr><td class="text-muted">Others</td><td class="fw-bold text-end"><?= Helper::money($employee['salary_other']) ?></td></tr>
                      <tr class="border-top"><td class="text-muted">OT Details</td><td class="small fw-semibold"><?= htmlspecialchars($employee['ot_details'] ?: 'Standard') ?></td></tr>
                   </table>
                </div>
             </div>
          </div>
        </div>
        
        <div class="card border-0 shadow-sm mt-3">
          <div class="card-header bg-white py-3 border-0"><h6 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-check me-2 text-success"></i>Statutory Compliance</h6></div>
          <div class="card-body pt-0">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="p-2 border rounded bg-light">
                   <div class="small fw-bold <?= $employee['epf_applicable'] === 'YES' ? 'text-success' : 'text-muted' ?>">EPF: <?= $employee['epf_applicable'] ?></div>
                   <div class="small text-muted mb-1">Calculation Base: <?= Helper::money($employee['amt_for_calc_epf']) ?></div>
                   <div class="small fw-bold">No: <?= htmlspecialchars($employee['epf_no'] ?: '—') ?></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="p-2 border rounded bg-light">
                   <div class="small fw-bold <?= $employee['esi_applicable'] === 'YES' ? 'text-primary' : 'text-muted' ?>">ESI: <?= $employee['esi_applicable'] ?></div>
                   <div class="small text-muted mb-1">Calculation Base: <?= Helper::money($employee['amt_for_calc_esi']) ?></div>
                   <div class="small fw-bold">No: <?= htmlspecialchars($employee['esi_no'] ?: '—') ?></div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="p-2 border rounded bg-light d-flex justify-content-between">
                   <div><span class="small text-muted">TDS Status:</span> <span class="badge bg-white text-dark border ms-2"><?= $employee['tds_avail'] ?></span></div>
                   <div><span class="small text-muted">PAN / TDS No:</span> <span class="fw-bold ms-2"><?= htmlspecialchars($employee['tds_no'] ?: $employee['pan']) ?></span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white py-3 border-0"><h6 class="mb-0 fw-bold text-dark"><i class="bi bi-heart-pulse me-2 text-danger"></i>Insurance & Nominee</h6></div>
          <div class="card-body pt-0">
            <div class="mb-3 border-bottom pb-2">
               <label class="small text-muted d-block mb-1">Nominee Name</label>
               <div class="fw-bold"><?= htmlspecialchars($employee['nominee_name'] ?: 'Not Nominated') ?></div>
               <div class="small text-muted">Relation: <?= htmlspecialchars($employee['emg_rel'] ?: '—') ?></div>
            </div>
            <div class="mb-3">
               <label class="small text-muted d-block mb-1">Insurance Policy</label>
               <div class="fw-bold"><?= htmlspecialchars($employee['ins_number'] ?: 'No Policy Found') ?></div>
               <div class="d-flex justify-content-between mt-1">
                  <span class="small">Renewal: <span class="fw-bold"><?= $employee['ins_renewal_date'] ? date('d M Y', strtotime($employee['ins_renewal_date'])) : '—' ?></span></span>
                  <span class="small">Premium: <span class="fw-bold"><?= Helper::money($employee['premium_amount']) ?></span></span>
               </div>
            </div>
            <div class="bg-light p-3 rounded">
               <label class="small text-muted d-block mb-1">Bank Account Confirmed</label>
               <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($employee['bank_name']) ?></div>
               <div class="small fw-bold"><?= htmlspecialchars($employee['bank_account']) ?></div>
               <div class="small text-muted">IFSC: <?= htmlspecialchars($employee['bank_ifsc']) ?></div>
               <div class="small text-muted">Branch: <?= htmlspecialchars($employee['bank_branch'] ?: 'N/A') ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 3c. Attachments Tab -->
  <div class="tab-pane fade" id="tab-docs">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
         <div class="row g-3">
            <?php 
            $docs = [
                'doc_application' => ['label' => 'Application Form', 'icon' => 'bi-file-earmark-text'],
                'doc_aadhaar'     => ['label' => 'Aadhaar Card', 'icon' => 'bi-card-heading'],
                'doc_passbook'    => ['label' => 'Bank Passbook', 'icon' => 'bi-bank'],
                'doc_photo'       => ['label' => 'Passport Photo', 'icon' => 'bi-person-square'],
                'doc_settlement'  => ['label' => 'Co. Settlement', 'icon' => 'bi-file-earmark-check'],
                'doc_extra_1'     => ['label' => 'Extra 1', 'icon' => 'bi-file-earmark-plus'],
                'doc_extra_2'     => ['label' => 'Extra 2', 'icon' => 'bi-file-earmark-plus'],
                'doc_extra_3'     => ['label' => 'Extra 3', 'icon' => 'bi-file-earmark-plus']
            ];
            foreach($docs as $field => $info): ?>
            <div class="col-md-3">
               <div class="p-3 border rounded text-center bg-light h-100 d-flex flex-column justify-content-between">
                  <div>
                    <i class="bi <?= $info['icon'] ?> fs-2 text-muted mb-2"></i>
                    <div class="small fw-bold mb-1 text-truncate"><?= $info['label'] ?></div>
                  </div>
                  <?php if ($employee[$field]): ?>
                    <?php
                    $ext = strtolower(pathinfo($employee[$field], PATHINFO_EXTENSION));
                    $isImg = in_array($ext, ['jpg','jpeg','png','webp']);
                    $docUrl = BASE_URL.'/uploads/employee_docs/'.$employee[$field];
                  ?>
                  <a href="<?= $docUrl ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="bi <?= $isImg ? 'bi-image' : 'bi-file-earmark-pdf' ?> me-1"></i>View Document
                  </a>
                  <?php else: ?>
                    <span class="text-muted small mt-2">Not Uploaded</span>
                  <?php endif; ?>
               </div>
            </div>
            <?php endforeach; ?>
         </div>
      </div>
    </div>
  </div>

  <!-- 4. Legal & ID Tab -->
  <div class="tab-pane fade" id="tab-legal">
    <div class="row g-3">
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white py-3 border-0">
             <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Identification Documents</h6>
          </div>
          <div class="card-body pt-0">
            <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
               <i class="bi bi-card-heading text-primary fs-4 me-3"></i>
               <div><div class="small text-muted">Aadhaar Number</div><div class="fw-bold"><?= $employee['aadhaar'] ?></div></div>
            </div>
            <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
               <i class="bi bi-credit-card-2-front text-primary fs-4 me-3"></i>
               <div><div class="small text-muted">PAN Card Number</div><div class="fw-bold"><?= $employee['pan'] ?></div></div>
            </div>
            <div class="row g-2">
              <div class="col-6">
                <div class="p-2 border rounded">
                  <div class="small text-muted">UAN Number</div><div class="small fw-bold"><?= $employee['uan_no']??"N/A" ?></div>
                </div>
              </div>
              <div class="col-6">
                <div class="p-2 border rounded">
                  <div class="small text-muted">ESI Number</div><div class="small fw-bold"><?= $employee['esi_no']??"N/A" ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
           <div class="card-header bg-white py-3 border-0">
             <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-exclamation me-2 text-primary"></i>Compliance & Background</h6>
          </div>
          <div class="card-body pt-0">
            <div class="mb-3">
              <div class="small text-muted mb-1">Criminal Record?</div>
              <?php if($employee['convicted'] === 'Yes'): ?>
                <span class="badge bg-danger">YES - Convicted</span>
                <div class="alert alert-danger mt-2 py-1 small"><?= htmlspecialchars($employee['convicted_reason']) ?></div>
              <?php else: ?>
                <span class="badge bg-success">NO - Clean Record</span>
              <?php endif; ?>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-12 py-2 border-top">
                <div class="small text-muted mb-1">Identification Marks</div>
                <div class="small mb-1">1. <?= htmlspecialchars($employee['id_mark_1'] ?: 'N/A') ?></div>
                <div class="small">2. <?= htmlspecialchars($employee['id_mark_2'] ?: 'N/A') ?></div>
              </div>
              <div class="col-12 py-2 border-top">
                <div class="small text-muted">Police Station: <span class="text-dark fw-bold"><?= htmlspecialchars($employee['nearby_police_station'] ?: 'N/A') ?></span></div>
                <div class="small text-muted">Referred By: <span class="text-dark fw-bold"><?= htmlspecialchars($employee['referred_by_id'] ?: 'N/A') ?></span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 5. Employment History Tab -->
  <div class="tab-pane fade" id="tab-work">
     <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-calendar-range me-2 text-primary"></i>Internal Appointed History</h6>
            <a href="<?= u('positions/add?employee_id='.$employee['id']) ?>" class="btn btn-primary btn-sm no-print"><i class="bi bi-plus me-1"></i>Assign New</a>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                  <thead class="bg-light">
                    <tr><th>Company</th><th>Designation</th><th>Shift</th><th>Daily Rate</th><th>Appointed Date</th><th>Status</th></tr>
                  </thead>
                  <tbody>
                    <?php foreach ($positions as $p): ?>
                    <tr>
                      <td class="fw-bold text-dark small"><?= htmlspecialchars($p['company_name']) ?></td>
                      <td><span class="badge bg-light text-dark border fw-normal"><?= htmlspecialchars($p['designation']) ?></span></td>
                      <td class="small"><?= htmlspecialchars($p['shift'] ?? '') ?></td>
                      <td class="small fw-bold"><?= number_format($p['rate'],2) ?></td>
                      <td class="small"><?= $p['appointed_date'] ? date('d M Y',strtotime($p['appointed_date'])) : '—' ?></td>
                      <td><span class="badge <?=$p['status']==='active'?'bg-success':'bg-secondary'?>"><?= ucfirst($p['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($positions)): ?><tr><td colspan="6" class="text-center py-4 text-muted">No internal history found.</td></tr><?php endif; ?>
                  </tbody>
                </table>
            </div>
        </div>
     </div>
  </div>

  <!-- 6. Call Logs Tab -->
  <div class="tab-pane fade" id="tab-logs">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-telephone-outbound me-2 text-primary"></i>Interaction History</h6>
        <button class="btn btn-primary btn-sm no-print" data-bs-toggle="modal" data-bs-target="#addLogModal"><i class="bi bi-plus-circle me-1"></i>Add New Log</button>
      </div>
      <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0">
              <thead class="bg-light"><tr><th>Date</th><th>Type</th><th>Subject</th><th>Short Notes</th><th>Follow Up</th></tr></thead>
              <tbody>
              <?php foreach ($callLogs as $log): ?>
              <tr>
                <td class="small"><?= date('d M Y H:i',strtotime($log['call_date'])) ?></td>
                <td><span class="badge <?=$log['call_type']==='incoming'?'bg-success':($log['call_type']==='missed'?'bg-danger':'bg-primary')?>"><?= ucfirst($log['call_type']) ?></span></td>
                <td class="small fw-bold"><?= htmlspecialchars($log['subject'] ?? '') ?></td>
                <td class="small text-muted"><?= htmlspecialchars($log['notes'] ?? '') ?></td>
                <td class="small"><?= $log['follow_up_date'] ? date('d M Y',strtotime($log['follow_up_date'])) : '—' ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($callLogs)): ?><tr><td colspan="5" class="text-center py-4 text-muted">No logs found.</td></tr><?php endif; ?>
              </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Call Log Modal -->
<div class="modal fade" id="addLogModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow">
  <form method="POST" action="<?= u('employees/addlog/'.$employee['id']) ?>">
    <div class="modal-header bg-light"><h6 class="modal-title fw-bold">Add New Interaction Log</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body p-4">
      <div class="row g-3">
        <div class="col-6"><label class="form-label small fw-bold">Date & Time</label><input type="datetime-local" name="call_date" class="form-control form-control-sm" value="<?= date('Y-m-d\TH:i') ?>" required></div>
        <div class="col-6"><label class="form-label small fw-bold">Type</label>
          <select name="call_type" class="form-select form-select-sm"><option value="outgoing">Outgoing</option><option value="incoming">Incoming</option><option value="missed">Missed</option></select></div>
        <div class="col-6"><label class="form-label small fw-bold">Contact Used</label><input name="phone" class="form-control form-control-sm" value="<?= htmlspecialchars($employee['mobile']??'') ?>"></div>
        <div class="col-6"><label class="form-label small fw-bold">Next Follow Up</label><input type="date" name="follow_up_date" class="form-control form-control-sm"></div>
        <div class="col-12"><label class="form-label small fw-bold">Subject / Concern</label><input name="subject" class="form-control form-control-sm" placeholder="e.g. Salary discussion, Document pending..." required></div>
        <div class="col-12"><label class="form-label small fw-bold">Detailed Notes</label><textarea name="notes" class="form-control form-control-sm" rows="3" placeholder="Brief summary of the call..."></textarea></div>
      </div>
    </div>
    <div class="modal-footer border-0 p-3"><button type="submit" class="btn btn-primary w-100 py-2">SAVE LOG ENTRY</button></div>
  </form>
</div></div></div>

<style>
.ls-1 { letter-spacing: 0.5px; }
.nav-pills .nav-link { color: #6c757d; font-weight: 500; border-radius: 6px; }
.nav-pills .nav-link.active { background-color: #f8f9fa; color: #0d6efd; box-shadow: inset 0 0 0 1px #0d6efd; }
.stat-card { padding: 15px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.tab-pane { padding-top: 10px; }
@media print {
  .no-print { display: none !important; }
  .card { border: 1px solid #ddd !important; box-shadow: none !important; }
  .tab-content > .tab-pane { display: block !important; opacity: 1 !important; }
}
</style>

