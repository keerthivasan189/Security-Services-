<?php
$colLabels = array_map(function($c) {
    return ucwords(str_replace('_', ' ', $c));
}, $columns);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <a href="<?= u('masterdata/index') ?>" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i>Back to Master Data</a>
    <h5 class="mb-0 mt-1"><i class="bi <?= $icon ?> me-2" style="color:<?= $color ?>"></i><?= htmlspecialchars($label) ?></h5>
  </div>
</div>

<div class="row g-3">
  <!-- Data Table -->
  <div class="col-md-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><?= count($list) ?> records</span>
      </div>
      <div class="card-body p-0">
        <table class="table table-hover datatable mb-0">
          <thead class="table-light">
            <tr>
              <th width="40">#</th>
              <?php foreach ($colLabels as $cl): ?><th><?= $cl ?></th><?php endforeach; ?>
              <th width="110">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($list as $i => $row): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <?php foreach ($columns as $col): ?>
            <td class="fw-semibold"><?= htmlspecialchars($row[$col] ?? '') ?></td>
            <?php endforeach; ?>
            <td>
              <!-- Edit btn triggers modal -->
              <button class="btn btn-xs btn-outline-warning py-0 px-2 edit-btn"
                data-id="<?= $row['id'] ?>"
                <?php foreach ($columns as $col): ?>
                data-<?= $col ?>="<?= htmlspecialchars($row[$col] ?? '') ?>"
                <?php endforeach; ?>
              ><i class="bi bi-pencil"></i></button>
              <!-- Delete -->
              <form method="POST" class="d-inline" onsubmit="return confirm('Delete this record?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button class="btn btn-xs btn-outline-danger py-0 px-2"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($list)): ?><tr><td colspan="<?= count($columns)+2 ?>" class="text-center text-muted py-4">No records</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Add Form -->
  <div class="col-md-4">
    <div class="card">
      <div class="card-header" style="background:<?= $color ?>10;border-left:3px solid <?= $color ?>">
        <i class="bi bi-plus-circle me-1"></i>Add New <?= htmlspecialchars($label) ?>
      </div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="action" value="add">
          <?php foreach ($columns as $idx => $col): ?>
          <div class="mb-3">
            <label class="form-label small fw-semibold"><?= $colLabels[$idx] ?></label>
            <input name="<?= $col ?>" class="form-control" placeholder="Enter <?= strtolower($colLabels[$idx]) ?>" required>
          </div>
          <?php endforeach; ?>
          <button class="btn btn-primary btn-sm w-100"><i class="bi bi-plus me-1"></i>Add Record</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <form method="POST">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="id" id="edit-id">
    <div class="modal-header"><h6 class="modal-title">Edit Record</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <?php foreach ($columns as $idx => $col): ?>
      <div class="mb-3">
        <label class="form-label small fw-semibold"><?= $colLabels[$idx] ?></label>
        <input name="<?= $col ?>" id="edit-<?= $col ?>" class="form-control" required>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="modal-footer"><button class="btn btn-primary btn-sm">Update</button></div>
  </form>
</div></div></div>

<?php $extraJs = '<script>
document.querySelectorAll(".edit-btn").forEach(function(btn){
  btn.addEventListener("click", function(){
    document.getElementById("edit-id").value = this.dataset.id;
    ' . implode("\n    ", array_map(function($col) {
        return 'document.getElementById("edit-' . $col . '").value = this.dataset.' . $col . ';';
    }, $columns)) . '
    new bootstrap.Modal(document.getElementById("editModal")).show();
  });
});
</script>'; ?>
