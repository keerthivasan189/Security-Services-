<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Vendor Master</h5>
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVendor"><i class="bi bi-plus-circle me-1"></i>Add Vendor</button>
</div>
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover datatable mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>Vendor Name</th><th>Contact</th><th>Mobile</th><th>Address</th><th>Actions</th></tr>
      </thead>
      <tbody>
      <?php foreach ($vendors as $i => $v): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td class="fw-semibold"><?= htmlspecialchars($v['vendor_name']) ?></td>
        <td><?= htmlspecialchars($v['contact_name'] ?? '') ?></td>
        <td><?= htmlspecialchars($v['mobile'] ?? '') ?></td>
        <td class="small text-muted"><?= htmlspecialchars($v['address'] ?? '') ?></td>
        <td>
          <a href="<?= u('inventory/editVendor/' . $v['id']) ?>" class="btn btn-xs btn-outline-warning py-0 px-2"><i class="bi bi-pencil"></i></a>
          <form method="POST" class="d-inline" onsubmit="return confirm('Delete this vendor?')">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="vendor_id" value="<?= $v['id'] ?>">
            <button class="btn btn-xs btn-outline-danger py-0 px-2"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($vendors)): ?><tr><td colspan="6" class="text-center text-muted py-4">No vendors found</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Vendor Modal -->
<div class="modal fade" id="addVendor" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <form method="POST">
    <input type="hidden" name="action" value="add">
    <div class="modal-header"><h6 class="modal-title">Add Vendor</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <div class="mb-3"><label class="form-label">Vendor Name *</label><input name="vendor_name" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Contact Person</label><input name="contact_name" class="form-control"></div>
      <div class="mb-3"><label class="form-label">Mobile</label><input name="mobile" class="form-control"></div>
      <div class="mb-3"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
  </form>
</div></div></div>
