<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Generate Salary</h5>
</div>

<?php if ($msg): ?>
<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="card" style="max-width:500px">
  <div class="card-header">Generate monthly salary from attendance</div>
  <div class="card-body">
    <form method="POST">
      <div class="mb-3">
        <label class="form-label fw-semibold small">Select Client <span class="text-danger">*</span></label>
        <select name="client_id" class="form-select" required>
          <option value="">— Select Client —</option>
          <?php foreach ($clients as $c): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['company_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold small">Salary Month <span class="text-danger">*</span></label>
        <input type="month" name="salary_month" class="form-control" value="<?= date('Y-m', strtotime('-1 month')) ?>" required>
      </div>
      <div class="alert alert-info small">
        <i class="bi bi-info-circle me-2"></i>
        This will calculate salary for all active employees of the selected client based on their attendance for the chosen month.
        EPF (12%), ESI (0.75%), advances, and uniform dues will be auto-deducted.
      </div>
      <button type="submit" class="btn btn-primary px-4">
        <i class="bi bi-lightning me-2"></i>Generate Salary
      </button>
    </form>
  </div>
</div>
