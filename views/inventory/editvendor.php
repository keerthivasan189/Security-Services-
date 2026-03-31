<h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Vendor</h5>
<div class="card mt-3">
  <div class="card-body">
    <form method="POST">
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Vendor Name *</label><input name="vendor_name" class="form-control" value="<?= htmlspecialchars($vendor['vendor_name']) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Contact Person</label><input name="contact_name" class="form-control" value="<?= htmlspecialchars($vendor['contact_name'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="form-label">Mobile</label><input name="mobile" class="form-control" value="<?= htmlspecialchars($vendor['mobile'] ?? '') ?>"></div>
        <div class="col-md-8"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($vendor['address'] ?? '') ?></textarea></div>
      </div>
      <div class="mt-3">
        <button class="btn btn-primary">Update</button>
        <a href="<?= u('inventory/index') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
      </div>
    </form>
  </div>
</div>
