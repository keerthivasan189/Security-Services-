<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Invoice Schedule Types</h5>
</div>
<div class="row g-3">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body p-0">
        <table class="table table-hover datatable mb-0">
          <thead class="table-light"><tr><th>#</th><th>Schedule Type</th><th>Actions</th></tr></thead>
          <tbody>
          <?php foreach ($list as $i => $s): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($s['name']) ?></td>
            <td>
              <form method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $s['id'] ?>">
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
      <div class="card-header">Add Schedule Type</div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="action" value="add">
          <div class="mb-3"><input name="name" class="form-control" placeholder="e.g. EVERY MONTH LAST DATE" required></div>
          <button class="btn btn-primary btn-sm w-100">Add</button>
        </form>
      </div>
    </div>
  </div>
</div>
