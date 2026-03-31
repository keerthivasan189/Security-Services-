<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="bi bi-arrow-repeat me-2"></i>Migrate Candidate to Active Employee</h5>
    <a href="<?= u('employees/index') ?>?status=pre_employee" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to List</a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body text-center">
                <img src="<?= $employee['photo'] ? BASE_URL.'/'.$employee['photo'] : 'https://ui-avatars.com/api/?name='.urlencode($employee['name']).'&size=128' ?>" class="rounded-circle mb-3 border p-1" style="width:120px; height:120px; object-fit:cover;">
                <h5 class="mb-1"><?= htmlspecialchars($employee['name']) ?></h5>
                <p class="text-muted small mb-0"><?= htmlspecialchars($employee['emp_code']) ?> • <?= htmlspecialchars($employee['designation']) ?></p>
                <hr>
                <div class="text-start small">
                    <div class="d-flex justify-content-between mb-1"><span>Aadhaar:</span> <span class="fw-semibold"><?= $employee['aadhaar'] ?></span></div>
                    <div class="d-flex justify-content-between mb-1"><span>Mobile:</span> <span class="fw-semibold"><?= $employee['mobile'] ?></span></div>
                    <div class="d-flex justify-content-between"><span>DOJ:</span> <span class="fw-semibold"><?= $employee['doj'] ? date('d-M-Y', strtotime($employee['doj'])) : 'Not Set' ?></span></div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info border-0 shadow-sm small">
            <i class="bi bi-info-circle-fill me-2"></i>
            Migration will change the status to <strong>Active</strong> and allow you to capture payroll and compliance details required for salary processing.
        </div>
    </div>

    <div class="col-md-8">
        <form action="<?= u('employees/migrate/'.$employee['id']) ?>" method="POST" enctype="multipart/form-data">
            <!-- Salary & Financials -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-primary text-white py-2 fw-semibold small">
                    <i class="bi bi-cash-stack me-1"></i> SALARY & FINANCIAL PARTICULARS
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Basic Wage (Daily)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" name="basic_wage" id="basic_wage" class="form-control fw-bold text-primary" value="<?= $employee['basic_wage'] ?: 0 ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Daily Allowance (DA)</label>
                            <input type="number" step="0.01" name="salary_da" class="form-control form-control-sm" value="<?= $employee['salary_da'] ?: 0 ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">HRA (Daily)</label>
                            <input type="number" step="0.01" name="salary_hra" class="form-control form-control-sm" value="<?= $employee['salary_hra'] ?: 0 ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Conveyance (Daily)</label>
                            <input type="number" step="0.01" name="salary_conv" class="form-control form-control-sm" value="<?= $employee['salary_conv'] ?: 0 ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Med / Washing</label>
                            <input type="number" step="0.01" name="salary_med_wash" class="form-control form-control-sm" value="<?= $employee['salary_med_wash'] ?: 0 ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Other Allowances</label>
                            <input type="number" step="0.01" name="salary_other" class="form-control form-control-sm" value="<?= $employee['salary_other'] ?: 0 ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">OverTime (OT) Details</label>
                            <input type="text" name="ot_details" class="form-control form-control-sm" value="<?= htmlspecialchars($employee['ot_details'] ?? '') ?>" placeholder="e.g. 1.5x for Sunday, Double for Holidays">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compliance & Deductions -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-success text-white py-2 fw-semibold small">
                    <i class="bi bi-shield-check me-1"></i> STATUTORY COMPLIANCE & EPF/ESI
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6 border-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="epf_applicable" value="YES" id="epf_check" <?= ($employee['epf_applicable']??'NO')==='YES'?'checked':'' ?>>
                                <label class="form-check-label small fw-bold" for="epf_check">EPF Applicable</label>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">EPF Calculation Base Amt</label>
                                <input type="number" name="amt_for_calc_epf" class="form-control form-control-sm" value="<?= $employee['amt_for_calc_epf'] ?: 0 ?>">
                            </div>
                            <div>
                                <label class="form-label small text-muted mb-1">EPF Member ID / No</label>
                                <input type="text" name="epf_no" class="form-control form-control-sm text-uppercase" value="<?= htmlspecialchars($employee['epf_no'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="esi_applicable" value="YES" id="esi_check" <?= ($employee['esi_applicable']??'NO')==='YES'?'checked':'' ?>>
                                <label class="form-check-label small fw-bold" for="esi_check">ESI Applicable</label>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-muted mb-1">ESI Calculation Base Amt</label>
                                <input type="number" name="amt_for_calc_esi" class="form-control form-control-sm" value="<?= $employee['amt_for_calc_esi'] ?: 0 ?>">
                            </div>
                            <div>
                                <label class="form-label small text-muted mb-1">ESI Number</label>
                                <input type="text" name="esi_no" class="form-control form-control-sm" value="<?= htmlspecialchars($employee['esi_no'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">TDS Applicable?</label>
                            <select name="tds_avail" class="form-select form-select-sm">
                                <option value="NO" <?= ($employee['tds_avail']??'NO')==='NO'?'selected':'' ?>>No</option>
                                <option value="YES" <?= ($employee['tds_avail']??'NO')==='YES'?'selected':'' ?>>Yes</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-muted">PAN / TDS Number</label>
                            <input type="text" name="tds_no" class="form-control form-control-sm text-uppercase" value="<?= htmlspecialchars($employee['tds_no'] ?? $employee['pan']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Insurance & Nominee -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-info text-white py-2 fw-semibold small">
                    <i class="bi bi-heart-pulse me-1"></i> INSURANCE & NOMINEE DETAILS
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Nominee Name</label>
                            <input type="text" name="nominee_name" class="form-control form-control-sm" value="<?= htmlspecialchars($employee['nominee_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Relation with Nominee</label>
                            <select name="nominee_relation_id" class="form-select form-select-sm">
                                <option value="">— Select Relation —</option>
                                <?php foreach ($masters['relationships'] as $r): ?>
                                    <option value="<?= $r['id'] ?>" <?= ($employee['nominee_relation_id']??'')==$r['id']?'selected':'' ?>><?= htmlspecialchars($r['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Insurance Policy No.</label>
                            <input type="text" name="ins_number" class="form-control form-control-sm" value="<?= htmlspecialchars($employee['ins_number'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Renewal Date</label>
                            <input type="date" name="ins_renewal_date" class="form-control form-control-sm" value="<?= $employee['ins_renewal_date'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Premium Amount</label>
                            <input type="number" step="0.01" name="premium_amount" class="form-control form-control-sm" value="<?= $employee['premium_amount'] ?: 0 ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white py-2 fw-semibold small">
                    <i class="bi bi-paperclip me-1"></i> FINAL DOCUMENT ATTACHMENTS (MAX 2MB EACH)
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php 
                        $docs = [
                            'doc_application' => 'Filled Application Form',
                            'doc_aadhaar'     => 'Aadhaar Card Copy',
                            'doc_passbook'    => 'Bank Passbook / Cheque',
                            'doc_photo'       => 'Recent Passport Photo',
                            'doc_settlement'  => 'Prev. Co. Settlement',
                            'doc_extra_1'     => 'Certificates (Extra 1)',
                            'doc_extra_2'     => 'Police Clearance (Extra 2)',
                            'doc_extra_3'     => 'Resume (Extra 3)'
                        ];
                        foreach($docs as $field => $label): ?>
                        <div class="col-md-6 border rounded p-2 bg-white">
                            <label class="form-label small fw-bold d-block mb-1"><?= $label ?></label>
                            <?php if ($employee[$field]): ?>
                                <div class="mb-1 text-success small"><i class="bi bi-check-circle-fill me-1"></i>Uploaded: <a href="<?= BASE_URL.'/'.$employee[$field] ?>" target="_blank" class="text-decoration-none">View File</a></div>
                            <?php endif; ?>
                            <input type="file" name="<?= $field ?>" class="form-control form-control-sm">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body text-center py-4 bg-light">
                    <p class="text-muted small mb-3">By clicking <strong>FINALIZE MIGRATION</strong>, the candidate will be officially registered as an Active Employee with the details above.</p>
                    <!-- Hidden fields to preserve existing basic data -->
                    <?php 
                    $basicFields = ['name', 'father_name', 'mother_name', 'designation', 'doj', 'dob', 'gender', 'married_status', 'mobile', 'whatsapp_number', 'email_id', 'qualification_id', 'blood_group_id', 'address', 'communication_state_id', 'communication_district_id', 'communication_taluk_id', 'communication_town_id', 'communication_pincode', 'permanent_address', 'aadhaar', 'pan', 'uan_no', 'esi_no', 'bank_name', 'bank_account', 'bank_ifsc', 'field_officer_id', 'branch_id', 'date_of_walkin', 'emg_contact_name', 'emg_contact_relation_id', 'emg_contact_mobile', 'trusted_person_name', 'trusted_person_relation_id', 'trusted_person_mobile', 'introducer_relate_id', 'intro_employee_id', 'introducer_name', 'introducer_mobile', 'height_cm', 'weight_kg', 'chest_inches', 'hip_inches', 'body_type_id', 'work_exp_years', 'prev_company', 'prev_contact_number', 'prev_strength', 'reason_for_relieve', 'expected_salary_1', 'expected_salary_2', 'work_hours', 'expected_designation_id_1', 'expected_designation_id_2', 'expected_company_id_1', 'expected_company_id_2', 'exp_state_id', 'exp_district_id', 'exp_taluk_id', 'exp_town_id', 'convicted', 'convicted_reason', 'id_mark_1', 'id_mark_2', 'nearby_police_station', 'referred_by_id', 'remarks_extra', 'bank_branch', 'will_have_header', 'will_have_footer'];
                    foreach($basicFields as $f): ?>
                        <input type="hidden" name="<?= $f ?>" value="<?= htmlspecialchars($employee[$f] ?? '') ?>">
                    <?php endforeach; ?>
                    
                    <button type="submit" class="btn btn-success btn-lg px-5 shadow"><i class="bi bi-check-all me-2"></i>FINALIZE MIGRATION</button>
                    <a href="<?= u('employees/index') ?>" class="btn btn-outline-secondary btn-lg ms-3 px-4">CANCEL</a>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.input-group-text { background-color: #f8f9fa; border-right: 0; }
.form-control:focus { border-color: #0d6efd; box-shadow: none; }
.card-header { text-transform: uppercase; letter-spacing: 0.5px; }
.btn-xs { padding: 0.1rem 0.4rem; font-size: 0.75rem; }
</style>
