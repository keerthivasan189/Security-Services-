<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-tags me-2"></i>Uniform Item Catalog</h5>
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addItem"><i class="bi bi-plus-circle me-1"></i>Add Item</button>
</div>
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover datatable mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>Item Name</th><th>Vendor</th><th>Unit Price</th><th>Actions</th></tr>
      </thead>
      <tbody>
      <?php foreach ($items as $i => $it): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td class="fw-semibold"><?= htmlspecialchars($it['item_name']) ?></td>
        <td><?= htmlspecialchars($it['vendor_name'] ?? '—') ?></td>
        <td><?= Helper::money((float)$it['unit_price']) ?></td>
        <td>
          <form method="POST" class="d-inline" onsubmit="return confirm('Delete this item?')">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="item_id" value="<?= $it['id'] ?>">
            <button class="btn btn-xs btn-outline-danger py-0 px-2"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($items)): ?><tr><td colspan="5" class="text-center text-muted py-4">No items found</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItem" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <form method="POST">
    <input type="hidden" name="action" value="add">
    <div class="modal-header"><h6 class="modal-title">Add Uniform Item</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <div class="mb-3"><label class="form-label">Item Name *</label><input name="item_name" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Vendor</label>
        <select name="vendor_id" class="form-select">
          <option value="">— None —</option>
          <?php foreach ($vendors as $v): ?>
          <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['vendor_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3"><label class="form-label">Unit Price (₹)</label><input name="unit_price" type="number" step="0.01" class="form-control" value="0"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
  </form>
</div></div></div>
