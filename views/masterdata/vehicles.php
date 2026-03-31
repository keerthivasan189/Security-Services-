<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-car-front me-2"></i>Vehicle Master</h5>
</div>
<div class="row g-3">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body p-0">
        <table class="table table-hover datatable mb-0">
          <thead class="table-light"><tr><th>#</th><th>Vehicle No</th><th>Model</th><th>Last KM</th><th>Actions</th></tr></thead>
          <tbody>
          <?php foreach ($list as $i => $v): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($v['vehicle_no']) ?></td>
            <td><?= htmlspecialchars($v['model'] ?? '—') ?></td>
            <td><?= number_format($v['last_km']) ?></td>
            <td>
              <form method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $v['id'] ?>">
                <button class="btn btn-xs btn-outline-danger py-0 px-2"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">Add Vehicle</div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="action" value="add">
          <div class="mb-3"><input name="vehicle_no" class="form-control" placeholder="e.g. TN 31 AC 0123" required></div>
          <div class="mb-3"><input name="model" class="form-control" placeholder="Model (optional)"></div>
          <button class="btn btn-primary btn-sm w-100">Add</button>
        </form>
      </div>
    </div>
  </div>
</div>
