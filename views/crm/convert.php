<div class="d-flex justify-content-between align-items-center mb-4">
  <h5 class="mb-0 fw-semibold"><i class="bi bi-arrow-right-circle me-2 text-success"></i>Convert Lead to Client</h5>
  <a href="<?= u("crm/viewLead/{$lead['id']}") ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card border-success">
      <div class="card-header bg-success text-white">
        <i class="bi bi-building me-1"></i> Lead Details
      </div>
      <div class="card-body">
        <table class="table table-borderless mb-0" style="font-size:13px">
          <tr><td class="text-muted">Lead Code</td><td class="fw-semibold"><?= $lead['lead_code'] ?></td></tr>
          <tr><td class="text-muted">Company</td><td class="fw-bold"><?= htmlspecialchars($lead['company_name']) ?></td></tr>
          <tr><td class="text-muted">Contact</td><td><?= htmlspecialchars($lead['contact_person']) ?> · <?= $lead['mobile'] ?></td></tr>
          <tr><td class="text-muted">Expected Value</td><td class="fw-bold text-success">₹<?= number_format($lead['expected_value']) ?>/month</td></tr>
          <tr><td class="text-muted">Strength</td><td><?= $lead['expected_strength'] ?> staff</td></tr>
          <tr><td class="text-muted">District</td><td><?= $lead['district'] ?>, <?= $lead['state'] ?></td></tr>
        </table>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header"><i class="bi bi-info-circle me-1 text-primary"></i> What happens on conversion?</div>
      <div class="card-body" style="font-size:13px">
        <ul class="mb-0">
          <li>A new <strong>Client</strong> record is created with <code>pre_client</code> status</li>
          <li>This lead will be marked as <strong>Won</strong> in CRM</li>
          <li>You can then complete the client details in <strong>Client Master</strong></li>
          <li>The link between this lead and the client is preserved</li>
        </ul>
      </div>
    </div>

    <form method="POST" action="<?= u("crm/convertToClient/{$lead['id']}") ?>" class="mt-3">
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success px-4 w-100">
          <i class="bi bi-check-lg me-1"></i>Confirm — Convert to Client
        </button>
        <a href="<?= u("crm/viewLead/{$lead['id']}") ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
