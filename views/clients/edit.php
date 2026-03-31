<?php
$statusLabel = ($client['status'] ?? 'pre_client') === 'pre_client' ? 'PRE-CLIENT' : 'CLIENT';
$isEdit = isset($client);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0 text-uppercase fw-semibold" style="color:#666">ADD/UPDATE <?= $statusLabel ?></h5>
  <a href="<?= u('clients/index') ?>?status=<?= htmlspecialchars($client['status'] ?? 'pre_client') ?>" class="btn btn-primary btn-sm px-3" style="background:#5a4fcf;border-color:#5a4fcf">
    SHOW ALL <?= $statusLabel ?>S
  </a>
</div>

<div class="card mb-4 border-0 shadow-sm" style="border-radius:10px">
  <div class="card-body p-4">
    <form method="POST" enctype="multipart/form-data">
      
      <!-- Section 1: Contact Details -->
      <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Contact Details</h6>
      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Company Name: <span class="text-danger">*</span></label>
          <input type="text" name="company_name" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['company_name'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">PH.No:</label>
          <input type="text" name="phone" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['phone'] ?? '') ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Ext No:</label>
          <input type="text" name="ext_no" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['ext_no'] ?? '') ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">HR Email ID: <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control form-control-sm border-0 border-bottom bg-white rounded-0" value="<?= htmlspecialchars($client['email'] ?? '') ?>" style="border-bottom: 1px solid #ced4da!important">
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Contact Person 1: <span class="text-danger">*</span></label>
          <input type="text" name="contact_person" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['contact_person'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Role: <span class="text-danger">*</span></label>
          <select name="role" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
            <option <?= ($client['role']??'')==='PROPRIETOR'?'selected':'' ?>>PROPRIETOR</option>
            <option <?= ($client['role']??'')==='MANAGER'?'selected':'' ?>>MANAGER</option>
            <option <?= ($client['role']??'')==='HR'?'selected':'' ?>>HR</option>
            <option <?= ($client['role']??'')==='DIRECTOR'?'selected':'' ?>>DIRECTOR</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Mobile No: <span class="text-danger">*</span></label>
          <input type="text" name="mobile" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['mobile'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">WhatsApp: <span class="text-danger">*</span></label>
          <input type="text" name="whatsapp" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['whatsapp'] ?? '') ?>" required>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Branch: <span class="text-danger">*</span></label>
          <select name="branch" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" required>
            <option value="">---------</option>
            <option <?= ($client['branch']??'')==='CUDDALORE(HO)'?'selected':'' ?>>CUDDALORE(HO)</option>
            <option <?= ($client['branch']??'')==='VILLUPURAM'?'selected':'' ?>>VILLUPURAM</option>
            <option <?= ($client['branch']??'')==='PONDICHERRY'?'selected':'' ?>>PONDICHERRY</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Field Officer:</label>
          <input type="text" name="field_officer" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['field_officer'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold small text-muted">Client Address: <span class="text-danger">*</span></label>
          <input type="text" name="address" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['address'] ?? '') ?>" required>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">State: <span class="text-danger">*</span></label>
          <select name="state" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
             <option value="">---------</option>
             <option <?= ($client['state']??'TAMIL NADU')==='TAMIL NADU'?'selected':'' ?>>TAMIL NADU</option>
             <option <?= ($client['state']??'')==='PUDUCHERRY'?'selected':'' ?>>PUDUCHERRY</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">District: <span class="text-danger">*</span></label>
          <select name="district" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" required>
             <option value="">---------</option>
             <?php $dists = ['CUDDALORE','VILLUPURAM','PUDUCHERRY','CHENNAI']; foreach($dists as $d): ?>
               <option <?= ($client['district']??'')===$d?'selected':'' ?>><?= $d ?></option>
             <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Taluk: <span class="text-danger">*</span></label>
          <select name="taluk" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" required>
             <option value="">---------</option>
             <?php $taluks = ['CUDDALORE','PANRUTI','CHIDAMBARAM']; foreach($taluks as $t): ?>
               <option <?= ($client['taluk']??'')===$t?'selected':'' ?>><?= $t ?></option>
             <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Town: <span class="text-danger">*</span></label>
          <select name="town" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" required>
             <option value="">---------</option>
             <?php $towns = ['CUDDALORE','NEYVELI','VILLUPURAM']; foreach($towns as $t): ?>
               <option <?= ($client['town']??'')===$t?'selected':'' ?>><?= $t ?></option>
             <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">PIN Code:</label>
          <input type="text" name="pincode" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['pincode'] ?? '') ?>">
        </div>
      </div>

      <!-- Section 2: Invoice & Service Settings -->
      <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Location & Invoice Settings</h6>
      <div class="row g-3 mb-4">
        
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Client Code:</label>
          <input type="text" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['client_code'] ?? 'Auto-generated') ?>" readonly>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Bill Type: <span class="text-danger">*</span></label>
          <select name="bill_type" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" required>
            <option value="">---------</option>
            <option <?= ($client['bill_type']??'')==='GST'?'selected':'' ?>>GST</option>
            <option <?= ($client['bill_type']??'')==='RCM'?'selected':'' ?>>RCM</option>
            <option <?= ($client['bill_type']??'')==='EXEMPT'?'selected':'' ?>>EXEMPT</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">GST Exempted?:</label>
          <select name="gst_exempted" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
             <option <?= ($client['gst_exempted']??'No')==='No'?'selected':'' ?>>No</option>
             <option <?= ($client['gst_exempted']??'')==='Yes'?'selected':'' ?>>Yes</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">GST Number:</label>
          <input type="text" name="gstin" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['gstin'] ?? '') ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">GST Cal Method:</label>
          <select name="gst_calc_method" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
             <option value="">---------</option>
             <option <?= ($client['gst_calc_method']??'')==='GST Calc Method'?'selected':'' ?>>GST Calc Method</option>
             <option <?= ($client['gst_calc_method']??'')==='Standard'?'selected':'' ?>>Standard</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">SAC Code: <span class="text-danger">*</span></label>
          <input type="text" name="sac_code" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['sac_code'] ?? '') ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Salary Calculation for Employee: <span class="text-danger">*</span></label>
          <select name="salary_calc_employee" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
             <option value="">---------</option>
             <option <?= ($client['salary_calc_employee']??'')==='Basic'?'selected':'' ?>>Basic</option>
             <option <?= ($client['salary_calc_employee']??'')==='Gross'?'selected':'' ?>>Gross</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Month Denominator: <span class="text-danger">*</span></label>
          <select name="month_denominator" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
             <option value="">---------</option>
             <option <?= ($client['month_denominator']??'')==='26'?'selected':'' ?>>26</option>
             <option <?= ($client['month_denominator']??'')==='30'?'selected':'' ?>>30</option>
             <option <?= ($client['month_denominator']??'')==='Actual Days'?'selected':'' ?>>Actual Days</option>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold small text-muted">Work Order Number:</label>
          <input type="text" name="work_order_no" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['work_order_no'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold small text-muted">Work Order Date:</label>
          <input type="date" name="work_order_date" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['work_order_date'] ?? '') ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">TDS Avil?: <span class="text-danger">*</span></label>
          <select name="tds_avail" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
             <option <?= ($client['tds_avail']??'No')==='No'?'selected':'' ?>>No</option>
             <option <?= ($client['tds_avail']??'')==='Yes'?'selected':'' ?>>Yes</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">TDS %:</label>
          <input type="number" step="0.01" name="tds_percent" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['tds_percent'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">TDS on GST %:</label>
          <input type="number" step="0.01" name="tds_on_gst_percent" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['tds_on_gst_percent'] ?? '') ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">EPF Avil?: <span class="text-danger">*</span></label>
          <select name="epf_avail" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
             <option <?= ($client['epf_avail']??'No')==='No'?'selected':'' ?>>No</option>
             <option <?= ($client['epf_avail']??'')==='Yes'?'selected':'' ?>>Yes</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">EPF %:</label>
          <input type="number" step="0.01" name="epf_percent" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['epf_percent'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">EPF on Value:</label>
          <input type="text" name="epf_on_value" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['epf_on_value'] ?? '') ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">ESI Avil?: <span class="text-danger">*</span></label>
          <select name="esi_avail" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
             <option <?= ($client['esi_avail']??'No')==='No'?'selected':'' ?>>No</option>
             <option <?= ($client['esi_avail']??'')==='Yes'?'selected':'' ?>>Yes</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">ESI %:</label>
          <input type="number" step="0.01" name="esi_percent" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['esi_percent'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">ESI on Value:</label>
          <input type="text" name="esi_on_value" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['esi_on_value'] ?? '') ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Weekly off For:</label>
          <?php $woffs = explode(',', $client['weekly_off_for']??''); ?>
          <select name="weekly_off_for[]" class="form-select form-select-sm border-0 bg-light" multiple size="4">
            <option <?= in_array('SECURITY OFFICER',$woffs)?'selected':'' ?>>SECURITY OFFICER</option>
            <option <?= in_array('AREA OFFICER',$woffs)?'selected':'' ?>>AREA OFFICER</option>
            <option <?= in_array('SECURITY GUARD',$woffs)?'selected':'' ?>>SECURITY GUARD</option>
            <option <?= in_array('LADY SECURITY GUARD',$woffs)?'selected':'' ?>>LADY SECURITY GUARD</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Company Off Days:</label>
          <?php $coffs = explode(',', $client['company_off_days']??''); ?>
          <select name="company_off_days[]" class="form-select form-select-sm border-0 bg-light" multiple size="4">
            <option <?= in_array('Sunday',$coffs)?'selected':'' ?>>Sunday</option>
            <option <?= in_array('Saturday',$coffs)?'selected':'' ?>>Saturday</option>
            <option <?= in_array('Festivals',$coffs)?'selected':'' ?>>Festivals</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Our Service Provide Shifts: <span class="text-danger">*</span></label>
          <?php $curShifts = explode(',', $client['service_shifts']??''); ?>
          <select name="service_shifts[]" class="form-select form-select-sm border-0 bg-light" multiple size="4" required>
            <option <?= in_array('GENERAL SHIFT',$curShifts)?'selected':'' ?>>GENERAL SHIFT</option>
            <option <?= in_array('DAY SHIFT',$curShifts)?'selected':'' ?>>DAY SHIFT</option>
            <option <?= in_array('NIGHT SHIFT',$curShifts)?'selected':'' ?>>NIGHT SHIFT</option>
            <option <?= in_array('A SHIFT',$curShifts)?'selected':'' ?>>A SHIFT</option>
            <option <?= in_array('B SHIFT',$curShifts)?'selected':'' ?>>B SHIFT</option>
            <option <?= in_array('C SHIFT',$curShifts)?'selected':'' ?>>C SHIFT</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small text-muted">Hours per Duty: <span class="text-danger">*</span></label>
          <select name="hours_per_duty" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
             <option value="8" <?= ($client['hours_per_duty']??'8')==='8'?'selected':'' ?>>8</option>
             <option value="12" <?= ($client['hours_per_duty']??'')==='12'?'selected':'' ?>>12</option>
          </select>
        </div>
      </div>

      <!-- Section 3: Billing Info -->
      <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Billing Details</h6>
      <div class="row g-3">
        
        <div class="col-md-3">
           <label class="form-label fw-semibold small text-muted">Invoice Calculation by: <span class="text-danger">*</span></label>
           <select name="invoice_calculation_by" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" required>
             <option value="PRO MONTH" <?= ($client['invoice_calculation_by']??'')==='PRO MONTH'?'selected':'' ?>>PRO MONTH</option>
             <option value="HOURLY" <?= ($client['invoice_calculation_by']??'')==='HOURLY'?'selected':'' ?>>HOURLY</option>
           </select>
        </div>
        <div class="col-md-3">
           <label class="form-label fw-semibold small text-muted">Invoice Generate Date: <span class="text-danger">*</span></label>
           <select name="invoice_schedule" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" required>
             <option value="EVERY MONTH LAST DATE" <?= ($client['invoice_schedule']??'')==='EVERY MONTH LAST DATE'?'selected':'' ?>>EVERY MONTH LAST DATE</option>
             <option value="EVERY MONTH 1ST DATE" <?= ($client['invoice_schedule']??'')==='EVERY MONTH 1ST DATE'?'selected':'' ?>>EVERY MONTH 1ST DATE</option>
             <option value="EVERY MONTH 25TH DATE" <?= ($client['invoice_schedule']??'')==='EVERY MONTH 25TH DATE'?'selected':'' ?>>EVERY MONTH 25TH DATE</option>
           </select>
        </div>
        <div class="col-md-3">
           <label class="form-label fw-semibold small text-muted">Bill Send By: <span class="text-danger">*</span></label>
           <select name="bill_send_by" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" required>
             <option value="COURIER" <?= ($client['bill_send_by']??'COURIER')==='COURIER'?'selected':'' ?>>COURIER</option>
             <option value="EMAIL" <?= ($client['bill_send_by']??'')==='EMAIL'?'selected':'' ?>>EMAIL</option>
             <option value="BY HAND" <?= ($client['bill_send_by']??'')==='BY HAND'?'selected':'' ?>>BY HAND</option>
           </select>
        </div>
        <div class="col-md-3">
           <label class="form-label fw-semibold small text-muted">Grace Period:</label>
           <input type="number" name="grace_period" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['grace_period'] ?? '7') ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">Service Charges Avil?: <span class="text-danger">*</span></label>
          <select name="service_charges_avail" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
             <option <?= ($client['service_charges_avail']??'No')==='No'?'selected':'' ?>>No</option>
             <option <?= ($client['service_charges_avail']??'')==='Yes'?'selected':'' ?>>Yes</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">Flat/Percentage:</label>
          <select name="service_charges_type" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
             <option value="">Select</option>
             <option value="Flat" <?= ($client['service_charges_type']??'')==='Flat'?'selected':'' ?>>Flat</option>
             <option value="Percentage" <?= ($client['service_charges_type']??'')==='Percentage'?'selected':'' ?>>Percentage</option>
          </select>
        </div>
        <div class="col-md-4">
           <label class="form-label fw-semibold small text-muted">Value:</label>
           <input type="number" step="0.01" name="service_charges_value" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['service_charges_value'] ?? '') ?>">
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold small text-muted">Doc Attach with Invoice:</label>
          <textarea name="doc_attach_invoice" class="form-control border-0 bg-light" rows="3"><?= htmlspecialchars($client['doc_attach_invoice'] ?? "EPF AND ESI FULL COPY\nMOSTROLL ATTENDANCE\nINVOICE COPY") ?></textarea>
        </div>

        <div class="col-md-4">
           <label class="form-label fw-semibold small text-muted">Previous Contractor Name:</label>
           <input type="text" name="prev_contractor_name" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['prev_contractor_name'] ?? '') ?>">
        </div>
        <div class="col-md-4">
           <label class="form-label fw-semibold small text-muted">Previous Contractor Mobile:</label>
           <input type="text" name="prev_contractor_mobile" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['prev_contractor_mobile'] ?? '') ?>">
        </div>
        <div class="col-md-4">
           <label class="form-label fw-semibold small text-muted">Strength:</label>
           <input type="text" name="strength" class="form-control form-control-sm border-0 bg-light" value="<?= htmlspecialchars($client['strength'] ?? '') ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">Bank Account To Show on Invoice: <span class="text-danger">*</span></label>
          <select name="bank_account_show" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" required>
             <option value="MAIN ACCOUNT" <?= ($client['bank_account_show']??'')==='MAIN ACCOUNT'?'selected':'' ?>>MAIN ACCOUNT</option>
             <option value="SECONDARY ACCOUNT" <?= ($client['bank_account_show']??'')==='SECONDARY ACCOUNT'?'selected':'' ?>>SECONDARY ACCOUNT</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">Will Bill have Header?: <span class="text-danger">*</span></label>
          <select name="bill_header" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
             <option <?= ($client['bill_header']??'YES')==='YES'?'selected':'' ?>>YES</option>
             <option <?= ($client['bill_header']??'')==='NO'?'selected':'' ?>>NO</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold small text-muted">Will Bill have Footer?: <span class="text-danger">*</span></label>
          <select name="bill_footer" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
             <option <?= ($client['bill_footer']??'YES')==='YES'?'selected':'' ?>>YES</option>
             <option <?= ($client['bill_footer']??'')==='NO'?'selected':'' ?>>NO</option>
          </select>
        </div>

        <div class="col-md-6 mt-4">
          <label class="form-label fw-semibold small text-muted">Status: <span class="text-danger">*</span></label>
          <select name="status" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important">
            <option value="pre_client" <?= ($client['status']??'pre_client')==='pre_client'?'selected':'' ?>>Pre-Client</option>
            <option value="active" <?= ($client['status']??'')==='active'?'selected':'' ?>>Active Client</option>
            <option value="inactive" <?= ($client['status']??'')==='inactive'?'selected':'' ?>>Relieved Clients</option>
          </select>
        </div>
        <div class="col-md-6 mt-4">
          <label class="form-label fw-semibold small text-muted">Reference: <span class="text-danger">*</span></label>
          <select name="reference" class="form-select form-select-sm border-0 border-bottom bg-white rounded-0" style="border-bottom:1px solid #ced4da!important" required>
            <option value="DIRECT" <?= ($client['reference']??'DIRECT')==='DIRECT'? 'selected':'' ?>>DIRECT</option>
            <option value="REFERRAL" <?= ($client['reference']??'')==='REFERRAL'? 'selected':'' ?>>REFERRAL</option>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold small text-muted">Quotation:</label>
          <input type="file" name="quotation_file" class="form-control form-control-sm bg-light border-0">
          <?php if(!empty($client['quotation_file'])): ?>
            <small class="text-muted">Current file: <a href="<?= BASE_URL ?>/uploads/clients/<?= $client['quotation_file'] ?>" target="_blank">View</a></small>
          <?php endif; ?>
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold small text-muted">Client Remarks:</label>
          <textarea name="preclient_remarks" class="form-control border-0 bg-light" rows="3"><?= htmlspecialchars($client['preclient_remarks'] ?? '') ?></textarea>
        </div>

        <!-- Submit -->
        <div class="col-12 mt-4 text-center">
          <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold shadow-sm w-100" style="background:#5a4fcf;border:none;max-width:300px">SAVE / UPDATE</button>
        </div>

      </div>
    </form>
  </div>
</div>
